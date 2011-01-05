/*@cc_on@if(@_jscript_version<9){'video audio source track'.replace(/\w+/g,function(n){document.createElement(n)})}@end@*/

/**
* Kaltura html5 library loader 
* For more info on mwEmbed / kaltura html5 library see: 
* http://www.kaltura.org/project/HTML5_Video_Media_JavaScript_Library
* 
* HTML5 Library usage is driven by html5 attributes see: 
* http://www.whatwg.org/specs/web-apps/current-work/multipage/video.html
* 
* Kaltura Configuration options are set via mw.setConfig( option, value ) or
* mw.setConfig( {json set of option value pairs } );
* 
* Some config options and their default values: ( can be set via mw.setConfig( name, value ); ) 
* 
*	// Enable analytics tracking for html5 devices
*	'Kaltura.EnableAnalytics' : true
*
*	// Base url for your api
*	'Kaltura.ServiceUrl' : 'http://www.kaltura.com'
*
*	// Path to kaltura api 
*	'Kaltura.ServiceBase' : '/api_v3/index.php?service=',
*
*	// The CDN url that hosts your assets.
*	'Kaltura.CdnUrl' : 'http://cdn.kaltura.com'
*
*	// If the html5 library should be loaded when there are video tags in the page.  
*	'Kaltura.LoadScriptForVideoTags' : true
*
*	// If the iframe should expose a javascript api emulating the video tag bindings and api
*	// lets you treat the iframe id like a video tag ie: 
*	// $j('#iframeid').get(0).play() 
*	//   and 
*	// $j('#iframeid').bind('ended', function(){ .. end playback event ... }
*	'EmbedPlayer.EnableIframeApi' : false
*/
var kURID = '1.2h';
// Static script loader url: 
var SCRIPT_LOADER_URL = 'http://www.kaltura.org/apis/html5lib/mwEmbed/ResourceLoader.php';
var SCRIPT_FORCE_DEBUG = false;
var FORCE_LOAD_JQUERY = false;

// These Lines are for local testing: 
SCRIPT_FORCE_DEBUG = true;
SCRIPT_LOADER_URL = 'http://local.trunk/lib/js/html5lib/ResourceLoader.php';

if( typeof console != 'undefined' && console.log ) {
	console.log( 'Kaltura MwEmbed Loader Version: ' + kURID );
}

// Define mw ( if not already set ) 
if( !window['mw'] ){
	window['mw'] = {};
}

// Url parameter to enable debug mode
if( document.URL.indexOf('debugKalturaPlayer=true') != -1 ){
	SCRIPT_FORCE_DEBUG = true;
}
if( document.URL.indexOf('debugKalturaForceJquery=true') != -1 ){
	FORCE_LOAD_JQUERY = true
}

// Define the DOM ready flag
var kAlreadyRunDomReadyFlag = false;

// Try and override the swfObject at runtime
// In case it was included before mwEmbedLoader and the embedSWF call is inline 
kOverideSwfObject();

// Setup preMwEmbedReady queue
if( !preMwEmbedReady ){
	var preMwEmbedReady = [];
}
// Wrap mw.ready to preMwEmbedReady values
if( !mw.ready){
	mw.ready = function( fn ){
		// if running an iframe rewrite without code update we should just run the ready function directly: 
		if( preMwEmbedConfig['Kaltura.IframeRewrite'] && !preMwEmbedConfig['EmbedPlayer.EnableIframeApi'] ){
			fn();
			return ;
		}
		
		preMwEmbedReady.push( fn );
		// Check if mw.ready was called after the dom is ready:
		if( kAlreadyRunDomReadyFlag ){
			kCheckAddScript();
		}
	}
}
// Setup a preMwEmbedConfig var
if( ! preMwEmbedConfig ) {
	var preMwEmbedConfig = {};
}
if( !mw.setConfig ){
	mw.setConfig = function( set, value ){
		var valueQueue = {};
		if( typeof value != 'undefined'  ) {
			preMwEmbedConfig[ set	] = value;
		} else if ( typeof set == 'object' ){
			for( var i in set ){
				preMwEmbedConfig[ i ] = set[i];
			}
		}
	}
}

