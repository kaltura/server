/**
 * Master default configuration for sequencer
 *
 * Do not modify this file rather after including mwEmbed
 * set any of these configuration values via
 * the mw.setConfig() method
 *
 */
// Wrap in mw closure
( function( mw ) {
	// Define the class name
	mw.SequencerConfig = true;

	mw.setDefaultConfig({
		// If the sequencer should attribute kaltura
		"Sequencer.KalturaAttribution" : true,

		// If a the sequencer should open new windows
		"Sequencer.SpawnNewWindows" : true,

		// The size of the undo stack
		"Sequencer.NumberOfUndos" : 100,

		// Default image duration
		"Sequencer.AddMediaImageDuration" : 2,

		// NOTE these values 800x600 are the default display
		// size for assets in wikimedia commons and helps avoid server side resizes
		// and gives the asset a better chance of being served from the cache

		// Default import image source width
		"Sequencer.AddMediaImageWidth" : 800,

		// Default import image source height
		"Sequencer.AddMediaImageHeight" : 600,

		// If a asset can be directly added to the sequence by url
		// ( if disabled only urls that are part addMedia can be added )
		"Sequencer.AddAssetByUrl" : true,

		// Default timeline clip timeline track height
		"Sequencer.TimelineTrackHeight" : 100,

		// Default timeline audio or collapsed timeline height
		"Sequencer.TimelineColapsedTrackSize" : 35
	});

} )( window.mw );
