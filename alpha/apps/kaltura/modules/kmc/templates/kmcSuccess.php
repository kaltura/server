<?php
$service_url = requestUtils::getHost();

$www_host = kConf::get('www_host');
$https_enabled = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;
if (kConf::get('kmc_secured_login') || $https_enabled) {
	$flash_dir = 'https://';
}
else {
	$flash_dir = 'http://';
}

$flash_dir .= $www_host .'/'. myContentStorage::getFSFlashRootPath ();

$beta_str = $beta ? "/beta/{$beta}" : "";
?>
<script type="text/javascript">var service_url = "<?php echo $service_url ?>";</script>
<script type="text/javascript" src="/lib/js/kmc.login.js"></script>

<style>
 body { background-color:#272929 !important; background-image:none !important;}
  div#login { width:500px; margin: 0 auto; text-align:center;}
</style>

<form id="form1" action="<?php echo $service_url ?>/index.php/kmc/kmc2<?php echo $beta_str ?>" method="post">
	<input type="hidden" name="_partner_id">
	<input type="hidden" name="_subp_id">
	<input type="hidden" name="_uid">
	<input type="hidden" name="_ks">
</form>	


<div id="kmcHeader">
	<img src="<?php echo $service_url; ?>/lib/images/kmc/logo_kmc.png" alt="Kaltura CMS" />
	<div id="user_links">
    	<a href="<?php echo $service_url; ?>/content/docs/pdf/KMC_User_Manual.pdf" target="_blank">User Manual</a>
	</div> 
</div><!-- end kmcHeader -->
    
<div id="login">
    <div id="login_swf"><img src="/lib/images/kmc/flash.jpg" alt="Install Flash Player" /><span>You must have flash installed. <a href="http://get.adobe.com/flashplayer/" target="_blank">click here to download</a></span></div>
</div>

<script type="text/javascript">
// attempt to login without params - see if there are cookies - the remMe is true so the expiry will continue 
if ( !loginF ( null , null , null , null , true ) ) {
	var flashVars = {
		<?php
			if (isset($setPassHashKey) && $setPassHashKey) {
				echo 'hashKey: "'.$setPassHashKey.'",';
			}
		?>
		<?php
			if (isset($hashKeyErrorCode) && $hashKeyErrorCode) {
				echo 'errorCode: "'.$hashKeyErrorCode.'",';
			}
		?>
		loginF: "loginF",
		closeF: "closeLoginF",
		host: "<?php echo $www_host; ?>",
		displayErrorFromServer: "<?php echo ($displayErrorFromServer)? 'true': 'false'; ?>",
		visibleSignup: "<?php echo (kConf::get('kmc_login_show_signup_link'))? 'true': 'false'; ?>",
		urchinNumber: "UA-12055206-1",
		srvurl: "api_v3/index.php"
	}

	var params = {
		allowscriptaccess: "always",
		allownetworking: "all",
		bgcolor: "#272929",
		quality: "high",
		wmode: "window" ,
		movie: "<?php echo $flash_dir ?>/kmc/login/<?php echo $kmc_login_version ?>/login.swf"
	};
	swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/login/<?php echo $kmc_login_version ?>/login.swf", "login_swf", "384", "350", "10.0.0", "<?php echo $service_url ?>/expressInstall.swf", flashVars, params);
}
</script>