// Set url based config:
if( document.URL.indexOf('forceMobileHTML5') != -1 ){
	mw.setConfig( 'forceMobileHTML5', true );
}
function kDoIframeRewriteList( rewriteObjects ){
	var options;
	for(var i=0;i < rewriteObjects.length; i++){
		options = { width: rewriteObjects[i].width, height: rewriteObjects[i].height }
		kalturaIframeEmbed( rewriteObjects[i].id, rewriteObjects[i].kSettings, options );
	}
}
function kalturaIframeEmbed( replaceTargetId, kEmbedSettings , options){
	
	var iframeSrc = SCRIPT_LOADER_URL.replace('ResourceLoader.php', 'mwEmbedFrame.php');
	var kalturaAttributeList = { 'uiconf_id':1, 'entry_id':1, 'wid':1, 'p':1};
	for(var attrKey in kEmbedSettings ){
		if( attrKey in kalturaAttributeList ){
			iframeSrc+= '/' + attrKey + '/' + encodeURIComponent( kEmbedSettings[attrKey] );  
		}
	}
	
	// Package in the source page url for iframe message checks.
	
	// Add the parentUrl to the iframe config:
	preMwEmbedConfig['EmbedPlayer.IframeParentUrl'] = document.URL;
	
	// Encode the configuration into the iframe hash url: 
	iframeSrc+= '#' + encodeURIComponent( 
		JSON.stringify( { 'mwConfig' : preMwEmbedConfig } )
	);
	var targetNode = document.getElementById( replaceTargetId );
	var parentNode = targetNode.parentNode;
	var iframe = document.createElement('iframe');
	iframe.src = iframeSrc;
	iframe.id = replaceTargetId;
	iframe.width = (options.width) ? options.width : '100%';
	iframe.height = (options.height) ? options.height : '100%';
	iframe.style.border = '0px';
		
	parentNode.replaceChild(iframe, targetNode );
	/*
	$j('#' + replaceTargetId ).replaceWith(
		$j('<iframe />').attr({
			'src' : iframeSrc,
			'id' : replaceTargetId,
			'width' : options.width,
			'height' : options.height
		}).css({
			'border' : '0px'
		})
	)
	*/
}
// Test if swfObject exists, try and override its embed method to wrap html5 rewrite calls. 
function kOverideSwfObject(){
	var doEmbedSettingsWrite = function ( kEmbedSettings, replaceTargetId, widthStr, heightStr ){
		// Add a ready event to re-write: 
		mw.ready(function(){
			// Setup the embedPlayer attributes
			var embedPlayerAttributes = {
				'kwidgetid' : kEmbedSettings.wid,
				'kuiconfid' : kEmbedSettings.uiconf_id
			}
			var width = ( widthStr )? parseInt( widthStr ) : $j('#' + replaceTargetId ).width();
			var height = ( heightStr)? parseInt( heightStr ) : $j('#' + replaceTargetId ).height();
			
			if( kEmbedSettings.entry_id ){
				embedPlayerAttributes.kentryid = kEmbedSettings.entry_id;				
				embedPlayerAttributes.poster = kGetEntryThumbUrl( {
					'width' : width,
					'height' : height,
					'entry_id' :  kEmbedSettings.entry_id,
					'partner_id': kEmbedSettings.p 
				})
			}
			if( preMwEmbedConfig['Kaltura.IframeRewrite'] ){
				kalturaIframeEmbed( replaceTargetId, kEmbedSettings , { width: width, height: height } );
			} else {
				$j('#' + replaceTargetId ).empty()
				.css({
					'width' : width,
					'height' : height
				}).embedPlayer( embedPlayerAttributes );
			}
		});
	}
	// SWFObject v 1.5 
	if( window['SWFObject']  && !window['SWFObject'].prototype['originalWrite']){
		window['SWFObject'].prototype['originalWrite'] = window['SWFObject'].prototype.write;
		window['SWFObject'].prototype['write'] = function( targetId ){
			var _this = this;
			mw.ready(function(){
				var flashVarsSet = ( _this.params.flashVars )? _this.params.flashVars.split('&'): [];
				flashVars = {};
				for( var i =0 ;i < flashVarsSet.length; i ++){
					var flashVar = flashVarsSet[i].split('=');
					if( flashVar[0] &&   flashVar[1]){
						flashVars[ flashVar[0] ] = flashVar[1];
					}
				}
				var kEmbedSettings = kGetKalturaEmbedSettings( _this.attributes.swf, flashVars);
				if( kIsHTML5FallForward() && kEmbedSettings.uiconf_id ){
					doEmbedSettingsWrite( kEmbedSettings, targetId, _this.attributes.width, _this.attributes.height);
				} else {
					// use the original flash player embed:  
					_this.originalWrite( targetId );
				}
			});
		}
	}
	// SWFObject v 2.0
	if( window['swfobject'] && !window['swfobject']['originalEmbedSWF'] ){
		window['swfobject']['originalEmbedSWF'] = window['swfobject']['embedSWF'];
		// Override embedObject for our own ends
		window['swfobject']['embedSWF'] = function( swfUrlStr, replaceElemIdStr, widthStr,
				heightStr, swfVersionStr, xiSwfUrlStr, flashvarsObj, parObj, attObj, callbackFn)
		{
			kAddReadyHook(function(){
				var kEmbedSettings = kGetKalturaEmbedSettings( swfUrlStr, flashvarsObj);
				// Check if mobile safari:
				if( kIsHTML5FallForward() && kEmbedSettings.wid ){
					doEmbedSettingsWrite( kEmbedSettings, replaceElemIdStr, widthStr,  heightStr);
				} else {
					// Else call the original EmbedSWF with all its arguments 
					window['swfobject']['originalEmbedSWF']( swfUrlStr, replaceElemIdStr, widthStr,
							heightStr, swfVersionStr, xiSwfUrlStr, flashvarsObj, parObj, attObj, callbackFn )
				}
			});
		}
	}
}

