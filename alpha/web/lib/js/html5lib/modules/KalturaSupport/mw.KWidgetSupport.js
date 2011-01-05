mw.KWidgetSupport = function( options ) {
	// Create KWidgetSupport instance
	return this.init( options );
};
mw.KWidgetSupport.prototype = {

	// The Kaltura client local reference
	kClient : null,
	
	// Constructor check settings etc
	init: function( options ){
	
	},
	
	/**
	* Add Player hooks for supporting Kaltura api stuff
	*/ 
	addPlayerHooks: function( ){
		var _this = this;		
		// Add the hooks to the player manager
		$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {
			// Add hook for check player sources to use local kEntry ID source check:
			$j( embedPlayer ).bind( 'checkPlayerSourcesEvent', function( event, callback ) {
				// Load all the player configuration from kaltura: 
				var status = _this.loadPlayerData( embedPlayer, function( playerData ){
					if( !playerData ){
						callback();
						return ;
					}
					// Check access controls ( this is kind of silly and needs to be done on the server ) 
					if( playerData.accessControl ){
						var acStatus = _this.getAccessControlStatus( playerData.accessControl );
						if( acStatus !== true ){
							$j('.loadingSpinner').remove();
							$j(embedPlayer).replaceWith( acStatus );
							return ;
						}
					}					
					
					// Apply player Sources
					if( playerData.flavors ){
						_this.addFlavorSources( embedPlayer, playerData.flavors );
					}
					
					// Apply player metadata
					if( playerData.meta ) {
						embedPlayer.duration = playerData.meta.duration;
						$j( embedPlayer ).data( 'kaltura.meta', playerData.meta );
						$j( embedPlayer ).trigger( 'KalturaSupport.metaDataReady', $j( embedPlayer ).data( 'kaltura.meta') );
					}
					
					// Add kaltura analytics if we have a session if we have a client ( set in loadPlayerData ) 									
					if( mw.getConfig( 'Kaltura.EnableAnalytics' ) === true && _this.kClient ) {
						mw.addKAnalytics( embedPlayer, _this.kClient );
					}
					
					// Check for uiConf	
					if( playerData.uiConf ){
						// Store the parsed uiConf in the embedPlayer object:
						embedPlayer.$uiConf = $j( playerData.uiConf );
						// Trigger the check kaltura uiConf event					
						$j( embedPlayer ).triggerQueueCallback( 'KalturaSupport.checkUiConf', embedPlayer.$uiConf, function(){
							// Ui-conf file checks done
							callback();
						});
					} else {
						callback();
					}			
				});
			});						
		});		
	},
	
	/**
	 * Alternate source grabbing script ( for cases where we need to hot-swap the source ) 
	 * playlists on iPhone for example we can't re-load the player we have to just switch the src. 
	 * 
	 * accessible via static reference mw.getEntryIdSourcesFromApi
	 * 
	 */
	getEntryIdSourcesFromApi:  function( widgetId, entryId, callback ){
		var _this = this;
		this.kClient = mw.KApiPlayerLoader( {
			'widget_id' : widgetId, 
			'entry_id' : entryId,
		}, function( playerData ){
			
			// Check access control 
			if( playerData.accessControl ){
				var acStatus = _this.getAccessControlStatus( playerData.accessControl );
				if( acStatus !== true ){
					callback( acStatus );
					return ;
				}
			}					
			// Get device sources 
			var deviceSources = _this.getEntryIdSourcesFromFlavorData( _this.kClient.getPartnerId(), playerData.flavors );
			var sources = _this.getSourcesForDevice( deviceSources );
			callback( sources );
		});
	},
	
	/**
	 * Sets up variables and issues the mw.KApiPlayerLoader call
	 */
	loadPlayerData: function( embedPlayer, callback ){
		var _this = this;
		var playerRequest = {};
		
		// Check for widget id	 
		if( ! $j( embedPlayer ).attr( 'kwidgetid' ) ){
			mw.log( "Error: missing required widget paramater")
			callback( false );
			return false;
		} else {
			playerRequest.widget_id = $j( embedPlayer ).attr( 'kwidgetid' );
		}
		
		// Check if the entryId is of type url: 
		if( !this.checkForUrlEntryId( embedPlayer ) && $j( embedPlayer ).attr( 'kentryid' ) ){
			// Add entry_id playerLoader call			
			playerRequest.entry_id =  $j( embedPlayer ).attr( 'kentryid' );
		}
		
		// Add the uiconf_id 
		playerRequest.uiconf_id = this.getUiConfId( embedPlayer );
		
		// Check if we have the player data bootstrap from the iframe
		var bootstrapData = mw.getConfig("KalturaSupport.BootstrapPlayerData");
		// Insure the bootStrap data has all the required info: 
		if( bootstrapData 
			&& bootstrapData.partner_id ==  $j( embedPlayer ).attr( 'kwidgetid' ).replace('_', '')
			&&  bootstrapData.ks 
		){
			this.kClient = mw.kApiGetPartnerClient( playerRequest.widget_id );
			this.kClient.setKS( bootstrapData.ks );
			callback( bootstrapData );
		} else {
			// Run the request: ( run async to avoid stack )
			setTimeout(function(){
				_this.kClient = mw.KApiPlayerLoader( playerRequest, function( playerData ){
					callback( playerData );
				});
			});
		}
	},
	
	/**
	 * Check if the access control is oky and set a given error message
	 * 
	 * NOTE should match the iframe messages
	 * NOTE need to i8ln message with gM( 'msg-key' );
	 * 
	 * @return 
	 * @type boolean 
	 * 		true if the media can be played
	 * 		false if the media should not be played. 
	 */
	getAccessControlStatus: function( ac ){
		if( ac.isAdmin){
			return true;
		}
		if( ac.isCountryRestricted ){
			return 'country is restricted';
		}
		if( !ac.isScheduledNow ){
			return 'is not scheduled now';
		}
		if( ac.isSessionRestricted ){
			return 'session restricted';
		}
		if( ac.isSiteRestricted ){
			return 'site restricted';
		}
		if( ac.previewLength != -1 ){
			return 'preview not handled in library yet';
		}
		return true;
	},
	
	/**
	 * Get the uiconf id, if unset its the kwidget id / partner id default
	 */
	getUiConfId: function( embedPlayer ){
		var uiConfId = ( embedPlayer.kuiconfid ) ? embedPlayer.kuiconfid : false; 
		if( !uiConfId && embedPlayer.kwidgetid ) {
			uiConfId = embedPlayer.kwidgetid.replace( '_', '' );
		}
		return uiConfId;
	},
	/**
	 * Check if the entryId is a url ( add source and do not include in request ) 
	 */
	checkForUrlEntryId:function( embedPlayer ){
		if( $j( embedPlayer ).attr( 'kentryid' ) 
				&& 
			$j( embedPlayer ).attr( 'kentryid' ).indexOf('://') != -1 )
		{
			embedPlayer.mediaElement.tryAddSource(
					$j('<source />')
					.attr( {
						'src' : $j( embedPlayer ).attr( 'kentryid' )
					} )
					.get( 0 )
				)
			return true;
		}
		return false;
	},
	/**
	* Convert flavorData to embedPlayer sources
	* 
	* @param {Object} embedPlayer Player object to apply sources to
	* @param {Object} flavorData Function to be called once sources are ready 
	*/ 
	addFlavorSources: function( embedPlayer, flavorData ) {
		var _this = this;
		mw.log( 'KWidgetSupport::addEntryIdSources:');

		// Set the poster ( if not already set ) 
		if( !embedPlayer.poster && $j( embedPlayer ).attr( 'kentryid' ) ){
			embedPlayer.poster = mw.getConfig( 'Kaltura.CdnUrl' ) + '/p/' + this.kClient.getPartnerId() + '/sp/' +
				this.kClient.getPartnerId() + '00/thumbnail/entry_id/' + $j( embedPlayer ).attr( 'kentryid' ) + '/width/' +
				embedPlayer.getWidth() + '/height/' + embedPlayer.getHeight();
		}
		
		
		// Check existing sources have kaltura specific data-flavorid attribute ) 
		// NOTE we may refactor how we package in the kaltura pay-load from the iframe 
		var sources = embedPlayer.mediaElement.getSources();
		if( sources[0] && sources[0]['data-flavorid'] ){
			// Not so clean ... will refactor once we add another source
			var deviceSources = [];
			for(var i=0; i< sources.length;i++){
				deviceSources[ sources[i]['data-flavorid'] ] = sources[i].src;
			}
			// Unset existing DOM source children ( so that html5 video hacks work better ) 
			$j('#' + embedPlayer.pid).find('source').remove();
			// Empty the embedPlayers sources ( we don't want iPad h.264 being used for iPhone devices ) 
			embedPlayer.mediaElement.sources = [];
			// Update the set of sources in the embedPlayer ( might cause issues with other plugins ) 
		} else {		
			// Get device flavors ( if not already set )
			var deviceSources = _this.getEntryIdSourcesFromFlavorData( this.kClient.getPartnerId(), flavorData );	
		}
		// Update the source list per the current user-agent device: 
		var sources = _this.getSourcesForDevice( deviceSources );
		
		for( var i=0;i < sources.length ; i++) {
			mw.log( 'KWidgetSupport:: addSource::' + embedPlayer.id + ' : ' +  sources[i].src + ' type: ' +  sources[i].type);
			embedPlayer.mediaElement.tryAddSource(
				$j('<source />')
				.attr( {
					'src' : sources[i].src,
					'type' : sources[i].type
				} )
				.get( 0 )
			);
		}
	},
	
	/**
	 * Get client entry id sources: 
	 */
	getEntryIdSourcesFromFlavorData: function( partner_id, flavorData ){
		var _this = this;
		
		if( !flavorData ){
			mw.log("Error: KWidgetSupport: flavorData is not defined ");
		}
		
		// Setup the src defines
		var deviceSources = {};		
		
		// Find a compatible stream
		for( var i = 0 ; i < flavorData.length; i ++ ) {			
			var asset = flavorData[i];			
			/**
			* The template of downloading a direct flavor is
			*/							
			var src  = mw.getConfig('Kaltura.CdnUrl') + '/p/' + partner_id +
				'/sp/' +  partner_id + '00/flvclipper/entry_id/' +
				asset.entryId + '/flavor/' + asset.id ;
			
			// Check the tags to read what type of mp4 source
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('ipad') != -1 ){					
				deviceSources['iPad'] = src + '/a.mp4?novar=0';
			}
			
			// Check for iPhone src
			if( asset.fileExt == 'mp4' && asset.tags.indexOf('iphone') != -1 ){
				deviceSources['iPhone'] = src + '/a.mp4?novar=0';
			}
			
			// Check for ogg source
			if( asset.fileExt == 'ogg' || asset.fileExt == 'ogv'){
				deviceSources['ogg'] = src + '/a.ogg?novar=0';
			}				
			
			// Check for 3gp source
			if( asset.fileExt == '3gp' ){
				deviceSources['3gp'] = src + '/a.ogg?novar=0';
			}
		}
		return deviceSources;
	},
	
	getSourcesForDevice: function(  deviceSources ){
		var sources = [];
		var addSource = function ( src, type ){
			sources.push( {
				'src': src,
				'type': type
			} );
		}

		// If on an iPad or iPhone4 use iPad Source
		if( mw.isIpad() || mw.isIphone4() ) {
			mw.log( "KwidgetSupport:: Add iPad / iPhone4 source");
			// Note it would be nice to detect if the iPhone was on wifi or 3g
			if( deviceSources['iPad'] ){ 
				addSource( deviceSources['iPad'] , 'video/h264' );
				return sources;
			} else if ( deviceSources['iPhone']) {
				addSource( deviceSources['iPhone'], 'video/h264' );
				return sources;
			}
		}
		
		// If on iPhone or Android or iPod use iPhone src
		if( ( mw.isIphone() || mw.isAndroid2() || mw.isIpod() ) ){			
			if( deviceSources['iPhone'] ) {
				addSource( deviceSources['iPhone'], 'video/h264' );	
			} else if( deviceSources['3gp'] ){
				addSource( deviceSources['3gp'], 'video/3gp' );	
			}
			return sources;
		} else {
			// use h264 source for flash fallback ( desktop browsers ) 
			mw.log( "KwidgetSupport:: Add from flash h264 fallback" );
			if( deviceSources['iPad'] ) {
				addSource( deviceSources['iPad'], 'video/h264' );
			} else if( deviceSources['iPhone'] ) {
				addSource( deviceSources['iPhone'], 'video/h264' );
			}
		}
		
		// Add the 3gp source if available
		if( deviceSources['3gp'] ){
			addSource( deviceSources['3gp'] );
		}
		
		// Always add the oggSrc if we got to this point
		if( deviceSources['ogg'] ) {
			addSource( deviceSources['ogg'], 'video/ogg' );
		}
		return sources;
	}
}

//Setup the kWidgetSupport global if not already set
if( !window.kWidgetSupport ){
	window.kWidgetSupport = new mw.KWidgetSupport();
}


// Add player Manager binding ( if playerManager not ready bind to when its ready )
// NOTE we may want to move this into the loader since its more "action/loader" code
if( mw.playerManager ){
	kWidgetSupport.addPlayerHooks();
} else {
	mw.log( 'KWidgetSupport::bind:EmbedPlayerManagerReady');
	$j( mw ).bind( 'EmbedPlayerManagerReady', function(){
		mw.log( "KWidgetSupport::EmbedPlayerManagerReady" );
		kWidgetSupport.addPlayerHooks();
	});
}

/**
 * Register a global shortcuts for the kaltura sources query
 */
mw.getEntryIdSourcesFromApi = function( widgetId, entryId, callback ){
	kWidgetSupport.getEntryIdSourcesFromApi( widgetId, entryId, callback);
}


