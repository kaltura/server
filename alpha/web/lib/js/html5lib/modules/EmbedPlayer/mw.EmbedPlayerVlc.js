/*
* VLC embed based on: http://people.videolan.org/~damienf/plugin-0.8.6.html
* javascript api: http://www.videolan.org/doc/play-howto/en/ch04.html
*  assume version > 0.8.5.1
*/
mw.EmbedPlayerVlc = {

	//Instance Name:
	instanceOf : 'Vlc',

	//What the vlc player / plug-in supports:
	supports : {
		'playHead':true,
		'pause':true,
		'stop':true,
		'fullscreen':true,
		'timeDisplay':true,
		'volumeControl':true,

		'playlist_driver':true, // if the object supports playlist functions
		'overlay':false
	},

	// The previous state of the player instance
	prevState : 0,

	// Counter for waiting for vlc embed to be ready
	waitForVlcCount:0,

	// Store the current play time for vlc
	vlcCurrentTime: 0,

	/**
	* Get embed HTML
	*/
	doEmbedHTML: function() {
		var _this = this;
		$j( this ).html(
			'<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" ' +
				'codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab#Version=0,8,6,0" ' +
				'id="' + this.pid + '" events="True" height="' + this.getPlayerHeight() + '" width="' + this.getPlayerWidth() + '"' +
				'>' +
					'<param name="MRL" value="">' +
					'<param name="ShowDisplay" value="True">' +
					'<param name="AutoLoop" value="False">' +
					'<param name="AutoPlay" value="False">' +
					'<param name="Volume" value="50">' +
					'<param name="StartTime" value="0">' +
					'<embed pluginspage="http://www.videolan.org" type="application/x-vlc-plugin" ' +
						'progid="VideoLAN.VLCPlugin.2" name="' + this.pid + '" ' +
						'height="' + this.getHeight() + '" width="' + this.getWidth() + '" ' +
						// set the style too 'just to be sure'
						'style="width:' + this.getWidth() + 'px;height:' + this.getHeight() + 'px;" ' +
					'>' +
			'</object>'
		)
		/*
			$j( this ).html(
			'<embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org" version="VideoLAN.VLCPlugin.2" '+
				'width="' + this.width +'" ' +
				'height="' + this.height + '" ' +
				'id="' + this.pid + '"> ' +
			'</embed>'
		);*/


		// give VLC 150ms to initialize before we start playback
		// @@todo should be able to do this as an ready event
		this.waitForVlcCount = 0;
		setTimeout( function() {
			_this.postEmbedJS();
		}, 150 );
	},

	/**
	* Javascript to run post vlc embedding
	* Inserts the requested src to the embed instance
	*/
	postEmbedJS: function() {
		var _this = this;
		// load a pointer to the vlc into the object (this.playerElement)
		this.getPlayerElement();
		if ( this.playerElement && this.playerElement.playlist) {
			// manipulate the dom object to make sure vlc has the correct size:
			this.playerElement.style.width = this.getWidth();
			this.playerElement.style.height = this.getHeight();
			this.playerElement.playlist.items.clear();

			// VLC likes absolute urls:
			var src = mw.absoluteUrl( this.getSrc() ) ;

			// @@todo if client supports seeking no need to send seek_offset to URI
			mw.log( 'vlc play::' + src );
			var itemId = this.playerElement.playlist.add( src );
			if ( itemId != -1 ) {
				// Play
				this.playerElement.playlist.playItem( itemId );
			} else {
				mw.log( "error:cannot play at the moment !" );
			}
			setTimeout( function() {
				_this.monitor();
			}, 100 );
		} else {
			mw.log( 'postEmbedJS: vlc not ready' );
			this.waitForVlcCount++;
			if ( this.waitForVlcCount < 10 ) {
				setTimeout( function() {
					_this.postEmbedJS();
				}, 100 );
			} else {
				mw.log( 'vlc never ready' );
			}
		}
	},

	/**
	* Handles seek requests based on temporal media source type support
	*
	* @param {Float} percent Seek to this percent of the stream
	*/
	doSeek : function( percent ) {
		this.getPlayerElement();
		if ( this.supportsURLTimeEncoding() ) {
			this.parent_doSeek( percent );
		} else if ( this.playerElement ) {
			this.seeking = true;
			mw.log( "do vlc http seek to: " + percent )
			if ( ( this.playerElement.input.state == 3 ) && ( this.playerElement.input.position != percent ) )
			{
				this.playerElement.input.position = percent;
				this.controlBuilder.setStatus( 'seeking...' );
			}
		} else {
			this.doPlayThenSeek( percent );
		}
		this.parent_monitor();
	},

	/**
	* Issues a play request then seeks to a given time
	*
	* @param {Float} percent Seek to this percent of the stream after playing
	*/
	doPlayThenSeek:function( percent ) {
		mw.log( 'doPlayThenSeekHack' );
		var _this = this;
		this.play();
		var rfsCount = 0;
		var readyForSeek = function() {
			_this.getPlayerElement();
			var newState = _this.playerElement.input.state;
			// if playing we are ready to do the
			if ( newState == 3 ) {
				_this.doSeek( percent );
			} else {
				// try to get player for 10 seconds:
				if ( rfsCount < 200 ) {
					setTimeout( readyForSeek, 50 );
					rfsCount++;
				} else {
					mw.log( 'error:doPlayThenSeek failed' );
				}
			}
		}
		readyForSeek();
	},

	/**
	* Updates the status time and player state
	*/
	monitor: function() {
		this.getPlayerElement();
		if ( !this.playerElement )
			return ;
		try{
			//mw.log( 'state:' + this.playerElement.input.state);
			//mw.log('time: ' + this.playerElement.input.time);
			//mw.log('pos: ' + this.playerElement.input.position);
			if ( this.playerElement.log.messages.count > 0 ) {
				// there is one or more messages in the log
				var iter = this.playerElement.log.messages.iterator();
				while ( iter.hasNext ) {
					var msg = iter.next();
					var msgtype = msg.type.toString();
					if ( ( msg.severity == 1 ) && ( msgtype == "input" ) )
					{
						mw.log( msg.message );
					}
				}
				// clear the log once finished to avoid clogging
				this.playerElement.log.messages.clear();
			}

			var newState = this.playerElement.input.state;
			if ( this.prevState != newState ) {
				if ( newState == 0 )
				{
					// current media has stopped
					this.onStop();
				}
				else if ( newState == 1 )
				{
					// current media is opening/connecting
					this.onOpen();
				}
				else if ( newState == 2 )
				{
					// current media is buffering data
					this.onBuffer();
				}
				else if ( newState == 3 )
				{
					// current media is now playing
					this.onPlay();
				}
				else if ( this.playerElement.input.state == 4 ) {
					// current media is now paused
					this.onPause();
				}
				this.prevState = newState;
			} else if ( newState == 3 ) {
				// current media is playing
				this.onPlaying();
			}
		} catch( e ){
			mw.log("EmbedPlayerVlc::Monitor error");
		}
		// update the status and check timmer via universal parent monitor
		this.parent_monitor();
	},

	/**
	* Events:
	*  NOTE : should be localized:
	*/
	onOpen: function() {
		this.controlBuilder.setStatus( "Opening..." );
	},
	onBuffer: function() {
		this.controlBuilder.setStatus( "Buffering..." );
	},
	onPlay: function() {
		this.onPlaying();
	},
	onPlaying: function() {
		this.seeking = false;
		// for now trust the duration from url over vlc input.length
		if ( !this.getDuration() && this.playerElement.input.length > 0 )
		{
			// mw.log('setting duration to ' + this.playerElement.input.length /1000);
			this.duration = this.playerElement.input.length / 1000;
		}
		this.vlcCurrentTime = this.playerElement.input.time / 1000;
	},

	/**
	* Get the embed player time
	*/
	getPlayerElementTime: function(){
		return this.vlcCurrentTime;
	},

	onPause: function() {
		this.parent_pause(); // update the inteface if paused via native control
	},
	onStop: function() {
		mw.log( 'vlc:onStop:' );
		if ( !this.seeking )
			this.onClipDone();
	},

	/**
	* Handles play requests
	*/
	play : function() {
		mw.log( 'f:vlcPlay' );
		// Update the interface
		this.parent_play();
		if ( this.getPlayerElement() ) {
			// plugin is already being present send play call:
			// clear the message log and enable error logging
			if ( this.playerElement.log ) {
				this.playerElement.log.messages.clear();
			}
			if ( this.playerElement.playlist && typeof this.playerElement.playlist.play == 'function')
				this.playerElement.playlist.play();

			if( typeof this.playerElement.play == 'function' )
				this.playerElement.play();

			this.paused = false;

			// re-start the monitor:
			this.monitor();
		}
	},

	/**
	* Passes the Pause request to the plugin.
	* calls parent "pause" to update interface
	*/
	pause : function() {
		this.parent_pause(); // update the interface if paused via native control
		if ( this.getPlayerElement() ) {
			try{
				this.playerElement.playlist.togglePause();
			} catch( e ){
				mw.log("EmbedPlayerVlc could not pause video " + e);
			}
		}
	},

	/**
	* Mutes the video
	* calls parent "toggleMute" to update interface
	*/
	toggleMute:function() {
		this.parent_toggleMute();
		if ( this.getPlayerElement() )
			this.playerElement.audio.toggleMute();
	},

	/**
	* Update the player volume
	* @pram {Float} percent Percent of total volume
	*/
	setPlayerElementVolume: function ( percent ) {
		if ( this.getPlayerElement() ) {
			this.playerElement.audio.volume = percent * 100;
		}
	},

	/**
	* Gets the current volume
	* @return {Float} percent percent of total volume
	*/
	getVolumen:function() {
		if ( this.getPlayerElement() )
			return this.playerElement.audio.volume / 100;
	},

	/**
	* Passes fullscreen request to plugin
	*/
	fullscreen : function() {
		if ( this.playerElement ) {
			if ( this.playerElement.video ){
				try{
					this.playerElement.video.toggleFullscreen();
				} catch ( e ){
					mw.log("VlcEmbed toggle fullscreen : possible error: " + e);
				}
			}
		}
	},

	/**
	* Get the embed vlc object
	*/
	getPlayerElement : function() {
		this.playerElement = $j( '#' + this.pid ).get(0);
		return this.playerElement;
	}
};
