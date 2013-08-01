<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie10 lt-ie9"> <![endif]-->
<!--[if lt IE 10]>     <html class="lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if( $entry_id ) { ?>
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
	<?php if( isset($flavor_asset_id) ) { ?>
	<meta property="og:video" content="<?php echo $flavorUrl; ?>" />
	<meta property="og:video:type" content="video/mp4" />	
	<?php } ?>
	<meta property="og:site_name" content="Kaltura" />
	<?php } ?>
	<title><?php echo htmlspecialchars($entry_name); ?></title>
	<link type="text/css" rel="stylesheet" href="/lib/css/kmc.css" />
	<?php if($framed)  { ?>
	<style>
	html, body {margin: 0; padding: 0; width: 100%; height: 100%; } 
	body { background-color: #fff !important; }
	#framePlayerContainer {margin: 0 auto; padding-top: 20px; text-align: center; } 
	object, div { margin: 0 auto; }
	</style>
	<?php } else { ?>
	<style>
	#main .content .title h1 { font-size: 24px; font-weight: bold; }
	#main p { margin-bottom: 20px; font-size: 18px; }
	</style>
	<?php } ?>
	<!--[if lte IE 7]>
	<script src="/lib/js/json2.min.js"></script>
	<![endif]-->
	<script src="/lib/js/jquery-1.8.3.min.js"></script>
	<script src="/lib/js/KalturaEmbedCodeGenerator-1.0.6.min.js"></script>	
</head>
<body>
	<?php if(!$framed) { ?>
	<div id="main" style="position: static;">

		<div class="content">
			<div class="title">
				<h1><?php echo htmlspecialchars($entry_name); ?></h1>
			</div>
			<div class="contwrap">
			<p><?php echo htmlspecialchars($entry_description); ?></p>
			<div id="videoContainer">
	<?php } ?>
				<div id="framePlayerContainer">
<script>
var scriptToEval = '';
var code = new kEmbedCodeGenerator(<?php echo json_encode($embedParams); ?>).getCode();
var embedType = '<?php echo $embedType;?>';
var ltIE10 = $('html').hasClass('lt-ie10');

// IE9 and below has issue with document.write script tag
if( ltIE10 && (embedType == 'dynamic' || embedType == 'thumb') ) {
	$(code).each(function() {
		if( ! this.outerHTML ) return true;
		if( this.nodeName === 'SCRIPT' ) {
			// If we have external script, append to head
			if( this.src ) {
				$.getScript(this.src, function() {
					$.globalEval(scriptToEval);
				});
			} else {
				scriptToEval += this.innerHTML;
			}
		} else {
			// Write any other elements
			document.write(this.outerHTML);
		}
	});
} else {
	document.write(code);
}
</script>
				</div>
<?php if(!$framed) { ?>				
			</div>
<!--<br /><p>This page is for preview only. Not for production use.</p>-->
			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
<?php } ?>
</body>
</html>