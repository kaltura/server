<?php

//$first_login = ( !$allow_reports ) ? true : false;

if ( kConf::get('kmc_display_server_tab') )
{
	$support_url = '#support';
	$_SESSION['api_v3_login'] = true;
}
else
{
	$support_url = '/index.php/kmc/support?type=' . md5($payingPartner) . '&pid=' . $partner_id . '&email=' . $email;
}

// Multi Account User
$currentAccount = '';
if( count($allowedPartners) > 1 ) {
	$currentAccount = ' &nbsp;<span class="sep">|</span>&nbsp; Account: '.  $partner->getName() .' &nbsp;( <a id="ChangePartner" href="#change_partner">Change Account</a> ) &nbsp;';
}
?>

<script type="text/javascript">
	var kmc = {
		vars : {
		  /* --- new vars KMC4 */
			kmc_version				: "<?php echo $kmc_swf_version; ?>",
			kmc_general_uiconf		: "<?php echo $kmc_general->getId(); ?>",
			kmc_permissions_uiconf	: "<?php echo $kmc_permissions->getId(); ?>", 
			allowed_partners		: <?php echo json_encode($allowedPartners); ?>,
			hide_akamai_hd_network	: <?php echo ($hideAkamaiHDNetwork) ? "true" : "false"; ?>,
			kmc_secured				: <?php echo (kConf::get("kmc_secured_login"))? "true" : "false"; ?>,
		  /* END new vars KMC4 */
		
			service_url		: "<?php echo $service_url; ?>",
			host			: "<?php echo $host; ?>",
			cdn_host		: "<?php echo $cdn_host; ?>",
			rtmp_host		: "<?php echo $rtmp_host; ?>",
			embed_host		: "<?php echo $embed_host; ?>",
			embed_host_https: "<?php echo $embed_host_https; ?>",
			flash_dir		: "<?php echo $flash_dir ?>",
			getuiconfs_url	: "<?php echo url_for('kmc/getuiconfs'); ?>",
			terms_of_use	: "<?php echo kConf::get('terms_of_use_uri'); ?>",
			ks				: "<?php echo $ks; ?>",
			partner_id		: "<?php echo $partner_id; ?>",
			subp_id			: "<?php echo $subp_id; ?>",
			user_id			: "<?php echo $uid; ?>",
			first_login		: <?php echo ($first_login) ? "true" : "false"; ?>,
			whitelabel		: <?php echo $templatePartnerId; ?>,
			ignore_seo_links: <?php echo $ignoreSeoLinks; ?>,
			ignore_entry_seo: <?php echo ($ignoreEntrySeoLinks) ? "true" : "false"; ?>,
			embed_code_protocol_https: <?php echo ($useEmbedCodeProtocolHttps) ? "true" : "false"; ?>,
			has_v2_flavors: <?php echo ($v2Flavors) ? "true" : "false"; ?>,
			<?php if($v2Flavors) { ?>embed_code_delivery_type: 'akamai',<?php } ?>
			kcw_webcam_uiconf : "<?php echo $content_uiconfs_upload_webcam->getId(); ?>",
			kcw_import_uiconf : "<?php echo $content_uiconfs_upload_import->getId(); ?>",
			default_kdp		: {
					height		: "<?php echo $content_uiconfs_flavorpreview->getHeight(); ?>",
					width		: "<?php echo $content_uiconfs_flavorpreview->getWidth(); ?>",
					uiconf_id	: "<?php echo $content_uiconfs_flavorpreview->getId(); ?>",
					swf_version	: "<?php echo $content_uiconfs_flavorpreview->getswfUrlVersion(); ?>"
			},
			clipapp : {
				version	: "<?php echo kConf::get("clipapp_version"); ?>",
				kdp		: "<?php echo $content_uiconds_clipapp_kdp->getId(); ?>",
				kclip	: "<?php echo $content_uiconds_clipapp_kclip->getId(); ?>"
			},
			disable_analytics : <?php echo (kConf::get("kmc_disable_analytics"))? "true" : "false"; ?>,
			google_analytics_account : "<?php echo kConf::get("ga_account"); ?>",
			language	 : "<?php echo (isset($language) ? $language : '') ?>",
			logoutUrl	 : "<?php echo (isset($logoutUrl) ? $logoutUrl : '') ?>"
		}
	};
</script>

	<div id="kmcHeader"<?php if($templatePartnerId) echo ' class="whiteLabel"'; ?>>
	 <div id="logo"></div>
	 <ul id="hTabs">
	    <li id="loading"><img src="/lib/images/kmc/loader.gif" alt="Loading" /> <span>Loading...</span></li>
	 </ul>
	 <div id="user"><span class="left-arrow"></span><?php echo $full_name; ?></div>
	 <div id="user_links">
	  <span id="closeMenu"></span> &nbsp;&nbsp;<span><?php echo $full_name; ?>&nbsp;&nbsp; <a id="Logout" href="#logout">( Logout )</a>&nbsp;&nbsp; <?php echo $currentAccount; ?> </span>
	    <?php if (!$templatePartnerId) { ?>
	    <span> <span class="sep">|</span> &nbsp; <a id="Quickstart Guide" href="<?php echo $service_url ?>/content/docs/pdf/KMC_User_Manual.pdf" target="_blank">User Manual</a> &nbsp; <span class="sep">|</span> &nbsp;
	      <a id="Support" href="<?php echo $support_url; ?>" target="_blank">Support</a></span>
	    <?php } ?>
	 </div>
	</div><!-- kmcHeader -->

	<div id="main">
		<div id="flash_wrap" class="flash_wrap">
			<div id="kcms"></div>
		</div><!-- flash_wrap -->
        <div id="server_wrap">
         <iframe frameborder="0" id="server_frame" height="100%" width="100%"></iframe>
        </div> <!-- server_wrap -->
	</div><!-- main -->
<script type="text/javascript" src="/lib/js/kmc5.js?v=<?php echo $kmc_swf_version; ?>"></script>
