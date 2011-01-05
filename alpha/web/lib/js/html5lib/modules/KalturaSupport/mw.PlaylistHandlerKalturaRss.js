mw.PlaylistHandlerKalturaRss = function( Playlist ){
	return this.init( Playlist );
}

mw.PlaylistHandlerKalturaRss.prototype = {
	// Set the media rss namespace
	mediaNS: 'http://search.yahoo.com/mrss/',
			
	init: function ( Playlist ){
		this.playlist = Playlist;		
		// inherit PlaylistHandlerMediaRss
		var tmp = new mw.PlaylistHandlerMediaRss( Playlist );
		for( var i in tmp ){
			if( this[i] ){
				this['parent_' + i ] = tmp[i];				
			} else {
				this[i] = tmp[i];
			}
		}
	},
	getSrc: function(){	
		// In kaltura player embeds the playlistid url is the source: 
		return this.playlist.playlistid;
	},
	getClipSources: function( clipIndex, callback ){
		this.parent_getClipSources( clipIndex, function( clipSources ){
			// Kaltura mediaRss feeds define a single "content" tag with flash swf as the url
			if( clipSources[0] && 
				clipSources.length == 1 && 
				mw.getKalturaEmbedSettings( clipSources[0].src )['entryId'] 
			){	
				var kEmbedSettings = mw.getKalturaEmbedSettings( clipSources[0].src );
				var playerRequest = {
					'entry_id' : kEmbedSettings.entryId,
					'widget_id' : kEmbedSettings.widgetId
				}	
				var clipDuration = clipSources[0].duration;		
				
				// Make sure we have a client session established: 
				mw.KApiPlayerLoader( playerRequest, function( playerData ) {
					
					mw.getEntryIdSourcesFromApi( kalturaEntryId , function( sources ){						
						for( var i in sources ){
							sources[i].durationHint = clipDuration;
						}
						callback( sources );
					});
					
				});
			} else {
				mw.log("Error: kalturaPlaylist MediaRss used with multiple sources or non-kaltura flash applet url");
			}			
		})
	}
}
	