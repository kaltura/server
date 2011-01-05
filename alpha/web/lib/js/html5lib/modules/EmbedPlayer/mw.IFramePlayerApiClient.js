/**
* iFrame api mapping support 
* 
* Client side ( binds a given iFrames to expose the player api ) 
*/

( function( mw ) {
	
mw.IFramePlayerApiClient = function( iframe, playerProxy, options ){
	return this.init( iframe , playerProxy, options);
}
mw.IFramePlayerApiClient.prototype = {
	'exportedMethods': [
		'play',
		'pause'
	],
	// Local store of the post message ( not updated by user js )
	'_prevPlayerProxy': {},
	// Stores the current playerProxy ( can be updated by user js )
	'init': function( iframe , playerProxy, options ){
		this.iframe = iframe;
		this.playerProxy = playerProxy;
		// Set the iframe server
		var srcParts = mw.parseUri( mw.absoluteUrl( $j(this.iframe).attr('src') ) );
		this.iframeServer = srcParts.protocol + '://' + srcParts.authority;
		this.addPlayerSendApi();
		this.addPlayerReciveApi();
	},
	'addPlayerSendApi': function(){
		var _this = this;
		$j.each( this.exportedMethods, function(na, method){
			_this.playerProxy[ method ] = function(){
				_this.postMessage( {
					'method' : method,
					'args' : arguments
				} );
			};
		});
	},
	'addPlayerReciveApi': function(){
		var _this = this;
		$j.receiveMessage( function( event ){
			_this.hanldeReciveMsg( event )
		});
	},
	/**
	 * Handle received events
	 */
	'hanldeReciveMsg': function( event ){
		var _this = this;
		//mw.log("IframePlayerApiClient:: hanldeReciveMsg ");
		// Confirm the event is coming for the target host:
		if( event.origin != this.iframeServer){
			mw.log("Skip msg from host does not match iFrame player: " + event.origin + 
					' != iframe Server: ' + this.iframeServer )
			return ;
		};
		// Decode the message 
		var msgObject = JSON.parse( event.data );
		var playerAttributes = mw.getConfig( 'EmbedPlayer.Attributes' );
		// Before we update local attributes check that the object has not been updated by user js
		for( var attrName in playerAttributes ){
			if( attrName != 'id' ){
				if( _this._prevPlayerProxy[ attrName ] != _this.playerProxy[ attrName ] ){
					mw.log( "IFramePlayerApiClient:: User js update:" + attrName + ' set to: ' + this.playerProxy[ attrName ] + ' != old: ' + _this._prevPlayerProxy[ attrName ] );
					// Send the updated attribute back to the iframe: 
					_this.postMessage({
						'attrName' : attrName,
						'attrValue' : _this.playerProxy[ attrName ]
	 				});
				}
			}
		}
		// Update any attributes
		if( msgObject.attributes ){
			for( var i in msgObject.attributes ){
				if( i != 'id' && i != 'class' && i != 'style' ){
					try{
						this.playerProxy[ i ] = msgObject.attributes[i];
						this._prevPlayerProxy[i] = msgObject.attributes[i];
					} catch( e ){
						mw.log("Error could not set:" + i );
					}
				}
			}
		}
		// Trigger any binding events 
		if( typeof msgObject.triggerName != 'undefined' && msgObject.triggerArgs != 'undefined') {
			//mw.log('IFramePlayerApiClient:: trigger: ' + msgObject.triggerName );
			$j( _this.playerProxy ).trigger( msgObject.triggerName, msgObject.triggerArgs );
		}
		// @@TODO:: Allow extending modules to wrap these api events ( kaltura kdp javascript emulation ? )
	},
	'postMessage': function( msgObj ){
		//mw.log( "IFramePlayerApiClient:: postMessage(): " + JSON.stringify( msgObj ) );
		$j.postMessage(
			JSON.stringify( msgObj ), 
			mw.absoluteUrl( $j( this.iframe ).attr('src') ), 
			this.iframe.contentWindow 
		);
	}
};

//Add the jQuery binding
( function( $ ) {
	$.fn.iFramePlayer = function( options ){
		if( ! this.selector ){
			this.selector = $j( this ).get(0);
		}
		// Append '_ifp' ( iframe player ) to id of real iframe so that 'id', and 'src' attributes don't conflict
		var originalIframeId = ( $( this.selector ).attr( 'id' ) )? $( this.selector ).attr( 'id' ) : Math.floor( 9999999 * Math.random() );
		var iframePlayerId = originalIframeId + '_ifp' ; // use random to generate a unique id
		// Append the div element proxy after the iframe 
		$j( this.selector )
			.attr('id', iframePlayerId)
			.after(
				$('<div />')
				.attr( 'id', originalIframeId )
			);
		var playerProxy = $j( '#' + originalIframeId ).get(0);
		var iframe = $j('#' + iframePlayerId).get(0);
		if(!iframe){
			mw.log("Error invalide iFramePlayer request");
			return false;
		}
		if( !iframe['playerApi'] ){
			iframe['playerApi'] = new mw.IFramePlayerApiClient( iframe, playerProxy, options );
		}
		
		// Allow modules to extend the 'iframe' based player
		$j( mw ).trigger( 'newIframeEmbedPlayerEvent', playerProxy);
		
		// Return the player proxy for chaining player events / attributes
		return $j( playerProxy );
	};
} )( jQuery );

} )( window.mw );