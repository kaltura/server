<?php
// TEST PAGE FOR ADDING JW TO KMC

$jw_license = ($jw_license)? "licensed": "non-commercial";
$service_url = myPartnerUtils::getHost($partner_id);
$host = str_replace ( "http://" , "" , $service_url );
$cdn_url = myPartnerUtils::getCdnHost($partner_id);
$cdn_host = str_replace ( "http://" , "" , $cdn_url );

$cache_st = "cache_st/".(time()+ 15 * 60);

$flash_dir = $service_url . myContentStorage::getFSFlashRootPath ();
//$allow_reports = false;
$disableurlhashing = kConf::get('disable_url_hashing');
if ( !$allow_reports )
{
  $first_login = true;
}
if ( kConf::get('kmc_display_server_tab') )
{
  $support_url = '#';
  $_SESSION['api_v3_login'] = true;
}
else
{
	$support_url = '/index.php/kmc/support?type=' . md5($payingPartner) . '&pid=' . $partner_id . '&email=' . $email;
//  if($visibleCT === 'true') // paying partner, not CE
//  {
//    $support_url = 'http://corp.kaltura.com/support/form/project/30/partnerId/'.(($host != 1)? '': $partner_id);
//    $support_url = 'http://tickets.kaltura.com/';
//  }
//  else // free partner - open modal
//  {
//    $support_url = '#';
//  }
}
if ( $host == "www.kaltura.com" ) $host = "1";

?>

<script type="text/javascript">

 function adSolution() {	// checkbox onclick
	if ($("#AdSolution").attr("checked")) {
		$("#ads_notes").show();
		$("#adSolution_channel").focus();
	}
	else {
		$("div.description ul").hide();
		$("#adSolution_channel").val("");
	}
	window.chkbox_flag=false;
 }

 function adsChannel (this_input,entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf) {	// input
	if(this_input.value=="" || this_input.value=="_") {
		if (!chkbox_flag)
			$("#AdSolution").attr("checked",false);
		$("div.description ul").hide();
//		return;
	}

	var embed_code = doJwPreviewEmbed(entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf, showing_jw_playlist, showing_playlist_type);
	$("#player_wrap").html(embed_code);
	$("#jw_embed_code").val(embed_code);
// ux of above needs to be improved by only reloading if actual change took place
 }

function adsolutionSetup(start) {
	var $adSolution_channel=$("#adSolution_channel");
	if(start)
		if($adSolution_channel.val()=="")
			$adSolution_channel.val("_");
	else
		if($adSolution_channel.val()=="_")
			$adSolution_channel.val("");
}

function copyCode() {
	$("#jw_embed_code").select();
	$("#jw_copy").show();
	setTimeout(function(){$("#jw_copy").hide(500);},1500)
}

