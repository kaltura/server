<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta property="og:url" content="<?php echo $pageURL; ?>" />
	<meta property="og:title" content="<?php echo addslashes(strip_tags(html_entity_decode($entry_name))); ?>" />
	<meta property="og:description" content="<?php echo addslashes(strip_tags(html_entity_decode($entry_description))); ?>" />
	<meta property="og:type" content="video.other" />
	<meta property="og:image" content="<?php echo $entry_thumbnail_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
	<meta property="og:image:secure_url" content="<?php echo $entry_thumbnail_secure_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
	
	<?php if( isset($flavorUrl) ) { ?>
	<meta property="og:video" content="<?php echo $flavorUrl; ?>" />
	<meta property="og:video:type" content="video/mp4" />	
	<?php } ?>
	
	<meta property="og:video:url" content="<?php echo $playerUrl; ?>">
	<meta property="og:video:secure_url" content="<?php echo $playerUrl; ?>">
	<meta property="og:video:type" content="text/html">
	<meta property="og:video:width" content="<?php echo $uiConf->getHeight();?>">
	<meta property="og:video:height" content="<?php echo $uiConf->getHeight();?>">

	<meta name="twitter:card" content="player"/>
    <meta name="twitter:site" content="@kaltura"/>
    <meta name="twitter:creator" content="@kaltura"/>
    <meta name="twitter:title" content="<?php echo htmlspecialchars($entry_name); ?>" />
    <meta name="twitter:description" content="<?php echo htmlspecialchars($entry_description); ?>" />
    <meta name="twitter:image" content="<?php echo $entry_thumbnail_secure_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
    <meta name="twitter:player" content="<?php echo $playerUrl; ?>" />
    <?php if( isset($flavorUrl) ) { ?>
    <meta name="twitter:player:stream" content="<?php echo $flavorUrl; ?>" />
    <meta name="twitter:player:stream:content_type" content="video/mp4"/>
    <?php } ?>
    <meta name="twitter:player:height" content="<?php echo $uiConf->getHeight();?>" />
    <meta name="twitter:player:width" content="<?php echo $uiConf->getWidth();?>" />

	<meta property="og:site_name" content="Kaltura" />
	<title><?php echo htmlspecialchars($entry_name); ?></title>
</head>
<body>

<script src="<?php echo $html5Url; ?>"></script>
<div id="kaltura_player" style="width: <?php echo $uiConf->getWidth(); ?>px; height: <?php echo $uiConf->getHeight(); ?>px;"></div>
<script>
kWidget.embed({
  "targetId": "kaltura_player",
  "wid": "<?php echo $widget->getId(); ?>",
  "uiconf_id": <?php echo $uiConf->getId(); ?>,
  "entry_id": "<?php echo $entry->getId(); ?>"
});
</script>

</body> 
</html>
