/**
* EmbedPlayer loader
*/
/**
* Default player module configuration
*/
( function( mw ) {

	mw.setDefaultConfig( {
		// If the player controls should be overlaid on top of the video ( if supported by playback method)
		// can be set to false per embed player via overlayControls attribute
		'EmbedPlayer.OverlayControls' : true,

		// If the iPad should use html controls ( can't use fullscreen or control volume, 
		// but lets you support overlays ie html controls ads etc. )
		'EmbedPlayer.EnableIpadHTMLControls': false, 
		
		'EmbedPlayer.LibraryPage': 'http://www.kaltura.org/project/HTML5_Video_Media_JavaScript_Library',

		// A default apiProvider ( ie where to lookup subtitles, video properties etc )
		// NOTE: Each player instance can also specify a specific provider
		"EmbedPlayer.ApiProvider" : "local",

		// What tags will be re-written to video player by default
		// Set to empty string or null to avoid automatic video tag rewrites to embedPlayer
		"EmbedPlayer.RewriteTags" : "video,audio,playlist",

		// Default video size ( if no size provided )
		"EmbedPlayer.DefaultSize" : "400x300",

		// If the video player should attribute kaltura
		"EmbedPlayer.KalturaAttribution" : true,

		// The attribution button
		'EmbedPlayer.AttributionButton' :{
			'title' : 'Kaltura html5 video library',
		 	'href' : 'http://www.kaltura.org/project/HTML5_Video_Media_JavaScript_Library',
			// Style icon to be applied
			'class' : 'kaltura-icon',
			// An icon image url ( should be a 12x12 image or data url )
			'iconurl' : false
		},
		
		// If the player should wait for metadata like video size and duration, before trying to draw
		// the player interface. 
		'EmbedPlayer.WaitForMeta' : true,
		
		// Set the browser player warning flag displays warning for non optimal playback
		"EmbedPlayer.ShowNativeWarning" : true,

		// If fullscreen is global enabled.
		"EmbedPlayer.EnableFullscreen" : true,

		// If mwEmbed should use the Native player controls
		// this will prevent video tag rewriting and skinning
		// useful for devices such as iPad / iPod that
		// don't fully support DOM overlays or don't expose full-screen
		// functionality to javascript
		"EmbedPlayer.NativeControls" : false,

		// If mwEmbed should use native controls on mobile safari
		"EmbedPlayer.NativeControlsMobileSafari" : true,


		// The z-index given to the player interface during full screen ( high z-index )
		"EmbedPlayer.fullScreenZIndex" : 999998,

		// The default share embed mode ( can be "object" or "videojs" )
		//
		// "iframe" will provide a <iframe tag pointing to mwEmbedFrame.php
		// 		Object embedding should be much more compatible with sites that
		//		let users embed flash applets
		// "videojs" will include the source javascript and video tag to
		//	 	rewrite the player on the remote page DOM
		//		Video tag embedding is much more mash-up friendly but exposes
		//		the remote site to the mwEmbed javascript and can be a xss issue.
		"EmbedPlayer.ShareEmbedMode" : 'iframe',

		// Default player skin name
		"EmbedPlayer.SkinName" : "mvpcf",

		// Number of milliseconds between interface updates
		'EmbedPlayer.MonitorRate' : 250,

		// If the embedPlayer should accept arguments passed in from iframe postMessages calls
		'EmbedPlayer.EnalbeIFramePlayerServer' : false,
		
		// If embedPlayer should support server side temporal urls for seeking options are 
		// flash|always|none default is support for flash only.
		'EmbedPlayer.EnableURLTimeEncoding' : 'flash',
		
		// The domains which can read and send events to the video player
		'EmbedPLayer.IFramePlayer.DomainWhiteList' : '*',
		
		// If the iframe should send and receive javascript events across domains via postMessage 
		'EmbedPlayer.EnableIframeApi' : false
	} );
	
	/**
	 * The base source attribute checks also see:
	 * http://dev.w3.org/html5/spec/Overview.html#the-source-element
	 */
	mw.setDefaultConfig( 'EmbedPlayer.SourceAttributes', [
		// source id
		'id',

		// media url
		'src',

		// Title string for the source asset
		'title',

		// boolean if we support temporal url requests on the source media
		'URLTimeEncoding',

		// Media has a startOffset ( used for plugins that
		// display ogg page time rather than presentation time
		'startOffset',

		// A hint to the duration of the media file so that duration
		// can be displayed in the player without loading the media file
		'durationHint',

		// Media start time
		'start',

		// Media end time
		'end',

		// If the source is the default source
		'default',
		
		// Title of the source
		'title',
		
		// titleKey ( used for api lookups TODO move into mediaWiki specific support
		'titleKey'
	] );
	
	/*
	 * The default video attributes supported by embedPlayer
	 */
	mw.setDefaultConfig('EmbedPlayer.Attributes', {
		/*
		 * Base html element attributes:
		 */

		// id: Auto-populated if unset
		"id" : null,

		// Width: alternate to "style" to set player width
		"width" : null,

		// Height: alternative to "style" to set player height
		"height" : null,

		/*
		 * Base html5 video element attributes / states also see:
		 * http://www.whatwg.org/specs/web-apps/current-work/multipage/video.html
		 */

		// Media src URI, can be relative or absolute URI
		"src" : null,

		// Poster attribute for displaying a place holder image before loading
		// or playing the video
		"poster" : null,

		// Autoplay if the media should start playing
		"autoplay" : false,

		// Loop attribute if the media should repeat on complete
		"loop" : false,

		// If the player controls should be displayed
		"controls" : true,

		// Video starts "paused"
		"paused" : true,

		// ReadyState an attribute informs clients of video loading state:
		// see: http://www.whatwg.org/specs/web-apps/current-work/#readystate
		"readyState" : 0,

		// Loading state of the video element
		"networkState" : 0,

		// Current playback position
		"currentTime" : 0,

		// Previous player set time
		// Lets javascript use $j('#videoId').get(0).currentTime = newTime;
		"previousTime" : 0,

		// Previous player set volume
		// Lets javascript use $j('#videoId').get(0).volume = newVolume;
		"previousVolume" : 1,

		// Initial player volume:
		"volume" : 0.75,

		// Caches the volume before a mute toggle
		"preMuteVolume" : 0.75,

		// Media duration: Value is populated via
		// custom durationHint attribute or via the media file once its played
		"duration" : null,

		// Mute state
		"muted" : false,

		/**
		 * Custom attributes for embedPlayer player: (not part of the html5
		 * video spec)
		 */

		// Default video aspect ratio
		'videoAspect' : '4:3',

		// Start time of the clip
		"start" : 0,

		// End time of the clip
		"end" : null,

		// A apiTitleKey for looking up subtitles, credits and related videos
		"apiTitleKey" : null,

		// The apiProvider where to lookup the title key
		"apiProvider" : null,

		// If the player controls should be overlaid
		// ( Global default via config EmbedPlayer.OverlayControls in module
		// loader.js)
		"overlaycontrols" : true,

		// Attribute to use 'native' controls
		"usenativecontrols" : false,

		// If the player should include an attribution button:
		'attributionbutton' : true,

		// ROE url ( for xml based metadata )
		// also see: http://wiki.xiph.org/ROE
		"roe" : null,

		// If serving an ogg_chop segment use this to offset the presentation
		// time
		// ( for some plugins that use ogg page time rather than presentation
		// time )
		"startOffset" : 0,

		// Thumbnail (same as poster)
		"thumbnail" : null,

		// Source page for media asset ( used for linkbacks in remote embedding
		// )
		"linkback" : null,

		// If the download link should be shown
		"download_link" : true,

		// Content type of the media
		"type" : null
	} );

	// Add class file paths
	mw.addResourcePaths( {
		"mw.EmbedPlayer"	: "mw.EmbedPlayer.js",

		"mw.EmbedPlayerKplayer"	: "mw.EmbedPlayerKplayer.js",
		"mw.EmbedPlayerGeneric"	: "mw.EmbedPlayerGeneric.js",
		"mw.EmbedPlayerHtml" : "mw.EmbedPlayerHtml.js",
		"mw.EmbedPlayerJava": "mw.EmbedPlayerJava.js",
		"mw.EmbedPlayerNative"	: "mw.EmbedPlayerNative.js",

		"mw.EmbedPlayerVlc" : "mw.EmbedPlayerVlc.js",

		"mw.PlayerControlBuilder" : "skins/mw.PlayerControlBuilder.js",

		"mw.style.EmbedPlayer" : "skins/mw.style.EmbedPlayer.css",

		"mw.style.PlayerSkinKskin" 	: "skins/kskin/mw.style.PlayerSkinKskin.css",

		"mw.PlayerSkinKskin"		: "skins/kskin/mw.PlayerSkinKskin.js",

		"mw.PlayerSkinMvpcf"		: "skins/mvpcf/mw.PlayerSkinMvpcf.js",
		"mw.style.PlayerSkinMvpcf" 	: "skins/mvpcf/mw.style.PlayerSkinMvpcf.css",

		"mw.IFramePlayerApiServer" : "mw.IFramePlayerApiServer.js",
		"mw.IFramePlayerApiClient" : "mw.IFramePlayerApiClient.js"
	} );

	/**
	* Check the current DOM for any tags in "EmbedPlayer.RewriteTags"
	*/
	mw.documentHasPlayerTags = function() {
		var rewriteTags = mw.getConfig( 'EmbedPlayer.RewriteTags' );
		if( $j( rewriteTags ).length != 0 ) {
			return true;
		}

		var tagCheckObject = { 'hasTags' : false };
		$j( mw ).trigger( 'LoaderEmbedPlayerCheckForPlayerTags',
				[ tagCheckObject ]);

		return tagCheckObject.hasTags;
	};

	/**
	* Add a DOM ready check for player tags
	*
	* We use mw.addSetupHook instead of mw.ready so that
	* mwEmbed player is setup before any other mw.ready calls
	*/
	mw.addSetupHook( function( callback ) {
		mw.rewritePagePlayerTags( callback );
	});

	mw.rewritePagePlayerTags = function( callback ) {
		var rewriteCount = mw.documentHasPlayerTags()
		mw.log( 'EmbedPlayer:: Document::' + rewriteCount);
		if( rewriteCount ) {
			var rewriteElementCount = 0;

			// Set each player to loading ( as early on as possible )
			$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).each( function( index, element ){

				// Assign an the element an ID ( if its missing one )
				if ( $j( element ).attr( "id" ) == '' ) {
					$j( element ).attr( "id", 'v' + ( rewriteElementCount++ ) );
				}
				// Add an absolute positioned loader
				$j( element )
					.getAbsoluteOverlaySpinner()
					.attr('id', 'loadingSpinner_' + $j( element ).attr('id') )
					.addClass( 'playerLoadingSpinner' );

			});
			// Load the embedPlayer module ( then run queued hooks )
			mw.load( 'EmbedPlayer', function ( ) {
				mw.log("EmbedPlayer:: do rewrite players:" + $j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).length );
				// Rewrite the EmbedPlayer.RewriteTags with the
				$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).embedPlayer( callback );
			})
		} else {
			callback();
		}
	};
	
	/**
	* Add the module loader function:
	*/
	mw.addModuleLoader( 'EmbedPlayer', function() {
		var _this = this;
		// Hide videonojs class
		$j( '.videonojs' ).hide();

		// Set up the embed video player class request: (include the skin js as well)
		var dependencyRequest = [
			[
				'mw.EmbedPlayer'
			],
			[
			 	'mw.PlayerControlBuilder',
				'$j.fn.hoverIntent',
				'mw.style.EmbedPlayer',
				'$j.cookie',
				// Add JSON lib if browsers does not define "JSON" natively
				'JSON',
				'$j.ui',
				'$j.widget'
			],
			[
				'$j.ui.mouse',
				'$j.fn.menu',
				'mw.style.jquerymenu',
				'$j.ui.slider'
			]
		];

		// Pass every tag being rewritten through the update request function
		$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).each( function(inx, playerElement) {
			mw.embedPlayerUpdateLibraryRequest( playerElement, dependencyRequest[ 1 ] )
		} );

		// Add PNG fix code needed:
		if ( $j.browser.msie && $j.browser.version < 7 ) {
			dependencyRequest[0].push( '$j.fn.pngFix' );
		}

		// Do short detection, to avoid extra player library request in ~most~ cases.
		//( If browser is firefox include native, if browser is IE include java )
		if( $j.browser.msie ) {
			dependencyRequest[0].push( 'mw.EmbedPlayerJava' );
		}

		// Safari gets slower load since we have to detect ogg support
		if( !!document.createElement('video').canPlayType && !$j.browser.safari ) {
			dependencyRequest[0].push( 'mw.EmbedPlayerNative' )
		}
		// Check if the iFrame player server is enabled:
		//alert('ifmra' + mw.getConfig('EmbedPlayer.EnableIframeApi'));
		if ( mw.getConfig('EmbedPlayer.EnableIframeApi') ) {
			dependencyRequest[0].push('mw.EmbedPlayerNative');
			dependencyRequest[0].push('$j.postMessage');
			dependencyRequest[0].push('mw.IFramePlayerApiServer');
		}

		// Return the set of libs to be loaded
		return dependencyRequest;
	} );

	/**
	 * Takes a embed player element and updates a request object with any
	 * dependent libraries per that tags attributes.
	 *
	 * For example a player skin class name could result in adding some
	 *  css and javascirpt to the player library request.
	 *
	 * @param {Object} playerElement The tag to check for library dependent request classes.
	 * @param {Array} dependencyRequest The library request array
	 */
	mw.embedPlayerUpdateLibraryRequest = function(playerElement, dependencyRequest ){
		var skinName = $j( playerElement ).attr( 'class' );
		// Set playerClassName to default if unset or not a valid skin
		if( ! skinName || $j.inArray( skinName.toLowerCase(), mw.validSkins ) == -1 ){
			skinName = mw.getConfig( 'EmbedPlayer.SkinName' );
		}
		skinName = skinName.toLowerCase();
		// Add the skin to the request
		var skinCaseName = skinName.charAt(0).toUpperCase() + skinName.substr(1);
		// The skin js:
		if( $j.inArray( 'mw.PlayerSkin' + skinCaseName, dependencyRequest ) == -1 ){
			dependencyRequest.push( 'mw.PlayerSkin' + skinCaseName );
		}
		// The skin css
		if( $j.inArray( 'mw.style.PlayerSkin' + skinCaseName, dependencyRequest ) == -1 ){
			dependencyRequest.push( 'mw.style.PlayerSkin' + skinCaseName );
		}

		// Check if the iFrame api server should be loaded ( iframe api is on ):
		if( mw.getConfig('EmbedPlayer.EnableIframeApi') ){
			dependencyRequest.push(	'mw.IFramePlayerApiServer' );
		}


		// Allow extension to extend the request.
		$j( mw ).trigger( 'LoaderEmbedPlayerUpdateRequest',
				[ playerElement, dependencyRequest ] );
	}

} )( window.mw );