</script>
<style type="text/css">
	a, a:visited { }
    a.close, a.help { height:19px; width:19px; background: url(sprite.png) no-repeat -19px 0; float:right; margin: -2px 0 0 5px;}
	a.help { background-position: 0 0;}
	#player_wrap { min-height:290px; min-width:400px; padding-bottom:8px;}
	#jw_preview { width:400px; padding:10px 12px; border: solid 1px #bbb; margin: 0 auto; -moz-border-radius:8px; -webkit-border-radius:8px; background:#98BBC3 url(sprite.png) repeat-x 0 -20px; font: normal 12px arial,sans-serif;}
	#player_wrap { margin: 0 auto;}
	a.license { margin: 0 5px;}
	div.label { width:99px; margin: 10px 0 5px; float:left; clear:both; font-weight:bold;}
	div.description { width:300px; margin: 10px 0 0;}
	div.description a { color:#433179;}
	div.description a:hover { text-decoration:underline;}
	div.description ul { display:none; margin: 3px 0 0 33px;}
	div.description li { margin-bottom:3px;}
	div.description ul input { width:90px; height:12px; border: solid 1px #9B9B9B; background:#f7f7f7;}
	div.description ul button { padding: 0 5px; vertical-align:1px;}
	#player_embed_code { margin-top:0; width:398px; background:#F7F7F7; border: solid 1px #9B9B9B;}
	#mbContent { display:table; font: normal 12px arial,sans-serif; color:#000;}
	#AdSolution { width:auto; vertical-align:-2px; margin-right:8px;}
	#select_player { margin: 5px 0 5px -10px !important;}
	#nomix_box, #jw_copy { margin: 0 0 15px; padding:8px; border: solid 1px #ccc; background:#FFFFBC; position:relative;}
	 #nomix_box p, #jw_copy p { margin: 0 0 5px;}
	 #nomix_box dfn { font-style:normal; border-bottom: dashed 1px blue; cursor:help;}
	 #nomix_box div	{ margin: 15px 0 0; text-align:right; color:#444;}
	 #nomix_box input { width:auto; vertical-align:-2px;}
	#jw_copy { margin: 5px 0 0; display:none;}
	#copy_code { margin-top:10px;}
	#mbContent { margin-bottom:20px;}
	#jw_embed_code { background:#F8F8F8;}
</style>
	<div id="kmcHeader">
	<?php if (!$templatePartnerId) { ?>
     <img src="<?php echo $service_url; ?>/lib/images/kmc/logo_kmc.jpg" alt="Kaltura Management Console" />
	<?php } ?>
     <ul>
      <li><a id="dashboard" href="javascript:void(0)"><span>Dashboard</span></a></li>
      <li><a id="content" href="javascript:void(0)"><span>Content</span></a></li>
     <?php if ( kConf::get ( "kmc_display_customize_tab" ) && !$templatePartnerId) { ?>
	  <li><a id="appstudio" href="javascript:void(0)"><span>Application&nbsp;Studio</span></a></li>
	 <?php } ?>
	  <!-- <li><a href="#Ad Networks">Ad Networks</a></li> -->
	  <!-- <li><a href="#Reports">Reports</a></li> -->
	 <?php if ( kConf::get ( "kmc_display_account_tab" ) ) { ?>
      <li><a id="account" href="javascript:void(0)"><span>Account</span></a></li>
	 <?php } ?>
	 <?php if ( kConf::get ( "kmc_display_server_tab" ) ) { ?>
      <li><a id="server" href="<?php echo $service_url; ?>/api_v3/system/batchwatch.php" target="_server"><span>Server</span></a></li>
	 <?php } ?>
	 <?php if ( kConf::get ( "kmc_display_developer_tab" ) ) { ?>
      <li><a id="developer" href="<?php echo $service_url; ?>/api_v3/testme/index.php"><span>Developer</span></a></li>
	 <?php } ?>
	 <?php if ($allow_reports) { ?>
	 <li><a id="reports" href="javascript:void(0)"><span>Analytics</span></a></li>
	 <?php } ?>
	 </ul>

     <div>
      <span>Hi <?php echo $screen_name ?></span><br />
      <?php if ($templatePartnerId) { ?>
      <a href="javascript:logout()">Logout</a>
      <?php } else { ?>
      <a href="<?php echo $service_url ?>/content/docs/pdf/KMC_Quick_Start_Guide.pdf" target="_blank">Quickstart Guide</a> &nbsp; | &nbsp;
	  <a href="javascript:logout()">Logout</a> &nbsp; | &nbsp;
	  <a target="_blank" onclick="return openSupport(this);" href="<?php echo $support_url; ?>">Support</a>
      <?php } ?>
	 </div>

	</div><!-- kmcHeader -->

	<div id="main">
		<div id="flash_wrap" class="flash_wrap" style="">
			<div id="kcms"></div>
		</div><!-- end #flashcontent -->
        <div id="server_wrap">
         <iframe frameborder="0" id="server_frame" height="100%" width="100%"></iframe>
        </div> <!--server_wrap-->
	</div><!-- end #main -->

<script type="text/javascript">
var current_module = 'content';
var partner_id = <?php echo $partner_id; ?>;
var subpid = <?php echo $subp_id; ?>;
var user_id = '<?php echo $uid; ?>';
var ks = '<?php echo $ks; ?>';
var screen_name = "<?php echo $screen_name; ?>";
var email = '<?php echo $email; ?>';
var next_module = null;
var selected_uiconfId = null;
var sub_nav_tab = "";

var refreshPlayerList = 0;
var refreshPlaylistList = 0;

var ui_confs_player;
<?php
		$uiconfs_array = array();
		foreach($player_uiconf_list as $uiconf)
		{
		    $uiconf_array = array();
            $uiconf_array["id"] = $uiconf->getId();
            $uiconf_array["name"] = $uiconf->getName();
            $uiconf_array["width"] = $uiconf->getWidth();
            $uiconf_array["height"] = $uiconf->getHeight();

            $uiconfs_array[] = $uiconf_array;
		}
		$uiconfs_array = array_merge($uiconfs_array,$jw_uiconfs_array);
	echo "ui_confs_player=" . json_encode($uiconfs_array);
?>

var ui_confs_playlist;
<?php
		$uiconfs_array = array();
		foreach($playlist_uiconf_list as $uiconf)
		{
		    $uiconf_array = array();
            $uiconf_array["id"] = $uiconf->getId();
            $uiconf_array["name"] = $uiconf->getName();
            $uiconf_array["width"] = $uiconf->getWidth();
            $uiconf_array["height"] = $uiconf->getHeight();

            $uiconfs_array[] = $uiconf_array;
		}
		$uiconfs_array = array_merge($uiconfs_array,$jw_uiconf_playlist);
	echo "ui_confs_playlist=" . json_encode($uiconfs_array);
?>

function playerAdded()
{
	refreshPlayerList = 1;
	jQuery.ajax({
		url: "<? echo url_for('kmc/getuiconfs'); ?>",
		type: "POST",
		data: { "type": "player", "partner_id": partner_id, "ks": ks },
		dataType: "json",
		success: function(data) {
			if (data && data.length) {
				ui_confs_player = data;
			}
		}
	});
}

function playlistAdded()
{
	refreshPlaylistList = 1;
	jQuery.ajax({
		url: "<? echo url_for('kmc/getuiconfs'); ?>",
		type: "POST",
		data: { "type": "playlist", "partner_id": partner_id, "ks": ks },
		dataType: "json",
		success: function(data) {
			if (data && data.length) {
				ui_confs_playlist = data;
			}
		}
	});
}

function expiredF ( )
{
	window.location = "<? echo $service_url; ?>/index.php/kmc/kmc";
}

function refreshSWF()
{
	sub_nav_tab = "";
	loadModule( current_module, partner_id, subpid,  user_id,  ks, screen_name, email );
}

function selectPlaylistContent(playerId,isPlaylist)
{
	if(isPlaylist == "true")
		sub_nav_tab = "Playlist";
	else
		sub_nav_tab = "";

	selected_uiconfId = playerId;
	next_module = "content";
	$("#kmcHeader ul li a").removeClass('active');
	$("a#content").addClass('active');
	loadModule(next_module, partner_id, subpid, user_id, ks, screen_name, email);
}

// load module upon dashboard clicks
function loadModuleFromDashboard(next_module_name, sub_module)
{
	$("#kmcHeader ul li a").removeClass('active');
	$("a#" + next_module_name).addClass('active');

	if ( next_module_name == "content" )
	{
		if(sub_module == "uploadKMC")
		{
			sub_nav_tab = "Upload";
			loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
			openCw(ks, '');
		}
		if(sub_module == "upload")
		{
			sub_nav_tab = "Upload";
			loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
		}
		if(sub_module == "playlist")
		{
			sub_nav_tab = "Playlist";
			loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
		}
		if(sub_module == "entries")
		{
			sub_nav_tab = "Entries";
			loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
		}
	}
	if ( next_module_name == "account" )
	{
		if(sub_module == "accountUpgrade")
		{
			sub_nav_tab = "Account Upgrade";
			loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
		}
	}
	if ( next_module_name == "appstudio" )
	{
		loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
	}
	if ( next_module_name == "reports" )
	{
		loadModule(next_module_name, partner_id, subpid, user_id, ks, screen_name, email);
	}



}

function loadModule ( module_name , partner_id , subp_id ,  uid  ,  ks , screen_name , email )
{
	if ( module_name == "dashboard" )
	{
		setDivToHide ( "kcms" );
		current_module = 'dashboard';
		var flashVars = {
				'host' : "<? echo $host ?>" ,
				'cdnhost' : "<? echo $cdn_host ?>" ,
				'uid' : uid ,
				'partnerid' : partner_id,
				'subpid' : subp_id ,
				'openCw' : "openCw" ,
				'srvurl' : 'api_v3/index.php' ,
				'userName': '<?php echo $screen_name ?>' ,
				'ks' : ks ,
				'devFlag' : 'false' ,
				'entryId' : "-1" ,
				'kshowId' : '-1',
				'refreshPlayerList' : refreshPlayerList,
				'refreshPlaylistList' : refreshPlaylistList,
				'widget_id' : '_' + partner_id ,
				'uiconfid' : '48308',
				'openPlaylist' : 'openPlaylist',
				'openPlayer' : 'openPlayer' ,
				'expiredF' : 'expiredF',
				'subNavTab' : sub_nav_tab,
				'innerKdpVersion':'v2.7.0',
				'firstLogin' : '<? echo ($first_login) ? "true" : "false"; ?>',
				'visibleCT' : '<? echo $visibleCT; ?>',
				'disableurlhashing' : '<? echo $disableurlhashing; ?>'
				};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#1B1E1F",
				bgcolor: "#1B1E1F",
				quality: "high",
//				wmode: "opaque" ,
				movie: "<?php echo $flash_dir ?>/kmc/dashboard/<? echo $kmc_dashboard_version ?>/dashboard.swf"
			};
			swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/dashboard/<? echo $kmc_dashboard_version ?>/dashboard.swf",
				"kcms", "100%", "100%", "9.0.0", false, flashVars , params);

		// setTimeout ( "content_resize()" , 1000 );
	}
	if ( module_name == "content" )
	{
		setDivToHide ( "kcms" );
		current_module = 'content';
		var flashVars = {
				'host' : "<? echo $host ?>" ,
				'cdnhost' : "<? echo $cdn_host ?>" ,
				'uid' : uid ,
				'partnerid' : partner_id,
				'subpid' : subp_id ,
				'openCw' : "openCw" ,
				'ks' : ks ,
				'devFlag' : 'false' ,
				'entryId' : "-1" ,
				'kshowId' : '-1',
				'refreshPlayerList' : refreshPlayerList,
				'refreshPlaylistList' : refreshPlaylistList,
				'widget_id' : '_' + partner_id ,
				'uiconfid' : '48308',
				'openPlaylist' : 'openPlaylist',
				'openPlayer' : 'openPlayer' ,
				'expiredF' : 'expiredF',
				'subNavTab' : sub_nav_tab,
				'innerKdpVersion':'<? echo $content_kdp_version; ?>',
				'visibleCT' : '<? echo $visibleCT; ?>',
				'disableurlhashing' : '<? echo $disableurlhashing; ?>',
				email : '<? echo $email; ?>'
				};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#1B1E1F",
				bgcolor: "#1B1E1F",
				quality: "high",
//				wmode: "opaque" ,
				movie: "<?php echo $flash_dir ?>/kmc/content/<? echo $kmc_content_version ?>/content.swf?r113"
			};
			swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/content/<? echo $kmc_content_version ?>/content.swf?r113",
				"kcms", "100%", "100%", "9.0.0", false, flashVars , params);

		// setTimeout ( "content_resize()" , 1000 );
	}
	if ( module_name == "account" ) //***
	{
	    setDivToHide ( "kcms" );
		current_module = 'account';
		var flashVars = {
				'host' : "<? echo $host ?>" ,
				'cdnhost' : "<? echo $cdn_host ?>" ,
				'uid' : uid ,
				'partnerid' : partner_id,
				'subpid' : subp_id ,
				'email': email ,
				'openCw' : "openCw" ,
				'ks' : ks ,
				'devFlag' : 'true' ,
				'entryId' : "-1" ,
				'kshowId' : '-1',
				'widget_id' : '_' + partner_id ,
				'subNavTab' : sub_nav_tab,
				'openPlaylist' : 'openPlaylist',
				'openPlayer' : 'openPlayer',
				'showUsage' : '<?php echo (kConf::get('kmc_account_show_usage'))? 'true': 'false'; ?>'
				};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#1B1E1F",
				bgcolor: "#1B1E1F",
				quality: "high",
//				wmode: "opaque" ,
				movie: "<?php echo $flash_dir ?>/kmc/account/<? echo $kmc_account_version ?>/account.swf"
			};
			swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/account/<? echo $kmc_account_version ?>/account.swf",
				"kcms", "100%", "100%", "9.0.0", false, flashVars , params);

		setTimeout ( "content_resize()" , 1000 );
	}
	if ( module_name == "appstudio" )
	{
		sub_nav_tab = "";
		setDivToHide ( "kcms" );
		current_module = 'appstudio';
		var flashVars = {
				'host' : "<? echo $host ?>" ,
				'cdnhost' : "<? echo $cdn_host ?>" ,
				'uid' : uid ,
				'partner_id' : partner_id,
				'subp_id' : subp_id ,
				'email': email ,
				'openCw' : "openCw" ,
				'ks' : ks ,
				'inapplicationstudio' : 'true' ,
				'devFlag' : 'true' ,
				'entryId' : "_KMCLOGO" ,
				'kshowId' : '-1',
				'widget_id' : '_' + partner_id ,
				'openPlaylist' : 'openPlaylist',
				'openPlayer' : 'openPlayer',
				'devFlag' : 'false' ,
				'uiconfid' : '48308',
				'servicesPath' : 'index.php/partnerservices2/',
				'serverPath' : "<? echo $service_url; ?>",
				'kdpUrl' : "<?php echo $flash_dir ?>/kdp/v2.7.0/kdp.swf",
				'disableurlhashing' : '<? echo $disableurlhashing; ?>'
				};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#1B1E1F",
				bgcolor: "#1B1E1F",
				quality: "high",
//				wmode: "opaque" ,
				movie: "<?php echo $flash_dir ?>/kmc/appstudio/<? echo $kmc_appstudio_version ?>/applicationstudio.swf"
			};
			swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/appstudio/<? echo $kmc_appstudio_version ?>/applicationstudio.swf",
				"kcms", "100%", "100%", "9.0.0", false, flashVars , params);

		setTimeout ( "content_resize()" , 1000 );
	}
	if ( module_name == "reports" )
	{
		sub_nav_tab = "";
		setDivToHide ( "kcms" );
		current_module = 'reports';
		var flashVars = {
				'host' : "<? echo $host ?>" ,
				'cdnhost' : "<? echo $cdn_host ?>" ,
				'uid' : uid ,
				'partner_id' : partner_id,
				'srvurl' : 'api_v3/index.php',
				'innerKdpVersion' : 'v2.7.0',
				'kdpUrl' : "<?php echo $flash_dir ?>/kdp/v2.7.0/kdp.swf",
				'uiconfId' : '48500' ,
				'subp_id' : subp_id ,
				'ks' : ks ,
				'widget_id' : '_' + partner_id ,
				'devFlag' : 'false' ,
				'serverPath' : "<? echo $service_url; ?>"
				};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#1B1E1F",
				bgcolor: "#1B1E1F",
				quality: "high",
//				wmode: "opaque" ,
				movie: "<?php echo $flash_dir ?>/kmc/analytics//ReportsAndAnalytics.swf"
			};
			swfobject.embedSWF("<?php echo $flash_dir ?>/kmc/analytics/<? echo $kmc_rna_version ?>/ReportsAndAnalytics.swf",
				"kcms", "100%", "100%", "9.0.0", false, flashVars , params);

		setTimeout ( "content_resize()" , 1000 );
	}
}