// Check DOM for Kaltura embeds ( fall forward ) 
// && html5 video tag ( for fallback & html5 player interface )
function kCheckAddScript(){
	// If user javascript is using mw.ready add script
	if( preMwEmbedReady.length ) {
		kAddScript();
		return ;
	}
	if( preMwEmbedConfig['Kaltura.LoadScriptForVideoTags'] !== false ){
		// If document includes audio or video tags
		if( document.getElementsByTagName('video').length != 0
			|| document.getElementsByTagName('audio').length != 0 ) {
			kAddScript();
			return ;
		}
	}
	
	// If document includes kaltura embed tags && isMobile safari: 
	if ( kIsHTML5FallForward() &&  kGetKalturaPlayerList().length ) {
		// Check for Kaltura objects in the page
		kAddScript();
	} else {
		// Restore the jsCallbackReady ( we are not rewriting )
		if( window.KalturaKDPCallbackReady ){
			window.jsCallbackReady = window.KalturaKDPCallbackReady;
			if( window.KalturaKDPCallbackAlreadyCalled ){
				window.jsCallbackReady();
			}
		}
	}
}
// Fallforward by default prefers flash, uses html5 only if flash is not installed or not avaliable 
function kIsHTML5FallForward(){
	// Check for a mobile html5 user agent:	
	if ( (navigator.userAgent.indexOf('iPhone') != -1) || 
		(navigator.userAgent.indexOf('iPod') != -1) || 
		(navigator.userAgent.indexOf('iPad') != -1) ||
		(navigator.userAgent.indexOf('Android 2.') != -1) || 
		// Force html5 for chrome / desktop safari
		( preMwEmbedConfig['forceMobileHTML5'] === true )
	){
		return true;
	}
	// if the browser supports flash ( don't use html5 )
	if( kSupportsFlash() ){
		return false;
	}
	// No flash return true if the browser supports html5 video tag with basic support for canPlayType:
	if( kSupportsHTML5() ){
		return true;
	}
	// if we have the iframe enabled return true ( since the iframe will output a fallback link
	// even if the client does not support html5 or flash )
	if( preMwEmbedConfig['Kaltura.IframeRewrite'] ){
		return true;
	}
	
	// No video tag or flash, or iframe, normal "install flash" user flow )
	return false;
}
// basic html5 support check ( note Android 2.2 and bellow fail to return anything on canPlayType
// but is part of the mobile check above. 
function kSupportsHTML5(){
	var dummyvid = document.createElement( "video" );
	// Blackberry is evil in its response to canPlayType calls. 
	if( navigator.userAgent.indexOf('BlackBerry') != -1 ){
		return false ;
	}
	if( dummyvid.canPlayType ) {
		return true;
	}
	return false;
}
function kSupportsFlash(){
	// Check if the client does not have flash and has the video tag
	if ( navigator.mimeTypes && navigator.mimeTypes.length > 0 ) {
		for ( var i = 0; i < navigator.mimeTypes.length; i++ ) {
			var type = navigator.mimeTypes[i].type;
			var semicolonPos = type.indexOf( ';' );
			if ( semicolonPos > -1 ) {
				type = type.substr( 0, semicolonPos );
			}
			if ( type == 'application/x-shockwave-flash' ) {
				// flash is installed 				
				return true;
			}
		}
	}

	// for IE: 
	var hasObj = true;
	if( typeof ActiveXObject != 'undefined' ){
		try {
			var obj = new ActiveXObject( 'ShockwaveFlash.ShockwaveFlash' );
		} catch ( e ) {
			hasObj = false;
		}
		if( hasObj ){
			return true;
		}
	}
	return false;
}

