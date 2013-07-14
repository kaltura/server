/* kmc and kmc.vars defined in script block in kmc4success.php */

// For debug enable to true. Debug will show information in the browser console
kmc.vars.debug = false;

// Quickstart guide (should be moved to kmc4success.php)
kmc.vars.quickstart_guide = "/content/docs/pdf/KMC3_Quick_Start_Guide.pdf";
kmc.vars.help_url = kmc.vars.service_url + '/kmc5help.html';

// Log function
kmc.log = function(msg) {
	if(kmc.vars.debug) {
		if( typeof console !='undefined' && console.log){
			console.log(arguments);
		}
	}
};

kmc.functions = {

	loadSwf : function() {

		var kmc_swf_url = 'http://' + kmc.vars.cdn_host + '/flash/kmc/' + kmc.vars.kmc_version + '/kmc.swf';

		var flashvars = {
			// kmc configuration
			kmc_uiconf			: kmc.vars.kmc_general_uiconf,

			//permission uiconf id:
			permission_uiconf	: kmc.vars.kmc_permissions_uiconf,

			host				: kmc.vars.host,
			cdnhost				: kmc.vars.cdn_host,
			srvurl				: "api_v3/index.php",
			partnerid			: kmc.vars.partner_id,
			subpid				: kmc.vars.subp_id,
			uid					: kmc.vars.user_id,
			ks					: kmc.vars.ks,
			entryId				: "-1",
			kshowId				: "-1",
			debugmode			: "true",
			widget_id			: "_" + kmc.vars.partner_id,
			urchinNumber		: kmc.vars.google_analytics_account, // "UA-12055206-1""
			firstLogin			: kmc.vars.first_login,
			refreshPlayerList	: "refreshPlayerList", // @todo: ???!!!
			refreshPlaylistList : "refreshPlaylistList", // @todo: ???!!!
			openPlayer			: "kmc.preview_embed.doPreviewEmbed", // @todo: remove for 2.0.9 ?
			openPlaylist		: "kmc.preview_embed.doPreviewEmbed",
			openCw				: "kmc.functions.openKcw",
			language			: kmc.vars.language
		};
		var params = {
			allowNetworking: "all",
			allowScriptAccess: "always"
		};

		swfobject.embedSWF(kmc_swf_url, "kcms", "100%", "100%", "10.0.0", false, flashvars, params);
		$("#kcms").attr('style', ''); // Reset the object style
	},

	checkForOngoingProcess : function() {
		var warning_message;
		try {
			warning_message = $("#kcms")[0].hasOngoingProcess();
		}
		catch(e) {
			warning_message = null;
		}

		if(warning_message != null) {
			return warning_message;
		}
		return;
	},
	
	expired : function() {
		kmc.user.logout();
	},

	openKcw : function(conversion_profile, uiconf_tag) {

		conversion_profile = conversion_profile || "";

		// uiconf_tag - uploadWebCam or uploadImport
		var kcw_uiconf = (uiconf_tag == "uploadWebCam") ? kmc.vars.kcw_webcam_uiconf : kmc.vars.kcw_import_uiconf;

		var flashvars = {
			host			: kmc.vars.host,
			cdnhost			: kmc.vars.cdn_host,
			userId			: kmc.vars.user_id,
			partnerid		: kmc.vars.partner_id,
			subPartnerId	: kmc.vars.subp_id,
			sessionId		: kmc.vars.ks,
			devFlag			: "true",
			entryId			: "-1",
			kshow_id		: "-1",
			terms_of_use	: kmc.vars.terms_of_use,
			close			: "kmc.functions.onCloseKcw",
			quick_edit		: 0, 
			kvar_conversionQuality : conversion_profile
		};

		var params = {
			allowscriptaccess: "always",
			allownetworking: "all",
			bgcolor: "#DBE3E9",
			quality: "high",
			movie: kmc.vars.service_url + "/kcw/ui_conf_id/" + kcw_uiconf
		};
		
		kmc.layout.modal.open( {
			'width' : 700,
			'height' : 420,
			'content' : '<div id="kcw"></div>'
		} );

		swfobject.embedSWF(params.movie, "kcw", "680", "400" , "9.0.0", false, flashvars , params);
	},
	onCloseKcw : function() {
		kmc.layout.modal.close();
		$("#kcms")[0].gotoPage({
			moduleName: "content",
			subtab: "manage"
		});
	},
	// Should be moved into user object
	openChangePwd : function(email) {
		kmc.user.changeSetting('password', {
			email: email
		} );
	},
	openChangeEmail : function(email) {
		kmc.user.changeSetting('email', {
			email: email
		} );
	},
	openChangeName : function(fname, lname, email) {
		kmc.user.changeSetting('name', {
			fname: fname,
			lname: lname,
			email: email
		} );
	},
	getAddPanelPosition : function() {
		var el = $("#add").parent();
		return (el.position().left + el.width() - 10);
	},
	openClipApp : function( entry_id, mode ) {
		
		var iframe_url = 'http://' + window.location.hostname + '/apps/clipapp/' + kmc.vars.clipapp.version;
			iframe_url += '/?kdpUiconf=' + kmc.vars.clipapp.kdp + '&kclipUiconf=' + kmc.vars.clipapp.kclip;
			iframe_url += '&partnerId=' + kmc.vars.partner_id + '&mode=' + mode + '&config=kmc&entryId=' + entry_id;

		var title = ( mode == 'trim' ) ? 'Trimming Tool' : 'Clipping Tool';

		kmc.layout.modal.open( {
			'width' : 950,
			'height' : 616,
			'title'	: title,
			'content' : '<iframe src="' + iframe_url + '" width="100%" height="586" frameborder="0"></iframe>',
			'style'	: 'iframe'
		} );
	}
};