var modal = null;
function openSupport(a_obj) {
	kalturaCloseModalBox();
	var modal_width = $.browser.msie ? 543 : 519;
	var iframe_height = $.browser.msie ? 751 : ($.browser.safari ? 697 : 732);
	var href = a_obj.href;
	$("#flash_wrap").css("visibility","hidden");
	modal = kalturaInitModalBox ( null , { width : modal_width , height: 450 } );
	modal.innerHTML =	'<div><a id="close" href="#close" style="display:block; height:19px; width:19px; ' +
						'background: url(/lib/images/kmc/action_btns.gif) -19px 0 no-repeat; float:right; margin: -6px -6px 0 0;"></a></div>' +
						'<iframe id="support" src="' + href + '&style=v" scrolling="no" frameborder="0"' +
						'marginheight="0" marginwidth="0" height="' + iframe_height + '" width="519"></iframe>';
	$("#close").click(function() {
		kmcCloseModal();
		return false;
	});
	return false;

//	if (a_obj.href != "<?php echo $service_url; ?>/#") return true;
//
//	modal = kalturaInitModalBox ( null , { width: 700, height: 360 } );
//	modal.innerHTML = '<a onclick="onCloseCw();" style="font-size:11px; font-weight:bold;">X Close</a>'+
//	'<iframe width="670" height="350" src="<?php echo $service_url ?>/index.php/kmc/support"></iframe>';
//	return false;
}

