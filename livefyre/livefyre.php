<?php
/*
* overlay   plugin 3.1
* Joomla plugin
* by Purple Cow Websites
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/
// no direct access

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

require_once(JPATH_SITE.'/components/com_content/helpers/route.php');
class plgContentlivefyre extends JPlugin {

	function plgContentlivefyre( &$subject, $params ) {
		parent::__construct( $subject, $params );
	}

	function onContentPrepare($context, &$row, &$params, $page = 0) {

		// Check versions for logging and backward compatibility
		$joomla_v3 = false;
		$use_log = false;
		if (version_compare( JVERSION, '3.0', '>=') == 1) {
			$joomla_v3 = true;
		}
		if (version_compare( JVERSION, '1.7', '>=') == 1 && JDEBUG) {
			jimport('joomla.log.log');
			JLog::addLogger(array());
			$use_log = true;
		}

		if ($use_log) JLog::add('Livefyre: On Joomla '.JVERSION.'.', JLog::DEBUG, 'Livefyre');

		// Reference parameters
		$plg_name = "livefyre";

		// API
		$app 		= &JFactory::getApplication();
		$document 	= &JFactory::getDocument();
		$db 		= &JFactory::getDBO();
		$user 		= &JFactory::getUser();
		$aid 		= max ($user->getAuthorisedViewLevels());
		$this->loadLanguage();

		// Assign paths
		$sitePath = JPATH_SITE;
		// JURI deprecated in 3.0
		$siteUrl  = substr(JUri::root(), 0, -1);

		// Requests
		if ($joomla_v3) {
			$option = JFactory::getApplication()->input->get('option');
			$view   = JFactory::getApplication()->input->get('view');
			$layout = JFactory::getApplication()->input->get('layout');
			$page   = JFactory::getApplication()->input->get('page');
			$secid  = JFactory::getApplication()->input->get('secid');
			$catid  = JFactory::getApplication()->input->get('catid');
			$itemid = JFactory::getApplication()->input->get('Itemid');
		}
		// Depreciated in 3.0
		else {
			$option = JRequest::getCmd('option');
			$view   = JRequest::getCmd('view');
			$layout = JRequest::getCmd('layout');
			$page   = JRequest::getCmd('page');
			$secid  = JRequest::getInt('secid');
			$catid  = JRequest::getInt('catid');
			$itemid = JRequest::getInt('Itemid');
		}
		if (!$itemid) $itemid = 999999;

		// Check if plugin is enabled
		if (JPluginHelper::isEnabled('content', $plg_name) == false) {
			if($use_log) JLog::add('Livefyre: Plugin not enabled.', JLog::DEBUG, 'Livefyre');
			return;
		}

		// Simple checks before parsing the plugin
		$properties = get_object_vars($row);
		
		// ----------------------------------- Get plugin parameters -----------------------------------

		// Deleted in 3.0
		if ($joomla_v3) {
			$selectedCategories = $this->params->get('selectedCategories');
			$blogid 			= $this->params->get('blogid');
			$site_key 			= $this->params->get('apisecret');
			$lf_domain 			= $this->params->get('domain');
		}
		else {
			$plugin 			= &JPluginHelper::getPlugin('content', $plg_name);
			$pluginParams		= new JParameter($plugin->params);
			$selectedCategories	= $pluginParams->get('selectedCategories','');
			$blogid				= $pluginParams->get( 'blogid' );
			$site_key 			= $pluginParams->get( 'apisecret' );
			$lf_domain 			= $pluginParams->get( 'domain' );
		}
		$articleId 		= $row->id;
		$articleTitle 	= $row->title;
		$articlecatid 	= $data->catid;

		// ----------------------------------- Before plugin render -----------------------------------
	
		if (is_null($row->catslug && $view == 'article')) {
			$currectCategory = 0;
		}
		else if ($view == 'category' &&  $layout == 'blog') {
			$currectCategory=$_REQUEST['id'];
		}
		else if (($_REQUEST['view'] == 'featured') && ($_REQUEST['option'] == 'com_content')) {
			if (is_string($row->text) && $row->text != '') {
				// setQuery might be deprecated in 3.0
				// $query = "SELECT * FROM `#__content` where featured='1' and introtext='".$row->text."'";
				// $db->setQuery($query);
				// $data= $db->loadObject();
				// $currectCategory = $data->catid;

				// everything approved
				$query = $db->getQuery(true);
				$query->select("*");
				$query->from("#__content");
				$query->where("featured = '1' and introtext = '".$row->text."'");
				try {
					$db->setQuery($query);
				}
				catch (JDatabaseException $e) {
					if ($use_log) {
					    JLog::add('Livefyre: Database error in listing.php', JLog::DEBUG, 'Livefyre');
					}
				}
				$data = $db->loadObject();
				$currentCategory = $data->catid;
			}
		}
		else {
			$currectCategory = explode(":",$row->catslug);
			$currectCategory = $currectCategory[0];	
		}

		// Define plugin category restrictions
		if (is_array($selectedCategories)) {
			$categories = $selectedCategories;
		}
		else if ($selectedCategories == '') {
			$categories[] = $currectCategory;
		}
		else {
			$categories[] = $selectedCategories;
		}
			
		// ----------------------------------- Prepare elements -----------------------------------
		
		// Includes
		require_once(JPATH_SITE.'/components/com_content/helpers/route.php');
		require_once(dirname(__FILE__).'/'.$plg_name.'/includes/helper.php');
		
		// Output object
		$output = new JObject;
		
		// Article URLs (raw, browser, system)
		$itemURLraw = $siteUrl.'/index.php?option=com_content&view=article&id='.$articleId;
		
		$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
		$itemURLbrowser = $websiteURL.$_SERVER['REQUEST_URI'];
		
		$itemURLbrowser = explode("#",$itemURLbrowser);
		$itemURLbrowser = $itemURLbrowser[0];

		
		if ($row->access <= $aid) {
			$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
		}
		else {
			$itemURL = JRoute::_("index.php?option=com_user&task=register");
		}
		
		// Article URL assignments
		$output->itemURL 			= $websiteURL.$itemURL;
		$output->itemURLrelative 	= $itemURL;
		$output->itemURLbrowser		= $itemURLbrowser;
		$output->itemURLraw			= $itemURLraw;

		// Fetch elements specific to the "article" view only
		if (in_array($currectCategory,$categories) && $option == 'com_content' && $view == 'article') {
			// Don't show widget if there is no blog id. We saw an issue where the article id was being used as the fyre id, so comments were showing up on incorrect blogs.
			if (isset($blogid) && $blogid != '') {
				// Comments (article page)
				$document =& JFactory::getDocument();
				$document->addScript('http://livefyre.com/javascripts/ncomments.js#bn='.$blogid);
				$document->addStyleSheet('http://livefyre.com/css/compressed_embed.css?ver=3.0.1','text/css','all');
			}
		} // End fetch elements specific to the "article" view only

		// ----------------------------------- Render the output -----------------------------------

		if (in_array($currectCategory, $categories) || $view == 'featured') {
			if ($option == 'com_content' && $view == 'article') {

				// Fetch the template
				ob_start();
				$dsqArticlePath = JWlivefyreHelper::getTemplatePath($plg_name,'article.php');
			
				$dsqArticlePath = $dsqArticlePath->file;
				$mycom_folder = JPATH_SITE.'/plugins/content/livefyre/livefyre/tmpl/article.php';

				include($mycom_folder);
				$getArticleTemplate = ob_get_contents();
				ob_end_clean();

				// Output
				$row->text = $getArticleTemplate;
				
			}
			else if ($option == 'com_content' && ($view == 'featured'  || $view == 'category')) {
				
				// Fetch the template
				ob_start();
				$dsqArticlePath = JWlivefyreHelper::getTemplatePath($plg_name,'article.php');
				
				$dsqArticlePath = $dsqArticlePath->file;
				
				$mycom_folder = JPATH_SITE.'/plugins/content/livefyre/livefyre/tmpl/listing.php';
				include($mycom_folder);
				$getListingTemplate = ob_get_contents();
				ob_end_clean();
				
				// Output
				$row->text = $getListingTemplate;

			}
		} // END IF
	} // END FUNCTION
} // END CLASS

?>
