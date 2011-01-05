/**
* MediaRssPlayer
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {

	mw.addResourcePaths( {
		"mw.Playlist" : "mw.Playlist.js",
		"mw.PlaylistHandlerMediaRss" : "mw.PlaylistHandlerMediaRss.js",
		"mw.PlaylistLayoutJQueryUi" : "mw.PlaylistLayoutJQueryUi.js",	
		"mw.PlaylistLayoutMobile" : "mw.PlaylistLayoutMobile.js"	
	});

	// Set the default config
	mw.setDefaultConfig( {
		// Playlist layout 'vertical' or 'horizontal'
		'Playlist.layout' : 'vertical',
		
		// Skin, presently 'jqueryui' or 'jquerymobile' supported
		"Playlist.Skin" : "jqueryui",
			
		// Player aspect ratio
		'Playlist.playerAspect' : '4:3',

		// Width of item thubmnails
		'Playlist.itemThumbWidth' : '60',

		// Height of the mediaRss title
		'Playlist.titleHeight' : '20',

		// Default playlist type:
		'Playlist.defaultType' : 'application/rss+xml'
	} );

	// Module loader
	mw.addModuleLoader( 'Playlist', function(){
		// TODO loader should check playlist configuration and conditionally load the MobileTheme
		//, 'mw.PlaylistThemeUi', 'mw.PlaylistLayoutMobile'
		return ["mw.Playlist", "mw.PlaylistHandlerMediaRss"];
	});


} )( window.mw );

// Add the jQuery hook:
( function( $ ) {
	$.fn.playlist = function( options ){
		var _this = this;
		if ( !this.selector ) {
			mw.log( "Error: Calling mediaRssPlayer with empty selector " + this.selector);
			return ;
		}
		// Set the target to loading
		$j( this.selector ).loadingSpinner();

		// Set the target
		options[ 'target' ] = _this.selector;

		// Load the mediaRss class ( if not already loaded )
		mw.load ( ['EmbedPlayer', 'Playlist'], function(){
			// load and display the media Rss
			var myPlaylist = new mw.Playlist( options );
			myPlaylist.drawUI();
		});
	}
} )( jQuery );

