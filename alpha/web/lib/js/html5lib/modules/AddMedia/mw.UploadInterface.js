/**
* This handles upload interfaces
*
* There are several interface types:
*
* Inline interface
*     Dispatches updates to an inline html target
*
* Dialog interface
*     There is only one upload and it results in dialogs taking up the full screen
*
* Iframe interface
*     Dispatches updates to an iframe target for upload proxy
*
*/
mw.addMessages({
	"mwe-upload-in-progress" : "Upload in progress (do not close this window)",
	"mwe-uploaded-status" : "$1% Uploaded",
	"mwe-transcoded-status" : "$1% Transcoded",
	"mwe-uploaded-time-remaining" : "Time remaining: $1",
	"mwe-upload-done" : "Your upload <i>should be<\/i> accessible."
} );

/**
 * Set the base uploadInterface
 */
mw.UploadInterface = { };

/**
 * Upload Dialog Interface
 */
mw.UploadDialogInterface = function( uploadHandler ) {
	// Set a reference the uploadHandler if provided
	if( uploadHandler ) {
		this.uploadHandler = uploadHandler;
	}
	return this;
}
mw.UploadDialogInterface.prototype = {

	// The following are really state of the upload, not the interface.
	// we are currently only managing one, so this is okay... for now.
	uploadBeginTime: null,

	/**
	* Setup the dialog display
	* @param {Object} options
	*/
	setup: function( options ) {
		var _this = this;

		if( ! options ){
			options = { };
		}

		// Start the "upload" time
		this.uploadBeginTime = ( new Date() ).getTime();

		// Remove the old instance if present
		if( $j( '#upProgressDialog' ).length != 0 ) {
			$j( '#upProgressDialog' ).dialog( 'destroy' ).remove();
		}

		// Add a new one
		$j( 'body' ).append(
			$j( '<div />')
			.attr( 'id', "upProgressDialog" )
		);

		if( !options.title ) {
			options.title = gM('mwe-upload-in-progress');
		}

		$j( '#upProgressDialog' ).dialog( {
			title : options.title,
			bgiframe: true,
			modal: true,
			draggable: true,
			width: 450,
			heigh: 200,
			beforeclose: function( event, ui ) {
				// If the upload is not complete, ask the user if they want to cancel
				if ( event.button == 0 && _this.action_done === false ) {
					_this.onCancel( this );
					return false;
				} else {
					// Complete already, allow close
					return true;
				}
			},
			buttons: _this.getCancelButton()
		} );

		mw.log( 'upProgressDialog::dialog done' );

		var $progressContainer = $j('<div />')
			.attr('id', 'up-pbar-container')
			.css({
				'height' : '15px'
			});

		// Add the progress bar
		$progressContainer.append(
			$j('<div />')
				.attr('id', 'up-progressbar')
				.css({
					'height' : '15px'
				})
		);

		// Add the status container
		$progressContainer.append( $j('<span />' )
			.attr( 'id', 'up-status-container')
			.css( 'float', 'left' )
			.append(
				$j( '<span />' )
				.attr( 'id', 'up-status-state' )
			)
		);

		this.statusType = options.statusType;
		
		var statusType = ( this.statusType == 'transcode' ) ? 'mwe-transcoded-status' : 'mwe-uploaded-status';
		$progressContainer.find( '#up-status-state' ).text( gM( statusType, 0 ) );
		

		// Add the estimated time remaining
		$progressContainer.append(
			$j('<span />')
			.attr( 'id', 'up-etr' )
			.css( 'float', 'right' )
			.text( gM( 'mwe-uploaded-time-remaining', '' ) )
		);

		// Add the status container to dialog div
		$j( '#upProgressDialog' ).empty().append( $progressContainer	);

		// Open the empty progress window
		$j( '#upProgressDialog' ).dialog( 'open' );

		// Create progress bar
		$j( '#up-progressbar' ).progressbar({
			value: 0
		});
	},

	/**
	 * Update the progress bar to a given completion fraction (between 0 and 1)
	 * NOTE: This progress bar is used for encoding AND for upload. The dialog title is set elsewhere
	 *
	 * @param {Float} progress Progress float
	 * @param {Number} [loaded] optional Bytes loaded
	 * @param {Number} [contentLength] optional Length of content
	 */
	updateProgress: function( fraction, loaded, contentLength ) {
		var _this = this;

		$j( '#up-progressbar' ).progressbar( 'value', parseInt( fraction * 100 ) );
		
		var statusType = ( this.statusType == 'transcode' ) ? 'mwe-transcoded-status' : 'mwe-uploaded-status';
		$j( '#up-status-state' ).html( gM( statusType, parseInt( fraction * 100 ) ) );

		if ( _this.uploadBeginTime) {
			var elapsedMilliseconds = ( new Date() ).getTime() - _this.uploadBeginTime;
			if (fraction > 0.0 && elapsedMilliseconds > 0) { // or some other minimums for good data
				var fractionPerMillisecond = fraction / elapsedMilliseconds;
				var remainingSeconds = parseInt( ( ( 1.0 - fraction ) / fractionPerMillisecond ) / 1000 );
				$j( '#up-etr' ).html( gM( 'mwe-uploaded-time-remaining', mw.seconds2npt( remainingSeconds ) ) );
			}
		}
		if( loaded && contentLength ){
			$j( '#up-status-container' ).text(
				gM( 'mwe-upload-stats-fileprogress',
					[
						mw.Language.formatSize( data.upload['loaded'] ),
						mw.Language.formatSize( data.upload['content_length'] )
					]
				)
			);
		} else if ( loaded ){
			$j( '#up-status-container' ).text(
				gM( 'mwe-upload-stats-fileprogress',
					[
						mw.Language.formatSize( data.upload['loaded'] ),
						gM( 'mwe-upload-unknown-size' )
					]
				)
			);
		}

	},

	/**
	 * UI cancel button handler.
	 * Show a dialog box asking the user whether they want to cancel an upload.
	 * @param Element dialogElement Dialog element to be canceled
	 */
	onCancel: function( dialogElement ) {
		//confirm:
		if ( confirm( gM( 'mwe-cancel-confim' ) ) ) {
			// NOTE: (cancel the encode / upload)
			$j( dialogElement ).dialog( 'close' );
		}
	},

	/**
	 * Set the dialog to loading
	 */
	setLoading: function( ) {
		this.action_done = false;
		//Update the progress dialog (no bar without XHR request)
		$j( '#upProgressDialog' ).loadingSpinner();
	},

	/**
	 * Set the interface with a "title", "msg text" and buttons prompts
	 * list of buttons below it.
	 *
	 * @param title_txt Plain text
	 * @param msg HTML
	 * @param buttons See http://docs.jquery.com/UI/Dialog#option-buttons
	 */
	setPrompt: function( title_txt, msg, buttons ) {
		var _this = this;

		if ( !title_txt )
			title_txt = _this.getProgressTitle();

		if ( !buttons ) {
			// If no buttons are specified, add a close button
			buttons = {};
			buttons[ gM( 'mwe-ok' ) ] = function() {
				$j( this ).dialog( 'close' ).remove();
			};
		}

		$j( '#upProgressDialog' ).dialog( 'option', 'title', title_txt );
		if ( !msg ) {
			$j( '#upProgressDialog' ).loadingSpinner();
		} else {
			$j( '#upProgressDialog' ).html( msg );
		}
		$j( '#upProgressDialog' ).dialog( 'option', 'buttons', buttons );
	},


	/**
	 * Given the result of an action=upload API request, display the error message
	 * to the user.
	 *
	 * @param {Object} apiRes The result object
	 */
	showApiError: function( apiRes ) {
		var _this = this;
		// NOTE: this could be simplified and cleaned up
		// by simplified the error output provided by the upload api

		// Generate the error button
		var buttons = {};
		buttons[ gM( 'mwe-return-to-form' ) ] = function() {
			_this.returnToForm( this );
		};


		if ( apiRes && apiRes.error || ( apiRes.upload && apiRes.upload.result == "Failure" ) ) {

			// Check a few places for the error code
			var error_code = 0;
			var errorReplaceArg = '';
			if ( apiRes.error && apiRes.error.code ) {
				error_code = apiRes.error.code;
			} else if ( apiRes.upload.code && typeof apiRes.upload.code == 'object' ) {
				if ( apiRes.upload.code[0] ) {
					error_code = apiRes.upload.code[0];
				}
				if ( apiRes.upload.code['status'] ) {
					error_code = apiRes.upload.code['status'];
					if ( apiRes.upload.code['filtered'] ){
						errorReplaceArg = apiRes.upload.code['filtered'];
					}
				}
			}

			var error_msg = '';
			if ( typeof apiRes.error == 'string' ){
				error_msg = apiRes.error;
			}
			// There are many possible error messages here, so we don't load all
			// message text in advance, instead we use mw.getRemoteMsg() for some.
			//
			// This code is similar to the error handling code formerly in
			// SpecialUpload::processUpload()
			var error_msg_key = {
				'2' : 'largefileserver',
				'3' : 'emptyfile',
				'4' : 'minlength1',
				'5' : 'illegalfilename'
			};

			if ( typeof error_code == 'number'
				&& typeof error_msg_key[ error_code ] == 'undefined' )
			{
				if ( apiRes.upload.code.finalExt ) {
					_this.setPrompt(
						gM( 'mwe-uploaderror' ),
						gM( 'mwe-wgfogg_warning_bad_extension', apiRes.upload.code.finalExt ),
						buttons );
				} else {
					_this.setPrompt(
						gM( 'mwe-uploaderror' ),
						gM( 'mwe-unknown-error' ) + ' : ' + error_code,
						buttons );
				}
				return false;
			}

			// If no "error_code" was provided or it is an unknown-error
			// try to use the errorKey in apiRes.upload.details
			if ( !error_code || error_code == 'unknown-error' ) {
				if ( apiRes.upload.error == 'internal-error' ) {
					// Do a remote message load
					errorKey = apiRes.upload.details[0];
					mw.getRemoteMsg( errorKey, function() {
						_this.setPrompt( gM( 'mwe-uploaderror' ), gM( errorKey ), buttons );
					});
					return false;
				}
				_this.setPrompt(
					gM('mwe-uploaderror'),
					gM('mwe-unknown-error') + '<br>' + error_msg,
					buttons
				);
				return false;
			}

			// This is the ideal error handling,
			// if apiRes consistently provided error.info
			if ( apiRes.error && apiRes.error.info ) {
				_this.setPrompt( gM( 'mwe-uploaderror' ), apiRes.error.info, buttons );
				return false;
			}

			mw.log( 'get remote error key: ' + error_msg_key[ error_code ] )
			mw.getRemoteMsg( error_msg_key[ error_code ], function() {
				_this.setPrompt(
					gM( 'mwe-uploaderror' ),
					gM( error_msg_key[ error_code ], errorReplaceArg ),
					buttons );
			});
			mw.log( "api.error" );
			return false;
		}

		// If nothing above was able to set the error
		// set simple unknown-error
		if ( apiRes.upload && apiRes.upload.error ) {
			mw.log( ' apiRes.upload.error: ' + apiRes.upload.error );
			_this.setPrompt(
				gM( 'mwe-uploaderror' ),
				gM( 'mwe-unknown-error' ) + '<br>',
				buttons );
			return false;
		}

		// Check for warnings:
		if ( apiRes.upload && apiRes.upload.warnings ) {
			var wmsg = '<ul>';
			for ( var wtype in apiRes.upload.warnings ) {
				var winfo = apiRes.upload.warnings[wtype]
				wmsg += '<li>';
				switch ( wtype ) {
					case 'duplicate':
					case 'exists':
						if ( winfo[1] && winfo[1].title && winfo[1].title.mTextform ) {
							wmsg += gM( 'mwe-file-exists-duplicate' ) + ' ' +
								'<b>' + winfo[1].title.mTextform + '</b>';
						} else {
							//misc error (weird that winfo[1] not present
							wmsg += gM( 'mwe-upload-misc-error' ) + ' ' + wtype;
						}
						break;
					case 'file-thumbnail-no':
						wmsg += gM( 'mwe-file-thumbnail-no', winfo );
						break;
					default:
						wmsg += gM( 'mwe-upload-misc-error' ) + ' ' + wtype;
						break;
				}
				wmsg += '</li>';
			}
			wmsg += '</ul>';


			// Create the "ignore warning" button
			var buttons = {};
			buttons[ gM( 'mwe-ignorewarning' ) ] = function() {
				// call the upload object ignore warnings:
				_this.sendUploadAction( 'ignoreWarnings' );
			};
			// Create the "return to form" button
			buttons[ gM( 'mwe-return-to-form' ) ] = function() {
				_this.returnToForm( this );
			}
			// Show warning
			_this.setPrompt(
				gM( 'mwe-uploadwarning' ),
				$j('<div />')
				.append(
					$j( '<h3 />' )
					.text( gM( 'mwe-uploadwarning' ) ),

					$j('<span />')
					.html( wmsg )
				),
				buttons );
			return false;
		}
		// No error!
		return true;
	},
	/**
	 * return to the upload form handler
	 */
	returnToForm: function( dialogElement ){
		$j( dialogElement ).dialog( 'close' );
		//retun to form actions
		this.sendUploadAction( 'returnToForm' );

		// Disable direct submit on the transport ( so send via sendUploadAction )
		this.sendUploadAction( 'disableDirectSubmit' );

		// returnToFormCb
		this.sendUploadAction( 'returnToFormCb' );
	},

	/**
	* Send an upload action to the upload handler.
	* @param {Object} action
	*/
	sendUploadAction: function( action ) {
		this.uploadHandler.uploadHandlerAction( action );
	},

	/**
	* Shows api success from a apiResult
	* @param {Object} apiRes Result object
	*/
	showApiSuccess: function( apiRes ){
		mw.log( " UI:: showApiSuccess: " );
		// set the target resource url:
		var url = apiRes.upload.imageinfo.descriptionurl;
		var _this = this;
		var buttons = {};
		// "Return" button
		buttons[ gM( 'mwe-return-to-form' ) ] = function() {
			$j( this ).dialog( 'destroy' ).remove();
			_this.sendUploadAction( 'returnToForm' );
			_this.sendUploadAction( 'disableDirectSubmit' );
		}
		// "Go to resource" button
		buttons[ gM('mwe-go-to-resource') ] = function() {
			window.location = url;
		};
		_this.action_done = true;
		_this.setPrompt(
			gM( 'mwe-successfulupload' ),
			$j('<a />')
			.attr( 'href', url )
			.html(
				gM( 'mwe-upload-done')
			),
			buttons
		);
		mw.log( 'apiRes.upload.imageinfo::' + url );
	},

	/**
	 * Set the dialog to "done"
	 */
	close: function() {
		this.action_done = true;
		$j( '#upProgressDialog' ).dialog( 'destroy' ).remove();
	},

	/**
	* Get a standard cancel button in the jQuery.ui dialog format
	*/
	getCancelButton: function() {
		var _this = this;
		mw.log( 'f: getCancelButton()' );
		var cancelBtn = [];
		cancelBtn[ gM( 'mwe-cancel' ) ] = function() {
			$j( this ).dialog( 'close' );
		};
		return cancelBtn;
	}
};