// Add the kaltura html5 mwEmbed script
var kAddedScript = false;
function kAddScript(){
	if( kAddedScript ){
		return ;
	}
	kAddedScript = true;

	var jsRequestSet = [];
	if( typeof window.jQuery == 'undefined' || FORCE_LOAD_JQUERY ) {
		jsRequestSet.push( ['window.jQuery'] )
	}
	// Check if we are using an iframe ( load only the iframe api client ) 
	if( preMwEmbedConfig['Kaltura.IframeRewrite'] ) {
		if( preMwEmbedConfig['EmbedPlayer.EnableIframeApi'] ){
			jsRequestSet.push( [ 'mwEmbed', 'mw.EmbedPlayerNative', '$j.postMessage',  'mw.IFramePlayerApiClient', 'JSON' ] );
			kLoadJsRequestSet( jsRequestSet );
		} else {
			kDoIframeRewriteList( kGetKalturaPlayerList() )	;
		}
		return ;
	}
	
	// Add all the classes needed for video 
	jsRequestSet.push( [	 
	    'mwEmbed',
		// core skin: 
		'mw.style.mwCommon',
		// embed player:
		'mw.EmbedPlayer', 
		'mw.style.EmbedPlayer',
		'mw.PlayerControlBuilder',
		// default skin: 
		'mw.PlayerSkinMvpcf',
		'mw.style.PlayerSkinMvpcf',
		// common playback methods:
		'mw.EmbedPlayerNative',
		'mw.EmbedPlayerKplayer',
		'mw.EmbedPlayerJava',
		// jQuery lib
		'$j.ui',  
		'$j.widget',
		'$j.ui.mouse',
		'$j.fn.hoverIntent',
		'$j.cookie',
		'JSON',
		'$j.ui.slider',
		'$j.fn.menu',
		'mw.style.jquerymenu',
		// Timed Text module
		'mw.TimedText',
		'mw.style.TimedText'
	]);
	
	// Add the jquery ui skin: 
	if( preMwEmbedConfig['jQueryUISkin'] ){
		jsRequestSet.push( [ 'mw.style.ui_' + preMwEmbedConfig['jQueryUISkin'] ] );
	} else {
		jsRequestSet.push( [ 'mw.style.ui_kdark' ] );
	}
	
	var objectPlayerList = kGetKalturaPlayerList();
	// Check if we are doing object rewrite ( add the kaltura library ) 
	if ( kIsHTML5FallForward() || objectPlayerList.length ){
		// Kaltura client libraries:
		jsRequestSet.push( [
		  'MD5',
		  "mw.KApi",
		  'mw.KWidgetSupport',
		  'mw.KAnalytics', 
		  'mw.KDPMapping',
		  'mw.KAds',
		  'mw.AdTimeline', 
		  'mw.AdLoader', 
		  'mw.VastAdParser',
		  'controlbarLayout',
		  'faderPlugin',
		  'watermarkPlugin',
		  'adPlugin'
		]);
		// kaltura playlist support ( so small relative to client libraries that we always include it )	
		jsRequestSet.push([
		   'mw.Playlist',
		   'mw.PlaylistHandlerMediaRss',
		   'mw.PlaylistHandlerKaltura', 
		   'mw.PlaylistHandlerKalturaRss'
		]);
	}
	kLoadJsRequestSet( jsRequestSet );
};