kmc.utils = {
	// Backward compatability
	closeModal : function() { kmc.layout.modal.close(); },

	handleMenu : function() {

		// Activate menu links
		kmc.utils.activateHeader();
	
		// Calculate menu width
		var menu_width = 10;
		$("#user_links > *").each( function() {
			menu_width += $(this).width();
		});

		var openMenu = function() {

			// Set close menu to true
			kmc.vars.close_menu = true;

			var menu_default_css = {
				"width": 0,
				"visibility": 'visible',
				"top": '6px',
				"right": '6px'
			};

			var menu_animation_css = {
				"width": menu_width + 'px',
				"padding-top": '2px',
				"padding-bottom": '2px'
			};

			$("#user_links").css( menu_default_css );
			$("#user_links").animate( menu_animation_css , 500);
		};

		$("#user").hover( openMenu ).click( openMenu );
		$("#user_links").mouseover( function(){
			kmc.vars.close_menu = false;
		} )
		$("#user_links").mouseleave( function() {
			kmc.vars.close_menu = true;
			setTimeout( "kmc.utils.closeMenu()" , 650 );
		} );
		$("#closeMenu").click( function() {
			kmc.vars.close_menu = true;
			kmc.utils.closeMenu();
		} );
	},

	closeMenu : function() {
		if( kmc.vars.close_menu ) {
			$("#user_links").animate( {
				width: 0
			} , 500, function() {
				$("#user_links").css( {
					width: 'auto',
					visibility: 'hidden'
				} );
			});
		}
	},

	activateHeader : function() {
		$("#user_links a").click(function(e) {
			var tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");

			switch(tab) {
				case "Quickstart Guide" :
					this.href = kmc.vars.quickstart_guide;
					return true;
					break;
				case "Logout" :
					kmc.user.logout();
					return false;
					break;
				case "Support" :
					kmc.user.openSupport(this);
					return false;
					break;
				case "ChangePartner" :
					kmc.user.changePartner();
					return false;
					break;
				default :
					return false;
			}
		});
	},

	resize : function() {
		var min_height = ($.browser.ie) ? 640 : 590;
		var doc_height = $(document).height(),
		offset = $.browser.mozilla ? 37 : 74;
		doc_height = (doc_height-offset);
		doc_height = (doc_height < min_height) ? min_height : doc_height; // Flash minimum height is 590 px
		$("#flash_wrap").height(doc_height + "px");
		$("#server_wrap iframe").height(doc_height + "px");
		$("#server_wrap").css("margin-top", "-"+ (doc_height + 2) +"px");
	},
	escapeQuotes : function(string) {
		string = string.replace(/"/g,"&Prime;");
		string = string.replace(/'/g,"&prime;");
		return string;
	},
	isModuleLoaded : function() {
		if($("#flash_wrap object").length || $("#flash_wrap embed").length) {
			kmc.utils.resize();
			clearInterval(kmc.vars.isLoadedInterval);
			kmc.vars.isLoadedInterval = null;
		}
	},
	debug : function() {
		try{
			console.info(" ks: ",kmc.vars.ks);
			console.info(" partner_id: ",kmc.vars.partner_id);
		}
		catch(err) {}
	},
	
	// we should have only one overlay for both flash & html modals
	maskHeader : function(hide) {
		if(hide) {
			$("#mask").hide();
		}
		else {
			$("#mask").show();
		}
	},

	// Create dynamic tabs
	createTabs : function(arr) {
		// Close the user link menu
		$("#closeMenu").trigger('click');
	
		if(arr) {
			var module_url = kmc.vars.service_url + '/index.php/kmc/kmc4',
				arr_len = arr.length,
				tabs_html = '',
				tab_class;
			for( var i = 0; i < arr_len; i++ ) {
				tab_class = (arr[i].type == "action") ? 'class="menu" ' : '';
				tabs_html += '<li><a id="'+ arr[i].module_name +'" ' + tab_class + ' rel="'+ arr[i].subtab +'" href="'+ module_url + '#' + arr[i].module_name +'|'+ arr[i].subtab +'"><span>' + arr[i].display_name + '</span></a></li>';
			}
				
			$('#hTabs').html(tabs_html);

			// Get maximum width for user name
			var max_user_width = ( $("body").width() - ($("#logo").width() + $("#hTabs").width() + 100) );
			if( ($("#user").width()+ 20) > max_user_width ) {
				$("#user").width(max_user_width);
			}
				
			$('#hTabs a').click(function(e) {
				var tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");
				var subtab = (e.target.tagName == "A") ? $(e.target).attr("rel") : $(e.target).parent().attr("rel");
					
				var go_to = {
					moduleName : tab,
					subtab : subtab
				};
				$("#kcms")[0].gotoPage(go_to);
				return false;
					
			});
		} else {
			alert('Error geting tabs');
		}
	},
		
	setTab : function(module, resetAll){
		if( resetAll ) {$("#kmcHeader ul li a").removeClass("active");}
		$("a#" + module).addClass("active");
	},

	// Reset active tab
	resetTab : function(module) {
		$("a#" + module).removeClass("active");
	},

	// we should combine the two following functions into one
	hideFlash : function(hide) {
		if(hide) {
			if( $.browser.msie ) {
				// For IE only we're positioning outside of the screen
				$("#flash_wrap").css("margin-right","3333px");
			} else {
				// For other browsers we're just make it
				$("#flash_wrap").css("visibility","hidden");
			}
		} else {
			if( $.browser.msie ) {
				$("#flash_wrap").css("margin-right","0");
			} else {
				$("#flash_wrap").css("visibility","visible");
			}
		}
	},
	showFlash : function() {
		$("#server_wrap").hide();
		$("#server_frame").removeAttr('src');
		if( !kmc.layout.modal.isOpen() ) {
			$("#flash_wrap").css("visibility","visible");
		}
		$("#server_wrap").css("margin-top", 0);
	},

	// HTML Tab iframe
	openIframe : function(url) {
		$("#flash_wrap").css("visibility","hidden");
		$("#server_frame").attr("src", url);
		$("#server_wrap").css("margin-top", "-"+ ($("#flash_wrap").height() + 2) +"px");
		$("#server_wrap").show();
	}
		
};

kmc.mediator =  {

	writeUrlHash : function(module,subtab){
		location.hash = module + "|" + subtab;
		document.title = "KMC > " + module + ((subtab && subtab != "") ? " > " + subtab + " |" : "");
	},
	readUrlHash : function() {
		var module = "dashboard", // @todo: change to kmc.vars.default_state.module ?
		subtab = "";
		try {
			var hash = location.hash.split("#")[1].split("|");
		}
		catch(err) {
			var nohash=true;
		}
		if(!nohash && hash[0]!="") {
			module = hash[0];
			subtab = hash[1];
			extra = {};
			if (hash[2])
			{
				var tmp = hash[2].split("&");
				for (var i = 0; i<tmp.length; i++)
				{
					var tmp2 = tmp[i].split(":");
					extra[tmp2[0]] = tmp2[1];
				}
			}

			// Support old hash links
			switch(module) {

				// case for Content tab
				case "content":
					switch(subtab) {
						case "Moderate":
							subtab = "moderation";
							break;
						case "Syndicate":
							subtab = "syndication";
							break;
					}
					subtab = subtab.toLowerCase();
					break;

				// case for Studio tab
				case "appstudio":
					module = "studio";
					subtab = "playersList";
					break;

				// case for Settings tab
				case "Settings":
					module = "account";
					switch(subtab) {
						case "Account_Settings":
							subtab = "overview";
							break;
						case "Integration Settings":
							subtab = "integration";
							break;
						case "Access Control":
							subtab = "accessControl";
							break;
						case "Transcoding Settings":
							subtab = "transcoding";
							break;
						case "Account Upgrade":
							subtab = "upgrade";
							break;
					}
					break;
		    
				// case for Analytics tab
				case "reports":
					module = "analytics";
					if(subtab == "Bandwidth Usage Reports") {
						subtab = "usageTabTitle";
					}
					break;
			}
		}

		return {
			"moduleName" : module,
			"subtab" : subtab,
			"extra" : extra
		};
	},
	selectContent : function(uiconf_id,is_playlist) { // called by selectPlaylistContent which is caled from appstudio
		//			alert("selectContent("+uiconf_id+","+is_playlist+")");
		var subtab = is_playlist ? "playlists" : "manage";
		//			kmc.vars.current_uiconf = uiconf_id; // used by doPreviewEmbed
		kmc.vars.current_uiconf = {
			"uiconf_id" : uiconf_id ,
			"is_playlist" : is_playlist
		}; // used by doPreviewEmbed
	}
};

kmc.preview_embed = {
	// Should be changed to accept object with parameters
	doPreviewEmbed : function(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, html5_compatible) {
		kmc.log('doPreviewEmbed', arguments);

		var has_mobile_flavors = kmc.preview_embed.hasMobileFlavors( entry_flavors );
		// default value for html5_compatible
		html5_compatible = (html5_compatible) ? html5_compatible : false;
		html5_compatible = (previewOnly) ? false : html5_compatible;

		if(id != "multitab_playlist") {
			name = (name) ? kmc.utils.escapeQuotes(name) : '';
			description = kmc.utils.escapeQuotes(description); 

			if(kmc.vars.current_uiconf) { // set by kmc.mediator.selectContent called from appstudio's "select content" action
				if((is_playlist && kmc.vars.current_uiconf.is_playlist) || (!is_playlist && !kmc.vars.current_uiconf.is_playlist)) { // @todo: minor optimization possible
					uiconf_id = kmc.vars.current_uiconf.uiconf_id;
				}
				kmc.vars.current_uiconf = null;
			}

			if(!uiconf_id) { // get default uiconf_id (first one in list)
				uiconf_id = (is_playlist) ? kmc.vars.playlists_list[0].id : kmc.vars.players_list[0].id;
			}
		}

		var embed_code, preview_player,
		id_type = is_playlist ? "Playlist " + (id == "multitab_playlist" ? "Name" : "ID") : "Embedding",
		uiconf_details = kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist);

		if( live_bitrates ) { kmc.vars.embed_code_delivery_type = "http"; } // Reset delivery type to http
		
		embed_code = kmc.preview_embed.buildKalturaEmbed(id, name, description, is_playlist, uiconf_id);
		preview_player = embed_code.replace('{FLAVOR}','ks=' + kmc.vars.ks + '&');
		embed_code = embed_code.replace('{FLAVOR}','');
		
		var modal_content = ((live_bitrates) ? kmc.preview_embed.buildLiveBitrates(name,live_bitrates) : '') +
		'<div id="player_wrap">' + preview_player + '</div>' +
		((id == "multitab_playlist") ? '' : kmc.preview_embed.buildSelect(is_playlist, uiconf_id)) +
		((live_bitrates) ? '' : kmc.preview_embed.buildRtmpOptions()) +
		((html5_compatible) ? kmc.preview_embed.buildHTML5Option(id, kmc.vars.partner_id, uiconf_id, has_mobile_flavors) : '') +
		'<div class="embed_code_div"><div class="label embedcode">Embed Code:</div> <div class="right"><textarea id="embed_code" rows="5" cols=""' +
		'readonly="true">' + embed_code + '</textarea></div><br class="clear" />' +
		'<div id="copy_msg">Press Ctrl+C to copy embed code (Command+C on Mac)</div><div class="center"><button id="select_code">' +
		'<span>Select Code</span></button></div></div>';

		kmc.layout.modal.open( {
			'width' : parseInt(uiconf_details.width) + 140,
			'title' : id_type + ': ' + name,
			'help' : '<a class="help icon" target="_blank" href="' + kmc.vars.help_url + '#section118"></a>',
			'content' : '<div id="preview_embed">' + modal_content + '</div>'
		} );

		// attach events here instead of writing them inline
		$("#embed_code, #select_code").click(function(){
			$("#copy_msg").show();
			setTimeout(function(){
				$("#copy_msg").hide(500);
			},1500);
			$("textarea#embed_code").select();
		});

		$("#delivery_type").change(function(){
			kmc.vars.embed_code_delivery_type = this.value;
			kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, html5_compatible);
		});
		$("#player_select").change(function(){
			kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, this.value, live_bitrates, entry_flavors, html5_compatible);
		});
			
		$("#html5_support").change(function(){
			var html5_support = ($(this).attr("checked")) ? true : false;
			var val = kmc.preview_embed.buildKalturaEmbed(id,name,description, is_playlist, uiconf_id, html5_support);
			$("#embed_code").val(val);
		});
			
		// show the embed code & enable the checkbox if its not a preview
		if (previewOnly==false) {
			$('.embed_code_div').show();
		}
		if(has_mobile_flavors) {
			$('#html5_support').attr('disabled', null);
		}
	}, // doPreviewEmbed

	buildLiveBitrates : function(name,live_bitrates) {
		var bitrates = "",
		len = live_bitrates.length,
		i;
		for(i=0;i<len;i++) {
			bitrates += live_bitrates[i].bitrate + " kbps, " + live_bitrates[i].width + " x " + live_bitrates[i].height + "<br />";
		}
		var lbr_data = 	'<dl style="margin: 0 0 15px">' + '<dt>Name:</dt><dd>' + name + '</dd>' +
		'<dt>Bitrates:</dt><dd>' + bitrates + '</dd></dl>';
		return lbr_data;
	},

	buildRtmpOptions : function() {
		var selected = ' selected="selected"';
		var delivery_type = kmc.vars.embed_code_delivery_type || "http";
		var html = '<div id="rtmp" class="label">Select Flash Delivery Type:</div> <div class="right"><select id="delivery_type">';
		var options = '<option value="http"' + ((delivery_type == "http") ? selected : "") + '>Progressive Download (HTTP)&nbsp;</option>' +
		'<option value="rtmp"' + ((delivery_type == "rtmp") ? selected : "") + '>Adaptive Streaming (RTMP)&nbsp;</option>';
		if(!kmc.vars.hide_akamai_hd_network) {
			options += '<option value="akamai"' + ((delivery_type == "akamai") ? selected : "") + '>Akamai HD Network &nbsp;</option>';
		}
		html += options + '</select></div><br /><div class="note">Adaptive Streaming automatically adjusts to the viewer\'s bandwidth,' +
		'while Progressive Download allows buffering of the content. <a target="_blank" href="' + kmc.vars.help_url + '#1431">Read more</a></div><br />';
		return html;
	},
		
	buildHTML5Option : function(entry_id, partner_id, uiconf_id, has_mobile_flavors) {
		kmc.log('buildHTML5Option');
		kmc.log(arguments);
			
		var long_url = kmc.vars.service_url + '/index.php/kmc/preview/partner_id/' + partner_id + '/entry_id/' + entry_id + '/uiconf_id/' + uiconf_id + '/delivery/' + kmc.vars.embed_code_delivery_type;
		kmc.client.getShortURL(long_url);
			
		var description = '<div class="note red">This video does not have video flavors compatible with IPhone & IPad. <a target="_blank" href="' + kmc.vars.help_url + '#section1432">Read more</a></div>';
		if(has_mobile_flavors) {
			description = '<div class="note">If you enable the HTML5 player, the viewer device will be automatically detected.' +
			' <a target="_blank" href="' + kmc.vars.help_url + '#section1432">Read more</a>' +
			'<br class"clear" />View player outside KMC: <span class="preview_url"><img src="/lib/images/kmc/url_loader.gif" alt="loading..." /> Updating Short URL...</span></div>';
		}
		var html = '<div class="label checkbox"><input id="html5_support" type="checkbox" disabled="disabled" /> <label for="html5_support">Support iPhone' +
		' &amp; iPad with HTML5</label></div><br />' + description + '<br />';
		return html;
	},

	// for content|Manage->drilldown->flavors->preview
	// flavor_details = json:
	doFlavorPreview : function(entry_id, entry_name, flavor_details) {

		var player_code = kmc.preview_embed.buildKalturaEmbed(entry_id,entry_name,null,false,kmc.vars.default_kdp);
		player_code = player_code.replace('&{FLAVOR}', '&flavorId=' + flavor_details.asset_id + '&ks=' + kmc.vars.ks);
		
		var modal_content = player_code + '<dl>' +
		'<dt>Entry Name:</dt><dd>&nbsp;' + entry_name + '</dd>' +
		'<dt>Entry Id:</dt><dd>&nbsp;' + entry_id + '</dd>' +
		'<dt>Flavor Name:</dt><dd>&nbsp;' + flavor_details.flavor_name + '</dd>' +
		'<dt>Flavor Asset Id:</dt><dd>&nbsp;' + flavor_details.asset_id + '</dd>' +
		'<dt>Bitrate:</dt><dd>&nbsp;' + flavor_details.bitrate + '</dd>' +
		'<dt>Codec:</dt><dd>&nbsp;' + flavor_details.codec + '</dd>' +
		'<dt>Dimensions:</dt><dd>&nbsp;' + flavor_details.dimensions.width + ' x ' + flavor_details.dimensions.height + '</dd>' +
		'<dt>Format:</dt><dd>&nbsp;' + flavor_details.format + '</dd>' +
		'<dt>Size (KB):</dt><dd>&nbsp;' + flavor_details.sizeKB + '</dd>' +
		'<dt>Status:</dt><dd>&nbsp;' + flavor_details.status + '</dd>' +
		'</dl>';

		kmc.layout.modal.open( {
			'width' : parseInt(kmc.vars.default_kdp.width) + 20,
			'height' : parseInt(kmc.vars.default_kdp.height) + 300,
			'title' : 'Flavor Preview',
			'content' : '<div id="preview_embed">' + modal_content + '</div>'
		} );

	},

	// eventually replace with <? php echo $embedCodeTemplate; ?>  ;  (variables used: HEIGHT WIDTH HOST CACHE_ST UICONF_ID PARTNER_ID PLAYLIST_ID ENTRY_ID) + {VER}, {SILVERLIGHT}, {INIT_PARAMS} for Silverlight + NAME, DESCRIPTION
	embed_code_template :	{
		object_tag :	'<object id="kaltura_player_{CACHE_ST}" name="kaltura_player_{CACHE_ST}" type="application/x-shockwave-flash" allowFullScreen="true" ' +
		'allowNetworking="all" allowScriptAccess="always" height="{HEIGHT}" width="{WIDTH}" bgcolor="#000000" ' +
		'xmlns:dc="http://purl.org/dc/terms/" xmlns:media="http://search.yahoo.com/searchmonkey/media/" rel="media:{MEDIA}" ' +
		'resource="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" ' +
		'data="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}">' +
		'<param name="allowFullScreen" value="true" /><param name="allowNetworking" value="all" />' +
		'<param name="allowScriptAccess" value="always" /><param name="bgcolor" value="#000000" />' +
		'<param name="flashVars" value="{FLASHVARS}&{FLAVOR}" /><param name="movie" value="http://{HOST}/index.php/kwidget' +
		'/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" />{ALT} {SEO} ' + '</object>',
		script_tag :	'<script type="text/javascript" src="{SCRIPT_URL}"></script>',
		iframe_tag : 	'<iframe id="kaltura_player_{CACHE_ST}" name="kaltura_player" src="{IFRAME_URL}"' +
		' height="{HEIGHT}" width="{WIDTH}" frameborder="0">{ALT} {SEO}</iframe>',
		playlist_flashvars :	'playlistAPI.autoInsert=true&playlistAPI.kpl0Name={PL_NAME}' +
		'&playlistAPI.kpl0Url=http%3A%2F%2F{HOST}%2Findex.php%2Fpartnerservices2%2Fexecuteplaylist%3Fuid%3D%26' +
		'partner_id%3D{PARTNER_ID}%26subp_id%3D{PARTNER_ID}00%26format%3D8%26ks%3D%7Bks%7D%26playlist_id%3D{PLAYLIST_ID}',
		kaltura_links :		'<a href="http://corp.kaltura.com">video platform</a> <a href="http://corp.kaltura.com/video_platform/video_management">' +
		'video management</a> <a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a> ' +
		'<a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a>',
		media_seo_info :	'<a rel="media:thumbnail" href="http://{CDN_HOST}/p/{PARTNER_ID}/sp/{PARTNER_ID}00/thumbnail{ENTRY_ID}/width/120/height/90/bgcolor/000000/type/2"></a> ' +
		'<span property="dc:description" content="{DESCRIPTION}"></span><span property="media:title" content="{NAME}"></span> ' +
		'<span property="media:width" content="{WIDTH}"></span><span property="media:height" content="{HEIGHT}"></span> ' +
		'<span property="media:type" content="application/x-shockwave-flash"></span>'
	},

	// id = entry id, asset id or playlist id; name = entry name or playlist name;
	// uiconf = uiconfid (normal scenario) or uiconf details json (for #content|Manage->drill down->flavors->preview)
	buildKalturaEmbed : function(id, name, description, is_playlist, uiconf, html5) {

		var uiconf_id = uiconf.uiconf_id || uiconf,
		uiconf_details = (typeof uiconf == "object") ? uiconf : kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist),  // getUiconfDetails returns json
		cache_st = kmc.preview_embed.setCacheStartTime(),
		embed_code;

		embed_code = (html5) ? kmc.preview_embed.embed_code_template.script_tag + '\n' + kmc.preview_embed.embed_code_template.object_tag : kmc.preview_embed.embed_code_template.object_tag;
		if(!kmc.vars.jw) { // more efficient to add "&& !kmc.vars.silverlight" (?)
			kmc.vars.embed_code_delivery_type = kmc.vars.embed_code_delivery_type || "http";
			if(kmc.vars.embed_code_delivery_type == "rtmp") {
				embed_code = embed_code.replace("{FLASHVARS}", "streamerType=rtmp&amp;{FLASHVARS}"); // rtmp://rtmpakmi.kaltura.com/ondemand
			} else if (kmc.vars.embed_code_delivery_type == "akamai") {
				embed_code = embed_code.replace("{FLASHVARS}", "streamerType=hdnetwork&amp;akamaiHD.loadingPolicy=preInitialize&amp;akamaiHD.asyncInit=true&amp;{FLASHVARS}");
			}
		}
		if(is_playlist && id != "multitab_playlist") {	// playlist (not multitab)
			embed_code = embed_code.replace(/{ENTRY_ID}/g,"");
			embed_code = embed_code.replace("{FLASHVARS}",kmc.preview_embed.embed_code_template.playlist_flashvars);
			//				kmc.log(uiconf_details.swf_version); alert("uiconf_details.swf_version logged");
			if(uiconf_details.swf_version.indexOf("v3") == -1) { // not kdp3
				embed_code = embed_code.replace("playlistAPI.autoContinue","k_pl_autoContinue");
				embed_code = embed_code.replace("playlistAPI.autoInsert","k_pl_autoInsertMedia");
				embed_code = embed_code.replace("playlistAPI.kpl0Name","k_pl_0_name");
				embed_code = embed_code.replace("playlistAPI.kpl0Url","k_pl_0_url");
			}
		}
		else {											// player and multitab playlist
			embed_code = embed_code.replace("{SEO}", (is_playlist ? "" : kmc.preview_embed.embed_code_template.media_seo_info));
			embed_code = embed_code.replace(/{ENTRY_ID}/g, (is_playlist ? "" : "/entry_id/" + id));
			embed_code = embed_code.replace("{FLASHVARS}", "");
		}
			
		var iframe_url = kmc.vars.service_url + '/html5/html5lib/v1.2/mwEmbedFrame.php/entry_id/' + id + '/wid/_' + kmc.vars.partner_id + '/uiconf_id/' + uiconf_id;
		var script_url = kmc.vars.service_url + '/p/'+ kmc.vars.partner_id + '/sp/' + kmc.vars.partner_id + '00/embedIframeJs/uiconf_id/' + uiconf_id + '/partner_id/' + kmc.vars.partner_id;
			
		embed_code = embed_code.replace("{MEDIA}", "video");
		embed_code = embed_code.replace(/{HEIGHT}/gi,uiconf_details.height);
		embed_code = embed_code.replace(/{WIDTH}/gi,uiconf_details.width);
		embed_code = embed_code.replace(/{HOST}/gi,kmc.vars.host);
		embed_code = embed_code.replace(/{CACHE_ST}/gi,cache_st);
		embed_code = embed_code.replace(/{UICONF_ID}/gi,uiconf_id);
		embed_code = embed_code.replace(/{PARTNER_ID}/gi,kmc.vars.partner_id);
		embed_code = embed_code.replace("{PLAYLIST_ID}",id);
		embed_code = embed_code.replace("{PL_NAME}",name);
		embed_code = embed_code.replace(/{SERVICE_URL}/gi,kmc.vars.service_url);
		embed_code = embed_code.replace("{ALT}", ((kmc.vars.whitelabel || kmc.vars.ignore_seo_links) ? "" : kmc.preview_embed.embed_code_template.kaltura_links));
		embed_code = embed_code.replace("{CDN_HOST}",kmc.vars.cdn_host);
		embed_code = embed_code.replace("{NAME}", name);
		embed_code = embed_code.replace("{DESCRIPTION}", description);
		embed_code = embed_code.replace("{IFRAME_URL}", iframe_url); 
		embed_code = embed_code.replace("{SCRIPT_URL}", script_url); 

		return embed_code;
	},

	buildSelect : function(is_playlist, uiconf_id) {

		uiconf_id = kmc.vars.current_uiconf || uiconf_id; 
		var list_type = is_playlist ? "playlist" : "player",
		list_length = eval("kmc.vars." + list_type + "s_list.length"),
		html_select = '',
		this_uiconf, selected;

		for(var i=0; i<list_length; i++) {
			this_uiconf = eval("kmc.vars." + list_type + "s_list[" + i + "]"),
			selected = (this_uiconf.id == uiconf_id) ? ' selected="selected"' : '';
			html_select += '<option ' + selected + ' value="' + this_uiconf.id + '">' + this_uiconf.name + '</option>';
		}
		html_select = '<div class="label">Select Player:</div><div class="right"><select id="player_select">' + html_select + '</select></div><br /><div class="note">Kaltura player includes both layout and functionality (advertising, subtitles, etc)</div><br />';
		kmc.vars.current_uiconf = null;
		return html_select;
	},

	getUiconfDetails : function(uiconf_id,is_playlist) {

		var i,
		uiconfs_array = is_playlist ? kmc.vars.playlists_list : kmc.vars.players_list;
		for(i in uiconfs_array) {
			if(uiconfs_array[i].id == uiconf_id) {
				return uiconfs_array[i];
				break;
			}
		}
		$("#kcms")[0].alert("getUiconfDetails error: uiconf_id "+uiconf_id+" not found in " + ((is_playlist) ? "kmc.vars.playlists_list" : "kmc.vars.players_list"));
		return false;
	},
	setCacheStartTime : function() {
		var d = new Date;
		cache_st = Math.floor(d.getTime() / 1000) + (15 * 60); // start caching in 15 minutes
		return cache_st;
	},
	updateList : function(is_playlist) {

		var type = is_playlist ? "playlist" : "player";
		$.ajax({
			url: kmc.vars.getuiconfs_url,
			type: "POST",
			data: {
				"type": type,
				"partner_id": kmc.vars.partner_id,
				"ks": kmc.vars.ks
				},
			dataType: "json",
			success: function(data) {
				if (data && data.length) {
					if(is_playlist) {
						kmc.vars.playlists_list = data;
					}
					else {
						kmc.vars.players_list = data;
					}
				}
			}
		});
	},
		
	setShortURL : function(id) {
		var url = kmc.vars.service_url + '/tiny/' + id;
		//var url_text = url.replace(/http:\/\/|www./ig, '');
		var url_text = url.replace(/http:\/\//ig, '');
			
		var html = '<a href="' + url + '" target="_blank">' + url_text + '</a>';
		$(".preview_url").html(html);
	},
	hasMobileFlavors : function( entry_flavors ) {
		if( !entry_flavors ) { return false; }
		for(var i=0; i<entry_flavors.length; i++) {
			var asset = entry_flavors[i];
			// Add iPad Akamai flavor to iPad flavor Ids list
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('ipadnew') != -1 ){
				return true;
			}

			// Add iPhone Akamai flavor to iPad&iPhone flavor Ids list
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('iphonenew') != -1 ){
				return true;
			}

			// Check the tags to read what type of mp4 source
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('ipad') != -1 ){
				return true;
			}

			// Check for iPhone src
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('iphone') != -1 ){
				return true;
			}
		}
		return false;
	}
};

