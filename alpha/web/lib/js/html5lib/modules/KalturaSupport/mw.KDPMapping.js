/*
 * Add full kaltura mapping support to html5 based players
 * Based on the 'kdp3 javascript api'
 * http://www.kaltura.org/demos/kdp3/docs.html#jsapi
 */

// scope in mw
( function( mw ) {
 
	mw.KDPMapping = function( options ) {
		// Create a Player Manage
		return this.init( options );
	};
	mw.KDPMapping.prototype = {
		/**
		* Add Player hooks for supporting Kaltura api stuff
		*/ 
		init: function( ){
			this.addGlobalReadyHook();
			this.addPlayerHooks();		 	
		},
		
		addGlobalReadyHook: function(){
			mw.playerManager.addCallback(function(){
				// Fire the global ready
				if( window.jsCallbackReady ){
					window.jsCallbackReady();
				}			
			})
		},
		
		addPlayerHooks: function(){
			var _this = this;
			// Add the hooks to the player manager			
			$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {						
				// Add the addJsListener and sendNotification maps
				embedPlayer.addJsListener = function(listenerString, callback){
					_this.addJsListener( embedPlayer, listenerString, callback )
				}
				
				embedPlayer.removeJsListener = function(listenerString, callback){
					_this.removeJsListener( embedPlayer, listenerString, callback )
				}				
				
				embedPlayer.sendNotification = function( notificationName, notificationData ){
					_this.sendNotification( embedPlayer, notificationName, notificationData)
				}
				
				embedPlayer.evaluate = function( objectString ){
					_this.evaluate( embedPlayer, objectString);
				}
				
				embedPlayer.setKDPAttribute = function( componentName, property, value ) {
					_this.setKDPAttribute( embedPlayer, componentName, property, value );
				}
			});
		},
		
		/*
		 * emulates kaltura setAttribute function
		 */
		setKDPAttribute: function( embedPlayer, componentName, property, value ) {
			switch( property ) {
				case 'autoPlay':
					embedPlayer.autoplay = value;
					break;
			}
		},
		
		/**
		 * emulates kaltura evaluate function
		 */
		evaluate: function( embedPlayer, objectString ){
			// Strip the { } from the objectString
			objectString = objectString.replace( /\{|\}/g, '' );
			objectPath = objectString.split('.');
			console.log(objectPath);
			switch( objectPath[0] ){
				case 'video':
					switch( objectPath[1] ){
						case 'volume': 
							return embedPlayer.volume;
						break;
					}
				break;			
				
				case 'mediaProxy':
					switch( objectPath[1] ){
						case 'entry':
							if( objectPath[2] ) {
								return $j( embedPlayer ).data( 'kaltura.meta' )[ objectPath[2] ];
							} else {
								return $j( embedPlayer ).data( 'kaltura.meta' );
							}
						break;
					}
				break;
				
				case 'configProxy':
					switch( objectPath[1] ){
						case 'flashvars': 
							if( objectPath[2] ) {
								switch( objectPath[2] ) {
									case 'autoPlay':
										// get autoplay
										return embedPlayer.autoplay;
									break;
								}
							} else {
								// get flashvars
							}
						break;
					}
				break;	
				
				case 'playerStatusProxy':
					switch( objectPath[1] ){
						case 'kdpStatus': 
							//TODO
						break;
					}
				break;					
			}
		},
		
		/**
		 * emulates kalatura addJsListener function
		 */
		addJsListener: function( embedPlayer, eventName, globalFuncName ){
			//mw.log("KDPMapping:: addJsListener: " + eventName + ' cb:' + callbackFuncName );
			var callback = window[ globalFuncName ];
			switch( eventName ){
				case 'volumeChanged': 
					$j( embedPlayer ).bind('volumeChanged', function(percent){
						callback( {'newVolume' : percent }, embedPlayer.id );
					});
					break;
				case 'playerStateChange':					
					// Kind of tricky should do a few bindings to 'pause', 'play', 'ended', 'buffering/loading'
					$j( embedPlayer ).bind('pause', function(){						
						callback( 'pause', embedPlayer.id );
					});
					
					$j( embedPlayer ).bind('play', function(){
						callback( 'play', embedPlayer.id );
					});
					
					break;
				case 'durationChange': 
					// TODO add in duration change support
					break;
				case 'playerUpdatePlayhead':
					$j( embedPlayer ).bind('monitorEvent', function(){
						callback( embedPlayer.currentTime,  embedPlayer.id );
					})
					break;	
				case 'entryReady': 
					$j( embedPlayer ).bind( 'KalturaSupport.metaDataReady', function( event, meta ) {				
						callback( meta );
					});
					break;
			}				
		},
		
		/**
		 * emulates kalatura removeJsListener function
		 */
		removeJsListener: function( embedPlayer, eventName, callbackFuncName ){
			//mw.log("KDPMapping:: removeJsListener: " + eventName + ' cb:' + callbackFuncName );
			var callback = window[ callbackFuncName ];
			switch( eventName ){
				case 'volumeChanged': 
					$j( embedPlayer ).unbind('volumeChanged');
					break;
				case 'playerStateChange':					
					$j( embedPlayer ).unbind('pause');
					$j( embedPlayer ).unbind('play');
					break;
				case 'durationChange': 
					// TODO add in duration change support
					break;
				case 'playerUpdatePlayhead':
					$j( embedPlayer).unbind('monitorEvent');
					break;				
			}
			
			callback();
				
		},		
		
		/**
		 * Master send action list: 
		 */
		sendNotification: function( embedPlayer, notificationName, notificationData ){			
			switch( notificationName ){
				case 'doPlay':
					embedPlayer.play();
					break;
				case 'doPause':
					embedPlayer.pause();
					break;
				case 'doStop':
					embedPlayer.stop();
					break;
				case 'doSeek':
					// Kaltura doSeek is in seconds rather than percentage:
					var percent = parseFloat( notificationData ) / embedPlayer.getDuration();
					embedPlayer.doSeek( percent );
					break;
				case 'changeVolume':
					embedPlayer.setVolume( parseFloat( notificationData ) );
					// TODO the setVolume should update the interface
					embedPlayer.setInterfaceVolume(  parseFloat( notificationData ) );
					break;
				case 'changeMedia':
					// Update the entry id
					embedPlayer.kentryid = notificationData.entryId;
					// TODO Should support updating any widget, ui_conf whatever else change media supports
					
					// Empty out sources
					embedPlayer.emptySources();
					
					// Stop the player 
					embedPlayer.stop();
					
					// Load new sources per the entry id
					embedPlayer.checkPlayerSources();
					
					/*
					var widgetId = '_423851'; // for testing only
					//var widgetId = '_' + $j( embedPlayer ).data( 'kaltura.meta' ).partnerId;
					console.log('Partner: ' + widgetId + ' | Entry: ' + entryId);
					
					mw.getEntryIdSourcesFromApi( widgetId, entryId, function( sources ) {
						console.log(sources);
						newSource = sources[0].src;
						embedPlayer.play();
						embedPlayer.switchPlaySrc( newSource, function( embedPlayer ) { 
							
							embedPlayer.stop(); // NOT WORKING!!!
						} );
					});
					*/
					break;					
			}
		}
	};	
		
	// Setup the KDPMapping
	if( mw.playerManager ){	
		window.KDPMapping = new mw.KDPMapping();	
	} else {
		mw.log( 'KDPMapping::bind:EmbedPlayerManagerReady');		
		$j( mw ).bind( 'EmbedPlayerManagerReady', function(){									
			if(!window.KDPMapping ){
				mw.log( "KDPMapping::EmbedPlayerManagerReady" );	
				window.KDPMapping = new mw.KDPMapping();	
			}
		});	
	}

	
} )( window.mw );