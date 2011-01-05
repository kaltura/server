/**
* Timed text edit interface based off of participatory culture foundation timed text mockups.
*/
mw.addMessages( {
	"mwe-timedtext-editor" : "Timed text editor",
	"mwe-timedtext-stage-transcribe" : "Transcribe",
	"mwe-timedtext-stage-sync" : "Sync",
	"mwe-timedtext-stage-translate" : "Translate",
	"mwe-timedtext-stage-upload" : "Upload from local file",

	"mwe-timedtext-select-language": "Select language",
	"mwe-timedtext-file-language": "Subtitle file language",

	"mwe-timedtext-upload-text": "Upload text file",
	"mwe-timedtext-uploading-text": "Uploading text file",
	"mwe-timedtext-upload-text-desc-title": "Upload a text file",
	"mwe-timedtext-upload-text-desc-help": "The upload text file interface accepts .srt files",
	"mwe-timedtext-upload-text-desc-help-browse": "Browse your local computer for the srt file you want to upload",
	"mwe-timedtext-upload-text-desc-help-select" : "Select the language of the file",
	"mwe-timedtext-upload-text-desc-help-review" : "Review / edit the text content and then press upload to add the text",
	"mwe-timedtext-upload-text-preview" : "Review text",

	"mwe-timedtext-upload-text-success" : "Upload of timed text was successful",
	"mwe-timedtext-upload-text-done" : "Upload done",
	"mwe-timedtext-upload-text-fail-desc" : "Upload was unsuccessful",
	"mwe-timedtext-upload-text-fail-title" : "Upload failed",
	"mwe-timedtext-upload-text-another" : "Upload another",
	"mwe-timedtext-upload-text-done-uploading" : "Done uploading"
} );

