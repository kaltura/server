<style>
body { background-color:#272929 !important; background-image:none !important;}
div#login { width:500px; margin: 0 auto; text-align:center;}
</style>
<link rel="stylesheet" type="text/css" media="screen" href="/lib/css/kmc5.css" />
<div id="kmcHeader">
	<?php if( $logoUrl ) { ?>
	<img src="<?php echo $logoUrl; ?>" />
	<?php } else { ?>
	<img src="/lib/images/kmc/logo_kmc.png" alt="Kaltura CMS" />
	<?php } ?>
	<div id="langIcon"></div>
	<div id="user_links" style="right: 36px">
    	<a href="/content/docs/pdf/KMC_User_Manual.pdf" target="_blank">User Manual</a>
	</div> 
</div><!-- end kmcHeader -->

<div id="langMenu"></div>

<div id="login">
	<div id="notSupported">Thank you for your logging into the Kaltura Management Console.<br />The KMC is no longer supported in Internet Explorer 7.<br />Please upgrade your Internet Explorer to a higher version or browse to the KMC from another browser.</div>
    <div id="login_swf"><img src="/lib/images/kmc/flash.jpg" alt="Install Flash Player" /><span>You must have flash installed. <a href="http://get.adobe.com/flashplayer/" target="_blank">click here to download</a></span></div>
</div>

<script type="text/javascript">
// Prevent the page to be framed
if(top != window) { top.location = window.location; }
// Options
var options = {
	secureLogin: <?php echo ($securedLogin) ? 'true' : 'false'; ?>,
	swfUrl: "<?php echo $swfUrl; ?>",
	flashVars: {
		host: "<?php echo $www_host; ?>",
		displayErrorFromServer: "<?php echo ($displayErrorFromServer)? 'true': 'false'; ?>",
		visibleSignup: "<?php echo (kConf::get('kmc_login_show_signup_link'))? 'true': 'false'; ?>",
		hashKey: "<?php echo (isset($setPassHashKey) && $setPassHashKey) ? $setPassHashKey : ''; ?>",
		errorCode: "<?php echo (isset($hashKeyErrorCode) && $hashKeyErrorCode) ? $hashKeyErrorCode : ''; ?>"
	}
};
</script>
<script src="/lib/js/kmc/6.0.10/langMenu.min.js"></script>
<script type="text/javascript" src="/lib/js/kmc.login.js"></script>