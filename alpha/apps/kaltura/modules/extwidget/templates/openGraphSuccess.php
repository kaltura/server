<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie10 lt-ie9"> <![endif]-->
<!--[if lt IE 10]>     <html class="lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta property="og:url" content="<?php echo $pageURL; ?>" />
	<meta property="og:title" content="<?php echo htmlspecialchars($entry_name); ?>" />
	<meta property="og:description" content="<?php echo htmlspecialchars($entry_description); ?>" />
	<meta property="og:type" content="video.other" />
	<meta property="og:image" content="<?php echo $entry_thumbnail_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
	<meta property="og:image:seucre_url" content="<?php echo $entry_thumbnail_secure_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
	<meta property="og:video" content="<?php echo $swfUrl; ?>" />
	<meta property="og:video:secure_url" content="<?php echo $swfSecureUrl; ?>" />
	<meta property="og:video:width" content="<?php echo $uiConf->getWidth();?>" />
	<meta property="og:video:height" content="<?php echo $uiConf->getHeight();?>" />	
	<meta property="og:video:type" content="application/x-shockwave-flash" />
	<?php if( isset($flavorUrl) ) { ?>
	<meta property="og:video" content="<?php echo $flavorUrl; ?>" />
	<meta property="og:video:type" content="video/mp4" />	
	<?php } ?>
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
