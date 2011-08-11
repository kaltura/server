<?php
require_once("../../../bootstrap.php");
require_once("../config.php");
require_once("../lib/KalturaClient.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Demo Players</title>
	<script type="text/javascript" src="../js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="../js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/layout.css" />
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage playersPage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			
			<!--~~~~~~~~~~~~ Row1 ~~~~~~~~~~~~-->
			<div id="row1" class="clearfix">
				<h1>Players Gallery</h1>
				<p>
					Below is a collection of default players that come with the system. The black and white players are accessible from the <a href="<?php echo SERVER_URL; ?>/index.php/kmc">Kaltura Management Console</a> the blue player is available as part of the <a href="<?php echo SERVER_URL; ?>/help/wordpress-kaltura_integration.html">WordPress extension</a>.
				</p>
				<p>
					Need a custom player? You can either do it yourself, <a href="http://corp.kaltura.com/wiki/index.php/KDP" target="_blank">see documents here</a> or <a href="http://corp.kaltura.com/support/form/project/41" target="_blank">contact us</a>
				</p>
				<div class="playerBox">
					<h2>Dark Player</h2>
					<div id="kplayer1"></div>
				</div>
				<div class="playerBox">
					<h2>Light Player</h2>
					<div id="kplayer2"></div>
				</div>
				<div class="playerBox">
					<h2>White/Blue Player</h2>
					<div id="kplayer3"></div>
				</div>
				<br clear="all" />
				<p class="more">See additional players <a href="http://corp.kaltura.com/technology/video_player" target="_blank">here</a></p>
				<script type="text/javascript">
					var params = { 
						allowscriptaccess: "always", 
						allownetworking: "all",
						allowfullscreen: "true", 
						bgcolor: "#000000", 
						wmode: "opaque" 
					};
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kwidget/wid/_<?php echo PARTNER_ID; ?>/ui_conf_id/48410/entry_id/1i5ojrn9ts", "kplayer1", "400", "362", "9.0.0", false, null, params);
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kwidget/wid/_<?php echo PARTNER_ID; ?>/ui_conf_id/48411/entry_id/1i5ojrn9ts", "kplayer2", "400", "362", "9.0.0", false, null, params);
					
					var flashVars = {
						entryId: "1i5ojrn9ts"
					};
					<?php /* FIXME: old player version is used here, so the embed code and the functions are different */ ?>
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kwidget/wid/_1_520", "kplayer3", "400", "362", "9.0.0", false, flashVars, params);
				</script>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("../footer_nav.php"); ?>
	</div>
</body>
</html>