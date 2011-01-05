/*
* Handles driving the firefogg render system
*/

/*
* Set the jQuery bindings:
*/
( function( $ ) {
	$.fn.firefoggRender = function( options ) {
		if(!options)
			options = {};
		options.playerTarget = this.selector;
		var myFogg = new mw.FirefoggRender( options );
		return myFogg;
	};
} )( jQuery );


mw.FirefoggRender = function( options ) {
	return this.init( options );
};
// Set up the mvPlaylist object
mw.FirefoggRender.prototype = {

	// Default render options:
	renderOptions: {
		"videoQuality" : 8,
		"framerate"	: 30
	},

	// Render time
	renderTime: null,

	// The interval time ( set via requested framerate)
	interval: null,

	// Continue rendering
	continueRendering:false,

	// Start time for rendering
	startTime: 0,

	// Callback for when the render with a pointer to the firefogg object
	doneRenderCallback: null,

	// Bollean attribute if we should save to local file
	saveToLocalFile : true,

	// Callback function for render progress
	onProgress: null,

	// Constructor
	init:function( options ) {
		var _this = this;

		// Grab the mvFirefogg object to do basic tests
		this.myFogg = new mw.Firefogg( {
			'only_fogg':true
		});

		// Check for firefogg:
		if ( this.myFogg.getFirefogg() ) {
			this.enabled = true;
		} else {
			this.enabled = false;
			mw.log('Error firefogg not installed');
			return this;
		}

		// Setup local fogg pointer:
		this.fogg = this.myFogg.fogg;

		// Setup player instance
		this.playerTarget = options.playerTarget;

		// Extend the render options with any provided details
		if( options['renderOptions'] ){
			this.renderOptions = $j.extend( {}, this.renderOptions, options['renderOptions'] );
		}

		if( options ['statusTarget']){
			this.statusTarget = options ['statusTarget'];
		}

		if( options [ 'doneRenderCallback' ] ){
			this.doneRenderCallback = options [ 'doneRenderCallback' ];
		}
		// xxx should probably be a normal event binding .. oh well
		if( options['onProgress'] ){
			this.onProgress = options['onProgress'];
		}


		if( typeof options['saveToLocalFile'] != 'undefiend' ){
			this.saveToLocalFile = options['saveToLocalFile'] ;
		}
		// If no height width provided use target DOM width/height
		if( !this.renderOptions.width && !this.renderOptions.height ) {
			this.renderOptions.width = $j(this.playerTarget).width();
			this.renderOptions.height = $j(this.playerTarget).height();
		}

	},
	getPlayer: function(){
		return $j( this.playerTarget ).get( 0 );
	},
	// Start rendering
	doRender: function() {
		var _this = this;
		// Check if we save the file to disk:
		if( this.saveToLocalFile ){
			if( !_this.fogg.saveVideoAs() ){
				return false;
			}
		}
		// Set the render time to "startTime" of the render request
		this.renderTime = this.startTime;

		// Get the interval from renderOptions framerate
		this.interval = 1 / this.renderOptions.framerate;

		// Set the continue rendering flag to true:
		this.continueRendering = true;

		// Set a target file:
		mw.log( "Firefogg Render Settings:" + JSON.stringify( _this.renderOptions ) );
		this.fogg.initRender( JSON.stringify( _this.renderOptions ), 'foggRender.ogv' );

		// Add audio if we had any:
		var audioSet = this.getPlayer().getAudioTimeSet();
		var previusAudioTime = 0;
		for( var i=0; i < audioSet.length ; i++) {
			var currentAudio = audioSet[i];
			// Check if we need to add silence
			if( currentAudio.startTime > previusAudioTime ){
				mw.log("FirefoggRender::addSilence " + ( currentAudio.startTime - previusAudioTime ));
				this.fogg.addSilence( currentAudio.startTime - previusAudioTime );
			}
			// Add the block of audio from the url
			mw.log("FirefoggRender::addAudioUrl " + currentAudio.src +
					', ' + currentAudio.offset + ', ' + currentAudio.duration );
			this.fogg.addAudioUrl( currentAudio.src, currentAudio.offset, currentAudio.duration );

			// Update previusAudioTime
			previusAudioTime = currentAudio.startTime + currentAudio.duration;
		}
		// xxx localize status?
		$j( _this.statusTarget ).text( 'rendering' );
		// Now issue the save video as call
		_this.doNextFrame();
		return true;
	},

	/**
	* Do the next frame in the render target
	*/
	doNextFrame: function() {
		var _this = this;
		// internal function to handle updates:
		/*mw.log( "FirefoggRender::doNextFrame: on " + ( Math.round( _this.renderTime * 10 ) / 10 ) + " of " +
			( Math.round( _this.player.getDuration() * 10 ) / 10 ) );
		*/
		if( this.onProgress ){
			this.onProgress(
				_this.renderTime / _this.getPlayer().getDuration()
			);
		}

		_this.getPlayer().setCurrentTime( _this.renderTime, function() {
			_this.fogg.addFrame( $j( _this.playerTarget ).attr( 'id' ) );
			//	$j( _this.statusTarget ).text( "AddFrame::" + ( Math.round( _this.renderTime * 1000 ) / 1000 ) );
			_this.renderTime += _this.interval;

			if ( _this.renderTime >= _this.getPlayer().getDuration() || ! _this.continueRendering ) {
				_this.doFinalRender();
			} else {
				// Don't block on render
				setTimeout( function(){
					_this.doNextFrame();
				}, 1);
			}
		}, true /* hide the buffer overlay */ );
	},

	/**
	* Stop the current render process on the next frame
	*/
	stopRender: function() {
		this.continueRendering = false;
	},

	/**
	* Issue the call to firefogg to render out the ogg video
	*/
	doFinalRender: function() {
		mw.log("FirefoggRender:: doFinalRenderr" );
		this.fogg.render();
		this.checkRenderStatus();
	},

	/**
	* Update the render status
	*/
	checkRenderStatus: function() {
		var _this = this;
		// Check if we are still rendering
		var rstatus = _this.fogg.renderstatus();
		$j( _this.statusTarget ).text( rstatus );
		if ( rstatus != 'done' && rstatus != 'rendering failed' ) {
			setTimeout( function() {
				_this.checkRenderStatus();
			}, 100 );
			return ;
		}
		if( rstatus == 'rendering failed' ){
			mw.log("Error: rendering failed");
			return;
		}
		if( this.doneRenderCallback ){
			// Pass the firefogg object to the render done callback for other operations
			// ( such as uploading the asset )
			this.doneRenderCallback( this.fogg );
		}
	}
};