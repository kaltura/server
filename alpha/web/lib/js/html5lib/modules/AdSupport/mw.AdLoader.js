
mw.AdLoader = {
	/**
	 * Get ad display configuration object from a url
	 * 
	 * @param {string} adUrl
	 * 		The url which contains the xml ad payload
	 * @param {function} callback
	 * 		Function called with ad payload once ad content is loaded. 
	 */
	load: function( adUrl, callback ){
		var _this = this;
		// We use a xml proxy ( passing on the clients ip for geo lookup ) 
		// since the ad server is almost never on the same domain as the api.
		// @@todo also we should explore html5 based cross domain request to avoid the proxy
		var proxyUrl = mw.getConfig( 'AdSupport.XmlProxyUrl' );
		if( !proxyUrl){
			mw.log("Error: mw.KAds : missing kaltura proxy url ( can't load ad ) ");
			return ; 
		}
		$j.getJSON( proxyUrl + '?url=' + encodeURIComponent( adUrl ) + '&callback=?', function( result ){
			var adDisplayConf = {};
			if( result['http_code'] == 'ERROR' || result['http_code'] == 0 ){
				mw.log("Error: loadAdXml error with http response");
				callback(false);
				return ;
			}
			
			switch( _this.getAdFormat( result['contents'] ) ){
				case 'vast':
					// If we have lots of ad formats we could conditionally load them here: 
					// ( normally we load VastAdParser before we get here but just in-case ) 
					mw.load( 'mw.VastAdParser', function(){
						callback(
							mw.VastAdParser.parse( result['contents'] ) 
						);
					});
					return ;
				break;
			}					
			mw.log("Error: could not parse adFormat from add content: \n" + result['contents']);
			callback( {} );
		});
	},
	/**
	 * Get ad Format
	 * @param {string} 
	 * 		The xml string of the ad contents
	 * @return 
	 * @type {string}
	 * 		The type of string
	 */
	getAdFormat: function( xmlString ){
		var lowerCaseXml = xmlString.toLowerCase();
		
		// Check xml  for "<vast> </vast> tag
		if( lowerCaseXml.indexOf('<vast') != -1 &&
			lowerCaseXml.indexOf('</vast>')	)
		{
			return 'vast';
		}
		// OpenX vast ads have a root element of videoAdServingTemplate
		if( lowerCaseXml.indexOf('<videoadservingtemplate') != -1 &&
			lowerCaseXml.indexOf('</videoadservingtemplate>')	)
		{
			return 'vast';
		}	
		return 'unknown';
	}
}