function kmcCloseModal() {
	kalturaCloseModalBox();
	$("#flash_wrap").css("visibility","visible");
	return false;
}

function openCw ( ks ,conversion_quality )
{
	// use wrap = 0 to indicate se should be open withou the html & form wrapper
	modal = kalturaInitModalBox ( null , { width: 700, height: 360 } );
	modal.innerHTML = "<div id='kaltura_cw'></div>" ;


	var flashVars = {
		'host' : "<? echo $host ?>" ,
		'cdnhost' : "<? echo $cdn_host ?>" ,
		'userId' : "<?php echo $uid ?>",
		'partnerid' : "<?php echo $partner_id ?>",
		'subPartnerId' : "<?php echo $subp_id ?>",
		'sessionId' : ks ,
		'devFlag' : 'true' ,
		'entryId' : "-1" ,
		'kshow_id' : '-1',
		'terms_of_use' : "<?php echo kConf::get('terms_of_use_uri'); ?>",
		'close' : 'onCloseCw' ,
		'quick_edit' : 0 , 		// when opening from the KMC - don't add to the roughcut
		'kvar_conversionQuality' : conversion_quality ,
		'partnerData' : "conversionQuality:" + conversion_quality + ";"	// this is a hack until the CW supports kvar
		};

		var params = {
			allowscriptaccess: "always",
			allownetworking: "all",
			//bgcolor: "#1B1E1F",
			bgcolor: "#DBE3E9",
			quality: "high",
			wmode: "opaque" ,
			movie: "<? echo $service_url; ?>/kcw/ui_conf_id/36203"
		};

		swfobject.embedSWF("<? echo $service_url; ?>/kcw/ui_conf_id/36203",  // 36201 - new CW with ability to pass params not ready for this version
			"kaltura_cw", "680", "400" , "9.0.0", false, flashVars , params);

		setObjectToRemove ( "kaltura_cw" );
}

