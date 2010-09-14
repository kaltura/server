<?php
require_once("../../bootstrap.php");
require_once("config.php");
require_once("lib/KalturaClient.php");

$config = new KalturaConfiguration(PARTNER_ID);
$config->serviceUrl = SERVER_URL;
$client = new KalturaClient($config);
$ks = $client->session->start(SECRET, "USERID", KalturaSessionType::USER);

$flashVars = array();
$flashVars["partnerId"] 	= PARTNER_ID;
$flashVars["sessionId"] 	= $ks;
$flashVars["kshowId"] 		= -2;
$flashVars["terms_of_use"]	= kConf::get('terms_of_use_uri');
$flashVars["afterAddEntry"] = "onContributionWizardAfterAddEntry";
$flashVars["close"] 		= "onContributionWizardClose";
$flashVars["showCloseButton"] = false; // because we don't show the contribution wizard in a modal window

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Starter Kit - Add Video</title>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
	<script type="text/javascript">
		function onContributionWizardAfterAddEntry(obj) {
			// nothing to do
		}

		function onContributionWizardClose() {
			window.location.href  = "mix_gallery.php";
		}
	</script>
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage addVideoPage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			<div id="row1" class="clearfix">
				<?php require("header.php"); ?>
				<div id="kcwWrap">
					<div id="kcw"></div>
				</div>
				<script type="text/javascript">
					var params = {
						allowScriptAccess: "always",
						allowNetworking: "all",
						wmode: "opaque"
					};
					
					var flashVars = <?php echo json_encode($flashVars); ?>;
					swfobject.embedSWF("<?php echo SERVER_URL; ?>/kcw/ui_conf_id/36200", "kcw", "680", "360", "9.0.0", false, flashVars, params);
				</script>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("footer_nav.php"); ?>
	</div>
</body>
</html>