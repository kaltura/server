<script type="text/javascript">
var kmc = {
	vars : <?php echo json_encode($kmcVars);?>
};
</script>
	<div id="kmcHeader"<?php if($templatePartnerId) echo ' class="whiteLabel"'; ?>>
	 <?php 
	 if(isset($kmcVars['logoUrl'])) {
	 	echo '<div id="logo" style="background: transparent; overflow:hidden;"><img src="' . $kmcVars['logoUrl'] . '" /></div>';
	 } else {
	 	echo '<div id="logo"></div>';
	 }
	 ?>
	 <ul id="hTabs">
	    <li id="loading"><img src="/lib/images/kmc/loader.gif" alt="Loading" /> <span>Loading...</span></li>
	 </ul>
	 <div id="user"><span class="left-arrow"></span><?php echo $full_name; ?></div>
	 <div id="user_links">
	  <span id="closeMenu"></span> &nbsp;&nbsp;
	  <span>
	  	<?php echo $full_name; ?>&nbsp;&nbsp; 
	  	<a id="Logout" href="#logout">( Logout )</a>&nbsp;&nbsp; 
	  	<?php if($showChangeAccount) { ?>
	  	&nbsp;<span class="sep">|</span>&nbsp; 
	  	Account: <?php echo $partner->getName(); ?> &nbsp;
	  	( <a id="ChangePartner" href="#change_partner">Change Account</a> ) &nbsp;
	  	<?php } ?>
	  </span>
	    <?php if (!$templatePartnerId) { ?>
	    <span> <span class="sep">|</span> &nbsp; <a id="Quickstart Guide" href="<?php echo $service_url ?>/content/docs/pdf/KMC_User_Manual.pdf" target="_blank">User Manual</a> &nbsp; <span class="sep">|</span> &nbsp;
	      <?php 
	      if( isset($kmcVars['supportUrl']) ){
	      	$supportUrl = $kmcVars['supportUrl'];
	      	$supportId = "";
	      }
	      else {
	      	$supportUrl = "/index.php/kmc/support?type=" . md5($payingPartner) . "&pid=" . $partner_id;
	      	$supportId = 'id="Support"';
	      }
	      ?>
	      <a <?php echo $supportId; ?> href="<?php echo $supportUrl; ?>" target="_blank">Support</a></span>
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
<script type="text/javascript" src="<?php echo requestUtils::getCdnHost( requestUtils::getRequestProtocol() ); ?>/lib/js/localStorage.min.js"></script>
<![endif]-->
<?php if( $previewEmbedV2 ) { ?>
<!-- Preview & Embed Modal -->
<div id="previewModal" class="modal preview_embed" ng-controller="PreviewCtrl">
	<div class="title clearfix">
		<h2></h2>
		<span class="close icon"></span>		
		<a class="help icon" href="javascript:kmc.utils.openHelp('section_pne');"></a>
	</div>
	<div class="content row-fluid">
		<div class="span4 options form-horizontal">
			<div ng-show="liveBitrates">
				<div class="control-group">
					<label class="control-label">Live Bitrates: </label>
				</div>
				<ul ng-repeat="bitrate in liveBitrates">
					<li>{{bitrate.bitrate}} kbps, {{bitrate.width}}x{{bitrate.height}}</li>
				</ul>
				<div class="hr"></div>
			</div>
			<div class="control-group" ng-hide="playerOnly">
				<label class="control-label">Select Player: </label>
				<div class="controls">
					<select id="player" ng-model="player" ng-options="p.id as p.name for p in players"></select>
				</div>
				<small class="help-block">Kaltura player includes both layout and functionality (advertising, subtitles, etc)</small>
				<div class="hr"></div>
			</div>
			<div class="control-group advance">
				<div class="arrow-right pull-left" ng-hide="showAdvancedOptionsStatus"></div>
				<a ng-hide="showAdvancedOptionsStatus" ng-click="showAdvancedOptions($event, true)" href="#">Show Advanced Options</a>
				<div class="arrow-down pull-left" ng-show="showAdvancedOptionsStatus"></div>
				<a ng-show="showAdvancedOptionsStatus" ng-click="showAdvancedOptions($event, false)" href="#">Hide Advanced Options</a>
			</div>
			<div class="hr"></div>
			<div show-slide="showAdvancedOptionsStatus">
				<div class="control-group" ng-hide="liveBitrates">
					<label class="control-label">Delivery Type: </label>
					<div class="controls"><select ng-model="deliveryType" ng-options="d.id as d.label for d in deliveryTypes"></select></div>
					<small class="help-block">Adaptive Streaming automatically adjusts to the viewer's bandwidth, while Progressive Download allows buffering of the content. <a href="javascript:kmc.utils.openHelp('section_pne_stream');">Read more</a></small>
					<div class="hr"></div>					
				</div>
				<div class="control-group">
					<label class="control-label">Embed Type: </label>
					<div class="controls"><select ng-model="embedType" ng-options="e.id as e.label for e in embedTypes"></select></div>
					<small class="help-block">Auto embed is the default embed code type and is best to get a player quickly on a page without any runtime customizations. <a href="javascript:kmc.utils.openHelp('section_pne_embed');">Read more</a> about the different embed code types.</small>
				</div>
				<div class="hr"></div>
				<div class="control-group">
					<label class="checkbox"><input type="checkbox" ng-model="includeSeo"> Include Search Engine Optimization data</label>
					<label class="checkbox"><input type="checkbox" ng-model="secureEmbed"> Support for HTTPS embed code</label>
				</div>
				<div class="hr"></div>
			</div>
			<div>
				<div class="control-group clearfix">
					<label class="control-label">Preview: </label>
				</div>
				<div class="qr-block">
					<small class="help-block">Scan the QR code to preview in your mobile device</small>
					<div ng-hide="shortLinkGenerated" class="qr-placeholder"><div class="qr-text">Generating...</div></div>
					<div ng-show="shortLinkGenerated" id="qrcode"></div>
					<div class="hr"></div>
				</div>
				<small class="help-block">View a standalone page with this player</small>
				<div class="urlBox"><a href="{{previewUrl}}" target="_blank">{{previewUrl}}</a></div>
			</div>
			<div ng-hide="previewOnly">
				<div class="hr"></div>
				<div class="control-group">
					<label class="control-label">Embed Code: </label>
				</div>
				<div class="input-append">
				  <textarea class="span2" id="embedCode" readonly>{{embedCode}}</textarea>
				  <button class="btn copy-code" data-clipboard-target="embedCode">Copy</button>
				</div>
			</div>
		</div>
		<div class="span8" id="previewIframe"></div>
	</div>
	<div class="footer">
		<button class="btn btn-info copy-code" data-close="true" data-clipboard-target="embedCode">{{closeButtonText}}</button>
	</div>
</div>
<script src="/lib/js/angular-1.0.4.min.js"></script>
<script src="/lib/js/kmc/6.0.10/kmc.min.js?v=<?php echo $kmc_swf_version; ?>"></script>
<?php } else { ?>
<script type="text/javascript" src="/lib/js/kmc5.js?v=<?php echo $kmc_swf_version; ?>"></script>
<?php } ?>
