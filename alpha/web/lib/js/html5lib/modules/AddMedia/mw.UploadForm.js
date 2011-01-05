/**
 * Simple form output jquery binding
 * enables dynamic form output to a given target
 */

mw.addMessages( {
	"mwe-select_file" : "Select file",
	"mwe-select_ownwork" : "I am uploading entirely my own work, and licensing it under:",
	"mwe-license_cc-by-sa" : "Creative Commons Share Alike (3.0)",
	"mwe-upload" : "Upload file",
	"mwe-destfilename" : "Destination filename:",

	"mwe-summary" : "Summary",
	"mwe-date-of-work" : "Date of the work",

	"mwe-error_not_loggedin" : "You do not appear to be logged in or do not have upload privileges.",

	"mwe-error-not-loggedin-file" : "You do not appear to be logged in or there was an error in the software. You can $1 and try again",
	"mwe-link-login" : "login",

	"mwe-watch-this-file" : "Watch this file",
	"mwe-ignore-any-warnings" : "Ignore any warnings",

	"mwe-i-would-like-to" : "I would like to",
	"mwe-upload-own-file" : "Upload my own work to $1",
	"mwe-upload-not-my-file" : "Upload media that is not my own work to $1",
	"mwe-upload-once-done" : "Please $1. Once you have completed your upload, $2",
	"mwe-upload-in-new-win-link" : "upload in the new window or tab",
	"mwe-upload-refresh" : "refresh your upload list",

	"mwe-ie-inline-upload" : "Inline uploading is currently disabled for Internet Explorer. You can $1, then $2"
} );

var default_form_options = {
	'enable_fogg'	 : true,
	'license_options': ['cc-by-sa'],
	'apiUrl' : false,
	'doneUploadCb' : null
};
mw.UploadForm = { };

