// Wrap in mw
// Check for new Embed Player events: 
$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){

	// Check for KalturaSupport uiConf
	$j( embedPlayer ).bind( 'KalturaSupport.checkUiConf', function( event, $uiConf, callback ){
		
		// Check if the kaltura ad plugin is enabled:
		if( $uiConf.find('Plugin#vast').length ){
			adPlugin( embedPlayer,  $uiConf, callback );
		} else {
			// Continue player build out for players without ads
			callback();
		}
	});
});

var adPlugin = function( embedPlayer,  $uiConf, callback){
	// Load the Kaltura Ads and AdSupport Module:
	mw.load( [ "AdSupport", "mw.KAds" ], function(){
		
		// Add the ads to the player: 
		mw.addKalturaAds( embedPlayer,  $uiConf.find('Plugin#vast'), function(){
			
			// Wait until ads are loaded before running callback
			// ( ie we don't want to display the player until ads are ready )
			callback();
		});
		
	});
}

