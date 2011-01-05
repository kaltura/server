/**
* API Helper functions
*/

( function( mw ) {
	// xxx note we should namespace the following helper functions into Api class.
	mw.Api = {};

	/**
	*
	* Helper function to get latest revision text for a given title
	*
	* Assumes "follow redirects"
	*
	* $j.getTitleText( [apiUrl], title, callback )
	*
	* @param {String} url or title key
	* @parma {Mixed} title or callback function
	* @param {Function} callback Function or NULL
	*
	* @return callback is called with:
	* 	{Boolean} false if no page found
	* 	{String} text of wiki page
	*/
	mw.getTitleText = function( apiUrl, title, callback ) {
		// Check if optional apiURL was not included
		if( !callback ) {
			title = apiUrl;
			callback = title;
			apiUrl = mw.getLocalApiUrl();
		}
		var request = {
			// Normalize the File NS (ie sometimes its present in apiTitleKey other times not
			'titles' : title,
			'prop' : 'revisions',
			'rvprop' : 'content'
		};

		mw.getJSON( apiUrl , request, function( data ) {
			if( !data || !data.query || !data.query.pages ) {
				callback( false );
			}
			var pages = data.query.pages;
			for(var i in pages) {
				var page = pages[ i ];
				if( page[ 'revisions' ] && typeof page[ 'revisions' ][0]['*'] != 'undefined' ) {
					callback( page[ 'revisions' ][0]['*'] );
					return ;
				}
			}
			callback( false );
		} );
	};

	/**
	* Issues the wikitext parse call
	*
	* @param {String} wikitext Wiki Text to be parsed by mediaWiki api call
	* @param {String} title Context title of the content to be parsed
	* @param {Function} callback Function called with api parser output
	*/
	mw.parseWikiText = function( wikitext, title, callback ) {
		mw.log("mw.parseWikiText text length: " + wikitext.length + ' title context: ' + title );
		mw.load( 'JSON', function(){
			$j.ajax({
				type: 'POST',
				url: mw.getLocalApiUrl(),
				// Give the wiki 60 seconds to parse the wiki-text
				timeout : 60000,
				data: {
					'action': 'parse',
					'format': 'json',
					'title' : title,
					'text': wikitext
				},
				dataType: 'text',
				success: function( data ) {
					var jsonData = JSON.parse( data ) ;
					// xxx should handle other failures
					callback( jsonData.parse.text['*'] );
				},
				error: function( XMLHttpRequest, textStatus, errorThrown ){
					// xxx should better handle failures
					mw.log( "Error: mw.parseWikiText:" + textStatus );
					callback( "Error: failed to parse wikitext " );
				}
			});
		});
	}

	/**
	* mediaWiki JSON a wrapper for jQuery getJSON:
	* ( could also be named mw.apiRequest )
	*
	* The mwEmbed version lets you skip the url part
	* mw.getJSON( [url], data, callback, [timeoutCallback] );
	*
	* Lets you assume:
	* 	url is optional
	* 		( If the first argument is not a string we assume a local mediaWiki api request )
	*   callback parameter is not needed for the request data
	* 	url param 'action'=>'query' is assumed ( if not set to something else in the "data" param
	* 	format is set to "json" automatically
	* 	automatically issues request over "POST" if the request api post type
	*	automatically will setup apiProxy where request is cross domain
	*
	* @param {Mixed} url or data request
	* @param {Mixed} data or callback
	* @param {Function} callback function called on success
	* @param {Function} callbackTimeout - optional function called on timeout
	* 	Setting timeout callback also avoids default timed-out dialog for proxy requests
	*/
	mw.getJSON = function() {
		// Process the arguments:

		// Set up the url
		var url = false;
		url = ( typeof arguments[0] == 'string' ) ? arguments[0] : mw.getLocalApiUrl();

		// Set up the data:
		var data = null;
		data = ( typeof arguments[0] == 'object' ) ? arguments[0] : null;
		if( !data && typeof arguments[1] == 'object' ) {
			data = arguments[1];
		}

		// Setup the callback
		var callback = false;
		callback = ( typeof arguments[1] == 'function') ? arguments[1] : false;
		var cbinx = 1;
		if( ! callback && ( typeof arguments[2] == 'function') ) {
			callback = arguments[2];
			cbinx = 2;
		}

		// Setup the timeoutCallback ( function after callback index )
		var timeoutCallback = false;
		timeoutCallback = ( typeof arguments[ cbinx + 1 ] == 'function' ) ? arguments[ cbinx + 1 ] : false;

		// Make sure we got a url:
		if( !url ) {
			mw.log( 'Error: no api url for api request' );
			return false;
		}

		// Add default action if unset:
		if( !data['action'] ) {
			data['action'] = 'query';
		}

		// Add default format if not set:
		if( !data['format'] ) {
			data['format'] = 'json';
		}

		// Setup callback wrapper for timeout
		var requestTimeOutFlag = false;
		var ranCallback = false;

		/**
		 * local callback function to control timeout
		 * @param {Object} data Result data
		 */
		var myCallback = function( data ){
			if( ! requestTimeOutFlag ){
				ranCallback = true;
				callback( data );
			}
		}
		// Set the local timeout call based on defaultRequestTimeout
		setTimeout( function( ) {
			if( ! ranCallback ) {
				requestTimeOutFlag = true;
				mw.log( "Error:: request timed out: " + url );
				if( timeoutCallback ){
					timeoutCallback();
				}
			}
		}, mw.getConfig( 'defaultRequestTimeout' ) * 1000 );

		//mw.log("run getJSON: " + mw.replaceUrlParams( url, data ) );

		// Check if the request requires a "post"
		if( mw.checkRequestPost( data ) || data['_method'] == 'post' ) {

			// Check if we need to setup a proxy
			if( ! mw.isLocalDomain( url ) ) {

				//Set local scope ranCallback to true
				// ( ApiProxy handles timeouts internally )
				ranCallback = true;

				// Load the proxy and issue the request
				mw.load( 'ApiProxy', function( ) {
					mw.ApiProxy.doRequest( url, data, callback, timeoutCallback);
				} );

			} else {
				// Do the request an ajax post
				$j.post( url, data, myCallback, 'json');
			}
			return ;
		}

		// If cross domain setup a callback:
		if( ! mw.isLocalDomain( url ) ) {
			if( url.indexOf( 'callback=' ) == -1 || data[ 'callback' ] == -1 ) {
				// jQuery specific jsonp format: ( second ? is replaced with the callback )
				url += ( url.indexOf('?') == -1 ) ? '?callback=?' : '&callback=?';
			}
		}
		// Pass off the jQuery getJSON request:
		$j.getJSON( url, data, myCallback );
	}

	/**
	* Checks if a mw request data requires a post request or not
	* @param {Object}
	* @return {Boolean}
	*	true if the request requires a post request
	* 	false if the request does not
	*/
	mw.checkRequestPost = function ( data ) {
		if( $j.inArray( data['action'], mw.getConfig( 'apiPostActions' ) ) != -1 ) {
			return true;
		}
		if( data['prop'] == 'info' && data['intoken'] ) {
			return true;
		}
		if( data['meta'] == 'userinfo' ) {
			return true;
		}
		return false;
	}

	/**
	* Check if the url is a request for the local domain
	*  relative paths are "local" domain
	* @param {String} url Url for local domain
	* @return {Boolean}
	*	true if url domain is local or relative
	* 	false if the domain is
	*/
	mw.isLocalDomain = function( url ) {
		if( mw.parseUri( document.URL ).host == mw.parseUri( url ).host
			|| url.indexOf( '://' ) == -1 )
		{
			return true;
		}
		return false;
	}

	/**
	 * Api helper to grab an edit token
	 *
	 * @param {String} [apiUrl] Optional target API URL (uses default local api if unset)
	 * @param {String} title The wiki page title you want to edit
	 * @param {callback} callback Function to pass the token to.
	 * 						issues callback with "false" if token not retrieved
	 */
	mw.getToken = function( apiUrl, title, callback ) {
		// Make the apiUrl be optional:
		if( typeof title == 'function' ) {
			callback = title;
			title = apiUrl;
			apiUrl = mw.getLocalApiUrl();
		}

		mw.log( 'mw:getToken' );

		var request = {
			'prop': 'info',
			'intoken': 'edit',
			'titles': title
		};
		mw.getJSON( apiUrl, request, function( data ) {
			for ( var i in data.query.pages ) {
				if ( data.query.pages[i]['edittoken'] ) {
					callback ( data.query.pages[i]['edittoken'] );
					return ;
				}
			}
			// No token found:
			callback ( false );
		} );
	}

	/**
	 * Api helper to grab the username
	 * @param {String} [apiUrl] Optional target API url (uses default local api if unset)
	 * @param {Function} callback Function to callback with username or false if not found
	 * @param {Boolean} fresh A fresh check is issued.
	 */
	 // Stub feature apiUserNameCache to avoid multiple calls
	 // ( a more general api cache framework should be developed  )
	 var apiUserNameCache = {};
	 mw.getUserName = function( apiUrl, callback, fresh ){
	 	if( typeof apiUrl == 'function' ){
	 		var callback = apiUrl;
	 		var apiUrl = mw.getLocalApiUrl();
	 	}

	 	// If apiUrl is local check wgUserName global
	 	//  before issuing the api request.
	 	if( mw.isLocalDomain( apiUrl ) ){
	 		if( typeof wgUserName != 'undefined' && wgUserName !== null ) {
	 			callback( wgUserName )
	 			// In case someone called this function without a callback
	 			return wgUserName;
	 		}
	 	}
	 	if( ! fresh && apiUserNameCache[ apiUrl ] ) {
	 		callback( apiUserNameCache[ apiUrl ] );
	 		return ;
	 	}

	 	// Setup the api request
		var request = {
			'action':'query',
			'meta':'userinfo'
		};

		// Do request
		mw.getJSON( apiUrl, request, function( data ) {
			if( !data || !data.query || !data.query.userinfo || !data.query.userinfo.name ){
				// Could not get user name user is not-logged in
				mw.log( " No userName in response " );
				callback( false );
				return ;
			}
			// Check for "not logged in" id == 0
			if( data.query.userinfo.id == 0 ){
				callback( false );
				return ;
			}
			apiUserNameCache[ apiUrl ] = data.query.userinfo.name;
			// Else return the username:
			callback( data.query.userinfo.name );
		}, function(){
			// Timeout also results in callback( false ) ( no user found)
			callback( false );
		} );
	}

	/**
	* Get the api url for a given content provider key
	* @return {Mixed}
	*	url for the provider
	* 	local wiki api if no apiProvider is set
	*/
	mw.getApiProviderURL = function( providerId ) {
		if( mw.getConfig( providerId + '_apiurl') ) {
			return mw.getConfig( providerId + '_apiurl');
		}
		return mw.getLocalApiUrl();
	};

	/**
	* Get Api URL from mediaWiki page defined variables
	* @return {Mixed}
	* 	api url
	* 	false
	*/
	mw.getLocalApiUrl = function() {
		if ( typeof wgServer != 'undefined' && typeof wgScriptPath != 'undefined' ) {
			return wgServer + wgScriptPath + '/api.php';
		}
		return false;
	};

}) ( window.mw );