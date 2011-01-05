/**
 * Firefogg support.
 * autodetects: new upload api or old http POST.
 */

mw.addMessages({
	"mwe-upload-transcoded-status" : "%1 Transcoded",
	"mwe-upload-transcode-in-progress" : "Transcode and upload in progress (do not close this window)",
	"fogg-transcoding" : "Encoding video",
	"fogg-select_file" : "Select file",
	"fogg-select_new_file" : "Select new file",
	"fogg-select_url" : "Select URL",
	"fogg-check_for_firefogg" : "Checking for Firefogg...",
	"fogg-installed" : "Firefogg is installed,",
	"fogg-not-installed" : "Firefogg is not installed or not enabled.",
	"fogg-for_improved_uploads" : "For improved uploads:",
	"fogg-please-install" : "$1. More $2",
	"fogg-please-install-install-linktext" : "Install Firefogg",
	"fogg-please-install-about-linktext" : "about Firefogg",
	"fogg-use_latest_firefox" : "Please first install $1. <i>Then revisit this page to install the <b>Firefogg<\/b> extension.<\/i>",
	"fogg-latest-firefox" : "latest Firefox",
	"fogg-passthrough_mode" : "Your selected file is already ogg or not a video file",
	"fogg-encoding-done" : "Encoding complete",
	"fogg-badtoken" : "Token is not valid",
	"fogg-preview" : "Preview video",
	"fogg-hidepreview" : "Hide preview",
	"fogg-warning-firebug" : "<b>Firebug</b> can cause conflicts with <i>Firefogg</i>. Please disable <b>Firebug</b> for this page.",
	"fogg-missing-webm-support" : "Please use a [$1 webm compatible] browsers to preview results of webm videos"
});

var firefogg_install_links = {
	'macosx': 'http://firefogg.org/macosx/Firefogg.xpi',
	'win32': 'http://firefogg.org/win32/Firefogg.xpi',
	'linux': 'http://firefogg.org/linux/Firefogg.xpi'
};

var default_firefogg_options = {
	// Callback for upload completion
	'doneUploadCb': false,

	// The API URL to upload to
	'apiUrl': null,

	// True when a file is uploaded without re-encoding
	'passthrough': false,

	// True if we will be showing the encoder interface
	'encoder_interface': false,

	// If the install firefogg should be shown or not.
	'showFoggWarningFlag' : true,

	// jQuery selector identifying the target control container or form (can't be left null)
	'selector': '',

	// May be "upload" to if we are rewriting an upload form, or "local" if we are encoding a local file
	'form_type': 'local',

	// Special Mode that just checks for firefogg install and puts a notice after the target
	'installCheckMode': false,

	// CSS selector for the select file button
	'target_btn_select_file': false,

	// CSS selector for the select new file button
	'target_btn_select_new_file': false,

	// CSS selector for the save local file button
	'target_btn_save_local_file': false,

	// CSS selector for the input file name button
	'target_input_file_name': false,

	// CSS selector for the "checking for firefogg..." message div
	'target_check_for_firefogg': false,

	// CSS selector for the "firefogg is installed" message div
	'target_installed': false,

	// CSS selector for the "please install firefogg" message div
	'target_please_install': false,

	// CSS selector for the "please use Firefox 3.5" message div
	'target_use_latest_firefox': false,

	// CSS selector for the message div warning that passthrough mode is enabled
	'target_passthrough_mode': false,

	// True if firefogg should take over the form submit action
	'firefogg_form_action': true,

	// True if we should show a preview of the encoding progress
	'show_preview': true,

	//If we should enable chunk uploads ( mediaWiki api supports chunk uploads)
	'enableChunks' : false
};

/**
* Setup firefogg jquery binding
* NOTE: we should have the firefogg binding work the same way as
* the upload form binding.
*/
( function( $ ) {
	$.fn.firefogg = function( options ) {
		if ( !options ){
			options = { };
		}

		// Add the selector
		options[ 'selector' ] = this.selector;

		// Setup the Firefogg:
		var myFogg = new mw.Firefogg( options );

		// Kind of silly installCheckMode check
		//  need to refactor as described in init :: installCheckMode
		if ( myFogg && ! myFogg.installCheckMode ) {
			myFogg.doRewrite( );
			var selectorElement = $j( this.selector ).get( 0 );
			selectorElement[ 'uploadHandler' ] = myFogg;
		}
	}
} )( jQuery );


