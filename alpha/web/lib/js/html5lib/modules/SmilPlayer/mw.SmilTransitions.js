/**
* Handles the smil transitions
*/
mw.SmilTransitions = function( smilObject ){
	return this.init( smilObject );
}
mw.SmilTransitions.prototype = {

	init: function( smilObject ) {
		this.smil = smilObject;
	},

	getTransitionInRange: function( smilElement, animateTime ) {
		var _this = this;
		var inRangeTransitions = [];
		var transitionDirections = ['transIn', 'transOut'];
		$j.each( transitionDirections, function(inx, transitionDirection ){
			if( $j( smilElement ).attr( transitionDirection ) ){
				$transition = _this.smil.$dom.find( '#' + $j( smilElement ).attr( transitionDirection) );
				var transitionDuration = _this.smil.parseTime( $transition.attr('dur') );
				// Check if the transition is in range
				var percent = false;
				if( transitionDirection == 'transIn' ){
					if ( transitionDuration > animateTime ){
						percent = animateTime / transitionDuration;
					}
				}
				if( transitionDirection == 'transOut' ){
					var nodeDuration = _this.smil.getBody().getClipDuration( smilElement );
					if( animateTime > ( nodeDuration - transitionDuration ) ){
						percent = ( animateTime - ( nodeDuration - transitionDuration ) ) / transitionDuration;
						// Invert the percentage for "transOut"
						//percent = 1 - percent;
					}
				}
				if( percent !== false ){
					inRangeTransitions.push( {
						'transition': $transition,
						'percent': percent
					})
				}
			}
		});
		return inRangeTransitions;
	},

	// Returns true if a transition is in rage false if not
	hasTransitionInRange : function( smilElement, animateTime ) {
		return ( this.getTransitionInRange( smilElement, animateTime ) != 0 );
	},

	// hide any associative transition overlays ( ie the element is no longer displayed )
	hideElementOverlays: function( smilElement ){

	},

	// Generates a transition overlay based on the transition type
	transformTransitionOverlay: function( smilElement, animateTime ) {
		var _this = this;
		/*mw.log('SmilTransitions::transformTransitionOverlay:' + animateTime +
				' tIn:' + $j( smilElement ).attr( 'transIn' ) +
				' tOut:' + $j( smilElement ).attr( 'transOut' ) );*/

		// Get the transition in range
		var transitionInRange = this.getTransitionInRange( smilElement, animateTime );
		$j.each( transitionInRange, function(inx, tran){
			_this.drawTransition( tran.percent, tran.transition, smilElement );
		});
	},

	/**
	 * hideTransitionElements hides transition overlays that are out of range
	 */
	hideTransitionElements: function ( smilElement ){
		// for now just hide
		if( $j( smilElement ).attr( 'transIn' ) ){
			$j( '#' +
				this.getTransitionOverlayId(
					this.smil.$dom.find( '#' + $j( smilElement ).attr( 'transIn' ) ),
					smilElement
				)
			).hide();
		}
		if( $j( smilElement ).attr( 'transOut' ) ){
			$j( '#' +
				this.getTransitionOverlayId(
					this.smil.$dom.find( '#' + $j( smilElement ).attr( 'transOut' ) ),
					smilElement
				)
			).hide();
		}
	},

	/**
	 * Updates a transition to a requested percent
	 *
	 * @param {float} percent Percent to draw transition
	 * @param {Element} $transition The transition node
	 * @param {Element} smilElement The element to transition on.
	 */
	drawTransition: function( percent, $transition, smilElement ){
		//mw.log( 'SmilTransitions::drawTransition::' + $transition.attr('id') );
		// Map draw request to correct transition handler:
		if( ! this.transitionFunctionMap[ $transition.attr('type') ]
			||
			! this.transitionFunctionMap[ $transition.attr('type') ][ $transition.attr( 'subtype' ) ] ){
			mw.log( "Error no support for transition " +
					$transition.attr('type') + " with subtype: " + $transition.attr( 'subtype' ) );
			return ;
		}
		// Run the transitionFunctionMap update:
		this.transitionFunctionMap[ $transition.attr('type') ]
									[ $transition.attr( 'subtype' ) ]
									(this, percent, $transition, smilElement )
	},

	/**
	 * Maps all supported transition function types
	 *
	 * Also see: http://www.w3.org/TR/SMIL/smil-transitions.html
	 *
	 * Each transition map function accepts:
	 *
	 * @param {Object} _this Reference to SmilTransistions object
	 * @param {float} percent Percent to draw transition
	 * @param {Element} $transition The transition node
	 * @param {Element} smilElement The element to transition on.
	 */
	transitionFunctionMap : {
		'fade' : {
			'_doColorOverlay': function( _this, percent, $transition, smilElement ){
				// Add the overlay if missing
				var transitionOverlayId = _this.getTransitionOverlayId( $transition, smilElement );
				if( $j( '#' + transitionOverlayId ).length == 0 ){

					// Add the transition to the smilElements "region"
					// xxx might want to have layout drive the draw a bit more
					_this.smil.getLayout().getRegionTarget( smilElement ).append(
						$j('<div />')
							.attr('id', transitionOverlayId)
							.addClass( 'smilFillWindow' )
							.addClass( 'smilTransitionOverlay' )
					);
					mw.log('fadeFromColor:: added: ' + transitionOverlayId);
				}
				//mw.log(' SET COLOR:: ' + $transition.attr( 'fadeColor') );
				// Update the color:
				$j( '#' + transitionOverlayId ).css( 'background-color', $transition.attr( 'fadeColor'))

				// Update the overlay opacity
				$j( '#' + transitionOverlayId ).show().css( 'opacity', percent );
			},
			'fadeFromColor': function( _this, percent, $transition, smilElement ){
				// Invert the percentage since we setting opacity from full color we are fading from
				percent = 1 - percent;

				this._doColorOverlay( _this, percent, $transition, smilElement );
			},
			'fadeToColor': function( _this, percent, $transition, smilElement ){
				this._doColorOverlay( _this, percent, $transition, smilElement );
			},
			'crossfade': function( _this, percent, $transition, smilElement ){
				// fade "ourselves" ... in cases of overlapping timelines this will create a true cross fade
				$j( '#' + _this.smil.getSmilElementPlayerID( smilElement ) ).css( 'opacity', percent );
			}
		}
	},

	getTransitionOverlayId: function( $transition, smilElement) {
		 return this.smil.getSmilElementPlayerID( smilElement ) + '_' + $transition.attr('id');
	}


}