/**
* Stop-gap for php sequencer support does some transformations
* to page views to support sequence namespace
*
* Supports basic "sequencer" functionality as a javascript rewrite system.
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {

mw.addMessageKeys( [
	"mwe-sequencer-no-sequence-create",
	"mwe-sequencer-create-sequence",
	"mwe-sequencer-edit-sequence",
	"mwe-sequencer-embed-sequence",
	"mwe-sequencer-embed-sequence-desc",
	"mwe-sequencer-loading-sequencer",

	"mwe-sequencer-visual-editor",
	"mwe-sequencer-text-editor-warn",
	"mwe-sequencer-restore-text-edit",

	"mwe-sequencer-loading-publish-render",

	"mwe-sequencer-not-published",
	"mwe-sequencer-published-out-of-date",
	"mwe-sequencer-you-can-edit-this-video",
	"mwe-sequencer-using-kaltura-video-editor"
]);

/* exported functions */

/**
 * Special wrapper for sequence links when in 'extension' mode
 * there is no need for js rewrite helpers
 *
 *  @param {String} url The url to be wrapped
 */
mw.getRemoteSequencerLink = function( url ){
	if( mw.getConfig( 'Mw.AppendWithJS' ) ){
		if( url.indexOf('?') == -1){
			url+='?';
		} else {
			url+='&';
		}
		url+= mw.getConfig( 'Mw.AppendWithJS' );
	}
	return url;
};