mw.Firefogg = function( options ) {
	return this.init( options );
};
mw.Firefogg.prototype = { // extends mw.BaseUploadHandler
	// Minimum version of firefogg allowed
	min_firefogg_version: '1.2.06',

	// The default encoder settings
	// NOTE: should be mw.getConfig based
	default_encoder_settings: {
		'maxSize'        : '400',
		'videoBitrate'   : '544',
		'audioBitrate'   : '96',
		'noUpscaling'    : true
	},

	// Lazy initialized, use getFirefogg()
	have_firefogg: null,

	// Lazy initialized, use getEncoderSettings()
	current_encoder_settings: null,

	// Lazy initialized, use getSourceFileInfo()
	sourceFileInfo: null,

	// Valid ogg extensions
	ogg_extensions: [ 'ogg', 'ogv', 'oga' ],

	passthrough: false,
	sourceMode: 'file',

	/**
	 * Object initialisation
	 */
	init: function( options ) {
		if ( !options ){
			options = {};
		}

		// If we have no apiUrl, set upload mode to "post"
		if ( !options.apiUrl ){
			options.upload_mode = 'post';
		}

		// Set options
		for ( var i in default_firefogg_options ) {
			if ( typeof options[ i ] != 'undefined' ) {
				this[ i ] = options[i];
			} else {
				this[ i ] = default_firefogg_options[i];
			}
		}
		// Check for special installCheckMode

		// NOTE we should refactor install checks into static functions / entry points
		//  so that they can be called without initializing the firefogg object with a special flag.
		if( this.installCheckMode ){
			if ( ! this.getFirefogg() ) {
				this.form_type = 'upload';
				// Show install firefogg msg
				this.showInstallFirefog();
				return ;
			}
			if( typeof console != 'undefined' && console.firebug ) {
				this.appendFirebugWarning();
			}
			mw.log( "installCheckMode no firefogg init");
			return ;
		}

		// Inherit from mw.BaseUploadHandler
		var myBUI = new mw.UploadHandler( options );

		// Prefix conflicting members with parent_
		for ( var i in myBUI ) {
			if ( this[ i ] ) {
				this[ 'parent_'+ i ] = myBUI[i];
			} else {
				this[ i ] = myBUI[i];
			}
		}

		// Setup ui uploadHandler pointer
		this.ui.uploadHandler = this;

		if ( !this.selector ) {
			mw.log('firefogg: missing selector ');
		}
	},

	/**
	 * Rewrite the upload form, or create our own upload controls for local transcoding.
	 * Called from firefogg() jQuery binding
	 */
	doRewrite: function( callback ) {
		var _this = this;
		mw.log( 'sel len: ' + this.selector + '::' + $j( this.selector ).length +
				' tag:' + $j( this.selector ).get( 0 ).tagName );
		if ( $j( this.selector ).length >= 0 ) {
			if ( $j( this.selector ).get( 0 ).tagName.toLowerCase() == 'input' ) {
				_this.form_type = 'upload';
			}
		}
		if ( this.form_type == 'upload' ) {
			// Initialise existing upload form
			this.setupForm();
		} else {
			// Create our own form controls
			this.createControls();
			this.bindControls();
		}

		if ( callback )
			callback();
	},

	/**
	 * Create controls for local transcoding and add them to the page
	 */
	createControls: function() {
		var _this = this;
		var out = '';
		$j.each( default_firefogg_options, function( target, na ) {
			if ( /^target/.test( target ) ) {
				// Create the control if it doesn't already exist
				if( _this[target] === false ) {
					out += _this.getControlHtml(target) + ' ';
					// Update the target selector
					_this[target] = _this.selector + ' .' + target;
				}
			}
		});
		$j( this.selector ).append( out ).hide();
	},

	/**
	 * Get the HTML for the control with a particular name
	 */
	getControlHtml: function( target ) {
		if ( /^target_btn_/.test( target ) ) {
			// Button
			var msg = gM( target.replace( /^target_btn_/, 'fogg-' ) );
			return '<input style="" ' +
				'class="' + target + '" ' +
				'type="button" ' +
				'value="' + msg + '"/> ';
		} else if ( /^target_input_/.test( target ) ) {
			// Text input
			var msg = gM( target.replace( /^target_input_/, 'fogg-' ) );
			return '<input style="" ' +
				'class="' + target + '" ' +
				'type="text" ' +
				'value="' + msg + '"/> ';
		} else if ( /^target_/.test( target ) ) {
			// Message
			var msg = gM( target.replace( /^target_/, 'fogg-' ) );
			return '<div style="" class="' + target + '" >' + msg + '</div> ';
		} else {
			mw.log( 'Invalid target: ' + target );
			return '';
		}
	},

	/**
	 * Set up events for the controls which were created with createControls()
	 */
	bindControls: function( ) {
		var _this = this;

		// Hide all controls
		var hide_target_list = '';
		var comma = '';
		$j.each( default_firefogg_options, function( target, na ) {
			if ( /^target/.test( target ) ) {
				hide_target_list += comma + _this[target];
				comma = ',';
			}
		});
		$j( hide_target_list ).hide();

		// Now show the form
		$j( _this.selector ).show();
		if ( _this.getFirefogg() ) {
			// Firefogg enabled
			// If we're in upload mode, show the input filename
			if ( _this.form_type == 'upload' )
				$j( _this.target_input_file_name ).show();

			// Show the select file button
			$j( this.target_btn_select_file )
				.unbind()
				.attr( 'disabled', false )
				.css( { 'display': 'inline' } )
				.click( function() {
					_this.selectSourceFile();
				} );

			// Set up the click handler for the filename box
			$j( this.target_input_file_name )
				.unbind()
				.attr( 'readonly', 'readonly' )
				.click( function() {
					_this.selectSourceFile();
				});
		}


		// Set up the click handler for the "save local file" button
		if( _this.target_btn_save_local_file ) {
			$j( _this.target_btn_save_local_file )
			.unbind()
			.click( function() {
				_this.doLocalEncodeAndSave();
			} );
		}
	},

	/**
	* Show the install firefogg msg
	*/
	showInstallFirefog: function( target ) {
		var _this = this;

		if( target ){
			this.target_use_latest_firefox = target;
			this.target_please_install = target;
		}

		var upMsg = ( _this.form_type == 'upload' ) ?
			gM( 'fogg-for_improved_uploads' ) + ' ' : gM( 'fogg-not-installed') + ' ';

		// Show the "use latest Firefox" message if necessary
		mw.log( 'mw.Firefogg:: browser: ' + mw.versionIsAtLeast( '1.9.1', $j.browser.version ) );
		if ( !( $j.browser.mozilla && mw.versionIsAtLeast( '1.9.1', $j.browser.version ) ) ) {
			mw.log( 'mw.Firefogg::show use latest::' + _this.target_use_latest_firefox );

			// Add the use_latest if not present:
			if ( !this.target_use_latest_firefox ) {
				$j( this.selector ).after(
					$j( '<div />' )
					.addClass( 'target_use_latest_firefox' )
					.html(
						gM('fogg-use_latest_firefox',
							$j('<a />')
							.attr({
								'href' : 'http://www.mozilla.com/firefox/?from=firefogg',
								'target' : "_new"
							})
							.text(
								gM( 'fogg-latest-firefox' )
							)
						)
					)
				);
				this.target_use_latest_firefox = this.selector + ' ~ .target_use_latest_firefox';
			}

			// Add the upload msg if we are "uploading"
			if ( _this.form_type == 'upload' ) {
				$j( _this.target_use_latest_firefox )
				.prepend( upMsg );
			}

			$j( _this.target_use_latest_firefox ).show();
			return ;
		}
		mw.log( 'mw.Firefogg::should show install link');

		// Otherwise show the "install Firefogg" message
		var firefoggUrl = _this.getFirefoggInstallUrl();
		if( firefoggUrl ) {
			// Add the target please install in not present:
			if ( !this.target_please_install ) {
				$j( this.selector ).after(
					$j('<div />')
					.addClass( 'ui-corner-all target_please_install' )
					.css({
						'border' : 'thin solid black',
						'margin' : '4px'
					})
				);
				this.target_please_install = this.selector + ' ~ .target_please_install';
			}
			// Add the install msg
			$j( _this.target_please_install )
				.append(
					upMsg,
					gM( 'fogg-please-install',
						// Install link
						$j('<a />')
						.text( gM( "fogg-please-install-install-linktext" ) )
						.attr('href', firefoggUrl ),

						// About link
						$j('<a />')
						.text( gM( "fogg-please-install-about-linktext" ) )
						.attr({
							'href' : 'http://commons.wikimedia.org/wiki/Commons:Firefogg',
							'target' : '_new'
						} )
					)
				)
				.css( 'padding', '10px' )
				.show();
		}
	},

	/*
	 * Get the URL for installing firefogg on the client OS
	 */
	getFirefoggInstallUrl: function() {
		var os_link = false;
		if ( navigator.oscpu ) {
			if ( navigator.oscpu.search( 'Linux' ) >= 0 )
				os_link = firefogg_install_links['linux'];
			else if ( navigator.oscpu.search( 'Mac' ) >= 0 )
				os_link = firefogg_install_links['macosx'];
			else if (navigator.oscpu.search( 'Win' ) >= 0 )
				os_link = firefogg_install_links['win32'];
		}
		return os_link;
	},

	/**
	 * Get the Firefogg instance (or false if firefogg is unavailable)
	 */
	getFirefogg: function() {
		if ( this.have_firefogg == null ) {
			if ( typeof( Firefogg ) != 'undefined'
				&& mw.versionIsAtLeast(this.min_firefogg_version, Firefogg().version ) )
			{
				this.have_firefogg = true;
				this.fogg = new Firefogg();
			} else {
				this.have_firefogg = false;
				this.fogg = false;
			}
		}
		return this.fogg;
	},

	/**
	 * Set up the upload form
	 */
	setupForm: function() {
		mw.log( 'firefogg::setupForm::' );
		var _this = this;
		// Set up the parent if we are in upload mode
		if ( this.form_type == 'upload' ) {
			this.parent_setupForm();
		}

		// If Firefogg is not available, just show a "please install" message
		if ( ! _this.getFirefogg() ) {
			// Show install firefogg msg
			if( _this.showFoggWarningFlag ){
				this.showInstallFirefog();
			}
			return ;
		}

		// If uploading and firefogg is on show warning
		if ( this.form_type == 'upload'
			&&	typeof console != 'undefined'
			&& console.firebug && _this.showFoggWarningFlag ) {
			this.appendFirebugWarning();
		}

		// Change the file browser to type text. We can't simply change the attribute so
		// we have to delete and recreate.
		var inputTag = '<input ';
		$j.each( $j( this.selector ).get( 0 ).attributes, function( i, attr ) {
			var val = attr.value;
			if ( attr.name == 'type' )
				val = 'text';
			inputTag += attr.name + '="' + val + '" ';
		} );
		if ( !$j( this.selector ).attr( 'style' ) )
			inputTag += 'style="display:inline" ';

		var id = $j( this.selector ).attr( 'name' ) + '_firefogg-control';
		inputTag += '/><span id="' + id + '"></span>';

		mw.log( 'set input: ' + inputTag );

		$j( this.selector ).replaceWith( inputTag );

		this.target_input_file_name = 'input[name=' + $j( this.selector ).attr( 'name' ) + ']';

		// Point the selector at the span we just created
		this.selector = '#' + id;

		// Create controls for local transcoding
		this.createControls();
		this.bindControls();
	},
	appendFirebugWarning : function(){
		$j( this.selector ).after(
			$j( '<div />' )
			.addClass( 'ui-state-error ui-corner-all' )
			.html( gM( 'fogg-warning-firebug' ) )
			.css({
				'width' : 'auto',
				'margin' : '5px',
				'padding' : '5px'
			})
		);
	},

	/**
	 * Create controls for showing a transcode preview
	 */
	createPreviewControls: function() {
		var _this = this;

		// Set the initial button html:
		var buttonHtml = '';
		if( _this.show_preview == true ) {
			buttonHtml = $j.btnHtml( gM( 'fogg-hidepreview' ), 'fogg_preview', 'triangle-1-s' );
		} else {
			buttonHtml = $j.btnHtml( gM( 'fogg-preview' ), 'fogg_preview', 'triangle-1-e' );
		}

		// Add the preview button and canvas
		$j( '#upProgressDialog' ).append(
			$j('<div />')
			.css({
				"clear" : 'both',
				'height' : '3em'
			}),
			buttonHtml,
			$j('<div />')
			.css({
				"padding" : '10px'
			})
			.append(
				$j( '<canvas />' )
				.css('margin', 'auto' )
				.attr( 'id', 'fogg_preview_canvas')
			)
		);

		// Set the initial state
		if ( _this.show_preview == true ) {
			$j( '#fogg_preview_canvas' ).show();
		}

		// Bind the preview button
		$j( '#upProgressDialog .fogg_preview' ).buttonHover().click( function() {
			return _this.onPreviewClick( this );
		});
	},

	/**
	 * onclick handler for the hide/show preview button
	 */
	onPreviewClick: function( sourceNode ) {
		var button = $j( sourceNode );
		var icon = button.children( '.ui-icon' );
		mw.log( "click .fogg_preview" + icon.attr( 'class' ) );

		if ( icon.hasClass( 'ui-icon-triangle-1-e' ) ) {
			// Show preview
			// Toggle button class and set button text to "hide".
			this.show_preview = true;
			icon.removeClass( 'ui-icon-triangle-1-e' ).addClass( 'ui-icon-triangle-1-s' );
			button.children( '.btnText' ).text( gM( 'fogg-hidepreview' ) );
			$j( '#fogg_preview_canvas' ).show( 'fast' );
		} else {
			// Hide preview
			// Toggle button class and set button text to "show".
			this.show_preview = false;
			icon.removeClass( 'ui-icon-triangle-1-s' ).addClass( 'ui-icon-triangle-1-e' );
			button.children( '.btnText' ).text( gM( 'fogg-preview' ) );
			$j( '#fogg_preview_canvas' ).hide( 'slow' );
		}
		// Don't follow the # link
		return false;
	},

	/**
	 * Render the preview frame (asynchronously)
	 */
	renderPreview: function() {
		var _this = this;
		// Set up the hidden video to pull frames from
		if( $j( '#fogg_preview_vid' ).length == 0 )
			$j( 'body' ).append(
				$j('<video />')
				.attr( 'id', "fogg_preview_vid")
				.css( "display", 'none' )
			);
		var v = $j( '#fogg_preview_vid' ).get( 0 );

		function seekToEnd() {
			var v = $j( '#fogg_preview_vid' ).get( 0 );
			if( v ) {
				// Seek to near the end of the clip ( arbitrary -.4 seconds from end )
				v.currentTime = v.duration - 0.4;
			}
		}
		function renderFrame() {
			var v = $j( '#fogg_preview_vid' ).get( 0 );
			var canvas = $j( '#fogg_preview_canvas' ).get( 0 );
			if ( canvas ) {
				canvas.width = 160;
				canvas.height = canvas.width * v.videoHeight / v.videoWidth;
				var ctx = canvas.getContext( "2d" );
				ctx.drawImage( v, 0, 0, canvas.width, canvas.height );
			}
		}
		function preview() {
			// Initialize the video if it is not set up already
			var v = $j( '#fogg_preview_vid' ).get( 0 );
			if ( v.src != _this.fogg.previewUrl ) {
				mw.log( 'init preview with url:' + _this.fogg.previewUrl );
				v.src = _this.fogg.previewUrl;
				// Once it's loaded, seek to the end ( for ogg )
				v.removeEventListener( "loadedmetadata", seekToEnd, true );
				v.addEventListener( "loadedmetadata", seekToEnd, true );

				// When the seek is done, render a frame
				v.removeEventListener( "seeked", renderFrame, true );
				v.addEventListener( "seeked", renderFrame, true );

				// Refresh the video duration and metadata
				var previewTimer = setInterval( function() {
					if ( _this.fogg.status() != "encoding" ) {
						clearInterval( previewTimer );
						_this.show_preview == false;
					}
					if ( _this.show_preview == true ) {
						v.load();
					}
				}, 1000 );
			}
		}
		preview();
	},

	/**
	 * Get the DOMNode of the form element we are rewriting.
	 * Returns false if it can't be found.
	 * Overrides mw.BaseUploadHandler.getForm().
	 */
	getForm: function() {
		if ( this.form_selector ) {
			return this.parent_getForm();
		} else {
			// No configured form selector
			// Use the first form descendant of the current container
			return $j( this.selector ).parents( 'form:first' ).get( 0 );
		}
	},

	/**
	 * Show a dialog box allowing the user to select the source file of the
	 * encode/upload operation. The filename is stored by Firefogg until the
	 * next encode/upload call.
	 *
	 * After a successful select, the UI is updated accordingly.
	 */
	selectSourceFile: function() {
		var _this = this;
		if( !_this.fogg.selectVideo() ) {
			// User clicked "cancel"
			return;
		}
		_this.clearSourceInfoCache();
		_this.updateSourceFileUI();
	},

	/**
	 * Update the UI due to the source file changing
	 */
	updateSourceFileUI: function() {
		mw.log( 'videoSelectReady' );
		var _this = this;
		if ( !_this.fogg.sourceInfo || !_this.fogg.sourceFilename ) {
			// Something wrong with the source file?
			mw.log( 'selectSourceFile: sourceInfo/sourceFilename missing' );
			return;
		}

		// Hide the "select file" button and show "select new"
		$j( _this.target_btn_select_file ).hide();
		$j( _this.target_btn_select_new_file)
			.show()
			.unbind()
			.click( function() {
				_this.fogg = new Firefogg();
				_this.selectSourceFile();
			} );

		var settings = this.getEncoderSettings();

		// If we're in passthrough mode, update the interface (if not a form)
		if ( settings['passthrough'] == true && _this.form_type == 'local' ) {
			$j( _this.target_passthrough_mode ).show();
		} else {
			$j( _this.target_passthrough_mode ).hide();
			// Show the "save file" button if this is a local form
			if ( _this.form_type == 'local' ) {
				$j( _this.target_btn_save_local_file ).show();
			} // else the upload will be done on form submit
		}

		// Update the input file name box and show it
		mw.log( " should update: " + _this.target_input_file_name +
				' to: ' + _this.fogg.sourceFilename );

		$j( _this.target_input_file_name )
			.val( _this.fogg.sourceFilename )
			.show();


		// Notify callback selectFileCb
		if ( _this.selectFileCb ) {
			if ( settings['passthrough'] ) {
				var fName = _this.fogg.sourceFilename;
			} else {
				var oggExt = _this.isSourceAudio() ? 'oga' : 'ogg';
				oggExt = _this.isSourceVideo() ? 'ogv' : oggExt;
				oggExt = _this.isUnknown() ? 'ogg' : oggExt;
				oggName = _this.fogg.sourceFilename.substr( 0,
					_this.fogg.sourceFilename.lastIndexOf( '.' ) );
				var fName = oggName + '.' + oggExt;
			}
			_this.selectFileCb( fName );
		}
	},

	/**
	 * Get the source file info for the current file selected into this.fogg
	 */
	getSourceFileInfo: function() {
		if ( this.sourceFileInfo == null ) {
			if ( !this.fogg.sourceInfo ) {
				mw.log( 'No firefogg source info is available' );
				return false;
			}
			try {
				this.sourceFileInfo = JSON.parse( this.fogg.sourceInfo );
			} catch ( e ) {
				mw.log( 'error could not parse fogg sourceInfo' );
				return false;
			}
		}
		return this.sourceFileInfo;
	},

	/**
	 * Clear the cache of the source file info, presumably due to a new file
	 * being selected into this.fogg
	 */
	clearSourceInfoCache: function() {
		this.sourceFileInfo = null;
		this.current_encoder_settings = null;
	},

	/**
	 * Save the result of the transcode as a local file
	 */
	doLocalEncodeAndSave: function() {
		var _this = this;
		if ( !this.fogg ) {
			mw.log( 'Error: doLocalEncodeAndSave: no Firefogg object!' );
			return false;
		}
		// Setup the interface progress indicator:
		_this.ui.setup( {
			'title' : gM( 'fogg-transcoding' ),
			'statusType' : 'transcode'
		} );

		// Add the preview controls if transcoding:
		if ( !_this.getEncoderSettings()[ 'passthrough' ] && _this.current_encoder_settings['videoCodec'] != 'vp8' ) {
			_this.createPreviewControls();
		}

		// Set up the target location
		// Firefogg shows the "save as" dialog box, and sets the path chosen as
		// the destination for a later encode() call.
		if ( !this.fogg.saveVideoAs() ) {
			_this.ui.close();
			// User clicked "cancel"
			return false;
		}

		// We have a source file, now do the encode
		this.doEncode(
			function /* onProgress */ ( progress ) {
				_this.ui.updateProgress( progress );
			},
			function /* onDone */ () {
				mw.log( "done with encoding (no upload) " );
				_this.onLocalEncodeDone();
			}
		);
	},

	/**
	 * This is called when a local encode operation has completed. It updates the UI.
	 */
	onLocalEncodeDone: function() {
		var _this = this;
		var videoEmbedCode = '<video controls="true" style="margin:auto" id="fogg_final_vid" '+
			'src="' +_this.fogg.previewUrl + '"></video>';

		if( this.current_encoder_settings['videoCodec'] == 'vp8' ) {
			var dummyvid = document.createElement( "video" );
			if( !dummyvid.canPlayType('video/webm; codecs="vp8, vorbis"') ) {
				videoEmbedCode = gM('fogg-missing-webm-support',
					$j('<a />')
					.attr({
						'href' : 'http://www.webmproject.org/users/',
						'target' : '_new'
					})
				)
			}
		}
		_this.ui.setPrompt( gM( 'fogg-encoding-done' ),
			$j( '<div />' ).append(
				gM( 'fogg-encoding-done' ),
				$j('<br>' ),
				videoEmbedCode
			)
		);
		//Load the video and set a callback:
		var v = $j( '#fogg_final_vid' ).get( 0 );
		if( v ) {
			function resizeVid() {
				var v = $j( '#fogg_final_vid' ).get(0);
				if ( v.videoWidth > 720 ) {
					var vW = 720;
					var vH = 720 * v.videoHeight / v.videoWidth;
				} else {
					var vW = v.videoWidth;
					var vH = v.videoHeight;
				}
				//reize the video:
				$j( v ).css({
					'width': vW,
					'height': vH
				});
				//if large video resize the dialog box:
				if( vW + 5 > 400 ) {
					//also resize the dialog box
					$j( '#upProgressDialog' ).dialog( 'option', 'width', vW + 20 );
					$j( '#upProgressDialog' ).dialog( 'option', 'height', vH + 120 );

					//also position the dialog container
					$j( '#upProgressDialog') .dialog( 'option', 'position', 'center' );
				}
			}
			v.removeEventListener( "loadedmetadata", resizeVid, true );
			v.addEventListener( "loadedmetadata", resizeVid, true );
			v.load();
		}
	},

	/**
	 * Get the appropriate encoder settings for the current Firefogg object,
	 * into which a video has already been selected.
	 */
	getEncoderSettings: function() {
		// set the current_encoder_settings form the default_encoder_settings if not yet set
		if ( this.current_encoder_settings == null ) {

			// Clone the default settings
			var settings = $j.extend( { }, this.default_encoder_settings) ;

			// Grab the extension
			var sf = this.fogg.sourceFilename;
			if ( !sf ) {
				mw.log( 'getEncoderSettings(): No Firefogg source filename is available!' );
				return false;
			}
			var ext = '';
			if ( sf.lastIndexOf('.') != -1 )
				ext = sf.substring( sf.lastIndexOf( '.' ) + 1 ).toLowerCase();

			// Determine passthrough mode
			if ( this.isOggFormat() ) {
				// Already Ogg, no need to encode
				settings['passthrough'] = true;
			} else if ( this.isSourceAudio() || this.isSourceVideo() ) {
				// OK to encode
				settings['passthrough'] = false;
			} else {
				// Not audio or video, can't encode
				settings['passthrough'] = true;
			}

			mw.log( 'base setupAutoEncoder::' + this.getSourceFileInfo().contentType +
				' passthrough:' + settings['passthrough'] );

			this.current_encoder_settings = settings;
		}

		// Remove maxSize if width or height is set:
		if( ( this.current_encoder_settings['width'] || this.current_encoder_settings['height'] )
			&& this.current_encoder_settings['maxSize'] ){
			delete this.current_encoder_settings['maxSize'];
		}

		// Update the format based on codec selection
		if( this.current_encoder_settings['videoCodec'] == 'vp8' ){
			this.fogg.setFormat('webm');
		} else {
			this.fogg.setFormat('ogg');
		}

		return this.current_encoder_settings;
	},

	isUnknown: function() {
		return ( this.getSourceFileInfo().contentType.indexOf("unknown") != -1 );
	},

	isSourceAudio: function() {
		return ( this.getSourceFileInfo().contentType.indexOf("audio/") != -1 );
	},

	isSourceVideo: function() {
		return ( this.getSourceFileInfo().contentType.indexOf("video/") != -1 );
	},

	isOggFormat: function() {
		var contentType = this.getSourceFileInfo().contentType;
		return ( contentType.indexOf("video/ogg") != -1
			|| contentType.indexOf("application/ogg") != -1 );
	},

	/**
	 * Get the default title of the progress window
	 */
	getProgressTitle: function() {
		mw.log( "fogg:getProgressTitle f:" + ( this.getFirefogg() ? 'on' : 'off' ) +
			' mode:' + this.form_type );
		// Return the parent's title if we don't have Firefogg turned on
		if ( !this.getFirefogg() || !this.firefogg_form_action ) {
			return this.parent_getProgressTitle();
		} else if ( this.form_type == 'local' ) {
			return gM( 'fogg-transcoding' );
		} else if ( _this.getEncoderSettings()['passthrough'] ) {
			return gM( 'mwe-upload-in-progress' );
		} else {
			return gM( 'mwe-upload-transcode-in-progress' );
		}
	},

	/**
	 * Do an upload, with the mode given by this.upload_mode
	 * NOTE: should probably be dispatched from BaseUploadHandler doUpload instead
	 */
	doUpload: function() {
		var _this = this;

 		_this.uploadBeginTime = (new Date()).getTime();
		// If Firefogg is disabled or doing an copyByUrl upload, just invoke the parent method
		if( !this.getFirefogg() || this.isCopyUpload() ) {
			_this.parent_doUpload();
			return ;
		}
		// We are doing a firefogg upload:
		mw.log( "firefogg: doUpload:: " );

		// Add the preview controls if transcoding:
		if ( !_this.getEncoderSettings()['passthrough'] ) {
			// Setup the firefogg transcode dialog (if not passthrough )
			_this.ui.setup( {
				'title' : gM( 'mwe-upload-transcode-in-progress' ),
				'statusType' : 'transcode'
			} );

			// setup preview controls:
			_this.createPreviewControls();
		}
		// Update the formData 'comment' per the upload description
		$j(this.form).find("[name='comment']").val( _this.getUploadDescription() );

		// Get the input form data into an array
		mw.log( 'get this.formData ::' );
		var data = $j( this.form ).serializeArray();
		this.formData = {};
		for ( var i = 0; i < data.length; i++ ) {
			if ( data[i]['name'] ) {
				// Special case of upload.js commons hack:
				if( data[i]['name'] == 'wpUploadDescription' ) {
					this.formData[ 'comment' ] = data[i]['value'];
				}else{
					this.formData[ data[i]['name'] ] = data[i]['value'];
				}
			}
		}



		// Get the edit token from formData if it's not set already
		if ( !_this.editToken && _this.formData['token'] ) {
			_this.editToken = _this.formData['token'];
		}

		if( _this.editToken ) {
			mw.log( 'we already have an edit token: ' + _this.editToken );
			_this.doUploadWithFormData();
			return;
		}

		// No edit token. Fetch it asynchronously and then do the upload.
		mw.getToken( _this.apiUrl, 'File:'+ _this.formData['filename'], function( editToken ) {
			if( !editToken || editToken == '+\\' ) {
				_this.ui.setPrompt( gM( 'fogg-badtoken' ), gM( 'fogg-badtoken' ) );
				return false;
			}
			_this.editToken = editToken;
			_this.doUploadWithFormData();
		} );

	},

	/**
	 * Internal function called once the token and form data is available
	 */
	doUploadWithFormData: function() {
		var _this = this;
		// We can do a chunk upload
		if( _this.upload_mode == 'post' && _this.enableChunks ) {
			_this.doChunkUpload();
		} else if ( _this.upload_mode == 'post' ) {
			// Encode and then do a post upload
			_this.doEncode(
				function /* onProgress */ ( progress ) {				
					_this.ui.updateProgress( progress );
				},
				function /* onDone */ () {
					var uploadRequest = _this.getUploadApiRequest();

					// Update the UI for uploading
					_this.ui.setup( {
						'title' : gM( 'mwe-upload-in-progress' ),
						'statusType' : 'upload'
					} );

					mw.log( 'Do POST upload to:' +_this.apiUrl + ' with data:\n' + JSON.stringify( uploadRequest ) );

					_this.fogg.post( _this.apiUrl, 'file', JSON.stringify( uploadRequest ) );

					_this.doUploadStatus();
				}
			);
		} else {
			mw.log( 'Error: unrecongized upload mode: ' + _this.upload_mode );
		}
	},

	/**
	 * Do both uploading and encoding at the same time. Uploads 1MB chunks as
	 * they become ready.
	 */
	doChunkUpload : function() {
		mw.log( 'firefogg::doChunkUpload' );
		var _this = this;
		_this.action_done = false;

		if ( !_this.getEncoderSettings()['passthrough'] ) {
			// We are transcoding to Ogg. Fix the destination extension, it
			// must be ogg/ogv/oga.
			var fileName = _this.formData['filename'];
			var ext = '';
			var dotPos = fileName.lastIndexOf( '.' );
			if ( dotPos != -1 ) {
				ext = fileName.substring( dotPos ).toLowerCase();
			}
			if ( $j.inArray( ext.substr( 1 ), _this.ogg_extensions ) == -1 ) {
				var extreg = new RegExp( ext + '$', 'i' );
				_this.formData['filename'] = fileName.replace( extreg, '.ogg' );
			}
		}
		_this.doChunkUploadWithFormData();
	},

	/**
	 * Get the uplaod api request object from _this.formData
	 *
	 * @param {Object} options Options
	 */
	getUploadApiRequest: function( options ) {
		var _this = this;
		var request = {
			'action': 'upload',
			'format': 'json',
			'filename': _this.formData['filename'],
			'comment': _this.formData['comment']
		};
		if( options && options.enableChunks == true ) {
			request[ 'enablechunks' ] = 'true';
		}

		if ( _this.editToken ) {
			request['token'] = this.editToken;
		}

		if ( _this.formData['watch'] ) {
			request['watch'] = _this.formData['watch'];
		}

		if ( _this.formData['ignorewarnings'] ) {
			request['ignorewarnings'] = _this.formData['ignorewarnings'];
		}

		return request;
	},

	/**
	 * Internal function called from doChunkUpload() when form data is available
	 */
	doChunkUploadWithFormData: function() {
		var _this = this;
		mw.log( "firefogg::doChunkUploadWithFormData: " + _this.editToken );
		// get the upload api request;
		var uploadRequest = this.getUploadApiRequest( { 'enableChunks' : true } );

		var encoderSettings = this.getEncoderSettings();
		mw.log( 'do fogg upload/encode call: ' + _this.apiUrl + ' :: ' + JSON.stringify( uploadRequest ) );
		mw.log( 'foggEncode: ' + JSON.stringify( encoderSettings ) );
		_this.fogg.upload( JSON.stringify( encoderSettings ), _this.apiUrl,
			JSON.stringify( uploadRequest ) );

		// Start polling the upload status
		_this.doUploadStatus();
	},

	/**
	 * Encode the video and monitor progress.
	 *
	 * Calls progressCallback() regularly with a number between 0 and 1 indicating progress.
	 * Calls doneCallback() when the encode is finished.
	 *
	 * Asynchronous, returns immediately.
	 */
	doEncode: function( progressCallback, doneCallback ) {
		var _this = this;
		_this.action_done = false;

		var encoderSettings = this.getEncoderSettings();

		// Check for special encode settings that remap things.

		// Check if encoderSettings passthrough is on ( then skip the encode )
		if( encoderSettings['passthrough'] == true) {
			// Firefogg requires an encode request to setup a the file to be uploaded.
			_this.fogg.encode( JSON.stringify( { 'passthrough' : true } ) );
			doneCallback();
			return ;
		}

		mw.log( 'doEncode: with: ' + JSON.stringify( encoderSettings ) );
		_this.fogg.encode( JSON.stringify( encoderSettings ) );


		// Setup a local function for timed callback:
		var encodingStatus = function() {
			var status = _this.fogg.status();

			if ( _this.show_preview == true
				&& _this.fogg.state == 'encoding'
				// No way to seek in VP8 atm
				&& _this.current_encoder_settings['videoCodec'] != 'vp8') {
				_this.renderPreview();
			}

			// Update progress
			progressCallback( _this.fogg.progress() );

			// Loop to get new status if still encoding
			if ( _this.fogg.state == 'encoding' ) {
				setTimeout( encodingStatus, 500 );
			} else if ( _this.fogg.state == 'encoding done' ) {
				_this.action_done = true;
				progressCallback( 1 );
				doneCallback();
			} else if ( _this.fogg.state == 'encoding fail' ) {
				// TODO error handling:
				mw.log( 'encoding failed' );
			}
		};
		encodingStatus();
	},

	/**
	 * Poll the upload progress and update the UI regularly until the upload is complete.
	 *
	 * Asynchronous, returns immediately.
	 */
	doUploadStatus: function() {
		var _this = this;
		$j( '#up-status-state' ).html( 
				gM( 'mwe-uploaded-status', 0 ) 
		);

		_this.oldResponseText = '';

		// Create a local function for timed callback
		var uploadStatus = function() {
			var response_text = _this.fogg.responseText;
			if ( !response_text ) {
				try {
					var pstatus = JSON.parse( _this.fogg.uploadstatus() );
					response_text = pstatus["responseText"];
				} catch( e ) {
					mw.log( "could not parse uploadstatus / could not get responseText: " + e );
				}
			}

			// Check response is not null and old response does not match current
			if ( typeof response_text != 'undefined' && _this.oldResponseText != response_text ) {
				mw.log( 'Fogg: new result text:' + response_text + ' state:' + _this.fogg.state );
				_this.oldResponseText = response_text;
				// Parse the response text and check for errors
				try {
					var apiResult = JSON.parse( response_text );
				} catch( e ) {
					mw.log( "could not parse response_text::" + response_text +
						' ...for now try with eval...' );
					try {
						var apiResult = eval( response_text );
					} catch( e ) {
						var apiResult = null;
					}
				}

				// Process the api result ( if not a chunk )
				if( ! apiResult.resultUrl ){
					_this.processApiResult ( apiResult );
					return true;
				}

			}
			// Show the video preview if encoding and show_preview is enabled.
			if ( _this.show_preview == true && _this.fogg.state == 'encoding') {
				_this.renderPreview();
			}

			//mw.log( 'Update fogg progress: ' + _this.fogg.progress() );
			// If not an error, Update the progress bar
			_this.ui.updateProgress( _this.fogg.progress() );

			// If we're still uploading or encoding, continue to poll the status
			if ( _this.fogg.state == 'encoding' || _this.fogg.state == 'uploading' ) {
				setTimeout( uploadStatus, 100 );
				return true;
			}

			// Upload done?
			if ( -1 == $j.inArray( _this.fogg.state, [ 'upload done', 'done', 'encoding done' ] ) ) {
				mw.log( 'Error:firefogg upload error: ' + _this.fogg.state + ' \nresponse text: ' +response_text );
				return ;
			}
			// Chunk upload mode:
			if ( apiResult && apiResult.resultUrl ) {
				this.action_done = true;
				// Call the callback
				if ( typeof _this.doneUploadCb == 'function' ) {
					_this.doneUploadCb( apiRes )
					// Close the ui
					_this.ui.close();
					return true;
				}
				// Else pass off the api Success to interface:
				_this.ui.showApiSuccess( apiResult );
				return true;
			} else {
				// Done state with error?
				// Should not be possible because firefogg would not be "done" without resultURL
				mw.log( " Upload done in chunks mode, but no resultUrl!" );
			}

		}
		uploadStatus();
	},

	/**
	 * This is the cancel button handler, referenced by getCancelButton() in the parent.
	 * @param {Element} dialogElement Dialog element that was "canceled"
	 */
	onCancel: function( dialogElement ) {
		if ( !this.getFirefogg() ) {
			return this.parent_cancel_action();
		}
		mw.log( 'firefogg:cancel' )
		if ( confirm( gM( 'mwe-cancel-confim' ) ) ) {
			this.action_done = true;
			this.fogg.cancel();
			$j( dialogElement ).empty().dialog( 'close' );
		}
		// Don't follow the # link:
		return false;
	}
};