// TODO: Create one function to handle all client requests
kmc.client = {
			
	buildClientURL : function(service, action) {
		//return kmc.vars.service_url + '/api_v3/index.php?service='+service+'&action='+action;
		return 'http://' + window.location.hostname + '/api_v3/index.php?service='+service+'&action='+action;
	},
		
	// Get the Short URL code
	getShortURL : function(url) {
		kmc.log('getShortURL');
			
		// First do short_url :: list action to see if it already exists
		var service_url = kmc.client.buildClientURL("shortlink_shortlink", "list");
			
		var data = {
			"ks"					: kmc.vars.ks,
			"format"				: 1,
			"filter:objectType"		: "KalturaShortLinkFilter",
			"filter:userIdEqual"	: kmc.vars.user_id,
			"filter:systemNameEqual": "KMC-PREVIEW"
		};
			
		$.getJSON( service_url, data, function(res) {
			if(res.totalCount == 0) {
				// if no url were found, create a new one
				return kmc.client.createShortURL(url);
			} else {
				// update the url
				var id = res.objects[0].id;
				var res_url = res.objects[0].fullUrl;
				if(url == res_url) {
					kmc.preview_embed.setShortURL(id);
				} else {
					return kmc.client.updateShortURL(url, id);
				}
			}
		} );
	},
		
	createShortURL : function(url) {
		kmc.log('createShortURL');
			
		var service_url = kmc.client.buildClientURL("shortlink_shortlink", "add");
			
		var data = {
			"ks"					: kmc.vars.ks, // Set KS
			"format"				: 1, //format JSON
			"shortLink:objectType"	: "KalturaShortLink",
			"shortLink:userId"		: kmc.vars.user_id,
			"shortLink:systemName"	: "KMC-PREVIEW", // Unique name for filtering
			"shortLink:fullUrl"		: url
		};
			
		$.getJSON( service_url, data, function(res) {
			kmc.preview_embed.setShortURL(res.id);
		});
	},
		
	updateShortURL : function(url, id) {
		kmc.log('updateShortURL');
			
		var service_url = kmc.client.buildClientURL("shortlink_shortlink", "update");
			
		var data = {
			"ks"					: kmc.vars.ks, // Set KS
			"format"				: 1, //format JSON
			"id"					: id,
			"shortLink:objectType"	: "KalturaShortLink",
			"shortLink:fullUrl"		: url
		};
			
		$.getJSON( service_url, data, function(res) {
			kmc.preview_embed.setShortURL(id);
		});
			
	}
};

