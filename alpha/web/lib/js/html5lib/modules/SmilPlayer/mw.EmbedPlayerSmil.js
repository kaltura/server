/**
* Extends EmbedPlayer to wrap smil playback in the html5 video tag abstraction.
*/

//Get all our message text
mw.includeAllModuleMessages();

// Setup the EmbedPlayerSmil object:
mw.EmbedPlayerSmil = {

	// Instance Name
	instanceOf: 'Smil',

	// The jQuery target location to render smil html
	$renderTarget: null,

	// Store the actual play time
	smilPlayTime: 0,

	// Flag to register the player being embedded
	smilPlayerEmbedded: false,

	// Store the pause time
	smilPauseTime: 0,

	// Store a playback duration
	smilplaySegmentEndTime: null,

	// flag to register when video is paused to fill a buffer.
	pausedForBuffer: false,

	// The virtual volume for all underling clips
	volume: .75,

	// The max out of sync value before pausing playback
	// set to .5 second:
	maxSyncDelta: .5,

	// Player supported feature set
	supports: {
		'playHead' : true,
		'pause' : true,
		'fullscreen' : true,
		'timeDisplay' : true,
		'volumeControl' : true,
		'overlays' : true
	},

	/**
	* Put the embed player into the container
	*/
	doEmbedHTML: function() {
		var _this = this;

		// check if we have already embed the player:
		if( this.smilPlayerEmbedded ){
			return;
		}
		this.smilPlayerEmbedded = true;
		mw.log("EmbedPlayerSmil::doEmbedHTML: " + this.id + " time:" + this.smilPlayTime ) ;

		this.setCurrentTime( this.smilPlayTime, function(){
			mw.log("EmbedPlayerSmil::doEmbedHTML:: render callback ready " );
		});
		
		// Be sure the interface does not have black background
		this.$interface.css('background', 'none');
	},

	/**
	 * Set the virtual smil volume ( will key all underling assets against this volume )
	 * ( we can't presently "normalize" across clips )
	 */
	setPlayerElementVolume: function( percent ){
		this.volume = percent;
	},

	/**
	 * Seeks to the requested time and issues a callback when ready / displayed
	 * @param {float} time Time in seconds to seek to
	 * @param {function} callback Function to be called once currentTime is loaded and displayed
	 */
	setCurrentTime: function( time, callback , hideLoader) {
		//mw.log('EmbedPlayerSmil::setCurrentTime: ' + time );
		// Set "loading" spinner here)
		if( !hideLoader ){
			if( $j('#loadingSpinner_' + this.id ).length == 0 ){
				$j( this ).getAbsoluteOverlaySpinner()
					.attr('id', 'loadingSpinner_' + this.id );
			}
		}
		// Start seek
		this.controlBuilder.onSeek();
		this.smilPlayTime = time;
		this.smilPauseTime = this.smilPlayTime;
		var _this = this;
		this.getSmil( function( smil ){
			smil.renderTime( time, function(){
				//mw.log( "setCurrentTime:: renderTime callback" );
				$j('#loadingSpinner_' + _this.id ).remove();
				_this.monitor();
				if( callback ){
					callback();
				}
			} );
		});
	},

	/**
	* Issue a seeking request.
	*
	* @param {Float} percentage
	*/
	doSeek: function( percentage ) {
		mw.log( 'EmbedPlayerSmil::doSeek p: ' + percentage );
		this.seeking = true;
		var _this = this;
		// Run the seeking hook

		$j( this.embedPlayer ).trigger( 'seeking' );
		this.setCurrentTime( percentage * this.getDuration(), function(){
			mw.log("EmbedPlayerSmil:: seek done");
			_this.seeking = false;
			_this.monitor();
		});
	},

	/**
	* Return the render target for output of smil html
	*/
	getRenderTarget: function(){
		if( !this.$renderTarget ){
			if( $j('#smilCanvas_' + this.id ).length === 0 ) {
				// If no render target exist create one:
				$j( this ).html(
					$j( '<div />')
					.attr( 'id', 'smilCanvas_' + this.id )
					.css( {
						'width' : '100%',
						'height' : '100%',
						'position' : 'relative'
					})
				);
			}
			this.$renderTarget = $j('#smilCanvas_' + this.id );
		}
		return this.$renderTarget;
	},

	/**
	 * Smil play function
	 * @param {float=} playSegmentEndTime Optional duration to be played before pausing playback
	 */
	play: function( playSegmentEndTime ){
		var _this = this;
		mw.log(" EmbedPlayerSmil::play " + _this.smilPlayTime + ' to ' + playSegmentEndTime + ' pause time: ' + this.smilPauseTime );

		// Update clock start time;
		_this.clockStartTime = new Date().getTime();

		// Update the interface
		this.parent_play();

		// xxx set player to 'loading / buffering'

		// Update the playSegmentEndTime flag
		if( ! playSegmentEndTime ){
			this.playSegmentEndTime = null;
		} else {
			this.playSegmentEndTime = playSegmentEndTime;
		}

		// Make sure this.smil is ready :
		this.getSmil( function( smil ){

			// Start buffering the movie
			_this.smil.startBuffer();

			if( isNaN( _this.smilPlayTime ) ){
				_this.smilPlayTime = 0;
			}
			// Sync with current smilPlayTime
			_this.clockStartTime = new Date().getTime() - ( _this.smilPlayTime * 1000 );
			mw.log('smil callback set clockTime: ' + new Date().getTime() +
					'-' + ' splaytime: ' + _this.smilPlayTime +' x1000' );
			// Zero out the pause time:
			_this.smilPauseTime = 0;

			// Set posterDisplayed to false
			this.posterDisplayed = false;

			// Start up monitor:
			_this.monitor();
		});
	},
	/**
	 * Maps a "load" call to startBuffer call in the smil engine
	 */
	load: function(){
		var _this = this;
		this.getSmil( function( smil ){
			// Start buffering the movie
			_this.smil.startBuffer();
			// Start up monitor:
			_this.monitor();
		});
	},

	stop: function(){
		mw.log("EmbedSmilPlayer:: stop");
		this.smilPlayTime = 0;
		this.smilPauseTime = 0;
		this.setCurrentTime( 0 );
		this.parent_stop();
	},

	/**
	* Preserves the pause time across for timed playback
	*/
	pause: function() {
		mw.log( 'EmbedPlayerSmil::pause at time:' + this.smilPlayTime );
		this.smilPauseTime = this.smilPlayTime;

		// Issue pause to smil engine
		this.smil.pause( this.smilPlayTime );

		// Update the interface
		this.parent_pause();
	},

	/**
	* Get the embed player time
	*/
	getPlayerElementTime: function() {
		return this.smilPlayTime;
	},


	/**
	 * Monitor function render a given time
	 */
	monitor: function(){
		// Get a local variable of the new target time:
		//mw.log("smilPlayer::monitor: isPlaying:" + this.isPlaying() + ' is stoped: ' + this.isStopped() + ' pausedForBuffer:' + this.pausedForBuffer + ' playtime:' + this.smilPlayTime);

		// Check if we reached playSegmentEndTime and pause playback
		if( this.playSegmentEndTime && this.smilPlayTime >= this.playSegmentEndTime ) {
			mw.log("monitor:: Reached playSegmentEndTime pause playback: " + this.playSegmentEndTime );
			$j( this ).trigger( 'playSegmentEnd' );
			this.playSegmentEndTime= null;
			this.pause();
			this.parent_monitor();
			return ;
		}

		// Update the bufferedPercent
		this.bufferedPercent = this.smil.getBufferedPercent();

		// Update the smilPlayTime if playing
		if( this.isPlaying() ){

			// Check for buffer under-run if so don't update time
			var syncDelta = this.smil.getPlaybackSyncDelta( this.smilPlayTime );
			// if not in sync update the master playhead
			if( syncDelta != 0 &&
				( syncDelta > this.maxSyncDelta
				||
				this.pausedForBuffer
				)
			){
				mw.log('EmbedSmilPlayer:: monitor: syncDelta too large buffering: ' +syncDelta );
				this.pausedForBuffer = true;
				this.clockStartTime += syncDelta + this.monitorRate;
				this.parent_monitor();
				this.controlBuilder.setStatus( gM('mwe-embedplayer-buffering') );
				return ;
			}

			if( !this.pausedForBuffer ){
				// Update playtime if not pausedForBuffer
				this.smilPlayTime = this.smilPauseTime +
					( ( new Date().getTime() - this.clockStartTime ) / 1000 );

				/*mw.log(" update smilPlayTime: " + this.smilPauseTime + " getTime: " + new Date().getTime() +
						' - clockStartTime: ' + this.clockStartTime + ' = ' +
						( ( new Date().getTime() - this.clockStartTime ) / 1000 ) +
						" \n time:" + this.smilPlayTime );*/

			}

			// Reset the pausedForBuffer flag:
			this.pausedForBuffer = false;

			//mw.log( "Call animateTime: " + this.smilPlayTime);
			// Issue an animate time request with monitorDelta
			this.smil.animateTime( this.smilPlayTime, this.monitorRate );
		}

		this.parent_monitor();
	},

	/**
	* Get the smil object. If the smil object does not exist create one with the source url:
	* @param callback
	*/
	getSmil: function( callback ){
		var _this = this;
		if( !this.smil ) {
			// Create the Smil engine object
			this.smil = new mw.Smil( this );

			// Load the smil
			this.smil.loadFromUrl( this.getSrc(), function(){
				callback( _this.smil );
			});
		} else {
			callback( this.smil );
		}
	},

	/**
	* Get the duration of smil document.
	*/
	getDuration: function( forceRefresh ){
		if( forceRefresh ){
			this.duration = null;
		}
		if( !this.duration ){
			if( this.smil ){
				this.duration = this.smil.getDuration( forceRefresh );
			} else {
				this.duration = this.parent_getDuration();
			}
		}
		// If we forceRefresh duration stop playback if playing, so we can update the interface.
		if( forceRefresh )
			this.stop();

		return this.duration;
	},

	/**
	* Return the virtual canvas element
	*/
	getPlayerElement: function(){
		// return the virtual canvas
		return $j( '#smilCanvas_' + this.id ).get(0);
	},

	/**
	* Update the thumbnail html
	*/
	updatePosterHTML: function() {
		// If we have a "poster" use that;
		if( this.poster ){
			this.parent_updatePosterHTML();
			return ;
		}
		// If no thumb could be found use the first frame of smil:
		this.doEmbedHTML();
	},

	/**
	 * Smil Engine utility functions
	 */

	/**
	 * Returns an array of audio urls, start and end points.
	 *
	 * This is used to support flattening by building a set of
	 * start and end points for a series of audio files or audio
	 * tracks from movie files.
	 */
	getAudioTimeSet: function(){
		if(!this.smil){
			return null;
		}
		return this.smil.getAudioTimeSet();
	}

}
