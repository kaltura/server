/**
 * This file exposes the functionality of mwEmbed to wikis
 * that do not yet have mwEmbed enabled
 */
var urlparts = getRemoteEmbedPath();
var mwEmbedHostPath = urlparts[0];
var mwRemoteVersion = 'r182';
var mwUseScriptLoader = true;

// Log the mwRemote version makes it easy to debug cache issues
if( window.console ){
	window.console.log( 'mwEmbed:remote: ' + mwRemoteVersion );
}

// Make sure mw exists::
if( typeof window.mw == 'undefined'){
	window.mw = {};
}

// Setup up request Params:
var reqParts = urlparts[1].substring( 1 ).split( '&' );
var mwReqParam = { };
for ( var i = 0; i < reqParts.length; i++ ) {
	var p = reqParts[i].split( '=' );
	if ( p.length == 2 ) {
		mwReqParam[ p[0] ] = p[1];
	}
}
// Allow the document.URL to trigger the debug flag with "debug=true" in its url
if( document.URL.indexOf( 'debug=true' ) !== -1 ){
	mwReqParam['debug'] = true;
}
// Allow the document.URL to trigger the embedplayer mode:
if( document.URL.indexOf( 'embedplayer=yes' ) !== -1 ){
	mwReqParam['embedplayer'] = 'yes';
}
// Check if debug mode and disable script grouping
if( mwReqParam['debug'] ) {
	mwUseScriptLoader = false;
}

mwReqParam['debug'] = true;
mwUseScriptLoader = true;

// Setup up some globals to wrap mwEmbed mw.ready and mw.setConfig functions

//Setup preMwEmbedReady queue
if( !preMwEmbedReady ){
	var preMwEmbedReady = [];
}
// Wrap mw.ready to preMwEmbedReady values
if( !mw.ready){
	mw.ready = function( fn ){
		preMwEmbedReady.push( fn );
	};
}
// Setup a preMwEmbedConfig var
if( ! preMwEmbedConfig ) {
	var preMwEmbedConfig = [];
}
if( !mw.setConfig ){
	mw.setConfig = function( set, value ){
		var valueQueue = {};
		if( typeof value != 'undefined' ) {
			preMwEmbedConfig[ set	] = value;
		} else if ( typeof set == 'object' ){
			for( var i in set ){
				preMwEmbedConfig[ i ] = set[i];
			}
		}
	};
}


/*******************************
* Wikimedia specific config
********************************/
// The player should attribute kaltura
mw.setConfig( 'EmbedPlayer.KalturaAttribution', true );

// The sequencer clips should include attribution 'edit sequence' link on pause
mw.setConfig( 'Sequencer.KalturaPlayerEditOverlay', true );

// If the swarm p2p transport stream should be used for clients that have it installed
mw.setConfig( 'SwarmTransport.Enable', true );

// Sequencer should only load asset from upload.wikimedia.org:
mw.setConfig( 'SmilPlayer.AssetDomainWhiteList', ['upload.wikimedia.org'] );

// Gadgets should append withJS to requests where we need to re-invoke the gadget on a different page
// NOTE this is REQUIRED for apiProxy to work across projects where the user has not universally enabled the gadget
mw.setConfig( 'Mw.AppendWithJS', 'withJS=MediaWiki:MwEmbed.js');

// Allow all wikimedia RegEx domains matches to support api-proxy requests
// NOTE remember to put $ at the end of the domain or it would match en.wikipedia.org.evil.com
mw.setConfig( 'ApiProxy.DomainWhiteList',
	[ /wikimedia\.org$/ , /wikipedia\.org$/ , /wiktionary.org$/ , /wikinews.org$/ , /wikibooks.org$/ , /wikisource.org$/ , /wikiversity.org$/ , /wikiquote.org$/ ]
);

// Legacy Add media wizard config: 
if( typeof mwAddMediaConfig == 'undefined'){
   mwAddMediaConfig = {};
}
mwAddMediaConfig['enabled_providers'] = [ 'wiki_commons', 'upload' ];