// Maintain support for old kmc2 functions:
function openPlayer(title, width, height, uiconf_id, previewOnly) {
	if (previewOnly==true) $("#kcms")[0].alert('previewOnly from studio');
	kmc.preview_embed.doPreviewEmbed("multitab_playlist", title, null, previewOnly, true, uiconf_id, false, false, false);
}
function playlistAdded() {kmc.preview_embed.updateList(true);}
function playerAdded() {kmc.preview_embed.updateList(false);}
/*** end old functions ***/

// When page ready initilize KMC
$(function() {
	kmc.layout.init();
	kmc.utils.handleMenu();
	kmc.functions.loadSwf();

	// Load kdp player & playlists for preview & embed
	kmc.preview_embed.updateList(); // Load players
	kmc.preview_embed.updateList(true); // Load playlists
});

// When flash finished loading, resize the page
$(window).load(function(){
	$(window).wresize(kmc.utils.resize);
	kmc.vars.isLoadedInterval = setInterval("kmc.utils.isModuleLoaded()",200);
});

// If we have ongoing process, we show a warning message when the user try to leaves the page
window.onbeforeunload = kmc.functions.checkForOngoingProcess;

kmc.layout = {
	init: function() {
		// Close open menu if user click anywhere
		$("#kmcHeader").bind( 'click', function() { 
			$("#hTabs a").each(function(inx, tab) {
				var $tab = $(tab);
				if( $tab.hasClass('menu') && $tab.hasClass('active') ){
					$("#kcms")[0].gotoPage({
						moduleName: $tab.attr('id'),
						subtab: $tab.attr('rel')
					});
				} else {
					return true;
				}
			});
		} );
		// Add Modal & Overlay divs when page loads
		$("body").append('<div id="mask"></div><div id="overlay"></div><div id="modal"><div class="title"><h2></h2><span class="close icon"></span></div><div class="content"></div></div>');
	},
	overlay: {
		show: function() {$("#overlay").show();},
		hide: function() {$("#overlay").hide();}
	},
	modal: {

		create: function(data) {
			// Set defaults
			var $modal = $("#modal"),
				$modal_title = $modal.find(".title h2"),
				$modal_content = $modal.find(".content"),

				options = {
					title : '',
					content : '',
					help : '',
					width : 680,
					height : 'auto',
					style : ''
				};
			// Overwrite defaults with data
			$.extend(options, data);

			// Set width & height
			$modal.css( {
				'width' : options.width,
				'height' : options.height
			}).attr('class', options.style);

			// Insert data into modal
			if( options.title ) {
				$modal_title.text(options.title).attr('title', options.title).parent().show();
			} else {
				$modal_title.parent().hide();
				$modal_content.addClass('flash_only');
			}
			$modal.find(".help").remove();
			$modal_title.parent().append( options.help );
			
			$modal_content.html(options.content);

			// Activate close button
			$modal.find(".close").click( function() {
				kmc.layout.modal.close();
			});

			return $modal;
		},

		show: function() {
			var $modal = $("#modal");

			kmc.utils.hideFlash(true);
			kmc.layout.overlay.show();
			$modal.fadeIn(600);
			if( ! $.browser.msie ) {
				$modal.css('display', 'table');
			}
			this.position();
		},

		open: function(data) {
			this.create(data);
			this.show();
		},
		
		position: function() {

			var $modal = $("#modal");
			// Calculate Modal Position
			var mTop = ( ($(window).height() - $modal.height()) / 2 ),
				mLeft = ( ($(window).width() - $modal.width()) / (2+$(window).scrollLeft()) );
				mTop = (mTop < 40) ? 40 : mTop;
			// Apply style
			$modal.css( {
				'top' : mTop + "px",
				'left' : mLeft + "px"
			});
			
		},
		close: function() {
			$("#modal").fadeOut(300, function() {
				$("#modal").find(".content").html('');
				kmc.layout.overlay.hide();
				kmc.utils.hideFlash();
			});
		},
		isOpen: function() {
			return $("#modal").is(":visible");
		}
	}
};