var global_embed_code;
// TODO - this should open a dialog box and call a flash object
function openPlaylist ( embed_code , playlist_id , pl_width_str , pl_height_str , ui_conf_id, jw_skin, jw_share, jw_flashvars , jw_playlistType)
{
	if(ui_conf_id<1000 && ui_conf_id>799) {
//		jw_swf,partner_id,entry_id,skin,width,height,share,flashvars
		build_modal(playlist_id , jw_skin, pl_width_str , pl_height_str , jw_share, jw_flashvars, ui_conf_id, null, true, jw_playlistType);
		$("div.description").css("float","left");
		return;
	}

	global_embed_code = embed_code;
	if ( ! ui_conf_id || ui_conf_id == 190 || ui_conf_id == 199 )
	{
<?
if (false && kConf::get('www_host') == 'www.kaltura.com'){
?>
		ui_conf_id = 48206;
		pl_width_str = 660;
		pl_height_str = 272;
<?
} else {
?>
		if(sub_nav_tab == "")
		  selected_uiconfId = null;
		// check if the page is now in state where there is a selected_uiconfId
		if ( ! ui_conf_id && selected_uiconfId != null && ui_confs_playlist )
		{
			for(var i = 0; i < ui_confs_playlist.length; i++)
			{
				var uiconf = ui_confs_playlist[i];
				if ( uiconf.id == selected_uiconfId )
				{
					ui_conf_id = uiconf.id;
					pl_width_str = uiconf.width;
					pl_height_str = uiconf.height;
					break;
				}
			}
		}
		else
		{
			// override the default values from the depricated ui_conf
			ui_conf_id = <? echo $playlist_uiconf_list[0]->getId();  ?>; //48206;
			pl_width_str = <? echo $playlist_uiconf_list[0]->getWidth();  ?>;// 660;
			pl_height_str = <? echo $playlist_uiconf_list[0]->getHeight();  ?>;//272;
		}
<?
}
?>
	}

//	alert ( embed_code );
	// override the ui_conf_id from the embed code
	// replace the embed_code to use the ui_conf_id + width + height
	embed_code = embed_code.replace ( /wid\//g , "<?php echo $cache_st ?>/wid/");
	embed_code = embed_code.replace ( /ui_conf_id\/[0-9a-zA-Z]*/g , "ui_conf_id/" + ui_conf_id );
	embed_code = embed_code.replace ( /width="[0-9a-zA-Z]*"/g , 'width="' + pl_width_str + '"' );
	embed_code = embed_code.replace ( /height="[0-9a-zA-Z]*"/g , 'height="' + pl_height_str + '"' );

	window.ui_conf_select = createSelectUiConfForPlaylist ( playlist_id , ui_conf_id );

//	alert ( ui_conf_select );

	pl_width = parseInt ( pl_width_str );
	pl_height = parseInt ( pl_height_str );
	modal = kalturaInitModalBox ( null , { width:pl_width+20 , height: (pl_height + 200)  } );
	playlist_html =
		"<div class='title'><a href='#close' onclick='kalturaCloseModalBox(); return false;' class='closeBtn'></a><a href='<? echo $service_url; ?>/index.php/kmc/help#contentSection147' target='_blank' class='help'></a>Playlist ID: " + playlist_id + ui_conf_select + "</div>" + // third tr will have the playlist_id
		"<div class='kplayer' style='height:" + pl_height + "px'>" + embed_code + "</div>" + // create div to hold the playlist
		"<div class='embed_code' style='text-align:center'><textarea  id='embed_code' cols='30' rows='5' readonly='true' onclick='copyToClipboard(\"embed_code\");'>" + embed_code + "</textarea></div>" + // raw embed code
		"<div class='buttons'><button onclick='copyToClipboard(\"embed_code\");'>Select code</div>";

	setObjectToRemove ( "kaltura_playlist" );
	modal.innerHTML = playlist_html;
}

function createSelectUiConfForPlaylist ( playlist_id , ui_conf_id )
{
	window.ui_conf_select = "<span style='display:block; padding-left:10px;'><select id='select_player' onchange='" +
		"reopenPlaylist( \"" +  playlist_id  + "\" , this );'>";

	if (ui_confs_playlist)
	{
		for(var i = 0; i < ui_confs_playlist.length; i++)
		{
			var uiconf = ui_confs_playlist[i];
			ui_conf_select += createSelectUiConfAddOption(ui_conf_id ,uiconf.id, uiconf.width, uiconf.height, uiconf.name, uiconf.skin, uiconf.share, uiconf.flashvars, uiconf.playlistType);
		}
	}
	else
	{
<?php
if (false && kConf::get('www_host') != 'www.kaltura.com'){
	foreach ( $playlist_uiconf_list as $ui_conf )
	{
		$name = $ui_conf->getName();
		$name =  substr( htmlspecialchars  ( $name , ENT_QUOTES ) ,0 , 30 );
		echo "ui_conf_select += createSelectUiConfAddOption ( ui_conf_id ," . $ui_conf->getId() . "," .   $ui_conf->getWidth() . "," .  $ui_conf->getHeight() . ",'$name'); \n";
	}
}else {
?>
			ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48206 ,660,272 , "Horizontal" );
			ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48207 ,724,322 , "Horizontal Compact" );
			ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48205 ,400,600 , "Vertical" );
			ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48204 ,400,600 , "Vertical Compact" );
<?
}
?>
	}

	ui_conf_select += "</select></span>";

	return 	ui_conf_select;
}

function createSelectUiConfAddOption ( current_ui_conf_id , ui_conf_id , width, height , name, skin, share, flashvars , playlistType)
{
	var selected = ( current_ui_conf_id == ui_conf_id ? " selected " : "" );
//	selected = "";
	if(skin == "undefined") skin="default";
	var option = '<option value="' + ui_conf_id + ',' + width + ',' + height + ',' + skin + ',' + share + ',' + flashvars + ','+playlistType+'" ' + selected + ' >' + name + '</option>';
	return option;
}

// assume the ui_conf_data will be of the structure:
// {id,name;width,height}
function reopenPlaylist ( playlist_id , select_elem )
{
	jelem = jQuery ( select_elem );

//	alert ( "reopenPlaylist [" + jelem[0].value + "]" );
	ui_conf_data = jelem[0].value.split(",");

	// replace the ui_conf in the embed_code
	var embed_code = global_embed_code.replace ( /ui_conf_id\/[0-9a-zA-Z]*/g , "ui_conf_id/" + ui_conf_data[0] );
	embed_code = embed_code.replace ( /width="[0-9a-zA-Z]*"/g , 'width="' + ui_conf_data[1] + '"' );
	embed_code = embed_code.replace ( /height="[0-9a-zA-Z]*"/g , 'height="' + ui_conf_data[2] + '"' );
	kalturaCloseModalBox();
	openPlaylist ( embed_code , playlist_id , ui_conf_data[1] , ui_conf_data[2], ui_conf_data[0], ui_conf_data[3] ,ui_conf_data[4] , ui_conf_data[5], ui_conf_data[6] );
}

