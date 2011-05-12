<?php
require_once("../../../bootstrap.php");
require_once("../config.php");
require_once("../lib/KalturaClient.php");

$entryId = "iiwkp77qfk";

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
$flashVars["jsDelegate"] 	= "callbacksObj";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Advanced Editor</title>
	<script type="text/javascript" src="../js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="../js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/layout.css" />
	<script type="text/javascript">
		var callbacksObj = {
			publishHandler:publishHandler,
			closeHandler:closeHandler
		};		
		function publishHandler() {
			window.location.href = "player.php";
		}

		function closeHandler() {
			window.location.href = "player.php";
		}
	</script>
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage boxedPage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			
			<!--~~~~~~~~~~~~ Row1 ~~~~~~~~~~~~-->
			<div id="row1" class="clearfix">
				<h1>Advanced Editor</h1>
				<div id="kaeWrap">
					<div id="kae"></div>
				</div>
				<script type="text/javascript">
					var params = {
						allowscriptaccess: "always",
						allownetworking: "all",
						wmode: "opaque"
					};
					
					var flashVars = <?php echo json_encode($flashVars); ?>;
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kae/ui_conf_id/47401", "kae", "821px", "680", "9.0.0", false, flashVars, params);
				</script>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("../footer_nav.php"); ?>
	</div>
</body>
</html>