/**
 * Iframe Interface ( sends updates to an iframe for remote upload progress events )
 */
mw.UploadIframeUI = function( callbackProxy ) {
	return this.init( callbackProxy );
};
mw.UploadIframeUI.prototype = {
	lastProgressTime : 0,

	/**
	* init
	* @param {Function} callbackProxy Function that reciveds
	* 	all the method calls to be pass along as msgs to the
	* 	other domain via iframe proxy or eventually html5 sendMsg
	*/
	init: function( callbackProxy ){
		var _this = this;
		this.callbackProxy = callbackProxy;
	},

	// Don't call update progress more than once every 3 seconds
	// Since it involves loading a cached iframe. Once we support html5
	// cross domain "sendMsg" then we can pass all updates
	updateProgress: function( fraction ) {
		if( ( new Date() ).getTime() - this.lastProgressTime > 3000 ){
			this.lastProgressTime = ( new Date() ).getTime()
			this.callbackProxy( 'updateProgress', fraction );
		}
	},

	// Pass on the setup call
	setup: function( options ){
		this.callbackProxy( 'setup', options );
	},

	// pass along the close request
	close: function(){
		this.callbackProxy( 'close' );
	},

	// Pass on the "setLoading" action
	setLoading: function( ){
		this.callbackProxy( 'setLoading' );
	},

	// Pass on the show api errror:
	showApiError: function ( apiRes ){
		this.callbackProxy( 'showApiError', apiRes );
	},

	// Pass on the show api success:
	showApiSuccess: function ( apiRes ) {
		this.callbackProxy( 'showApiSuccess', apiRes );
	},

	// Pass on api action
	sendUploadAction: function( action ) {
		this.callbackProxy( 'sendUploadAction', action );
	}

};
