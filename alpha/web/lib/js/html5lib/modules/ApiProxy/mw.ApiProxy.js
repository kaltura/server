/**
*
* Api proxy system
*
* Supports cross domain uploading, and api actions for a approved set of domains.
*
* All cross domain communication is done with iframe children ( Avoids polling
* which is resource intensive and can lose messages )
*
* The framework ~will eventually~ support a request approval system for per-user domain approval
* and a central blacklisting of domains controlled by the site
*
* NOTE: If the browser supports it we should pass msgs with postMessage API
* http://ejohn.org/blog/cross-window-messaging/ ( rather than using these iframes )
*
* NOTE: refactor efforts will include separating out "core" proxy functions and
* having a separate class for "server" and "client" api usage
*
*/

mw.includeAllModuleMessages();

/**
 * apiProxy jQuery binding
 *
 * Note: probably should split up "server" and "client" binding
 *
 * @param {String} mode Mode is either 'server' or 'client'
 * @param {Object} proxyConfig Proxy configuration
 * @param {Function} callback The function called once proxy request is done
 */

 /**
 * Set the base API Proxy object
 *
 */
mw.ApiProxy = { };

// ApiProxy scoped functions:
( function( $ ) {

	// Proxy Context stores an array of proxy context requests
	// this enables multiple simultaneous requests to be processed.
	var proxyContext = [];

	/**
	* Takes a requestQuery, executes the query and then calls the callback
	*  sets the local callback to be called once requestQuery completes
	*
	* @param {String} apiUrl Url to the api we want to do the request on.
	* @param {Object} requestQuery Api request object
	* @param {Function} callback Function called once the request is complete
	* @param {Function} [callbackTimeout] Optional Function called on api timeout
	*/
	$.doRequest = function( apiUrl, requestQuery, callback , callbackTimeout ) {
		// Sanity check:
		if ( mw.isLocalDomain( apiUrl ) ) {
			mw.log( "Error: trying to proxy local domain? " );
			return false;
		}

		// Generate a new context object: (its oky that optional arguments are null )
		var context = createContext({
			'apiUrl' :  apiUrl, // currentServerApiUrl
			'apiReq' : requestQuery,
			'callback' : callback,
			'timeoutCb' : callbackTimeout
		} );

		mw.log( "doRequest:: " + JSON.stringify( requestQuery ) );

		// Do the proxy req:
		doFrameProxy( context );
	};

	/**
	* Generates a remote browse file iframe window
	*	usefull for "uploading" cross domain
	*
	* @param {Function} callback Function to host the
	*/
	$.browseFile = function( options ) {
		if( ! options ) {
			options = {};
		}

		if( ! options.target ) {
			mw.log( "Error: no target for file browse iframe" ) ;
			return false;
		}

		if( !options.token ){
			mw.log( "Error: no token for file browse ");
			return false;
		}

		if( ! options.apiUrl ) {
			mw.log( "Error: no api url to target" );
			return false;
		}
		mw.log( 'Setup uploadDialogInterface' );

		// Make sure we have the dialog interface:
		var uploadDialogInterface = new mw.UploadDialogInterface( {
			'uploadHandlerAction' : function( action ){
				mw.log(	'apiProxy uploadActionHandler:: ' + action );

				// Handle actions that don't need to go to the iframe:
				switch ( action ){
					case 'returnToFormCb':
						if( options.returnToFormCb ){
							options.returnToFormCb();
						}
						return ;
					break;
				}

				// Handle actions that get sent to the remote frame
				mw.ApiProxy.sendServerMsg( {
					'apiUrl' : options.apiUrl,
					'frameName' : iFrameName,
					'frameMsg' : {
						'action' : 'uploadHandlerAction',
						'uiAction' : action
					}
				} );
			}
		} );

		// Setup the context with the callback in the current closure
		var context = createContext( {
			'apiUrl' : options.apiUrl,
			// Setup the callback to process iframeData
			'callback' : function( iframeData ) {
				// Process fileBrowse callbacks ::

				// check for basic status "ok"
				if( iframeData['status'] == 'ok' ) {
					// Hide the loading spinner
					$j( options.target ).find('.loadingSpinner').fadeOut('fast');
					mw.log("iframe ready callback");
					$j( '#' + iFrameName ).fadeIn( 'fast' );
					return ;
				}
				mw.log( '~browseFile Callback~ event type: ' + iframeData['event'] );

				// Handle events:
				if( iframeData['event'] ) {
					switch( iframeData['event'] ) {
						case 'selectFileCb':
							if( options.selectFileCb ) {
								options.selectFileCb( iframeData[ 'fileName' ] );
							}
						break;
						// Set the doneUploadCb if set in the browseFile options
						case 'doneUploadCb':
							mw.log( "should call cb: " + options.doneUploadCb );
							if( options.doneUploadCb ) {
								options.doneUploadCb( iframeData[ 'apiResult' ] );
								return true;
							}else{
								return false;
							}
						break;
						case 'uploadUI':
							if( uploadDialogInterface[ iframeData[ 'method' ] ] ){
								var args = iframeData['arguments'];
								mw.log( "Do dialog interface: " + iframeData['method'] + ' args: ' + args[0] + ', ' + args[1] + ', ' + args[2] );
								uploadDialogInterface[ iframeData['method'] ](
									args[0], args[1], args[2]
								);
							}
						break;
						default:
							mw.log(" Error unreconginzed event " + iframeData['event'] );
					}
				}
			}
		});

		// Setup the default width and height:
		if( ! options.width ) {
			options.width = 270;
		}
		if( ! options.height ) {
			options.height = 27;
		}

		var iFrameName = ( options.iframeName ) ? options.iframeName : 'fileBrowse_' + $j('iframe').length;
		// Setup an object to be packaged into the frame
		var iFrameRequest = {
			'clientFrame' : getClientFrame( context ),
			'action' : 'browseFile',
			'token' : options.token
		};

		var frameStyle = 'display:none;border:none;overflow:hidden;'
			+ 'width:'+ parseInt( options.width ) + 'px;'
			+ 'height:' + parseInt( options.height ) +'px';

		// Empty the target ( so that the iframe can be put there )
		$j( options.target ).empty();

		mw.log( 'append spinner');
		// Add a loading spinner to the target
		$j( options.target ).append(
			$j( '<div />' ).loadingSpinner()
		);

		// Append the browseFile iframe to the target:
		appendIframe( {
			'context' : context,
			'persist' : true,
			'style' : frameStyle,
			'name' : iFrameName,
			'request' : iFrameRequest,
			'target' : options.target
		} );

		// Return the name of the browseFile frame
		return iFrameName;
	};

	/**
	 * Output a server msg to a server iFrame
	 * ( such as a hosted browse file or dialog prompt )
	 *
	 * @param {Object} options Arguments to setup send server msg
	 * 	apiUrl The api url of the server to send the frame msg to
	 *  frameName The frame name to send the msg to
	 *  frameMsg The msg object to send to frame
	 */
	$.sendServerMsg = function( options ){
		if( !options.apiUrl || ! options.frameMsg || !options.frameName ){
			mw.log( "Error missing required option");
			return false;
		}

		//Setup a new context
		var context = createContext( {
			'apiUrl' : options.apiUrl
		} );

		// Send a msg to the server frameName from the server domain
		// Setup an object to be packaged into the frame
		var iFrameRequest = {
			'clientFrame' : getClientFrame( context ),
			'action' : 'sendFrameMsg',
			'frameName' : options.frameName,
			'frameMsg' : options.frameMsg
		};

		// Send the iframe request:
		appendIframe( {
			'persist' : true,
			'context' : context,
			'request' : iFrameRequest,
			'target' : options.target
		}, function( ) {
			mw.log( "sendServerMsg iframe done loading" );
		} );
	};

	/**
	 * The nested iframe action that passes its result back up to the top frame instance
	 *
	 * Entry point for hashResult from nested iframe
	 *
	 * @param {Object} hashResult Value to be sent to parent frame
	 */
	$.nested = function( hashResult ) {
		// Close the loader if present:
		//mw.closeLoaderDialog();

		mw.log( '$.proxy.nested callback :: ' + decodeURIComponent( hashResult ) );

		// Try to parse the hash result
		try {
			var resultObject = JSON.parse( decodeURIComponent( hashResult ) );
		} catch ( e ) {
			mw.log( "Error could not parse hashResult" );
		}

		// Check for the contextKey
		if ( ! resultObject.contextKey ) {
			mw.log( "Error missing context key in nested callback" );
			return false;
		}

		// Get the context via contextKey
		var context = getContext( resultObject.contextKey );

		// Set the loaded flag to true. ( avoids timeout calls )
		context[ 'proxyLoaded' ] = true;

		// Special callback to quickly establish a valid proxy connection.
		// If the proxyed "request" takes more time it does not
		// count against the proxy connection being established.
		if ( resultObject.state == 'ok' ) {
			return ;
		}

		// Check for the context callback:
		if( context.callback ){
			context.callback( resultObject );
		}
	};


	/**
	* Handle a server msg
	*
	* @param {Object} frameMsg
	*/
	$.handleServerMsg = function( frameMsg ){
		mw.log( "handleServerMsg:: " + JSON.stringify( frameMsg ) );
		if( ! frameMsg.action ) {
			mw.log(" missing frameMsg action " );
			return false;
		}
		switch( frameMsg.action ) {
			case 'fileSubmit':
				serverSubmitFile( frameMsg.formData );
			break;
			case 'uploadHandlerAction':
				serverSendUploadHandlerAction( frameMsg.uiAction );
			break;
		}
	};

	/**
	* Api server proxy entry point:
	* validates the server frame request
	* and process the request type
	*/
	$.server = function() {
		// Validate the server request:
		if( ! validateIframeRequest() ) {
			mw.log( "Not a valid iframe request");
			return false;
		}
		// Inform the client frame that we passed validation
		sendClientMsg( { 'state':'ok' } );

		return serverHandleRequest();
	};

	/**
	* Local scoped helper functions:
	*/

	/**
	* Creates a new context stored in the proxyContext local global
	* @param {Object} contextVars Initial contextVars
	*/
	function createContext ( contextVars ) {
		// Create a ~ sufficiently ~ unique context key
		var contextKey = new Date().getTime() * Math.random();
		proxyContext [ contextKey ] = contextVars;

		// Setup the proxy loaded flag for this context:
		proxyContext[ contextKey ][ 'proxyLoaded' ] = false;

		// Set a local pointer to the contextKey
		proxyContext[ contextKey ]['contextKey' ] = contextKey;

		mw.log( "created context with key:" + contextKey );

		// Return the proxy context
		return proxyContext [ contextKey ];
	};

	/**
	* Get a context from a contextKey
	* @param {String} [optional] contextKey Key of the context object to be returned
	* @return context object
	*	false if context object can not be found
	*/
	function getContext ( contextKey ) {
		if( ! proxyContext [ contextKey ] ){
			mw.log( "Error: contextKey not found:: " + contextKey );
			return false;
		}
		return proxyContext [ contextKey ];;
	};

	/**
	* Get the client frame path
	*/
	function getClientFrame( context ) {
		// Check if the mwEmbed is on the same server as we are
		if( mw.isLocalDomain( mw.getMwEmbedPath() ) ){
			return mw.getMwEmbedPath() + 'modules/ApiProxy/NestedCallbackIframe.html';
		} else {
			// Use the nested callback function ( server frame point back )
			nestedServerFrame = getServerFrame( {
				'apiUrl' : mw.getLocalApiUrl(),
				'pageName' : 'ApiProxyNestedCb'
			} );
			// Update the context to include the nestedCallbackFlag flag in the request
			return nestedServerFrame;
		}
	};

	/**
	* Get the server Frame path per requested Api url
	* (presently hard coded to MediaWiki:ApiProxy per /remotes/medaiWiki.js )
	*
	* NOTE: we should have a Hosted page once we deploy mwEmbed on the servers.
	* A hosted page would be much faster since it would not have to load all the
	* normal page view assets prior to being rewrite for api proxy usage.
	*
	* NOTE: We add the gadget incase the user has not enabled the gadget on the
	* domain they want to iframe to. There is no cost if they already have the
	* gadget on. This can be removed once deployed as well.
	*
	* @param {URL} apiUrl The url of the api server
	*/
	// Include gadget js ( in case the user has not enabled the gadget on that domain )

	//var gadgetWithJS = '';

	function getServerFrame( context ) {
		if( ! context || ! context.apiUrl ){
			mw.log( "Error no context api url " );
			return false;
		}
		var parsedUrl = mw.parseUri( context.apiUrl );

		var pageName = ( context.pageName ) ? context.pageName : 'ApiProxy';

		var pageUrl = parsedUrl.protocol + '://' + parsedUrl.authority
		+ '/w/index.php/MediaWiki:' + pageName;

		if( mw.getConfig( 'Mw.AppendWithJS' ) ){
			pageUrl+= '?' + mw.getConfig( 'Mw.AppendWithJS' );
		}
		return pageUrl;
	}

	/**
	* Do the frame proxy
	* 	Sets up a frame proxy request
	*
	* @param {Object} context ( the context of the current doFrameProxy call )
	* @param {Object} requestQuery The api request object
	*/
	function doFrameProxy ( context ) {

		var iframeRequest = {
			// Client domain frame ( will be approved by the server before sending and receiving msgs )
			'clientFrame' : getClientFrame( context ),
			'action' : 'apiRequest',
			'request' : context[ 'apiReq' ]
		};

		mw.log( "Do frame proxy request on src: \n" + getServerFrame( context ) + "\n" + JSON.stringify( context[ 'apiReq' ] ) );
		appendIframe( {
			'persist' : true,
			'request' : iframeRequest,
			'context' : context
		} )
	}

	/**
	* Validate an iframe request
	* checks the url hash for required parameters
	* checks  master_blacklist
	* checks  master_whitelist
	*/
	function validateIframeRequest() {
		var clientRequest = getClientRequest();

		if ( !clientRequest || !clientRequest.clientFrame ) {
			mw.log( "Error: no client domain provided " );
			$j( 'body' ).append( "no client frame provided" );
			return false;
		}

		// Make sure we are logged in
		// (its a normal mediaWiki page so all site vars should be defined)
		if ( typeof wgUserName != 'undefined' && !wgUserName ) {
			mw.log( 'Error Not logged in' );
			return false;
		}

		mw.log( "Setup server on: " + mw.parseUri( document.URL ).host );
		mw.log('Client frame: ' + clientRequest.clientFrame );

		/**
		* CHECK IF THE DOMAIN IS ALLOWED per the ApiProxy config:
		*/
		return isAllowedClientFrame( clientRequest.clientFrame );;
	}

	/**
	 * Check if a domain is allowed.
	 * @param {Object} clientFrame
	 */
	function isAllowedClientFrame( clientFrame ) {
		var clientDomain = mw.parseUri( clientFrame ).host ;
		// Get the proxy config

		// Check master blacklist
		if( mw.getConfig('ApiProxy.DomainBlackList') && mw.getConfig('ApiProxy.DomainBlackList').length ){
			var domainBlackList = mw.getConfig('ApiProxy.DomainBlackList');
			for ( var i =0; i < domainBlackList.length; i++ ) {
				var blackDomain = domainBlackList[i];
				// Check if domain check is a RegEx:
				if( typeof blackDomain == 'object' ){
					if( clientDomain.match( blackDomain ) ){
						return false;
					}
				} else {
					// just do a direct domain check:
					if( clientDomain == blackDomain ){
						return false;
					}
				}
			}
		}

		// Check the master whitelist:
		if( mw.getConfig('ApiProxy.DomainWhiteList') && mw.getConfig('ApiProxy.DomainWhiteList').length ){
			var domainWhiteList = mw.getConfig('ApiProxy.DomainWhiteList');
			for ( var i =0; i < domainWhiteList.length; i++ ) {
				whiteDomain = domainWhiteList[i];
				// Check if domain check is a RegEx:
				if( typeof whiteDomain == 'object' ){
					if( clientDomain.match( whiteDomain ) ) {
						return true;
					}
				} else {
					if( clientDomain == whiteDomain ){
						return true;
					}
				}
			}
		}

		// FIXME Add in user based approval ::

		// FIXME offer the user the ability to "approve" requested domain save to
		// their user preference setup )

		// FIXME grab and check domain against the users whitelist and permissions

		// for now just return false if the domain is not in the approved list
		return false;
	};

	/**
	* Get the client request from the document hash
	* @return {Object} the object result of parsing the document anchor msg
	*/
	function getClientRequest() {
		// Read the anchor data package from the requesting url
		var hashMsg = decodeURIComponent( mw.parseUri( document.URL ).anchor );
		try {
			return JSON.parse( hashMsg );
		} catch ( e ) {
			mw.log( "ProxyServer:: could not parse anchor" );
			return false;
		}
	};

	/**
	* Dialog to send the user if a proxy to the remote server could not be created
	* @param {Object} context
	*/
	function proxyNotReadyTimeout( context ) {
		mw.log( "Error:: api proxy timeout " + context.contextKey );

		// See if we have a callback function to call ( do not display the dialog )
		if( context[ 'timeoutCb' ] && typeof context[ 'timeoutCb' ] == 'function' ) {
			context[ 'timeoutCb' ] ( );
			return true;
		}

		var buttons = { };
		buttons[ gM( 'mwe-re-try' ) ] = function() {
			mw.addLoaderDialog( gM( 'mwe-re-trying' ) );
			// Re try the same context request:
			doFrameProxy( context );
		};
		buttons[ gM( 'mwe-cancel' ) ] = function() {
			mw.closeLoaderDialog ( );
		};

		// Setup the login link:
		var pUri = mw.parseUri( getServerFrame( context ) );
		var login_url = pUri.protocol + '://' + pUri.host;
		login_url += pUri.path.replace( 'MediaWiki:ApiProxy', 'Special:UserLogin' );

		var $dialogMsg = $j('<p />');
		$dialogMsg.append(
			gM( 'mwe-please-login',
				pUri.host,

				// Add log-in link:
				$j( '<a />')
				.attr( {
					'href' : login_url,
					'target' : '_new'
				} )
				.text( gM('mwe-log-in-link') )
			)
		)
		// Add the security note as well:
		$dialogMsg.append(
			$j('<br />'),
			gM( 'mwe-remember-loging' )
		)

		mw.addDialog( {
			'title' : gM( 'mwe-proxy-not-ready' ),
			'content' : $dialogMsg,
			'buttons' : buttons
		})
	};

	/**
	* API iFrame Server::
	*
	* Handles the server side proxy of requests
	* it adds child frames pointing to the parent "blank" frames
	*/

	/**
	* serverHandleRequest handle a given request from the client
	* maps the request to serverBrowseFile or serverApiRequest
	*
	* NOTE: mw.ApiProxy.server entry point validates the request
	*/
	function serverHandleRequest( ) {
		var clientRequest = getClientRequest();
		mw.log(" Handle client request :: " + JSON.stringify( clientRequest ) );
		// Process request type:
		switch( clientRequest[ 'action' ] ){
			case 'browseFile':
				return serverBrowseFile();
			break;
			case 'apiRequest':
				return serverApiRequest();
			break;
			case 'sendFrameMsg':
				return serverSendFrameMsg();
			break;
		}
		mw.log( "Error could not handle client request" );
		return false;
	};

 	/**
	* Api iFrame request:
	*/
	function serverApiRequest( ) {
		// Get the client request
		var clientRequest = getClientRequest();

		// Make sure its a json format
		clientRequest.request[ 'format' ] = 'json';

		mw.log(" do post request to: " + wgScriptPath + '/api' + wgScriptExtension );

		// Process the API request. We don't use mw.getJSON since we need to "post"
		$j.post( wgScriptPath + '/api' + wgScriptExtension,
			clientRequest.request,
			function( data ) {
				// Make sure data is in JSON data format ( not a string )
				if( typeof data != 'object' ){
					data = JSON.parse( data );
				}
				mw.log(" server api request got data: " + JSON.stringify( data ) );
				// Send the result data to the client
				sendClientMsg( data );
			}
		);
	}

	/**
	 *  Send a msg to a server frame
	 *
	 *  Server frame instances that handle msgs
	 *  should accept processMsg calls
	 */
	function serverSendFrameMsg( ){
		var clientRequest = getClientRequest();

		// Make sure the requested frame exists:
		if( ! clientRequest.frameMsg || ! clientRequest.frameName ) {
			mw.log("Error serverSendFrameMsg without frame msg or frameName" );
			return false;
		}

		// Send the message to the target frame
		top[ clientRequest.frameName ].mw.ApiProxy.handleServerMsg( clientRequest.frameMsg );
	}

	/**
	* Setup the browse file proxy on the "server"
	*
	* Sets the page content to browser file
	*/
	function serverBrowseFile( ) {

		// If wgEnableFirefogg is not boolean false, set to true
		if ( typeof wgEnableFirefogg == 'undefined' ) {
			wgEnableFirefogg = true;
		}

		// Setup the browse file html
		serverBrowseFileSetup();

		// Load the mw.upload library with iframe interface (similar to uploadPage.js)
		// Check if firefogg is enabled:
		// NOTE: the binding function should be made identical.
		if( wgEnableFirefogg ) {
			mw.load( 'AddMedia.firefogg', function() {
				$j( '#wpUploadFile' ).firefogg( getUploadFileConfig() );

				// Update status
				sendClientMsg( {'status':'ok'} );
			});
		} else {
			mw.load( 'AddMedia.UploadHandler', function() {
				var uploadConfig = getUploadFileConfig();

				$j( '#mw-upload-form' ).uploadHandler( getUploadFileConfig() );

				// Update status
				sendClientMsg( {'status':'ok'} );
			});
		}
	};

	/**
	 * Setup the browse file html
	 * @return browse file config
	 */
	function serverBrowseFileSetup( ){
		// Get the client request config
		var clientRequest = getClientRequest();

		// Check for fw ( file width )
		if( ! clientRequest.fileWidth ) {
			clientRequest.fileWidth = 130;
		}
		// Check for the token
		if( ! clientRequest.token ){
			mw.log("Error server browse file setup without token")
			return false;
		}

		//Build a form with bindings similar to uploadPage.js ( but only the browse button )
		$j('body').html(
			$j('<form />')
			.attr( {
				'name' : "mw-upload-form",
				'id' : "mw-upload-form",
				'enctype' : "multipart/form-data",
				'method' : "post",
				// Submit to the local domain
				'action' : 	mw.getLocalApiUrl()
			} )
			.append(
				//Add the "browse for file" button
				$j('<input />')
				.attr({
					'type' : "file",
					'name' : "wpUploadFile",
					'id' : "wpUploadFile"
				})
				.css({
					'width' : clientRequest.fileWidth
				}),

				// Append the token
				$j('<input />')
				.attr({
					'type' : 'hidden',
					'id' : "wpEditToken",
					'name' : 'token'
				})
				.val( clientRequest.token )
			)
		);
	}

	/**
	* Browse file upload config generator
	*/
	function getUploadFileConfig(){

		// Setup the upload iframeUI
		var uploadIframeUI = new mw.UploadIframeUI( function( method ){
			// Get all the arguments after the "method"
			var args = $j.makeArray( arguments ).splice( 1 );
			// Send the client the msg:
			sendClientMsg( {
				'event' : 'uploadUI',
				'method' : method,
				// Get all the arguments after the "method"
				'arguments' : args
			} );
		} );

		var uploadConfig = {
			// Set the interface type
			'ui' : uploadIframeUI,

			// Set the select file callback to update clientFrame
			'selectFileCb' : function( fileName ) {
				sendClientMsg( {
					'event': 'selectFileCb',
					'fileName' : fileName
				} );
			},
			// The return to form cb:
			'returnToFormCb' : function (){
				sendClientMsg( {
					'event': 'returnToFormCb'
				} );
			},
			// Api proxy does not handle descriptionText rewrite
			'rewriteDescriptionText' : false,

			// Don't show firefogg upload warning
			'showFoggWarningFlag' : false,

			// Set the doneUploadCb if set in the browseFile options
			'doneUploadCb' : function ( apiResult ){
				sendClientMsg( {
					'event': 'doneUploadCb',
					'apiResult' : apiResult
				} );
			}
		}

		return 	uploadConfig;
	}

	/**
	* Server send interface action
	*/
	function serverSendUploadHandlerAction( action ) {
		// Get a refrence to the uploadHandler:
		// NOTE: both firefogg and upload form should save upload target in a similar way
		var selector = ( wgEnableFirefogg ) ? '#wpUploadFile' : '#mw-upload-form';
		var uploadHandler = $j( selector ).get(0).uploadHandler;
		if( uploadHandler ){
			uploadHandler.uploadHandlerAction( action );
		} else {
			mw.log( "Error: could not find upload handler" );
		}
	}

	/**
	* Server submit file
	* @param {Object} options Options for submiting file
	*/
	function serverSubmitFile( formData ){
		mw.log("Submit form with fname:" + formData.filename + "\n :: " + formData.comment)
		// Add the FileName and and the description to the form
		var $form = $j('#mw-upload-form');
		var formApiFields = [ 'filename', 'comment', 'watch', 'ignorewarnings', 'token' ];

		for( var i=0; i < formApiFields.length ; i++ ){
			var fieldName = formApiFields[ i ];
			if( typeof formData[ fieldName ] == 'string' ) {
				// Add the input field if not already there:
				if( ! $form.find("[name='" + fieldName + "']" ).length ){
					$form.append(
						$j( '<input />' )
						.attr( {
							'name' : fieldName,
							'type' : 'hidden'
		 				} )
		 			)
				}
				// Add the value if set:
				$form.find("[name='" + fieldName + "']" ).val( formData[ fieldName ] );
			}
		}
		// Do submit the form
		$form.submit();
	};

	/**
	* Outputs the result object to the client domain
	*
	* @param {msgObj} msgObj Msg to send to client domain
	*/
	function sendClientMsg( msgObj ) {

		// Get the client Request:
		var clientRequest = getClientRequest();

		// Get a local reference to the client request
		var clientFrame = clientRequest[ 'clientFrame' ];

		// Double check that the client is an approved domain before outputting the iframe
		if( ! isAllowedClientFrame ( clientFrame ) ) {
			mw.log( "Cant send msg to " + clientFrame );
			return false;
		}

		// Double check we have a context key:
		if( ! clientRequest.contextKey ) {
			mw.log( "Error: missing context key " );
			return false;
		}

		var nestName = 'NestedFrame_' + $j( 'iframe' ).length;

		// Append the iframe to body
		appendIframe( {
			'src' : clientFrame,
			'request' : msgObj,
			// Client msgs just have the contextKey ( not the full context )
			'context' : {
				'contextKey' : clientRequest.contextKey
			}
		} );
	};

	/**
	 * Appends an iframe to the body from a given set of options
	 *
	 * NOTE: this uses string html building instead of jquery build-out
	 * because IE does not allow setting of iframe attributes
	 *
	 * @param {Object} options Iframe attribute options
	 * 	name - the name of the iframe
	 *  src - the url for the iframe
	 *  request - the request object to be packaged into the hash url
	 *  persist - set to true if the iframe should not
	 * 			  be removed from the dom after its done loading
	 */
	function appendIframe( options ){

		// Build out iframe in string since IE throws away attributes of
		//  jQuery iframe buildout
		var s = '<iframe ';

		// Check for context
		if( ! options[ 'context' ] ) {
			mw.log("Error missing context");
			return false;
		}else{
			var context = options[ 'context' ];
		}

		if( ! options[ 'src' ] ) {
			options[ 'src' ] = getServerFrame( context );
		}

		// Check for frame name:
		if( ! options[ 'name' ] ) {
			options[ 'name' ] = 'mwApiProxyFrame_' + $j( 'iframe' ).length;
		}

		// Add the frame name / id:
		s += 'name="' + mw.escapeQuotes( options[ 'name' ] ) + '" ';
		s += 'id="' + mw.escapeQuotes( options[ 'name' ] ) + '" ';

		// Check for style:
		if( ! options['style'] ) {
			options['style'] = 'display:none';
		}

		// Add style attribute:
		s += 'style="' + mw.escapeQuotes( options[ 'style' ] ) + '" ';

		// Special handler for src and packaged hash request:
		if( options.src ) {
			s += 'src="' + mw.escapeQuotes( options.src );
			if( options.request ) {

				// Add the contextKey to the request
				options.request[ 'contextKey' ] = context.contextKey;

				// Add the escaped version of the request:
				s += '#' + encodeURIComponent( JSON.stringify( options.request ) );
			}
			s += '" ';
		}

		// Close up the iframe:
		s += '></iframe>';

		// Check for the iframe append target ( default "body" tag )
		if( ! options[ 'target' ] ){
			options[ 'target' ] = 'body';
		}
		var targetName = ( typeof options[ 'target' ] == 'string') ? options[ 'target' ] : $j( options[ 'target' ]).length ;

		mw.log( "Append iframe:" + options[ 'src' ] + ' to: ' + targetName + " \n with data: " + JSON.stringify( options.request ) );

		// Append to target
		$j( options[ 'target' ] ).append( s );

		// Setup the onload callback
		$j( '#' + options[ 'name' ] ).get( 0 ).onload = function() {
			if( ! options.persist ){
				// Schedule the removal of the iframe
				// We don't call remove directly since some browsers seem to call "ready"
				//  before blocking javascript code is done running
				setTimeout( function() {
					$j('#' + options[ 'name' ] ).remove();
				}, 10 );
			}
		};

		// Setupt the timeout check:
		setTimeout( function() {
			if ( context[ 'proxyLoaded' ] === false ) {
				// We timed out no api proxy (should make sure the user is "logged in")
				proxyNotReadyTimeout( context );
			}
		}, mw.getConfig( 'defaultRequestTimeout') * 1000 );
	}

} )( window.mw.ApiProxy );
