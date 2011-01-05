/**
 */

mw.includeAllModuleMessages();

/**
* Setup the sequencer jQuery binding:
*/

( function( $ ) {
	$.fn.sequencer = function( options ) {
		// Debugger
		if( $j( this.selector ).length == 0 ){
			mw.log("mw.Sequencer::Error missing target container");
			return;
		}
		var seqContainer = $j( this.selector ).get(0);

		// Support jQuery ui style 'destroy' call
		if( options == 'destroy' ){
			if( seqContainer['sequencer'] )
				delete seqContainer['sequencer'];
			return this;
		}


		// Check if we already have a sequencer associated with this target
		if( seqContainer['sequencer'] ){
			// xxx todo: pass on the options / action
			return ;
		}
		options['interfaceContainer'] = this.selector;

		// Issue a request to get the CSS file (if not already included):
		mw.log( 'Sequencer:: create new Sequencer' );

		// Initialize the sequence object (it will take over from there)
		seqContainer['sequencer'] = new mw.Sequencer( options );

		// Draw the sequencer UI
		seqContainer['sequencer'].drawUI();

		// Return the sequence jQuery object
		return this;

	};
} )( jQuery );

// Wrap in mw closure to avoid global variables
( function( mw ) {

/**
 * The set of valid sequencer options
 */
var mw_sequenceedit_default_options = {
	'interfaceContainer' : null,
	'title': null,
	'smilSource' : null,
	'newSequence' : null,
	'server' : null,
	'addMedia': null,
	'onExitCallback' : null,
	'videoAspect' : '4:3'
};
mw.Sequencer = function( options ) {
	return this.init( options );
};

// Set up the mvSequencer object
mw.Sequencer.prototype = {
	// lazy init id for the sequencer instance.
	id: null,
	// Holds sequencer configuration options
	options: {},

	init: function( options ){
		if(!options){
			options = {};
		}
		//	Validate and set default options :
		for( var optionName in mw_sequenceedit_default_options ){
			if( typeof options[ optionName] != 'undefined'){
				this.options[optionName] = options[ optionName] ;
			} else {
				this.options[optionName] = mw_sequenceedit_default_options[ optionName ];
			}
		}

		// Move specific options into core
		if( this.options.smilSource ){
			this.smilSource = options.smilSource;
		}
		if( this.options.interfaceContainer ){
			this.interfaceContainer = this.options.interfaceContainer;
		}

		// For style properties assign top level mwe-sequencer class
		this.getContainer()
			.addClass('mwe-sequencer');
	},

	getOption: function( optionName ){
		if( this.options[ optionName ]){
			return this.options[ optionName ];
		}
		return false;
	},

	// Return the container id for the sequence
	getId: function(){
		if( !this.id ){
			// Assign the container an id if missing one::
			if( ! this.getContainer().attr('id') ){
				this.getContainer().attr('id', 'sequencer_' + new Date().getTime() + Math.random() );
			}
			this.id = this.getContainer().attr('id');
		}
		return this.id;
	},

	/**
	 * Update the smil xml and then update the interface
	 */
	updateSmilXML: function( smilXML ){
		mw.log( "Sequencer::updateSmilXML" );
		var _this = this;
		// Update the embedPlayer smil:
		this.getSmil().updateFromString( smilXML );

		// Get a duration ( forceRefresh to clear the cache )
		this.getEmbedPlayer().getDuration( true );

		// Redraw the timeline
		this.getTimeline().drawTimeline();

		// if a tool is displayed update the tool:
		this.getTools().updateToolDisplay();
	},

	/**
	 * Draw the initial sequence ui, uses ui.layout for adjustable layout
	 */
	drawUI: function( ){
		var _this = this;
		mw.log( "Sequencer:: drawUI to: " + this.interfaceContainer + ' ' + this.getContainer().length );

		// Add the ui layout
		this.getContainer().html(
			this.getUiLayout()
		);

		// Once the layout is in the dom setup resizableLayout "layout" options
		this.applyLayoutBindings();

		// Add the smil player
		_this.getPlayer().drawPlayer( function(){
			// Once the player and smil is loaded ::
			// start buffering
			_this.getEmbedPlayer().load();

			// Add the timeline
			_this.getTimeline().drawTimeline();

			// Draw the top level menu
			_this.getMenu().drawMenu();

			// initialize the edit stack to support undo / redo actions
			_this.getActionsEdit().setupEditStack();
		});

	},
	/**
	 * Load a smil source if newSequence flag is set create new sequence source
	 * @param {function} callback Function called with smilSource
	 */
	getSmilSource: function( callback ){
		var _this = this;
		if( !_this.smilSource ){
			if( _this.getOption( 'newSequence' ) ){
				_this.smilSource = _this.getDataUrl( _this.getNewSmilXML() );
			} else {
				mw.log( "Load smil source from server" );
				// Load from the server
				_this.getServer().getSmilXml( function( smilXml ){
					// xxx should parse the sequence data
					if( smilXml == '' ){
						smilXml = _this.getNewSmilXML();
					}
					_this.smilSource = _this.getDataUrl( smilXml );
					callback( _this.smilSource );
				});
				// Wait for server to return smil source
				return ;
			}
		}
		// return the smilSource
		callback( _this.smilSource );
	},
	getDataUrl: function( xmlString ){
		if( ! xmlString ){
			xmlString = this.getSmil().getXMLString();
		}
		return 'data:text/xml;charset=utf-8,' + encodeURIComponent( xmlString );
	},
	getNewSmilXML: function( ){
		var title = ( this.getOption('title') ) ?
					this.getOption('title') :
					gM('mwe-sequencer-untitled-sequence');
		return '<?xml version="1.0" encoding="UTF-8"?>' +
			"\n" + '<smil baseProfile="Language" version="3.0" xmlns="http://www.w3.org/ns/SMIL">' +
			"\n\t" + '<head>' +
			"\n\t\t" + '<meta name="title" content="' + mw.escapeQuotesHTML( title ) + '" />' +
			"\n\t" + '</head>' +
			"\n\t" + '<body>' +
			"\n\t\t" + '<par>' +
			"\n\t\t\t" + '<seq title="Video Track 1" tracktype="video">' +
			"\n\t\t\t" + '</seq>' +
			"\n\t\t\t" + '<seq title="Audio track 1" tracktype="audio">' +
			"\n\t\t\t" + '</seq>' +
			"\n\t\t" + '</par>' +
			"\n\t" + '</body>' +
			"\n" + '</smil>';
	},
	getServer: function(){
		if( !this.server ){
			this.server = new mw.SequencerServer( this );
		}
		return this.server;
	},

	getMenu: function(){
		if( !this.menu){
			this.menu = new mw.SequencerMenu( this );
		}
		return this.menu;
	},
	getPlayer: function(){
		if( ! this.player ){
			this.player = new mw.SequencerPlayer( this );
		}
		return this.player;
	},

	/* Menu Action getters */
	getActionsSequence: function(){
		if( ! this.actionsSequence ){
			this.actionsSequence = new mw.SequencerActionsSequence( this );
		}
		return this.actionsSequence;
	},
	getActionsView: function(){
		if( ! this.actionsView ){
			this.actionsView = new mw.SequencerActionsView( this );
		}
		return this.actionsView;
	},
	getActionsEdit: function(){
		if( !this.actionsEdit ){
			this.actionsEdit = new mw.SequencerActionsEdit( this );
		}
		return this.actionsEdit;
	},
	getEmbedPlayer:function(){
		 return this.getPlayer().getEmbedPlayer();
	},
	getSmil: function(){
		if( !this.smil ){
			this.smil = this.getEmbedPlayer().smil;
		}
		return this.smil;
	},
	getTimeline: function(){
		if( !this.timeline ){
			this.timeline = new mw.SequencerTimeline( this );
		}
		return this.timeline;
	},
	getTools: function(){
		if( !this.editTools ){
			this.editTools = new mw.SequencerTools( this );
		}
		return this.editTools;
	},
	getAddMedia: function(){
		if( ! this.addMedia ){
			this.addMedia = new mw.SequencerAddMedia( this );
		}
		return this.addMedia;
	},
	getAddByUri: function(){
		if( ! this.addByUri ){
			this.addByUri = new mw.SequencerAddByUri( this );
		}
		return this.addByUri;
	},
	getKeyBindings:function(){
		if( ! this.keyBindings ){
			this.keyBindings = new mw.SequencerKeyBindings( this );
		}
		return this.keyBindings;
	},

	// Apply the re-sizable layout bindings and default sizes
	applyLayoutBindings: function(){
		var _this = this;
		this.getContainer().find('.resizableLayout').layout({
			'applyDefaultStyles': true,
			/* player container */
			'east__minSize': 240,
			'east__size': 440,
			'east__onresize':function(){
				_this.getPlayer().resizePlayer();
			},

			/* edit container */
			'center__minSize' : 300,

			/* timeline container */
			'south__minSize' : 160,
			'south__size' : 220,
			'south__onresize' : function(){
				_this.getTimeline().resizeTimeline();
			}
		});
	},

	/**
	 * Get the UI layout
	 */
	getUiLayout: function(){
		var _this = this;
		// xxx There is probably a cleaner way to generate a list of jQuery objects than $j('new').children();
		return $j('<div />').append(
			$j('<div />')
			.addClass( "mwseq-menu" )
			.css({
				'position':'absolute',
				'height': '25px',
				'width': '100%',
				'top': '3px',
				'left' : '0px',
				'background' : '#fff'
			})
			.text( gM('mwe-sequencer-loading-menu') )
			,

			$j('<div />')
			.addClass('resizableLayout')
			.css({
				'position':'absolute',
				'top': '27px',
				'left': '0px',
				'right': '0px',
				'bottom':'0px'
			})
			.append(
				$j('<div />')
					.addClass( "ui-layout-center mwseq-edit" )
					.html( _this.getTools().getDefaultText() ),
				$j('<div />')
					.addClass( "ui-layout-east mwseq-player" )
					.text( gM('mwe-sequencer-loading-player') ),
				$j('<div />')
					.addClass( "ui-layout-south mwseq-timeline" )
					.text( gM('mwe-sequencer-loading-timeline') )
			)
		)
		.children();
	},

	getMenuTarget: function(){
		return this.getContainer().find( '.mwseq-menu' );
	},
	getEditToolTarget: function(){
		return this.getContainer().find( '.mwseq-edit' );
	},
	getContainer: function(){
		return $j( this.interfaceContainer );
	}
}

} )( window.mw );
