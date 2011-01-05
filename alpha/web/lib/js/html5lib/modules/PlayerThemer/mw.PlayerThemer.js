// Setup the jquery binding

/**
* Define mw.PlayerThemer object:
*
*  @@todo we need to do some updates to some mwEmbedPlayer bindings to make this work properly
*   for now just target raw 'html5'
*/
mw.PlayerThemer = function( themeContainer, options ) {
	return this.init( themeContainer, options);
}
mw.PlayerThemer.prototype = {
	/**
	 * Master Theme Config:
	 */
	defaultComponentConfig: {
		/**
		 * Component config has the following items:
		 *
		 * @@todo we need tighter integration with controlBuilder / remaping
		 *
		 * 'selector' target selector
		 * 'visible' visible states:
		 * 	 'stop', 'playing', 'playerFocus', 'playerNoFocus', 'seeking'
		 */

		'centerPlayButton': {
			'show' : ['stop', 'paused'],
			'hide' : ['playing', 'seeking'],
			'doBind' : function( _this ){
				_this.$getCompoent( 'centerPlayButton' ).click(function(){
					// Fade away the button
					$j(this).fadeOut( 'fast' );
					_this.getEmbedPlayer().play();
				})				
			},
			'customShow' : function( _this ){
				_this.$getCompoent('centerPlayButton').fadeIn('slow');
			},
			'customHide' : function( _this ){
				_this.$getCompoent('centerPlayButton').fadeOut('slow');
			}
		},
		'widgetOverlay' : {
			'show' : ['stop', 'paused'],
			'hide' : ['playing']
		},
		'bottomTitle' : {
			'show' : ['stop'],
			'customShow' : function( _this ){
				_this.$getCompoent('bottomTitle').show('slow');
			},
			'customHide' : function( _this ){
				_this.$getCompoent('bottomTitle').hide('slow');
			}
		},
		// The minimal control bar status
		'playControlMin' : {
			'show' : ['playing', 'playerNoFocus'],
			'hide' : ['stop', 'playerFocus'],
			'doBind' : function( _this ){
				// bind the progress bar
			}
		},
		// The full progress bar with sub components:
		'playControlFull': {
			'show' : ['playerFocus'],
			'hide' : ['playerNoFocus'],
			'doBind' : function( _this ){
				// Bind the progress bar time and buffer update
				$j( _this.getEmbedPlayer() ).bind( 'updatePlayHeadPercent', function( event, perc ){
					_this.$getCompoent( 'playScrubber' ).slider( "option", "value", perc * 1000 );
				})
				$j( _this.getEmbedPlayer() ).bind('updateBufferPercent', function(event, perc ){
					_this.$getCompoent( 'bufferProgress' ).css('width', ( perc * 100 ) + '%' );
				});

				// Add the handler css to the handler ( drives ui slider widget below )
				if( ! _this.$getCompoent('playHandle').hasClass( 'ui-slider-handle' ) ){
					_this.$getCompoent('playHandle').addClass( 'ui-slider-handle' );
				}

				// Bind the playhead // @@todo clean up (just copied from controlBuilder for now )
				_this.$getCompoent( 'playScrubber' )
				.slider( {
					range: "min",
					value: 0,
					min: 0,
					max: 1000,
					start: function( event, ui ) {
						_this.getEmbedPlayer().userSlide = true;

						_this.setDisplayState('seeking');
					},
					slide: function( event, ui ) {
						var perc = ui.value / 1000;
						_this.getEmbedPlayer().jump_time = mw.seconds2npt( parseFloat( parseFloat( _this.getEmbedPlayer().getDuration() ) * perc ) + _this.getEmbedPlayer().start_time_sec );
						// mw.log('perc:' + perc + ' * ' + embedPlayer.getDuration() + ' jt:'+ this.jump_time);
						if ( _this.longTimeDisp ) {
							//ctrlObj.setStatus( gM( 'mwe-embedplayer-seek_to', embedPlayer.jump_time ) );
						} else {
							//ctrlObj.setStatus( embedPlayer.jump_time );
						}
					},
					change:function( event, ui ) {
						// Only run the onChange event if done by a user slide
						// (otherwise it runs times it should not)
						if ( _this.getEmbedPlayer().userSlide ) {
							_this.getEmbedPlayer().userSlide = false;
							_this.getEmbedPlayer().seeking = true;

							var perc = ui.value / 1000;
							// set seek time (in case we have to do a url seek)
							_this.getEmbedPlayer().seek_time_sec = mw.npt2seconds( _this.getEmbedPlayer().jump_time, true );
							mw.log( 'do jump to: ' + _this.getEmbedPlayer().jump_time + ' perc:' + perc + ' sts:' + _this.getEmbedPlayer().seek_time_sec );
							//ctrlObj.setStatus( gM( 'mwe-embedplayer-seeking' ) );
							_this.getEmbedPlayer().doSeek( perc );
							_this.setDisplayState('playing');
						}
					}
				} )
				.removeClass('ui-widget-content')
				// @@todo need a clean way to not have jquery ui themes get in the way
				.find('.playHandle').removeClass('ui-corner-all ui-state-default');
			}
		},
		'playHandle' : {
			'show' : ['stop']
		},
		'bufferProgress' :{
			'show' : ['stop']
		},
		'playButton': {
			'show' : ['stop', 'paused'],
			'hide' : ['playing'],
			'doBind' : function( _this ){
				_this.$getCompoent( 'playButton' ).click( function(){
					_this.getEmbedPlayer().play();
					_this.setDisplayState('playing');
				});
			}
		},
		'pauseButton' : {
			'show' : ['playing'],
			'hide' : ['paused', 'stop'],
			'doBind' : function( _this ){
				_this.$getCompoent( 'pauseButton' ).click( function(){
					_this.getEmbedPlayer().pause();
					_this.setDisplayState('paused');
				})
			}
		},
		'volumeButton' : {
			'orientation': 'vertical',
			'doBind': function( _this ) {
				_this.$getCompoent( 'volumeButton' )
				.hoverIntent({
					'sensitivity': 4,
					'timeout' : 2000,
					'over' : function(){
						_this.$getCompoent('volumeSliderContainer').fadeIn( 'fast' )
					},
					'out' : function(){
						_this.$getCompoent('volumeSliderContainer').fadeOut( 'fast' )
					}
				})
				// for touch devices:
				.bind( 'touchstart', function(){
					_this.$getCompoent('volumeSlider').fadeIn( 'fast' )
				});

				// Add the handler css to the slider ( drives ui slider widget below )
				if( ! _this.$getCompoent('volumeHandle').hasClass( 'ui-slider-handle' ) ){
					_this.$getCompoent('volumeHandle').addClass( 'ui-slider-handle' );
				}

				// Setup volume slider:
				_this.$getCompoent( 'volumeSlider' ).slider(
					{
						range: "min",
						value: 80,
						min: 0,
						max: 100,
						orientation: _this.getComponentConfig('volumeButton').orientation,
						slide: function( event, ui ) {
							var percent = ui.value / 100;
							mw.log( 'slide::update volume:' + percent );
							_this.getEmbedPlayer().setVolume( percent );
						},
						change: function( event, ui ) {
							var percent = ui.value / 100;
							if ( percent == 0 ) {
								//_this.getEmbedPlayer().$interface.find( '.volume_control span' ).removeClass( 'ui-icon-volume-on' ).addClass( 'ui-icon-volume-off' );
							} else {
								//_this.getEmbedPlayer().$interface.find( '.volume_control span' ).removeClass( 'ui-icon-volume-off' ).addClass( 'ui-icon-volume-on' );
							}
							mw.log('change::update volume:' + percent);
							_this.getEmbedPlayer().setVolume( percent );
						}
					}
				)
				.removeClass('ui-widget-content')
				.find('.volumeHandle')
				.removeClass('ui-corner-all');
			}
		},
		'volumeSliderContainer' :{
			'hide' : ['paused', 'stop', 'playing', 'playerNoFocus']
		},
		'fullscreenButton' : {
			'doBind' : function( _this ){
				_this.$getCompoent( 'fullscreenButton' ).click( function(){

				});
			}
		}

	},
	// Stores a json config set of components ( setup at init )
	components: {},


	defaultConfig:{
		'classPrefix' : ''
	},
	config: {},

	init: function( themeContainer, options){
		var _this = this;
		if( $j( themeContainer ).length == 0 ){
			mw.log("Error: PlayerThemer can't them empty target")
		}
		this.$target = $j( themeContainer );
		// set the id:
		if( !this.$target.attr('id') ){
			this.$target.attr('id', 'playerThemer_' + Math.random() );
		}
		var playerId = this.$target.find('video').attr('id');
		if( !playerId ){
			playerId = 'vid_' + Math.random();
			this.$target.find('video').attr('id', playerId)
		}
		mw.load('EmbedPlayer', function(){
			_this.$target.find('video').embedPlayer(function(){
				// Bind to the embedPlayer library:
				_this.embedPlayer = $j('#' + playerId).get(0);
				// Merge in the components
				if(! options.components )
					 options.components = {};

				_this.components = $j.extend( true, _this.defaultComponentConfig, options.components);

				// Merge in config ( everything but the components )
				delete options.components;
				_this.config = $j.extend( true, _this.defaultConfig, options);

				// Rewrite the player with the 'stop' state
				_this.setDisplayState( 'stop' );

				// Bind all buttons
				_this.bindActions();

				// Bind player events that update the interface
				_this.bindPlayerDisplayState();

				// Check for 'ready' callback
				if( options.ready ){
					options.ready();
				}
			})
		})
	},
	getEmbedPlayer:function( embedPlayer ){
		return this.embedPlayer;
	},
	/**
	 * return the query object of the component
	 */
	$getCompoent: function( componentId ){
		if( this.components[componentId] && this.components[componentId].selector ){
			return this.$target.find( this.components[componentId].selector );
		}
		// Return with classPrefix:
		return this.$target.find( '.' + this.config.classPrefix + componentId );
	},

	getComponentConfig: function( componentId ){
		if( this.components[componentId] ){
			return this.components[componentId]
		}
		return false;
	},

	/**
	 * Hide all the elements that are not part of the default state:
	 */
	setDisplayState:function( displayState ){
		_this = this;
		for( var componentId in this.components ){
			component = this.components[ componentId ];
			if( $j.inArray( displayState, component.show ) != -1 ){
				// checkfor custom animation:
				if( component.customShow ){
					component.customShow( _this );
				} else {
					this.$getCompoent(componentId).show();
				}
			}

			if( $j.inArray( displayState, component.hide ) != -1 ){
				if( component.customHide ){
					component.customHide( _this );
				} else {
					this.$getCompoent(componentId).hide();
				}
			}
		}
	},

	/**
	 * Set up player bindings for updating the interface
	 */
	bindPlayerDisplayState: function(){
		var _this = this;

		$j( this.embedPlayer ).bind('play', function(){
			_this.setDisplayState('playing')
		});

		$j( this.embedPlayer ).bind('paused', function(){
			_this.setDisplayState('paused');
		});

		$j( this.embedPlayer ).bind('ended', function(){
			// xxx should support 'ended' state
			//_this.setDisplayState('ended');
			_this.setDisplayState('stop');
			// reset the scrubber:
			_this.$getCompoent( 'playScrubber' ).slider( "option", "value", 0 );
		});

		// show stuff on player touch:
		_this.$target.bind('touchstart', function() {
			_this.setDisplayState('playerFocus');
		} );

		// 'player focus'
		var shouldDeFocus = true;
		/*_this.$target.hoverIntent({
			'sensitivity': 4,
			'timeout' : 2000,
			'over' : function(){
				_this.setDisplayState('playerFocus');
			},
			'out' : function(){
				_this.setDisplayState('playerNoFocus');
			}
		});	*/
	},

	/**
	 * Binds all the button actions
	 */
	bindActions: function(){
		var _this = this;
		$j.each(this.components, function(componentKey, component){
			if( component.doBind ){
				component.doBind( _this );
			}
		});
	}

}