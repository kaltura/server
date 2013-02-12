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
			delivery_types	: <?php echo ($deliveryTypes) ? json_encode($deliveryTypes) : "{}"; ?>,
			embed_code_types: <?php echo ($embedCodeTypes) ? json_encode($embedCodeTypes) : "{}"; ?>,
			default_delivery_type: "<?php echo $defaultDeliveryType; ?>",
			default_embed_code_type: "<?php echo $defaultEmbedCodeType; ?>",			
			kcw_webcam_uiconf : "<?php echo $content_uiconfs_upload_webcam->getId(); ?>",
			kcw_import_uiconf : "<?php echo $content_uiconfs_upload_import->getId(); ?>",
			default_kdp		: {
					id	: "<?php echo $content_uiconfs_flavorpreview->getId(); ?>",				
					height		: "<?php echo $content_uiconfs_flavorpreview->getHeight(); ?>",
					width		: "<?php echo $content_uiconfs_flavorpreview->getWidth(); ?>",
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
<!--[if lte IE 7]>
<script type="text/javascript" src="<?php echo requestUtils::getCdnHost( requestUtils::getRequestProtocol() ); ?>/lib/js/json2.min.js"></script>
<![endif]-->
<?php if( $previewEmbedV2 ) { ?>
<script type="text/javascript" src="/lib/js/kmc6.js?v=<?php echo $kmc_swf_version; ?>"></script>
<script src="/lib/js/angular-1.0.4.min.js"></script>
<script src="/lib/js/KalturaEmbedCodeGenerator-1.0.2.min.js"></script>
<script src="/lib/js/jquery.qrcode.min.js"></script>
<script src="/lib/js/preview.js"></script>
<div id="previewModal" class="modal preview_embed">
	<div class="title">
		<h2></h2>
		<span class="close icon"></span>		
		<a class="help icon" href="javascript:kmc.utils.openHelp('section_pne');"></a>
	</div>
	<div class="content">
		<div class="row-fluid" ng-controller="PreviewCtrl">
			<div class="span4 options">
				<label>Select Player: </label>
				<select id="player" ng-model="player" ng-options="p.id as p.name for p in players"></select>
				<small class="help-block">Kaltura player includes both layout and functionality (advertising, subtitles, etc)</small>
				<div class="hr"></div>
				<div class="padBottom advance">
					<a ng-hide="showAdvancedOptions" ng-click="showAdvancedOptions = true" href="#">Show Advanced Options<i class="pull-right icon-chevron-down"></i></a>
					<a ng-show="showAdvancedOptions" ng-click="showAdvancedOptions = false" href="#">Hide Advanced Options<i class="pull-right icon-chevron-up"></i></a>
				</div>
				<div class="hr"></div>
				<div class="padBottom" show-slide="showAdvancedOptions">
					<label>Delivery Types: </label>
					<select ng-model="deliveryType" ng-options="d.id as d.label for d in deliveryTypes"></select>
					<small class="help-block">Adaptive Streaming automatically adjusts to the viewer's bandwidth,while Progressive Download allows buffering of the content. <a href="javascript:kmc.utils.openHelp('section_pne_stream');">Read more</a></small>
					<label>Embed Types: </label>
					<select ng-model="embedType" ng-options="e.id as e.label for e in embedTypes"></select>
					<small class="help-block">Auto embed is the default embed code type and is best to get a player quickly on a page without any runtime customizations. <a href="javascript:kmc.utils.openHelp('section_pne_embed');">Read more</a> about the different embed code types.</small>
					<label><input type="checkbox" ng-model="includeSeo"> Include Search Engine Optimazation data</label>
					<label><input type="checkbox" ng-model="secureEmbed"> Support for HTTPS embed code</label>
					<div class="hr"></div>
				</div>
				<div>
					<h3>Preview:</h3>
					<small class="help-block">Scan the QR code the preview in your mobile device</small>
					<div id="qrcode"></div>
					<div class="hr"></div>
					<small class="help-block">View a standalone page with this player</small>
					<div class="urlBox"><a href="{{previewUrl}}" target="_blank">{{previewUrl}}</a></div>
				</div>
				<div class="hr"></div>
				<h3>Embed Code:</h3>
				<textarea class="embedcode">{{embedCode}}</textarea>
				<button class="btn">Copy</button>
			</div>
			<div class="span8">
				<div id="previewIframe"></div>
			</div>
		</div>
	</div>
</div>
<?php } else { ?> 
<script type="text/javascript" src="/lib/js/kmc5.js?v=<?php echo $kmc_swf_version; ?>"></script>
<?php } ?>
