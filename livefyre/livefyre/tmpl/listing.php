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

?>

<script type="text/javascript">
if (typeof(document.getElementById('ncomments_js')) == 'undefined' || document.getElementById('ncomments_js') == null) {
	document.write('<scr'+'ipt type="text/javascript" id="ncomments_js" src="http://livefyre.com/javascripts/ncomments.js#bn=<?php echo $blogid; ?>"></scr'+'ipt>');
}
</script>

<?php 

if($view == 'category' &&  $layout == 'blog') {
	$query = $db->getQuery(true);
	$query->select("*");
	$query->from("#__content");
	$query->where("catid = '".$_REQUEST['id']."'");
	try {
		$db->setQuery($query);
	}
	catch (JDatabaseException $e) {
		if ($use_log) {
		    JLog::add('Livefyre: Database error in listing.php', JLog::DEBUG, 'Livefyre');
		}
	}
				
	$data = $db->loadObject();
	$itemURL =$siteUrl.'/index.php?option=com_content&view=article&id='.$data->id;
		
?>

<!-- livefyre comments counter and anchor link -->
<a class="livefyre-ncomments" style="display:block; float:right;" href="<?php echo $itemURL; ?>#livefyre_thread" article_id="<?php echo $data->id; ?>" title="<?php echo JText::_('no comments'); ?>"><?php echo JText::_('no comments'); ?></a>

<?php
}
else if($_REQUEST['view'] == 'featured') {
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

	$data= $db->loadObject();
	$itemURL = $siteUrl.'/index.php?option=com_content&view=article&id='.$data->id;
	if($data->id){
?>
	<a class="livefyre-ncomments" style="display:block; float:right;" href="<?php echo $itemURL; ?>#livefyre_thread" article_id="<?php echo $data->id; ?>" title="<?php echo JText::_('no comments'); ?>"><?php echo JText::_('no comments'); ?></a>
<?php
	}
}

$livefyre_logger->add('Livefyre: Comment Count on article: Id: ' .$data->id, JLog::DEBUG, 'Livefyre');

?>
