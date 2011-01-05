/**
* Base remote search Object.
* provides the base class for the other search system to extend.
*/
mw.addMessages( {
	"mwe-imported_from" : "$1 imported from [$2 $3]. See the original [$4 resource page] for more information.",
	"mwe-import-description" : "$1, imported from $2"
} );

/**
* rsd_default_rss_item_mapping
*
*  @key is name of resource variable
*  @value is where to find the value in the item xml
*
*  *value format:*
*  . indicates multiple tags
*  @ separates the tag from attribute list
*  {.}tag_name@{attribute1|attribute2}
*
* Also see mapAttributeToResource function bellow
*
* FIXME should switch this over to something like Xpath if we end up parsing a lot of rss formats
*/
var rsd_default_rss_item_mapping = {
	'poster'	: 'media:thumbnail@url',
	'roe_url'	: 'media:roe_embed@url',
	'person'	: 'media:person@label|url',
	'parent_clip':'media:parent_clip@url',
	'bill'		: 'media:bill@label|url',
	'title'		: 'title',
	'link'		: 'link',
	'desc'		: 'description',
	// multiple items
	'category'	: '.media:category@label|url'
};

var baseRemoteSearch = function( options ) {
	this.init( options );
};
baseRemoteSearch.prototype = {

	// Number of completed requests
	completed_req:0,

	// Number of requests
	num_req:0,

	// ResultsObj holds the array of results
	resultsObj: { },

	// Default search result values for paging:
	offset			 :0,
	limit			: 30,
	more_results	: false,
	num_results		: 0,

	/**
	* Initialize the baseRemoteSearch
	* @param {Object} options The set of options for the remote search class
	*/
	init: function( options ) {
		mw.log( 'mvBaseRemoteSearch:init' );
		for ( var i in options ) {
			this[i] = options[i];
		}
		return this;
	},

	getResourceFromUrl: function( url, callback ){
		mw.log("Error getResourceFromUrl must be implemented by remoteSearch provider");
	},

	/**
	* Base search results
	* Does some common initialisation for search results
	* @param {String} search_query Text search string
	*/
	getSearchResults: function( search_query , callback ) {
		// Empty out the current results before issuing a request
		this.resultsObj = { };

		// Do global getSearchResults bindings
		this.last_query = search_query;
		this.last_offset = this.provider.offset;

		// Get the provider specifc results
		this.getProviderResults( search_query , callback);
	},

	/**
	* getProviderResults abstract method
	*/
	getProviderResults: function( searchQuery , calback) {
		mw.log( 'Error: getProviderResults not set by provider' );
		callback( 'error-provider' );
	},

	/**
	* Clears Results
	*/
	clearResults: function() {
		this.resultsObj = { };
		this.last_query = '';
	},

	/*
	* Parses and adds video rss based input format
	*
	* @param {XML Nodes} data the data to be parsed
	* @param {String} provider_url the source url (used to generate absolute links)
	*/
	addRSSData: function( data , provider_url ) {
		mw.log( 'f:addRSSData' );
		var _this = this;
		var http_host = '';
		var http_path = '';
		if ( provider_url ) {
			pUrl = mw.parseUri( provider_url );
			http_host = pUrl.protocol + '://' + pUrl.authority;
			http_path = pUrl.directory;
		}
		var items = data.getElementsByTagName( 'item' );
		// mw.log('found ' + items.length );
		$j.each( items, function( inx, item ) {
			var resource = { };
			for ( var attr in rsd_default_rss_item_mapping ) {
				_this.mapAttributeToResource( resource, item, attr );
			}
			// Make relative urls absolute:
			var url_param = new Array( 'src', 'poster' );
			for ( var j = 0; j < url_param.length; j++ ) {
				var p = url_param[j];
				if ( typeof resource[p] != 'undefined' ) {
					if ( resource[p].substr( 0, 1 ) == '/' ) {
						resource[p] = http_host + resource[p];
					}
					if ( mw.parseUri( resource[ j ] ).host == resource[p] ) {
						resource[p] = http_host + http_path + resource[p];
					}
				}
			}
			// Force a mime type. In the future generalize for other RSS feeds
			resource['mime'] = 'video/ogg';
			// Add pointer to parent search obj:( this.provider.limit )? this.provider.limit : this.limit,

			resource['pSobj'] = _this;

			// Set target_resource_title
			_this.updateTargetResourceTitle( resource );

			// add the result to the result set:
			_this.resultsObj[ inx ] = resource;
			_this.num_results++;
		} );
	},

	/*
	* Maps a given attribute to a resource object per mapping defined in
	* rsd_default_rss_item_mapping
	*
	* @param {Object} resource the resource object
	* @param {XML Node} the xml result node
	* @param {attr} the name attribute we are maping to the resource object
	*/
	mapAttributeToResource: function( resource, item, attr ) {
		var selector = rsd_default_rss_item_mapping[ attr ].split( '@' );
		var flag_multiple = ( selector[0].substr( 0, 1 ) == '.' ) ? true : false;
		if ( flag_multiple ) {
			resource[ attr ] = new Array();
			var tag_name = selector[0].substr( 1 );
		} else {
			var tag_name = selector[0];
		}

		var attr_name = null;
		if ( typeof selector[1] != 'undefined' ) {
			attr_name = selector[1];
			if ( attr_name.indexOf( '|' ) != -1 )
				attr_name = attr_name.split( '|' );
		}

		$j.each( item.getElementsByTagName( tag_name ), function ( inx, node ) {
			var tag_val = '';
			if ( node != null && attr_name == null ) {
				if ( node.childNodes[0] != null ) {
					// trim and strip html:
					tag_val = $j.trim( node.firstChild.nodeValue ).replace(/(<([^>]+)>)/ig,"");
				}
			}
			if ( node != null && attr_name != null ) {
				if ( typeof attr_name == 'string' ) {
					tag_val = $j.trim( $j( node ).attr( attr_name ) );
				} else {
					var attr_vals = { };
					for ( var j in attr_name ) {
						if ( $j( node ).attr( attr_name[j] ).length != 0 )
							attr_vals[ attr_name[j] ] = $j.trim( $j(node).attr( attr_name[j]) ).replace(/(<([^>]+)>)/ig,"");
					}
					tag_val = attr_vals ;
				}
			}
			if ( flag_multiple ) {
				resource[ attr ].push( tag_val )
			} else {
				resource[ attr ] = tag_val;
			}
		} );
		// Nothing to return we update the "resource" directly
	},

	/**
	* Get the html representation of the resource Object parameter
	*
	* @param {Object} resource Resource Object to get embed HTML from
	* @param {Object} options Embed HTML options can include:
	* 	'width', 'height' and 'max_height'
	*/
	getEmbedHTML: function( resource , options ) {
		if ( !options )
			options = { };

		// Set up the output var with the default values:
		if(! options.width ) {
			options.width = resource.width;
		}

		if(! options.height ) {
			options.height = resource.height;
		}

		var outHtml = '';
		if ( ! options['max_width'] ) {
			options['max_width'] = 500;
		}
		options.width = ( options.max_width > resource.width ) ? resource.width : options.max_width;
		options.height = ( resource.height / resource.width ) * options.width;

		options.style = '';
		if( options.height ) {
			options.style += 'height:' + parseInt( options.height ) + 'px;';
		}
		if( options.width ) {
			options.style += 'width:' + parseInt( options.width ) + 'px;';
		}
		// Check for image
		if ( resource.mime.indexOf( 'image/' ) != -1 ) {
			return this.getImageEmbedHTML( resource, options );
		}

		// Assume audio or video

		// Setup the attribute html
		// NOTE: Can't use jQuery builder for video element, ( does not work consistently )
		var attributes = ( options['id'] ) ? ' id = "' + options['id'] + '" ': '';
		attributes+=	'src="' + mw.escapeQuotesHTML( resource.src ) + '" ' +
				// Set kskin, NOTE: should be config option
				'class="kskin" ' +
				'style="' + options.style + '" ' +
				'poster="' + mw.escapeQuotesHTML( resource.poster ) + '" '+
				'type="' + mw.escapeQuotesHTML( resource.mime ) + '" ';


		// Add the api title key if available:
		if( resource.titleKey ) {
			attributes+= 'apiTitleKey="' +
				mw.escapeQuotesHTML( resource.titleKey.replace('File:', '') ) + '" ';
		}

		// Add the commons apiProvider if the resource is from commons
		// ( so that subtitles can be displayed / edited )
		if( resource.pSobj.provider.id == 'wiki_commons'
			|| resource.commonsShareRepoFlag
		){
			attributes+= 'apiProvider="commons" ';
		}

		mw.log( 'baseRemoteSearch::getEmbedHTML::audio or video:' + attributes );
		if ( resource.mime == 'application/ogg' || resource.mime == 'video/ogg' ) {
			return '<video ' + attributes + '></video>';
		}

		if ( resource.mime == 'audio/ogg' ) {
			return '<audio ' + attributes + '></audio>';
		}

		// No output give error:
		mw.log( "ERROR:: no embed code for mime type: " + resource.mime );
		return 'Error missing embed code for: ' + mw.escapeQuotesHTML( resource.mime );
	},

	/*
	* Wrap embed html with description div
	*/
	getEmbedWithDescription: function( resource , options ){
		// Return the output.
		return this.wrapHtmlDesc(
			resource,
			options,
			this.getEmbedHTML( resource, options )
		);
	},

	/**
	 * Wrap output html with resource description links
	 * @param {Object} resource Resource object
	 * @param {Object} options Resource description options
	 * @param {String} outHtml Html to be wrapped.
	 */
	wrapHtmlDesc: function( resource, options, outHtml ) {
		var stripedTitle = resource.title.replace( /File:|Image:|.jpg|.png|.gif|.ogg|.ogv|.oga|.svg/ig, '');

		if( !options ){
			options = {};
		}
		// NOTE should add "size" to insert control
		if( !options.width ){
			options.width = ( resource.width > 600 )? 600 : resource.width;
		}

		var $titleLink = $j( '<a />' )
		.attr({
			'title' : stripedTitle,
			'href' : resource.link
		})
		.text( stripedTitle )

		var providerTitle = gM('rsd-' + this.provider.id + '-title');

		$providerLink = $j( '<a />')
		.attr({
			'href' : this.provider.homepage,
			'title' : providerTitle
		})
		.text( providerTitle )

		$importResourceDiv = $j('<div />')
		.addClass ( "mw-imported-resource" )
		.css({
			'width':parseInt( options.width ) + 'px'
		})
		.html(
			outHtml
		)
		.append(
			gM( 'mwe-import-description', [$titleLink, $providerLink])
		)
		// return the $importResourceDiv html:
		return $j('<div />').append( $importResourceDiv ).html();
	},
	/**
	* Get the embed html specifically for an image type resource Object.
	*
	* @param {Object} resource Resource to get embed html from
	* @param {Object} options Embeding options
	*/
	getImageEmbedHTML:function( resource, options ) {
		// if crop is null do base output:
		var $img = $j('<img />')
		.attr({
			'src' : resource.edit_url,
			'style' : options.style
		});
		if( options['id'] ) {
			$img.attr( 'id', options['id'] );
		}
		if ( resource.crop == null ) {
			return $j('<div />').append( $img ).html();
		}
		// Else do crop output:
		$cropHtml = $j('<div />')
			.css({
				'width' : resource.crop.w,
				'height' : resource.crop.h,
				'overflow' : 'hidden',
				'position' : 'relative'
			})
			.append(
				$j('<div />')
				.css({
					'position' : 'relative',
					'top' : '-' + resource.crop.y,
					'left': '-' + resource.crop.x
				})
				.append( $img )
			)
		return $j('<div />').append( $cropHtml ).html();
	},

	/**
	* Get an image object from a requested transformation via callback
	* ( letting api search implementations query the remote server for a
	*  given transformation )
	*
	* By default just return the existing image.
	*
	* @param {Object} resource Not used in base method
	* @param {Object} size Not used in base method
	* @param {Function} callbcak Function called with returned image Object
	*/
	getImageObj:function( resource, size, callback ) {
		callback( {
			'url' : resource.poster
		} );
	},

	/**
	* Get the inline wikiText description of the resource Object
	*/
	getInlineDescWiki:function( resource ) {
		// return striped html & trim white space
		if ( resource.desc )
			return $j.trim( resource.desc.replace(/(<([^>]+)>)/ig,"") );
		// No Description available:
		return '';
	},
	/**
	* Get the license wikiText tag for a given resource Object.
	*
	* By default license permission wiki text is cc based template mapping
	* (does not confirm the templates actually exist)
	*/
	getPermissionWikiTag: function( resource ) {
		if ( !resource.license )
			return '';// no license info

		// First check if we have a special license template tag already set:
		if( resource.license_template_tag )
			return '{{' + resource.license_template_tag + '}}';

		// Check that its a defined creative commons license key:
		if ( this.rsd.licenses.cc.licenses[ resource.license.key ] != 'undefined' ) {
			return '{{Cc-' + resource.license.key + '}}';
		} else if ( resource.license.lurl ) {
			return '{{Template:External_License|' + resource.license.lurl + '}}';
		}

	},

	/**
	* Get the resource import description text
	*
	* @param {Object} resource Resource to get description of
	*/
	getImportResourceDescWiki:function( resource ) {
		return gM( 'mwe-imported_from', [resource.title, this.provider.homepage, gM('rsd-' + this.provider.id + '-title'), resource.link] );
	},

	/**
	* Get any extra wikitext description for the given resource object.
	* For content outside of the main template description,
	* like categories or additional wikitext notes.
	*
	* By default its an empty string.
	*/
	getExtraResourceDescWiki:function( resource ) {
		return '';
	},

	/**
	* Get an image transformation
	* by default it just return the poster
	*
	* @param {Object} resource Resource for image transformation
	* @param {Object} options Transformation options
	*/
	getImageTransform:function( resource, options ) {
		return resource.poster;
	},

	/**
	* Adds additional resource information from an embedding instance.
	*
	* @param {Object} resource Resource to add embeded info to
	* @param {String} embed_id Id of embed object
	*/
	addEmbedInfo : function( resource, embed_id ) {
		return resource;
	},

	/**
	* Adds resource info with a callback
	*
	* Usefull for async grabbing extra info that is not available in the initial
	* search results api request.
	*
	* For example see archive.org extra resource query
	*
	* @param {Object} resource Resource to add information to
	* @param {Function} callback Callback function once extra resource info has been added
	*/
	addResourceInfoCallback:function( resource, callback ) {
		callback();
	},

	/**
	* Get the wiki embed code for a given resource object
	*
	* @param {Object} resource Resource to get embed wiki code for.
	*/
	getEmbedWikiCode:function( resource ) {
		var layout = ( resource.layout ) ? resource.layout : "right";
		var o = '[[' + this.rsd.canonicalFileNS + ':' + resource.target_resource_title + '|thumb|' + layout;

		if ( !resource.target_width && resource.width ) {
			resource.target_width = ( resource.width < 640 ) ? resource.width: '640';
		}

		if ( resource.target_width )
			o += '|' + resource.target_width + 'px';

		if ( resource.inlineDesc )
			o += '|' + $j.trim( resource.inlineDesc );

		o += ']]';
		return o;
	},

	/**
	* Updates / normalizes the target_resource_title
	*
	* @parma {Object} resource Resource to update title on.
	*/
	updateTargetResourceTitle:function( resource ) {
		if( resource.titleKey ) {
			resource.target_resource_title = resource.titleKey.replace( /^(File:|Image:)/ , '' ).replace(':', '');
			resource.target_resource_title = this.provider.resource_prefix + resource.target_resource_title;
		}
	},

	/**
	* Utily to convert from mime type to extension
	*/
	getMimeExtension: function( mime ) {
		if( mime == 'video/ogg' || mime == 'application/ogg' )
			return 'ogv';
		if( mime == 'audio/ogg' )
			return 'oga';
		if( mime == 'image/jpeg' )
			return 'jpg';
		if( mime == 'image/png' )
			return 'png';
		if( mime == 'image/svg+xml' )
			return 'svg';
	}
}