kmc.user = {

	openSupport: function(href) {

		// Show overlay
		kmc.utils.hideFlash(true);
		kmc.layout.overlay.show();

		// We want the show the modal only after the iframe is loaded so we use "create" instead of "open"
	   	var modal_content = '<iframe id="support" src="' + href + '" width="100%" scrolling="no" frameborder="0"></iframe>';
		kmc.layout.modal.create( {
			'width' : 550,
			'title' : 'Support Request',
			'content' : modal_content
		} );

		// Wait until iframe loads and then show the modal
		$("#support").load(function() {
			// In order to get the iframe content height the modal must be visible
			kmc.layout.modal.show();
			// Get iframe content height & update iframe
			var iframe_height = $("#support")[0].contentWindow.document.body.scrollHeight;
			$("#support").height( iframe_height );
			// Re-position the modal box
			kmc.layout.modal.position();
		});
	},

	logout: function() {
		var message = kmc.functions.checkForOngoingProcess();
		if( message ) { alert('message'); return false; }
		var expiry = new Date("January 1, 1970"); // "Thu, 01-Jan-70 00:00:01 GMT";
		expiry = expiry.toGMTString();
		document.cookie = "pid=; expires=" + expiry + "; path=/";
		document.cookie = "subpid=; expires=" + expiry + "; path=/";
		document.cookie = "uid=; expires=" + expiry + "; path=/";
		document.cookie = "kmcks=; expires=" + expiry + "; path=/";
		document.cookie = "screen_name=; expires=" + expiry + "; path=/";
		document.cookie = "email=; expires=" + expiry + "; path=/";
		var state = kmc.mediator.readUrlHash();
		$.ajax({
			url: location.protocol + "//" + location.hostname + "/index.php/kmc/logout",
			type: "POST",
			data: {
				"ks": kmc.vars.ks
				},
			dataType: "json",
			complete: function() {
				window.location = kmc.vars.service_url + "/index.php/kmc/kmc#" + state.moduleName + "|" + state.subtab;
			}
		});
	},

	changeSetting: function(action, fields) {
		// Set title
		var title, iframe_height;
		switch(action) {
			case "password":
				title = "Change Password";
				iframe_height = 180;
				break;
			case "email":
				title = "Change Email Address";
				iframe_height = 160;
				break;
			case "name":
				title = "Edit Name";
				iframe_height = 200;
				break;
		}

		// setup url
		var http_protocol = (kmc.vars.kmc_secured) ? 'https' : 'http';
		var from_domain = http_protocol + '://' + window.location.hostname;
		var url = from_domain + "/secure_form.php?action=" + action;
		// pass in the fields
		for(var i in fields) {
			var fld = (fields[i]) ? fields[i] : '';
			url += '&' + i + '=' + encodeURIComponent(fld);
		}
		// pass the parent url for the postMessage to work
		url = url + '&parent=' + encodeURIComponent(document.location.href);

		var modal_content = '<iframe src="' + url + '" width="100%" height="' + iframe_height + '" scrolling="no" frameborder="0"></iframe>';

		kmc.layout.modal.open( {
			'width' : 370,
			'title' : title,
			'content' : modal_content
		} );

		// setup a callback to handle the dispatched MessageEvent. if window.postMessage is supported the passed
		// event will have .data, .origin and .source properties. otherwise, it will only have the .data property.
		XD.receiveMessage(function(message){
			kmc.layout.modal.close();
			if(message.data == "reload") {
				if( ($.browser.msie) && ($.browser.version < 8) ) {
					window.location.hash = "account|user";
				}
				window.location.reload();
			}
		}, from_domain);
	},

	changePartner: function() {

		var i, pid = 0, selected, bolded,
			total = kmc.vars.allowed_partners.length;

		var modal_content = '<div id="change_account"><span>Please choose partner:</span><div class="container">';

		for( i=0; i < total; i++ ) {
			pid = kmc.vars.allowed_partners[i].id;
			if( kmc.vars.partner_id == pid ) {
				selected = ' checked="checked"';
				bolded = ' style="font-weight: bold"';
			} else {
				selected = '';
				bolded = '';
			}
			modal_content += '<label' + bolded + '><input type="radio" name="pid" value="' + pid + '" ' + selected + '/> &nbsp;' + kmc.vars.allowed_partners[i].name + '</label>';
		}
		modal_content += '</div><div class="center"><button id="do_change_partner"><span>Continue</span></button></div>';

		kmc.layout.modal.open( {
			'width' : 300,
			'title' : 'Change Account',
			'content' : modal_content
		} );

		$("#do_change_partner").click(function() {
			var pid = $('input[name=pid]:radio:checked').val();
			var url = '/index.php/kmc/extlogin?ks=' + kmc.vars.ks + '&partner_id=' + pid;
			window.location.href = url;
		});

		return false;
	}
};