function doJwPreviewEmbed(entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf, is_playlist, jw_playlistType) {
//	console.log(entry_id," | ",jw_skin," | ",jw_width," | ",jw_height," | ","jw_share: ",jw_share," | ",jw_flashvars," | ",ui_conf_id," | ",uiconf," | ",is_playlist," | ",jw_playlistType);

	var jw_swf = "<?php echo $jw_license; ?>.swf";
	var jw_flashvars = '';
	var jw_uid = new Date(); jw_uid=jw_uid.getTime();
	var jw_plugins =  new Array();

	if(!is_playlist || is_playlist == "undefined") {
		jw_flashvars += 'file=http://<?php echo $cdn_host ?>/p/' + partner_id + '/sp/' + partner_id + '00/flvclipper/entry_id/' + entry_id + '/version/100000/.flv';
		jw_plugins.push("kalturastats");
	}
	else {
		jw_flashvars += 'file=http://<?php echo $cdn_host ?>/index.php/partnerservices2/executeplaylist%3Fuid%3D%26format%3D8%26playlist_id%3D' + entry_id +
						'%26partner_id%3D' + partner_id + '%26subp_id%3D' + partner_id + '00%26ks%3D%7Bks%7D';
		jw_flashvars += '&playlist=' + jw_playlistType;
		if(jw_playlistType != "bottom") {
	      jw_flashvars += '&playlistsize=300';
		}
	}

	if(jw_share=="true" || jw_share==true) {
		jw_flashvars += '&viral.functions=embed,link&viral.onpause=false';
		jw_plugins.push("viral-2");
	}

/* AdSolution */
	var jw_ads = { channel : $("#adSolution_channel").val() };
	if ($("#AdSolution").is(":checked") && jw_ads.channel != "") {
		jw_ads.flashvars =	'&ltas.cc=' + jw_ads.channel + 	// &ltas.xmlprefix=http://zo.longtailvideo.com.s3.amazonaws.com/ //uacbirxmcnulxmf
							'&mediaid=' + entry_id;
		jw_plugins.push("ltas");
		jQuery.ajax({
			async:		false,
			url:		"<? echo url_for('kmc/getentryinfo'); ?>",
			type:		"POST",
			data:		{ "partner_id": partner_id, "ks": ks, "entryId": entry_id },
			dataType:	"json",
			success:	function(data) {
							if (data) { // && data.length
								jw_ads.flashvars += "&title=" + escape(data.name) + "&description=" + escape(data.desc);
							}
						}
		});
		jw_flashvars += jw_ads.flashvars;
	}
/* end AdSolution */

	jw_skin = (jw_skin == "undefined" ||jw_skin == "") ? '' : '&skin=http://<?php echo $cdn_host ?>/flash/jw/skins/' + jw_skin;

	jw_flashvars =  jw_flashvars +
					'&amp;image=http://<?php echo $cdn_host ?>/p/' + partner_id + '/sp/' + partner_id + '00/thumbnail/entry_id/' + entry_id + '/width/640/height/480' +
					jw_skin + '&widgetId=jw00000001&entryId=' + entry_id + '&partnerId=' + partner_id + '&uiconfId=' + ui_conf_id + '&plugins=' + jw_plugins;

	jw_embed_code = '<div id="jw_wrap_' + jw_uid + '"> <object width="' + jw_width + '" height="' + jw_height + '" id="jw_player_' + jw_uid +
					'" name="jw_player_' + jw_uid + '">' +
					' <param name="movie" value="http://<?php echo $cdn_host ?>/flash/jw/player/' + jw_swf + '" />' +
					' <param name="wmode" value="transparent" />' +
					' <param name="allowScriptAccess" value="always" />' +
					' <param name="flashvars" value="' + jw_flashvars + '" />' +
					' <embed id="jw_player__' + jw_uid + '" name="jw_player__' + jw_uid + '" src="http://<?php echo $cdn_host ?>/flash/jw/player/' + jw_swf +
					'" width="' + jw_width + '" height="' + jw_height + '" allowfullscreen="true" wmode="transparent" allowscriptaccess="always"' +
					'flashvars="' + jw_flashvars + '" /> <noembed><a href="http://www.kaltura.org/">Open Source Video</a></noembed> </object> </div>';

/*	if (jw_ads.script)
		jw_embed_code += jw_ads.script;
*/

	return jw_embed_code;

} /* end build jw embed code */

