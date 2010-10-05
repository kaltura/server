<?php
$service_url = requestUtils::getHost();
$host = str_replace ( "http://" , "" , $service_url );
if ( $host == "www.kaltura.com" ) $host = "1";

$www_host = kConf::get('www_host');
if (kConf::get('kmc_secured_login')) {
	$flash_dir = 'https://';
}
else {
	$flash_dir = 'http://';
}

$flash_dir .= $www_host .'/'. myContentStorage::getFSFlashRootPath ();

$beta_str = $beta ? "/beta/{$beta}" : "";
?>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
var _partner_id, _subp_id, _uid;

function empty(val) {
	if(val === null)
		return true;
	return false;
}
function loginF(remMe, partner_id, subp_id, uid, ks , screen_name, email) {
	var has_cookie = false;
	if (partner_id == null) {
		partner_id = getCookie ( "pid" );
		subp_id = getCookie ( "subpid" );
		uid = getCookie ( "uid" );
		ks = getCookie ( "kmcks" );
		screen_name = getCookie ("screen_name" );
		email = getCookie ("email" );
		// if any of the required params is null - return false and the login page will be displayed
		if ( empty(partner_id) || empty(subp_id) || empty(uid) || empty(ks) )
			return false;
		has_cookie = true;
	}
//	alert( partner_id + " | " +  subp_id + " | " +   uid + " | " + ks + " | " + remMe);
	_partner_id = partner_id;
	_subp_id = subp_id;
	_uid = uid;
	path = '/';

	if ( remMe )
		exp = 30*86400; // 30 days in seconds - expiration of KS
	else
		exp = 24*3600-15; // set the cookies to expire in just under 24 hours

	if (!has_cookie) {
		setCookie("pid", partner_id, exp, path);
		setCookie("subpid", subp_id, exp, path);
		setCookie("uid", uid , exp, path);
		setCookie("kmcks", ks, exp, path);
		setCookie("screen_name", screen_name, exp, path);
		setCookie("email", email, exp, path);
	}

//	url = "<?php echo $service_url ?>/index.php/kmc/kmc2<?php echo $beta_str ?>?partner_id=" + partner_id + "&subp_id=" + subp_id + "&uid=" +
//		uid + "&ks=" + ks + "&screen_name=" + screen_name + "&email=" + email + location.hash ;
		var state = location.hash || "" ;
	url = "<?php echo $service_url ?>/index.php/kmc/kmc2"+state;
//	alert ( url );
	window.location = url;

	// TODO - send by post using form1
	return true;			
}

function closeLoginF()
{
//	alert('closeLoginF');
}

function gotoSignup()
{
	window.location = "<?php echo $service_url ?>/index.php/kmc/signup";
}

var kmc = {};
// -->
</script>

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
        	<a href="<?php echo $service_url; ?>/lib/pdf/KMC_Quick_Start_Guide.pdf" target="_blank">Quickstart Guide</a>
		</div> 
	</div><!-- end kmcHeader -->
    
	<div id="login">
		<div id="login_swf"></div>
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
			loginF: "loginF" ,
			closeF: "closeLoginF" ,
			host: "<?php echo $www_host ?>",
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
		swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/login/<?php echo $kmc_login_version ?>/login.swf", "login_swf", "384", "350", "9.0.0", false, flashVars, params);
	}
</script>

