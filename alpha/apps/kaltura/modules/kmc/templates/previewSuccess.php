<?php 

/*
 * TODO!!!
 * Move all this code to previewAction.
 */

//Build Script URL
$scriptUrl = $partner_host . "/p/". $partner_id ."/sp/". $partner_id ."00/embedIframeJs/uiconf_id/". $uiconf_id ."/partner_id/". $partner_id;

// Build SWF URL
$swfPath = "/index.php/kwidget";
$swfPath .= "/cache_st/" . (time()+(60*15));
$swfPath .= "/wid/_" . $partner_id;
$swfPath .= "/uiconf_id/" . $uiconf_id;
if( $entry_id ) {
	$swfPath .= "/entry_id/" . $entry_id;
}

$swfUrl = $partner_host . $swfPath;
$swfSecureUrl = 'https://' . $secure_host . $swfPath;

// Array to contain flash vars
$flashVars = array();

// Set the current flash vars for delivery type
switch($delivery_type) {

    case "rtmp":
		$flashVars["streamerType"] = "rtmp";
		break;

    case "akamai":
		$flashVars["streamerType"] = "hdnetwork";
		$flashVars["akamaiHD.loadingPolicy"] = "preInitialize";
		$flashVars["akamaiHD.asyncInit"] = "true";
		break;
}

if( $playlist_id ) {

	// build playlist url
	$playlist_url = $partner_host ."/index.php/partnerservices2/executeplaylist?";
	$playlist_url .= "partner_id=" . $partner_id . "&subp_id=" . $partner_id . "00&format=8&playlist_id=" . $playlist_id;

	// Add playlist flashVars
	$flashVars["playlistAPI.autoInsert"] = "true";
	$flashVars["playlistAPI.kpl0Name"] = $playlist_name;
	$flashVars["playlistAPI.kpl0Url"] = urlencode($playlist_url);
}
// Transform flashvars array to string
$flashVars = http_build_query($flashVars, '', '&amp;');

// URL to this page
$protocol = 'http';
if ($_SERVER["HTTPS"] == "on") {$protocol .= "s";}
$pageURL = $protocol . "://";
if ($_SERVER["SERVER_PORT"] != "80") {
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
} else {
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}
 //$_SERVER['PATH_INFO']
if( $flavor_asset_id ) {
	$flavorUrl = $partner_host . '/p/'. $partner_id .'/sp/' . $partner_id . '00/playManifest/entryId/' . $entry_id . '/flavorId/' . $flavor_asset_id . '/format/url/protocol/' . $protocol . '/a.mp4';
}

// <meta property="og:image:secure_url" content="/p/27017/sp/2701700/thumbnail/entry_id/1_elwxyx1c/version/0" />
// <meta property="fb:app_id" content="351010711616984">
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if( ! $playlist_id ) { ?>
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
	<?php if( $flavor_asset_id ) { ?>
	<meta property="og:video" content="<?php echo $flavorUrl; ?>" />
	<meta property="og:video:type" content="video/mp4" />	
	<?php } ?>
	<meta property="og:site_name" content="Kaltura" />
	<?php } ?>
	
	<title><?php echo htmlspecialchars($entry_name); ?></title>
	<script src="<?php echo $scriptUrl; ?>"></script>
	<script>mw.setConfig('Kaltura.NoApiCache', true);</script>
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
				<param name="flashVars" value="<?php echo $flashVars; ?>" />
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
			</div>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
</body>
</html>