// Special embedplayer handler ( iframe )
if( mwReqParam['embedplayer'] == 'yes' ){
	// No fullscreen in iframe for now:
	mw.setConfig( 'EmbedPlayer.EnableFullscreen', false );
	// No subtitle editor ( cross domain issues ) 
	mw.setConfig( 'MiroSubs.EnableUniversalSubsEditor', false );
	// No subtile upload either ( for now ) 
	mw.setConfig( 'TimedText.showAddTextLink', false );
}


// Use jQuery document ready
if( window.jQuery ){
	jQuery( document ).ready( doPageSpecificRewrite );
} else {
	addOnloadHook( function() {
		doPageSpecificRewrite();
	} );
}


/**
* Page specific rewrites for mediaWiki
*/
function doPageSpecificRewrite() {
	// Deal with multiple doPageSpecificRewrite
	if( window.ranMwRewrites ){
		return ;
	}
	window.ranMwRewrites = true;

	// Add media wizard ( only if not on a sequence page
	if ( wgAction == 'edit' || wgAction == 'submit' ) {
		if( wgPageName.indexOf( "Sequence:" ) != 0 ){
			// Add a timeout to give a chance for wikieditor to build out.
			loadMwEmbed( [
				'mw.RemoteSearchDriver',
				'mw.ClipEdit',
			 	'mw.style.ClipEdit',
				'$j.fn.textSelection',
				'$j.ui',
				'$j.widget',
				'$j.ui.mouse',
				'$j.ui.button',
				'$j.ui.position',
				'$j.ui.progressbar',
				'$j.ui.dialog',
				'$j.ui.draggable',
				'$j.ui.sortable',
				'$j.ui.datepicker'
			], function() {
					mw.load( mwEmbedHostPath + '/remotes/AddMediaWizardEditPage.js?' + mwGetReqArgs() );
			} );
		}
	}

	// Timed text display:
	if ( wgPageName.indexOf( "TimedText:" ) === 0
			&&
			( 	document.URL.indexOf('&diff=') == -1
				&&
				document.URL.indexOf('?diff=') == -1
			)
		){
		if( wgAction == 'view' ){
			var orgBody = mwSetPageToLoading();
			//load the "player" ( includes call to loadMwEmbed )

			// Add a timeout to give a chance for ui core to build out ( bug with replace jquery ui )
			mwLoadPlayer(function(){
				// Now load MediaWiki TimedText Remote:
				mw.load( 'RemoteMwTimedText',function(){
					//Setup the remote configuration
					var myRemote = new RemoteMwTimedText( {
						'action': wgAction,
						'title' : wgTitle,
						'target': '#bodyContent',
						'orgBody': orgBody
					});
					// Update the UI
					myRemote.updateUI();
				} );
			} );
			return ;
		}
	}
	
	// Remote Sequencer
	if( wgPageName.indexOf( "Sequence:" ) === 0 ){
		//console.log( 'spl: ' + typeof mwSetPageToLoading );
		// If on a view page set content to "loading"
		if( ( wgAction == 'view' || wgAction == 'edit' )
			&&
			(
				document.URL.indexOf('&diff=') == -1
				&&
				document.URL.indexOf('?diff=') == -1
			)
		){
			if( wgAction == 'view' ){
				var catLinksHtml = document.getElementById('catlinks');
				mwSetPageToLoading();
			}
			if( wgAction == 'edit' ){
				mwAddCommonStyleSheet();
				var body = document.getElementById( 'bodyContent' );
				body.innerHTML = "<div class=\"loadingSpinner sequenceLoader\"></div>" + body.innerHTML;
			}
			if( window.mwSequencerRemote ){
				window.mwSequencerRemote.drawUI();
			} else {
				mwLoadPlayer(function(){
					// wait for wikieditor to do its thing
					$j('#editform,.mw-newarticletext,#toolbar').hide();
					$j('.sequenceLoader').hide();

					window.mwSequencerRemote = new mw.MediaWikiRemoteSequencer({
						'action': wgAction,
						'titleKey' : wgPageName,
						'target' : '#bodyContent',
						'catLinks' : catLinksHtml
					});
					window.mwSequencerRemote.drawUI();
				});
			}

		}
		return ;
	}


	// Upload page -> Firefogg / upload API / uploadWizard integration
	if ( wgPageName == "Special:Upload" ) {
		var scriptUrl = null;
		var scriptName = null;
		var libraries = [];
		scriptName = 'uploadPage.js';
		libraries = [
			'mw.UploadInterface',
			'mw.Firefogg',
			'$j.ui',
			'$j.widget',
			'$j.ui.mouse',
			'$j.ui.position',
			'$j.ui.progressbar',
			'$j.ui.dialog',
			'$j.ui.draggable'
		];
		var scriptUrl = mwEmbedHostPath + '/remotes/' + scriptName + '?' + mwGetReqArgs();
		loadMwEmbed(libraries, function() {
			mw.load( scriptUrl );
		} );
		return ;
	}

	// Special api proxy page
	if ( wgPageName == 'MediaWiki:ApiProxy' ) {
		var wgEnableIframeApiProxy = true;
		loadMwEmbed( [ 'mw.ApiProxy' ], function(){
			mw.load( mwEmbedHostPath + '/modules/ApiProxy/ApiProxyPage.js?' + mwGetReqArgs() );
		});
		return ;
	}

	// Special api proxy page for nested callback of hash url
	// Can be replaced with:
	if ( wgPageName == 'MediaWiki:ApiProxyNestedCb' ) {
		// Note top.mw.ApiProxy.nested frame needs to be on the same domain
		top.mw.ApiProxy.nested( window.location.href.split("#")[1] || false );
		return ;
	}

	// OggHandler rewrite for view pages:
	var vidIdList = [];
	
	
	// Check for special "embedplayer" yes and set relevent config: 	
	if( mwReqParam['embedplayer'] == 'yes' ){	
		mwAddCommonStyleSheet();
		
		// Only rewrite the main embed player
		var playerDiv = document.getElementById( 'file' ).childNodes[0].cloneNode( true );	
		document.body.style.overflow = 'hidden';
		document.body.innerHTML = '<div id="loadingPlayer" style="height:100%;width:100%"><div class="loadingSpinner" style="position:absolute;left:50%;top:50%"></div></div>';		
		document.body.appendChild( playerDiv );		
	}
	
	var divs = document.getElementsByTagName( 'div' );
	for ( var i = 0; i < divs.length; i++ ) {
		if ( divs[i].id && divs[i].id.substring( 0, 11 ) == 'ogg_player_' ) {
			vidIdList.push( divs[i].getAttribute( "id" ) );
		}
	}
	if ( vidIdList.length > 0 ) {

		// Reverse order the array so videos at the "top" get swapped first:
		vidIdList = vidIdList.reverse();
		mwLoadPlayer( function(){
			// Check for flat file page:
			var flatFilePretext = "File:Sequence-";
			if( wgPageName.indexOf(flatFilePretext ) === 0
					&&
				wgPageName.indexOf('.ogv') !== -1 )
			{
				var sequenceTitle = 'Sequence:' + wgPageName.substring( flatFilePretext.length, wgPageName.length - 4 );
				window.mwSequencerRemote = new mw.MediaWikiRemoteSequencer({
					'action': wgAction,
					'titleKey' : sequenceTitle,
					'target' : '#bodyContent'
				});
				window.mwSequencerRemote.showViewFlattenedFile();
			}

			// Do utility rewrite of OggHandler content:
			mw.ready(function(){
				rewrite_for_OggHandler( vidIdList );
			});
		} );
		return ;
	}

	// IF we did not match any rewrite ( but we do have a ready function ) load mwEmbed
	if( preMwEmbedReady.length ){
		loadMwEmbed( function(){
			// mwEmbed loaded
		});
	}
}
/*
* Sets the mediaWiki content to "loading"
*/
function mwSetPageToLoading(){
	mwAddCommonStyleSheet();
	var body = document.getElementById( 'bodyContent' );
	var oldBodyHTML = body.innerHTML;
	body.innerHTML = '<div class="loadingSpinner"></div>';
	return oldBodyHTML;
}
function mwAddCommonStyleSheet(){
	importStylesheetURI( mwEmbedHostPath + '/skins/common/mw.style.mwCommon.css?' + mwGetReqArgs() );
	// Set the style to defined ( so that when mw won't load the style sheet again)
	if( !mw.style ){
		mw.style = { 'mwCommon' : true };
	} else {
		mw.style['mwCommon'] = true;
	}
}
/**
* Similar to the player loader in /modules/embedPlayer/loader.js
* ( front-loaded to avoid extra requests )
*/
function mwLoadPlayer( callback ){
	// The jsPlayerRequest includes both javascript and style sheets for the embedPlayer
	var jsPlayerRequest = [
		'$j.ui',
		'$j.widget',
		'$j.ui.mouse',

		'$j.ui.button',
		'$j.ui.draggable',
		'$j.ui.position',
		'$j.ui.resizable',
		'$j.ui.slider',

		'$j.ui.dialog',

		'$j.cookie',
		'mw.EmbedPlayer',
		'mw.style.EmbedPlayer',

		'mw.PlayerControlBuilder',
		'$j.fn.hoverIntent',
		'JSON',

		'mw.PlayerSkinKskin',
		'mw.style.PlayerSkinKskin',

		'$j.fn.menu',
		'mw.style.jquerymenu',

		// Timed Text module
		'mw.TimedText',
		'mw.style.TimedText',

		// mwSwarmTransport module
		'mw.SwarmTransport',

		// Sequencer remote:
		'mw.MediaWikiRemoteSequencer',
		'mw.style.SequencerRemote'
	];
	// Quick sniff use java if IE and native if firefox
	// ( other browsers will run detect and get on-demand )
	if (navigator.userAgent.indexOf("MSIE") != -1){
		jsPlayerRequest.push( 'mw.EmbedPlayerJava' );
	}

	if ( navigator.userAgent && navigator.userAgent.indexOf("Firefox") != -1 ){
		jsPlayerRequest.push( 'mw.EmbedPlayerNative' );
	}

	loadMwEmbed( jsPlayerRequest, function() {
		// hide the novideojs if present
		$j( '.videonojs' ).hide();
		mw.ready( callback );
	});
}

