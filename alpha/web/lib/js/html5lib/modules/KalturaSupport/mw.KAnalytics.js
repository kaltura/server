/**
 * Kaltura style analytics reporting class
 */

// Avoid undefined symbol for javascript response "Kaltura" from the api
window['Kaltura'] = true;

// KAnalytics Constructor
mw.KAnalytics = function( embedPlayer, kalturaClient ){
	this.init( 	embedPlayer, kalturaClient );
}

// Add analytics to the embed player: 
mw.addKAnalytics = function( embedPlayer, kalturaClient ) {
	if( ! embedPlayer.kAnalytics ) {
		embedPlayer.kAnalytics = new mw.KAnalytics( embedPlayer, kalturaClient );
	}
}

mw.KAnalytics.prototype = {

	// The version of kAnalytics
	version : '0.1',
	
	// Local reference to embedPlayer
	embedPlayer: null,
	
	// Report Set object
	reportSet : null,
	
	// Stores the last time we issued a seek event
	// avoids sending lots of seeks while scrubbing 
	lastSeekEventTime: 0,
	
	// Start Time
	startReportTime: 0, 
	
	kEventTypes : {
		'WIDGET_LOADED' : 1,
		'MEDIA_LOADED' : 2,
		'PLAY' : 3,
		'PLAY_REACHED_25' : 4,
		'PLAY_REACHED_50' : 5,
		'PLAY_REACHED_75' : 6,
		'PLAY_REACHED_100' : 7,
		'OPEN_EDIT' : 8,
		'OPEN_VIRAL' : 9,
		'OPEN_DOWNLOAD' : 10,
		'OPEN_REPORT' : 11,
		'BUFFER_START' : 12,
		'BUFFER_END' : 13,
		'OPEN_FULL_SCREEN' : 14,
		'CLOSE_FULL_SCREEN' : 15,
		'REPLAY' : 16,
		'SEEK' : 17,
		'OPEN_UPLOAD' : 18,
		'SAVE_PUBLISH' : 19,
		'CLOSE_EDITOR' : 20,
		'PRE_BUMPER_PLAYED' : 21,
		'POST_BUMPER_PLAYED' : 22,
		'BUMPER_CLICKED' : 23,
		'FUTURE_USE_1' : 24,
		'FUTURE_USE_2' : 25,
		'FUTURE_USE_3' : 26
	},
	
	/**
	 * Constructor for kAnalytics
	 * 
	 * @param {Object}
	 *          embedPlayer Player to apply Kaltura analytics to.
	 * @parma {Object} 
	 * 			kalturaClient Kaltura client object for the api session.  
	 */
	init: function( embedPlayer, kClient ) {
	
		// Setup the local reference to the embed player
		this.embedPlayer = embedPlayer;
		this.kClient = kClient;
		
		// Setup the initial state of some flags
		this._p25Once = false;
		this._p50Once = false;
		this._p75Once = false;
		this._p100Once = false;
		this.hasSeeked = false;
		this.lastSeek = 0;
		
		// Setup the stats service
		//this.kalturaCollector = new KalturaStatsService( kalturaClient );
		
		// Add relevant hooks for reporting beacons
		this.bindPlayerEvents();		
				
	},
	
	/**
	 * Get the current report set
	 * 
	 * @param {Number}
	 *            KalturaStatsEventType The eventType number.
	 */
	sendAnalyticsEvent: function( KalturaStatsEventKey ){
		var _this = this;
		// make sure we have a KS
		this.kClient.getKS( function( ks ){
			_this.doSendAnalyticsEvent( ks, KalturaStatsEventKey );
		})
	},
	doSendAnalyticsEvent: function( ks, KalturaStatsEventKey ){
		var _this = this;				
		
		// get the id for the given event: 	
		var eventKeyId = this.kEventTypes[ KalturaStatsEventKey ];
		
		// Generate the status event 
		var eventSet = {
			'eventType' :	eventKeyId,			
			'clientVer' : this.version,
			'currentPoint' : 	parseInt( this.embedPlayer.currentTime * 1000 ),
			'duration' :	this.embedPlayer.getDuration(),
			'eventTimestamp' : new Date().getTime(),			
			'isFirstInSession' : 'false',
			'objectType' : 'KalturaStatsEvent',
			'partnerId' :	this.kClient.getPartnerId(),		
			'sessionId' :	ks, 
			'uiconfId' : 0
		};				
		
		if( isNaN( eventSet.duration )  ){
			eventSet.duration = 0;
		}
		
		// Set the seek condition:
		eventSet[ 'seek' ] = ( this.hasSeeked ) ? 'true' : 'false';
		
		// Set the 'event:entryId'
		if( this.embedPlayer.kentryid ){
			eventSet[ 'entryId' ] = this.embedPlayer.kentryid;
		} else { 
			// if kentryid is not set, use the selected source url
			eventSet[ 'entryId' ] = this.embedPlayer.getSrc();
		}					

		// Check if Kaltura.AnalyticsCallback is enabled:
		$j( mw ).trigger( 'Kaltura.SendAnalyticEvent', [ KalturaStatsEventKey ] );
		
		var eventRequest = {};
		for( var i in eventSet){
			eventRequest['event:' + i] = eventSet[i];
		}
		// Add in base service and action calls: 
		$j.extend( eventRequest, {
			'action' : 'collect',
			'service' : 'stats'
		} );
		// Do the api request: 
		this.kClient.doRequest( eventRequest );
	},
	
	/**
	 * Binds player events for analytics reporting
	 */ 
	bindPlayerEvents: function(){
	
		// Setup local reference to embedPlayer
		var embedPlayer = this.embedPlayer;
		var _this = this;
		
		// Setup shortcut anonymous function for player bindings
		var b = function( hookName, eventType ){
			$j( _this.embedPlayer ).bind( hookName, function(){
				_this.sendAnalyticsEvent( eventType )
			});
		};
		
		// When the player is ready
		b( 'playerReady', 'WIDGET_LOADED' );
		
		// When the poster or video ( when autoplay ) media is loaded
		b( 'mediaLoaded', 'MEDIA_LOADED' );
		
		// When the play button is pressed or called from javascript
		b( 'play', 'PLAY' );
	
		// When the show Share menu is displayed
		b( 'showShareEvent', 'OPEN_VIRAL' );
		
		// When the show download menu is displayed
		b( 'showDownloadEvent', 'OPEN_DOWNLOAD' );
		
		// When the clip starts to buffer ( not all player types )
		b( 'bufferStartEvent', 'BUFFER_START' );
		
		// When the clip is full buffered
		b( 'bufferEndEvent', 'BUFFER_END' );
		
		// When the fullscreen button is pressed
		// ( presently does not register iPhone / iPad until it has js bindings )
		b( 'openFullScreen', 'OPEN_FULL_SCREEN' );
		
		// When the close fullscreen button is pressed.
		// ( presently does not register iphone / ipad until it has js bindings
		// )
		b( 'closeFullScreen', 'CLOSE_FULL_SCREEN' );
		
		// When the user plays (after the ondone event was fired )
		b( 'replayEvent', 'REPLAY' );	
	
		// Bind on the seek event
		$j( embedPlayer ).bind( 'seeked', function( seekTarget ) {
			// Don't send a bunch of seeks on scrub:
			if( _this.lastSeekEventTime == 0 || 
				_this.lastSeekEventTime + 2000	< new Date().getTime() )
			{
				_this.sendAnalyticsEvent( 'SEEK' ); 
			}
			
			// Update the last seekTime
			_this.lastSeekEventTime =  new Date().getTime();
			
			// Then set local seek flags
			this.hasSeeked = true;		
			this.lastSeek = seekTarget;	
		} );
		
		// Let updateTimeStats handle the currentTime monitor timing

		$j( embedPlayer ).bind( 'monitorEvent', function(){
			_this.updateTimeStats();			
		}); 
				
		 
		/*
		 * Other kaltura event types that are presently not usable in the 
		 * html5 player at this point in time:
		 * 
		 * OPEN_EDIT = 8;
		 * OPEN_REPORT = 11;
		 * OPEN_UPLOAD = 18;
		 * SAVE_PUBLISH = 19;
		 * CLOSE_EDITOR = 20;
		 * 
		 * PRE_BUMPER_PLAYED = 21;
		 * POST_BUMPER_PLAYED = 22;
		 * BUMPER_CLICKED = 23;
		 * 
		 * FUTURE_USE_1 = 24;
		 * FUTURE_USE_2 = 25;
		 * FUTURE_USE_3 = 26;
		 */
	},
	
	/**
	 * Send updates for time stats
	 */  
	updateTimeStats: function() {
		// Setup local references:
		var embedPlayer = this.embedPlayer;
		var _this = this;
		
		// Set the seek and time percent:
		var percent = embedPlayer.currentTime / embedPlayer.duration;
		var seekPercent = this.lastSeek/ embedPlayer.duration;
		
		
		// Send updates based on logic present in StatisticsMediator.as
		if( !_this._p25Once && percent >= .25  &&  seekPercent <= .25 ) {
					
			_this._p25Once = true;			
			_this.sendAnalyticsEvent( 'PLAY_REACHED_25' );
									
		} else if ( !_this._p50Once && percent >= .50 && seekPercent < .50 ) {
		
			_this._p50Once = true;
			_this.sendAnalyticsEvent( 'PLAY_REACHED_50' );
						
		} else if( !_this._p75Once && percent >= .75 && seekPercent < .75 ) {
			
			_this._p75Once = true;
			_this.sendAnalyticsEvent( 'PLAY_REACHED_75' );
			
		} else if(  !_this._p100Once && percent >= .98 && seekPercent < 1) {
			
			_this._p100Once = true;
			_this.sendAnalyticsEvent( 'PLAY_REACHED_100' );
			
		}
	}
};

