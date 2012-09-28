<?php
/*
//* overlay   plugin 3.1

* Joomla plugin

* by Purple Cow Websites

* @copyright Copyright (C) 2010 * Livefyre All rights reserved.

//*////////////

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );
require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
class plgContentlivefyre extends JPlugin {

	function plgContentlivefyre( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	function onContentPrepare($context, &$row, &$params, $page = 0){
	//	$db=& JFactory::getDBO(); $query="SELECT * FROM `#__content` where featured='1'";
	 //$db->setQuery($query);
//if (!$db->query())
		//{
		// JError::raiseError(500, $db->getErrorMsg() );
		//}
		//$data= $db->loadObjectlist();
	//	echo '<pre>';print_r($row); echo '</pre>';

    //   reference parameters
    $plg_name               = "livefyre";
		// API
		$app = &JFactory::getApplication();
    //(joomla1.5)$mainframe	= &JFactory::getApplication();
		$document 	= &JFactory::getDocument();
		$db 				= &JFactory::getDBO();
		$user =& JFactory::getUser();
		$this->loadLanguage();
		//(joomla1.5)$aid 				= $user->get('aid',0);
		$aid = max ($user->getAuthorisedViewLevels());
		
		// Assign paths
	 $sitePath = JPATH_SITE;
    $siteUrl  = substr(JURI::root(), 0, -1);




		// Requests
//$article =& JTable::getInstance("content");

 		$option 		= JRequest::getCmd('option');
		$view 			= JRequest::getCmd('view');
		$layout 		= JRequest::getCmd('layout');
		$page 			= JRequest::getCmd('page');
		$secid 			= JRequest::getInt('secid');
		$catid 			= JRequest::getInt('catid');
		$itemid 		= JRequest::getInt('Itemid');
		if(!$itemid) $itemid = 999999;
    
    // Check if plugin is enabled
    if(JPluginHelper::isEnabled('content',$plg_name)==false) return;
       
	
		// Simple checks before parsing the plugin
		$properties = get_object_vars($row);
		//echo '<pre>';	print_r($properties); echo '</pre>';

		//if (!(array_key_exists('catid',$properties) || array_key_exists('sectionid',$properties))) return;
		//if(!$row->id) return;
		
		// ----------------------------------- Get plugin parameters -----------------------------------
		/* Joomla1.5 $plugin =& JPluginHelper::getPlugin('content', $plg_name);
		$pluginParams = new JParameter( $plugin->params );*/
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
        $pluginParams = new JParameter($plugin->params);
		$selectedCategories			= $pluginParams->get('selectedCategories','');
		$blogid = $pluginParams->get( 'blogid' );
		$site_key = $pluginParams->get( 'apisecret' );
		$lf_domain = $pluginParams->get( 'domain' );
		$articleId = $row->id;
		$articleTitle = $row->title;
		$articlecatid=$data->catid;
		//echo '<pre>'; print_r($selectedCategories); echo '</pre>';

		// ----------------------------------- Before plugin render -----------------------------------
		
///echo $row->catslug;
		// Get the current category
		//echo $currectCategory;
	
		if(is_null($row->catslug && $view=='article')  ){
			$currectCategory = 0;
		} 
		else if($view=='category' &&  $layout=='blog')
		{
			$currectCategory=$_REQUEST['id'];
		}
		else if(($_REQUEST['view']=='featured') && ($_REQUEST['option']=='com_content'))
		{
			if (is_string($row->text) && $row->text!='')
			{
  $query="SELECT * FROM `#__content` where featured='1' and introtext='".$row->text."'";
	$db->setQuery($query);
//if (!$db->query())
		//{
		// JError::raiseError(500, $db->getErrorMsg() );
		//}
		$data= $db->loadObject();
		$currectCategory=$data->catid;
			}
		}
		else {
			$currectCategory = explode(":",$row->catslug);
			$currectCategory = $currectCategory[0];	
		}

		// Define plugin category restrictions
		if (is_array($selectedCategories)){
			$categories = $selectedCategories;
			//print_r($categories);
		} elseif ($selectedCategories==''){
			$categories[] = $currectCategory;
		} else {
			$categories[] = $selectedCategories;
		}
			
		// ----------------------------------- Prepare elements -----------------------------------
		
		// Includes
		require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		require_once(dirname(__FILE__).DS.$plg_name.DS.'includes'.DS.'helper.php');
		
		// Output object
		$output = new JObject;
		
		// Article URLs (raw, browser, system)
		$itemURLraw = $siteUrl.'/index.php?option=com_content&view=article&id='.$articleId;
		
		$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
		$itemURLbrowser = $websiteURL.$_SERVER['REQUEST_URI'];
		
		$itemURLbrowser = explode("#",$itemURLbrowser);
		$itemURLbrowser = $itemURLbrowser[0];

		
		if ($row->access <= $aid){
			$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
		} else {
			$itemURL = JRoute::_("index.php?option=com_user&task=register");
		}
		
		// Article URL assignments
		$output->itemURL 					= $websiteURL.$itemURL;

		$output->itemURLrelative 	= $itemURL;
		$output->itemURLbrowser		= $itemURLbrowser;
		$output->itemURLraw				= $itemURLraw;

		// Fetch elements specific to the "article" view only
		if( in_array($currectCategory,$categories) && $option=='com_content' && $view=='article') {

			if (!isset($blogid) || $blogid == '') {
				// Don't show widget if there is no blog id. We saw an issue where the article id was being used as the fyre id, so comments were showing up on incorrect blogs.
			} else {
				// Comments (article page)
				$document =& JFactory::getDocument();
				//$document->addScript('http://livefyre.com/javascripts/compressed/livefyreembed.js#bn='.$blogid.'&article_id='.$articleId.'&platform=joomla');
				$document->addScript('http://livefyre.com/javascripts/ncomments.js#bn='.$blogid);
				$document->addStyleSheet('http://livefyre.com/css/compressed_embed.css?ver=3.0.1','text/css','all');
			}
		} // End fetch elements specific to the "article" view only
		//echo JURI::root() .'\plugins\content\livefyre\livefyre\tmpl\article.php';
		// ----------------------------------- Render the output -----------------------------------
	// echo $currectCategory;

		if( in_array($currectCategory,$categories) || $view=='featured'){
											
				if( ($option=='com_content' && $view=='article')){

					// Fetch the template
					ob_start();
					$dsqArticlePath = JWlivefyreHelper::getTemplatePath($plg_name,'article.php');
				
					$dsqArticlePath = $dsqArticlePath->file;
				   $mycom_folder = JPATH_SITE . DS . 'plugins/content/livefyre/livefyre/tmpl/article.php';

					include($mycom_folder);
					$getArticleTemplate = ob_get_contents();
					ob_end_clean();
	
					// Output
					$row->text = $getArticleTemplate;
					
				} else if(($option=='com_content' && ($view=='featured'  || $view=='category'))) {
			
					// Fetch the template
					ob_start();
					$dsqArticlePath = JWlivefyreHelper::getTemplatePath($plg_name,'article.php');
				  
					$dsqArticlePath = $dsqArticlePath->file;
					
				    $mycom_folder = JPATH_SITE . DS . 'plugins/content/livefyre/livefyre/tmpl/listing.php';
					include($mycom_folder);
					$getListingTemplate = ob_get_contents();
					ob_end_clean();
						
					// Output
					$row->text = $getListingTemplate;
									
				}

				
		} // END IF
		  
	} // END FUNCTION

} // END CLASS
