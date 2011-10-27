<?php 

//Build Script URL
$scriptUrl = $partner_host . "/p/". $partner_id ."/sp/". $partner_id ."00/embedIframeJs/uiconf_id/". $uiconf_id ."/partner_id/". $partner_id;

// Build SWF URL
$swfUrl = $partner_host ."/index.php/kwidget";
$swfUrl .= "/cache_st/" . (time()+(60*15));
$swfUrl .= "/wid/_" . $partner_id;
$swfUrl .= "/uiconf_id/" . $uiconf_id;
if( $entry_id ) {
	$swfUrl .= "/entry_id/" . $entry_id;
}

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
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Kaltura Player</title>
	<script src="<?php echo $scriptUrl; ?>"></script>
	<script>mw.setConfig('Kaltura.NoApiCache', true);</script>
</head>
<body>
	<div id="main" style="position: static;">

		<div class="content">
			<div class="title">
				<h2>Kaltura Player</h2>
			</div>
			<div class="contwrap">
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
				<span property="media:width" content="400"></span>
				<span property="media:height" content="333"></span> 
				<span property="media:type" content="application/x-shockwave-flash"></span> 
			   </object>
			</div>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
</body>
</html>