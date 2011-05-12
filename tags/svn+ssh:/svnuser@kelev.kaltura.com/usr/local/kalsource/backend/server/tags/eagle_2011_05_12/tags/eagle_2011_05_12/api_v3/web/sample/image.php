<?php
require_once("../../bootstrap.php");
require_once("config.php");
require_once("lib/KalturaClient.php");

$entryId = @$_GET["entryId"];
$config = new KalturaConfiguration(PARTNER_ID);
$config->serviceUrl = SERVER_URL;
$client = new KalturaClient($config);
$ks = $client->session->start(ADMIN_SECRET, "USERID", KalturaSessionType::ADMIN);
$client->setKs($ks);
$mediaEntry = $client->media->get($entryId);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Sample Kit - Player</title>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage imagePage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			<div id="row1" class="clearfix">
				<?php require("header.php"); ?>
				<img class="imageEntry" src="<?php echo $mediaEntry->dataUrl; ?>" alt="<?php $mediaEntry->name; ?>" />					
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("footer_nav.php"); ?>
	</div>
</body>
</html>