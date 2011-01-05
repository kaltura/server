/**
 * Add the messages text:
 */

mw.includeAllModuleMessages();

/**
* Define mw.SwarmTransport object:
*/
mw.SwarmTransport = {

	addPlayerHooks: function(){
		var _this = this;
		// Bind some hooks to every player:
		$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ) {

			// Setup the "embedCode" binding to swap in an updated url
			$j( embedPlayer ).bind( 'checkPlayerSourcesEvent', function( event, callback ) {
				// Confirm SwarmTransport add-on is available ( defines swarmTransport var )
				if( _this.getPluginLibrary() ){
					// Add the swarm source
					_this.addSwarmSource( embedPlayer, function( status ){
						// Check if the status returned true 
						if( status ){
							// Update the source if paused
							if( embedPlayer.paused ) {
								// Resetup sources
								embedPlayer.setupSourcePlayer();
							}
						}
					});
				}
				// Don't block on swarm request, directly do the callback
				callback();
			} );

			// Check if we have a "recommend" binding and provide an xpi install link
			mw.log('SwarmTransport::bind:addControlBindingsEvent');
			$j( embedPlayer ).bind( 'addControlBindingsEvent', function(){
				if( mw.getConfig( 'SwarmTransport.Recommend' ) && _this.getPluginLibrary() ){
					embedPlayer.controlBuilder.doWarningBindinng(
						'recommendSwarmTransport',
						_this.getRecomendSwarmMessage()
					);
				}
			});

		} );


		// Add the swarmTransport player to available player types:
		$j( mw ).bind( 'EmbedPlayerManagerReady', function( event ) {
			var playerLib = _this.getPluginLibrary();
			// Add the swarmTransport playerType
			mw.EmbedTypes.players.defaultPlayers['video/swarmTransport'] = [ playerLib ];  

			// Build the swarm Transport Player
			var swarmTransportPlayer = new mediaPlayer( 'swarmTransportPlayer', ['video/swarmTransport' ], playerLib );

			// Add the swarmTransport "player"
			mw.EmbedTypes.players.addPlayer( swarmTransportPlayer );
		});

	},
	// Check if the swam player exists and return its associated player library
	getPluginLibrary: function(){
		// Check for swarmTransport global in javascript ( firefox )
		if( typeof window['swarmTransport'] != 'undefined' ){
			return 'Native';
		}
		if( typeof window['tswiftTransport'] != 'undefined' ){
			return 'Native';
		}
		// Look for swarm player:
		try{
			if( mw.EmbedTypes.testActiveX( 'P2PNext.SwarmPlayer' ) ){
				return 'SwarmVlc';
			}
		} catch (e ){
			mw.log(" Error:: SwarmTransport:testActiveX( 'P2PNext.SwarmPlayer' failed ");
		}
		return false;
	},
	getTorrentLookupObj: function(){
		if( typeof window['swarmTransport'] != 'undefined' ){
			return {
				'url':	mw.getConfig( 'SwarmTransport.TorrentLookupUrl' ),
				'protocol' : 'tribe://'
			}
		}
		if( typeof window['tswiftTransport'] != 'undefined' ){
			return {
				'url' : mw.getConfig( 'TSwiftTransport.TorrentLookupUrl' ),
				'protocol' : 'tswift://'
			}
		}
	},
	addSwarmSource: function( embedPlayer, callback ) {
		var _this = this;

		// xxx todo: also grab the WebM source if supported.
		var source = embedPlayer.mediaElement.getSources( 'video/ogg' )[0];
		if( ! source ){
			mw.log("Warning: addSwarmSource: could not find video/ogg source to generate torrent from");
			callback();
			return ;
		}
		// Setup the torrent request:
		var torrentLookupRequest = {
			'url' : mw.absoluteUrl( source.getSrc() )
		};

		mw.log( 'SwarmTransport:: lookup torrent url: ' +
				mw.getConfig( 'SwarmTransport.TorrentLookupUrl' ) + "\n" +
				mw.absoluteUrl( source.getSrc() )
			);
		
		var torrentLookup = this.getTorrentLookupObj();
		// Setup function to run in context based on callback result
		$j.getJSON(
			torrentLookup.url + '?jsonp=?',
			torrentLookupRequest,
			function( data ){
				// Check if the torrent is ready:
				if( !data.torrent ){
					mw.log( "SwarmTransport: Torrent not ready status: " + data.status.text );
					callback( false );
					return ;
				}
				mw.log( 'SwarmTransport: addSwarmSource for: ' + source.getSrc() + "\n\nGot:" + data.torrent );
				// XXX need to update preference
				embedPlayer.mediaElement.tryAddSource(
					$j('<source />')
					.attr( {
						'type' : 'video/swarmTransport',
						'title': gM('mwe-swarmtransport-stream-ogg'),
						'src': torrentLookup.protocol + data.torrent,
						'default' : true
					} )
					.get( 0 )
				);
				callback( true );
			}
		);
	},

	getRecomendSwarmMessage: function(){
		//xxx an xpi link would be nice ( for now just link out to the web site )
		return gM( 'mwe-swarmtransport-recommend', 'http://wikipedia.p2p-next.org/download/' );
	}

};

// Add player bindings for swarm Transport
mw.SwarmTransport.addPlayerHooks();