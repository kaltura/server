// Prevent the page to be framed
if(kmc.vars.allowFrame == false && top != window) { top.location = window.location; }

/* kmc and kmc.vars defined in script block in kmc4Action.class.php */

// For debug enable to true. Debug will show information in the browser console
kmc.vars.debug = false;

// Quickstart guide (should be moved to kmc4success.php)
kmc.vars.quickstart_guide = "/content/docs/pdf/KMC_User_Manual.pdf";
kmc.vars.help_url = kmc.vars.service_url + '/kmc5help.html';

// Set base URL
kmc.vars.port = (window.location.port) ? ":" + window.location.port : "";
kmc.vars.base_url = window.location.protocol + '//' + window.location.hostname + kmc.vars.port;
kmc.vars.api_url = window.location.protocol + '//' + kmc.vars.host;

// Holds the minimum version for html5 & kdp with the api_v3 for playlists
kmc.vars.min_kdp_version_for_playlist_api_v3 = '3.6.15';
kmc.vars.min_html5_version_for_playlist_api_v3 = '1.7.1.3';

// Log function
kmc.log = function() {
	if( kmc.vars.debug && typeof console !='undefined' && console.log ){
		if (arguments.length == 1) {
			console.log( arguments[0] );
		} else {
			var args = Array.prototype.slice.call(arguments);  
			console.log( args[0], args.slice( 1 ) );
		}
	}	
};

kmc.functions = {

	loadSwf : function() {

		var kmc_swf_url = window.location.protocol + '//' + kmc.vars.cdn_host + '/flash/kmc/' + kmc.vars.kmc_version + '/kmc.swf';

		var flashvars = {
			// kmc configuration
			kmc_uiconf			: kmc.vars.kmc_general_uiconf,

			//permission uiconf id:
			permission_uiconf	: kmc.vars.kmc_permissions_uiconf,

			host				: kmc.vars.host,
			cdnhost				: kmc.vars.cdn_host,
			srvurl				: "api_v3/index.php",
			protocol 			: window.location.protocol + '//',
			partnerid			: kmc.vars.partner_id,
			subpid				: kmc.vars.partner_id + '00',
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
			language			: (kmc.vars.language || "")
		};
		// Disable analytics
		if( kmc.vars.disable_analytics ) {
			flashvars[ 'disableAnalytics' ] = true;
		}
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
			protocol 		: window.location.protocol.slice(0, -1),
			partnerid		: kmc.vars.partner_id,
			subPartnerId	: kmc.vars.partner_id + '00',
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
		
		var iframe_url = kmc.vars.base_url + '/apps/clipapp/' + kmc.vars.clipapp.version;
			iframe_url += '/?kdpUiconf=' + kmc.vars.clipapp.kdp + '&kclipUiconf=' + kmc.vars.clipapp.kclip;
			iframe_url += '&partnerId=' + kmc.vars.partner_id + '&host=' + kmc.vars.host + '&mode=' + mode + '&config=kmc&entryId=' + entry_id;

		var title = ( mode == 'trim' ) ? 'Trimming Tool' : 'Clipping Tool';

		kmc.layout.modal.open( {
			'width' : 950,
			'height' : 616,
			'title'	: title,
			'content' : '<iframe src="' + iframe_url + '" width="100%" height="586" frameborder="0"></iframe>',
			'style'	: 'iframe',
			'closeCallback': function() {
				$("#kcms")[0].gotoPage({
					moduleName: "content",
					subtab: "manage"
				});				
			}
		} );
	},
	flashVarsToString: function( flashVarsObject ) {
		 var params = '';
		 for( var i in flashVarsObject ){
			 // check for object representation of plugin config:
			 if( typeof flashVarsObject[i] == 'object' ){
				 for( var j in flashVarsObject[i] ){
					 params+= '&' + '' + encodeURIComponent( i ) +
					 	'.' + encodeURIComponent( j ) +
					 	'=' + encodeURIComponent( flashVarsObject[i][j] );
				 }
			 } else {
				 params+= '&' + '' + encodeURIComponent( i ) + '=' + encodeURIComponent( flashVarsObject[i] );
			 }
		 }
		 return params;
	 },
	flashVarsToUrl: function( flashVarsObject ){
		 var params = '';
		 for( var i in flashVarsObject ){
			 var curVal = typeof flashVarsObject[i] == 'object'?
					 JSON.stringify( flashVarsObject[i] ):
					 flashVarsObject[i]
			 params+= '&' + 'flashvars[' + encodeURIComponent( i ) + ']=' +
			 	encodeURIComponent(  curVal );
		 }
		 return params;
	},
	versionIsAtLeast: function( minVersion, clientVersion ) {
		if( ! clientVersion ){
			return false;
		}
		var minVersionParts = minVersion.split('.');
		var clientVersionParts = clientVersion.split('.');
		for( var i =0; i < minVersionParts.length; i++ ) {
			if( parseInt( clientVersionParts[i] ) > parseInt( minVersionParts[i] ) ) {
				return true;
			}
			if( parseInt( clientVersionParts[i] ) < parseInt( minVersionParts[i] ) ) {
				return false;
			}
		}
		// Same version:
		return true;
	},
	getVersionFromPath: function( path ) {
		return (typeof path == 'string') ? path.split("/v")[1].split("/")[0] : false;
	}
};

kmc.utils = {
	// Backward compatability
	closeModal : function() {kmc.layout.modal.close();},

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
		if( ! typeof string == 'string' ) {return ;}
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
				$("#flash_wrap object").css("visibility","hidden");
			}
		} else {
			if( $.browser.msie ) {
				$("#flash_wrap").css("margin-right","0");
			} else {
				$("#flash_wrap").css("visibility","visible");
				$("#flash_wrap object").css("visibility","visible");
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
	},
	
	openHelp: function( key ) {
		$("#kcms")[0].doHelp( key );
	}
		
};

