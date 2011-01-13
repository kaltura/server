
//alert("service_url="+kmc.vars.service_url +"\n host=" + kmc.vars.host + "\n cdn_host=" + kmc.vars.cdn_host + " \n flash_dir=" + kmc.vars.flash_dir);
// @todo:
//		* Must do / should do:
//			* Graphics:
//				* overlay
//				* replace inline styles with classes
//			* Implement embed code template from server
//			* logout() shouldn't have to location.href to '/index.php/kmc/' (should already be ok on prod)
// *		* Fix uiconf names ("Kaltura", jw playlist names) (Gonen)
//
//		* Maybe / not that important / takes too long - pushed to Blackeye:
//			* why is srvurl flashvar hardcoded to "api_v3/index.php" (why not from kconf.php)
//			* organize kmcSuccess
//			* remove openPlayer/ openPlaylist flashvars for 2.0.9
//			* move some jw code into own sub-object (doJw)
//			* memory profiling
//				* nullify preview players
//				* kill swf's - profiling
//			* understand setObjectToRemove and use or remove
//			* move cookie functions to kmc.utils
//			* get rid of legacy functions
//			* a few leftover @todo's inside code
//			* deactivate header on openning of flash modal (Eitan)
//			* In p&e, if mix, show only message box for jw (Yaron)
//			* Full copy to clipboard
//			* Flavors preview to display based on flavor size with logic for not exceeding available screen area
 
/* WResize: plugin for fixing the IE window resize bug (http://noteslog.com/) */
(function($){$.fn.wresize=function(f){version='1.1';wresize={fired:false,width:0};function resizeOnce(){if($.browser.msie){if(!wresize.fired){wresize.fired=true}else{var version=parseInt($.browser.version,10);wresize.fired=false;if(version<7){return false}else if(version==7){var width=$(window).width();if(width!=wresize.width){wresize.width=width;return false}}}}return true}function handleWResize(e){if(resizeOnce()){return f.apply(this,[e])}}this.each(function(){if(this==window){$(this).resize(handleWResize)}else{$(this).resize(f)}});return this}})(jQuery);

$(window).load(function(){
//	$("#kcms")[0].gotoPage(kmc.mediator.readUrlHash());
	//alert('First Loading');
	//kmc.utils.activateHeader(true);
 
	$(window).wresize(kmc.utils.resize);
	kmc.vars.isLoadedInterval = setInterval("kmc.utils.isModuleLoaded()",200);
});