/**
* This will be depreciated when we update to OggHandler
* @param {Object} vidIdList List of video ids to process
*/
function rewrite_for_OggHandler( vidIdList ) {
	
	function procVidId( vidId ) {
		// Don't process empty vids
		if ( !vidId ){
			return ;
		}

		tag_type = 'video';

		// Check type:
		var $pimg = $j( '#' + vidId + ' img:first' );
		var pwidth = $pimg.width();
		var imgSring = $pimg.attr('src').split('/').pop();
		if( $pimg.attr('src') && imgSring == 'play.png' || imgSring == 'fileicon-ogg.png' ){
			tag_type = 'audio';
			poster_attr = '';
			pheight = 0;
		}else{
			var poster_attr = ' poster = "' + $pimg.attr( 'src' ) + '" ';
			var pheight = $pimg.attr( 'height' );
		}

		// Parsed values:
		var src = '';
		var duration_attr = '';
		var rewriteHTML = $j( '#' + vidId ).html();

		if( rewriteHTML == ''){
			mw.log( "Error: empty rewrite html" );
			return ;
		}else{
			//mw.log(" rewrite: " + rewriteHTML + "\n of type: " + typeof rewriteHTML);
		}
		var re = new RegExp( /videoUrl(&quot;:?\s*)*([^&]*)/ );
		src = re.exec( rewriteHTML )[2];

		var apiTitleKey = src.split( '/' );
		apiTitleKey = decodeURI( apiTitleKey[ apiTitleKey.length - 1 ] );

		var re = new RegExp( /length(&quot;:?\s*)*([^,]*)/ );
		var dv = parseFloat( re.exec( rewriteHTML )[2] );
		duration_attr = ( dv )? 'durationHint="' + dv + '" ': '';

		var re = new RegExp( /offset(&quot;:?\s*)*([^,&]*)/ );
		offset = re.exec( rewriteHTML );
		var offset_attr = ( offset && offset[2] )? 'startOffset="' + offset[2] + '" ' : '';

		// Check if file is from commons and therefore should explicitly set apiProvider to commons:
		var apiProviderAttr = ( src.indexOf( 'wikipedia\/commons' ) != -1 )?'apiProvider="commons" ': '';

		// If in a gallery box or filehistory we will be displaying the video larger in a lightbox
		if( $j( '#' + vidId ).parents( '.gallerybox,.filehistory' ).length ){
			pwidth = 400;
			// Update the width to 400 and keep scale
			if( pheight != 0 ) {
				pheight = pwidth * ( $j( '#' + vidId + ' img' ).height() / $j( '#' + vidId + ' img' ).width() );
			}
		}

		if ( src ) {
			var html_out = '';

			var common_attr = ' id="mwe_' + vidId + '" ' +
				'apiTitleKey="' + apiTitleKey + '" ' +
				'src="' + src + '" ' +
				apiProviderAttr +
				duration_attr +
				offset_attr + ' ' +
				'class="kskin" ';

			if ( tag_type == 'audio' ) {
				if( pwidth < 250 ){
					pwidth = 250;
				}
				html_out = '<audio ' + common_attr + 'style="width:' + pwidth + 'px;height:0px;"></audio>';
			} else {
				html_out = '<video ' + common_attr +
				poster_attr + ' style="width:' + pwidth + 'px;height:' + pheight + 'px;">' +
				'</video>';
			}

			var checkForIframePlayerParam = function(){
				// Add full window binding if embedplayer flag set: 
				if( mwReqParam['embedplayer'] == 'yes' ){
					$j('#loadingPlayer').remove();
					$j('body').css('overflow', 'hidden');	
					$j( '#mwe_' + vidId ).get(0).resizePlayer({
						'width' : $j(window).width(),
						'height' : $j(window).height()
					});
					$j(window).unbind().resize(function(){
						$j( '#mwe_' + vidId ).get(0).resizePlayer({
							'width' : $j(window).width(),
							'height' : $j(window).height()
						}); 
					});
				}
			}
			
			
			// If the video is part of a "gallery box" use light-box linker instead
			if( $j( '#' + vidId ).parents( '.gallerybox,.filehistory' ).length ){
				$j( '#' + vidId ).after(
					 $j( '<div />')
					.css({
						'width' : $pimg.attr('width' ),
						'height' :$pimg.attr( 'height' ),
						'position' : 'relative',
						'background-color' : '#FFF'
					})
					.addClass( 'k-player' )
					.append(
						// The poster image
						$j( '<img />' )
						.css( {
							'width' : '100%',
							'height' : '100%'
						})
						.attr( 'src', $pimg.attr('src') ),

						// A play button:
						$j( '<div />' )
						.css({
							'position' : 'absolute',
							'top' : ( parseInt( $pimg.attr( 'height' ) ) /2 ) -25,
							'left' :  ( parseInt( $pimg.attr( 'width' ) ) /2 ) -35
						})

						.addClass( 'play-btn-large' )
						.buttonHover()
						.click( function(){
							var _this = this;
							var dialogHeight = ( pheight == 0 	)? 175 :
												( pheight + 130 );
							var buttons = {};
							buttons[ gM( 'mwe-ok' ) ] = function(){
								// close the dialog
								$j(this).dialog( 'close' ).remove();
							};
							var $dialog = mw.addDialog( {
								'title' : decodeURIComponent( apiTitleKey.replace(/_/g, ' ') ),
								'content' : html_out,
								'buttons' : buttons,
								'height' : dialogHeight,
								'width' : 430,
								'close': function(event, ui) {
									var embedPlayer = $j( '#mwe_' + vidId ).get(0);
									// stop the player before we close the dialog
									if( embedPlayer ) {
										embedPlayer.stop();
									}
								}
							});

							// Update the embed code to use the mwEmbed player:							
							$j( '#mwe_' + vidId ).embedPlayer( { 'autoplay' : true }, function(){
								var embedPlayer = $j( '#mwe_' + vidId ).get(0);
								// Show the control bar for two seconds (auto play is confusing without it )
								embedPlayer.controlBuilder.showControlBar();
								// hide the controls if they should they are overlayed on the video
								if( embedPlayer.controlBuilder.checkOverlayControls() ){
									setTimeout( function(){
										embedPlayer.controlBuilder.hideControlBar();
									}, 4000 );
								}
								checkForIframePlayerParam();
							});
						})
					)
				).remove();

			} else {
				// Set the video tag inner html remove extra player
				$j( '#' + vidId ).html( html_out );
				$j( '#mwe_' + vidId ).embedPlayer( checkForIframePlayerParam );
			}							
			
			// Issue an async request to rewrite the next clip
			if ( vidIdList.length != 0 ) {
				setTimeout( function() {
					procVidId( vidIdList.pop() );
				}, 1 );
			}
		}
	};
	// Process current top item in vidIdList
	procVidId( vidIdList.pop() );
}