kmc.mediator =  {

	writeUrlHash : function(module,subtab){
		location.hash = module + "|" + subtab;
		document.title = "KMC > " + module + ((subtab && subtab != "") ? " > " + subtab + " |" : "");
	},
	readUrlHash : function() {
		var module = "dashboard", 
		subtab = "",
		extra = {};

		try {
			var hash = location.hash.split("#")[1].split("|");
		}
		catch(err) {
			var nohash=true;
		}
		if(!nohash && hash[0]!="") {
			module = hash[0];
			subtab = hash[1];
			
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
	doPreviewEmbed : function(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, is_video) {
		
		var logMsg = 'doPreviewEmbed\n';
		logMsg += 'entry_id: ' + id + '\n';
		logMsg += 'name: ' + name + '\n';
		logMsg += 'description: ' + description + '\n';
		logMsg += 'previewOnly: ' + previewOnly + '\n';
		logMsg += 'is_playlist: ' + is_playlist + '\n';
		logMsg += 'uiconf_id: ' + uiconf_id + '\n';
		logMsg += 'live_bitrates: ' + live_bitrates + '\n';
		kmc.log( logMsg );
		
		description = description || '';

		if(id != "multitab_playlist") {
			//name = (name) ? $('<div />').text( name ).html() : '';
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

		if( live_bitrates ) {kmc.vars.last_delivery_type = "auto";} // Reset delivery type to http

		var https_embed_code = (window.location.protocol == 'https:') ? true : false;
		embed_code = kmc.preview_embed.buildKalturaEmbed(id, name, description, is_playlist, uiconf_id, true, https_embed_code);
		preview_player = embed_code.replace('{FLAVOR}','ks=' + kmc.vars.ks + '&');

		embed_code = kmc.preview_embed.buildKalturaEmbed(id, name, description, is_playlist, uiconf_id);
		embed_code = embed_code.replace('{FLAVOR}','');

		var embedOptions = (previewOnly) ? '' : kmc.preview_embed.buildEmbedOptions(uiconf_details, is_playlist) + kmc.preview_embed.buildHTTPSOption() + '<div class="hr"></div>';
		
		var modal_content = ((live_bitrates) ? kmc.preview_embed.buildLiveBitrates(name,live_bitrates) : '') +
		'<div id="player_wrap">' + preview_player + '</div><div id="preview_embed">' +
		((id == "multitab_playlist") ? '' : kmc.preview_embed.buildSelect(is_playlist, uiconf_id)) +
		((live_bitrates) ? '' : kmc.preview_embed.buildRtmpOptions(uiconf_details, is_playlist)) + embedOptions + 
		kmc.preview_embed.previewUrl(id, name, is_playlist, kmc.vars.partner_id, uiconf_id) + 
		'<div class="item embed_code clearfix"><div class="label">Embed Code</div> <textarea id="embed_code" readonly="true">' + embed_code + '</textarea></div>' +
		'</div><div id="embed_code_button"><div id="copy_msg">Press Ctrl+C to copy embed code (Command+C on Mac)</div>' +
		'<div class="center"><a id="select_code" class="blue_button" href="#">Select Code</a></div></div></div>';

		kmc.layout.modal.open( {
			'width' : parseInt(uiconf_details.width) + 160,
			'title' : id_type + ': ' + name,
			'style' : 'preview_embed',
			'help' : '<a class="help icon" href="javascript:kmc.utils.openHelp(\'section_pne\');"></a>',
			'content' : modal_content
		} );		

		// attach events here instead of writing them inline
		$("#embed_code, #select_code").click(function( e ){
			e.preventDefault();
			$("#copy_msg").show();
			setTimeout(function(){
				$("#copy_msg").hide(500);
			},1500);
			$("textarea#embed_code").select();
		});

		$("#delivery_type").change(function(){
			kmc.vars.last_delivery_type = this.value;
			kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, is_video);
		});
		$('#embed_types').change(function(){
			kmc.vars.last_embed_code_type = this.value;
			kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, is_video);
		});
		$("#player_select").change(function(){
			kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, this.value, live_bitrates, entry_flavors, is_video);
		});
		
		$("#https_support").change(function(){
			// Update short link
			kmc.preview_embed.previewUrl(id, name, is_playlist, kmc.vars.partner_id, uiconf_id);
			// Update embed code
			var val = kmc.preview_embed.buildKalturaEmbed(id,name,description, is_playlist, uiconf_id);
			$("#embed_code").val(val);
		});

		// Set default value for HTTPS checkbox
		if( kmc.vars.embed_code_protocol_https ) {
			$("#https_support").attr('checked','checked').trigger('change');
		}
			
		// show the embed code & enable the checkbox if its not a preview
		if (previewOnly==false) {
			$('.embed_code, #embed_code_button').show();
		}
	}, // doPreviewEmbed

	getDeliveryTypeFlashvars: function( typeId ) {
		var fv = {};
		$.each(kmc.vars.delivery_types, function(id, item) {
			if( typeId == id ) {
				fv = item.flashvars || {};
				// Add streamerType and mediaProtocol
				if(item.streamerType)
					fv.streamerType = item.streamerType;
				if(item.mediaProtocol)
					fv.mediaProtocol = item.mediaProtocol;

				return false;
			}
		});
		return fv;
	},

	getDefaultDeliveryType: function( uiconf, is_playlist ) {
		var types = kmc.preview_embed.getValidDeliveryTypes(uiconf, is_playlist);

		var defaultType = 'http';
		if( kmc.vars.last_delivery_type ) {
			return kmc.vars.last_delivery_type;
		} else {
			$.each(types, function(id, item) {
				if(id == kmc.vars.default_delivery_type){
					defaultType = id;
					return false;
				}
			});
		}
		return defaultType;
	},

	getValidDeliveryTypes: function( uiconf, is_playlist ) {
		// Get uiConf by Id
		if( typeof uiconf !== 'object' ) {
			uiconf = kmc.preview_embed.getUiconfDetails(uiconf,is_playlist);
		}

		var clearLastDeliveryType = function( id ) {
			if( kmc.vars.last_delivery_type == id ) { 
				kmc.vars.last_delivery_type = null;
			}
		};

		var validated = {};

		$.each(kmc.vars.delivery_types, function(id, item) {
			var swfVersion = uiconf.swf_version;
			if( item.minVersion && ! kmc.functions.versionIsAtLeast(item.minVersion, swfVersion) ) {
				clearLastDeliveryType(id);
				return true;
			}
			validated[ id ] = item;
		});
		return validated;
	},

	getDefaultEmbedType: function(uiconf, is_playlist) {
		var types = kmc.preview_embed.getValidEmbedTypes(uiconf, is_playlist);

		var defaultType = 'legacy';
		if( kmc.vars.last_embed_code_type ) {
			return kmc.vars.last_embed_code_type;
		} else {
			$.each(types, function(id, item) {
				if(id == kmc.vars.default_embed_code_type){
					defaultType = id;
					return false;
				}
			});
		}
		return defaultType;
	},

	getValidEmbedTypes: function( uiconf, is_playlist ) {
		// Get uiConf by Id
		if( typeof uiconf !== 'object' ) {
			uiconf = kmc.preview_embed.getUiconfDetails(uiconf,is_playlist);
		}

		var clearLastEmbedType = function( id ) {
			if( kmc.vars.last_embed_code_type == id ) { 
				kmc.vars.last_embed_code_type = null;
			}
		};

		var validated = {};

		// Go over embed code types
		$.each(kmc.vars.embed_code_types, function(id, item){
			// Ignore iframe embed
			if( id == 'iframe' ) {
				return true;
			}
			// Don't add embed code that are entry only for playlists
			if(is_playlist && this.entryOnly) {
				clearLastEmbedType(id);
				return true;
			}
			// Check for library minimum version to eanble embed type
			var libVersion = kmc.functions.getVersionFromPath(uiconf.html5Url);
			if( item.minVersion && ! kmc.functions.versionIsAtLeast(item.minVersion, libVersion) ) {
				clearLastEmbedType(id);
				return true;
			}
			validated[ id ] = item;
		});
		return validated;
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
		html_select = '<div class="clearfix"><div class="label" style="min-width: 140px;">Select Player:</div><select id="player_select">' + html_select + '</select></div>';
		html_select += '<div class="note">Kaltura player includes both layout and functionality (advertising, subtitles, etc)</div>';
		kmc.vars.current_uiconf = null;
		return '<div class="item">' + html_select + '</div>';
	},
	
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

	buildRtmpOptions : function(uiconf, is_playlist) {
		var selected = ' selected="selected"';
		var delivery_type = kmc.preview_embed.getDefaultDeliveryType(uiconf, is_playlist);
		var html = '<div class="clearfix"><div id="rtmp" class="label">Select Delivery Type:</div> <select id="delivery_type">';
		var options = '';

		$.each(kmc.preview_embed.getValidDeliveryTypes(uiconf, is_playlist), function(id, item){
			var selected = (delivery_type == id) ? 'selected="selected"' : '';
			options += '<option value="' + id + '"' + selected + '>' + item.label + '</option>';
		});

		html += options + '</select></div><div class="note">Adaptive Streaming automatically adjusts to the viewer\'s bandwidth,' +
		'while Progressive Download allows buffering of the content. <a href="javascript:kmc.utils.openHelp(\'section_pne_stream\');">Read more</a></div>';
		return '<div class="item">' + html + '</div>';
	},

	buildEmbedOptions: function(uiconf, is_playlist) {
		var embed_type = kmc.preview_embed.getDefaultEmbedType(uiconf, is_playlist);
		var html = '<div class="clearfix"><div id="embedtypes" class="label">Select Embed Code Type:</div> <select id="embed_types">';
		var options = '';
		// Go over embed code types
		$.each(kmc.preview_embed.getValidEmbedTypes(uiconf, is_playlist), function(id, item){
			var selected = (embed_type == id) ? 'selected="selected"' : '';
			options += '<option value="' + id + '"' + selected + '>' + item.label + '</option>';
		});
		html += options + '</select></div><div class="note">Auto embed is the default embed code type and is best to get a player quickly on a page without any runtime customizations. <a href="javascript:kmc.utils.openHelp(\'section_pne_embed\');">Read more</a> about the different embed code types.</div>';
		return '<div class="item">' + html + '</div>';
	},
	
	buildHTTPSOption: function() {
		return '<div class="item clearfix"><div class="label checkbox"><input id="https_support" type="checkbox" /> <label class="label_text" for="https_support">' + 
				'Modify embed code to use HTTPS secure delivery</label></div></div>';
	},
	
	previewUrl: function(entry_id, name, is_playlist, partner_id, uiconf_id){
		var embed_type = kmc.preview_embed.getDefaultEmbedType(uiconf_id, is_playlist);
		var delivery_type = kmc.preview_embed.getDefaultDeliveryType(uiconf_id, is_playlist);
		var update_html = '<img src="/lib/images/kmc/url_loader.gif" alt="loading..." /> Updating Short URL...';
		if( $(".preview_url").length ) {
			$(".preview_url").html( update_html );
		}
		// Base preview url
		var protocol = ($("#https_support").attr("checked")) ? 'https://' : 'http://';
		var flashVars = kmc.preview_embed.getEmbedFlashVars(entry_id, name, is_playlist, uiconf_id, delivery_type, $("#https_support").attr("checked"));
		var long_url = protocol + window.location.hostname + '/index.php/kmc/preview/partner_id/' + partner_id + '/uiconf_id/' + uiconf_id;
		if( !is_playlist ) {
			long_url += '/entry_id/' + entry_id;
		}
		long_url += '/embed/' + embed_type + '?' + kmc.functions.flashVarsToUrl(flashVars);

		kmc.client.setShortURL(long_url);
		
		return '<div class="item preview_link"><div class="label_text">View a standalone page with this player: &nbsp;<span class="preview_url">' + update_html + '</span></div></div>';
	},

	// for content|Manage->drilldown->flavors->preview
	// flavor_details = json:
	doFlavorPreview : function(entry_id, entry_name, flavor_details) {

		var https_embed_code = (window.location.protocol == 'https:') ? true : false;
		var player_code = kmc.preview_embed.buildKalturaEmbed(entry_id,entry_name,null,false,kmc.vars.default_kdp, true, https_embed_code);
		player_code = player_code.replace('&{FLAVOR}', '&flavorId=' + flavor_details.asset_id + '&ks=' + kmc.vars.ks);

		var modal_content = '<div class="center">' + player_code + '</div><dl>' +
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
			'width' : parseInt(kmc.vars.default_kdp.width) + 120,
			'height' : parseInt(kmc.vars.default_kdp.height) + 300,
			'title' : 'Flavor Preview',
			'content' : '<div id="preview_embed">' + modal_content + '</div>'
		} );

	},

	// eventually replace with <? php echo $embedCodeTemplate; ?>  ;  (variables used: HEIGHT WIDTH HOST CACHE_ST UICONF_ID PARTNER_ID PLAYLIST_ID ENTRY_ID) + {VER}, {SILVERLIGHT}, {INIT_PARAMS} for Silverlight + NAME, DESCRIPTION
	embed_code_template :	{
		object_tag :	'<object id="kaltura_player_{CACHE_ST}" name="kaltura_player_{CACHE_ST}" type="application/x-shockwave-flash" allowFullScreen="true" ' +
		'allowNetworking="all" allowScriptAccess="always" height="{HEIGHT}" width="{WIDTH}" bgcolor="#000000" {SEO_ATTS}' +
		'data="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}">' +
		'<param name="allowFullScreen" value="true" /><param name="allowNetworking" value="all" />' +
		'<param name="allowScriptAccess" value="always" /><param name="bgcolor" value="#000000" />' +
		'<param name="flashVars" value="{FLASHVARS}&{FLAVOR}" /><param name="movie" value="http://{HOST}/index.php/kwidget' +
		'/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" />{ALT} {SEO} ' + '</object>',
		script_tag :	'<script type="text/javascript" src="{SCRIPT_URL}"></script>',
		div_tag: 		'<div id="kaltura_player_{CACHE_ST}" style="width: {WIDTH}px; height: {HEIGHT}px"></div>',
		kwidget_object: '<script type="text/javascript">kWidget.embed({EMBED_OBJECT});</script>',
		thumb_embed: 	'<script type="text/javascript">kWidget.thumbEmbed({EMBED_OBJECT});</script>',
		kaltura_links :		'<a href="http://corp.kaltura.com/products/video-platform-features">Video Platform</a> <a href="http://corp.kaltura.com/Products/Features/Video-Management">' +
		'Video Management</a> <a href="http://corp.kaltura.com/Video-Solutions">Video Solutions</a> ' +
		'<a href="http://corp.kaltura.com/Products/Features/Video-Player">Video Player</a>',
		media_seo_info :	'<a rel="media:thumbnail" href="http://{CDN_HOST}/p/{PARTNER_ID}/sp/{PARTNER_ID}00/thumbnail{ENTRY_ID}/width/120/height/90/bgcolor/000000/type/2"></a> ' +
		'<span property="dc:description" content="{DESCRIPTION}"></span><span property="media:title" content="{NAME}"></span> ' +
		'<span property="media:width" content="{WIDTH}"></span><span property="media:height" content="{HEIGHT}"></span> ' +
		'<span property="media:type" content="application/x-shockwave-flash"></span>',
		media_seo_atts: 'xmlns:dc="http://purl.org/dc/terms/" xmlns:media="http://search.yahoo.com/searchmonkey/media/" rel="media:video" ' +
		'resource="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" '
	},

	getEmbedFlashVars: function(id, name, is_playlist, uiconf_id, delivery_type, secured) {
		var uiconf_details = (typeof uiconf_id == "object") ? uiconf_id : kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist);
		var protocol = (secured) ? 'https' : 'http';
		var embed_host = (secured) ? kmc.vars.embed_host_https : kmc.vars.embed_host;

		var flashVars = $.extend({}, kmc.preview_embed.getDeliveryTypeFlashvars( delivery_type ));
		if(is_playlist && id != "multitab_playlist") {
			// Use new kpl0Id flashvar for new players only
			var html5_version = kmc.functions.getVersionFromPath(uiconf_details.html5Url);
			if( kmc.functions.versionIsAtLeast(kmc.vars.min_kdp_version_for_playlist_api_v3, uiconf_details.swf_version) && 
				kmc.functions.versionIsAtLeast(kmc.vars.min_html5_version_for_playlist_api_v3, html5_version) ) {
				flashVars['playlistAPI.kpl0Id'] = id;
			} else {
				flashVars['playlistAPI.autoInsert'] = 'true';
				flashVars['playlistAPI.kpl0Name'] = name;
				flashVars['playlistAPI.kpl0Url'] = protocol + '://' + embed_host + '/index.php/partnerservices2/executeplaylist?' + 
													'partner_id=' + kmc.vars.partner_id + '&subp_id=' + kmc.vars.partner_id + '00' + 
													'&format=8&ks={ks}&playlist_id=' + id;
			}
		}
		return flashVars;
	},

	// id = entry id, asset id or playlist id; name = entry name or playlist name;
	// uiconf = uiconfid (normal scenario) or uiconf details json (for #content|Manage->drill down->flavors->preview)
	buildKalturaEmbed : function(id, name, description, is_playlist, uiconf, previewPlayer, secured ) {
		
		var https_support = ($("#https_support").attr("checked")) ? true : false;
		https_support = (secured) ? secured : https_support;

		name = kmc.utils.escapeQuotes(name); 
		var uiconf_id = uiconf.id || uiconf,
		uiconf_details = (typeof uiconf == "object") ? uiconf : kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist),  // getUiconfDetails returns json
		cache_st = kmc.preview_embed.setCacheStartTime(),
		embed_code, flashVars = {};

		var embed_host = (https_support) ? kmc.vars.embed_host_https : kmc.vars.embed_host;

		if( previewPlayer ) {
			embed_code = kmc.preview_embed.embed_code_template.object_tag;
		} else {
			embed_code = kmc.preview_embed.getEmbedCode( id, name, description, is_playlist, uiconf_details );
		}
		// Add SEO Atts
		embed_code = embed_code.replace("{SEO_ATTS}", (kmc.vars.ignore_entry_seo ? "" : kmc.preview_embed.embed_code_template.media_seo_atts));

		var delivery_type = kmc.preview_embed.getDefaultDeliveryType(uiconf_details, is_playlist);
		var flashVars = kmc.preview_embed.getEmbedFlashVars(id, name, is_playlist, uiconf_details, delivery_type, https_support);

		if(is_playlist && id != "multitab_playlist") {	// playlist (not multitab)
			embed_code = embed_code.replace(/{ENTRY_ID}/g,"");
			embed_code = embed_code.replace("{SEO}", "");
		}
		else {											// player and multitab playlist
			embed_code = embed_code.replace("{SEO}", (kmc.vars.ignore_entry_seo ? "" : kmc.preview_embed.embed_code_template.media_seo_info));
			embed_code = embed_code.replace(/{ENTRY_ID}/g, (is_playlist ? "" : "/entry_id/" + id));
		}
		// Change flashvars object to string and replace within the embed code
		embed_code = embed_code.replace('{FLASHVARS}', kmc.functions.flashVarsToString(flashVars));	
		embed_code = embed_code.replace('{FLASHVARS_URL}', kmc.functions.flashVarsToUrl(flashVars));	

		var script_url = 'http://' + embed_host + '/p/'+ kmc.vars.partner_id + '/sp/' + kmc.vars.partner_id + '00/embedIframeJs/uiconf_id/' + uiconf_id + '/partner_id/' + kmc.vars.partner_id;

		// Used by kWidget.embed
		var embedObject = {
			targetId: 'kaltura_player_' + cache_st,
			cache_st: cache_st,			
			wid: '_' + kmc.vars.partner_id,
			uiconf_id: uiconf_id,
			flashvars: flashVars
		};
		// Add entry id if not a playlist
		if(!is_playlist) {
			embedObject['entry_id'] = id;
		}
			
		embed_code = embed_code.replace(/{HEIGHT}/gi,uiconf_details.height);
		embed_code = embed_code.replace(/{WIDTH}/gi,uiconf_details.width);
		embed_code = embed_code.replace(/{HOST}/gi,embed_host);		
		embed_code = embed_code.replace(/{CACHE_ST}/gi,cache_st);
		embed_code = embed_code.replace(/{UICONF_ID}/gi,uiconf_id);
		embed_code = embed_code.replace(/{PARTNER_ID}/gi,kmc.vars.partner_id);
		embed_code = embed_code.replace(/{SERVICE_URL}/gi,kmc.vars.service_url);
		embed_code = embed_code.replace("{ALT}", ((kmc.vars.whitelabel || kmc.vars.ignore_seo_links) ? "" : kmc.preview_embed.embed_code_template.kaltura_links));
		embed_code = embed_code.replace("{CDN_HOST}",kmc.vars.cdn_host);
		embed_code = embed_code.replace("{NAME}", name);
		embed_code = embed_code.replace("{DESCRIPTION}", description);
		embed_code = embed_code.replace("{SCRIPT_URL}", script_url); 
		embed_code = embed_code.replace("{EMBED_OBJECT}", JSON.stringify(embedObject, null, 2));
		
		if( https_support ) {
			embed_code = embed_code.replace(/http:/g, "https:");
		} else {			
			embed_code = embed_code.replace(/https:/g, "http:");
		}

		return embed_code;
	},

	getEmbedCode: function( id, name, description, is_playlist, uiconf ) {
		var embed_type = kmc.preview_embed.getDefaultEmbedType(uiconf, is_playlist), 
			uiconf_id = uiconf.id || uiconf,
			code = '',
			entry_param = (!is_playlist) ? 'entry_id=' + id : '';
		switch( embed_type ) {
			case 'auto':
				code = '<script type="text/javascript" src="{SCRIPT_URL}?' + entry_param + 
						'&playerId=kaltura_player_{CACHE_ST}&cache_st={CACHE_ST}' + 
						'&autoembed=true&width={WIDTH}&height={HEIGHT}&{FLASHVARS_URL}"></script>';
			break;
			case 'dynamic':
				code = kmc.preview_embed.embed_code_template.div_tag + '\n' + 
						kmc.preview_embed.embed_code_template.script_tag + '\n' + 
						kmc.preview_embed.embed_code_template.kwidget_object;
			break;
			case 'thumb':
				code = kmc.preview_embed.embed_code_template.div_tag + '\n' + 
						kmc.preview_embed.embed_code_template.script_tag + '\n' + 
						kmc.preview_embed.embed_code_template.thumb_embed;
			break;
			default:
				code = kmc.preview_embed.embed_code_template.script_tag + '\n' + 
						kmc.preview_embed.embed_code_template.object_tag;
			break;
		};
		return code;
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
			url: kmc.vars.base_url + kmc.vars.getuiconfs_url,
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
		kmc.log('PreviewEmbed: setShortURL');
		var url = kmc.vars.service_url + '/tiny/' + id;
		//var url_text = url.replace(/http:\/\/|www./ig, '');
		var url_text = url.replace(/http:\/\//ig, '');
			
		var html = '<a href="' + url + '" target="_blank">' + url_text + '</a>';
		$(".preview_url").html(html);
	}
};

