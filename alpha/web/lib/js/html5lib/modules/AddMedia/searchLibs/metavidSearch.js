/*
* API modes (implementations should call these objects which inherit the mvBaseRemoteSearch
*/
mw.addMessages( {
	"mwe-stream_title" : "$1 $2 to $3"
} );
var metavidSearch = function( iObj ) {
	return this.init( iObj );
};
metavidSearch.prototype = {
 	// Default request parameters
	defaultReq: {
		'order': 'recent',
		'feed_format': 'json_rss'
	},
	init: function( iObj ) {
		// init base class and inherit:
		var baseSearch = new baseRemoteSearch( iObj );
		for ( var i in baseSearch ) {
			if ( typeof this[i] == 'undefined' ) {
				this[i] = baseSearch[i];
			} else {
				this['parent_' + i] = baseSearch[i];
			}
		}
	},

	/**
	* getSearchResults
	*
	* @param {String} search_query Text search string
	*/
	getProviderResults: function( search_query, callback ) {
		// Set local ref:
		var _this = this;

		mw.log( 'metavidSearch::getProviderResults()' );

		// Process all options
		var url = this.provider.apiUrl;
		var request = $j.extend( {}, this.defaultReq );
		request[ 'f[0][t]' ] = 'match';
		request[ 'f[0][v]' ] = search_query;

		// add offset limit:
		request[ 'limit' ] = this.provider.limit;
		request[ 'offset' ] = this.provider.offset;
		mw.log("getJSON: " + url + '&cb=?' );
		$j.getJSON( url + '&cb=?&cb_inx=1', request, function( data ) {
			mw.log( 'mvSearch: got data response::' );
			var xmldata = ( data && data['pay_load'] ) ? mw.parseXML( data['pay_load'] ) : false;
			if( !xmldata ) {
				// XML Error or No results:
				_this.resultsObj = {};
				_this.loading = 0;
				return ;
			}

			// Add the data xml payload with context url:
			_this.addRSSData( xmldata , url );

			// Do some metavid specific pos processing on the resource data:
			for ( var i in _this.resultsObj ) {
				var resource = _this.resultsObj[i];
				var proe = mw.parseUri( resource['roe_url'] );
				resource['start_time'] = proe.queryKey['t'].split( '/' )[0];
				resource['end_time'] = proe.queryKey['t'].split( '/' )[1];
				resource['stream_name'] = proe.queryKey['stream_name'];

				// All metavid content is public domain:
				resource['license'] = _this.rsd.getLicenseFromKey( 'pd' );

				// Transform the title into a wiki_safe title:
				resource['titleKey'] =	 _this.getTitleKey( resource );

				// Default width of metavid clips:
				resource['target_width'] = 400;

				resource['author'] = 'US Government';

				// Add in the date as UTC "toDateString" :
				var d = _this.getDateFromLink( resource.link );
				resource['date'] =	 d.toDateString();

				// Set the license_template_tag ( all metavid content is PD-USGov )
				resource['license_template_tag'] = 'PD-USGov';
			}
			// done loading:
			callback( 'ok' );
		} );
	},

	/**
	* Get a Title key for the asset name inside the mediaWiki system
	*
	* @param {Object} resource Resource to get title key from
	*/
	getTitleKey: function( resource ) {
		if( resource['titleKey'] ) {
			return resource['titleKey'];
		}
		return resource['stream_name'] + '_part_' + resource['start_time'].replace(/:/g, '.' ) + '_to_' + resource['end_time'].replace(/:/g, '.' ) + '.ogv';
	},

	/**
	* Get a Title from a resource
	*
	* @parma {Object} resoruce Resource to get title from
	*/
	getTitle:function( resource ) {
		var sn = resource['stream_name'].replace( /_/g, ' ' );
		sn = sn.charAt( 0 ).toUpperCase() + sn.substr( 1 );
		return gM( 'mwe-stream_title', [ sn, resource.start_time, resource.end_time ] );
	},

	/**
	* Get additional wiki text description
	*
	* @param {Object} resource Resource to get additional wikitext for.
	*/
	getExtraResourceDescWiki:function( resource ) {
		var o = "\n";
		// check for person
		if ( resource.person && resource.person['label'] )
			o += '* featuring [[' + resource.person['label'] + ']]' + "\n";

		if ( resource.parent_clip )
			o += '* part of longer [' + resource.parent_clip + ' video clip]' + "\n";

		if ( resource.person && resource.person['url'] && resource.person['label'] )
			o += '* also see speeches by [' + $j.trim( resource.person.url ) + ' ' + resource.person['label'] + ']' + "\n";

		// check for bill:
		if ( resource.bill && resource.bill['label'] && resource.bill['url'] )
			o += '* related to bill: [[' + resource.bill['label'] + ']] more bill [' + resource.bill['url'] + ' video clips]' + "\n";
		return o;
	},

	/**
	* Get inline description
	* format is "quote" followed by [[name of person]]
	* @param {Object} resource Resource to get inline description of.
	*/
	getInlineDescWiki:function( resource ) {
		var o = this.parent_getInlineDescWiki( resource );
		// add in person if found
		if ( resource.person && resource.person['label'] ) {
			o = $j.trim( o.replace( resource.person['label'], '' ) );
			// trim leading :
			if ( o.substr( 0, 1 ) == ':' )
				o = o.substr( 1 );
			// add quotes and person at the end:
			var d = this.getDateFromLink( resource.link );
			o = '"' + o + '" [[' + resource.person['label'] + ']] on ' + d.toDateString();
		}
		// could do ref or direct link:
		o += ' \'\'[' + $j.trim( resource.link ) + ' source clip]\'\' ';

		// var o= '"' + o + '" by [[' + resource.person['label'] + ']] '+
		//		'<ref>[' + resource.link + ' Metavid Source Page] for ' + resource.title +'</ref>';
		return o;
	},

	/**
	* Apply an updated start and end time to the resource ( for use with the #embed_vid clip )
	*
	* @param {Object} resource Resource to be updated
	*/
	applyVideoAdj: function( resource ) {
		mw.log( 'mv ApplyVideoAdj::' );

		// Update the titleKey:
		resource['titleKey'] =	 this.getTitleKey( resource );

		// Update the title:
		resource['title'] = this.getTitle( resource );

		// update the interface:
		mw.log( 'update title to: ' + resource['title'] );
		$j( '#rsd_resource_title' ).html( gM( 'rsd_resource_edit', resource['title'] ) );

		// if the video is "roe" based select the ogg stream
		if ( resource.roe_url && resource.pSobj.provider.stream_import_key ) {
			var source = $j( '#embed_vid' ).get( 0 ).mediaElement.getSourceById( resource.pSobj.provider.stream_import_key );
			if ( !source ) {
				mw.log( 'Error::could not find source: ' + resource.pSobj.provider.stream_import_key );
			} else {
				resource['src'] = source.getSrc();
				mw.log( "g src_key: " + resource.pSobj.provider.stream_import_key + ' src:' + resource['src'] ) ;
				return true;
			}
		}
	},

	/**
	* Get html to embed the resource into a page:
	*
	* @param {Object} resource Resource to be embed
	* @param {Object} options Resource Options for embedding ( like max_width )
	*/
	getEmbedHTML: function( resource , options ) {
		if ( !options )
			options = { };
		var id_attr = ( options['id'] ) ? ' id = "' + options['id'] + '" ': '';
		var style_attr = ( options['max_width'] ) ? ' style="width:' + options['max_width'] + 'px;"':'';
		if ( options['only_poster'] ) {
			return '<img ' + id_attr + ' src="' + resource['poster'] + '" ' + style_attr + '>';
		} else {
			return '<video ' + id_attr + ' roe="' + resource['roe_url'] + '"></video>';
		}
	},

	/**
	* Get Image Transform
	*
	* @param {Object} resource Resource to transform
	*/
	getImageTransform:function( resource, opt ) {
		if ( opt.width <= 80 ) {
			return mw.replaceUrlParams( resource.poster, { 'size' : "icon" } )
		} else if ( opt.width <= 160 ) {
			return mw.replaceUrlParams( resource.poster, { 'size' : "small" } )
		} else if ( opt.width <= 320 ) {
			return mw.replaceUrlParams( resource.poster, { 'size' : 'medium' } )
		} else if ( opt.width <= 512 ) {
			return mw.replaceUrlParams( resource.poster, { 'size' : 'large' } )
		} else {
			return mw.replaceUrlParams( resource.poster, { 'size' : 'full' } )
		}
	},

	/**
	* Add information from the embed instance to the resource
	*
	* @param {Object} resource Resource to transform
	*/
	addEmbedInfo : function( resource, embed_id ) {
		var sources = $j( '#' + embed_id ).get( 0 ).mediaElement.getSources();
		resource.other_versions = '*[' + resource['roe_url'] + ' XML of all Video Formats and Timed Text]' + "\n";
		for ( var i in sources ) {
			var cur_source = sources[i];
			// resource.other_versions += '*['+cur_source.getSrc() +' ' + cur_source.title +']' + "\n";
			if ( cur_source.id == this.provider.target_source_id )
				resource['url'] = cur_source.getSrc();
		}
		// mw.log('set url to: ' + resource['url']);
		return resource;
	},

	/**
	* Get a date from a media link
	*
	* @param {String} link Link url to be parsed
	* @return {Date Object}
	*/
	getDateFromLink:function( link ) {
		var dateExp = new RegExp( /_([0-9]+)\-([0-9]+)\-([0-9]+)/ );
		var dParts = link.match ( dateExp );
		var d = new Date();
		var year_full = ( dParts[3].length == 2 ) ? '20' + dParts[3].toString():dParts[3];
		d.setFullYear( year_full, dParts[1] - 1, dParts[2] );
		return d;
	}
}


/**
* Takes in a string returns an xml dom object
*
* NOTE: this should be deprecated in favor of jquery xml parsing
* $j( xml_string )
*
* @param {String} str String of XML content to be parsed
* @return
* 	{Object} XML
*	false If XML could not be parsed
*
*/
mw.parseXML = function ( str ) {
	if ( $j.browser.msie ) {
		// Attempt to parse as XML for IE
		var xmldata = new ActiveXObject( "Microsoft.XMLDOM" );
		xmldata.async = "false";
		try{
			xmldata.loadXML( str );
			return xmldata;
		} catch (e) {
			mw.log( 'XML parse ERROR: ' + e.message );
			return false;
		}
	}

	// For others (Firefox, Safari etc, older browsers
	// Some don't have native DOMParser either fallback defined bellow.
	try {
		var xmldata = ( new DOMParser() ).parseFromString( str, "text/xml" );
	} catch ( e ) {
		mw.log( 'XML parse ERROR: ' + e.message );
		return false;
	}
	return xmldata;
}