/**
* Get the remote embed Path
*/
function getRemoteEmbedPath() {
	//debugger;
	for ( var i = 0; i < document.getElementsByTagName( 'script' ).length; i++ ) {
		var s = document.getElementsByTagName( 'script' )[i];
		if ( s.src.indexOf( '/mediaWiki.js' ) != - 1 ) {
			var reqStr = '';
			var scriptPath = '';
			if ( s.src.indexOf( '?' ) != - 1 ) {
				reqStr = s.src.substr( s.src.indexOf( '?' ) );
				scriptPath = s.src.substr( 0, s.src.indexOf( '?' ) ).replace( '/mediaWiki.js', '' );
			} else {
				scriptPath = s.src.replace( '/mediaWiki.js', '' );
			}
			// Use the external_media_wizard path:
			return [scriptPath + '/..', reqStr];
		}
	}
}

/**
* Get the request arguments
*/
function mwGetReqArgs() {
	var rurl = '';
	if ( mwReqParam['debug'] ){
		rurl += 'debug=true&';
	}

	if ( mwReqParam['uselang'] ){
		rurl += 'uselang=' + mwReqParam['uselang'] + '&';
	}

	if ( mwReqParam['urid'] ) {
		rurl += 'urid=' + mwReqParam['urid'];
	} else {
		// Make sure to use an urid
		// This way remoteMwEmbed can control version of code being requested
		rurl += 'urid=' + mwRemoteVersion;
	}
	return rurl;
}

