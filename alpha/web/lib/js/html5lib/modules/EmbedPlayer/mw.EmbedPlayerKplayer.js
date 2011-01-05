/*
 * The "kaltura player" embedPlayer interface for fallback h.264 and flv video format support
 */

// Called from the kdp.swf
function jsInterfaceReadyFunc() {
	return true;
}

mw.EmbedPlayerKplayer = {

	// Instance name:
	instanceOf : 'Kplayer',

	// List of supported features:
	supports : {
		'playHead' : true,
		'pause' : true,
		'stop' : true,
		'timeDisplay' : true,
		'volumeControl' : true,
		'overlays' : true,
		'fullscreen' : true
	},

	// Stores the current time as set from flash
	flashCurrentTime : 0,

	/*
	 * Write the Embed html to the target
	 */
	doEmbedHTML : function() {
		var _this = this;

		mw.log("kPlayer:: embed src::" + _this.getSrc());
		var flashvars = {};
		flashvars.autoPlay = "true";
		var playerPath = mw.getMwEmbedPath() + 'modules/EmbedPlayer/binPlayers/kaltura-player';
		flashvars.entryId = mw.absoluteUrl(_this.getSrc());

		// Use a relative url if the protocal is file://
		if (mw.parseUri(document.URL).protocol == 'file') {
			playerPath = mw.getRelativeMwEmbedPath() + 'modules/EmbedPlayer/binPlayers/kaltura-player';
			flashvars.entryId = _this.getSrc();
		}

		flashvars.debugMode = "true";
		flashvars.fileSystemMode = "true";
		flashvars.widgetId = "_7463";
		flashvars.partnerId = "7463";
		flashvars.pluginDomain = "kdp3/plugins/";
		flashvars.kml = "local";
		flashvars.kmlPath = playerPath + '/config.xml';
		flashvars.sourceType = "url";

		// flashvars.host = "www.kaltura.com";
		flashvars.externalInterfaceDisabled = 'false';
		flashvars.skinPath = playerPath + '/skin.swf';

		flashvars["full.skinPath"] = playerPath + '/LightDoodleskin.swf';

		var params = {};
		params.quality = "best";
		params.wmode = "opaque";
		params.allowfullscreen = "true";
		params.allowscriptaccess = "always";

		var attributes = {};
		attributes.id = this.pid;
		attributes.name = this.pid;

		mw.log(" about to add the pid container");
		$j(this).html($j('<div />').attr('id', this.pid + '_container'));
		// Call swm dom loaded function:
		swfobject.callDomLoadFunctions();
		// Do the flash embedding with embedSWF
		swfobject.embedSWF(playerPath + "/kdp3.swf", this.pid + '_container',
				'100%', '100%', "10.0.0", playerPath + "/expressInstall.swf",
				flashvars, params, attributes);

		// Direct object embed
		/*
		 * $j( this ).html( '<object
		 * classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="780"
		 * height="420">'+ '<param name="movie" value="myContent.swf" />'+ '<!--[if
		 * !IE]>-->'+ '<object type="application/x-shockwave-flash"
		 * data="myContent.swf" width="780" height="420">'+ '<!--<![endif]-->'+ '<p>
		 * error with flash embed</p>' '<!--[if !IE]>-->'+ '</object>'+ '<!--<![endif]-->'+ '</object>' )
		 */

		setTimeout(function() {
			_this.postEmbedJS();
		}, 100);

		// Flash player loses its bindings once it changes sizes::
		$j(_this).bind('onOpenFullScreen', function() {
			_this.postEmbedJS();
		});
		$j(_this).bind('onCloseFullScreen', function() {
			_this.postEmbedJS();
		});
	},

	/**
	 * javascript run post player embedding
	 */
	postEmbedJS : function() {
		var _this = this;
		this.getPlayerElement();

		var bindEventMap = {
			'doPause' : 'onPause',
			'doPlay' : 'onPlay',
			'durationChange' : 'onDurationChange',
			'playerPlayEnd' : 'onClipDone',
			'playerUpdatePlayhead' : 'onUpdatePlayhead',
			'bytesTotalChange' : 'onBytesTotalChange',
			'bytesDownloadedChange' : 'onBytesDownloadedChange'
		}

		if (this.playerElement && this.playerElement.addJsListener) {
			$j.each( bindEventMap, function( bindName, localMethod ) {
				_this.bindPlayerFunction(bindName, localMethod);
			} );
			// Start the monitor
			this.monitor();
		} else {
			// Keep trying to get the player element
			// mw.log('insert media: not defined:' + typeof
			// this.playerElement.insertMedia );
			setTimeout(function() {
				_this.postEmbedJS();
			}, 250);
		}
	},

	/**
	 * Bind a Player Function,
	 * 
	 * Does some tricker to bind to "this" player instance:
	 * 
	 * @param {String}
	 *            flash binding name
	 * @param {String}
	 *            function callback name
	 */
	bindPlayerFunction : function(bindName, methodName) {
		// The kaltura kdp can only call a global function by given name
		var gKdpCallbackName = methodName + '_cb_' + this.id;

		// Create an anonymous function with local player scope
		var createGlobalCB = function(cName, embedPlayer) {
			window[cName] = function(data) {
				if (embedPlayer._propagateEvents) {
					embedPlayer[methodName](data);
				}
			};
		}(gKdpCallbackName, this);

		// Add the listener to the KDP flash player:
		this.playerElement.addJsListener(bindName, gKdpCallbackName);
	},

	/**
	 * on Pause callback from the kaltura flash player calls parent_pause to
	 * update the interface
	 */
	onPause : function() {
		this.parent_pause();
	},

	/**
	 * onPlay function callback from the kaltura flash player directly call the
	 * parent_play
	 */
	onPlay : function() {
		this.parent_play();
	},

	onDurationChange : function(data, id) {
		mw.log(" onDurationChange: " + data.newValue);
		// update the duration:
		this.duration = data.newValue;
	},

	/**
	 * play method calls parent_play to update the interface
	 */
	play : function() {
		if (this.playerElement && this.playerElement.sendNotification) {
			this.playerElement.sendNotification('doPlay');
		}
		this.parent_play();
	},

	/**
	 * pause method calls parent_pause to update the interface
	 */
	pause : function() {
		if (this.playerElement && this.playerElement.sendNotification) {
			this.playerElement.sendNotification('doPause');
		}
		this.parent_pause();
	},

	/**
	 * Issues a seek to the playerElement
	 * 
	 * @param {Float}
	 *            percentage Percentage of total stream length to seek to
	 */
	doSeek : function(percentage) {
		var _this = this;
		var seekTime = percentage * this.getDuration();
		mw.log( 'EmbedPlayerKalturaKplayer:: doSeek: ' + percentage + ' time:' + seekTime );
		if (this.supportsURLTimeEncoding()) {

			// Make sure we could not do a local seek instead:
			if (!(percentage < this.bufferedPercent
					&& this.playerElement.duration && !this.didSeekJump)) {
				// We support URLTimeEncoding call parent seek:
				this.parent_doSeek( percentage );
				return;
			}
		}

		if (this.playerElement) {		
			// Issue the seek to the flash player:
			this.playerElement.sendNotification('doSeek', seekTime);

			// Kdp is missing seek done callback
			setTimeout(function() {
				_this.seeking = false;
			}, 500);
		} else {
			// try to do a play then seek:
			this.doPlayThenSeek(percentage)
		}
		// Run the onSeeking interface update
		this.controlBuilder.onSeek();
	},

	/**
	 * Seek in a existing stream
	 * 
	 * @param {Float}
	 *            percentage Percentage of the stream to seek to between 0 and 1
	 */
	doPlayThenSeek : function(percentage) {
		mw.log('flash::doPlayThenSeek::');
		var _this = this;
		// issue the play request
	this.play();

	// let the player know we are seeking
	_this.seeking = true;

	var getPlayerCount = 0;
	var readyForSeek = function() {
		_this.getPlayerElement();
		// if we have duration then we are ready to do the seek ( flash can't
		// seek untill there is some buffer )
		if (_this.playerElement && _this.playerElement.sendNotification
				&& _this.getDuration() && _this.bufferedPercent) {
			var seekTime = percentage * _this.getDuration();
			// Issue the seek to the flash player:
			_this.playerElement.sendNotification('doSeek', seekTime);
		} else {
			// Try to get player for 20 seconds:
			if (getPlayerCount < 400) {
				setTimeout(readyForSeek, 50);
				getPlayerCount++;
			} else {
				mw.log('Error:doPlayThenSeek failed');
			}
		}
	}
	readyForSeek();
},

/**
 * Issues a volume update to the playerElement
 * 
 * @param {Float}
 *            percentage Percentage to update volume to
 */
setPlayerElementVolume : function(percentage) {
	if (this.playerElement && this.playerElement.sendNotification) {
		this.playerElement.sendNotification('changeVolume', percentage);
	}
},

/**
 * function called by flash at set interval to update the playhead.
 */
onUpdatePlayhead : function(playheadValue) {
	this.flashCurrentTime = playheadValue;
},

/**
 * function called by flash when the total media size changes
 */
onBytesTotalChange : function(data, id) {
	this.bytesTotal = data.newValue;
},

/**
 * function called by flash applet when download bytes changes
 */
onBytesDownloadedChange : function(data, id) {
	mw.log('onBytesDownloadedChange');
	this.bytesLoaded = data.newValue;
	this.bufferedPercent = this.bytesLoaded / this.bytesTotal;

	// Fire the parent html5 action
	$j(this).trigger('progress', {
		'loaded' : this.bytesLoaded,
		'total' : this.bytesTotal
	});
},

/**
 * Get the embed player time
 */
getPlayerElementTime : function() {
	// update currentTime
	return this.flashCurrentTime;
},

/**
 * Get the embed fla object player Element
 */
getPlayerElement : function() {
	this.playerElement = document.getElementById(this.pid);
	return this.playerElement;
}
}

