<?php
/*
* overlay   plugin 1.0
* Joomla plugin
* by Purple Cow Websites
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/
// no direct access

defined('_JEXEC') or die('Restricted access');

// Include the JLog class.
jimport('joomla.log.log');

echo $row->text;
?>

<script type="text/javascript" src="http://zor.livefyre.com/wjs/v3.0/javascripts/livefyre.js"></script> 

<div id="lfcomments">
    <?php

    // Get Bootstrap HTML
    $article_id_b64 = base64_encode($articleId);
    $url = "http://data.bootstrap.fyre.co/$lf_domain/$blogid/$article_id_b64/bootstrap.html";

    // Request it
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_POST, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    $raw_resp=curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Don't insert if the collection doesn't exist yet
    if ($http_status == '200') {
        echo $raw_resp; 
    }
    curl_close ($ch);

?>
</div>

<script type="text/javascript">
<?php

$article_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Include JWT
if (class_exists('LF_JWT') != true) {
    include(dirname(__FILE__).'/../includes/livefyre/JWT.php');
}

// Add debug statement if available
if (version_compare( JVERSION, '1.7', '>=') == 1 && JDEBUG) {
    jimport('joomla.log.log');
    JLog::addLogger(array());
    JLog::add('Livefyre: Outputing on article: Id: ' .$articleId. ' Title: ' .$articleTitle. ' URL: ' .$article_url, JLog::DEBUG, 'Livefyre');
    $use_log = true;
}

// Create collectionMeta
$meta = array(
    "articleId" => $articleId,
    "title" => $articleTitle,
    "url" => $article_url
);
$collectionMeta = LF_JWT::encode($meta, $site_key);

?>

fyre.conv.load({
    network: '<?php echo $lf_domain; ?>'
}, [{
    el: "lfcomments",
    siteId: '<?php echo $blogid; ?>',
    articleId: '<?php echo $articleId; ?>',
    collectionMeta: '<?php echo $collectionMeta; ?>'
    }], function(comments) {

});
</script>