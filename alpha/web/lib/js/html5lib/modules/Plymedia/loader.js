/**
* Plymedia close caption module loader
* 
* @Description of plymedia module goes here
* @author plymedia authored by
*/
( function( mw ) {	
	// List named resource paths
	mw.addResourcePaths({
		"plyMediaPlayer" : "plyMedia/plyMediaPlayer.js",
		"plyMedia.style" :  "plyMedia/plyMedia.css",
		"mw.plyMediaConfig" : "mw.plyMediaConfig.js"
	});
	
	mw.addModuleLoader( 'plyMedia', function(){
		// load any files needed for plyMedia player:
		return ['mw.plyMediaConfig'];
	});
	
	// Bind the plyMedia player where the uiconf includes the plymedia plugin
	$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){
		
		$j( embedPlayer ).bind( 'KalturaSupport.checkUiConf', function( event, $uiConf , callback){
			// Check for plyMedia in kaltura uiConf
			if( $uiConf.find("plugin#plymedia").length ){
				
				// Load the plyMeida module 
				// NOTE in production plyMedia would be pre-loaded by the iframe uiconf
				
			    mw.load( 'plyMedia', function(){
			    	mw.plyMediaConfig.bindPlayer( embedPlayer );
			    });
			}
			// Don't block player display on plyMedia plugin loading 
			callback();
		})
	})

} )( window.mw );