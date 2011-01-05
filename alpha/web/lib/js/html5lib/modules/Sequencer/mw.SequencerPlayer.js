//Wrap in mw closure to avoid global leakage
( function( mw ) {

mw.SequencerPlayer = function( sequencer ) {
	return this.init( sequencer );
};

// Set up the mvSequencer object
mw.SequencerPlayer.prototype = {
	// The id of the sequence player
	smilPlayerId: null, // lazy init

	init: function( sequencer ){
		this.sequencer = sequencer;
	},

	/**
	 * Draw a smil player to the screen.
	 */
	drawPlayer: function( callback ){
		var _this = this;
		var $playerTarget = this.sequencer.getContainer().find( '.mwseq-player' );

		this.sequencer.getSmilSource( function( smilSource ){
			mw.log("SequencePlayer::drawPlayer: Built player target url length:" + smilSource.length );
			// Add the player
			var $video = $j('<video />');
			// Set the title key if we have it if we have a title key
			if( _this.sequencer.getServer().getTitleKey() ){
				$video.attr('apiTitleKey', _this.sequencer.getServer().getTitleKey() );
			}
			$playerTarget.html(
				$video.css(
					_this.getPlayerSize()
				).attr({
					'id' : _this.getSmilPlayerId(),
					'attributionbutton' : false,
					'overlaycontrols' : false
				}).append(
					$j('<source />').attr({
						'type' : 'application/smil',
						'src' : smilSource
					})
				)
			);

			// Draw the player ( keep the playhead for now )
			// xxx we will eventually replace the playhead with sequence
			// based playhead interface for doing easy trims
			$j( '#' + _this.getSmilPlayerId() ).embedPlayer({}, function(){
				// Set the player interface to autoMargin ( need to fix css propagation in embed player)
				$j( '#' + _this.getSmilPlayerId() ).parent('.interface_wrap').css('margin', 'auto');
				if( callback ){
					callback();
				}
			})
		});
	},

	previewClip: function( smilClip, donePreivewCallback ){
		var _this = this;
		// Seek and play start of smilClip
		var startOffset = $j( smilClip ).data('startOffset');
		var clipEndTime = startOffset +
			this.sequencer.getSmil().getBody().getClipDuration( smilClip );
		this.getEmbedPlayer().setCurrentTime( startOffset, function(){
			mw.log("SequencerPlayer::Preview clip: " + startOffset + ' to ' + clipEndTime);
			_this.getEmbedPlayer().play( clipEndTime );
			// bind end of segment action
			$j( _this.sequencer.getEmbedPlayer() ).bind( 'playSegmentEnd', donePreivewCallback);
		})
	},

	closePreivew: function(){
		// restore border
		this.sequencer.getContainer().find( '.mwseq-player' )
			.css({'border': null});
	},


	resizePlayer: function(){
		mw.log("SequencerPlayer:: resizePlayer: " + $j('#' + this.getSmilPlayerId() ).length );
		this.getEmbedPlayer()
			.resizePlayer(
				this.getPlayerSize(),
				true
			);
	},

	getPlayerSize: function(){
		var size = {};
		var $playerContainer = this.sequencer.getContainer().find('.mwseq-player');

		var aspect = this.sequencer.options.videoAspect.split( ':' );
		size.width = $playerContainer.width() - 8;
		size.height = parseInt( size.width * ( aspect[1] / aspect[0] ) );

		if( size.height > $playerContainer.height() - 35 ){
			size.height = $playerContainer.height()- 35
			size.width = parseInt( size.height * ( aspect[0] / aspect[1] ) );
		}
		return size;
	},

	/**
	 * Get the embedplayer object instance
	 */
	getEmbedPlayer: function(){
		return $j( '#' + this.getSmilPlayerId() ).get(0);
	},

	/**
	 * Get a player id
	 */
	getSmilPlayerId: function(){
		if( !this.smilPlayerId ){
			this.smilPlayerId = this.sequencer.getId() + '_smilPlayer';
		}
		return this.smilPlayerId;
	}
}


} )( window.mw );