/**
* Load the mwEmbed library:
*
* @param {mixed} function or classSet to preload
* 	classSet saves round trips to the server by grabbing things we will likely need in the first request.
* @param {callback} function callback to be called once mwEmbed is ready
*/
function loadMwEmbed( classSet, callback ) {
	if( typeof classSet == 'function') {
		callback = classSet;
	}
	if ( typeof MW_EMBED_VERSION != 'undefined' ) {
		mw.load( classSet, callback);
		return ;
	}
	var doLoadMwEmbed = function(){
		// Inject mwEmbed
		if ( mwUseScriptLoader ) {
			var rurl = mwEmbedHostPath + '/ResourceLoader.php?class=';
	
			var coma = '';
	
	
			// Add jQuery too if we need it:
			if ( typeof window.jQuery == 'undefined'
				||
				// force load jquery if version 1.3.2 ( issues with '1.3.2' .data handling )
				jQuery.fn.jquery == '1.3.2')
			{
				rurl += 'window.jQuery';
				coma = ',';
			}
			// Add Core mwEmbed lib ( if not already defined )
			if( typeof MW_EMBED_VERSION == 'undefined' ){
				rurl += coma + 'mwEmbed,mw.style.mwCommon';
				coma = ',';
			}
	
			// Add requested classSet to scriptLoader request
			for( var i=0; i < classSet.length; i++ ){
				var cName = classSet[i];
				// always include our version of the library ( too many crazy conflicts with old library versions )
				rurl += ',' + cName;
			}
	
			// Add the remaining arguments
			rurl += '&' + mwGetReqArgs();
			$j.getScript( rurl,  callback);
		} else {
	
			// Force load jQuery for debug mode
			var jQueryRequested = false;
			$j.getScript(mwEmbedHostPath + '/libraries/jquery/jquery-1.4.2.js?' + mwGetReqArgs(), function(){
				// load mwEmbed js
				$j.getScript(  mwEmbedHostPath + '/ResourceLoader.php?class=window.jQuery,mwEmbed&&' + mwGetReqArgs(), function(){
					// Load the class set as part of mwReady callback
					mw.load( classSet, function(){
						callback();
					});
				});
			});
		}
	};
	
	// Wait for jQuery ui to be loaded ( so that we can override it ) 
	// Usability loads jqueryUI asynchronously. We need a to wait until its defined
	// so that we can override it. )
	// if not defined after 1 second assume it is not going to be loaded 
	var waitForJqueryUi = function(){
		if( !window.jQuery.ui ){
			setTimeout( waitForJqueryUi, 10);
		} else {
			doLoadMwEmbed();
		}
	};
	waitForJqueryUi();
}

