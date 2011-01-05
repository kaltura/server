/**
 * Handles dialogs for sequence actions such as
 * 	"save sequence",
 * 	"rename",
 * 	"publish"
 *
 * Hooks into sequencerApiProvider to run the actual api operations
 */

mw.SequencerActionsEdit = function( sequencer ) {
	return this.init( sequencer );
};

mw.SequencerActionsEdit.prototype = {

	// Stores the local edit history to support undo / redo
	editStack : [],

	// Store the edit index
	editIndex : 0,

	init: function( sequencer ) {
		this.sequencer = sequencer;
	},
	// return the configured numbers of undos supported
	getNumberOfUndos: function(){
		return mw.getConfig( 'Sequencer.NumberOfUndos' );
	},

	selectAll: function(){
		// Select all the items in the timeline
		$target = this.sequencer.getTimeline().getTimelineContainer();
		$target.find( '.timelineClip' ).addClass( 'selectedClip' );
	},

	/**
	 * Set up the edit stack
	 */
	setupEditStack: function(){
		this.editStack = [];
		// Set the initial edit state:
		this.editStack.push( this.sequencer.getSmil().getXMLString() );
		// Disable undo
		this.sequencer.getMenu().disableMenuItem( 'edit', 'undo' );
	},

	/**
	 * Apply a smil xml transform state ( to support undo / redo )
	 */
	registerEdit: function(){
		//mw.log( 'ActionsEdit::registerEdit: stacksize' + this.editStack.length + ' editIndex: ' + this.editIndex );
		// Make sure the edit is distinct from the latest in the stack:
		var currentXML = this.sequencer.getSmil().getXMLString();
		if( currentXML == this.editStack[ this.editStack-1 ] ){
			mw.log("ActionsEdit::registerEdit on identical smil xml state ( no edit stack modification ) ")
			return ;
		}

		// Throw away any edit history after the current editIndex:
		if( this.editStack.length && this.editIndex > this.editStack.length ) {
			this.editStack = this.editStack.splice(0, this.editIndex);
		}

		// Shift the undo index if we have hit our max undo size
		if( this.editStack.length > this.getNumberOfUndos() ){
			this.editStack = this.editStack.splice(0, 1 );
		}

		// @@TODO could save space to just compute the diff in JS and store that
		// ie: http://code.google.com/p/google-diff-match-patch/
		// ( instead of the full xml text with "key-pages" every 10 edits or something like that.
		// ( should non-block compress in workerThread / need workerThread architecture )
		this.editStack.push( currentXML );

		// Update the editIndex
		this.editIndex = this.editStack.length - 1;

		// Enable the undo option:
		this.sequencer.getMenu().enableMenuItem( 'edit', 'undo' );
		this.sequencer.getMenu().enableMenuItem( 'sequence', 'save' );
	},

	/**
	 * Undo an edit action
	 */
	undo: function(){
		this.editIndex--;
		if( this.editStack[ this.editIndex ] ) {
			this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
			// Enable redo action
			this.sequencer.getMenu().enableMenuItem( 'edit', 'redo' );
		} else {
			// index out of range set to 0
			this.editIndex = 0;
			mw.log("Error: SequenceActionsEdit:: undo Already at oldest index:" + this.editIndex);
			// make sure to disable the menu item:
			this.sequencer.getMenu().disableMenuItem( 'edit', 'undo' );
		}
		// if at oldest undo disable undo option
		if( ( this.editIndex - 1 ) <= 0 ){
			this.sequencer.getMenu().disableMenuItem( 'edit', 'undo' );
		}
	},
	/**
	 * Redo an edit action
	 */
	redo: function(){
		this.editIndex ++;
		if( this.editStack[ this.editIndex ] ) {
			mw.log("DO redo for editIndex::" + this.editIndex + ' xml lenght' + this.editStack[ this.editIndex ].length );
			this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
			// Enable undo action
			this.sequencer.getMenu().enableMenuItem( 'edit', 'undo' );
		} else {
			// Index out of redo range set to last edit
			this.editIndex == this.editStack.length - 1
			mw.log( 'Error: SequencerActionsEdit::Redo: Already at most recent edit avaliable');
		}

		// if at newest redo disable redo option
		mw.log( this.editIndex + ' >= ' + ( this.editStack.length -1 ) );
		if( this.editIndex >= this.editStack.length -1 ){
			this.sequencer.getMenu().disableMenuItem( 'edit', 'redo' );
		}
	}
};