function build_modal(entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf, is_playlist, jw_playlistType) {
	if(is_playlist == "undefined")
	  is_playlist = false;

	modal = kalturaInitModalBox ( null , { width:parseInt(jw_width)+20 , height: ""  } ); //(parseInt(jw_height) + 200)
	var jw_swf = "<?php echo $jw_license; ?>.swf";

	if(jw_swf == "licensed.swf") {
		jw_license_html = "<strong>COMMERCIAL</strong>";
		jw_license_ads_html = "";
	}
	else {
		jw_license_html =   '<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank" class="license tooltip"' +
							'title="With this license your player will show a JW Player watermark.  You may NOT use the non-commercial JW Player on commercial sites' +
							' such as: sites owned or operated by corporations, sites with advertisements, sites designed to promote a product, service or brand, ' +
							'etc.  If you are not sure whether you need to purchase a license, contact us.  You also may not use the AdSolution monetization plugin ' +
							'(which lets you make money off your player).">NON-COMMERCIAL <img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" />' +
							'</a>&nbsp;&bull;&nbsp;<a href="http://corp.kaltura.com/about/contact?subject=Upgrade%20JW%20Player%20to%20commercial%20license&amp;' +
							'&amp;pid=' + partner_id + '&amp;name=' + screen_name + '&amp;email=' + email  + '" target="_blank" class="license tooltip" ' +
							'title="Go to the Contact Us page and call us or fill in our Contact form and we\'ll call you (opens in new window/ tab).">Upgrade ' +
							'<img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" /></a>';
		jw_license_ads_html = '<li>Requires Commercial License</li>';
	}

	if(is_playlist) {
	  ui_conf_select = createSelectUiConfForPlaylist ( entry_id , ui_conf_id );
	  window.showing_jw_playlist = true;
	  window.showing_playlist_type = jw_playlistType;
	}
	else {
	  ui_conf_select = createSelectUiConfForPlayer ( entry_id , ui_conf_id );
	  window.showing_jw_playlist = false;
	  window.showing_playlist_type = null;
	}

	modal_html = 	'<div class="title">\n' +
					' <a href="#close" onclick="kalturaCloseModalBox(); return false;" class="closeBtn"></a>' +
					'<a href="<? echo $service_url; ?>/index.php/kmc/help#contentSection118" target="_blank"></a>\n' +
					'Entry ID: ' + entry_id + '<br style="margin-bottom:10px;"/>' + ui_conf_select + '\n</div>' +
					showNoMix(false,"check") +
					'<div id="player_wrap"></div>' +
					'<div class="label">License Type:</div>' +
					'<div class="description">' +
					jw_license_html + '</div>' +
					'<div class="label">AdSolution:</div>' +
					'<div class="description">' +
					' <input type="checkbox" id="AdSolution" onclick="adSolution()" onmousedown="window.chkbox_flag=true" />' +
					'Enable ads in your videos.&nbsp; <a href="http://www.longtailvideo.com/referral.aspx?page=kaltura&ref=azbkefsfkqchorl" target="_blank" class="tooltip" ' +
					'title="Go to the JW website to sign up for FREE or to learn more about running in-stream ads in your player from Google AdSense for ' +
					'Video, ScanScout, YuMe and others. (opens in new window/ tab)"> Free sign up... <img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" />' +
					'</a><br />\n <ul id="ads_notes">\n  <li>Channel Code: <input onblur="adsChannel(this,\'' + entry_id + '\',\'' + jw_skin +
					'\',\'' + jw_width + '\',\'' + jw_height + '\',' + jw_share + ',\'' + jw_flashvars + '\',\'' + ui_conf_id + '\',\'' + uiconf + '\')" type="text"' +
					' id="adSolution_channel" value="" /> <button>Apply</button></li>\n' +
					<?php if($jw_license=="non-commercial") { ?>
						'<li>Requires <a href="http:/corp.kaltura.com/about/contact?00N70000002GA5H=upgrade%20to%20JW%20commercial%20license" class="tooltip" title="' +
						'With a Commercial license your player will not show the JW Player watermark and you will be allowed to use the player on any site you ' +
						'want as well as use AdSolution (which lets you make money off your player)." target="_blank">Commercial license <img ' +
						'src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" /></a></li>' +
					<?php } ?>
					'</ul>\n </div>\n' +
					' <div class="label">Embed Code:</div>' +
					'<textarea style="width:' + (parseInt(jw_width)-12) + 'px; height:111px;" id="jw_embed_code" readonly="readonly" onfocus="copyCode()">' +
					doJwPreviewEmbed(entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf, is_playlist, jw_playlistType) +
					'</textarea>\n<div style="text-align:center;">' +
					'<div id="jw_copy">Press Ctrl+C to copy embed code (Command+C on Mac)</div>' +
					'<button id="copy_code" onclick="copyCode()">Select Code</button></div>';

	setObjectToRemove("kaltura_player");
	modal.innerHTML = modal_html;
	$("#player_wrap").html(doJwPreviewEmbed(entry_id, jw_skin, jw_width, jw_height, jw_share, jw_flashvars, ui_conf_id, uiconf, is_playlist, jw_playlistType));
}

function openPlayer ( entry_id , pl_width_str , pl_height_str , ui_conf_id  , jw_skin, jw_share, jw_flashvars, jw_playlistType) {

	if(ui_conf_id<1000 && ui_conf_id>799) {
//		jw_swf,partner_id,entry_id,skin,width,height,share,flashvars
		build_modal(entry_id , jw_skin, pl_width_str , pl_height_str , jw_share, jw_flashvars, ui_conf_id, null, false, null);
		$("div.description").css("float","left");
		return;
	}

		width_str = '400';
		height_str = '332';

<? 	if (false && kConf::get('www_host') == 'www.kaltura.com') {		?>
		width_str = '400';
		height_str = '332';
		ui_conf_id = '48110';
<?	}	else { ?>
		// check if the page is now in state where there is a selected_uiconfId
		if(sub_nav_tab == "Playlist")
		  selected_uiconfId = null;
		if ( ! ui_conf_id && selected_uiconfId != null && ui_confs_player )
		{
			for(var i = 0; i < ui_confs_player.length; i++)
			{
				var uiconf = ui_confs_player[i];
				if ( uiconf.id == selected_uiconfId )
				{
					ui_conf_id = uiconf.id;
					width_str = pl_width_str = uiconf.width;
					height_str = pl_height_str = uiconf.height;
					break;
				}
			}
		}
		else
		{
			if ( ! ui_conf_id )
			{
				ui_conf_id = '<? echo $player_uiconf_list[0]->getId(); ?>';
			}

			if ( pl_width_str )
			{
				width_str = pl_width_str;
			}
			else
			{
				width_str = '<? echo $player_uiconf_list[0]->getWidth(); ?>';
			}

			if ( pl_height_str )
			{
				height_str = pl_height_str;
			}
			else
			{
				height_str = '<? echo $player_uiconf_list[0]->getHeight(); ?>';
			}
		}
<?	} ?>


	// for now the embed code will be hard-coded
	embed_code = "<?php echo str_replace("wid/", "$cache_st/wid/", str_replace ( '"' , '\"' , $embed_code )) ?>";

//	embed_code = embed_code.replace ( /\/wid\/_([0-9]*)/g , '/wid/_$1/ui_conf_id/' + ui_conf_id  +"/entry_id/" + entry_id );
	// remove the original uiconf_id and replace it with the new one + entry_id now playing
	if ( entry_id ) entry_str = '/entry_id/' + entry_id;
	else entry_str = "";
	embed_code = embed_code.replace ( /\/uiconf_id\/[0-9]*/g , '/uiconf_id/' + ui_conf_id  + entry_str );
	embed_code = embed_code.replace ( /width="[0-9a-zA-Z]*"/g , 'width="' + width_str + '"' );
	embed_code = embed_code.replace ( /height="[0-9a-zA-Z]*"/g , 'height="' + height_str + '"' );

	global_embed_code = embed_code;

	// replace the embed_code to use the ui_conf_id + width + height
	window.ui_conf_select = createSelectUiConfForPlayer ( entry_id , ui_conf_id );

	pl_width = parseInt ( width_str );
	pl_height = parseInt ( height_str );
	modal = kalturaInitModalBox ( null , { width:pl_width+20 , height: (pl_height + 200)  } );
	playlist_html =
		"<div class='title'><a href='#close' onclick='kalturaCloseModalBox(); return false;' class='closeBtn'></a><a href='<? echo $service_url; ?>/index.php/kmc/help#contentSection118' target='_blank'></a>";
	if ( entry_id )
		playlist_html += "Entry ID: " + entry_id + ui_conf_select ;
	else
		playlist_html += "&nbsp;";
	playlist_html +=  "</div>" + // third tr will have the playlist_id
		"<div class='kplayer' style='height:" + pl_height + "px'>" + embed_code + "</div>" + // create div to hold the playlist
		"<div class='embed_code'style='text-align:center'><textarea id='embed_code' cols='30' rows='5' readonly='true' onclick='copyToClipboard(\"embed_code\");'>" + embed_code + "</textarea></div>" + // raw embed code
		"<div class='buttons'><button onclick='copyToClipboard(\"embed_code\");'>Select code</div>";

	setObjectToRemove ( "kaltura_player" );
	modal.innerHTML = playlist_html;
}