/**
 * Check if the gadget is installed
 * run after mwEmbed setup so $j and mw interface is available:
 */
function mwCheckForGadget(){
	//mw.log('mwCheckForGadget');
	if( $j('#mwe-gadget-button').length != 0){
		//Gadget button already in dom
		return false;
	}

	var scripts = document.getElementsByTagName( 'script' );

	// Check for document parameter withJS and ignore found gadget
	if( typeof getParamValue == 'undefined' && typeof getURLParamValue == 'undefined'){
		return false;
	}

	for( var i = 0 ; i < scripts.length ; i++ ){
		if (
			scripts[i].src
			&& scripts[i].src.indexOf( 'MediaWiki:Gadget-mwEmbed.js' ) !== -1
		){
			//mw.log( 'gadget already installed: ' + scripts[i].src );
			// Gadget found / enabled
			return false;
		}
	}

	// No gadget found add enable button:
	mw.log('gadget not installed, show install menu');
	var $gadgetBtn = $j.button({
			'text' : gM( 'mwe-enable-gadget' ),
			'icon': 'check'
		})
		.css({
			'font-size': '90%'
		})
		.click(function (){
			if( !wgUserName ){
				$j( this )
				.after( gM('mwe-must-login-gadget',
					wgArticlePath.replace(
						'$1', 'Special:UserLogin?returnto=' + wgPageName ) )
					)
				.remove();
				return false;
			}

			// Else Add loader
			$j( this )
			.after(
				$j('<div />')
				.attr( 'id', 'gadget-form-loader' )
				.loadingSpinner()
			)
			.remove();
			// Load gadgets form:
			mwSubmitGadgetPref( 'mwEmbed' );

			// return false to not follow link
			return false;
		} );

	// Add the $gadgetBtn before the first heading:
	$j('#firstHeading').before(
		$j('<div />')
		.attr('id','mwe-gadget-button')
		.css({
			'margin': '10px'
		}).html(
			$gadgetBtn
		)
	);
}
function mwSubmitGadgetPref( gadget_id ){
	$j.get( wgArticlePath.replace('$1', 'Special:Preferences'), function( pageHTML ){
		// get the form
		var form = mwGetFormFromPage ( pageHTML );
		if(!form){
			return false;
		}
		if( mwCheckFormDatagadget( form.data, gadget_id ) ){
			mw.log( gadget_id + ' is already enabled' );
			return false;
		}

		// add mwEmbed to the formData
		form.data.push( {
			'name' : 'wpgadgets[]',
			'value' : gadget_id
		} );

		// Submit the preferences
		$j.post( form.url, form.data, function( pageHTML ){
			var form = mwGetFormFromPage ( pageHTML );
			if(!form){
				return false;
			}
			if( mwCheckFormDatagadget(form.data, gadget_id ) ){
				//update the loader
				$j('#gadget-form-loader')
				.text( gM( 'mwe-enable-gadget-done' ) );
			}
		} );
	});
}
function mwGetFormFromPage( pageHTML ){
	var form = {};
	$j( pageHTML ).find('form').each( function( ){
		form.url = $j( this ).attr('action');
		if( form.url.indexOf( 'Special:Preferences') !== -1 ){
			form.data = $j( this ).serializeArray();
			// break out of loop
			return false;
		}
	});
	if( form.data )
		return form;
	mw.log("Error: could not get form data");
	return false;
}
function mwCheckFormDatagadget( formData, gadget_id ){
	for(var i =0; i < formData.length ; i ++ ){
		if( formData[i].name == 'wpgadgets[]' ){
			if( formData[i].value == gadget_id ){
				return true;
			}
		}
	}
	return false;
}