function kAppendScriptUrl(url, callback) {
	var script = document.createElement( 'script' );
	script.type = 'text/javascript';
	script.src = url;
	// xxx fixme integrate with new callback system ( resource loader rewrite )
	if( callback ){
		script.onload = callback;
	}
	document.getElementsByTagName('head')[0].appendChild( script );	
}

function kLoadJsRequestSet( jsRequestSet, callback ){
	var url = SCRIPT_LOADER_URL + '?class=';
	// Request each jsRequestSet
	for( var i = 0; i < jsRequestSet.length ; i++ ){
		url+= jsRequestSet[i].join(',') + ',';
	}
	url+= '&urid=' + kURID;
	url+= '&uselang=en';
	if ( SCRIPT_FORCE_DEBUG ){
		url+= '&debug=true';
	}
	
	kAppendScriptUrl(url, callback);
}


/**
* DOM-ready setup ( similar to jQuery.ready )  
*/
var kReadyHookSet = [];
function kAddReadyHook( callback ){
	if( kAlreadyRunDomReadyFlag ){
		callback();
	} else {
		kReadyHookSet.push( callback );
	}
}
function kRunMwDomReady(){
	// run dom ready with a 1ms timeout to prevent sync execution in browsers like chrome
	// Async call give a chance for configuration variables to be set
	setTimeout(function(){
		kAlreadyRunDomReadyFlag  = true;
		while( kReadyHookSet.length ){
			kReadyHookSet.shift()();
		}
		kOverideSwfObject();
		kCheckAddScript();
	},1 );
}
// Check if already ready: 
if ( document.readyState === "complete" ) {
	kRunMwDomReady();
}

// Cleanup functions for the document ready method
if ( document.addEventListener ) {
	DOMContentLoaded = function() {
		document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
		kRunMwDomReady();
	};

} else if ( document.attachEvent ) {
	DOMContentLoaded = function() {
		// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
		if ( document.readyState === "complete" ) {
			document.detachEvent( "onreadystatechange", DOMContentLoaded );
			kRunMwDomReady();
		}
	};
}
// Mozilla, Opera and webkit nightlies currently support this event
if ( document.addEventListener ) {
	// Use the handy event callback
	document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );
	// A fallback to window.onload, that will always work
	window.addEventListener( "load", kRunMwDomReady, false );

// If IE event model is used
} else if ( document.attachEvent ) {
	// ensure firing before onload,
	// maybe late but safe also for iframes
	document.attachEvent("onreadystatechange", DOMContentLoaded);
	// A fallback to window.onload, that will always work
	window.attachEvent( "onload", kRunMwDomReady );

	// If IE and not a frame
	// continually check to see if the document is ready
	var toplevel = false;

	try {
		toplevel = window.frameElement == null;
	} catch(e) {
	}
	if ( document.documentElement.doScroll && toplevel ) {
		doScrollCheck();
	}
}
// The DOM ready check for Internet Explorer
function doScrollCheck() {
	if ( kAlreadyRunDomReadyFlag ) {
		return;
	}
	try {
		// If IE is used, use the trick by Diego Perini
		// http://javascript.nwbox.com/IEContentLoaded/
		document.documentElement.doScroll("left");
	} catch( error ) {
		setTimeout( doScrollCheck, 1 );
		return;
	}
	// and execute any waiting functions
	kRunMwDomReady();
}

