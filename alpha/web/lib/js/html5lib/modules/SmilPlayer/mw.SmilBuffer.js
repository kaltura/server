/**
* Handles buffer information for the smilObject
*/

mw.SmilBuffer = function( smilObject ){
	return this.init( smilObject );
};

mw.SmilBuffer.prototype = {

	// Stores currently loading assets.
	assetLoadingSet: [],

	// A queue for asset loaded callbacks
	assetLoadingCallbacks : [],

	// Stores the percentage loaded of active video elements
	mediaLoadedPercent: {},

	// Stores seek listeners for active video elements
	mediaSeekListeners: {},

	// Stores the previous percentage buffered ( so we know what elements to check )
	prevBufferPercent : 0,

	/**
	* Constructor:
	*/
	init: function( smilObject ) {
		this.smil = smilObject;
	},

	/**
	 * Get the buffered percent
	 */
	getBufferedPercent: function(){
		var _this = this;

		// If we already have 100% return directly
		if( this.prevBufferPercent == 1 ) {
			return 1;
		}

		// Search for elements from the prevBufferPercent
		var bufferedStartTime = this.prevBufferPercent * _this.smil.getDuration();

		//mw.log("getBufferedPercent:: bufferedStartTime: " + bufferedStartTime );

		// Average the buffers of clips in the current time range:
		var bufferCount =0;
		var totalBufferPerc = 0;
		var minTimeBuffred = false;
		var maxTimeBuffred = 0;
		this.smil.getBody().getElementsForTime( bufferedStartTime, function( smilElement ){
			var relativeStartTime = $j( smilElement ).data ( 'startOffset' );
			var nodeBufferedPercent = _this.getElementPercentLoaded( smilElement );

			// xxx BUG in firefox buffer sometimes hangs at 93-99%
			if( nodeBufferedPercent > .91){
				nodeBufferedPercent= 1;
			}

			// Update counters:
			bufferCount ++;
			totalBufferPerc += nodeBufferedPercent;

			var nodeBuffredTime = relativeStartTime +
				( _this.smil.getBody().getClipDuration( smilElement ) * nodeBufferedPercent );

			//mw.log(" asset:" + $j( smilElement ).attr('id') + ' is buffered:' + nodeBufferedPercent + 'buffer time: ' + nodeBuffredTime );


			// Update min time buffered ( if the element is not 100% buffered )
			if( nodeBufferedPercent != 1 &&
				(
					minTimeBuffred === false
					||
					nodeBuffredTime < minTimeBuffred
				)
			){
				minTimeBuffred = nodeBuffredTime;
			}

			// Update the max time buffered
			if( nodeBuffredTime > maxTimeBuffred ){
				maxTimeBuffred = nodeBuffredTime;
			}
		});

		// Check if all the assets are full for this time rage:
		if( totalBufferPerc == bufferCount ) {
			if( maxTimeBuffred == 0 )
				return 0;
			var newBufferPercet = maxTimeBuffred / _this.smil.getDuration();
			if( newBufferPercet != this.prevBufferPercent ){
				// Update the prevBufferPercent and recurse
				this.prevBufferPercent = newBufferPercet;
				return this.getBufferedPercent();
			} else {
				return 1;
			}
		}
		// update the previous buffer and return the minimum in range buffer percent
		this.prevBufferPercent = minTimeBuffred / _this.smil.getDuration();
		return this.prevBufferPercent;
	},

	/**
	 * Start loading every asset in the smil sequence set.
	 */
	startBuffer: function( ){
		this.continueBufferLoad( 0 );
	},

	/**
	 * continueBufferLoad the buffer
	 * @param bufferTime The base time to load new buffer items into
	 */
	continueBufferLoad: function( bufferTime ){
		var _this = this;
		// Get all active elements for requested bufferTime
		this.smil.getBody().getElementsForTime( bufferTime, function( smilElement){
			// If the element is in "activePlayback" ( don't try to load it )
			if( ! $j( smilElement ).data('activePlayback' ) ){
				// Start loading active assets
				_this.loadElement( smilElement );
			}
		});
		// Loop on loading until all elements are loaded
		setTimeout( function(){
			if( _this.getBufferedPercent() == 1 ){
				//mw.log( "smilBuffer::continueBufferLoad:: done loading buffer for " + bufferTime);
				return ;
			}
			// get the percentage buffered, translated into buffer time and call continueBufferLoad with a timeout
			var timeBuffered = _this.getBufferedPercent() * _this.smil.getDuration();
			//mw.log( 'ContinueBufferLoad::Timed buffered: ' + timeBuffered );
			_this.continueBufferLoad( timeBuffered );
		}, this.smil.embedPlayer.monitorRate * 4 );

	},

	/**
	 * Start loading and buffering an target smilElement
	 */
	loadElement: function( smilElement ){
		var _this = this;

		// If the element is not already in the DOM add it as an invisible element
		if( $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).length == 0 ){
			// Draw the element
			_this.smil.getLayout().drawElement( smilElement );
			// Hide the element ( in modern browsers this should not cause a flicker
			// because DOM update are displayed at a given dom draw rate )
			_this.smil.getLayout().hideElement( smilElement );
			mw.log('loadElement::Add:' + this.smil.getSmilElementPlayerID( smilElement ) +
					' len: ' + $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).length );
		}

		// Start "loading" the asset (for now just audio/video )
		// but in theory we could set something up with large images / templates etc.
		switch( this.smil.getRefType( smilElement ) ){
			case 'audio':
			case 'video':
				var media = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) )
								.find('audio,video').get(0);
				if( !media ){
					break;
				}
				// The load request does not work very well instead .play() then .pause() and seek when on display
				media.load(); // try to use the load command anyway: 
				
				// Since we can't use "load" across html5 implementations do some hacks:
				/*if( media.paused && this.getMediaPercetLoaded( smilElement ) == 0 ){
					// Issue the load / play request
					media.play();
					media.volume = 0;
					// XXX seek to clipBegin if provided ( we don't need to load before that point )
				} else {
					//mw.log("loadElement:: pause video: " + this.smil.getSmilElementPlayerID( smilElement ));
					// else we have some percentage loaded pause playback
					//( should continue to load the asset )
					media.pause();
				}*/
			break;
		}
	},

	/**
	 * Get the percentage of an element that is loaded.
	 */
	getElementPercentLoaded: function( smilElement ){
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
			case 'audio':
				return this.getMediaPercetLoaded( smilElement );
			break;
		}
		// for other ref types check if element is in the dom
		// xxx todo hook into image / template loaders
		if( $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).length == 0 ){
			return 0;
		} else {
			return 1;
		}
	},

	/**
	 * Get the percentage of a video asset that has been loaded
	 */
	getMediaPercetLoaded: function ( smilElement ){
		var _this = this;
		var assetId = this.smil.getSmilElementPlayerID( smilElement );
		var $media = $j( '#' + assetId ).find('audio,video');

		// if the asset is not in the DOM return zero:
		if( $media.length == 0 ){
			return 0 ;
		}
		// check if 100% has already been loaded:
		if( _this.mediaLoadedPercent[ assetId ] == 1 ){
			return 1;
		}

		// Check if we have a loader registered
		if( !this.mediaLoadedPercent[ assetId ] ){
			// firefox loading based progress indicator:
			$media.unbind('progress').bind('progress', function( e ) {
				var eventData = e.originalEvent;
				//mw.log("Video loaded progress:" + assetId +' ' + (eventData.loaded / eventData.total ) );
				if( eventData.loaded && eventData.total ) {
					_this.mediaLoadedPercent[assetId] = eventData.loaded / eventData.total;
				}
			});
		}

		// Set up reference to media object:
		var media = $media.get(0);
		// Check for buffered attribute ( not all browsers support the progress event )
		if( media && media.buffered && media.buffered.end && media.duration ) {
			_this.mediaLoadedPercent[ assetId ] = ( media.buffered.end(0) / media.duration);
		}

		if( !_this.mediaLoadedPercent[ assetId ] ){
			return 0;
		} else {
			// Return the updated mediaLoadedPercent
			return _this.mediaLoadedPercent[ assetId ];
		}
	},


	/**
	* Add a callback for when assets loaded and "ready"
	*/
	addAssetsReadyCallback: function( callback ) {
		//mw.log( "smilBuffer::addAssetsReadyCallback:" + this.assetLoadingSet.length );
		// if no assets are "loading" issue the callback directly:
		if ( this.assetLoadingSet.length == 0 ){
			if( callback )
				callback();
			return ;
		}
		// Else we need to add a loading callback ( will be called once all the assets are ready )
		this.assetLoadingCallbacks.push( callback );
	},

	/**
	* Add a asset to the loading set:
	* @param assetId The asset to add to loading set
	*/
	addAssetLoading: function( assetId ) {
		if( $j.inArray( assetId, this.assetLoadingSet ) !== -1 ){
			mw.log("Possible Error: assetId already in loading set: " + assetId ) ;
			return ;
		}
		this.assetLoadingSet.push( assetId );
	},

	/**
	* Asset is ready, check queue and issue callback if empty
	*/
	assetReady: function( assetId ) {
		//mw.log("SmilBuffer::assetReady:" + assetId);
		for( var i=0; i < this.assetLoadingSet.length ; i++ ){
			if( assetId == this.assetLoadingSet[i] ) {
				 this.assetLoadingSet.splice( i, 1 );
			}
		}
		if( this.assetLoadingSet.length === 0 ) {
			while( this.assetLoadingCallbacks.length ) {
				this.assetLoadingCallbacks.shift()();
			}
		}
	},

	/**
	 * Clip ready for grabbing a frame such as a canvas thumb
	 */
	bufferedSeekRelativeTime: function( smilElement, relativeTime, callback ){
		var absoluteTime = relativeTime;
		if( $j( smilElement ).attr('clipBegin') ){
			absoluteTime += this.smil.parseTime( $j( smilElement ).attr('clipBegin') );
		}
		mw.log("SmilBuffer::bufferedSeekRelativeTime:" + this.smil.getSmilElementPlayerID( smilElement ) + ' relativeTime: ' + relativeTime + ' absoluteTime:' + absoluteTime );
		
		$j( smilElement ).data('activeSeek', true);
		var instanceCallback = function(){			
			$j( smilElement ).data('activeSeek', false);
			callback();
		};
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
			case 'audio':			
				this.mediaBufferSeek( smilElement, absoluteTime, function(){
					mw.log("SmilBuffer::bufferedSeekRelativeTime: callback time: " + absoluteTime + ' ready ');
					instanceCallback();
				});
			break;
			case 'img':
				this.loadImageCallback( smilElement, instanceCallback );
			break;
			case 'mwtemplate':
				this.loadMwTemplate( smilElement, instanceCallback );
			break;
			default:
				// Assume other formats are non-blocking and directly displayed
				instanceCallback();
			break;
		}
	},

	/**
	 * Check if we can play a given time
	 * @return {boolean} True if the time can be played, false if we need to buffer
	 */
	canPlayTime: function( smilElement, time ){
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
			case 'audio':
				return this.canPlayMediaTime( smilElement, time );
			break;
		}
		// by default return true
		return true;
	},
  
	/**
	 * Register a video loading progress indicator and check the time against the requested time
	 */
	canPlayMediaTime: function( smilVideoElement, time ){
		var _this = this;
		var assetId = this.smil.getSmilElementPlayerID( smilVideoElement );
		var $media = $j( '#' + assetId ).find('audio,video');
		var vid = $j( '#' + assetId ).get( 0 );
		// if the video element is not in the dom its not ready:
		if( $media.length == 0 || !$media.get(0) ){
			return false;
		}
		/* if we have no metadata return false */
		if( $media.attr('readyState') == 0 ){
			return false;
		}
		/* if we are asking about a time close to the current time use ready state */
		if( Math.abs( $media.attr('currentTime') - time ) < 1 ){
			// also see: http://www.whatwg.org/specs/web-apps/current-work/multipage/video.html#dom-media-have_metadata
			if( $media.attr('readyState') > 2 ){
				return true;
			}
		}
		// Check if _this.mediaLoadedPercent is in range of duration
		// xxx might need to take into consideration startOfsset
		if( _this.getMediaPercetLoaded( smilVideoElement ) > vid.duration / time ){
			return true;
		}
		// not likely that the video is loaded for the requested time, return false
		return false;
	},

	/**
	 * Abstract the seeked Listener so we don't have stacking bindings
	 */
	registerVideoSeekListener: function( assetId ){
		var _this = this;
		//mw.log( 'SmilBuffer::registerVideoSeekListener: ' + assetId );
		var vid = $j ( '#' + assetId).get(0);
		vid.addEventListener( 'seeked', function(){
			// Run the callback
			if( _this.mediaSeekListeners[ assetId ].callback ) {
				_this.mediaSeekListeners[ assetId ].callback();
			}
		}, false);
	},
	loadMwTemplate: function( smilElement, callback ){
		var assetId = this.smil.getSmilElementPlayerID( smilElement );

		// Set a load callback for the asset:
		$j( smilElement ).data('loadCallback',callback);
		this.loadElement( smilElement );
		mw.log( "loadMwTemplateCallback:: drwa img: " + assetId + ' ' + $j( '#' + assetId ).length );
	},

	loadImageCallback: function ( smilElement, callback ){
		var assetId = this.smil.getSmilElementPlayerID( smilElement );
		// Make sure the image is in the dom ( load it )
		this.loadElement( smilElement );
		mw.log( "loadImageCallback:: drwa img: " + assetId + ' found:' + $j( '#' + assetId ).length );
		// If we already have naturalHeight no need for loading callback
		if( $j( '#' + assetId ).get(0).naturalHeight ){
			mw.log( "loadImageCallback: " +assetId + ' already ready: run callback' );
			callback();
		} else {
			$j( '#' + assetId ).find('img').load( callback );
		}
	},
	/**
	 * once browsers work better with seeks we can use this code: 
	 */
	mediaBufferSeek: function ( smilElement, seekTime, callback ){
		var _this = this;
		var assetId = this.smil.getSmilElementPlayerID( smilElement );
		
		// Make sure the target video is in the dom:
		this.loadElement( smilElement );
		var $media = $j( '#' + assetId ).find('audio,video');
		var media = $media.get(0);
		
		mw.log("SmilBuffer::mediaBufferSeek: " + assetId + ' ctime:' + media.currentTime + ' seekTime:' + seekTime );
		var mediaLoadedFlag = false;
		var mediaMetaLoaded = function(){
			mw.log("SmilBuffer::mediaBufferSeek: Bind against: "  + $media.parent().attr('id') );
			// check if we need to issue a seek
			if( media.currentTime == seekTime ){
				mw.log("SmilBuffer::mediaBufferSeek: Already at target time:" + assetId + ' time:' + seekTime );
				if( callback )
					callback();
				callback = null;
				return ;
			}
			
			// Register the seeked callback ( throw away any timed out seek request ) 
			$media.unbind('seeked.smilBuffer').bind('seeked.smilBuffer', function(){
				mw.log("SmilBuffer::mediaBufferSeek: DONE for:" + assetId + ' time:' + media.currentTime );
				
				// TODO Would be great if browsers supported a mode to "stop" loading once we reach a given time
				
				if( callback )
					callback();
				callback = null;
			});
			
			$media.unbind('seeking.smilBuffer').bind( 'seeking.smilBuffer', function(){
				mw.log("SmilBuffer::mediaBufferSeek: SEEKING:" + assetId ); 
				media.pause();
				// Add a timeout to seek request to try again
				setTimeout(function(){
					// if the callback has not been called retry: 
					if( callback ){
						mw.log("SmilBuffer::mediaBufferSeek:seeking TimeOut ( retry ) " + assetId );
						mediaMetaLoaded();
					} else{
						mw.log( "SmilBuffer::mediaBufferSeek:seeking OK currentTime: " + media.currentTime + ' seekTime:' + seekTime);
					}
				}, 2000 );
			});
			// issue a play ( makes seeks work more consistently than just load ) 
			media.play();
			setTimeout(function(){
				mw.log("SmilBuffer::mediaBufferSeek: SET: " + assetId + ' to: ' + seekTime);
				// Issue the seek
				try{
					media.pause();
					media.currentTime = seekTime;
					media.play();
				} catch ( e ){
					mw.log( 'Error: in SmilBuffer could not set currentTime for ' + assetId );
				}
			}, 10 ); // give the browser 10ms to make sure the video tag is " really ready " 
		};
		mw.log("SmilBuffer::mediaBufferSeek: " + assetId + " READY State: " + $media.attr('readyState') );
		// Read the video state: http://www.w3.org/TR/html5/video.html#dom-media-have_nothing
		if( $media.attr('readyState') === 0 ){ // HAVE_NOTHING 
			// Check that we have metadata ( so we can issue the seek )			
			$media.bind( 'loadedmetadata.smilBuffer', function(){
				media.pause();
				$media.unbind( 'loadedmetadata.smilBuffer' );
				mediaMetaLoaded();
			} );
			media.load();
			media.play();
		}else {
			// Already have metadata directly issue the seek with callback
			mediaMetaLoaded();
		}
	}
	/*mediaBufferSeek: function ( smilElement, seekTime, callback ){
		var _this = this;
		//mw.log("SmilBuffer::mediaBufferSeek: " + this.smil.getSmilElementPlayerID( smilElement ) +' time:' + seekTime );

		// Get the asset target:
		var assetId = this.smil.getSmilElementPlayerID( smilElement );

		// Make sure the target video is in the dom:
		this.loadElement( smilElement );
		var $media = $j( '#' + assetId ).find('audio,video');
		var media = $media.get(0);
		
		// Add the asset to the loading set (if not there already )
		if( !_this.mediaSeekListeners[ assetId ] ){			
			_this.addAssetLoading( assetId );
		}
		var seekCallbackDone = false;
		var runSeekCallback = function(){

			// Register an object for the current asset seek Listener
			if( ! _this.mediaSeekListeners[ assetId ] ){
				_this.mediaSeekListeners[ assetId ]= {};
			};

			if( ! _this.mediaSeekListeners[ assetId ].listen ){
				_this.mediaSeekListeners[ assetId ].listen = true;
				_this.registerVideoSeekListener( assetId );
			}
			// Update the current context callback
			_this.mediaSeekListeners[ assetId ].callback = function(){
				// Seek has completed open up seek Listeners for future seeks
				_this.mediaSeekListeners[ assetId ].listen = false;

				// Set this asset to ready ( asset ready set )
				_this.assetReady( assetId );

				// Run the callback
				if( callback ){
					callback();
					// set the callback to null in case seeked is fired twice.
					callback = null;
				}
			}

			// Issue the seek
			try{
				media.currentTime = seekTime;
			} catch ( e ){
				mw.log( 'Error: in SmilBuffer could not set currentTime' );
			}
		}

		// Read the video state: http://www.w3.org/TR/html5/video.html#dom-media-have_nothing
		if( $media.attr('readyState') == 0 ){ // HAVE_NOTHING 
			// Check that we have metadata ( so we can issue the seek )
			$media.unbind( 'loadedmetadata' ).bind( 'loadedmetadata', function(){
				runSeekCallback();
			} );
		}else {
			// Already have metadata directly issue the seek with callback
			runSeekCallback();
		}
	}*/
};
