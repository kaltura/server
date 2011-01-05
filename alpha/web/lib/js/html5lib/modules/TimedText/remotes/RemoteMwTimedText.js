/**
* Stop-gap for mediaWiki php based timed text support
*
* Does some transformations to normal wiki timed Text pages to make them look
* like the php output that we will eventually want to have
*/

mw.addMessageKeys( [
	"mwe-timedtext-language-subtitles-for-clip",
	"mwe-timedtext-language-no-subtitles-for-clip"
]);

RemoteMwTimedText = function( options ) {
	return this.init( options );
}
mw_default_remote_text_options = [
	'action',
	'title',
	'target',
	'orgBody'
];
RemoteMwTimedText.prototype = {

	init: function( options ) {
		for(var i in mw_default_remote_text_options) {
			var opt = mw_default_remote_text_options[i]
			if( options[ opt ] ) {
				this[ opt ] = options[ opt ];
			}
		}
	},
	updateUI: function() {
		// Check page type
		if( this.action == 'view' ) {
			this.showViewUI();
		}else{
			//restore
		}
	},
	showViewUI: function() {
		var _this = this;
		var fileTitleKey = this.title.split('.');
		this.extension = fileTitleKey.pop();
		this.langKey = fileTitleKey.pop();
		this.fileTitleKey = fileTitleKey.join('.');

		this.getTitleResource( this.fileTitleKey, function( resource ) {
			_this.embedViewUI( resource );
		});
	},
	embedViewUI: function( resource ) {
		var _this = this;
		// Load the player module:
		mw.load( 'EmbedPlayer', function() {
			var width = ( resource.width > 500 )? 500 : resource.width;
			var height = width * ( resource.height / resource.width );
			// Add the embed code: ( jquery wrapping of "video" fails )
			$j( _this.target ).empty().append(
				$j('<video />').attr({
					'id': "timed-text-player-embed",
					'poster': resource.poster,
					'src':  resource.src,
					'durationHint' : resource.duration,
					'apiTitleKey' : resource.apiTitleKey
				})
				.css({
					'width' : width + 'px',
					'height' : height + 'px'
				})
				.addClass( 'kskin' )
				 ,
				 $j('<div />').css({
					'position' : 'relative',
					'left' : '510px',
					'top' : -height + 'px'
				 })
				 .append( _this.orgBody )
			);
			
			// embed the player with the pre-selected language:
			_this.embedPlayerLang();
		});
	},
	/**
	* Embeds a player with the current language key pre selected
	*/
	embedPlayerLang: function() {
		var _this = this;
		if( wgArticlePath ) {
			var $fileLink = $j('<div>').append(
				$j('<a>').attr({
					'href' : wgArticlePath.replace( '$1', 'File:' + _this.fileTitleKey)
				})
				.text( _this.fileTitleKey.replace('_', ' ') )
			)
		}

		// Rewrite the player (any video tags on the page)
		$j('#timed-text-player-embed').embedPlayer( function() {
			//Select the timed text for the page:

			//remove the loader
			$j('.loadingSpinner').remove();

			var player = $j('#timed-text-player-embed').get(0);


			if( !player.timedText ) {
				mw.log("Error: no timedText method on embedPlayer" );
				return ;
			}
			// Set the userLanguage:
			player.timedText.config.userLanugage = this.langKey;

			// Make sure the timed text sources are loaded:
			player.timedText.setupTextSources( function() {

				var source = player.timedText.getSourceByLanguage( _this.langKey );
				var pageMsgKey = 'mwe-timedtext-language-subtitles-for-clip';
				if( ! source ) {
					pageMsgKey = "mwe-timedtext-language-no-subtitles-for-clip"
				}
				// Add the page msg to the top
				$j( _this.target ).prepend(
					$j('<h3>')
					.html(
						gM( pageMsgKey, [ mw.Language.names[ _this.langKey ], $fileLink.html() ] )
					)
				);
				// Select the language if possible:
				if( source ) {
					player.timedText.selectTextSource( source );
				}
			} );
		} );
	},

	/**
	* Gets the properties of a given title as a resource
	* @param {String} fileTitle Title of media asset to embed
	* @param {Function} callback [Optional] Function to call once asset is embedded
	*/
	getTitleResource: function( fileTitle, callback ) {
		var _this = this;
		// Get all the embed details:
		var request = {
			'titles' : 'File:' + fileTitle,
			'prop' : 'imageinfo|revisions|redirects',
			'iiprop' : 'url|mime|size|metadata',
			'iiurlwidth' : mw.getConfig( 'EmbedPlayer.DefaultSize').split('x').pop(),
			'rvprop' : 'content'
		}
		// (only works for commons right now)
		mw.getJSON( request, function( data ) {			
			// Check for "page not found"
			if( data.query.pages['-1'] ) {
				//restore content:
				$j(_this.target).html( _this.orgBody );
				return ;
			}
			for ( var i in data.query.pages ) {
				var page = data.query.pages[i];				
				mw.log( "should process data result" );
				// Else process the result
				var resource = _this.getResource( page );
				callback( resource );
			}
		} );
	},

	/**
	* Get the embed code from response resource and sends it a callback
	*/
	getResource: function( page ) {
		var resource = {
				'apiTitleKey' : page.title.replace(/File:/ig, '' ),
				'link'		 : page.imageinfo[0].descriptionurl,
				'poster'	 : page.imageinfo[0].thumburl,
				'src'		 : page.imageinfo[0].url,
				'width' : page.imageinfo[0].width,
				'height': page.imageinfo[0].height
			};
		// check metadata for length:
		for( var i=0; page.imageinfo[0].metadata.length < i ; i++ ){
			var meta = page.imageinfo[0].metadata[i];
			if( meta.name == 'length' ){
				resource.duration = meta.value;
			}
		}
		return resource;
	}
};
