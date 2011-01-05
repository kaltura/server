
mw.PlaylistHandlerKaltura = function( options ){
	return this.init( options );
}

mw.PlaylistHandlerKaltura.prototype = {
	clipList:null,
	
	uiconf_id: null,
	widget_id: null,
	playlist_id: null,
	
	playlistSet : [],
	
	// ui conf data
	uiConfData : null,
	
	// If playback should continue to the next clip on clip complete
	autoContinue: true,
	
	init: function ( options ){
		this.uiconf_id =  options.uiconf_id;
		this.widget_id = options.widget_id;			
		if( options.playlist_id ){
			this.playlist_id = options.playlist_id;
		}
		
	},	
	
	loadPlaylist: function ( callback ){
		var _this = this;
	
		// Get the kaltura client:
		
		// Check if we have already initialised the playlist session: 
		if( _this.playlist_id !== null ){
			_this.loadCurrentPlaylist( callback );
			return ;
		}
		
		this.getKClient().playerLoader({
			'uiconf_id' : this.uiconf_id
		}, function( playerData ){
			// Get playlist uiConf data
					
			// TODO check if single playlist:
			//	_this.playlistSet[0] = {'playlist_id' : data.id };
			
			
			// Add all playlists to playlistSet
			var $uiConf = $j(  playerData.uiConf );				
			
			// Check for autoContinue ( we check false state so that by default we autoContinue ) 
			_this.autoContinue = 
				( $uiConf.find("uiVars [key='playlistAPI.autoContinue']").attr('value') == 'false' )? false: true
													
			// Find all the playlists by number  
			for( var i=0; i < 50 ; i ++ ){
				var playlist_id  = $uiConf.find("uiVars [key='kpl" + i +"EntryId']").attr('value');
				var playlistName = $uiConf.find("uiVars [key='playlistAPI.kpl" + i + "Name']").attr('value');
				if( playlist_id && playlistName ){
					_this.playlistSet.push( { 
						'name' : playlistName,
						'playlist_id' : playlist_id
					} )
				} else {
					break;
				}
			}				
			if( !_this.playlistSet[0] ){
				mw.log( "Error could not get playlist entry id in the following uiConf data::\n" + data.confFileFeatures );
				return false;
			}														
			
			mw.log( "PlaylistHandlerKaltura:: got  " +  _this.playlistSet.length + ' playlists ' );																
			// Set the playlist to the first playlist
			_this.setPlaylistIndex( 0 );
			
			// Load playlist by Id 
			_this.loadCurrentPlaylist( callback );
		});
	},
	hasMultiplePlaylists: function(){
		return ( this.playlistSet.length > 1 )
	},
	getPlaylistSet: function(){
		return this.playlistSet;
	},
	setPlaylistIndex: function( playlistIndex ){
		this.playlist_id = this.playlistSet[ playlistIndex ].playlist_id;		
	},
	loadCurrentPlaylist: function( callback ){
		this.loadPlaylistById( this.playlist_id, callback );
	},
	loadPlaylistById: function( playlist_id, callback ){
		var _this = this;
		var playlistRequest = { 
				'service' : 'playlist', 
				'action' : 'execute', 
				'id': playlist_id 
		};
		this.getKClient().doRequest( playlistRequest, function( playlistDataResult ) {
			// Empty the clip list
			_this.clipList = [];
			
			// The api does strange things with multi-playlist vs single playlist
			if( playlistDataResult[0].id ){
				playlistData = playlistDataResult
			} else if( playlistDataResult[0][0].id ){
				playlistData = playlistDataResult[0];
			} else {
				mw.log("Error: kaltura playlist:" + playlist_id + " could not load:" + playlistData.code)
			}
			
			mw.log( 'kPlaylistGrabber::Got playlist of length::' +   playlistData.length );
			_this.clipList =  playlistData;			
			callback();
		});
	},	
	
	getKClient: function(){
		if( !this.kClient ){
			this.kClient = mw.kApiGetPartnerClient( this.widget_id );
		}
		return this.kClient;			
	},
	
	/**
	 * Get clip count
	 * @return {number} Number of clips in playlist
	 */
	getClipCount: function(){		
		return this.getClipList().length;
	},
	
	getClip: function( clipIndex ){
		return this.getClipList()[ clipIndex ];
	},
	getClipList: function(){
		return this.clipList;
	},
	
	getClipSources: function( clipIndex, callback ){
		var _this = this;
		mw.getEntryIdSourcesFromApi( this.getKClient().getPartnerId(),  this.getClipList()[ clipIndex ].id, function( sources ){
			// Add the durationHint to the sources: 
			for( var i in sources){
				sources[i].durationHint = _this.getClipDuration( clipIndex );
			}
			callback( sources );
		});
	},
	
	applyCustomClipData:function( embedPlayer, clipIndex ){
		$j( embedPlayer ).attr({
			'kentryid' : this.getClip( clipIndex ).id,
			'kwidgetid' : this.widget_id
		});		
		$j( embedPlayer ).data( 'kuiconf', this.uiConfData );
	},
	
	/**
	* Get an items poster image ( return missing thumb src if not found )
	*/ 
	getClipPoster: function ( clipIndex ){						
		return this.getClip( clipIndex ).thumbnailUrl;
	},
	
	/** 
	* Get an item title from the $rss source
	*/
	getClipTitle: function( clipIndex ){
		return this.getClip( clipIndex ).name;
	},
	
	getClipDuration: function ( clipIndex ) {	
		return this.getClip( clipIndex ).duration;
	}
}