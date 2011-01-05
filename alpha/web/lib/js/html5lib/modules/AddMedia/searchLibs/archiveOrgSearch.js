/*
* Archive.org Search
*
* archive.org uses the solr engine:
* more about solr here:
* http://lucene.apache.org/solr/
*/

var archiveOrgSearch = function ( options ) {
	this.init( options );
}
archiveOrgSearch.prototype = {

	// Archive.org constants:
	downloadUrl : 'http://www.archive.org/download/',
	detailsUrl : 'http://www.archive.org/details/',

	/**
	* Initialize the archiveOrgSearch class.
	* archiveOrgSearch inherits the baseSearch class
	*/
	init:function( options ) {
		var baseSearch = new baseRemoteSearch( options );
		for ( var i in baseSearch ) {
			if ( typeof this[i] == 'undefined' ) {
				this[i] = baseSearch[i];
			} else {
				this['parent_' + i] = baseSearch[i];
			}
		}
	},

	/**
	* Get search results from the api query.
	*
	* @param {String} search_query Text search string
	*/
	getProviderResults: function( search_query, callback ) {

		var _this = this;
		mw.log( 'archive_org:getProviderResults for:' + search_query + ' from: ' + this.provider.apiUrl );


		// For now force (Ogg video) & url based license
		search_query += ' format:(Ogg video)';
		search_query += ' licenseurl:(http\\:\\/\\/*)';
		// Set the page number:
		var page_number = parseInt( this.provider.limit / this.provider.offset ) + 1;
		// Build the request Object
		var request = {
			'q': search_query, // just search for video atm
			'fl[]':"description,title,identifier,licenseurl,format,license,thumbnail",
			'fmt':'json',
			'rows' : this.provider.limit,
			'page' : page_number,
			'xmlsearch' : 'Search'
		}
		mw.getJSON( this.provider.apiUrl + '?json.wrf=?', request, function( data ) {
			_this.addResults( data );
			callback( 'ok' );
		} );
	},
	/**
	* Adds the search results to the local resultsObj
	*
	* @param {Object} data Api result data
	*/
	addResults:function( data ) {
		var _this = this;
		if ( data.response && data.response.docs ) {
			// Set result info:
			this.num_results = data.response.numFound;

			for ( var resource_id in data.response.docs ) {
				var resource = data.response.docs[resource_id];

				// Skip the resource if the license is not compatible
				// ( archive.org does not let us filter the license on search )
				if( ! _this.rsd.checkCompatibleLicense( resource.licenseurl ) ) {
					continue;
				}

				var resource = {
					// @@todo we should add .ogv or oga if video or audio:
					'titleKey'	 :  resource.identifier + '.ogv',
					'id' 		 :  resource.identifier,
					'link'		 : _this.detailsUrl + resource.identifier,
					'title'		 : resource.title,
					'poster'	 : _this.downloadUrl + resource.identifier + '/format=thumbnail',
					'poster_ani' : _this.downloadUrl + resource.identifier + '/format=Animated+Gif',
					'thumbwidth' : 160,
					'thumbheight': 110,
					'desc'		 : resource.description,
					'src'		 : _this.downloadUrl + resource.identifier + '/format=Ogg+video',
					'mime'		 : 'application/ogg',
					// Set the license: (rsd is a pointer to the parent remoteSearchDriver )
					'license'	 : _this.rsd.getLicenseFromUrl( resource.licenseurl ),
					'pSobj'		 :_this

				};
				this.resultsObj[ resource_id ] = resource;
			}
		}
	},
	/**
	* Get media metadata via a archive.org special entry point "avinfo"
	*
	* @param {Object} resource Resource to add metadata to.
	* @param {Function} callbcak Function called once extra metadata is added.
	*/
	addResourceInfoCallback: function( resource, callback ) {
		var _this = this;
		mw.log( 'archiveOrg: addResourceInfoCallback' );
		mw.getJSON(
			_this.downloadUrl + resource.id + '/format=Ogg+video?callback=?',
			{ 'avinfo' : 1 },
			function( data ) {
				if ( data['length'] )
					resource.duration = data['length'];
				if ( data['width'] )
					resource.width = data['width'];
				if ( data['height'] )
					resource.height = data['height'];
				callback();
		} );
	},

	/**
	* Returns html to embed a given result Object ( resource )
	* @param {Object} resource Resource to get embed HTML from.
	* @parma {Object} options Options for the embedding.
	*/
	getEmbedHTML: function( resource , options ) {
		mw.log( 'getEmbedHTML:: ' + resource.poster );
		if ( !options )
			options = { };

		var attributes = ( options['id'] ) ? ' id = "' + options['id'] + '" ': '';

		// Add height width if we have it:
		if( resource.width ) {
			attributes += ' width="'+ parseInt( resource.width ) + '"';
		}
		if( resource.height ) {
			attributes += ' height="' + parseInt( resource.height ) + '"';
		}
		// Add the src
		if( !resource.src ) {
			mw.log("Error: resource missing src");
		}else{
			attributes += ' src="' + resource.src + '"';
		}
		// For now no resource.duration ( oggzchop is not very stable )
		//if ( resource.duration ) {
		//	var src = resource.src + '?t=0:0:0/' + mw.seconds2npt( resource.duration );
		//} else {
		//var src = resource.src;
		//}
		var embedHtml ='';
		if ( resource.mime == 'application/ogg' || resource.mime == 'video/ogg' ) {
			embedHtml = '<video poster="' + resource.poster + '" ' + attributes +
					' type="video/ogg"></video>';
		}else if( resource.mime == 'audio/ogg' ) {
			embedHtml = '<audio ' + attributes + ' type="audio/ogg" ></audio>';
		}
		mw.log("archiveOrg::getEmbedHTML::" + embedHtml );
		return embedHtml;
	}
}
