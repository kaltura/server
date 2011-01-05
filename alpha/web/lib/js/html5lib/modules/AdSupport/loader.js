// Scope everything in "mw" ( keeps the global namespace clean ) 
( function( mw ) {
	
	mw.addResourcePaths({
		"mw.AdTimeline" : "mw.AdTimeline.js",
		"mw.AdLoader" : "mw.AdLoader.js",
		"mw.VastAdParser" : "mw.VastAdParser.js"
	});
	
	mw.addModuleLoader('AdSupport', function(){
		return [ 'mw.MobileAdTimeline', 'mw.AdLoader', 'mw.VastAdParser' ];
	});
	
	mw.setDefaultConfig({
		'AdSupport.XmlProxyUrl' : mw.getMwEmbedPath() + 'modules/AdSupport/simplePhpXMLProxy.php'
	});
	
	// Ads have to communicate with parent iframe to support companion ads.
	// ( we have to add them for all players since checkUiConf is done on the other side of the
	// iframe proxy )
	$j( mw ).bind( 'AddIframeExportedBindings', function( event, exportedBindings){
		// Add the updateCompanionTarget binding to bridge iframe
		exportedBindings.push( 'updateCompanionTarget' );
	});
	
	// Add the updateCompanion binding to new iframeEmbedPlayers
	$j( mw ).bind( 'newIframeEmbedPlayerEvent', function( event, embedPlayer ){
		$j( embedPlayer ).bind( 'updateCompanionTarget', function( event, companionObject) {
			// NOTE: security wise we should try and "scrub" the html for script tags
			$j('#' + companionObject.elementid ).html( 
					companionObject.html
			)
		});
	});

} )( window.mw );