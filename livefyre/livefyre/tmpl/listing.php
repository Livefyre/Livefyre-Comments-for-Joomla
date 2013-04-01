<?php 
/*
* "livefyre Comment System" Plugin for Joomla! 1.5.x - Version 2.2
* Copyright (c) 2006 - 2009   Ltd. All rights reserved.
* Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
* More info at http://www. .gr
* Designed and developed by the  team
* Last update: November 14th, 2009***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

echo $row->text;

if (!isset($plugin) || !isset($blogid)) {
	if ( version_compare( JVERSION, '3.0', '>=' ) == 1) {
		$blogid = $this->params->get('blogid');
	} else {
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
		$pluginParams = new JParameter( $plugin->params );
		$blogid = $pluginParams->get( 'blogid' );
	}
	// $plugin =& JPluginHelper::getPlugin('content', $plg_name);
	// $pluginParams = new JParameter( $plugin->params );
	// $blogid = $pluginParams->get( 'blogid' );
}

// Set up a debug log if available
if (version_compare( JVERSION, '1.7', '>=') == 1 && JDEBUG) {
	jimport('joomla.log.log');
	JLog::addLogger(array());
	$use_log = true;
}
?>

<script type="text/javascript">
if (typeof(document.getElementById('ncomments_js')) == 'undefined' || document.getElementById('ncomments_js') == null) {
	document.write('<scr'+'ipt type="text/javascript" id="ncomments_js" src="http://livefyre.com/javascripts/ncomments.js#bn=<?php echo $blogid; ?>"></scr'+'ipt>');
}
</script>

<?php 

if($view=='category' &&  $layout=='blog') {
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
} else if($_REQUEST['view'] == 'featured') {
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

// For debugging purposes
if ($use_log) {
    JLog::add('Livefyre: Comment Count on article: Id: ' .$data->id, JLog::DEBUG, 'Livefyre');
}

?>