/**
 * function called once player is ready.
 * 
 * NOTE: playerID is not always passed so we can't use this:
 */
function onKdpReady(playerId) {
	mw.log("player is ready::" + playerId);
}

/*
 * ! SWFObject v2.2 <http://code.google.com/p/swfobject/> is released under the
 * MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

var swfobject = function() {

	var UNDEF = "undefined", OBJECT = "object", SHOCKWAVE_FLASH = "Shockwave Flash", SHOCKWAVE_FLASH_AX = "ShockwaveFlash.ShockwaveFlash", FLASH_MIME_TYPE = "application/x-shockwave-flash", EXPRESS_INSTALL_ID = "SWFObjectExprInst", ON_READY_STATE_CHANGE = "onreadystatechange",

	win = window, doc = document, nav = navigator,

	plugin = false, domLoadFnArr = [ main ], regObjArr = [], objIdArr = [], listenersArr = [], storedAltContent, storedAltContentId, storedCallbackFn, storedCallbackObj, isDomLoaded = false, isExpressInstallActive = false, dynamicStylesheet, dynamicStylesheetMedia, autoHideShow = true,

	/*
	 * Centralized function for browser feature detection - User agent string
	 * detection is only used when no good alternative is possible - Is executed
	 * directly for optimal performance
	 */
	ua = function() {
		var w3cdom = typeof doc.getElementById != UNDEF
				&& typeof doc.getElementsByTagName != UNDEF
				&& typeof doc.createElement != UNDEF, u = nav.userAgent
				.toLowerCase(), p = nav.platform.toLowerCase(), windows = p ? /win/
				.test(p)
				: /win/.test(u), mac = p ? /mac/.test(p) : /mac/.test(u), webkit = /webkit/
				.test(u) ? parseFloat(u.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,
				"$1")) : false, // returns either the webkit version or false if
								// not webkit
		ie = !+"\v1", // feature detection based on Andrea Giammarchi's
						// solution:
						// http://webreflection.blogspot.com/2009/01/32-bytes-to-know-if-your-browser-is-ie.html
		playerVersion = [ 0, 0, 0 ], d = null;
		if (typeof nav.plugins != UNDEF
				&& typeof nav.plugins[SHOCKWAVE_FLASH] == OBJECT) {
			d = nav.plugins[SHOCKWAVE_FLASH].description;
			if (d
					&& !(typeof nav.mimeTypes != UNDEF
							&& nav.mimeTypes[FLASH_MIME_TYPE] && !nav.mimeTypes[FLASH_MIME_TYPE].enabledPlugin)) { // navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin
																													// indicates
																													// whether
																													// plug-ins
																													// are
																													// enabled
																													// or
																													// disabled
																													// in
																													// Safari
																													// 3+
				plugin = true;
				ie = false; // cascaded feature detection for Internet Explorer
				d = d.replace(/^.*\s+(\S+\s+\S+$)/, "$1");
				playerVersion[0] = parseInt(d.replace(/^(.*)\..*$/, "$1"), 10);
				playerVersion[1] = parseInt(d.replace(/^.*\.(.*)\s.*$/, "$1"),
						10);
				playerVersion[2] = /[a-zA-Z]/.test(d) ? parseInt(d.replace(
						/^.*[a-zA-Z]+(.*)$/, "$1"), 10) : 0;
			}
		} else if (typeof win.ActiveXObject != UNDEF) {
			try {
				var a = new ActiveXObject(SHOCKWAVE_FLASH_AX);
				if (a) { // a will return null when ActiveX is disabled
					d = a.GetVariable("$version");
					if (d) {
						ie = true; // cascaded feature detection for Internet
									// Explorer
						d = d.split(" ")[1].split(",");
						playerVersion = [ parseInt(d[0], 10),
								parseInt(d[1], 10), parseInt(d[2], 10) ];
					}
				}
			} catch (e) {
			}
		}
		return {
			w3 : w3cdom,
			pv : playerVersion,
			wk : webkit,
			ie : ie,
			win : windows,
			mac : mac
		};
	}();

	function callDomLoadFunctions() {
		if (isDomLoaded) {
			return;
		}
		try { // test if we can really add/remove elements to/from the DOM; we
				// don't want to fire it too early
			var t = doc.getElementsByTagName("body")[0]
					.appendChild(createElement("span"));
			t.parentNode.removeChild(t);
		} catch (e) {
			return;
		}
		isDomLoaded = true;
		var dl = domLoadFnArr.length;
		for ( var i = 0; i < dl; i++) {
			domLoadFnArr[i]();
		}
	}

	function addDomLoadEvent(fn) {
		if (isDomLoaded) {
			fn();
		} else {
			domLoadFnArr[domLoadFnArr.length] = fn; // Array.push() is only
													// available in IE5.5+
		}
	}

	/*
	 * Cross-browser onload - Based on James Edwards' solution:
	 * http://brothercake.com/site/resources/scripts/onload/ - Will fire an
	 * event as soon as a web page including all of its assets are loaded
	 */
	function addLoadEvent(fn) {
		if (typeof win.addEventListener != UNDEF) {
			win.addEventListener("load", fn, false);
		} else if (typeof doc.addEventListener != UNDEF) {
			doc.addEventListener("load", fn, false);
		} else if (typeof win.attachEvent != UNDEF) {
			addListener(win, "onload", fn);
		} else if (typeof win.onload == "function") {
			var fnOld = win.onload;
			win.onload = function() {
				fnOld();
				fn();
			};
		} else {
			win.onload = fn;
		}
	}

	/*
	 * Main function - Will preferably execute onDomLoad, otherwise onload (as a
	 * fallback)
	 */
	function main() {
		if (plugin) {
			testPlayerVersion();
		} else {
			matchVersions();
		}
	}

	/*
	 * Detect the Flash Player version for non-Internet Explorer browsers -
	 * Detecting the plug-in version via the object element is more precise than
	 * using the plugins collection item's description: a. Both release and
	 * build numbers can be detected b. Avoid wrong descriptions by corrupt
	 * installers provided by Adobe c. Avoid wrong descriptions by multiple
	 * Flash Player entries in the plugin Array, caused by incorrect browser
	 * imports - Disadvantage of this method is that it depends on the
	 * availability of the DOM, while the plugins collection is immediately
	 * available
	 */
	function testPlayerVersion() {
		var b = doc.getElementsByTagName("body")[0];
		var o = createElement(OBJECT);
		o.setAttribute("type", FLASH_MIME_TYPE);
		var t = b.appendChild(o);
		if (t) {
			var counter = 0;
			(function() {
				if (typeof t.GetVariable != UNDEF) {
					try {
						var d = t.GetVariable("$version");
						if (d) {
							d = d.split(" ")[1].split(",");
							ua.pv = [ parseInt(d[0], 10), parseInt(d[1], 10),
									parseInt(d[2], 10) ];
						}
					} catch (e) {
						// error in grabbing flash version
					}
				} else if (counter < 10) {
					counter++;
					setTimeout(arguments.callee, 10);
					return;
				}
				b.removeChild(o);
				t = null;
				matchVersions();
			})();
		} else {
			matchVersions();
		}
	}

	/*
	 * Perform Flash Player and SWF version matching; static publishing only
	 */
	function matchVersions() {
		var rl = regObjArr.length;
		if (rl > 0) {
			for ( var i = 0; i < rl; i++) { // for each registered object
											// element
				var id = regObjArr[i].id;
				var cb = regObjArr[i].callbackFn;
				var cbObj = {
					success : false,
					id : id
				};
				if (ua.pv[0] > 0) {
					var obj = getElementById(id);
					if (obj) {
						if (hasPlayerVersion(regObjArr[i].swfVersion)
								&& !(ua.wk && ua.wk < 312)) { // Flash Player
																// version >=
																// published SWF
																// version:
																// Houston, we
																// have a match!
							setVisibility(id, true);
							if (cb) {
								cbObj.success = true;
								cbObj.ref = getObjectById(id);
								cb(cbObj);
							}
						} else if (regObjArr[i].expressInstall
								&& canExpressInstall()) { // show the Adobe
															// Express Install
															// dialog if set by
															// the web page
															// author and if
															// supported
							var att = {};
							att.data = regObjArr[i].expressInstall;
							att.width = obj.getAttribute("width") || "0";
							att.height = obj.getAttribute("height") || "0";
							if (obj.getAttribute("class")) {
								att.styleclass = obj.getAttribute("class");
							}
							if (obj.getAttribute("align")) {
								att.align = obj.getAttribute("align");
							}
							// parse HTML object param element's name-value
							// pairs
							var par = {};
							var p = obj.getElementsByTagName("param");
							var pl = p.length;
							for ( var j = 0; j < pl; j++) {
								if (p[j].getAttribute("name").toLowerCase() != "movie") {
									par[p[j].getAttribute("name")] = p[j]
											.getAttribute("value");
								}
							}
							showExpressInstall(att, par, id, cb);
						} else { // Flash Player and SWF version mismatch or
									// an older Webkit engine that ignores the
									// HTML object element's nested param
									// elements: display alternative content
									// instead of SWF
							displayAltContent(obj);
							if (cb) {
								cb(cbObj);
							}
						}
					}
				} else { // if no Flash Player is installed or the fp version
							// cannot be detected we let the HTML object element
							// do its job (either show a SWF or alternative
							// content)
					setVisibility(id, true);
					if (cb) {
						var o = getObjectById(id); // test whether there is an
													// HTML object element or
													// not
						if (o && typeof o.SetVariable != UNDEF) {
							cbObj.success = true;
							cbObj.ref = o;
						}
						cb(cbObj);
					}
				}
			}
		}
	}

	function getObjectById(objectIdStr) {
		var r = null;
		var o = getElementById(objectIdStr);
		if (o && o.nodeName == "OBJECT") {
			if (typeof o.SetVariable != UNDEF) {
				r = o;
			} else {
				var n = o.getElementsByTagName(OBJECT)[0];
				if (n) {
					r = n;
				}
			}
		}
		return r;
	}

	/*
	 * Requirements for Adobe Express Install - only one instance can be active
	 * at a time - fp 6.0.65 or higher - Win/Mac OS only - no Webkit engines
	 * older than version 312
	 */
	function canExpressInstall() {
		return !isExpressInstallActive && hasPlayerVersion("6.0.65")
				&& (ua.win || ua.mac) && !(ua.wk && ua.wk < 312);
	}

	/*
	 * Show the Adobe Express Install dialog - Reference:
	 * http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=6a253b75
	 */
	function showExpressInstall(att, par, replaceElemIdStr, callbackFn) {
		isExpressInstallActive = true;
		storedCallbackFn = callbackFn || null;
		storedCallbackObj = {
			success : false,
			id : replaceElemIdStr
		};
		var obj = getElementById(replaceElemIdStr);
		if (obj) {
			if (obj.nodeName == "OBJECT") { // static publishing
				storedAltContent = abstractAltContent(obj);
				storedAltContentId = null;
			} else { // dynamic publishing
				storedAltContent = obj;
				storedAltContentId = replaceElemIdStr;
			}
			att.id = EXPRESS_INSTALL_ID;
			if (typeof att.width == UNDEF
					|| (!/%$/.test(att.width) && parseInt(att.width, 10) < 310)) {
				att.width = "310";
			}
			if (typeof att.height == UNDEF
					|| (!/%$/.test(att.height) && parseInt(att.height, 10) < 137)) {
				att.height = "137";
			}
			doc.title = doc.title.slice(0, 47) + " - Flash Player Installation";
			var pt = ua.ie && ua.win ? "ActiveX" : "PlugIn", fv = "MMredirectURL="
					+ win.location.toString().replace(/&/g, "%26")
					+ "&MMplayerType=" + pt + "&MMdoctitle=" + doc.title;
			if (typeof par.flashvars != UNDEF) {
				par.flashvars += "&" + fv;
			} else {
				par.flashvars = fv;
			}
			// IE only: when a SWF is loading (AND: not available in cache) wait
			// for the readyState of the object element to become 4 before
			// removing it,
			// because you cannot properly cancel a loading SWF file without
			// breaking browser load references, also obj.onreadystatechange
			// doesn't work
			if (ua.ie && ua.win && obj.readyState != 4) {
				var newObj = createElement("div");
				replaceElemIdStr += "SWFObjectNew";
				newObj.setAttribute("id", replaceElemIdStr);
				obj.parentNode.insertBefore(newObj, obj); // insert
															// placeholder div
															// that will be
															// replaced by the
															// object element
															// that loads
															// expressinstall.swf
				obj.style.display = "none";
				(function() {
					if (obj.readyState == 4) {
						obj.parentNode.removeChild(obj);
					} else {
						setTimeout(arguments.callee, 10);
					}
				})();
			}
			createSWF(att, par, replaceElemIdStr);
		}
	}

	/*
	 * Functions to abstract and display alternative content
	 */
	function displayAltContent(obj) {
		if (ua.ie && ua.win && obj.readyState != 4) {
			// IE only: when a SWF is loading (AND: not available in cache) wait
			// for the readyState of the object element to become 4 before
			// removing it,
			// because you cannot properly cancel a loading SWF file without
			// breaking browser load references, also obj.onreadystatechange
			// doesn't work
			var el = createElement("div");
			obj.parentNode.insertBefore(el, obj); // insert placeholder div
													// that will be replaced by
													// the alternative content
			el.parentNode.replaceChild(abstractAltContent(obj), el);
			obj.style.display = "none";
			(function() {
				if (obj.readyState == 4) {
					obj.parentNode.removeChild(obj);
				} else {
					setTimeout(arguments.callee, 10);
				}
			})();
		} else {
			obj.parentNode.replaceChild(abstractAltContent(obj), obj);
		}
	}

	function abstractAltContent(obj) {
		var ac = createElement("div");
		if (ua.win && ua.ie) {
			ac.innerHTML = obj.innerHTML;
		} else {
			var nestedObj = obj.getElementsByTagName(OBJECT)[0];
			if (nestedObj) {
				var c = nestedObj.childNodes;
				if (c) {
					var cl = c.length;
					for ( var i = 0; i < cl; i++) {
						if (!(c[i].nodeType == 1 && c[i].nodeName == "PARAM")
								&& !(c[i].nodeType == 8)) {
							ac.appendChild(c[i].cloneNode(true));
						}
					}
				}
			}
		}
		return ac;
	}

	/*
	 * Cross-browser dynamic SWF creation
	 */
	function createSWF(attObj, parObj, id) {
		var r, el = getElementById(id);
		if (ua.wk && ua.wk < 312) {
			return r;
		}
		if (el) {
			if (typeof attObj.id == UNDEF) { // if no 'id' is defined for the
												// object element, it will
												// inherit the 'id' from the
												// alternative content
				attObj.id = id;
			}
			if (ua.ie && ua.win) { // Internet Explorer + the HTML object
									// element + W3C DOM methods do not combine:
									// fall back to outerHTML
				var att = "";
				for ( var i in attObj) {
					if (attObj[i] != Object.prototype[i]) { // filter out
															// prototype
															// additions from
															// other potential
															// libraries
						if (i.toLowerCase() == "data") {
							parObj.movie = attObj[i];
						} else if (i.toLowerCase() == "styleclass") { // 'class'
																		// is an
																		// ECMA4
																		// reserved
																		// keyword
							att += ' class="' + attObj[i] + '"';
						} else if (i.toLowerCase() != "classid") {
							att += ' ' + i + '="' + attObj[i] + '"';
						}
					}
				}
				var par = "";
				for ( var j in parObj) {
					if (parObj[j] != Object.prototype[j]) { // filter out
															// prototype
															// additions from
															// other potential
															// libraries
						par += '<param name="' + j + '" value="' + parObj[j]
								+ '" />';
					}
				}
				el.outerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'
						+ att + '>' + par + '</object>';
				objIdArr[objIdArr.length] = attObj.id; // stored to fix object
														// 'leaks' on unload
														// (dynamic publishing
														// only)
				r = getElementById(attObj.id);
			} else { // well-behaving browsers
				var o = createElement(OBJECT);
				o.setAttribute("type", FLASH_MIME_TYPE);
				for ( var m in attObj) {
					if (attObj[m] != Object.prototype[m]) { // filter out
															// prototype
															// additions from
															// other potential
															// libraries
						if (m.toLowerCase() == "styleclass") { // 'class' is an
																// ECMA4
																// reserved
																// keyword
							o.setAttribute("class", attObj[m]);
						} else if (m.toLowerCase() != "classid") { // filter
																	// out IE
																	// specific
																	// attribute
							o.setAttribute(m, attObj[m]);
						}
					}
				}
				for ( var n in parObj) {
					if (parObj[n] != Object.prototype[n]
							&& n.toLowerCase() != "movie") { // filter out
																// prototype
																// additions
																// from other
																// potential
																// libraries and
																// IE specific
																// param element
						createObjParam(o, n, parObj[n]);
					}
				}
				el.parentNode.replaceChild(o, el);
				r = o;
			}
		}
		return r;
	}

	function createObjParam(el, pName, pValue) {
		var p = createElement("param");
		p.setAttribute("name", pName);
		p.setAttribute("value", pValue);
		el.appendChild(p);
	}

	/*
	 * Cross-browser SWF removal - Especially needed to safely and completely
	 * remove a SWF in Internet Explorer
	 */
	function removeSWF(id) {
		var obj = getElementById(id);
		if (obj && obj.nodeName == "OBJECT") {
			if (ua.ie && ua.win) {
				obj.style.display = "none";
				(function() {
					if (obj.readyState == 4) {
						removeObjectInIE(id);
					} else {
						setTimeout(arguments.callee, 10);
					}
				})();
			} else {
				obj.parentNode.removeChild(obj);
			}
		}
	}

	function removeObjectInIE(id) {
		var obj = getElementById(id);
		if (obj) {
			for ( var i in obj) {
				if (typeof obj[i] == "function") {
					obj[i] = null;
				}
			}
			obj.parentNode.removeChild(obj);
		}
	}

	/*
	 * Functions to optimize JavaScript compression
	 */
	function getElementById(id) {
		var el = null;
		try {
			el = doc.getElementById(id);
		} catch (e) {
		}
		return el;
	}

	function createElement(el) {
		return doc.createElement(el);
	}

	/*
	 * Updated attachEvent function for Internet Explorer - Stores attachEvent
	 * information in an Array, so on unload the detachEvent functions can be
	 * called to avoid memory leaks
	 */
	function addListener(target, eventType, fn) {
		target.attachEvent(eventType, fn);
		listenersArr[listenersArr.length] = [ target, eventType, fn ];
	}

	/*
	 * Flash Player and SWF content version matching
	 */
	function hasPlayerVersion(rv) {
		var pv = ua.pv, v = rv.split(".");
		v[0] = parseInt(v[0], 10);
		v[1] = parseInt(v[1], 10) || 0; // supports short notation, e.g. "9"
										// instead of "9.0.0"
		v[2] = parseInt(v[2], 10) || 0;
		return (pv[0] > v[0] || (pv[0] == v[0] && pv[1] > v[1]) || (pv[0] == v[0]
				&& pv[1] == v[1] && pv[2] >= v[2])) ? true : false;
	}

	/*
	 * Cross-browser dynamic CSS creation - Based on Bobby van der Sluis'
	 * solution: http://www.bobbyvandersluis.com/articles/dynamicCSS.php
	 */
	function createCSS(sel, decl, media, newStyle) {
		if (ua.ie && ua.mac) {
			return;
		}
		var h = doc.getElementsByTagName("head")[0];
		if (!h) {
			return;
		} // to also support badly authored HTML pages that lack a head
			// element
		var m = (media && typeof media == "string") ? media : "screen";
		if (newStyle) {
			dynamicStylesheet = null;
			dynamicStylesheetMedia = null;
		}
		if (!dynamicStylesheet || dynamicStylesheetMedia != m) {
			// create dynamic stylesheet + get a global reference to it
			var s = createElement("style");
			s.setAttribute("type", "text/css");
			s.setAttribute("media", m);
			dynamicStylesheet = h.appendChild(s);
			if (ua.ie && ua.win && typeof doc.styleSheets != UNDEF
					&& doc.styleSheets.length > 0) {
				dynamicStylesheet = doc.styleSheets[doc.styleSheets.length - 1];
			}
			dynamicStylesheetMedia = m;
		}
		// add style rule
		if (ua.ie && ua.win) {
			if (dynamicStylesheet && typeof dynamicStylesheet.addRule == OBJECT) {
				dynamicStylesheet.addRule(sel, decl);
			}
		} else {
			if (dynamicStylesheet && typeof doc.createTextNode != UNDEF) {
				dynamicStylesheet.appendChild(doc.createTextNode(sel + " {"
						+ decl + "}"));
			}
		}
	}

	function setVisibility(id, isVisible) {
		if (!autoHideShow) {
			return;
		}
		var v = isVisible ? "visible" : "hidden";
		if (isDomLoaded && getElementById(id)) {
			getElementById(id).style.visibility = v;
		} else {
			createCSS("#" + id, "visibility:" + v);
		}
	}

	/*
	 * Filter to avoid XSS attacks
	 */
	function urlEncodeIfNecessary(s) {
		var regex = /[\\\"<>\.;]/;
		var hasBadChars = regex.exec(s) != null;
		return hasBadChars && typeof encodeURIComponent != UNDEF ? encodeURIComponent(s)
				: s;
	}

	/*
	 * Release memory to avoid memory leaks caused by closures, fix hanging
	 * audio/video threads and force open sockets/NetConnections to disconnect
	 * (Internet Explorer only)
	 */
	var cleanup = function() {
		if (ua.ie && ua.win) {
			window.attachEvent("onunload", function() {
				// remove listeners to avoid memory leaks
					var ll = listenersArr.length;
					for ( var i = 0; i < ll; i++) {
						listenersArr[i][0].detachEvent(listenersArr[i][1],
								listenersArr[i][2]);
					}
					// cleanup dynamically embedded objects to fix audio/video
					// threads and force open sockets and NetConnections to
					// disconnect
					var il = objIdArr.length;
					for ( var j = 0; j < il; j++) {
						removeSWF(objIdArr[j]);
					}
					// cleanup library's main closures to avoid memory leaks
					for ( var k in ua) {
						ua[k] = null;
					}
					ua = null;
					for ( var l in swfobject) {
						swfobject[l] = null;
					}
					swfobject = null;
				});
		}
	}();

	return {
		/*
		 * Public API - Reference:
		 * http://code.google.com/p/swfobject/wiki/documentation
		 */
		registerObject : function(objectIdStr, swfVersionStr, xiSwfUrlStr,
				callbackFn) {
			if (ua.w3 && objectIdStr && swfVersionStr) {
				var regObj = {};
				regObj.id = objectIdStr;
				regObj.swfVersion = swfVersionStr;
				regObj.expressInstall = xiSwfUrlStr;
				regObj.callbackFn = callbackFn;
				regObjArr[regObjArr.length] = regObj;
				setVisibility(objectIdStr, false);
			} else if (callbackFn) {
				callbackFn( {
					success : false,
					id : objectIdStr
				});
			}
		},

		getObjectById : function(objectIdStr) {
			if (ua.w3) {
				return getObjectById(objectIdStr);
			}
		},
		// XXX added by mdale ( since we know the dom is ready,
		// this works better.
		callDomLoadFunctions : function() {
			callDomLoadFunctions();
		},

		embedSWF : function(swfUrlStr, replaceElemIdStr, widthStr, heightStr,
				swfVersionStr, xiSwfUrlStr, flashvarsObj, parObj, attObj,
				callbackFn) {
			var callbackObj = {
				success : false,
				id : replaceElemIdStr
			};
			if (ua.w3 && !(ua.wk && ua.wk < 312) && swfUrlStr
					&& replaceElemIdStr && widthStr && heightStr
					&& swfVersionStr) {
				setVisibility(replaceElemIdStr, false);
				addDomLoadEvent(function() {
					widthStr += ""; // auto-convert to string
					heightStr += "";
					var att = {};
					if (attObj && typeof attObj === OBJECT) {
						for ( var i in attObj) { // copy object to avoid the
													// use of references,
													// because web authors often
													// reuse attObj for multiple
													// SWFs
							att[i] = attObj[i];
						}
					}
					att.data = swfUrlStr;
					att.width = widthStr;
					att.height = heightStr;
					var par = {};
					if (parObj && typeof parObj === OBJECT) {
						for ( var j in parObj) { // copy object to avoid the
													// use of references,
													// because web authors often
													// reuse parObj for multiple
													// SWFs
							par[j] = parObj[j];
						}
					}
					if (flashvarsObj && typeof flashvarsObj === OBJECT) {
						for ( var k in flashvarsObj) { // copy object to avoid
														// the use of
														// references, because
														// web authors often
														// reuse flashvarsObj
														// for multiple SWFs
							if (typeof par.flashvars != UNDEF) {
								par.flashvars += "&" + k + "="
										+ flashvarsObj[k];
							} else {
								par.flashvars = k + "=" + flashvarsObj[k];
							}
						}
					}
					if (hasPlayerVersion(swfVersionStr)) { // create SWF
						var obj = createSWF(att, par, replaceElemIdStr);
						if (att.id == replaceElemIdStr) {
							setVisibility(replaceElemIdStr, true);
						}
						callbackObj.success = true;
						callbackObj.ref = obj;
					} else if (xiSwfUrlStr && canExpressInstall()) { // show
																		// Adobe
																		// Express
																		// Install
						att.data = xiSwfUrlStr;
						showExpressInstall(att, par, replaceElemIdStr,
								callbackFn);
						return;
					} else { // show alternative content
						setVisibility(replaceElemIdStr, true);
					}
					if (callbackFn) {
						callbackFn(callbackObj);
					}
				});
			} else if (callbackFn) {
				callbackFn(callbackObj);
			}
		},

		switchOffAutoHideShow : function() {
			autoHideShow = false;
		},

		ua : ua,

		getFlashPlayerVersion : function() {
			return {
				major : ua.pv[0],
				minor : ua.pv[1],
				release : ua.pv[2]
			};
		},

		hasFlashPlayerVersion : hasPlayerVersion,

		createSWF : function(attObj, parObj, replaceElemIdStr) {
			if (ua.w3) {
				return createSWF(attObj, parObj, replaceElemIdStr);
			} else {
				return undefined;
			}
		},

		showExpressInstall : function(att, par, replaceElemIdStr, callbackFn) {
			if (ua.w3 && canExpressInstall()) {
				showExpressInstall(att, par, replaceElemIdStr, callbackFn);
			}
		},

		removeSWF : function(objElemIdStr) {
			if (ua.w3) {
				removeSWF(objElemIdStr);
			}
		},

		createCSS : function(selStr, declStr, mediaStr, newStyleBoolean) {
			if (ua.w3) {
				createCSS(selStr, declStr, mediaStr, newStyleBoolean);
			}
		},

		addDomLoadEvent : addDomLoadEvent,

		addLoadEvent : addLoadEvent,

		getQueryParamValue : function(param) {
			var q = doc.location.search || doc.location.hash;
			if (q) {
				if (/\?/.test(q)) {
					q = q.split("?")[1];
				} // strip question mark
		if (param == null) {
			return urlEncodeIfNecessary(q);
		}
		var pairs = q.split("&");
		for ( var i = 0; i < pairs.length; i++) {
			if (pairs[i].substring(0, pairs[i].indexOf("=")) == param) {
				return urlEncodeIfNecessary(pairs[i].substring((pairs[i]
						.indexOf("=") + 1)));
			}
		}
	}
	return "";
},

// For internal usage only
		expressInstallCallback : function() {
			if (isExpressInstallActive) {
				var obj = getElementById(EXPRESS_INSTALL_ID);
				if (obj && storedAltContent) {
					obj.parentNode.replaceChild(storedAltContent, obj);
					if (storedAltContentId) {
						setVisibility(storedAltContentId, true);
						if (ua.ie && ua.win) {
							storedAltContent.style.display = "block";
						}
					}
					if (storedCallbackFn) {
						storedCallbackFn(storedCallbackObj);
					}
				}
				isExpressInstallActive = false;
			}
		}
	};
}();