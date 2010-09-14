<?php
$service_url = requestUtils::getRequestHost();
$host = str_replace ( "http://" , "" , $service_url );
$cdn_host = str_replace ( "http://" , "" , myPartnerUtils::getCdnHost($partner_id) );
$kmc_content_version = 'v1.1.8';
$kmc_account_version = 'v1.1.6';
$kmc_appstudio_version = 'v1.2.0';
$kmc_rna_version = 'v1.0.3';

$flash_dir = $service_url . myContentStorage::getFSFlashRootPath ();

?>
<script>
sub_nav_tab = "";
current_module = 'reports';
var flashVars = {	
		'host' : "<?php echo $host ?>" , 
		'cdnhost' : "<?php echo $cdn_host ?>" ,
		'uid' : "<?php echo $uid ?>" ,
		'partner_id' : "<?php echo $partner_id ?>",
		'srvurl' : 'api_v3/index.php',
		'innerKdpVersion' : 'v2.5.2.30876',
		'kdpUrl' : "<?php echo $flash_dir ?>/kdp/v2.5.2.30792/kdp.swf",
	    'uiconfId' : '48500' ,
		'subp_id' : '<?php echo $subp_id ?>' ,
		'ks' : '<?php echo $ks ?>' ,
		'widget_id' : '_<?php echo $partner_id ?>' ,
		'devFlag' : 'false' ,
		'serverPath' : "<?php echo $service_url; ?>"
		};
		
	var params = {
		allowscriptaccess: "always",
		allownetworking: "all",
		bgcolor: "#1B1E1F",
		bgcolor: "#1B1E1F",				
		quality: "high",
//		wmode: "opaque" ,
		movie: "<?php echo $flash_dir ?>/kmc/analytics//ReportsAndAnalytics.swf"
	};
	swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/analytics/<?php echo $kmc_rna_version ?>/ReportsAndAnalytics.swf", 
		"kcms", "100%", "100%", "9.0.0", false, flashVars , params);	
		
function content_resize(){
   var w = $( window );
   var H = w.height(); 
   var W = w.width(); 
   $( '#flash_wrap' ).height(H-5);
  // $('#server_wrap iframe').height(H-38);
}		
</script>

<div id='flash_wrap'>
<div id='kcms'></div>
</div>

<script>
content_resize();

</script>

