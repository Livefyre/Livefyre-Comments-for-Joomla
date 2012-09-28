<?php 
/*
//   "livefyre Comment System" Plugin for Joomla! 1.5.x - Version 2.2
// Copyright (c) 2006 - 2009   Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www. .gr
// Designed and developed by the  team
// ***Last update: November 14th, 2009***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php echo $row->text; ?>
<?php
	if (!isset($plugin) || !isset($blogid)) {
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
		$pluginParams = new JParameter( $plugin->params );
		$blogid = $pluginParams->get( 'blogid' );
	}
?>

<script type="text/javascript">
if (typeof(document.getElementById('ncomments_js')) == 'undefined' || document.getElementById('ncomments_js') == null) {
	document.write('<scr'+'ipt type="text/javascript" id="ncomments_js" src="http://livefyre.com/javascripts/ncomments.js#bn=<?php echo $blogid; ?>"></scr'+'ipt>');
}
</script>
<?php 

 if($view=='category' &&  $layout=='blog')
		{
		 $query="SELECT * FROM `#__content` where catid=".$_REQUEST['id']; 
				$db->setQuery($query);
				if (!$db->query())
				{
				JError::raiseError(500, $db->getErrorMsg() );
				}
				
				$data= $db->loadObject();
		//http://localhost/Joomla_2.5.2/index.php?option=com_content&view=category&layout=blog&id=9&Itemid=107
	      $itemURL =$siteUrl.'/index.php?option=com_content&view=article&id='.$data->id;
	        
//     echo '<prE>';
//	 print_r($_REQUEST);
//	 echo '</prE>';
		
	?>
<!-- livefyre comments counter and anchor link -->
<a class="livefyre-ncomments" style="display:block; float:right;" href="<?php echo $itemURL; ?>#livefyre_thread" article_id="<?php echo $data->id; ?>" title="<?php echo JText::_('no comments'); ?>"><?php echo JText::_('no comments'); ?></a>

<?php } ?>