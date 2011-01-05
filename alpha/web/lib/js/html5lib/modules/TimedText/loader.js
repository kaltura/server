/**
* TimedText loader.
*/
// Scope everything in "mw" ( keeps the global namespace clean )
( function( mw ) {

	mw.addResourcePaths( {
		"mw.TimedText" : "mw.TimedText.js",
		"mw.style.TimedText" : "css/mw.style.TimedText.css",

		"mw.TimedTextEdit" : "mw.TimedTextEdit.js",
		"mw.style.TimedTextEdit" : "css/mw.style.TimedTextEdit.css",

		"RemoteMwTimedText" : "remotes/RemoteMwTimedText.js"
	} );
	
	// Merge in timed text related attributes: TODO add merge config support with some way to 
	// clasify configuration as "default" vs custom. 
	var sourceAttr = mw.getConfig( 'EmbedPlayer.SourceAttributes');
	mw.setConfig( 'EmbedPlayer.SourceAttributes', $j.extend( sourceAttr, [
	   'srclang',
	   'category'
	]) );
	
	mw.setDefaultConfig( {
		// If the Timed Text interface should be displayed:
		// 'always' Displays link and call to contribute always
		// 'auto' Looks for child timed text elements or "apiTitleKey" & load interface
		// 'off' Does not display the timed text interface
		"TimedText.showInterface" : "auto",

		/**
		* If the "add timed text" link / interface should be exposed
		*/
		'TimedText.showAddTextLink' : true,

		// The category for listing videos that need transcription:
		'TimedText.NeedsTranscriptCategory' : 'Videos needing subtitles'
	});

	var mwTimedTextRequestSet = [
		'$j.fn.menu',
		'mw.TimedText',
		'mw.style.TimedText',
		'mw.style.jquerymenu'
	];

	// TimedText module
	mw.addModuleLoader( 'TimedText', mwTimedTextRequestSet );

	/**
	* Setup the load embedPlayer visit tag addSetupHook function
	*
	* Check if the video tags in the page support timed text
	* this way we can add our timed text libraries to the player
	* library request.
	*/

	// Update the player loader request with timedText library if the embedPlayer
	// includes timedText tracks.
	$j( mw ).bind( 'LoaderEmbedPlayerUpdateRequest', function( event, playerElement, classRequest ) {
		if( mw.isTimedTextSupported( playerElement ) ) {
			classRequest = $j.merge( classRequest, mwTimedTextRequestSet );
		}
	} );
	
	// On new embed player check if we need to add timedText
	$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){
		if( mw.isTimedTextSupported( embedPlayer) ){
			if( ! embedPlayer.timedText ) {
				embedPlayer.timedText = new mw.TimedText( embedPlayer );
			}
		}
	});
	
	/**
	 * Check if we should load the timedText interface or not.
	 *
	 * Note we check for text sources outside of
	 */
	mw.isTimedTextSupported = function( embedPlayer ) {
		if( mw.getConfig( 'TimedText.showInterface' ) == 'always' ) {
			return true;
		}
		// Check for timed text sources or api/ roe url
		if ( 
			(	 $j( embedPlayer ).attr( embedPlayer.roe ) 
				|| 
				$j( embedPlayer ).attr('apititlekey')
				||  
				$j( embedPlayer ).attr('apiTitleKey' )
			)
			|| 
			( embedPlayer.mediaElement && embedPlayer.mediaElement.textSourceExists() )	
			||
			$j( embedPlayer ).find( 'track' ).length != 0
		) {
			return true;
		} else {
			return false;
		}
	};
	
	// TimedText editor:
	mw.addModuleLoader( 'TimedText.Edit', [
		[
			'$j.ui',
			'$j.widget',
			'$j.ui.mouse',
			'$j.ui.position',
			'$j.fn.menu',
			"mw.style.jquerymenu",

			'mw.TimedText',
			'mw.style.TimedText',

			'mw.TimedTextEdit',
			'mw.style.TimedTextEdit'
		],
		[
			'$j.ui.dialog',
			'$j.ui.tabs'
		]
	]);

} )( window.mw );