// Add player pause binding if config is set::
$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {
	if( mw.getConfig( 'Sequencer.KalturaPlayerEditOverlay' )){

		var embedPlayerId = $j( embedPlayer ).attr( 'id' );

		// hide if the main menu is requested
		$j( embedPlayer ).bind( 'displayMenuOverlay', function(){
			$j( embedPlayer ).siblings( '.kalturaEditOverlay' ).fadeOut( 'fast' );
		});

		$j( embedPlayer ).bind( 'pause', function() {
			// Don't display if near the end of playback ( causes double fade in conflict with ended event )
			mw.remoteSequencerAddEditOverlay( embedPlayerId );

			// xxx should use getter setter
			embedPlayer.controlBuilder.displayOptionsMenuFlag = true;
			return true;
		});

		$j( embedPlayer ).bind( 'ended', function( onDoneAction ){
			if( embedPlayer.currentTime != 0 ){
				return ;
			}
			// pause event should fire
			mw.remoteSequencerAddEditOverlay( embedPlayerId );

			// show the credits screen after 3.5 seconds
			setTimeout(function(){
				$j( embedPlayer ).siblings( '.kalturaEditOverlay' ).fadeOut( 'fast' );
				embedPlayer.$interface.find('.k-menu').fadeIn('fast');
			}, 3500)

			// On end runs before interface bindings (give the dom 10ms to build out the menu )
			setTimeout(function(){
				$j( embedPlayer ).siblings( '.k-menu' ).hide();
			},10)
		});
		$j( embedPlayer ).bind( 'play', function(){
			$j( embedPlayer ).siblings( '.kalturaEditOverlay' ).fadeOut( 'fast' );
			embedPlayer.controlBuilder.displayOptionsMenuFlag = false;
			return true ;
		});

	}
});
mw.remoteSequencerAddEditOverlay = function( embedPlayerId ){
	var embedPlayer = $j( '#' + embedPlayerId ).get(0);

	// Check if we can do the overlay::
	if( !embedPlayer.supports['overlays']
	|| embedPlayer.instanceOf.toLowerCase() == 'smil'
	|| embedPlayer.getHeight() < 180
	|| embedPlayer.getWidth() < 240
	// Require that the video is a flat sequence special key: Sequence-
	|| embedPlayer.apiTitleKey.indexOf('Sequence-') != 0
	){
		return ;
	}

	if(! $j( '#' + embedPlayerId ).siblings( '.kalturaEditOverlay' ).length ){

		var seqTitle = embedPlayer.apiTitleKey
			.replace( 'Sequence-', 'Sequence:')
		// strip the extension
		seqTitle = seqTitle.substr(0, seqTitle.length -4 );
		// not ideal details page builder but 'should work' ::
		var editLink = mw.getApiProviderURL( embedPlayer.apiProvider ).replace( 'api.php', 'index.php' );
		editLink = mw.getRemoteSequencerLink (
				mw.replaceUrlParams( editLink,
					{
						'title' : seqTitle,
						'action' : 'edit'
					}
				)
			);
		var kalturaLinkAttr = {
				'href': 'http://kaltura.com',
				'target' : '_new',
				'title' : gM('mwe-embedplayer-kaltura-platform-title')
			};
		$j( '#' + embedPlayerId ).before(
			$j( '<div />' )
			.addClass( 'kalturaEditOverlay' )
			.css({
				'position' : 'absolute',
				'width' : '100%',
				'top' : '0px',
				'bottom' : '22px',
				'background' : 'none repeat scroll 0 0 #FFF',
				'color' : 'black',
				'opacity': 0.9,
				'z-index': 1
			})
			.append(
				$j('<div />')
				.css({
					'position' : 'absolute',

					'width' : '200px',
					'margin-left' : '-100px',

					'height' : '100px',
					'margin-top' : '-50px',

					'top' : '50%',
					'left' : '50%',
					'text-align' : 'center'
				})
				.append(
					gM('mwe-sequencer-you-can-edit-this-video',
						$j('<a />')
						.attr({
							'href': editLink,
							'target': '_new'
						})
						.click(function(){
							// load the editor in-place if on the same domain as the sequence
							if( editLink == '#' ){
								if( ! window.mwSequencerRemote ){
									window.mwSequencerRemote = new mw.MediaWikiRemoteSequencer({
										'title' : embedPlayer.apiTitleKey
									});
								}
								window.mwSequencerRemote.showEditor();
								return false;
							}
							return true;
						})
					),
					$j( '<br />' )
					,
					gM( 'mwe-sequencer-using-kaltura-video-editor',
						$j('<a />')
						.attr( kalturaLinkAttr )
					)
					,
					$j('<a />')
					.attr( kalturaLinkAttr )
					.append(
						$j('<div />')
						.addClass('mwe-kalturaLogoLarge')
					)
				)
			)
			.hide() // hide .kalturaEditOverlay by default
		);
	}

	$j( '#' + embedPlayerId ).siblings( '.kalturaEditOverlay' )
	.fadeIn('fast');
}
mw.MediaWikiRemoteSequencer = function( options ) {
	return this.init( options );
};
mw.MediaWikiRemoteSequencer.prototype = {
	/**
	* @constructor
	* @param {Object} options RemoteMwSequencer options
	*/
	init: function( options ) {
		if( ! options.action || ! options.titleKey || ! options.target){
			mw.log("Error sequence remote missing action, title or target");
		}
		this.action = options.action;
		this.titleKey = options.titleKey;
		this.target = options.target;
		this.catLinks = options.catLinks;
	},

	drawUI: function() {
		// Check page action
		if( this.action == 'view' ) {
			this.showViewUI();
		}
		if( this.action == 'edit' ){
			this.showEditUI();
		}
	},
	/**
	* Check page for sequence
	* if not present give link to "create" one.
	*/
	showViewUI: function() {
		var _this = this;
		if( wgArticleId == 0 ) {
			// Update create button
			$j('#ca-edit a')
				.html( $j('<span />').text( gM('mwe-sequencer-create-sequence' ) ) )
				.click(function(){
					_this.showEditor();
					return false;
				})

			$j( this.target ).html(
				gM("mwe-sequencer-no-sequence-create",
					$j('<a />').attr('href','#').click(function() {
						_this.showEditor();
						return false;
					})
				)
			);
		} else {
			// Update edit button
			$j('#ca-edit a')
				.html( $j('<span />').text( gM('mwe-sequencer-edit-sequence' ) ) )
				.click(function(){
					_this.showEditor();
					return false;
				})

			_this.displayPlayerEmbed();
		}
	},

	showViewFlattenedFile: function(){
		var _this = this;
		//just update the edit button:
		$j('#ca-edit a')
		.html( $j('<span />').text( gM('mwe-sequencer-edit-sequence' ) ) )
		.click(function(){
			_this.showEditor();
			return false;
		})
	},

	showEditUI: function(){
		var _this = this;
		$j('#bodyContent')
		.append(
			$j('<div />')
			.css({
				'position' : 'relative',
				'width' : '100%',
				'height' : '620px'
			})
			.attr({
				'id': 'sequencerContainer'
			}),
			$j('<div />')
			.append(
				gM("mwe-sequencer-restore-text-edit", $j('<a />').click(function(){
					$j('#sequencerContainer').hide();
					$j('#editform,#toolbar').show();
				}) )
			)
			.css( {'cursor': 'pointer', 'font-size':'x-small' })
		);
		// load the sequence editor with the sequencerContainer target
		mw.load( 'Sequencer', function(){
			$j('#sequencerContainer').sequencer( _this.getSequencerConfig() );
		});
	},


	getSequenceFileKey: function(){
		return 'File:' + wgPageName.replace( /:/g, '-') + '.ogv';
	},

	displayPlayerEmbed: function(){
		var _this = this;
		// load the embedPlayer module:
		mw.load('EmbedPlayer', function(){
			// Check if the sequence has been flattened and is up to date:
			var request = {
				'action': 'query',
				'titles': _this.getSequenceFileKey(),
				'prop': 'imageinfo|revisions',
				'iiprop': 'url|metadata',
				'iiurlwidth': '400',
				'redirects' : true // automatically follow redirects
			};

			var $embedPlayer = $j('<div />');
			mw.getJSON( request, function( data ){
				if(!data.query || !data.query.pages || data.query.pages[-1]){
					// no flattened file found
					$embedPlayer.append(
						$j( '<div />').append(
							gM('mwe-sequencer-not-published',
								$j('<a />').click( function(){
									_this.showEditor();
								}).css('cursor', 'pointer')
							)
						)
						.addClass( 'ui-state-highlight' )
					)
				} else {
					for( var pageId in data.query.pages) {
						var page = data.query.pages[ pageId ];

						// Check that the file has a later revision than the
						// page. ( up to date sequences always are later than
						// the revision of the page saved ).
						if( page.revisions && page.revisions[0] ){
							if( page.revisions[0].revid < wgCurRevisionId ){
								// flattened file out of date
								$embedPlayer.append(
									$j('<div />').append(
										gM('mwe-sequencer-published-out-of-date',
											$j('<a />').click( function(){
												_this.showEditor();
											}).css('cursor', 'pointer')
										)
									).addClass( 'ui-state-highlight' )
								)
							}
						}
						if( page.imageinfo && page.imageinfo[0] ){
							var imageinfo = page.imageinfo[0];
							var duration = 0;
							for( var i=0;i< imageinfo.metadata.length; i++){
								if( imageinfo.metadata[i].name == 'length' ){
									duration = Math.round(
										imageinfo.metadata[i].value * 1000
									) / 1000;
								}
							}
							// Append a player to the embedPlayer target
							// -- special title key sequence name bound
							$embedPlayer.append(
								$j('<video />')
								.attr({
									'id' : 'embedSequencePlayer',
									'poster' : imageinfo.thumburl,
									'durationHint' : duration,
									'apiTitleKey' : page.title.replace('File:',''),
								})
								.addClass('kskin')
								.css({
									'width': imageinfo.thumbwidth,
									'height' : imageinfo.thumbheight
								})
								.append(
									// ogg source
									$j('<source />')
									.attr({
										'type': 'video/ogg',
										'src' : imageinfo.url
									})
								)
							)
						}
					}
				}
				var width = ( imageinfo && imageinfo.thumbwidth )?imageinfo.thumbwidth : '400px';

				// Copy the category links if present

				// Display embed sequence
				$j( _this.target ).empty().append(
					$j('<div />')
					.addClass( 'sequencer-player')
					.css( {
						'float' : 'left',
						'width' : width
					})
					.append(
						$embedPlayer
					)
					,

					// Embed player
					$j('<div />')
					.addClass( 'sequencer-embed-helper')
					.css({
						'margin-left': '430px'
					})

					// Text embed code
					.append(
						$j('<h3 />')
						.text( gM('mwe-sequencer-embed-sequence') )
						,
						$j('<span />' )
						.text( gM('mwe-sequencer-embed-sequence-desc') )
						,
						$j('<br />'),
						$j('<textarea />')
						.css({
							'width' : '100%',
							'height' : '200px'
						}).focus(function(){
							$j(this).select();
						})
						.append(
							_this.getSequenceEmbedCode()
						)
					),

					// Add a clear both to give content body height
					$j('<div />').css( { 'clear': 'both' } )

				)
				// add cat links if set;
				if( _this.catLinks ){
					$j( _this.target ).append(
						$j('<div />').html( _this.catLinks )
					);
				}

				// Rewrite the player
				$j('#embedSequencePlayer').embedPlayer();
			}); // load json player data
		})
	},
	getSequenceEmbedCode: function(){
		var editLink = wgServer + wgArticlePath.replace('$1', wgTitle );
		editLink = mw.replaceUrlParams( editLink, {'action' : 'edit' });

		return '[[' + this.getSequenceFileKey() + "|thumb|400px|right|\n\n" +
		 "Sequence " + this.getTitle() + " \n\n" +
		 "&lt;br&gt;Edit this sequence with the [" +
		 mw.getRemoteSequencerLink ( editLink ) +
		 ' kaltura editor] ]]';
	},

	showEditor: function(){
		var _this = this;

		$j('body').append(
			$j('<div />')
			.attr('id', "edit_sequence_container")
			.css({
				'position' : 'absolute',
				'font-size' : '.8em',
				'top' : '5px',
				'bottom' : '5px',
				'left' : '5px',
				'right' : '6px',
				'background': '#FFF',
				'z-index' : '1001'
			})
			.append(
				$j('<div />').append(
					gM('mwe-sequencer-loading-sequencer'),
					$j('<span />').loadingSpinner()
				)
				.css( {'width':'200px', 'margin':'auto'})
			)
		)
		mw.load( 'Sequencer', function(){
			// Send a jquery ui style destroy command ( in case the editor is re-invoked )
			$j('#edit_sequence_container').sequencer( 'destroy');
			$j('#edit_sequence_container').sequencer( _this.getSequencerConfig() );
		});
	},
	getSequencerConfig: function(){
		var _this = this;
		return {
			// The title for this sequence:
			title : _this.getTitle(),

			// If the sequence is new
			newSequence : ( wgArticleId == 0 ),

			// Server config:
			server: {
				'type' : 'mediaWiki',
				'url' : _this.getApiUrl(),
				'titleKey' : _this.titleKey,
				'pagePathUrl' : wgServer + wgArticlePath,
				'userName' : wgUserName
			},
			// Set the add media wizard to only include commons:
			addMedia : {
				'enabled_providers':[ 'wiki_commons' ],
				'default_query' : _this.getTitle()
			},
			// Function called on sequence exit
			onExitCallback: function( sequenceHasChanged ){
				if( sequenceHasChanged ){
					window.location.reload();
				}
				// else do nothing
			}
		}
	},
	getApiTitleKey: function(){
		return wgPageName;
	},
	getTitle: function(){
		return wgPageName.replace( 'Sequence:', '').replace('_', ' ');
	},
	// Get the api url ( for now use whatever the page context is )
	getApiUrl: function(){
		return mw.absoluteUrl( wgScript.replace('index.php', 'api.php') );
	}

};

} )( window.mw );