(function( kmc ) {
	
	// If we already loaded helpMap, exit
	if( kmc.helpMap ) {
		return ;
	}
	
	// Setup help map
	var helpMap = {
		'test': 'test.html'
	};
	
	// Export helpMap to KMC global object
	kmc.helpMap = helpMap;
	
})( window.kmc || {} );