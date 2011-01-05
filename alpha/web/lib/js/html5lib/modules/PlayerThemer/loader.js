/**
* Auto Player themer takes an html css structure or a json set of selectors to 'auto-build' out a player
*/

( function( mw ) {
	// Adds a jquery binding / loader for playerThemer
	mw.addResourcePaths({
		"mw.PlayerThemer" : "mw.PlayerThemer.js"
	});

	// Add the mw.PlayerThemer to the embedPlayer loader request if we have special playerThemer class
	$j( mw ).bind( 'LoaderEmbedPlayerUpdateRequest', function( event, playerElement, classRequest ) {
		if( $j( playerElement ).hasClass('PlayerThemer') ){
			// Set the player useNativeControls attribute
			$j( playerElement ).attr('usenativecontrols', true);

			// Add playerThemer to the request:
			if( $j.inArray( 'mw.PlayerThemer', classRequest ) == -1 ){
				classRequest.push( 'mw.PlayerThemer');
			}
		}
	});
	
})( window.mw );