/* kmc and kmc.vars defined in script block in kmc2success.php */

	// kmc.vars.quickstart_guide = "/content/docs/pdf/KMC_Quick_Start_Guide__Butterfly.pdf#";
	kmc.vars.quickstart_guide = "/content/docs/pdf/KMC3_Quick_Start_Guide.pdf#"; // cassiopea

	kmc.functions = {
		expired : function() {
			// @todo: why no cookie killing ?
			window.location = kmc.vars.service_url + "/index.php/kmc/kmc" + location.hash; // @todo: shouldn't require '/index.php/kmc/'
		},
		doNothing : function() {
			return false;
		},
		closeEditor : function(is_modified) { // KSE
			if(is_modified) {
				var myConfirm = confirm("Exit without saving?\n\n - Click [OK] to close editor\n\n - Click [Cancel] to remain in editor\n\n");
				if(!myConfirm) {
					return;
				}
			}
			document.getElementById("flash_wrap").style.visibility = "visible";
			kalturaCloseModalBox();
		},
		saveEditor : function() { // KSE
			return;
		},
		openKcw : function(ks, conversion_profile) {
			conversion_profile = conversion_profile || "";

			// use wrap = 0 to indicate se should be open withou the html & form wrapper ????
			$("#flash_wrap").css("visibility","hidden");
			modal = kalturaInitModalBox ( null , { width: 700, height: 360 } );
			modal.innerHTML = '<div id="kcw"></div>';
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
				quick_edit		: 0, 		// "when opening from the KMC - don't add to the roughcut" ???
				kvar_conversionQuality : conversion_profile
			};

			var params = {
				allowscriptaccess: "always",
				allownetworking: "all",
				bgcolor: "#DBE3E9",
				quality: "high",
//				wmode: "opaque" ,
				movie: kmc.vars.service_url + "/kcw/ui_conf_id/" + kmc.vars.kcw_uiconf
			};

			swfobject.embedSWF(params.movie,			// old note: 36201 - new CW with ability to pass params not ready for this version
				"kcw", "680", "400" , "9.0.0", false, flashvars , params);

			setObjectToRemove("kaltura_cw"); // ???
		},
		onCloseKcw : function() {
			$("#flash_wrap").css("visibility","visible");
			kalturaCloseModalBox();
			modal = null;
			kmc.vars.kcw_open = false;
			// nullify flash object inside div kcw
		},
		openChangePwd : function() {
			kmc.utils.secureIframe('password');
		},
		openChangeEmail : function(email) {
			kmc.utils.secureIframe('email', { email: email } );
		},
		openChangeName : function(fname, lname) {
			kmc.utils.secureIframe('name', { fname: fname, lname: lname } );
		}
	}

	kmc.utils = {
			
		activateHeader : function(on) { // supports turning menu off if needed - just uncomment else clause
			//if(on) {
				$("a").click(function(e) {
					var go_to,
					tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");
//					alert("tab="+tab);
					switch(tab) {
						case "dashboard" :
							go_to = { moduleName : "dashboard", subtab : "" };
							break;
						case "content" :
							go_to = { moduleName : "content", subtab : "manage" };
							break;
						case "studio" :
							go_to = { moduleName : "studio", subtab : "playersList" };
							break;
						case "account":
							go_to = { moduleName : "account", subtab : "overview" };
							break;
						case "admin" :
							go_to = { moduleName : "admin", subtab : "usersTab" };
							break;
						case "analytics" :
							go_to = { moduleName : "analytics", subtab : "usage" };
							break;
//						case "Advertising" :
//							go_to = "tremor";
//							break;
						case "Quickstart Guide" :
							this.href = kmc.vars.quickstart_guide;
							return true;
						case "Logout" :
							kmc.utils.logout();
							return false;
						case "Support" :
							kmc.utils.openSupport(this);
							return false;
						default :
							return false;
					}
//					console.log(go_to);

					
//					if(go_to == "tremor") {
//						$("#flash_wrap").html('<iframe src="http://publishers.adap.tv/osclient/" scrolling="no" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="' + $("#main").height() + '"></iframe>');
//					}
//					else {
					//kmc.mediator.setState(go_to);
					$("#kcms")[0].gotoPage(go_to); //!!!
					return false;
				});
		//	}
		//	else {
		//		alert("kmc.utils.maskHeader(true)");
		//		kmc.utils.maskHeader(true);
		//		$("a").unbind("click")
		//			  .click(function(){
		//					return false;
		//		  });
		//	}
		},
		
		openSupport : function(href) {
			kalturaCloseModalBox();
			var modal_width = $.browser.msie ? 543 : 519;
			var iframe_height = $.browser.msie ? 751 : ($.browser.safari ? 697 : 732);
			$("#flash_wrap").css("visibility","hidden");
			modal = kalturaInitModalBox ( null , { width : modal_width , height: 450 } );
			modal.innerHTML = '<div id="modal"><div id="titlebar"><a id="close" href="#close"></a>' +
							  '<b>Support Request</b></div> <div id="modal_content"><iframe id="support" src="' + href + '" scrolling="no" frameborder="0"' +
							  'marginheight="0" marginwidth="0" height="' + iframe_height + '" width="519"></iframe></div></div>';
			$("#mbContent").addClass("new");
			$("#close").click(function() {
				kmc.utils.closeModal();
				return false;
			});
			return false;
		},

		// merge multipile (unlimited) json object into one.  All arguments passed must be json object.
		// The first argument passed is the json object into which the others will be merged.
		mergeJson : function() {
			var i,
			args=arguments.length,
			primaryObject=arguments[0];
			for(var j=1; j<args ; j++) {
				var jsonObj=arguments[j];
				for(i in jsonObj) {
					primaryObject[i] = jsonObj[i];
				}
			}
			return primaryObject;
		},
		jsonToQuerystring : function(jsonObj,joiner) {
			var i,
			myString="";
			if(typeof joiner == "undefined")
				var joiner = "&";
			for(i in jsonObj) {
				myString += i + "=" + jsonObj[i] + joiner;
			}
			return myString;
		},
		logout : function() {
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
                data: { "ks": kmc.vars.ks },
                dataType: "json",
                complete: function() {
                        window.location = kmc.vars.service_url + "/index.php/kmc/kmc#" + state.moduleName + "|" + state.subtab;
                }
			});
		},
		copyCode : function () {
			$("#copy_msg").show();
			setTimeout(function(){$("#copy_msg").hide(500);},1500)
			$(" textarea#embed_code").select();
		},
		resize : function() {
			var doc_height = $(document).height(),
			offset = $.browser.mozilla ? 37 : 74;
			doc_height = (doc_height-offset)+"px";
			$("#flash_wrap").height(doc_height);
			$("#server_wrap iframe").height(doc_height);
		},
		escapeQuotes : function(string) {
			string = string.replace(/"/g,"&Prime;");
			string = string.replace(/'/g,"&prime;");
			return string;
		},
		isModuleLoaded : function() {
			if($("#flash_wrap object").length || $("#flash_wrap embed").length) {
				kmc.utils.resize();
//				clearInterval(flashMovieTimeout);
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

		/*,
		cookies : {
			set		: function(){},
			get		: function(){},
			kill	: function(){}
		}*/
//};
		closeModal : function() {
			kalturaCloseModalBox();
			$("#flash_wrap").css("visibility","visible");
			return false;
		},
	
//hide header function
		maskHeader : function(hide) {
			if(hide) {
				$("#mask").remove();
			}
			else {
				$("#kmcHeader").append('<div id="mask"></div>');
			}
		},
		
		createTabs : function(arr) {
			if(arr) {
				var module_url = kmc.vars.service_url + '/index.php/kmc/kmc4';
				var arrLen = arr.length;
				var tabsHTML = '';
				for( var i = 0; i < arrLen; i++ ) {
					tabsHTML += '<li><a id="'+ arr[i].module_name +'" rel="'+ arr[i].subtab +'" href="'+ module_url + '#' + arr[i].module_name +'|'+ arr[i].subtab +'"><span>' + arr[i].display_name + '</span></a></li>';
				}
				
				$('#hTabs').html(tabsHTML);
				
				$('a').click(function(e) {
					var tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");
					var subtab = (e.target.tagName == "A") ? $(e.target).attr("rel") : $(e.target).parent().attr("rel");
					
					switch(tab) {
						case "Quickstart Guide" :
							this.href = kmc.vars.quickstart_guide;
							return true;
						case "Logout" :
							kmc.utils.logout();
							return false;
						case "Support" :
							kmc.utils.openSupport(this);
							return false;
						default :
							go_to = { moduleName : tab, subtab : subtab };
							break;
							
					}
					
					$("#kcms")[0].gotoPage(go_to); 
					return false;					
					
				});
			} else {
				$("#kcms")[0].alert('Error getting tabs');
			}
		},
		
		setTab : function(module){
			$("#kmcHeader ul li a").removeClass("active");
			$("a#" + module).addClass("active");
		},		
		
		showFlash : function() {
			$("#server_wrap").hide();
			$("#server_frame").removeAttr('src');
			$("#flash_wrap").css("visibility","visible");
			$("#server_wrap").css("margin-top", 0);
		}, 
		
		openIframe : function(url) {
			$("#flash_wrap").css("visibility","hidden");
			$("#server_frame").attr("src", url);
			$("#server_wrap").css("margin-top", "-"+ ($("#flash_wrap").height() + 2) +"px");
			$("#server_wrap").show();
		},
		
		secureIframe : function(action, fields) {			
			// Set title
			var title;
			switch(action) {
				case "password": 
					title = "Change Password";
					break;
				case "email": 
					title = "Change Email Address";
					break;
				case "name":
					title = "Edit Name";
					break;
			}
			
			// set url
			var url = kmc.vars.service_url + "/secure_form.php?action=" + action;
			// pass in the fields
			for(var i in fields) {
				var fld = (fields[i]) ? fields[i] : '';
				url += '&' + i + '=' + fld;
			}
			// change http to https
			url = url.replace("http", "https");
			
			var modal_width = 400;
			var modal_height = 160;
			$("#flash_wrap").css("visibility","hidden");
			kalturaCloseModalBox();
			modal = kalturaInitModalBox ( null , { width : modal_width , height: modal_height } );
			modal.innerHTML = '<div id="modal"><div id="titlebar"><a id="close" href="#close"></a>' +
							  '<b>' + title + '</b></div> <div id="modal_content"><iframe id="sec_iframe" src="' + url + '" scrolling="no" frameborder="0"' +
							  ' height="' + modal_height + '" width="' + modal_width + '"></iframe></div></div>';
			$("#mbContent").addClass("new");
			$("#close").click(function() {
				kmc.utils.closeModal();
				return false;
			});
		},
		
		changePartner : function() {
			var modal_width = 300;
			var modal_height = 180;
			var title = 'Change Partner';
			var content = 'Please choose partner:<br />';
			$("#flash_wrap").css("visibility","hidden");
			kalturaCloseModalBox();
			modal = kalturaInitModalBox ( null , { width : modal_width , height: modal_height } );
			modal.innerHTML = '<div id="modal"><div id="titlebar"><a id="close" href="#close"></a>' +
							  '<b>' + title + '</b></div> <div id="modal_content">' + content + '</div></div>';
			$("#mbContent").addClass("new");
			$("#close").click(function() {
				kmc.utils.closeModal();
				return false;
			});	
		}
		
	}

	kmc.mediator =  {
		/*
		  Need to implement saveAndClose call to module before switching tabs via html click:
			- inside swf's, show confirm: Save your changes before exiting ? [Yes] [No] [Cancel]
				- Yes = save and return true to html js function (to continue with tab change)
				- No = return true to html js function (without saving)
				- Cancel = return false to html js function, thereby canceling tab change
			- currently saveandclose calls onTabChange (no need)
		*/
		setState : function(go_to) { // go_to as json { module : module, subtab : subtab  }
//			alert("setState("+go_to.module+","+go_to.subtab+")");
			if(!go_to) {
//				alert("!go_to");
				go_to = kmc.vars.next_state; // dbl... checked elsewhere
				kmc.vars.next_state = null; // ???
			}
			if(go_to.subtab == "uploadKMC") {
//				alert("open kcw");
//				kmc.functions.openKcw();
				kmc.vars.kcw_open = true;
				//openCw(kmc.vars.ks, null); // null = conversion_quality
				go_to.subtab = "Upload";
			}
			if(go_to.subtab.toLowerCase() == "publish")
				go_to.subtab = "Playlists";
			if(!kmc.vars.kcw_open) { // ???
//				alert("CloseModalBox");
				kalturaCloseModalBox();
			}
//			alert("kmc.mediator.loadModule(" + go_to.module + "," + go_to.subtab + ")");
			kmc.mediator.setTab(go_to.module);
//			alert("post setTab");
			kmc.mediator.writeUrlHash(go_to.module,go_to.subtab);
//			alert("post writeUrlHash");
//			if(navigator.userAgent.indexOf("Chrome") != -1) {
////				alert("chrome");
//				setTimeout(kmc.mediator.loadModule(go_to.module,go_to.subtab),100);
//				return;
//			}
//			else {
				kmc.mediator.loadModule(go_to.module,go_to.subtab);
//			}
//			alert("post loadModule");
		},
		loadModule : function(module,subtab) {
//	alert("loadModule("+module+","+subtab+")");
			window.kmc_module = null;	// nullify swf object - @todo: check if works/ set correctly
			module = module.toLowerCase();
			if(module=="account")
				module = "settings";
//			subtab = subtab.charAt(0).toUpperCase() + subtab.slice(1).toLowerCase();
			subtab = subtab.replace(/ /g,"%20");
			var module_url = {data : eval("kmc.modules." + module + ".swf_url")};
//	alert("module_url="+module_url.data);
			var attributes = kmc.utils.mergeJson(kmc.modules.shared.attributes,module_url);
			var flashvars = kmc.utils.mergeJson(kmc.modules.shared.flashvars,eval("kmc.modules." + module + ".flashvars"),{ subNavTab : subtab });
			flashvars = { flashvars : kmc.utils.jsonToQuerystring(flashvars) };
			var params = kmc.utils.mergeJson(kmc.modules.shared.params,flashvars);
//			params.wmode = (module == "reports") ? "window" : "opaque";
//			if(module == "settings") {
//					params.wmode = "opaque";
//			}
//			alert(params.wmode);
//	alert("swfobject.createSWF("+attributes+", "+params+", "+kcms+")");
			window.kmc_module = swfobject.createSWF(attributes, params, "kcms");
			if(kmc.vars.kcw_open) {
				$("#flash_wrap").css("visibility","hidden");
				kmc.functions.openKcw();
				kmc.vars.kcw_open = false;
			}
//			alert($("#kcms"));
		},
		writeUrlHash : function(module,subtab){
//	alert("writeUrlHash");
//			if(module == "account")
//				module = "Settings";
			location.hash = module + "|" + subtab;
			document.title = "KMC > " + module + ((subtab && subtab != "") ? " > " + subtab + " |" : "");
//			document.title = "KMC > " + module.charAt(0).toUpperCase() + module.slice(1) + ((subtab && subtab != "") ? " > " + subtab + " |" : "");
		},
		readUrlHash : function() {
			var module = "dashboard", // @todo: change to kmc.vars.default_state.module ?
			subtab = "";
			try {
				var hash = location.hash.split("#")[1].split("|");
			}
			catch(err) {
				var nohash=true;
//				err = null;
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
			}
			return { "moduleName" : module, "subtab" : subtab, "extra" : extra };
		},
		 selectContent : function(uiconf_id,is_playlist) { // called by selectPlaylistContent which is caled from appstudio
//			alert("selectContent("+uiconf_id+","+is_playlist+")");
			var subtab = is_playlist ? "playlists" : "manage";
//			kmc.vars.current_uiconf = uiconf_id; // used by doPreviewEmbed
			kmc.vars.current_uiconf = { "uiconf_id" : uiconf_id , "is_playlist" : is_playlist }; // used by doPreviewEmbed
			kmc.mediator.setState( { module : "content", subtab : subtab } );
		 }
	}

	kmc.preview_embed = {

		// called from p&e dropdown, from content.swf and from appstudio.swf
		doPreviewEmbed : function(id, name, description,previewOnly, is_playlist, uiconf_id, live_bitrates, has_mobile_flavors) {
		// entry/playlist id, description, true/ false (or nothing or "" or null), uiconf id, live_bitrates obj or boolean, is_mix
//			alert("doPreviewEmbed: id="+id+", name="+name+", description="+description+", is_playlist="+is_playlist+", uiconf_id="+uiconf_id);

			if(id != "multitab_playlist") {

				name = kmc.utils.escapeQuotes(name);
				description = kmc.utils.escapeQuotes(description); // @todo: move to "// JW" block

				if(kmc.vars.current_uiconf) { // set by kmc.mediator.selectContent called from appstudio's "select content" action
//					console.log(kmc.vars.current_uiconf); alert("kmc.vars.current_uiconf logged");
//					console.log("is_playlist=",is_playlist);
					if((is_playlist && kmc.vars.current_uiconf.is_playlist) || (!is_playlist && !kmc.vars.current_uiconf.is_playlist)) { // @todo: minor optimization possible
						var uiconf_id = kmc.vars.current_uiconf.uiconf_id;
//						alert("doPreviewEmbed says:\nkmc.vars.current_uiconf true -> uiconf_id = "+uiconf_id);
					}
					kmc.vars.current_uiconf = null;
				}

				if(!uiconf_id) { // get default uiconf_id (first one in list)
					var uiconf_id = is_playlist ? kmc.vars.playlists_list[0].id : kmc.vars.players_list[0].id;
	//				alert(uiconf_id);
				}

				if(uiconf_id > 899 && uiconf_id < 1000) {
					kmc.vars.silverlight = true;
				}
				// JW
				else if(uiconf_id > 799 && uiconf_id < 900) {
					kmc.vars.jw = true,
					jw_license_html = '<strong>COMMERCIAL</strong>',
					jw_options_html = '',
					jw_nomix_box_html = kmc.preview_embed.jw.showNoMix(false,"check");

					if(kmc.vars.jw_swf == "non-commercial.swf") {
						jw_license_html =   '<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank" class="license tooltip"' +
											'title="With this license your player will show a JW Player watermark.  You may NOT use the non-commercial' +
											'JW Player on commercial sites such as: sites owned or operated by corporations, sites with advertisements,' +
											'sites designed to promote a product, service or brand, etc.  If you are not sure whether you need to '+
											'purchase a license, contact us.  You also may not use the AdSolution monetization plugin ' +
											'(which lets you make money off your player).">NON-COMMERCIAL <img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" />' +
											'</a>&nbsp;&bull;&nbsp;<a href="http://corp.kaltura.com/about/contact?subject=JW%20Player%20to%20commercial%20license&amp;' +
											'&amp;pid=' + kmc.vars.partner_id + '&amp;name=' + kmc.vars.screen_name + '&amp;email=' + kmc.vars.email  + '" target="_blank" class="license tooltip" ' +
											'title="Go to the Contact Us page and call us or fill in our Contact form and we\'ll call you (opens in new window/ tab).">Upgrade ' +
											'<img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" /></a>';
						var jw_license_ads_html = '<li>Requires <a href="http://corp.kaltura.com/about/contact?subject=JW%20Player%20to%20commercial%20license&amp;" ' +
											  'class="tooltip" title="With a Commercial license your player will not show the JW Player watermark and you will be ' +
											  'allowed to use the player on any site you want as well as use AdSolution (which lets you make money off your player)."' +
											  'target="_blank">Commercial license <img src="http://corp.kaltura.com/images/graphics/info.png" alt="show tooltip" /></a></li>';
					}
					jw_options_html =	'<div class="label">License Type:</div>\n<div class="description">' + jw_license_html + '</div>\n' +
										'<div class="label">AdSolution:</div><div class="description"> <input type="checkbox" id="AdSolution" ' +
										'onclick="kmc.preview_embed.jw.adSolution()" onmousedown="kmc.vars.jw_chkbox_flag=true" /> Enable ads ' +
										'in your videos.&nbsp; <a href="http://www.longtailvideo.com/referral.aspx?page=kaltura&ref=azbkefsfkqchorl" ' +
										'target="_blank" class="tooltip" title="Go to the JW website to sign up for FREE or to learn more about ' +
										'running in-stream ads in your player from Google AdSense for Video, ScanScout, YuMe and others. (opens ' +
										'in new window/ tab)"> Free sign up... <img src="http://corp.kaltura.com/images/graphics/info.png" alt="' +
										'show tooltip" /></a><br />\n <ul id="ads_notes">\n  <li>Channel Code: <input onblur="' +
										'kmc.preview_embed.jw.adsChannel(this, \'' + id + '\', \'' + name + '\', \'' + description + '\', ' + (is_playlist || false) + ', \'' + uiconf_id + '\');" ' +
										'type="text" id="adSolution_channel" value="" /> <button>Apply</button></li>\n' + (jw_license_ads_html || '') +
										'\n </ul>\n </div>\n';
				} // END JW
			} // end !multitab_playlist

			var embed_code, preview_player,
			id_type = is_playlist ? "Playlist " + (id == "multitab_playlist" ? "Name" : "ID") : "Embedding",
			uiconf_details = kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist);
//			console.log("uiconf_details="+uiconf_details);
			if(kmc.vars.jw) {
				embed_code = kmc.preview_embed.jw.buildJWEmbed(id, name, description, is_playlist, uiconf_id);
				preview_player = embed_code.replace('flvclipper', 'flvclipper/ks/' + kmc.vars.ks);
			}
			else if(kmc.vars.silverlight) {
				embed_code = kmc.preview_embed.buildSilverlightEmbed(id, name, is_playlist, uiconf_id);
				preview_player = embed_code.replace('{KS}','ks=' + kmc.vars.ks);
				embed_code = embed_code.replace('{KS}','');
				embed_code = embed_code.replace("{ALT}", ((kmc.vars.whitelabel) ? "" : "<br/>" + kmc.preview_embed.embed_code_template.kaltura_links));
			}
			else {
				embed_code = kmc.preview_embed.buildKalturaEmbed(id, name, description, is_playlist, uiconf_id);
				preview_player = embed_code.replace('{FLAVOR}','ks=' + kmc.vars.ks + '&');
				embed_code = embed_code.replace('{FLAVOR}','');
			}
			var modal_html = '<div id="modal"><div id="titlebar"><a id="close" href="#close"></a>' +
							 '<a id="help" target="_blank" href="' + kmc.vars.service_url + '/index.php/kmc/help#contentSection118"></a>' + id_type +
							 ': ' + name + '</div> <div id="modal_content">' +
//							 ((typeof(live_bitrates) == "object") ? kmc.preview_embed.buildLiveBitrates(name,live_bitrates) : '') + // always return true because null is an object
							 ((live_bitrates) ? kmc.preview_embed.buildLiveBitrates(name,live_bitrates) : '') +
							 '<div id="player_wrap">' + preview_player + '</div>' +
//							 ((id == "multitab_playlist") ? '' : kmc.preview_embed.buildSelect(id, name, description, is_playlist, uiconf_id)) +
							 ((id == "multitab_playlist") ? '' : kmc.preview_embed.buildSelect(is_playlist, uiconf_id)) +
							 (kmc.vars.jw ? jw_nomix_box_html : '') +							 
							 (kmc.vars.jw ? jw_options_html : '') +
							 ((kmc.vars.silverlight || live_bitrates) ? '' : kmc.preview_embed.buildRtmpOptions()) +
							 ((is_playlist) ? '' : kmc.preview_embed.buildHTML5Option(id, kmc.vars.partner_id, has_mobile_flavors)) + 
							 '<div class="embed_code_div"><div class="label">Embed Code:</div> <div class="right"><textarea id="embed_code" rows="5" cols=""' +
							 // style="width:' + (parseInt(uiconf_details.width)-10) + 'px;"
							 'readonly="true">' + embed_code + '</textarea></div><br class="clear" />' +
							 '<div id="copy_msg">Press Ctrl+C to copy embed code (Command+C on Mac)</div><button id="select_code">' +
							 '<span>Select Code</span></button></div></div></div>';
//			alert(modal_html);
			kmc.vars.jw = false;
			kmc.vars.silverlight = false;

			kalturaCloseModalBox();
			$("#flash_wrap").css("visibility","hidden");
			modal = kalturaInitModalBox ( null , { width : parseInt(uiconf_details.width) + 140 , height: parseInt(uiconf_details.height) + 200 } );
			modal.innerHTML = modal_html;
			$("#mbContent").addClass("new");
			// attach events here instead of writing them inline
			$("#embed_code, #select_code").click(function(){
				kmc.utils.copyCode();
			});
			$("#close").click(function(){
				 kmc.utils.closeModal();
				 return false;
			});
			$("#delivery_type").change(function(){
				kmc.vars.embed_code_delivery_type = this.value;
				kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, uiconf_id);
			});
			$("#player_select").change(function(){
				kmc.preview_embed.doPreviewEmbed(id, name, description, previewOnly, is_playlist, this.value, live_bitrates);
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
			console.log('buildLiveBitrates');
			console.log(arguments);
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

		buildSilverlightEmbed : function(id, name, is_playlist, uiconf_id) {
			var uiconf_details = kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist),
			cache_st = kmc.preview_embed.setCacheStartTime(),
			embed_code = kmc.preview_embed.embed_code_template.silverlight;
			embed_code = embed_code.replace("{WIDTH}",uiconf_details.width);
			embed_code = embed_code.replace("{HEIGHT}",uiconf_details.height);
			embed_code = embed_code.replace("{HOST}",kmc.vars.host);
			embed_code = embed_code.replace("{CACHE_ST}",cache_st);
			embed_code = embed_code.replace("{VER}",uiconf_details.swfUrlVersion); // sl
			embed_code = embed_code.replace("{PARTNER_ID}",kmc.vars.partner_id);
//			embed_code = embed_code.replace("{CDN_HOST}",kmc.vars.cdn_host); // sl
			embed_code = embed_code.replace("{UICONF_ID}",uiconf_id);
			embed_code = embed_code.replace(/{SILVERLIGHT}/gi,uiconf_details.minRuntimeVersion);
			embed_code = embed_code.replace("{INIT_PARAMS}",(is_playlist ? "playlist_id" : "entry_id"));
			embed_code = embed_code.replace("{ENTRY_ID}", id);
			return embed_code;
		},
		buildRtmpOptions : function() {
			var selected = ' selected="selected"';
			var delivery_type = kmc.vars.embed_code_delivery_type || "http";
			var html = '<div id="rtmp" class="label">Select Flash Delivery Type:</div> <div class="right"><select id="delivery_type">';
			var options = '<option value="http"' + ((delivery_type == "http") ? selected : "") + '>Progressive Download (HTTP)&nbsp;</option>' +
						  '<option value="rtmp"' + ((delivery_type == "rtmp") ? selected : "") + '>Adaptive Streaming (RTMP)&nbsp;</option>';
			html += options + '</select></div><br /><div class="note">Adaptive Streaming automatically adjusts to the viewer\'s bandwidth,' + 
					'while Progressive Download allows buffering of the content. <a target="_blank" href="' + kmc.vars.service_url + '/index.php/kmc/help#deliveryType">Read more</a></div><br />';
			return html;
		},
		
		buildHTML5Option : function(entry_id, partner_id, has_mobile_flavors) {
			console.log('buildHTML5Option');
			console.log(arguments);
			var url = kmc.vars.service_url + '/preview/' + partner_id + ':' + entry_id;
			var url_text = url.replace(/http:\/\/|www./ig, '');
			var description = '<div class="note red">This video does not have video flavors compatible with IPhone & IPad. <a target="_blank" href="' + kmc.vars.service_url + '/index.php/kmc/help#html5Support">Read more</a></div>';
			if(has_mobile_flavors) {
				description = '<div class="note">If you enable the HTML5 player, the viewer device will be automatically detected.' +
							  ' <a target="_blank" href="' + kmc.vars.service_url + '/index.php/kmc/help#html5Support">Read more</a>' + 
							  '<br class"clear" />View player outside KMC: <a target="_blank" href="' + url + '">' + 
							  '<span class="preview_url">' + url_text + '</span></a></div>';
			}
			var html = '<div class="label checkbox"><input id="html5_support" type="checkbox" disabled="disabled" /> <label for="html5_support">Support iPhone' + 
					   ' &amp; iPad with HTML5</label></div><br />' + description + '<br />';
			return html;
		},

		// for content|Manage->drilldown->flavors->preview
		// flavor_details = json:
		doFlavorPreview : function(entry_id, entry_name, flavor_details) {
//			console.log(flavor_details);
//			alert("doFlavorPreview(entry_id="+entry_id+", entry_name="+entry_name+", flavor_details logged)");
			entry_name = kmc.utils.escapeQuotes(entry_name);
//			var flavor_asset_name = kmc.utils.escapeQuotes(flavor_details.flavor_name) || "unknown";
			kalturaCloseModalBox();
			$("#flash_wrap").css("visibility","hidden");
			modal = kalturaInitModalBox ( null , { width : parseInt(kmc.vars.default_kdp.width) + 20 , height: parseInt(kmc.vars.default_kdp.height) + 10 } );
			$("#mbContent").addClass("new");
			var player_code = kmc.preview_embed.buildKalturaEmbed(entry_id,entry_name,null,false,kmc.vars.default_kdp);
//			alert("flavor_details.asset_id="+flavor_details.asset_id);
			player_code = player_code.replace('&{FLAVOR}', '&flavorId=' + flavor_details.asset_id + '&ks=' + kmc.vars.ks);
			var modal_html = '<div id="modal"><div id="titlebar"><a id="close" href="#close"></a>' +
							 'Flavor Preview</div>' +
							 '<div id="modal_content">' + player_code + '<dl>' +
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
							 '</dl></div></div>';
			modal.innerHTML = modal_html;
			$("#close").click(function(){
				 kmc.utils.closeModal();
				 return false;
			});
		},

		// eventually replace with <? php echo $embedCodeTemplate; ?>  ;  (variables used: HEIGHT WIDTH HOST CACHE_ST UICONF_ID PARTNER_ID PLAYLIST_ID ENTRY_ID) + {VER}, {SILVERLIGHT}, {INIT_PARAMS} for Silverlight + NAME, DESCRIPTION
		embed_code_template :	{
			object_tag :	'<object id="kaltura_player_{CACHE_ST}" name="kaltura_player" type="application/x-shockwave-flash" allowFullScreen="true" ' +
							'allowNetworking="all" allowScriptAccess="always" height="{HEIGHT}" width="{WIDTH}" ' +
							'xmlns:dc="http://purl.org/dc/terms/" xmlns:media="http://search.yahoo.com/searchmonkey/media/" rel="media:{MEDIA}" ' +
							'resource="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" ' +
							'data="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}">' +
							'<param name="allowFullScreen" value="true" /><param name="allowNetworking" value="all" />' +
							'<param name="allowScriptAccess" value="always" /><param name="bgcolor" value="#000000" />' +
							'<param name="flashVars" value="{FLASHVARS}&{FLAVOR}" /><param name="movie" value="http://{HOST}/index.php/kwidget' +
							'/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}{ENTRY_ID}" />{ALT} {SEO} ' + '</object>',
			iframe_tag : 	'<script type="text/javascript" src="{SCRIPT_URL}"></script><iframe id="kaltura_player_{CACHE_ST}" name="kaltura_player" src="{IFRAME_URL}"' + 
							' height="{HEIGHT}" width="{WIDTH}" frameborder="0">{ALT} {SEO}</iframe>',
			playlist_flashvars :	'playlistAPI.autoInsert=true&playlistAPI.kpl0Name={PL_NAME}' +
									'&playlistAPI.kpl0Url=http%3A%2F%2F{HOST}%2Findex.php%2Fpartnerservices2%2Fexecuteplaylist%3Fuid%3D%26' +
									'partner_id%3D{PARTNER_ID}%26subp_id%3D{PARTNER_ID}00%26format%3D8%26ks%3D%7Bks%7D%26playlist_id%3D{PLAYLIST_ID}',
			kaltura_links :		'<a href="http://corp.kaltura.com">video platform</a> <a href="http://corp.kaltura.com/video_platform/video_management">' +
								'video management</a> <a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a> ' +
								'<a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a>',
			media_seo_info :	'<a rel="media:thumbnail" href="http://{CDN_HOST}/p/{PARTNER_ID}/sp/{PARTNER_ID}00/thumbnail{ENTRY_ID}/width/120/height/90/bgcolor/000000/type/2" /> ' +
								'<span property="dc:description" content="{DESCRIPTION}" /><span property="media:title" content="{NAME}" /> ' +
								'<span property="media:width" content="{WIDTH}" /><span property="media:height" content="{HEIGHT}" /> ' +
								'<span property="media:type" content="application/x-shockwave-flash" />',
							// removed <span property="media:duration" content="{DURATION}" />
			// (variables used: {WIDTH} {HEIGHT} {HOST} {CDN_HOST} {UICONF_ID} {VER} {ENTRY_ID} {SILVERLIGHT} + Missing id and name
			silverlight :	'<object data="data:application/x-silverlight-2," type="application/x-silverlight-2" width="{WIDTH}" height="{HEIGHT}"> <param name="source" ' +
							'value="http://{HOST}/index.php/kwidget/cache_st/{CACHE_ST}/wid/_{PARTNER_ID}/uiconf_id/{UICONF_ID}/nowrapper/1/a/a.xap" />' +
//							'value="http://{HOST}/flash/slp/v{VER}/KalturaPlayer.xap?widget_id=_{PARTNER_ID}&host={HOST}&cdnHost={CDN_HOST}&uiconf_id={UICONF_ID}" />' +
							' <param name="enableHtmlAccess" value="true" /> <param name="background" value="black" /> <param name="minRuntimeVersion" ' +
							'value="{SILVERLIGHT}" /> <param name="autoUpgrade" value="true" /> <param name="InitParams" value="{INIT_PARAMS}={ENTRY_ID}&{KS}" />' +
							' <a href="http://go.microsoft.com/fwlink/?LinkId=149156&v={SILVERLIGHT}"><img src="http://go.microsoft.com/fwlink/?LinkId=161376" ' +
							'alt="Get Microsoft Silverlight" /></a>{ALT}</object>'
		},

		// id = entry id, asset id or playlist id; name = entry name or playlist name;
		// uiconf = uiconfid (normal scenario) or uiconf details json (for #content|Manage->drill down->flavors->preview)
		buildKalturaEmbed : function(id, name, description, is_playlist, uiconf, html5) {
//		alert("buildKalturaEmbed(id="+id+", name="+name+", is_playlist="+is_playlist+", uiconf = " + uiconf);
			var uiconf_id = uiconf.uiconf_id || uiconf,
			uiconf_details = (typeof uiconf == "object") ? uiconf : kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist),  // getUiconfDetails returns json
			cache_st = kmc.preview_embed.setCacheStartTime(),
			embed_code;
//			console.log(uiconf_details); alert("uiconf_details logged");
//			alert("cache_st = " + cache_st);
			embed_code = (html5) ? kmc.preview_embed.embed_code_template.iframe_tag : kmc.preview_embed.embed_code_template.object_tag;
			if(!kmc.vars.jw) { // more efficient to add "&& !kmc.vars.silverlight" (?)
				kmc.vars.embed_code_delivery_type = kmc.vars.embed_code_delivery_type || "http";
				if(kmc.vars.embed_code_delivery_type == "rtmp") {
					// Ran: Removed streamerUrl & rtmpFlavors flashvars [not needed]
					embed_code = embed_code.replace("{FLASHVARS}", "streamerType=rtmp&amp;{FLASHVARS}"); // rtmp://rtmpakmi.kaltura.com/ondemand
				}
			}
			if(is_playlist && id != "multitab_playlist") {	// playlist (not multitab)
				embed_code = embed_code.replace(/{ENTRY_ID}/g,"");
				embed_code = embed_code.replace("{FLASHVARS}",kmc.preview_embed.embed_code_template.playlist_flashvars);
//				console.log(uiconf_details.swf_version); alert("uiconf_details.swf_version logged");
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
			
			var iframe_url = kmc.vars.service_url + '/html5/html5lib/v1.0/mwEmbedFrame.php/entry_id/' + id + '/wid/_' + kmc.vars.partner_id + '/uiconf_id/' + uiconf_id;
			var script_url = kmc.vars.service_url + '/html5/html5lib/v1.0/mwEmbedLoader.php';
			
			embed_code = embed_code.replace("{MEDIA}", "video");	// to be replaced by real media type once doPreviewEmbed (called from within KMC>Content) starts passing full entry object			embed_code = embed_code.replace(/{ENTRY_ID}/gi, (is_playlist ? "-1" : id));
			embed_code = embed_code.replace(/{HEIGHT}/gi,uiconf_details.height);
			embed_code = embed_code.replace(/{WIDTH}/gi,uiconf_details.width);
			embed_code = embed_code.replace(/{HOST}/gi,kmc.vars.host);
			embed_code = embed_code.replace(/{CACHE_ST}/gi,cache_st);
			embed_code = embed_code.replace(/{UICONF_ID}/gi,uiconf_id);
			embed_code = embed_code.replace(/{PARTNER_ID}/gi,kmc.vars.partner_id);
			embed_code = embed_code.replace("{PLAYLIST_ID}",id);
			embed_code = embed_code.replace("{PL_NAME}",name);
   			embed_code = embed_code.replace(/{SERVICE_URL}/gi,kmc.vars.service_url);
			embed_code = embed_code.replace("{ALT}", ((kmc.vars.whitelabel) ? "" : kmc.preview_embed.embed_code_template.kaltura_links));
			embed_code = embed_code.replace("{CDN_HOST}",kmc.vars.cdn_host);
			embed_code = embed_code.replace("{NAME}", name);
			embed_code = embed_code.replace("{DESCRIPTION}", description);
			embed_code = embed_code.replace("{IFRAME_URL}", iframe_url); // added for iframe embed
			embed_code = embed_code.replace("{SCRIPT_URL}", script_url); // added for iframe embed
//			embed_code = embed_code.replace("{DURATION}", entry.duration || '');
//			alert("embed_code: "+embed_code);
			return embed_code;
		},
//		buildSelect : function(id, name, description, is_playlist, uiconf_id) { // called from modal_html;
		buildSelect : function(is_playlist, uiconf_id) { // called from modal_html;
			// used = uiconf_id; is_playlist;
//			alert("buildSelect("+id+", "+name+", "+description+", "+is_playlist+", "+uiconf_id+")");
			uiconf_id = kmc.vars.current_uiconf || uiconf_id;  // @todo: need to nullify kmc.vars.current_uiconf somewhere... on very next line ?
			var list_type = is_playlist ? "playlist" : "player",
			list_length = eval("kmc.vars." + list_type + "s_list.length"),
			html_select = '',
			this_uiconf, selected;
//			alert("uiconf_id="+uiconf_id+" | list_type="+list_type+" | html_select ="+html_select+" | list_length ="+list_length);
			for(var i=0; i<list_length; i++) {
				this_uiconf = eval("kmc.vars." + list_type + "s_list[" + i + "]"),
				selected = (this_uiconf.id == uiconf_id) ? ' selected="selected"' : '';
				html_select += '<option ' + selected + ' value="' + this_uiconf.id + '">' + this_uiconf.name + '</option>';
			}
//			html_select = '<select onchange="kmc.preview_embed.doPreviewEmbed(\'' + id + '\',\'' + name + '\',\'' + description + '\',' + is_playlist + ', this.value)">'
			html_select = '<div class="label">Select Player:</div><div class="right"><select id="player_select">' + html_select + '</select></div><br /><div class="note">Kaltura player includes both layout and functionality (advertising, subtitles, etc)</div><br />';
			kmc.vars.current_uiconf = null;
			return html_select;
		},

//		reload : function(id, name, description, is_playlist, uiconf_id) {
//			var embed_code = kmc.preview_embed.buildEmbed(id, name, description, is_playlist, uiconf_id);
//			$("#player_wrap").html(embed_code);
//			$("#embed_code textarea").val(embed_code);
//			kmc.preview_embed.doPreviewEmbed(id, name, description, is_playlist, uiconf_id);
//		},

		getUiconfDetails : function(uiconf_id,is_playlist) {
//			alert("getUiconfDetails("+"uiconf_id="+uiconf_id+", +is_playlist="+is_playlist+")");
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
//			alert("updateList(" + is_playlist + ")");
			var type = is_playlist ? "playlist" : "player";
//			alert("type = " + type);
			$.ajax({
				url: kmc.vars.getuiconfs_url,
				type: "POST",
				data: { "type": type, "partner_id": kmc.vars.partner_id, "ks": kmc.vars.ks },
				dataType: "json",
				success: function(data) {
//					alert(data);
					if (data && data.length) {
//						alert("success: data && data.length");
						if(is_playlist) {
//							alert("success: kmc.vars.playlists_list = data");
							kmc.vars.playlists_list = data;
						}
						else {
//							alert("success: kmc.vars.players_list = data");
							kmc.vars.players_list = data;
						}
					}
				}
			});
		},
		// JW
		jw : {
			// @todo: chg function name to ?
			adSolution		: function() {	// checkbox onclick; @todo: change id's ?
				if ($("#AdSolution").attr("checked")) {
					$("#ads_notes").show();
					$("#adSolution_channel").focus();
				}
				else {
					$("div.description ul").hide();
					$("#adSolution_channel").val("");
				}
				kmc.vars.jw_chkbox_flag=false;
			},
			adsChannel		: function(this_input, id, name, description, is_playlist, uiconf_id) {
				if(this_input.value=="" || this_input.value=="_") {
					if (!kmc.vars.jw_chkbox_flag) {
						$("#AdSolution").attr("checked",false);
					}
					$("div.description ul").hide();
				}
				var embed_code = kmc.preview_embed.jw.buildJWEmbed(id, name, description, is_playlist, uiconf_id);
				$("#player_wrap").html(embed_code);
				$("#embed_code textarea").val(embed_code);
				// @todo: improve ux by only reloading if actual change took place
			},
			adsolutionSetup	: function(start) { // @todo: explain
				var $adSolution_channel = $("#adSolution_channel");
				if(start)
					if($adSolution_channel.val()=="")
						$adSolution_channel.val("_");
				else
					if($adSolution_channel.val()=="_")
						$adSolution_channel.val("");
			},
			showNoMix : function(checkbox,action) {
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
										'The JW Player does not work with Kaltura <dfn title="A Video Mix is a video made up of two or more ' +
										'Entries, normally created through the Kaltura Editor.">Video Mixes</dfn>.</p>\n<div><input type="' +
										'checkbox" onclick="kmc.preview_embed.jw.showNoMix(this)"> Don\'t show this message again.</div></div>\n';
						else
							var html =	'';
						break;
					default :
						alert("error: no action");
						return;
				}
				return html;
			},

			buildJWEmbed : function (entry_id, name, description, is_playlist, uiconf_id) {
				var uiconf_details = kmc.preview_embed.getUiconfDetails(uiconf_id,is_playlist); // @ todo: change to embed_code.
				 var width			= uiconf_details.width;
				 var height			= uiconf_details.height;
				 var playlist_type	= uiconf_details.playlistType;
				 var share			= uiconf_details.share;
				 var skin			= uiconf_details.skin;
				var jw_flashvars = '';
				var unique_id = new Date(); unique_id = unique_id.getTime();
				var jw_plugins =  new Array();

				if(!is_playlist || is_playlist == "undefined") {
					jw_flashvars += 'file=http://' + kmc.vars.cdn_host + '/p/' + kmc.vars.partner_id + '/sp/' + kmc.vars.partner_id +
									'00/flvclipper/entry_id/' + entry_id + '/version/100000/ext/flv';
					jw_plugins.push("kalturastats");
				}
				else {
					jw_flashvars += 'file=http://' + kmc.vars.cdn_host + '/index.php/partnerservices2/executeplaylist%3Fuid%3D%26format%3D8%26playlist_id%3D' +
									entry_id + '%26partner_id%3D' + kmc.vars.partner_id + '%26subp_id%3D' + kmc.vars.partner_id + '00%26ks%3D%7Bks%7D' +
									'&playlist=' + playlist_type;
					if(playlist_type != "bottom") {
						jw_flashvars += '&playlistsize=300';
					}
				}

				if(share == "true" || share == true) {
					jw_flashvars += '&viral.functions=embed,link&viral.onpause=false';
					jw_plugins.push("viral-2");
				}

			/* for AdSolution */
				var jw_ads = { channel : $("#adSolution_channel").val() };
				if ($("#AdSolution").is(":checked") && jw_ads.channel != "") {
					jw_ads.flashvars =	'&ltas.cc=' + jw_ads.channel + 	// &ltas.xmlprefix=http://zo.longtailvideo.com.s3.amazonaws.com/ //uacbirxmcnulxmf
										'&mediaid=' + entry_id;
					jw_plugins.push("ltas");
					jw_ads.flashvars += "&title=" + name + "&description=" + description;
					jw_flashvars += jw_ads.flashvars;
				}
			/* end AdSolution */

				var jw_skin = (skin == "undefined" || skin == "") ? '' : '&skin=http://' + kmc.vars.cdn_host + '/flash/jw/skins/' + skin;

				jw_flashvars =  jw_flashvars +
								'&amp;image=http://' + kmc.vars.cdn_host + '/p/' + kmc.vars.partner_id + '/sp/' + kmc.vars.partner_id +
								'00/thumbnail/entry_id/' + entry_id + '/width/640/height/480' + jw_skin + '&widgetId=jw00000001&entryId=' +
								entry_id + '&partnerId=' + kmc.vars.partner_id + '&uiconfId=' + uiconf_id + '&plugins=' + jw_plugins;

				var jw_embed_code = '<div id="jw_wrap_' + unique_id + '"> <object width="' + width + '" height="' + height + '" id="jw_player_' +
									unique_id + '" name="jw_player_' + unique_id + '">' +
									' <param name="movie" value="http://' + kmc.vars.cdn_host + '/flash/jw/player/' + kmc.vars.jw_swf + '" />' +
//									' <param name="wmode" value="transparent" />' +
									' <param name="allowScriptAccess" value="always" />' +
									' <param name="flashvars" value="' + jw_flashvars + '" />' +
									' <embed id="jw_player__' + unique_id + '" name="jw_player__' + unique_id + '" src="http://' +
									kmc.vars.cdn_host + '/flash/jw/player/' + kmc.vars.jw_swf + '" width="' + width + '" height="' + height +
									'" allowfullscreen="true" ' +
									'wmode="transparent" ' +
									'allowscriptaccess="always" ' + 'flashvars="' + jw_flashvars +
									'" /> <noembed><a href="http://www.kaltura.org/">Open Source Video</a></noembed> </object> </div>';
				return jw_embed_code;
			} /* end build jw embed code */
		} // END JW
	}
	
	kmc.editors = {
		start: function(entry_id, entry_name, editor_type, new_mix) {
//			alert("kmc.editors.start("+entry_id+","+entry_name+","+editor_type+","+new_mix+")");
			if(new_mix) {
//				alert("call create mix ajax");
//				$("body").css("cursor","wait");
				jQuery.ajax({
					url: kmc.vars.createmix_url,
					type: "POST",
					data: { "entry_id": entry_id, "entry_name": entry_name, "partner_id": kmc.vars.partner_id, "ks": kmc.vars.ks, "editor_type": editor_type, "user_id": kmc.vars.user_id },
//						dataType: "json",
					success: function(data) {
//							alert("ajax success: " + data);
						if (data && data.length) {
//								console.info(data);
//								alert("openEditor(data logged," + entry_name + ",1)");
							kmc.editors.start(data, entry_name, editor_type, false);
						}
					}
				});
				return;
			}
			switch(editor_type) {
				case "1" :	// KSE
				case 1	 :
					var width = "868";  // 910
					var height = "544";
					var editor_uiconf = kmc.vars.kse_uiconf;
					kmc.editors.flashvars.entry_id = entry_id;
					break;

				case "2" :	// KAE
				case 2	 :
					var width = "825";
					var height = "604";
					var editor_uiconf = kmc.vars.kae_uiconf;
					kmc.editors.params.movie = kmc.vars.service_url + "/kse/ui_conf_id/" + kmc.vars.kae_uiconf;
					kmc.editors.flashvars.entry_id = entry_id;
					break;
				default :
					alert("error: switch=default");
					break;
			}
			kmc.editors.flashvars.entry_id = entry_id;
			width = $.browser.msie ? parseInt(width) + 32 : parseInt(width) + 22;
			$("#flash_wrap").css("visibility","hidden");
			modal = kalturaInitModalBox( null, { width: width, height: height } );
			modal.innerHTML = '<div id="keditor"></div>';
			swfobject.embedSWF(	kmc.vars.service_url + "/kse/ui_conf_id/" + editor_uiconf,
								"keditor",
								width,
								height,
								"9.0.0",
								false,
								kmc.editors.flashvars,
//								kmc.utils.mergeJson(kmc.editors.flashvars, { "entry_id" : entry_id }),
								kmc.editors.params
							);
			setObjectToRemove("keditor");
		},
		flashvars: {
			"uid"			: kmc.vars.user_id, // Anonymous
			"partner_id"	: kmc.vars.partner_id,
			"subp_id"		: kmc.vars.subp_id,
			"ks"			: kmc.vars.ks,
			"kshow_id"		: "-1",
			"backF"			: "kmc.functions.closeEditor", // kse
			"saveF"			: "kmc.functions.saveEditor", // kse
			// KAE can read both formats and cases of flashvars:
			// "partnerId", "subpId", "kshowId", "entryId", "uid", "ks"
			"terms_of_use"	: kmc.vars.terms_of_use,
			"disableurlhashing" : kmc.vars.disableurlhashing,
			"jsDelegate"	: "kmc.editors.kae_functions"
		},
		params: {
			allowscriptaccess	: "always",
			allownetworking		: "all",
			bgcolor				: "#ffffff", // ? for both ?
			quality				: "high",
//			wmode				: "opaque" ,
			movie				: kmc.vars.service_url + "/kse/ui_conf_id/" + kmc.vars.kse_uiconf
		},

		kae_functions: {
			closeHandler							: function(obj) {
					kmc.utils.closeModal();
				},
			publishHandler							: kmc.functions.doNothing,
			publishFailHandler						: kmc.functions.doNothing,
			connectVoiceRecorderFailHandler			: kmc.functions.doNothing,
			getMicrophoneVoiceRecorderFailHandler	: kmc.functions.doNothing,
			initializationFailHandler				: kmc.functions.doNothing,
			initKalturaApplicationFailHandler		: kmc.functions.doNothing,
			localeFailHandler						: kmc.functions.doNothing,
			skinFailHandler							: kmc.functions.doNothing,
			getUiConfFailHandler					: kmc.functions.doNothing,
			getPluginsProviderFailHandler			: kmc.functions.doNothing,
			openVoiceRecorderHandler				: kmc.functions.doNothing,
			connectVoiceRecorderHandler				: kmc.functions.doNothing,
			startRecordingHandler					: kmc.functions.doNothing,
			recorderCancelHandler					: kmc.functions.doNothing,
			contributeVoiceRecordingHandler			: kmc.functions.doNothing,
			openContributionWizardHandler			: kmc.functions.doNothing,
			contributeEntriesHandler				: kmc.functions.doNothing,
			addTransitionHandler					: kmc.functions.doNothing,
			trimTransitionHandler					: kmc.functions.doNothing,
			addPluginHandler						: kmc.functions.doNothing,
			pluginFlagClickHandler					: kmc.functions.doNothing,
			pluginEditHandler						: kmc.functions.doNothing,
			pluginTrimHandler						: kmc.functions.doNothing,
			addAssetHandler							: kmc.functions.doNothing,
			changeSolidColorHandler					: kmc.functions.doNothing,
			trimAssetHandler						: kmc.functions.doNothing,
			duplicateHandler						: kmc.functions.doNothing,
			splitHandler							: kmc.functions.doNothing,
			reorderStoryboardHandler				: kmc.functions.doNothing,
			reorderTimelineHandler					: kmc.functions.doNothing,
			removeHandler							: kmc.functions.doNothing,
			zoomChangeHandler						: kmc.functions.doNothing,
			kalturaLogoClickHandler					: kmc.functions.doNothing,
			editVolumeLevelsButtonHandler			: kmc.functions.doNothing,
			editVolumeLevelsChangeHandler			: kmc.functions.doNothing,
			volumeOverallChangeHandler				: kmc.functions.doNothing,
			emptyTimelinesHandler					: kmc.functions.doNothing,
			showHelpHandler							: kmc.functions.doNothing,
			showVersionsWindowHandler				: kmc.functions.doNothing,
			sortMediaClipsHandler					: kmc.functions.doNothing,
			filterMediaClipsHandler					: kmc.functions.doNothing
		}
	},


// Maintain support for old kmc2 functions:

//function openCw (ks ,conversion_quality) {
//	kmc.functions.openKcw();
// }
// function expiredF() { // @todo: change all modules
//	kmc.utils.expired();
// }
// function selectPlaylistContent(params) { // @todo: change call in appstudio
// function selectPlaylistContent(uiconf_id,is_playlist) {
//		alert("kmc.mediator.selectContent("+uiconf_id+","+is_playlist+")");
//		console.log(uiconf_id);
//		kmc.mediator.selectContent(params.playerId,params.isPlaylist);
// }
 
//function logout() {
//	kmc.utils.logout();
//}
// function openEditor(entry_id,entry_name,editor_type,newmix) {
//	kmc.editors.start(entry_id,entry_name,editor_type,newmix);
// }
// function refreshSWF() {
//	alert("refreshSWF()");
//	var state = kmc.mediator.readUrlHash();
//	kmc.mediator.loadModule(state.moduleName,state.subtab);
// }
 function openPlayer(emptystring, width, height, uiconf_id, previewOnly) { // for catching appstudio p&e
//	 alert("received call to openPlayer(emptystring="+emptystring+", "+"width="+width+", "+"height="+height+", uiconf_id="+uiconf_id+")");
	 if (previewOnly==true)
		 $("#kcms")[0].alert('previewOnly from studio');
	 kmc.preview_embed.doPreviewEmbed("multitab_playlist", null, null, previewOnly, true, uiconf_id); // id,name, description, previewOnly, is_playlist,uiconf_id
 }
// function openPlayer(id, name, description, is_playlist, uiconf_id) {
//	kmc.preview_embed.doPreviewEmbed(id, name, description, is_playlist, uiconf_id);
// }
// function openPlaylist(id, name, description, is_playlist, uiconf_id) {
//	kmc.preview_embed.doPreviewEmbed(id, name, description, is_playlist, uiconf_id);
// }
function playlistAdded() { // called from appstudio
//	alert("playlistAdded() calling kmc.preview_embed.updateList(true)");
	kmc.preview_embed.updateList(true);
}

function playerAdded() { // called from appstudio
//	alert("playerAdded() calling kmc.preview_embed.updateList(false)");
	kmc.preview_embed.updateList(false);
}

/*** end old functions ***/

$(function() {
	kmc.mediator.loadKmc();
	
	/*
	$('#change_partners').change(function(e) {
		window.location.href = '/index.php/kmc/extloginbyks?ks=' + kmc.vars.ks + '&partner_id=' + this.value; 
	});
	*/
	
	$('#change_partner').click(function() {
		console.log(kmc.vars.allowed_partners);
		//kmc.utils.changePartner();
	});

})

kmc.vars.kmc_swf = {
    url : kmc.vars.service_url+"/flash/kmc/"+kmc.vars.kmc_version+"/kmc.swf",
    flashvars : {
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
		enableCustomData	: kmc.vars.enable_custom_data,
		urchinNumber		: kmc.vars.google_analytics_account, // "UA-12055206-1""
		
		userName			: kmc.vars.screen_name,
		firstLogin			: kmc.vars.first_login,				
		refreshPlayerList	: "refreshPlayerList", // @todo: ???!!!
		refreshPlaylistList : "refreshPlaylistList", // @todo: ???!!!
		openPlayer			: "kmc.preview_embed.doPreviewEmbed", // @todo: remove for 2.0.9 ?
		openPlaylist		: "kmc.preview_embed.doPreviewEmbed",
		email				: kmc.vars.email,
		openCw				: "kmc.functions.openKcw"		
		
		// path to modules (optional):
		//modules_path:kmc.vars.service_url+"/flash/kmc/"+kmc.vars.kmc_version+"/modules",

    }
}
kmc.mediator.loadKmc = function() {
	var attributes = {
			height : "100%",
			width : "100%",
			data : kmc.vars.kmc_swf.url,
			id : "kcms"
	}
	var flashvars = kmc.vars.kmc_swf.flashvars; 
	var params = {
			flashvars: kmc.utils.jsonToQuerystring(flashvars)
	} 
	// attruib, params, 
	window.kmc_module = swfobject.createSWF(attributes, params, "kcms");
}