/* WResize: plugin for fixing the IE window resize bug (http://noteslog.com/) */
(function($){$.fn.wresize=function(f){version='1.1';wresize={fired:false,width:0};function resizeOnce(){if($.browser.msie){if(!wresize.fired){wresize.fired=true}else{var version=parseInt($.browser.version,10);wresize.fired=false;if(version<7){return false}else if(version==7){var width=$(window).width();if(width!=wresize.width){wresize.width=width;return false}}}}return true}function handleWResize(e){if(resizeOnce()){return f.apply(this,[e])}}this.each(function(){if(this==window){$(this).resize(handleWResize)}else{$(this).resize(f)}});return this}})(jQuery);

/* XD: a backwards compatable implementation of postMessage (http://www.onlineaspect.com/2010/01/15/backwards-compatible-postmessage/) */
var XD=function(){var e,g,h=1,f,d=this;return{postMessage:function(c,b,a){if(b)if(a=a||parent,d.postMessage)a.postMessage(c,b.replace(/([^:]+:\/\/[^\/]+).*/,"$1"));else if(b)a.location=b.replace(/#.*$/,"")+"#"+ +new Date+h++ +"&"+c},receiveMessage:function(c,b){if(d.postMessage)if(c&&(f=function(a){if(typeof b==="string"&&a.origin!==b||Object.prototype.toString.call(b)==="[object Function]"&&b(a.origin)===!1)return!1;c(a)}),d.addEventListener)d[c?"addEventListener":"removeEventListener"]("message",
f,!1);else d[c?"attachEvent":"detachEvent"]("onmessage",f);else e&&clearInterval(e),e=null,c&&(e=setInterval(function(){var a=document.location.hash,b=/^#?\d+&/;a!==g&&b.test(a)&&(g=a,c({data:a.replace(b,"")}))},100))}}}();