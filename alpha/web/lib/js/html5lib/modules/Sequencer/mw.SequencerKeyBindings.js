/**
* Stores the key bindings
*/

/**
 * jQuery helper for input focus binding
 */
( function( $ ) {
	$.fn.sequencerInput = function( sequencer ) {
		$j(this)
			.focus( function(){
				sequencer.getKeyBindings().onFocus();
			})
			.blur( function(){
				sequencer.getKeyBindings().onBlur();
			})
		return this;
	}
} )( jQuery );

mw.SequencerKeyBindings = function( sequencer ) {
	return this.init( sequencer );
};
mw.SequencerKeyBindings.prototype = {
	// set of key flags:
	shiftDown: false,
	ctrlDown: false,
	/* events */

	init: function( sequencer ){
		this.sequencer = sequencer;
		this.setupKeyBindigs()
	},
	onFocus: function( ){
		this.inputFocus = true;
		mw.log("text focus");
	},
	onBlur: function(){
		this.inputFocus = false;
		mw.log("text blur");
	},
	setupKeyBindigs: function(){
		var _this = this;
		// Set up key bindings
		$j( window ).keydown( function( e ) {
			mw.log( 'SequencerKeyBindings::pushed down on:' + e.which );
			if ( e.which == 16 )
				_this.shiftDown = true;

			if ( e.which == 17 )
				_this.ctrlDown = true;

			if ( ( e.which == 67 && _this.ctrlDown ) && !_this.inputFocus )
				$j( _this ).trigger( 'copy ');

			if ( ( e.which == 88 && _this.ctrlDown ) && !_this.inputFocus )
				$j( _this ).trigger( 'cut ');

			// Paste clip on v + ctrl while not focused on a text area:
			if ( ( e.which == 86 && _this.ctrlDown ) && !_this.inputFocus )
				$j( _this ).trigger( 'paste ');
		} );
		$j( window ).keyup( function( e ) {
			mw.log( 'SequencerKeyBindings::key up on ' + e.which );
			// User let go of "shift" turn off multi-select
			if ( e.which == 16 )
				_this.shiftDown = false;

			if ( e.which == 17 )
				_this.ctrlDown = false;

			// Escape key ( deselect )
			if ( e.which == 27 )
				$j( _this ).trigger( 'escape' );


			// Backspace or Delete key while not focused on a text area:
			if ( ( e.which == 8 || e.which == 46 ) && !_this.inputFocus ){
				$j( _this ).trigger( 'delete' );
			}
		} );
	}
};