/**
 * Get the list of embed objects on the page that are 'kaltura players'
 * Copied from kalturaSupport loader mw.getKalturaPlayerList  
 */
kGetKalturaPlayerList = function(){
	var kalturaPlayers = [];
	// check all objects for kaltura compatible urls 
	var objectList = document.getElementsByTagName('object');
	var tryAddKalturaEmbed = function( url ){
		var settings = kGetKalturaEmbedSettings( url );
		if( settings && settings.uiconf_id && settings.wid ){
			objectList[i].kSettings = settings;
			kalturaPlayers.push(  objectList[i] );
			return true
		}
		return false;
	}
	for( var i =0; i < objectList.length; i++){
		if( objectList[i].getAttribute('data') ){
			if( tryAddKalturaEmbed( objectList[i].getAttribute('data') ) )
				continue;
		}
		var paramTags = objectList[i].getElementsByTagName('param');
		for( var j = 0; j < paramTags.length; j++){
			if( paramTags[j].getAttribute('name') == 'data'
				||
				paramTags[j].getAttribute('name') == 'src' )
			{
				if( tryAddKalturaEmbed( paramTags[j].getAttribute('value') ) )
					break;
			}
		}
	}
	
	return kalturaPlayers;
}

function kGetEntryThumbUrl( entry ){
	var kCdn = ( preMwEmbedConfig['Kaltura.CdnUrl'] ) ? preMwEmbedConfig['Kaltura.CdnUrl'] : 'http://cdnakmi.kaltura.com';
	return kCdn + '/p/' + entry.partner_id + '/sp/' +
		entry.partner_id + '00/thumbnail/entry_id/' + entry.entry_id + '/width/' +
		entry.height + '/height/' + entry.width;
}
// Copied from kalturaSupport loader mw.getKalturaEmbedSettings  
kGetKalturaEmbedSettings = function( swfUrl, flashvars ){
	if( !flashvars )
		flashvars= {};
	var dataUrlParts = swfUrl.split('/');
	var embedSettings = {};
	// Search backward for key value pairs
	var prevUrlPart = null;
	while( dataUrlParts.length ){
		var curUrlPart =  dataUrlParts.pop();
		switch( curUrlPart ){
			case 'p':
				embedSettings.wid = '_' + prevUrlPart;
				embedSettings.p = prevUrlPart;
			break;
			case 'wid':
				embedSettings.wid = prevUrlPart;
				embedSettings.p = prevUrlPart.replace(/_/,'');
			break;
			case 'entry_id':
				embedSettings.entry_id = prevUrlPart;
			break;
			case 'uiconf_id':
				embedSettings.uiconf_id = prevUrlPart;
			break;
			case 'cache_st':
				embedSettings.cacheSt = prevUrlPart;
			break;
		}
		prevUrlPart = curUrlPart;
	}
	// Add in Flash vars embedSettings ( they take precedence over embed url )
	for( var i in  flashvars){
		embedSettings[ i.toLowerCase() ] = flashvars[i];
	}
	// Normalize the entryid to url request equivalents
	if( embedSettings['entryid'] ){
		embedSettings['entry_id'] =  embedSettings['entryid'];
	}
	return embedSettings;
};

/**
 * To support kaltura kdp mapping override
 */
var checkForKDPCallback = function(){
	if( typeof window.jsCallbackReady != 'undefined'){	
		window.KalturaKDPCallbackReady = window.jsCallbackReady;
		window.jsCallbackReady = function(){
			window.KalturaKDPCallbackAlreadyCalled = true;
		};
	}
}
// Check inline and when the dom is ready:
checkForKDPCallback()
kAddReadyHook( checkForKDPCallback );
