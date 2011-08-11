<?php
require_once("../../../bootstrap.php");
require_once("../config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Kaltura Demo Playlists</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="../css/layout.css" />
    <script type="text/javascript" src="js/swfobject.js"></script>
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage playlistsPage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			<div id="row1" class="clearfix">
				<h1>Playlist Gallery</h1>
				<p>
					View and play with the different playlist widgets that are supported by the system (Flash or HTML): Flash playlists are accessible from the <a href="<?php echo SERVER_URL; ?>/index.php/kmc">Kaltura Management Console</a> where you can add your own content either manually or dynamically (tag based or rule based). The HTML playlist is a good example of the flexibilities of working with the Kaltura API. It can be used as a base for customization of your own.
				</p>
				<p>
					Need a custom playlist widget? <a href="http://corp.kaltura.com/support/form/project/41" target="_blank">contact us</a>
				</p>
				<h2>Horizontal playlist</h2>
				<div id="kaltura_playlist_horizontal_wrap"></div>
				<script type="text/javascript">
					 var kaltura_swf = new SWFObject("<?php echo SERVER_URL; ?>/kwidget/wid/_1/ui_conf_id/48306", "kaltura_playlist_horizontal", "724", "322", "9", "#ffffff");
					 kaltura_swf.addParam("wmode", "opaque");
					 kaltura_swf.addParam("flashVars", "autoPlay=false&layoutId=playlistLight&uid=0&partner_id=1&subp_id=100&k_pl_autoContinue=true&k_pl_autoInsertMedia=true&k_pl_0_name=Demo Playlist 1&k_pl_0_url=<?php echo rawurlencode(SERVER_URL."/index.php/partnerservices2/executeplaylist?uid=&partner_id=1&subp_id=100&format=8&ks={ks}&playlist_id=644igevrzs"); ?>");
					 kaltura_swf.addParam("allowScriptAccess", "always");
					 kaltura_swf.addParam("allowFullScreen", "true");
					 kaltura_swf.addParam("allowNetworking", "all");
					 kaltura_swf.write("kaltura_playlist_horizontal_wrap");
				</script>

				<h2>Vertical compact playlist</h2>
				<div id="kaltura_playlist_vertical_wrap"></div>
				<script type="text/javascript">
					 var kaltura_swf = new SWFObject("<?php echo SERVER_URL; ?>/kwidget/wid/_1/ui_conf_id/48304", "kaltura_playlist_vertical", "400", "600", "9", "#ffffff");
					 kaltura_swf.addParam("wmode", "opaque");
					 kaltura_swf.addParam("flashVars", "autoPlay=false&layoutId=playlistLight&uid=0&partner_id=1&subp_id=100&k_pl_autoContinue=true&k_pl_autoInsertMedia=true&k_pl_0_name=Demo Playlist 2&k_pl_0_url=<?php echo rawurlencode(SERVER_URL."/index.php/partnerservices2/executeplaylist?uid=&partner_id=1&subp_id=100&format=8&ks={ks}&playlist_id=8j12w5m41s"); ?>");
					 kaltura_swf.addParam("allowScriptAccess", "always");
					 kaltura_swf.addParam("allowFullScreen", "true");
					 kaltura_swf.addParam("allowNetworking", "all");
					 kaltura_swf.write("kaltura_playlist_vertical_wrap");
				</script>
				
				<h2>HTML-based playlist</h2>
				<iframe src="html_playlist.php" width="782px" height="400px"></iframe>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("../footer_nav.php"); ?>
	</div>
</body>
</html>
