<?php
/*
* overlay   plugin 1.0
* Joomla plugin
* by Purple Cow Websites
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/
// no direct access

defined('_JEXEC') or die('Restricted access');

class JWlivefyreHelper {

	// Load Includes
	function loadHeadIncludes($headIncludes){
		global $loadlivefyrePluginIncludes;
		$document = & JFactory::getDocument();

		if(!$loadlivefyrePluginIncludes) {
			$loadlivefyrePluginIncludes=1;
			$document->addCustomTag($headIncludes);
		}
	}

	// Path overrides
	function getTemplatePath($pluginName,$file){
		//global $mainframe;
		global $app;
		$p = new JObject;

		if(file_exists(JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))) {

			$p->file = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::base()."templates/".$app->getTemplate()."/html/{$pluginName}/{$file}";
		} 
		else {

			$p->file = JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.'livefyre'.DS.'tmpl'.DS.$file;
			$p->http = JURI::base()."plugins/content/{$pluginName}/tmpl/{$file}";
		}

		return $p;
	}
} // end class

?>

