/**
 * Handles dialogs for sequence actions such as
 * 	"save sequence",
 * 	"rename",
 * 	"publish"
 *
 * Hooks into sequencerApiProvider to run the actual api operations
 */

mw.SequencerActionsSequence = function( sequencer ) {
	return this.init( sequencer );
};

mw.SequencerActionsSequence.prototype = {
	init: function( sequencer ) {
		this.sequencer = sequencer;
	},
	/**
	 * present an new dialog to the user and open the sequence in a new window
	 * ( presently very similar to open )
	 */
	newSequence : function(){
		var _this = this;
		var $content = $j('<div />').append(
				gM('mwe-sequencer-new-summary' ),
				$j('<input />')
				.css({ 'width': 400 })
				.attr({
					'id' : 'sequenceNewNameInput',
					'maxlength': 255
				})
				// Make sure keys press does not affect the sequencer interface
				.sequencerInput( _this.sequencer )
			);
		// XXX todo we should have an autocomplete on sequence name!

		var buttons = {};
		buttons[ gM('mwe-cancel') ] = function(){ $j( this ).dialog( 'cancel' ); };

		// For now just support server based open .. ideally we could browse for file
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-menu-sequence-new-desc'),
			'content' : $content,
			'buttons' : buttons,
			'width' : 450
		});
		$dialog.find('input').focus();
		// Add a special open button
		$dialog.parent().find( '.ui-dialog-buttonpane' ).prepend(
			$j.button({
				'icon' : 'document',
				'text' : gM('mwe-sequencer-menu-sequence-new')
			})
			// Match button layout
			.css({
				'margin':'0.5em 0.4em 0.5em 0',
				'padding' : '0.2em 1.4em 0.3em'
			})
			.attr({
				'id' : 'sequenceOpenButton',
				'target' : '_new',
				'href' : '#'
			}).click( function(){
				// Update the link
				$j(this).attr({
					'href':
						mw.getRemoteSequencerLink(
							mw.escapeQuotesHTML(
								_this.sequencer.getServer().getSequenceEditUrl(
										// ( Sequence: is automatically pre-appended with getSequencePageUrl
										// ( don't use Sequence: in the title )
										$j('#sequenceNewNameInput').val().replace(/Sequence:/i, '')
								)
							)
						)
				});
				// Close the dialog
				$j(this).dialog( 'close' );
				// Follow the link
				return true;
			})
		);
	},
	/**
	 * present an open dialog to the user, and open the sequence in a new window
	 */
	open: function(){
		var _this = this;
		var $content = $j('<div />').append(
				gM('mwe-sequencer-open-summary' ),
				$j('<input />')
				.css({ 'width': 400 })
				.attr({
					'id' : 'sequenceOpenNameInput',
					'maxlength': 255
				})
				// Make sure keys press does not affect the sequencer interface
				.sequencerInput( _this.sequencer )
			);
		// XXX todo we should have an autocomplete on sequence name!
		var buttons = {};
		buttons[ gM('mwe-cancel') ] = function(){ $j( this ).dialog( 'cancel' ); };

		// For now just support server based open .. ideally we could browse for file
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-menu-sequence-open-desc'),
			'content' : $content,
			'buttons' : buttons,
			'width' : 450
		});
		$dialog.find('input').focus();
		// Add a special open button
		$dialog.parent().find( '.ui-dialog-buttonpane' ).prepend(
			$j.button({
				'icon' : 'folder-open',
				'text' : gM('mwe-sequencer-menu-sequence-open')
			})
			// Match button layout
			.css({
				'margin':'0.5em 0.4em 0.5em 0',
				'padding' : '0.2em 1.4em 0.3em'
			})
			.attr({
				'id' : 'sequenceOpenButton',
				'target' : '_new',
				'href' : '#'
			}).click( function(){
				// Update the link
				$j(this).attr({
					'href':
						mw.getRemoteSequencerLink(
							mw.escapeQuotesHTML(
								_this.sequencer.getServer().getSequenceViewUrl(
										// ( Sequence: is automatically pre-appended with getSequencePageUrl
										// ( don't use Sequence: in the title )
										$j('#sequenceOpenNameInput').val().replace(/Sequence:/i, '')
								)
							)
						)
				});
				// Close the dialog
				$j(this).dialog( 'close' );
				// Follow the link
				return true;
			})
		);
	},
	save: function(){
		var _this = this;
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-menu-sequence-save-desc'),
			'content' : gM('mwe-sequencer-loading_user_rights'),
			'width' : 450
		});

		// Check if we have an api provider defined
		if( ! this.sequencer.getServer().isConfigured() ){
			$dialog.html( gM('mwe-sequencer-no-server-defined') );
			return ;
		}

		// Check if we have unsaved changes ( don't save for no reason )
		if( !_this.sequencer.getServer().hasLocalChanges() ){
			$dialog.html( gM('mwe-sequencer-save-no-changes') );
			var closeButton = {};
			closeButton[ gM('mwe-ok') ]= function(){ $j(this).dialog('close'); };
			$dialog.dialog( "option", "buttons", closeButton);
			return ;
		}


		// Check if we can save
		this.sequencer.getServer().userCanSave( function( canSave ){
			if( canSave === false ){
				$dialog.dialog( "option", "title", gM('mwe-sequencer-no_edit_permissions') );
				$dialog.html( gM( 'mwe-sequencer-no_edit_permissions-desc') );
				// Add close text
				$dialog.dialog( "option", "closeText", gM('mwe-ok') );
				return ;
			}
			_this.showSaveDialog( $dialog );
		});
	},
	showSaveDialog: function( $dialog ){
		var _this = this;
		// Else user 'can save' present a summary text box
		var saveDialogButtons = {};
		saveDialogButtons[ gM('mwe-sequencer-menu-sequence-save-desc') ] = function(){
			// grab the save summary before setting dialog to loading:
			var saveSummary = $j('#sequenceSaveSummary').val();
			// set dialog to loading
			$dialog.empty().append(
				gM('mwe-sequencer-saving_wait' ),
				$j('<div />').loadingSpinner()
			);
			// Remove buttons while loading
			$dialog.dialog( "option", "buttons", {} );

			_this.sequencer.getServer().save(
				/* Save summary */
				saveSummary,
				/* Save xml */
				_this.sequencer.getSmil().getXMLString(),
				/* Save callback */
				function( status, errorMsg ){
					if( status === false ){
						$dialog.text( errorMsg )
					} else {
						// save success
						$dialog.text( gM( 'mwe-sequencer-save_done' ) )
					}
					// Only let the user hit 'ok'
					var closeButton = {};
					closeButton[gM('mwe-ok')]= function(){ $j(this).dialog('close') };
					$dialog.dialog( "option", "buttons", closeButton);
				}
			);
		};
		saveDialogButtons[ gM('mwe-sequencer-edit_cancel') ] = function(){
			$dialog.dialog('close');
		};

		$dialog.empty().append(
			gM('mwe-sequencer-save-summary' ),
			$j('<input />')
			.css({ 'width': 400 })
			.attr({
				'id' : 'sequenceSaveSummary',
				'maxlength': 255
			})
			// Make sure keys press does not affect the sequencer interface
			.sequencerInput( _this.sequencer )
		)
		.dialog( "option", "buttons", saveDialogButtons )
		.dialog( "option", "title", gM('mwe-sequencer-menu-sequence-save-desc') )
		// give the input focus
		.find('input').focus()
	},
	renderToDisk: function(){
		var _this = this;
		// add a loading dialog
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-menu-sequence-publish-desc'),
			'content' : gM('mwe-sequencer-loading-publish-render'),
			'width' : 450,
			'height' : 470,
			'draggable' : false,
			'resizable' : false
		});
		mw.load( ['AddMedia.firefogg','FirefoggRender'], function(){
			_this.doPublish( $dialog, true );
		});
	},
	/**
	 * Display the publish dialog
	 * ( confirm the user has firefogg and rights to save a new version of the file )
	 */
	publish: function(){
		var _this = this;
		// add a loading dialog
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-menu-sequence-publish-desc'),
			'content' : gM('mwe-sequencer-loading-publish-render'),
			'width' : 450,
			'height' : 470,
			'draggable' : false,
			'resizable' : false
		});

		// Check if we have unsaved changes ( don't publish unsaved changes )
		if( _this.sequencer.getServer().hasLocalChanges() ){
			$dialog.empty().html( gM('mwe-sequencer-please-save-publish'))
			var buttons = {};
			buttons[ gM( 'mwe-sequencer-menu-sequence-save-desc') ] = function(){
				_this.save();
			};
			buttons[ gM('mwe-cancel') ] = function(){
				$j( this ).dialog( 'close' );
			}
			$dialog.dialog( 'option', 'buttons', buttons);
			return;
		}

		$dialog.append( $j('<div />').loadingSpinner() );

		// Check if the published version is already the latest
		_this.sequencer.getServer().isPublished( function( isPublished ){
			if( isPublished ){
				$dialog.empty().text( gM('mwe-sequencer-already-published') )
				var buttons = {};
				buttons[ gM('mwe-ok') ] = function(){
					$j( this ).dialog( 'close' );
				}
				$dialog.dialog( 'option', 'buttons', buttons);
			} else {
				mw.load( ['AddMedia.firefogg','FirefoggRender'], function(){
					_this.doPublish( $dialog, false )
				});
			}
		});
	},
	doPublish: function( $dialog, localFile ){
		var _this = this;

		// Get a Firefogg object to check if firefogg is installed
		var myFogg = new mw.Firefogg( {
			'only_fogg':true
		});
		if ( !myFogg.getFirefogg() ) {
			$dialog.empty().append(
				$j('<div />').attr('id', 'show_install_firefogg')
			);
			myFogg.showInstallFirefog( '#show_install_firefogg' );
			return ;
		}

		// Build a data-url of the current sequence:
		$dialog.dialog( "option", "title", gM('mwe-sequencer-running-publish') );

		$dialog.empty().append(
			$j( '<video />' )
			.attr({
				'id': 'publishVideoTarget',
				'src' : _this.sequencer.getDataUrl(),
				'type' : 'application/smil'
			})
			.css({
				'width' : '400px',
				'height' : '300px'
			})
			,
			$j('<div />' )
			.css( 'clear', 'both' ),
			$j('<span />' ).text( gM( 'mwe-sequencer-publishing-status') ),
			$j('<span />' ).attr( 'id', 'firefoggStatusTarget' ),
			$j('<span />').attr('id', 'firefoggPercentDone')
			.css('float', 'right')
			.text("%"),
			$j('<div />')
			.attr( 'id', 'firefoggProgressbar')
			.css({
				'width': '100%',
				'height' : '20px'
			})

		);

		// Embed the player and continue application flow
		$j('#publishVideoTarget').embedPlayer({
			'controls' : false
		}, function(){

			// wait 100ms before starting the firefogg render ( avoids page lock
			// and ensures we don't get a loading spinner for first frame of render)
			setTimeout(function(){

				// this should be depreciated ( hidden interface bug in mwEmbed ?)
				$j('#publishVideoTarget').parent().show();

				// Start up the render
				var foggRender = $j('#publishVideoTarget').firefoggRender({
					'statusTarget' : '#firefoggStatusTarget',
					'saveToLocalFile' : localFile,
					'onProgress' : function( progress ){
						var progressPrecent = ( Math.round( progress * 10000 ) / 100 );
						$j('#firefoggPercentDone').text(
								progressPrecent +
							'%'
						)
						$j("#firefoggProgressbar").progressbar({
							"value" : Math.round( progress * 100 )
						});
						// xxx WTF? no idea why progressbar above is not working
						$j("#firefoggProgressbar .ui-progressbar-value").css('width', Math.round( progress * 10000 ) / 100 + '%');
					},
					'doneRenderCallback': function( fogg ){
						if( localFile ){
							$dialog.html( gM('mwe-sequencer-save_done') );
						} else {
							_this.uploadRenderedVideo( $dialog, fogg );
						}
					}
				});
				var buttons = {};
				buttons[ gM('mwe-cancel') ] = function(){
					foggRender.stopRender();
					$j( this ).dialog( 'close' );
				}
				// Add cancel button
				$dialog.dialog( "option", "buttons", buttons );
				if( !foggRender.doRender() ){
					// do render returns false on firefox gui cancel close the dialog:
					$dialog.dialog("close");
				}
			}, 100);
		});
	},

	// Upload the video from a supplied fogg target
	// note xx this might be better in the firefogg library since it has firefogg specific calls
	// @param {jQuery Object } $dialog
	// @param {firefogg Object}
	uploadRenderedVideo: function( $dialog, fogg ){
		var _this = this;
		$j( '#firefoggStatusTarget' ).text( gM('mwe-sequencer-publishing-uploading' ) );
		var updateUploadStatus = function(){
			if( fogg.status() == 'uploading' ){
				$j('#firefoggPercentDone').text(
					( Math.round( fogg.progress() * 10000 ) / 100 ) +
					'%'
				)
				setTimeout(updateUploadStatus, 1000);
				return ;
			}
			// Parts of this code are replicated in firefogg upload handler
			// xxx should refactor so they share a common handler
			if( fogg.status() == 'upload done' ){
				var response_text = fogg.responseText;
				if ( !response_text ) {
					try {
						var pstatus = JSON.parse( fogg.uploadstatus() );
						response_text = pstatus["responseText"];
					} catch( e ) {
						mw.log( "Error: could not parse firefogg responseText: " + e );
					}
				}
				try {
					var apiResult = JSON.parse( response_text );
				} catch( e ) {
					mw.log( "Error: could not parse response_text::" + response_text +
						' ...for now try with eval...' );
					try {
						var apiResult = eval( response_text );
					} catch( e ) {
						var apiResult = null;
					}
				}

				// Check the api response
				if ( !apiResult || apiResult.error || ( apiResult.upload &&
						( apiResult.upload.result == "Failure" || apiResult.upload.error ) ) ) {

					$dialog.dialog( 'option', 'title', gM('mwe-sequencer-publishing-error' ) );
					// xxx improve error handling
					$dialog.empty().text( gM( 'mwe-sequencer-publishing-error-upload-desc' ) )
					return ;
				}

				_this.uploadSuccess( $dialog );
				return ;
			}
		}
		this.sequencer.getServer().getUploadRequestConfig( function( url, request ){
			fogg.post( url, 'file', JSON.stringify( request ) );
			updateUploadStatus();
		})
	},
	uploadSuccess: function($dialog, apiResult){
		var _this = this;
		// Update the description page:
		$dialog.html( gM('mwe-sequencer-publishing-updatepage' ) );

		// Update the file description
		this.sequencer.getServer().updateSequenceFileDescription( function( status ){
			if( status === true ){
				_this.fileDescriptionSuccess( $dialog );
			} else {
				// xxx do better error handling
				$dialog.empty().text( gM( 'mwe-sequencer-publishing-error-adding-desc' ) );
			}
		});
	},
	fileDescriptionSuccess: function( $dialog ){
		var _this = this;
		// Success link to the sequence page / ok closes dialog
		$dialog.dialog( 'option', 'title', gM('mwe-sequencer-publishing-success' ) );
		var button = {};
		button[ gM('mwe-ok') ] = function(){
			$j( this ).dialog('close')
		};
		$dialog.dialog( 'option', 'button', button );
		// for some reason we lose button height :( (jquery bug ? )
		$dialog.parent().css( 'height', $dialog.height() + 100 );

		$dialog.empty().html( gM('mwe-sequencer-publishing-success-desc',
			$j('<a />')
			.attr({
				'target': '_new',
				'href': wgArticlePath.replace( '$1', 'File:' +_this.sequencer.getServer().getVideoFileName()	)
			})
		) );
		// Update the buttons
		var buttons = {};
		buttons[ gM('mwe-ok') ] = function(){
			$j( this ).dialog('close');
		};
		$dialog.dialog( 'option', 'buttons', buttons);
	},
	/**
	 * exit the sequencer.
	 *  confirm we want to 'lose' changes (if not let the user save changes)
	 */
	exit: function(){
		var _this = this;
		if( _this.sequencer.getServer().hasLocalChanges() ){
			var buttons = {};
			buttons[ gM( 'mwe-sequencer-menu-sequence-save-desc') ] = function(){
				_this.save();
			};
			buttons[ gM('mwe-sequencer-menu-sequence-exit-desc') ] = function(){
				_this.closeSequencer();
				$j(this).dialog('close');
			}
			// Confirm the user wants to exit
			mw.addDialog( {
				'title': gM('mwe-sequencer-confirm-exit'),
				'content' : gM('mwe-sequencer-confirm-exit-desc'),
				'buttons' : buttons,
				'width' : '400px'
			})
		} else {
			_this.closeSequencer();
		}
	},
	closeSequencer: function(){
		var _this = this;
		this.sequencer.getContainer().fadeOut(
			function(){
				// Check if there is an on exit callback
				if( _this.sequencer.getOption('onExitCallback') ){
					// Send a flag of weather the sequence 'changed' or not
					_this.sequencer.getOption('onExitCallback')(
						_this.sequencer.getServer().hasSequenceBeenSavedOrPublished()
					);
				}
				$j( this ).remove();
			}
		);
	}
};