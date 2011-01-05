/**
* Loader for smilPlayer
*/
// Wrap in mw to not pollute global namespace
( function( mw ) {

	mw.setDefaultConfig( {
		// The framerate for the smil player
		'SmilPlayer.framerate': 30,

		// Array of Asset approved domains or keyword '*' for no restriction
		// Before any asset is displayed its domain is checked against this array of wildcard domains
		// Additionally best effort is made to check any text/html asset references
		// for example [ '*.wikimedia.org', 'en.wikipeida.org']
		'SmilPlayer.AssetDomainWhiteList' : '*'

	} );

	mw.addResourcePaths( {
		"mw.SmilHooks" : "mw.SmilHooks.js",
		"mw.Smil" : "mw.Smil.js",
		"mw.SmilLayout" : "mw.SmilLayout.js",
		"mw.style.SmilLayout" : "mw.style.SmilLayout.css",
		"mw.SmilBody" : "mw.SmilBody.js",
		"mw.SmilBuffer" : "mw.SmilBuffer.js",
		"mw.SmilAnimate" : "mw.SmilAnimate.js",
		"mw.SmilTransitions" : "mw.SmilTransitions.js",
		"mw.EmbedPlayerSmil" : "mw.EmbedPlayerSmil.js"
	} );

	// Add the mw.SmilPlayer to the embedPlayer loader:
	$j( mw ).bind( 'LoaderEmbedPlayerUpdateRequest', function( event, playerElement, resourceRequest ) {
		var smilPlayerLibrarySet = [
			"mw.SmilHooks",
			"mw.Smil",
			"mw.SmilLayout",
			"mw.style.SmilLayout",
			"mw.SmilBody",
			"mw.SmilBuffer",
			"mw.SmilAnimate",
			"mw.SmilTransitions",
			"mw.EmbedPlayerSmil"
		];

		// Add smil library set if needed
		if (mw.CheckElementForSMIL( playerElement ) ) {
			$j.merge(resourceRequest, smilPlayerLibrarySet);
		}
	} );


	/**
	* Check if a video tag element has a smil source
	*/
	mw.CheckElementForSMIL = function( element ){
		if( $j( element ) .attr('type' ) == 'application/smil' ||
			( $j( element ).attr('src' ) &&
		 	$j( element ).attr('src' ).substr( -4) == 'smil' ) )
		 {
		 	return true;
		 }
		 var loadSmil = false;
		 $j( element ).find( 'source' ).each( function( inx, sourceElement ){
			if( mw.CheckElementForSMIL( sourceElement ) ){
				loadSmil = true;
				return true;
			}
		});
		return loadSmil;
	};

} )( window.mw );