kmc.client = {
	makeRequest: function( service, action, params, callback ) {
		var serviceUrl = kmc.vars.api_url + '/api_v3/index.php?service='+service+'&action='+action;
		var defaultParams = {
			"ks"		: kmc.vars.ks,
			"format"	: 9
		};
		// Merge params and defaults
		$.extend( params, defaultParams);
		
		var ksort = function ( arr ) {
			var sArr = [];
			var tArr = [];
			var n = 0;
			for ( i in arr ){
				tArr[n++] = i+"|"+arr[i];
			}
			tArr = tArr.sort();
			for (var i=0; i<tArr.length; i++) {
				var x = tArr[i].split("|");
				sArr[x[0]] = x[1];
			}
			return sArr;
		};
		
		var getSignature = function( params ){
			params = ksort(params);
			var str = "";
			for(var v in params) {
				var k = params[v];
				str += k + v;
			}
			return md5(str);
		};
		
		// Add kaltura signature param
		var kalsig = getSignature( params );
		serviceUrl += '&kalsig=' + kalsig;

		// Make request
		$.ajax({
			type: 'GET',
			url: serviceUrl, 
			dataType: 'jsonp',
			data: params, 
			cache: false,
			success: callback
		});	
	},
		
	// Get the Short URL code
	setShortURL : function(url) {
		kmc.log( 'setShortURL' );
		
		var filter = {
			"filter:objectType"		: "KalturaShortLinkFilter",
			"filter:statusEqual"	: 2,
			"filter:systemNameEqual": "KMC-PREVIEW"
		};
		
		kmc.client.makeRequest("shortlink_shortlink", "list", filter, function( res ) {
			if(res && res.totalCount == 0) {
				// if no url were found, create a new one
				kmc.client.createShortURL(url);
			} else {
				// update the url
				var id = res.objects[0].id;
				var res_url = res.objects[0].fullUrl;
				if(url == res_url) {
					kmc.preview_embed.setShortURL(id);
				} else {
					kmc.client.updateShortURL(url, id);
				}
			}			
		});
	},
		
	createShortURL : function(url) {
		kmc.log('createShortURL');
			
		var data = {
			"shortLink:objectType"	: "KalturaShortLink",
			"shortLink:systemName"	: "KMC-PREVIEW", // Unique name for filtering
			"shortLink:fullUrl"		: url
		};
			
		kmc.client.makeRequest("shortlink_shortlink", "add", data, function( res ) {
			kmc.preview_embed.setShortURL(res.id);
		});
	},
		
	updateShortURL : function(url, id) {
		kmc.log('updateShortURL');
			
		var data = {
			"id"					: id,
			"shortLink:objectType"	: "KalturaShortLink",
			"shortLink:fullUrl"		: url
		};
			
		kmc.client.makeRequest("shortlink_shortlink", "update", data, function( res ) {
			kmc.preview_embed.setShortURL(res.id);
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

// Auto resize modal windows
$(window).resize(function() {
		// Exit if not open
	if( kmc.layout.modal.isOpen() ) {
		kmc.layout.modal.position();
	}
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
		$("body").append('<div id="mask"></div><div id="overlay"></div><div id="modal" class="modal"><div class="title"><h2></h2><span class="close icon"></span></div><div class="content"></div></div>');
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

			options.style = options.style + ' modal';

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
			$modal_content[0].innerHTML = options.content;

			// Activate close button
			$modal.find(".close").click( function() {
				kmc.layout.modal.close();
				if( $.isFunction( data.closeCallback ) ) {
					data.closeCallback();
				}
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

	openSupport: function(el) {
		var href = el.href;
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
			if( ! kmc.vars.support_frame_height ) {
				kmc.vars.support_frame_height = $("#support")[0].contentWindow.document.body.scrollHeight;
			}
			$("#support").height( kmc.vars.support_frame_height );
			// Re-position the modal box
			kmc.layout.modal.position();
		});
	},

	logout: function() {
		var message = kmc.functions.checkForOngoingProcess();
		if( message ) {alert( message );return false;}
		var state = kmc.mediator.readUrlHash();
		// Cookies are HTTP only, we delete them using logoutAction
		$.ajax({
			url: kmc.vars.base_url + "/index.php/kmc/logout",
			type: "POST",
			data: {
				"ks": kmc.vars.ks
				},
			dataType: "json",
			complete: function() {
				if (kmc.vars.logoutUrl)
					window.location = kmc.vars.logoutUrl;
				else
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
		var http_protocol = (kmc.vars.kmc_secured || location.protocol == 'https:') ? 'https' : 'http';
		var from_domain = http_protocol + '://' + window.location.hostname;
		var url = from_domain + kmc.vars.port + "/index.php/kmc/updateLoginData/type/" + action;

		// pass the parent url for the postMessage to work
		url = url + '?parent=' + encodeURIComponent(document.location.href);

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

			var url = kmc.vars.base_url + '/index.php/kmc/extlogin';

			// Setup input fields
			var ks_input = $('<input />').attr({
				'type': 'hidden',
				'name': 'ks',
				'value': kmc.vars.ks
			});
			var partner_id_input = $('<input />').attr({
				'type': 'hidden',
				'name': 'partner_id',
				'value': $('input[name=pid]:radio:checked').val() // grab the selected partner id
			});

			var $form = $('<form />')
						.attr({
							'action': url, 
							'method': 'post',
							'style': 'display: none'
						})
						.append( ks_input, partner_id_input );

			// Submit the form
			$('body').append( $form );
			$form[0].submit();
		});

		return false;
	}
};

/* WResize: plugin for fixing the IE window resize bug (http://noteslog.com/) */
(function($){$.fn.wresize=function(f){version='1.1';wresize={fired:false,width:0};function resizeOnce(){if($.browser.msie){if(!wresize.fired){wresize.fired=true}else{var version=parseInt($.browser.version,10);wresize.fired=false;if(version<7){return false}else if(version==7){var width=$(window).width();if(width!=wresize.width){wresize.width=width;return false}}}}return true}function handleWResize(e){if(resizeOnce()){return f.apply(this,[e])}}this.each(function(){if(this==window){$(this).resize(handleWResize)}else{$(this).resize(f)}});return this}})(jQuery);

/* XD: a backwards compatable implementation of postMessage (http://www.onlineaspect.com/2010/01/15/backwards-compatible-postmessage/) */
var XD=function(){var e,g,h=1,f,d=this;return{postMessage:function(c,b,a){if(b)if(a=a||parent,d.postMessage)a.postMessage(c,b.replace(/([^:]+:\/\/[^\/]+).*/,"$1"));else if(b)a.location=b.replace(/#.*$/,"")+"#"+ +new Date+h++ +"&"+c},receiveMessage:function(c,b){if(d.postMessage)if(c&&(f=function(a){if(typeof b==="string"&&a.origin!==b||Object.prototype.toString.call(b)==="[object Function]"&&b(a.origin)===!1)return!1;c(a)}),d.addEventListener)d[c?"addEventListener":"removeEventListener"]("message",
f,!1);else d[c?"attachEvent":"detachEvent"]("onmessage",f);else e&&clearInterval(e),e=null,c&&(e=setInterval(function(){var a=document.location.hash,b=/^#?\d+&/;a!==g&&b.test(a)&&(g=a,c({data:a.replace(b,"")}))},100))}}}();

/* md5 and utf8_encode from phpjs.org */
function md5(str){var xl;var rotateLeft=function(lValue,iShiftBits){return(lValue<<iShiftBits)|(lValue>>>(32-iShiftBits));};var addUnsigned=function(lX,lY){var lX4,lY4,lX8,lY8,lResult;lX8=(lX&0x80000000);lY8=(lY&0x80000000);lX4=(lX&0x40000000);lY4=(lY&0x40000000);lResult=(lX&0x3FFFFFFF)+(lY&0x3FFFFFFF);if(lX4&lY4){return(lResult^0x80000000^lX8^lY8);}
if(lX4|lY4){if(lResult&0x40000000){return(lResult^0xC0000000^lX8^lY8);}else{return(lResult^0x40000000^lX8^lY8);}}else{return(lResult^lX8^lY8);}};var _F=function(x,y,z){return(x&y)|((~x)&z);};var _G=function(x,y,z){return(x&z)|(y&(~z));};var _H=function(x,y,z){return(x^y^z);};var _I=function(x,y,z){return(y^(x|(~z)));};var _FF=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_F(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _GG=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_G(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _HH=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_H(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _II=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_I(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var convertToWordArray=function(str){var lWordCount;var lMessageLength=str.length;var lNumberOfWords_temp1=lMessageLength+8;var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1%64))/64;var lNumberOfWords=(lNumberOfWords_temp2+1)*16;var lWordArray=new Array(lNumberOfWords-1);var lBytePosition=0;var lByteCount=0;while(lByteCount<lMessageLength){lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=(lWordArray[lWordCount]|(str.charCodeAt(lByteCount)<<lBytePosition));lByteCount++;}
lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=lWordArray[lWordCount]|(0x80<<lBytePosition);lWordArray[lNumberOfWords-2]=lMessageLength<<3;lWordArray[lNumberOfWords-1]=lMessageLength>>>29;return lWordArray;};var wordToHex=function(lValue){var wordToHexValue="",wordToHexValue_temp="",lByte,lCount;for(lCount=0;lCount<=3;lCount++){lByte=(lValue>>>(lCount*8))&255;wordToHexValue_temp="0"+lByte.toString(16);wordToHexValue=wordToHexValue+wordToHexValue_temp.substr(wordToHexValue_temp.length-2,2);}
return wordToHexValue;};var x=[],k,AA,BB,CC,DD,a,b,c,d,S11=7,S12=12,S13=17,S14=22,S21=5,S22=9,S23=14,S24=20,S31=4,S32=11,S33=16,S34=23,S41=6,S42=10,S43=15,S44=21;str=this.utf8_encode(str);x=convertToWordArray(str);a=0x67452301;b=0xEFCDAB89;c=0x98BADCFE;d=0x10325476;xl=x.length;for(k=0;k<xl;k+=16){AA=a;BB=b;CC=c;DD=d;a=_FF(a,b,c,d,x[k+0],S11,0xD76AA478);d=_FF(d,a,b,c,x[k+1],S12,0xE8C7B756);c=_FF(c,d,a,b,x[k+2],S13,0x242070DB);b=_FF(b,c,d,a,x[k+3],S14,0xC1BDCEEE);a=_FF(a,b,c,d,x[k+4],S11,0xF57C0FAF);d=_FF(d,a,b,c,x[k+5],S12,0x4787C62A);c=_FF(c,d,a,b,x[k+6],S13,0xA8304613);b=_FF(b,c,d,a,x[k+7],S14,0xFD469501);a=_FF(a,b,c,d,x[k+8],S11,0x698098D8);d=_FF(d,a,b,c,x[k+9],S12,0x8B44F7AF);c=_FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);b=_FF(b,c,d,a,x[k+11],S14,0x895CD7BE);a=_FF(a,b,c,d,x[k+12],S11,0x6B901122);d=_FF(d,a,b,c,x[k+13],S12,0xFD987193);c=_FF(c,d,a,b,x[k+14],S13,0xA679438E);b=_FF(b,c,d,a,x[k+15],S14,0x49B40821);a=_GG(a,b,c,d,x[k+1],S21,0xF61E2562);d=_GG(d,a,b,c,x[k+6],S22,0xC040B340);c=_GG(c,d,a,b,x[k+11],S23,0x265E5A51);b=_GG(b,c,d,a,x[k+0],S24,0xE9B6C7AA);a=_GG(a,b,c,d,x[k+5],S21,0xD62F105D);d=_GG(d,a,b,c,x[k+10],S22,0x2441453);c=_GG(c,d,a,b,x[k+15],S23,0xD8A1E681);b=_GG(b,c,d,a,x[k+4],S24,0xE7D3FBC8);a=_GG(a,b,c,d,x[k+9],S21,0x21E1CDE6);d=_GG(d,a,b,c,x[k+14],S22,0xC33707D6);c=_GG(c,d,a,b,x[k+3],S23,0xF4D50D87);b=_GG(b,c,d,a,x[k+8],S24,0x455A14ED);a=_GG(a,b,c,d,x[k+13],S21,0xA9E3E905);d=_GG(d,a,b,c,x[k+2],S22,0xFCEFA3F8);c=_GG(c,d,a,b,x[k+7],S23,0x676F02D9);b=_GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);a=_HH(a,b,c,d,x[k+5],S31,0xFFFA3942);d=_HH(d,a,b,c,x[k+8],S32,0x8771F681);c=_HH(c,d,a,b,x[k+11],S33,0x6D9D6122);b=_HH(b,c,d,a,x[k+14],S34,0xFDE5380C);a=_HH(a,b,c,d,x[k+1],S31,0xA4BEEA44);d=_HH(d,a,b,c,x[k+4],S32,0x4BDECFA9);c=_HH(c,d,a,b,x[k+7],S33,0xF6BB4B60);b=_HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);a=_HH(a,b,c,d,x[k+13],S31,0x289B7EC6);d=_HH(d,a,b,c,x[k+0],S32,0xEAA127FA);c=_HH(c,d,a,b,x[k+3],S33,0xD4EF3085);b=_HH(b,c,d,a,x[k+6],S34,0x4881D05);a=_HH(a,b,c,d,x[k+9],S31,0xD9D4D039);d=_HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);c=_HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);b=_HH(b,c,d,a,x[k+2],S34,0xC4AC5665);a=_II(a,b,c,d,x[k+0],S41,0xF4292244);d=_II(d,a,b,c,x[k+7],S42,0x432AFF97);c=_II(c,d,a,b,x[k+14],S43,0xAB9423A7);b=_II(b,c,d,a,x[k+5],S44,0xFC93A039);a=_II(a,b,c,d,x[k+12],S41,0x655B59C3);d=_II(d,a,b,c,x[k+3],S42,0x8F0CCC92);c=_II(c,d,a,b,x[k+10],S43,0xFFEFF47D);b=_II(b,c,d,a,x[k+1],S44,0x85845DD1);a=_II(a,b,c,d,x[k+8],S41,0x6FA87E4F);d=_II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);c=_II(c,d,a,b,x[k+6],S43,0xA3014314);b=_II(b,c,d,a,x[k+13],S44,0x4E0811A1);a=_II(a,b,c,d,x[k+4],S41,0xF7537E82);d=_II(d,a,b,c,x[k+11],S42,0xBD3AF235);c=_II(c,d,a,b,x[k+2],S43,0x2AD7D2BB);b=_II(b,c,d,a,x[k+9],S44,0xEB86D391);a=addUnsigned(a,AA);b=addUnsigned(b,BB);c=addUnsigned(c,CC);d=addUnsigned(d,DD);}
var temp=wordToHex(a)+wordToHex(b)+wordToHex(c)+wordToHex(d);return temp.toLowerCase();}
function utf8_encode(argString){if(argString===null||typeof argString==="undefined"){return"";}
var string=(argString+'');var utftext="",start,end,stringl=0;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++;}else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128);}else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128);}
if(enc!==null){if(end>start){utftext+=string.slice(start,end);}
utftext+=enc;start=end=n+1;}}
if(end>start){utftext+=string.slice(start,stringl);}
return utftext;}