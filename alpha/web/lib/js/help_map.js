(function( kmc ) {
	
	// If we already loaded helpMap, exit
	if( kmc.helpMap ) {
		return ;
	}
	
	// Setup help map
	var helpMap = {
		'section_categories': 'section_categories.html'
	};
	
	// Export helpMap to KMC global object
	kmc.helpMap = helpMap;
	
})( window.kmc || {} );