function createSelectUiConfForPlayer ( entry_id , ui_conf_id )
{
	var ui_conf_select = "<span style='display:block; padding-left:10px;'><select id='select_player' onchange='" +
		"reopenPlayer( \"" +  entry_id  + "\" , this );'>";

	if (ui_confs_player)
	{
		for(var i = 0; i < ui_confs_player.length; i++)
		{
			var uiconf = ui_confs_player[i];
			ui_conf_select += createSelectUiConfAddOption(ui_conf_id ,uiconf.id, uiconf.width, uiconf.height, uiconf.name, uiconf.skin, uiconf.share, uiconf.flashvars);
		}
	}
	else
	{
<?php
if (false && kConf::get('www_host') != 'www.kaltura.com'){
	foreach ( $player_uiconf_list as $ui_conf )
	{
		$name = $ui_conf->getName();
		$name =  substr( htmlspecialchars  ( $name , ENT_QUOTES ) ,0 , 30 );

		echo "ui_conf_select += createSelectUiConfAddOption ( ui_conf_id ," . $ui_conf->getId() . "," .   $ui_conf->getWidth() . "," .  $ui_conf->getHeight() . ",'$name'); \n";
	}
}
else {
?>
		ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48110,400,332 , "Dark player skin" );
		ui_conf_select += createSelectUiConfAddOption ( ui_conf_id , 48111,400,332 , "Light player skin" );
<?
}
?>
	}

	ui_conf_select += "</select></span>";

	return 	ui_conf_select;
}

//assume the ui_conf_data will be of the structure:
//{id,name,width,height}
function reopenPlayer ( entry_id , select_elem ) {
	ui_conf_data = select_elem.value.split(",");

	kalturaCloseModalBox();

	openPlayer ( entry_id , ui_conf_data[1] , ui_conf_data[2], ui_conf_data[0] , ui_conf_data[3] ,ui_conf_data[4] , ui_conf_data[5], null);

}

function onCloseCw ()
{
	kalturaCloseModalBox();
	modal = null;
}


function closeLoginF()
{
	alert('closeLoginF');
}

function logout()
{
	var expiry = new Date("January 1, 1970"); // "Thu, 01-Jan-70 00:00:01 GMT";
	expiry = expiry.toGMTString();
	document.cookie = "pid=; expires=" + expiry + "; path=/";
	document.cookie = "subpid=; expires=" + expiry + "; path=/";
	document.cookie = "uid=; expires=" + expiry + "; path=/";
	document.cookie = "kmcks=; expires=" + expiry + "; path=/";
	document.cookie = "screen_name=; expires=" + expiry + "; path=/";
	document.cookie = "email=; expires=" + expiry + "; path=/";
	$.ajax({
		url: location.protocol + "//" + location.hostname + "/index.php/kmc/logout",
		type: "POST",
		data: { "ks": ks },
		dataType: "json",
		complete: function() {
			window.location = "<?php echo $service_url; ?>/index.php/kmc/kmc?logout=";
		}
	});
}

// will load the content modul by default
loadModule ( <?php echo "'$module' , '$partner_id' , '$subp_id' , '$uid' , '$ks' ,'$screen_name' , '$email' " ?> );
function showNoMix(checkbox,action) {
	if(checkbox) {
		if($(checkbox).is(':checked'))
			action = "set";
		else
			action = "delete"
	}
	switch(action) {
		case "set" :
			document.cookie = "kmc_preview_show_nomix_box=true; ; path=/";
			$("#nomix_box").hide(250);
			break;
		case "delete" :
			document.cookie = "kmc_preview_show_nomix_box=true; expires=Sun, 01 Jan 2000 00:00:01 GMT; path=/";
			break;
		case "check" :
			if (document.cookie.indexOf("kmc_preview_show_nomix_box") == -1)
				var html =	'<div id="nomix_box"><p><strong>NOTE</strong>: ' +
							'The JW Player does not work with Kaltura <dfn title="A Video Mix is a video made up of two or more Entries, normally created through the Kaltura Editor.">Video Mixes</dfn>.</p>\n' +
							'<div><input type="checkbox" onclick="showNoMix(this)"> Don\'t show this message again.</div></div>\n';
			else
				var html =	'';
			break;
		default :
			alert("no action");
			return;
	}
	return html;
 }
</script>
