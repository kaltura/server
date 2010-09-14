<?php
$service_url = requestUtils::getRequestHost();
$host = str_replace ( "http://" , "" , $service_url );
if ( $host == "www.kaltura.com" ) $host = "1";
$flash_dir = $service_url . myContentStorage::getFSFlashRootPath ();
?>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
var _partner_id, _subp_id, _uid;

function gotoLogin()
{
  window.location = "<?php echo $service_url ?>/index.php/kmc/kmc";
}

function closeLoginF()
{
//	alert('closeLoginF');
}


// -->
</script>

<div class="login">
	<div id="header">
		<img src="/lib/images/kmc/logo.gif" alt="Kaltura CMS" class="logo" />
	</div><!-- end #header -->
	<div id="login">
		<div class="wrapper">
			<div id="kaltura_flash_obj"></div>
		</div><!-- end wrapper -->
	</div><!-- end #login -->
</div>	


<script type="text/javascript">
	// attempt to login without params - see if there are cookies - the remMe is true so the expiry will continue 
		var flashVars = {
			tosUrl: "<?php echo $service_url ?>/index.php/kmc/TermsOfUse",
			loginF: "loginF" ,
			closeF: "closeLoginF" ,
			host: "<?php echo $host ?>"
		}
	
		var params = {
			allowscriptaccess: "always",
			allownetworking: "all",
			bgcolor: "#1B1E1F",
			quality: "high",
			wmode: "opaque" ,
			movie: "<?php echo $flash_dir ?>/kmc/signup/v1.0.2/signup.swf"
		};
		swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/signup/v1.0.2/signup.swf", 
			"kaltura_flash_obj", "350", "440", "9.0.0", false, flashVars , params);

</script>