( function( $ ) {
	/**
	* Add a upload target selection menu
	* with binding to build update form target
	*/
	var uploadMenuTarget = null;
	var remoteSearchDriver = null;
	var selectUploadProviderCb = null;

	mw.UploadForm.getUploadMenu = function( options ){
		if( ! options.target ){
			mw.log("Error no target for upload menu" );
			return false;
		}
		// Update local scope pointers:
		uploadMenuTarget = options.target;

		if( options.remoteSearchDriver ) {
			remoteSearchDriver = options.remoteSearchDriver
		}

		if( options.selectUploadProviderCb ){
			selectUploadProviderCb = options.selectUploadProviderCb;
		}

		// Build out the menu
		$j( uploadMenuTarget ).empty().append(
			$j( '<span />' )
			.text(
				gM('mwe-i-would-like-to' )
			),

			$j( '<br />' )
		);



		// Set provider Target
		for( var uploadTargetId in options.uploadTargets ){
			$j( uploadMenuTarget ).append(
				getProviderUploadLinks( uploadTargetId )
			);
		}
	};

	/**
	* Add the Simple Upload From jQuery binding
	*
	* @param {Object} options Set of options for the upload
	* overwriting default values in default_form_options
	*/
	mw.UploadForm.getForm = function( options ) {
		var _this = this;
		// set the options:
		for ( var i in default_form_options ) {
			if ( !options[i] )
				options[i] = default_form_options[i];
		}

		// First do a reality check on the options:
		if ( !options.apiUrl ) {
			$j( options.target ).html( 'Error: Missing api target' );
			return false;
		}

		// Get an edit Token for "uploading"
		mw.getToken( options.apiUrl, 'File:MyRandomFileTokenCheck.jpg', function( eToken ) {
			if ( !eToken || eToken == '+\\' ) {
				$j( options.target ).html( gM( 'mwe-error_not_loggedin' ) );
				return false;
			}
			// Update the options Token
			options.eToken = eToken;

			// Get a user Name for the upload
			mw.getUserName( options.apiUrl, function( userName ) {
				if( !userName ) {
					gM( 'mwe-error-not-loggedin-file',
					 	$j( '<a />' )
					 	.text( gM('mwe-link-login') )
					 	.attr('attr', options.apiUrl.replace( 'api.php', 'index.php' ) + '?title=Special:UserLogin' )
					 )
				}
				// Set the user name:
				options.userName = userName;

				// Add the upload form to the options target:
				addUploadForm( options );

				// By default disable:
				$j( '#wpUploadBtn' ).attr( 'disabled', 'disabled' );

				// Set up basic license binding:
				$j( '#wpLicence' ).click( function( ) {
					if ( $j( this ).is( ':checked' ) ) {
						$j( '#wpUploadBtn' ).removeAttr( 'disabled' );
					} else {
						$j( '#wpUploadBtn' ).attr( 'disabled', 'disabled' );
					}
				} );


				//Set up the bindings
				if( mw.isLocalDomain( options.apiUrl ) ) {
					// Setup Local upload bindings
					setupLocalUploadBindings( options );
				}else{
					// Setup ApiFile bindings
					setupApiFileBrowseProxy(
						options
					);
				}

				// Do remote or local destination check:
				$j( "#wpDestFile" ).change( function( ) {
					$j( "#wpDestFile" ).doDestCheck( {
						'apiUrl' : options.apiUrl,
						'warn_target':'#wpDestFile-warning'
					} );
				} );
			} ); // ( userName )
		}); // ( token )
	}

	/**
	* Setup the local upload bindings
	* ( this is different from the api file proxy bindings that
	* handles the interface bindings within the api file proxy setup.
	*/
	function setupLocalUploadBindings( options ) {

		mw.load( 'AddMedia.firefogg', function( ) {
			$j( "#wpUploadFile" ).firefogg( {
				// An api url (we won't submit directly to action of the form)
				'apiUrl' : options.apiUrl,

				// MediaWiki API supports chunk uploads:
				'enableChunks' : false,
				// We manually rewrite our description text
				'rewriteDescriptionText' : false,

				'form_selector' : '#suf_upload',

				'doneUploadCb' : options.doneUploadCb,

				'selectFileCb' : function( fileName ) {
					// Update our local target:
					$j('#wpDestFile').val( fileName );
					$j( "#wpDestFile" ).doDestCheck( {
						'apiUrl' : options.apiUrl,
						'warn_target' : "#wpDestFile-warning"
					} );
				},

				'returnToFormCb' : function(){
					// Enable upload button and remove loader
					$j( '#wpUploadBtn' )
					.attr( 'disabled', null )
					.parent()
					.find( '.loadingSpinner' )
					.remove();
				},

				'beforeSubmitCb' : function( ) {
					buildAssetDescription( options );
				}
			} );
		});
	}

	/**
	 * Build the Asset Description info template
	 * and update the wpUploadDescription value
	 */
	function buildAssetDescription( options ){
		// Update with basic info template:
		// TODO: it would be nice to have a template generator class
		// this is basically a simple version of the commons form hack
		var desc = $j('#comment-desc').val();
		var date = $j('#comment-date').val();

		if( !options.userName ){
			mw.log( "Error:: buildAssetDescription :: no userName" );
		}

		// Update the template if the user does not already have template code:
		if( desc.indexOf('{{Information') == -1) {
			$j('#wpUploadDescription').val(
				'== {{int:filedesc}} ==' + "\n" +
				'{{Information' + "\n" +
				'|Description={{en|' + desc + "\n}}\n" +
				'|Author=[[User:' + options.userName + '|' + options.userName + ']]' + "\n" +
				'|Source=' + "\n" +
				'|Date=' + date + "\n" +
				'|Permission=' + "\n" +
				'|other_versions=' + "\n" +
				'}}' + "\n" +
				'== {{int:license}} ==' + "\n" +
				'{{self|cc-by-sa-3.0}}' + "\n"
			);
		}
	}
	/**
	* Setup a fileBrowse proxy for a given target
	*/
	function setupApiFileBrowseProxy ( options ) {
		// Set the "firefogg warning"
		// ( note AddMedia.firefogg will be loaded by the same url should be cached )
		mw.load( 'AddMedia.firefogg', function( ) {
			$j( options.target ).find( '.remote-browse-file' ).firefogg( {
				'installCheckMode' : true
			});
		} );
		// Load the apiProxy ( if its not already loaded )
		mw.load( 'ApiProxy', function( ) {
			var fileIframeName = mw.ApiProxy.browseFile( {
				//Target div to put the iframe browser button:
				'target' : 	$j( options.target ).find( '.remote-browse-file' ),

				'token' : options.eToken,

				// Api url to upload to
				'apiUrl' : options.apiUrl,

				// Setup the callback:
				'doneUploadCb' : options.doneUploadCb,

				// File Destination Name change callback:
				'selectFileCb' : function( fileName ) {
					// Update our local target:
					$j('#wpDestFile').val( fileName );
					// Run a destination file name check on the remote target
					$j('#wpDestFile').doDestCheck( {
						'apiUrl' : options.apiUrl,
						'warn_target': '#file-warning'
					} );
				},
				'returnToFormCb' : function(){
					// Enable upload button and remove loader
					$j( '#wpUploadBtn' )
					.attr( 'disabled', null )
					.parent()
					.find( '.loadingSpinner' )
					.remove();
				},
				// Timeout callback
				'timeoutCb' : function(){
					mw.log("timed out in setting up setupApiFileBrowseProxy");
					$targetFileBrowse.html(
						gM( 'mwe-error-not-loggedin-file',
						 	$j( '<a />' )
						 	.text( gM('mwe-link-login') )
						 	.attr('attr', options.apiUrl.replace( 'api.php', 'index.php' ) + '?title=Special:UserLogin' )
						 )
					);
				}
			} );

			// Setup submit binding:
			$j('#wpUploadBtn').click( function(){

				// Update the asset description:
				buildAssetDescription( options );

				// Dissable upload button and add loader:
				$j( '#wpUploadBtn' )
				.attr( 'disabled', 'disabled' )
				.before(
					$j('<span />').loadingSpinner()
				);

				// Setup the form data:
				var formData = {
					'filename' : $j( '#wpDestFile' ).val(),
					'comment' : $j( '#wpUploadDescription' ).val()
				}

				if( $j( '#wpWatchthis' ).is( ':checked' ) ) {
					formData[ 'watch' ] = 'true';
				}
				if( $j('#wpIgnoreWarning' ).is( ':checked' ) ) {
					formData[ 'ignorewarnings' ] = 'true';
				}

				// Build the output and send upload request to fileProxy
				mw.ApiProxy.sendServerMsg( {
					'apiUrl' : options.apiUrl,
					'frameName' : fileIframeName,
					'frameMsg' : {
						'action' : 'fileSubmit',
						'formData' : formData
					}
				} );
			} );

			// Overwide the form submit:
			$j( '#suf_upload' ).submit( function(){
				// Only support form submit via button click
				return false;
			});


		});
	}
	/**
	* Get a provider upload links for local upload and remote
	*/
	function getProviderUploadLinks( uploadTargetId ){
		// Setup local pointers:
		var _this = this
		var uploadProvider = remoteSearchDriver.getUploadTargets()[ uploadTargetId ];
		var searchProvider = remoteSearchDriver.content_providers[ uploadTargetId ];

		var apiUrl = uploadProvider.apiUrl;
		$uploadLinksContainer = $j( '<div />' );

		if( uploadProvider.providerDescription ){
			$uploadLinksContainer.append( $j('<br />'),
				uploadProvider.providerDescription
			);
		}
		var $uploadLinksList = 	$j('<ul />');

		// Upload your own file
		$uploadLinksList.append(
			$j('<li />').append(
				$j( '<a />' )
				.attr( {
					'href' : '#'
				} )
				.text(
					gM( 'mwe-upload-own-file', uploadProvider.title )
				)
				.click( function( ) {
					// Check for IE ( requires p3p policy and requires more porting work. )
					if( $j.browser.msie ) {
						showUploadInTab( uploadTargetId, uploadMenuTarget, "mwe-ie-inline-upload" );
						return false;
					}

					$j( uploadMenuTarget ).empty().loadingSpinner();

					// if selectUploadProviderCb is set run the callback
					if( selectUploadProviderCb ) {
						selectUploadProviderCb( uploadProvider )
					}

					// Do upload form
					mw.UploadForm.getForm( {
						"target" : uploadMenuTarget,
						"apiUrl" : apiUrl,
						"doneUploadCb" : function( resultData ) {
							if( !resultData || ! resultData.upload || ! resultData.upload['filename']){
								mw.log( "Error in upload form no upload data in done Upload callback ");
								return true;
							}
							var wTitle = resultData.upload['filename'];
							mw.log( 'uploadForm: doneUploadCb : '+ wTitle);
							// Add the resource editor interface with loaders:
							remoteSearchDriver.addResourceEditLoader();

							//Add the uploaded result
							searchProvider.sObj.getByTitle( wTitle, function( resource ) {
								// Update the recent uploads ( background task )
								remoteSearchDriver.showUserRecentUploads( uploadTargetId );
								// Pull up resource editor:
								remoteSearchDriver.showResourceEditor( resource );
							} );
							// Return true to close progress window:
							return true;
						}
					} );

				} )
			)
		);

		// Upload a file not your own ( link to special:upload for that api url )
		$uploadLinksList.append (
			$j('<li />').append(
				$j( '<a />' )
				.attr( {
					'href' : '#',
					'target' : '_new'
				} )
				.text(
					gM( 'mwe-upload-not-my-file', uploadProvider.title )
				).click( function ( ) {
					showUploadInTab( uploadTargetId, uploadMenuTarget, "mwe-upload-once-done" );
				} )
			)
		);
		$uploadLinksContainer.append( $uploadLinksList );
		// return the list:
		return $uploadLinksContainer;
	};

	/**
	 * Handles the very similar layout of IE and non-inline upload
	 * @param {String} uploadTargetId Upload Target provider id
	 * @param {String} uploadMenuTarget Menu target
	 * @param {String} msgKey The msgKey to use for the upload in new tab msg text
	 */
	function showUploadInTab(uploadTargetId, uploadMenuTarget, msgKey ){
		var uploadProvider = remoteSearchDriver.getUploadTargets()[ uploadTargetId ];
		//Show refresh link
		$j( uploadMenuTarget ).empty().html(
			gM( msgKey,
				$j('<a />')
				.attr( {
					'href' : uploadProvider.uploadPage,
					'target' : "_new"
				} )
				.text(
					gM("mwe-upload-in-new-win-link")
				),

				$j('<a />')
				.attr( {
					'href' : '#'
				} )
				.addClass('user-upload-refresh')
				.text(
					gM('mwe-upload-refresh')
				)
			)
		);
		// NOTE: if gM supported jquery object a bit better
		// we could bind the link above in the gM call
		$j( uploadMenuTarget ).find( '.user-upload-refresh' )
		.click( function( ) {
			remoteSearchDriver.showUserRecentUploads( uploadTargetId );
			return false;
		} );
	}
	/**
	* Get a jquery built upload form
	*/
	function addUploadForm( options ){

		if( ! options.eToken ){
			mw.log( "Error getUploadForm missing token" );
			return false;
		}

		// Build an upload form:
		$j( options.target ).empty().append(
			$j( '<form />' ).attr( {
				'id' : "suf_upload",
				'name' : "suf_upload",
				'enctype' : "multipart/form-data",
				'action' : options.apiUrl,
				'method' : "post"
			} )
		);

		//Set the uploadForm target
		var $uploadForm = $j( options.target ).find('#suf_upload');

		// Add hidden input
		$uploadForm.append(
			$j( '<input />')
			.attr( {
				'type' : "hidden",
				'name' : "action",
				'value' : "upload"
			}),

			$j( '<input />')
			.attr( {
				'type' : "hidden",
				'name' : "format",
				'value' : "jsonfm"
			}),

			$j( '<input />')
			.attr( {
				'type' : "hidden",
				'id' : "wpEditToken",
				'name' : "token",
				'value' : options.eToken
			})
		)

		// Add upload File input
		$uploadForm.append(
			$j( '<label />' ).attr({
				'for' : "wpUploadFile"
			})
			.text( gM( 'mwe-select_file' ) ),

			$j( '<br />' )
		);

		// Output the upload file button ( check for cross domain )
		if( mw.isLocalDomain( options.apiUrl ) ) {
			$uploadForm.append(
				$j( '<input />')
				.attr( {
					'id' : 'wpUploadFile',
					'type' : "file",
					'name' : "wpUploadFile",
					'size' : "15"
				} )
				.css( 'display', 'inline' ),

				$j( '<br />' )
			);
		} else {
			/**
			* If the upload target is a remote domain
			* add the browse file button as a an iframe
			* to the target api location
			*/
			$uploadForm.append(
				$j( '<div />' )
				.addClass( 'remote-browse-file' )
				.loadingSpinner()
			);
		}

		// Add destination fileName
		$uploadForm.append(
			$j( '<label />' ).attr({
				'for' : "wpDestFile"
			})
			.text( gM( 'mwe-destfilename' ) ),

			$j( '<br />' ),

			$j( '<input />' )
			.attr( {
				'id' : 'wpDestFile',
				'name' : 'wpDestFile',
				'type' : 'text',
				'size' : "25"
			} )
			.css( 'display', 'inline' )
		)


		// Add upload description:
		$uploadForm.append(
			$j( '<br />' ),
			$j( '<label />' )
			.attr({
				'for' : "comment-desc"
			})
			.text( gM( 'mwe-summary' ) ),

			$j( '<br />' ),

			$j( '<textarea />' )
			.attr( {
				'id' : "comment-desc",
				'cols' : "30",
				'rows' : "3",
				'name' : "comment-desc",
				'tabindex' : "3"
			} ),

			$j( '<br />' )
		);
		// Add the hidden wpUploadDescription
		$uploadForm.append(
			$j( '<input />' )
			.attr( {
				'id' : "wpUploadDescription",
				'type' : "hidden",
				'name' : "wpUploadDescription"
			} )
			.val('')
		)

		//Add date of work
		$uploadForm.append(
			$j( '<label />' )
			.attr({
				'for' : "comment-date"
			})
			.text( gM( 'mwe-date-of-work' ) ),

			$j( '<br />' ),

			$j( '<input />' )
			.attr( {
				'id' : "comment-date",
				'size' : 15,
				'name' : "comment-date",
				'tabindex' : "4"
			} )
			.datepicker({
				changeMonth: true,
				changeYear: true,
				verticalOffset: 40,
				dateFormat: 'yy-mm-dd',
				onSelect: function( dateText ) {
					$j( this ).val( dateText );
				},
				beforeShow: function() {
					$j('#ui-datepicker-div').css({
						'z-index': 10001
					});
					return true;
				}
			}),

			$j( '<br />' )
		);




		// Add watchlist checkbox
		$uploadForm.append(
			$j('<input />')
			.attr({
				'type' : 'checkbox',
				'value' : 'true',
				'id' : 'wpWatchthis',
				'name' : 'watch',
				'tabindex' : "5"
			}),

			$j( '<label />' )
			.attr( {
				'for' : "wpWatchthis"
			} )
			.text( gM( 'mwe-watch-this-file' ) )
		);

		// Add ignore warning checkbox:
		$uploadForm.append(
			$j( '<input />' )
			.attr( {
				'type' : "checkbox",
				'value' : "true",
				'id' : "wpIgnoreWarning",
				'name' : "ignorewarnings",
				'tabindex' : "6"
			} ),

			$j( '<label />' )
			.attr({
				'for' : "wpIgnoreWarning"
			})
			.text(
				gM( 'mwe-ignore-any-warnings' )
			),

			$j( '<br />' )
		);

		// Add warning div:
		$uploadForm.append(
			$j( '<div />' )
			.attr({
				'id' : "wpDestFile-warning"
			}),

			$j( '<div />' )
			.css( {
				'clear' : 'both'
			}),

			$j( '<p />' )
		);

		// Add own work text and checkbox:
		$uploadForm.append(
			$j( '<span />')
			.text( gM( 'mwe-select_ownwork' ) ),

			$j( '<br />' ),

			$j( '<input />' )
			.attr( {
				'type' : "checkbox",
				'id' : "wpLicence",
				'name' : "wpLicence",
				'value' : "cc-by-sa"
			}),

			$j( '<span />' )
			.text( gM( 'mwe-license_cc-by-sa' ) ),

			$j( '<p />' )
		);

		// Add the submit button:
		$uploadForm.append(
			$j( '<input />' )
			.attr( {
				'type' : "submit",
				'accesskey' : "s",
				'value' : gM( 'mwe-upload' ),
				'name' : "wpUploadBtn",
				'id' : "wpUploadBtn",
				'tabindex' : "7"
			})
		);

		return $uploadForm;
	};


} )( window.mw.UploadForm );
