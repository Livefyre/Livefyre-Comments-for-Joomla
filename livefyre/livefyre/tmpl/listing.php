<?php 
/*
* Joomla Plugin
* Plugin Version: 1.0
* by Livefyre Inc
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/plugins/content/livefyre/livefyre/includes/logger.php');
$livefyre_logger = LivefyreLogger::getInstance();

echo $row->text;

if (!isset($plugin) || !isset($blogid)) {
	if ( version_compare( JVERSION, '3.0', '>=' ) == 1) {
		$blogid = $this->params->get('blogid');
	} 
	else {
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
		$pluginParams = new JParameter( $plugin->params );
		$blogid = $pluginParams->get( 'blogid' );
	}
}

$itemURL =$siteUrl.'/index.php?option=com_content&view=article&id='.$articleId;
$livefyre_logger->add('Livefyre: Comment Count on article: Id: ' .$articleId, JLog::DEBUG, 'Livefyre');

?>

<script type="text/javascript">
if (typeof(document.getElementById('ncomments_js')) == 'undefined' || document.getElementById('ncomments_js') == null) {
	document.write('<scr'+'ipt type="text/javascript" id="ncomments_js" src="http://livefyre.com/javascripts/ncomments.js#bn=<?php echo $blogid; ?>"></scr'+'ipt>');
}
</script>

<!-- livefyre comments counter and anchor link -->
<a class="livefyre-ncomments" style="display:block; float:right;" href="<?php echo $itemURL; ?>#livefyre_thread" article_id="<?php echo $articleId ?>" title="<?php echo JText::_('no comments'); ?>"><?php echo JText::_('no comments'); ?></a>
