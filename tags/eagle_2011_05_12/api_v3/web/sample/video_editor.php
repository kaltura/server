<?php
require_once("../../bootstrap.php");
require_once("config.php");
require_once("lib/KalturaClient.php");

$entryId = @$_GET["entryId"];

$config = new KalturaConfiguration(PARTNER_ID);
$config->serviceUrl = SERVER_URL;
$client = new KalturaClient($config);

$ks = $client->session->start(SECRET, "USERID", KalturaSessionType::USER, PARTNER_ID, 86400, "edit:*");

$flashVars = array();
$flashVars["partnerId"] = PARTNER_ID;
$flashVars["subpId"] = PARTNER_ID * 100;
$flashVars["uid"] = "USERID";
$flashVars["ks"] 		= $ks;
$flashVars["kshowId"] 	= -1;
$flashVars["entryId"] 	= $entryId;
$flashVars["backF"] 	= "onSimpleEditorBackClick";
$flashVars["saveF"] 	= "onSimpleEditorSaveClick";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Sample Kit - Add Video</title>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
	<script type="text/javascript">
		function onSimpleEditorBackClick() {
			window.location.href = "mix_gallery.php";
		}

		function onSimpleEditorSaveClick() {
			window.location.href = "player.php?entryId=<?php echo $entryId; ?>";
		}
	</script>
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			<div id="row1" class="clearfix">
				<?php require("header.php"); ?>
				<div id="kseWrap">
					<div id="kse"></div>
				</div>
				<script type="text/javascript">
					var params = {
						allowscriptaccess: "always",
						allownetworking: "all",
						wmode: "opaque"
					};
					
					var flashVars = <?php echo json_encode($flashVars); ?>;
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kse/ui_conf_id/36300", "kse", "890", "546", "9.0.0", false, flashVars, params);
				</script>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("footer_nav.php"); ?>
	</div>
</body>
</html>