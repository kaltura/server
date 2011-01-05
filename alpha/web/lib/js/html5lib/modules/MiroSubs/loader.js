/**
* Miro Sub module
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {
	mw.addMessages( {
		"mwe-mirosubs-add-universal-subtitles" : "Universal subtitles editor",
		"mwe-mirosubs-loading-universal-subtitles" : "Loading <i>universal subtitles</i> editor"
	});
	
	// Add as loader dependency 'mw.style.mirosubsMenu'
	mw.addResourcePaths( {
		"mirosubs" : "mirosubs/mirosubs-api.min.js",
		"mw.MiroSubsConfig" : "mw.MiroSubsConfig.js",
		"mw.style.mirosubsMenu" : "css/mw.style.mirosubsMenu.css",
		"mw.style.mirosubswidget" : "mirosubs/media/css/mirosubs-widget.css"
	});

	mw.setDefaultConfig( {
		'MiroSubs.EnableUniversalSubsEditor': true
	});

	mw.addModuleLoader( 'MiroSubs', function(){
		var resourceList = [ "mirosubs", "mw.style.mirosubswidget", "mw.MiroSubsConfig",
		                     "mw.ui.languageSelectBox", "mw.Language.names", "$j.ui.autocomplete",
		                     "$j.ui.combobox" ];
		return resourceList;
	});

	$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){
		// Check if the Miro Editor is enabled and the player has an apiTitleKey
		if( mw.getConfig( 'MiroSubs.EnableUniversalSubsEditor' )
			&&
			embedPlayer.apiTitleKey
		){
			// Build out the menu in the loader ( to load mirosubs interface on-demand )
			$j( embedPlayer ).bind( 'TimedText.BuildCCMenu', function( event, langMenu ){

				// Load the miro subs menu style ( will be part of the loader dependency later on)
				mw.load( 'mw.style.mirosubsMenu' );

				$j( langMenu ).append(
					$j.getLineItem( gM( 'mwe-mirosubs-add-universal-subtitles'), 'mirosubs', function() {
						// Show loader
						mw.addLoaderDialog( gM('mwe-mirosubs-loading-universal-subtitles') );
						// Load miro subs:
						mw.load( 'MiroSubs', function(){
							// Open the mirosubs dialog:
							mw.MiroSubsConfig.openDialog( embedPlayer );
						});
						// don't follow the line item # link 
						return false;
					})
				);
			});
		};
	});


} )( window.mw );