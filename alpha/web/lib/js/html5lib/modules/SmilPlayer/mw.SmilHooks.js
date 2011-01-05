
// Define the SmilHooks object
mw.SmilHooks = {
	addSmilPlayer: function(){
		// Check if the smil player has already been added:
		if( mw.EmbedTypes.players.defaultPlayers[ 'application/smil' ] )
			return ;

		// Add the swarmTransport playerType
		mw.EmbedTypes.players.defaultPlayers[ 'application/smil' ] = [ 'Smil' ];

		// Build the swarm Transport "player"
		var smilMediaPlayer = new mediaPlayer( 'smilPlayer', [ 'application/smil' ], 'Smil' );

		// Add the swarmTransport "player"
		mw.EmbedTypes.players.addPlayer( smilMediaPlayer );
	}
};

// Add the smil player to available player types:
$j( mw ).bind( 'EmbedPlayerManagerReady', function( event ) {
	mw.SmilHooks.addSmilPlayer();
} );

// Tell embedPlayer not to wait for height / width metadata in cases of smil documents
$j( mw ).bind( 'addElementWaitForMetaEvent', function( event, waitForMetaObject ) {
	if( mw.CheckElementForSMIL( waitForMetaObject[ 'playerElement' ] ) ){
		waitForMetaObject[ 'waitForMeta' ] = false;
		return false;
	}
});

// Bind the smil check for sources
$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {
	// Add the smil player ( in case we were dynamically loaded and EmbedPlayerManagerReady has already been called )
	mw.SmilHooks.addSmilPlayer();

	// Setup the "embedCode" binding to swap in an updated url
	$j( embedPlayer ).bind( 'checkPlayerSourcesEvent', function( event, callback ) {
		mw.log( "SmilHooks::checkPlayerSources" );
		// Make sure there is a smil source:
		if( embedPlayer.mediaElement.getSources( 'application/smil' ).length ){
			// Get the first smil source:
			//mw.log( "Source is: " + embedPlayer.mediaElement.getSources( 'application/smil' )[0].getSrc() );

			// Add the smil engine to the embed player:
			embedPlayer.smil = new mw.Smil( embedPlayer );

			// Load the smil url as part of "source check"
			embedPlayer.smil.loadFromUrl( embedPlayer.mediaElement.getSources( 'application/smil' )[0].getSrc(), function(){
				callback();
			});
		} else {
			callback();
		}
	} );
});

