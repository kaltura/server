<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if( $entry_id ) { ?>
	<meta property="og:url" content="<?php echo $pageURL; ?>" />
	<meta property="og:title" content="<?php echo htmlspecialchars($entry_name); ?>" />
	<meta property="og:description" content="<?php echo htmlspecialchars($entry_description); ?>" />
	<meta property="og:type" content="video.other" />
	<meta property="og:image" content="<?php echo $entry_thumbnail_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
	<meta property="og:video" content="<?php echo $swfUrl; ?>" />
	<meta property="og:video:secure_url" content="<?php echo $swfSecureUrl; ?>" />
	<meta property="og:video:width" content="<?php echo $uiConf->getWidth();?>" />
	<meta property="og:video:height" content="<?php echo $uiConf->getHeight();?>" />	
	<meta property="og:video:type" content="application/x-shockwave-flash" />
	<?php if( isset($flavor_asset_id) ) { ?>
	<meta property="og:video" content="<?php echo $flavorUrl; ?>" />
	<meta property="og:video:type" content="video/mp4" />	
	<?php } ?>
	<meta property="og:site_name" content="Kaltura" />
	<?php } ?>
	
	<title><?php echo htmlspecialchars($entry_name); ?></title>
	<style>
		#main .content .title h1 { font-size: 24px; font-weight: bold; }
		#main p { margin-bottom: 20px; font-size: 18px; }
	</style>
</head>
<body>
	<div id="main" style="position: static;">

		<div class="content">
			<div class="title">
				<h1><?php echo htmlspecialchars($entry_name); ?></h1>
			</div>
			<div class="contwrap">
			<p><?php echo htmlspecialchars($entry_description); ?></p>
			<div id="videoContainer">
				<script src="<?php echo $scriptUrl; ?>"></script>
				<script>mw.setConfig('Kaltura.NoApiCache', true);</script>
				<?php if($embed == 'dynamic' || $embed == 'thumb') { ?>
				<div id="kaltura_player" style="width: <?php echo $uiConf->getWidth();?>px; height: <?php echo $uiConf->getHeight();?>px"></div>
				<script type="text/javascript">
				kWidget.<?php echo $functionName; ?>(<?php echo json_encode($kwidgetObj); ?>);
				</script>
				<?php } ?>
				<?php if($embed == 'legacy') { ?>
			    <object id="kaltura_player" name="kaltura_player" type="application/x-shockwave-flash" 
				    allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="<?php echo $uiConf->getHeight();?>" width="<?php echo $uiConf->getWidth();?>"
				    xmlns:dc="http://purl.org/dc/terms/" 
				    xmlns:media="http://search.yahoo.com/searchmonkey/media/" 
				    rel="media:video" 
				    resource="<?php echo $swfUrl; ?>" 
				    data="<?php echo $swfUrl; ?>">
				<param name="allowFullScreen" value="true" />
				<param name="allowNetworking" value="all" />
				<param name="allowScriptAccess" value="always" />
				<param name="bgcolor" value="#000000" />
				<param name="flashVars" value="<?php echo $flashVarsString; ?>" />
				<param name="movie" value="<?php echo $swfUrl; ?>" />
				<a href="http://corp.kaltura.com">video platform</a> 
				<a href="http://corp.kaltura.com/video_platform/video_management">video management</a> 
				<a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a> 
				<a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a> 
				<span property="dc:description" content=""></span>
				<span property="media:title" content="Kaltura Video"></span>
				<span property="media:width" content="<?php echo $uiConf->getWidth();?>"></span>
				<span property="media:height" content="<?php echo $uiConf->getHeight();?>"></span>
				<span property="media:type" content="application/x-shockwave-flash"></span> 
			   </object>
			   <?php } ?>
			</div>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
</body>
</html>