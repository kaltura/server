/*
 * Simple kaltura javascript api
 *
 * uses configuration Kaltura.ServiceUrl and Kaltura.ServiceBase for api entry point
 */ 

/**
 * kApi takes supports a few mixed argument types
 * 
 * @param {Number}
 * 		partner_id used to establish a request key for the given session
 * 		( this enables multiple sessions per partner id on a single page )
 * @param {Mixed}
 * 		Array An Array of request params for multi-request 
 * 		Object Named request params
 */
	
mw.KApi = function( partner_id ){
	return this.init( partner_id );	
}

mw.KApi.prototype = {
	baseParam: {
		'apiVersion' : '3.0',
		'clientTag' : 'html5',
		'expiry' : '86400',
		'format' : 9, // 9 = JSONP format
		'ignoreNull' : 1
	},
	// The local kaltura session key ( so it does not have to be re-grabbed with every request
	ks : null,
	init: function( partner_id ){
		this.partner_id = partner_id;
	},
	getPartnerId: function( ){
		return this.partner_id;
	},
	doRequest : function( requestObject, callback ){
		var _this = this;
		var param = {};		
		// Convert into a multi-request if no session is set ( ks will be added bellow ) 
		if( !requestObject.length && !this.ks ){
			requestObject = [ requestObject ];
		}
		
		// Check that we have a session established if not make it part of our multi-part request
		if( requestObject.length ){
			param['service'] = 'multirequest';
			param['action'] = 'null';

			// Kaltura api starts with index 1 for some strange reason. 
			var mulitRequestIndex = 1;
			
			/**
			 * Ideally we could do a single request to get the KS and the payload.
			 * unfortunately that does not appear to be the case atm. 
			 */
			/*
			 if( ! this.ks ){				
				// Add the kaltura session ( if not already set ) 
				var multiRequest = {
					'service' : 'session',
		        	'action' : 'startwidgetsession',
		        	'widgetId': '_' + this.partner_id // don't ask me, I did not design the API! 
		        }
				for ( var  paramKey in multiRequest ){
					param[ mulitRequestIndex + ':' + paramKey ] = multiRequest[ paramKey ];
				}
				mulitRequestIndex++;
				
				if( ! this.ks ){		
					// For each search item append ks: 
					param[ 'ks' ] = '{1:result:objects:0:ks}';
				}
			}
			*/
			
			for( var i = 0 ; i < requestObject.length; i++ ){
				var requestInx = mulitRequestIndex + i;
				// MultiRequest pre-process each param with inx:param
				for( var paramKey in requestObject[i] ){
					// support multi dimension array request:  
					// NOTE kaltura api only has sub arrays ( would be more feature complete to 
					// recursively define key appends )
					if( typeof requestObject[i][paramKey] == 'object' ){
						for( var subParamKey in requestObject[i][paramKey] ){
							param[ requestInx + ':' + paramKey + ':' +  subParamKey ] =
								requestObject[i][paramKey][subParamKey];
						}
					} else {
						param[ requestInx + ':' + paramKey ] = requestObject[i][paramKey];
					}					
				}
			}			
		} else { 
			param = requestObject;
		}				
		
		// add in the base parameters:
		for( var i in this.baseParam ){
			if( typeof param[i] == 'undefined' ){
				param[i] = this.baseParam[i];
			}
		};
		
		// Make sure we have the kaltura session
		// ideally this could be part of the multi-request but could not get it to work 
		// see commented out code above. 
		this.getKS( function( ks ){
			
			// remove service tag ( hard coded into the api url ) 
			var serviceType = param['service'];
			delete param['service'];				
			
			param['ks'] = ks;
			param['kalsig'] = _this.getSignature( param );
			//debugger;
			var requestUrl = _this.getApiUrl() + serviceType + '&' + $j.param( param );
			// Do the getJSON jQuery call with special callback=? parameter: 
			$j.getJSON( requestUrl +  '&callback=?', function( data ){
				if( callback ){
					callback( data );
				}
			});
		});
	},
	setKS: function( ks ){
		this.ks = ks;
	},
	getKS: function( callback ){
		if( this.ks ){
			callback(this.ks);
			return true;
		}
		var _this = this;
		// Add the Kaltura session ( if not already set ) 
		var ksParam = {
        	'action' : 'startwidgetsession',
        	'widgetId': '_' + this.partner_id // don't ask me, I did not design the API! 
        }
		// add in the base parameters:
		var param = $j.extend( {}, this.baseParam, ksParam );
		var requestURL = this.getApiUrl() + 'session&' + $j.param( param );
		$j.getJSON( requestURL + '&callback=?', function( data ){
			_this.ks = data.ks;
			callback( _this.ks );
		});
	},
	getApiUrl : function(){
		return mw.getConfig( 'Kaltura.ServiceUrl' ) + mw.getConfig( 'Kaltura.ServiceBase' );
	},
	getSignature: function( params ){
		params = this.ksort(params);
		var str = "";
		for(var v in params) {
			var k = params[v];
			str += k + v;
		}
		return MD5(str);
	},
	/**
	 * Sorts an array by key, maintaining key to data correlations. This is useful mainly for associative arrays. 
	 * @param arr 	The array to sort.
	 * @return		The sorted array.
	 */
	ksort: function ( arr ) {
		var sArr = [];
		var tArr = [];
		var n = 0;
		for ( i in arr ){
			tArr[n++] = i+"|"+arr[i];
		}
		tArr = tArr.sort();
		for (var i=0; i<tArr.length; i++) {
			var x = tArr[i].split("|");
			sArr[x[0]] = x[1];
		}
		return sArr;
	},
	/**
	 * PlayerLoader
	 * 
	 * Does a single request to the api to 
	 * a) Get context data
	 * c) Get flavorasset 
	 * b) Get baseEntry
	 */
	playerLoader: function( kProperties, callback ){
		var requestObject = [];
		if( kProperties.entry_id ){ 
			// The referring  url ( can be from the iframe if in iframe mode ) 
			var refer = ( mw.getConfig( 'EmbedPlayer.IframeParentUrl') ) ? 
							mw.getConfig( 'EmbedPlayer.IframeParentUrl') : 
							document.URL;
			
			// Add Context Data request 			
			requestObject.push({
		        	 'contextDataParams' : {
			        	 	'referrer' : refer,
			        	 	'objectType' : 'KalturaEntryContextDataParams'
			         },
		        	 'service' : 'baseentry',
		        	 'entryId' : kProperties.entry_id ,
		        	 'action' : 'getContextData'
			});
			
			 // Get flavorasset
			requestObject.push({
		        	 'entryId' : kProperties.entry_id ,
		        	 'service' : 'flavorasset',
		        	 'action' : 'getByEntryId'
		    });
			
		    // Get baseEntry
			requestObject.push({
		        	 'service' : 'baseentry',
		        	 'action' : 'get',
		        	 'version' : '-1',
		        	 'entryId' : kProperties.entry_id
		    });
		}		
		if( kProperties.uiconf_id ){
			// Get Ui Conf if property is present
			requestObject.push({
		        	'service' : 'uiconf',
		        	'id' : kProperties.uiconf_id,
		        	'action' : 'get'
		    });
		}
		// Do the request and pass along the callback
		this.doRequest( requestObject, function( data ){
			var namedData = {};
			// Name each result data type for easy access
			if( kProperties.entry_id ){ 
				namedData['accessControl'] = data[0];
				namedData['flavors'] = data[1];
				namedData['meta'] = data[2];
				if( data[3] ){
					namedData['uiConf'] = data[3]['confFile'];
				}
			} else if( kProperties.uiconf_id ){
				// If only loading the confFile set here: 
				namedData['uiConf'] = data[0]['confFile'];
			}	
			callback( namedData );
		});
	}
};

/**
 * KApi public entry points: 
 * 
 * TODO maybe move these over to "static" members of the kApi object ( ie not part of the .prototype methods ) 
 */
// Cache api object per partner
// ( so that multiple partner types don't conflict if used on a single page )
mw.KApiPartnerCache = [];
mw.kApiGetPartnerClient = function( partner_or_widget_id ){
	// strip leading _ turn widget to partner
	var partner_id = partner_or_widget_id.replace(/_/g, '');
	
	if( !mw.KApiPartnerCache[ partner_id ] ){
		mw.KApiPartnerCache[ partner_id ] = new mw.KApi( partner_id );
	};
	return mw.KApiPartnerCache[ partner_id ];
}
mw.KApiPlayerLoader = function( kProperties, callback ){
	if( !kProperties.widget_id ) {
		mw.log( "Error:: mw.KApiPlayerLoader:: cant run player loader with widget_id " );
	}
	// Convert widget_id to partner id
	var partner_id = kProperties.widget_id.replace(/_/g, '');
	
	var kClient = mw.kApiGetPartnerClient( partner_id );
	kClient.playerLoader( kProperties, callback );
	
	// Return the kClient api object for future requests
	return kClient;
}