mw.TimedTextEdit = function( parentTimedText ) {
	return this.init( parentTimedText );
}
mw.TimedTextEdit.prototype = {
	// The target container for the interface:
	target_container: null,

	// Interface steps can be "transcribe", "sync", "translate"
	textEditStages:{
		'upload':{
			'icon' : 'folder-open'
		}
		/*
		'transcribe':{
			'icon' : 'comment'
		},
		'sync':{
			'icon' : 'clock'
		},
		'translate':{
			'icon' : 'flag'
		}
		*/
	},

	/**
	 * @constructor
	 * @param {Object} parentTimedText The parent TimedText object that called the editor
	 */
	init: function( parentTimedText ) {
		this.parentTimedText = parentTimedText;
	},

	/**
	 * Show the editor UI
	 */
	showUI: function() {
		// Setup the parent container:
		this.createDialogContainer();

		// Setup the timedText editor interface
		this.initDialog();
	},

	/**
	 * Setup the dialog layout: s
	 */
	initDialog: function() {
		var _this =this;
		_this.createTabs();
	},

	/**
	 * Creates interface tabs from the textEditStages
	 */
	createTabs: function() {
		var _this = this;
		$tabContainer = $j( '<div />' )
			.attr( 'id', "TimedTextEdit-tabs" )
			.append( '<ul />' );
		for(var edit_stage_id in this.textEditStages) {
			var editStage = this.textEditStages[ edit_stage_id ];
			// Append the menu item:
			$tabContainer.find('ul').append(
				$j('<li>').append(
					$j('<a>')
					.attr( 'href', '#tab-' + edit_stage_id )
					.append(
						$j('<span />')
						.css( "float","left" )
						.addClass( 'ui-icon ui-icon-' + editStage.icon )
						,
						$j('<span>')
						.text( gM('mwe-timedtext-stage-' + edit_stage_id) )
					)
				)
			);
			// Append the menu item content container
			$tabContainer.append(
				$j('<div>')
				.attr( 'id', 'tab-' + edit_stage_id )
				.css({
					'height': $j( window ).height() - 270,
					'position': 'relative'
				})
			);
		}
		//debugger
		// Add the tags to the target:
		$j( _this.target_container ).append( $tabContainer );

		//Create all the "interfaces"
		for(var edit_stage_id in this.textEditStages) {
			_this.createInterface( edit_stage_id )
		}

		//Add tabs interface
		$j('#TimedTextEdit-tabs').tabs( {
			select: function( event, ui ) {
				_this.selectTab( $j( ui.tab ).attr( 'href' ).replace('#','') );
			}
		});

	},
	selectTab: function( tab_id ) {
		mw.log('sel: ' + tab_id);
	},

	/**
	 * Creates an interface for a given stage id
	 * @return {Object} the jquery interface
	 */
	createInterface: function( edit_stage_id) {
		$target = $j('#tab-' + edit_stage_id);
		if( this[edit_stage_id + 'Interface']) {
			this[ edit_stage_id + 'Interface']( $target );
		}else{
			$target.append( ' interface under development' );
		}
	},
	/**
	* Builds out and binds the upload interface to a given target
	* @param {Object} $target jQuery target for the upload interface
	*/
	uploadInterface: function( $target ) {
		var _this = this;
		// Check if user has XHR file upload support & we are on the target wiki

		$target.append(
			$j('<div />')
			.addClass( "leftcolumn" )
			.append('<h4>')
			.text( gM('mwe-timedtext-upload-text') ),
			$j('<div />')
			.addClass( 'rightcolumn' )
			.append(
				$j( '<span />' )
				.attr('id', "timed-text-rightcolum-desc")
				.append(
					$j('<h4>')
						.text( gM('mwe-timedtext-upload-text-desc-title') ),
					$j('<i>').text ( gM( 'mwe-timedtext-upload-text-desc-help' ) ),
					$j('<ul>').append(
						$j('<li>').text( gM('mwe-timedtext-upload-text-desc-help-browse') ),
						$j('<li>').text( gM('mwe-timedtext-upload-text-desc-help-select') ),
						$j('<li>').text( gM('mwe-timedtext-upload-text-desc-help-review') )
					)
				),
				//The text preview
				$j('<h3>')
					.text( gM( 'mwe-timedtext-upload-text-preview' ) ),
				$j('<textarea id="timed-text-file-preview"></textarea>')
			)
		)

		// Adjust the height of the text preview:
		$j('#timed-text-file-preview')
		.css({
			'width':'100%',
			'height': '300px'
		});

		// Add Select file:
		$target.append(
			$j('<div>').css({
				'width':'300px',
				'float': 'left'
			}).append(
				$j('<input />')
				.attr( {
					'type': "file",
					'id' : "timed-text-file-upload"
				}),
				$j('<br />')
			)
		)


		$target.append(
			//Get a little helper input field to update the language
			$j('<input />')
			.attr( {
				'id' : "timed-text-langKey-input",
				'type' : "text",
				'maxlength' : "10",
				'size' :"3"
			} )
			.change(function() {
				var langKey = $j(this).val();
				if( mw.Language.names[ langKey ] ) {
					$buttonTarget.find('.btnText').text(
						mw.Language.names[ langKey ]
					);
				}
			}),
			// Get a jQuery button object with language menu:
			$j.button( {
				'style': { 'float' : 'left' },
				'class': 'language-select-btn',
				'text': gM('mwe-timedtext-select-language'),
				'icon': 'triangle-1-e'
			} )
			.attr('id', 'language-select')
		)


		var $buttonTarget = $target.find('.language-select-btn');

		// Add menu container:
		var loc = $buttonTarget.position();
		$target.append(
			$j('<div>')
			.addClass('ui-widget ui-widget-content ui-corner-all')
			.attr( 'id', 'upload-language-select' )
			.loadingSpinner()
			.css( {
				'position' 	: 'relative',
				'z-index' 	: 10,
				'height'	: '180px',
				'width' 	: '180px',
				'overflow'	: 'auto',
				'font-size'	: '12px',
				'z-index'	: 1005
			} )
			.hide()
		);
		// Add menu binding to button target
		setTimeout(function(){
			$buttonTarget.menu( {
				'content'	: _this.getLanguageList(),
				'backLinkText' : gM( 'mwe-timedtext-back-btn' ),
				'targetMenuContainer': '#upload-language-select',
				'keepPosition' : true
			} );
			// force the layout ( menu binding does strange things )
			$j('#upload-language-select').css( {'left': '315px', 'top' : '87px', 'position' : 'absolute'});
		},10);


		//Add upload input bindings:
		$j( '#timed-text-file-upload' ).change( function( ev ) {
			if ( $j(this).val() ) {

				// Update the preview text area:
				var file = $j( '#timed-text-file-upload' ).get(0).files[0];
				if( file.fileSize > 1048576 ) {
					$j( '#timed-text-file-preview' ).text( 'Error the file you selected is too lage');
					return ;
				}
				var srtData = file.getAsBinary();
				srtData = srtData.replace( '\r\n', '\n' );
				$j( '#timed-text-file-preview' ).text( srtData );

				// Update the selected language
				var langKey = $j(this).val().split( '.' );
				var extension = langKey.pop();
				langKey = langKey.pop();
				if( mw.Language.names[ langKey ] ) {
					$buttonTarget.find('.btnText').text(
						mw.Language.names[ langKey ]
					);
					// Update the key code
					$j('#timed-text-langKey-input').val( langKey );
				}
			}
		});

		//Add an upload button:
		$target.append(
			$j('<div />')
			.css('clear', 'both'),
			$j('<br />'),
			$j('<br />'),
			$j.button( {
				'style': { 'float' : 'left' },
				'text': gM('mwe-timedtext-upload-text'),
				'icon': 'disk'
			} )
			.click( function() {
				_this.uploadTextFile();
			})
		);

	},
	/**
	 * Uploads the text content
	 */
	uploadTextFile: function() {
		// Put a dialog ontop
		mw.addLoaderDialog( gM( 'mwe-timedtext-uploading-text') );

		// Get timed text target title
		// NOTE: this should be cleaned up with accessors
		var targetTitleKey = this.parentTimedText.embedPlayer.apiTitleKey;

		// Add TimedText NS and language key and ".srt"
		targetTitleKey = 'TimedText:' + targetTitleKey + '.' + $j('#timed-text-langKey-input').val() + '.srt';

		// Get a token
		mw.getToken( targetTitleKey, function( token ) {
			mw.log("got token: " + token);
			var request = {
				'action' : 'edit',
				'title' : targetTitleKey,
				'text' : $j('#timed-text-file-preview').val(),
				'token': token
			};
			mw.getJSON( request, function( data ) {
				//Close the loader dialog:
				mw.closeLoaderDialog();

				if( data.edit && data.edit.result == 'Success' ) {
					var buttons = { };
					buttons[ gM("mwe-timedtext-upload-text-another")] = function() {
						// just close the current dialog:
						$j( this ).dialog('close');
					};
					buttons[ gM( "mwe-timedtext-upload-text-done-uploading" ) ] = function() {
						window.location.reload();
					};
					//Edit success
					setTimeout(function(){
						mw.addDialog( {
							'width' : '400px',
							'title' : gM( "mwe-timedtext-upload-text-done"),
							'content' : gM("mwe-timedtext-upload-text-success"),
							'buttons' : buttons
						});
					}, 10 );
				}else{
					//Edit fail
					setTimeout(function(){
						mw.addDialog({
							'width' : '400px',
							'title' : gM( "mwe-timedtext-upload-text-fail-title"),
							'content' :gM( "mwe-timedtext-upload-text-fail-desc"),
							'buttons' : gM( 'mwe-ok' )
						});
					},10 );
				}
			});
		})
	},
	
	/**
	 * Gets the language set.
	 *
	 * Checks off languages that area already "loaded" according to parentTimedText
	 *
	 * This is cpu intensive function
	 *	Optimize: switch to html string building, insert and bind
	 * 		(instead of building html with jquery calls )
	 * 	Optimize: pre-sort both language lists and continue checks where we left off
	 *
	 *  ~ what really a lot of time is putting this ~into~ the dom ~
	 */
	getLanguageList: function() {
		var _this = this;
		var $langMenu = $j( '<ul>' );
		// Loop through all supported languages:
		for ( var langKey in mw.Language.names ) {
			var language = mw.Language.names [ langKey ];
			var source_icon = 'radio-on';
			//check if the key is in the _this.parentTimedText source array
			for( var i in _this.parentTimedText.textSources ) {
				var pSource = _this.parentTimedText.textSources[i];
				if( pSource.lang == langKey) {
					source_icon = 'bullet';
				}
			}
			// call out to "anonymous" function to variable scope the langKey
			$langMenu.append(
				_this.getLangMenuItem( langKey , source_icon)
			);
		}
		return $langMenu;
	},
	
	getLangMenuItem: function( langKey , source_icon) {
		return $j.getLineItem(
			langKey + ' - ' + mw.Language.names[ langKey ],
			source_icon,
			function() {
				mw.log( "Selected: " + langKey );
				// Update the input box text
				$j('#timed-text-langKey-input').val( langKey );
				// Update the menu item:
				$j('#language-select').find('.btnText').text( mw.Language.names[ langKey ] )
			}
			);
	},
	/**
	 * Creates the interface dialog container
	 */
	createDialogContainer: function() {
		var _this = this;
		//Setup the target container:
		_this.target_container = '#timedTextEdit_target';
		$j( _this.target_container ).remove();
		$j( 'body' ).append(
			$j('<div>')
				.attr({
					'id' : 'timedTextEdit_target',
					'title' : gM( 'mwe-timedtext-editor' )
				})
				.addClass('TimedTextEdit')
		);

		// Build cancel button
		var cancelButton = {};
		var cancelText = gM( 'mwe-cancel' );
		cancelButton[ cancelText ] = function() {
			_this.onCancelClipEdit();
		};

		$j( _this.target_container ).dialog( {
			bgiframe: true,
			autoOpen: true,
			width: $j(window).width()-50,
			height: $j(window).height()-50,
			position : 'center',
			modal: true,
			draggable: false,
			resizable: false,
			buttons: cancelButton,
			close: function() {
				// @@TODO if we are 'editing' we should confirm they want to exist:
				$j( this ).parents( '.ui-dialog' ).fadeOut( 'slow' );
			}
		} );
		// set a non-blocking fit window request
		setTimeout(function(){
			$j( _this.target_container ).dialogFitWindow();
		},10);

		// Add the window resize hook to keep dialog layout
		$j( window ).resize( function() {
			$j( _this.target_container ).dialogFitWindow();
		} );

	},

	onCancelClipEdit: function() {
		var _this = this;
		// Cancel edit
		$j( _this.target_container ).dialog( 'close' );
	}
};
