/**
* mediaWiki search implementation
*/
var mediaWikiSearch = function( options ) {
	this.init( options );
};
mediaWikiSearch.prototype = {

	/**
	* Inherits the base search object and passes along options
	* @constructor
	*/
	init: function( options ) {
		// init base class and inherit:
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
	* Adds a resource by its Title
	*
	* @param {String} title Title of the resource to be added (in the form of a wgPageName)
	* @param {Function} callback Function called once title resource acquired
	*/
	getByTitle:function( title , callback ) {

		mw.log( "MediaWikiSearch:: getByTitle:" + title );

		var _this = this;

		var request = {
			'titles' : 'File:' + title.replace(/File:|Image:/ig, ''),
			'prop' : 'imageinfo|revisions|categories',
			'iiprop' : 'url|mime|size|metadata',
			'iiurlwidth' : parseInt( this.rsd.thumb_width ),
			'rvprop' : 'content',
			'redirects' : true
		}
		mw.getJSON( this.provider.apiUrl, request, function( data ) {
			// call addSingleResult
			callback( _this.addSingleResult( data ) );
		});
	},

	/**
	 * Add a resource by its Url
	 *
	 * @param {String} url The url of the resource to be added
	 * @param {Function} callback Function called once the resource is acquired
	 */
	getResourceFromUrl: function( url, callback ){
		var title = this.getTitleKeyFromMwUrl( url );
		if( !title) {
			callback( false );
			return ;
		}
		this.getByTitle(title , callback );
	},

	/**
	 * Does best effort to get the title key from a mediawiki url
	 *
	 * @param {String} url The url for which we want to find the title
	 * @return {String} The title in a form that should mirror wgPageName
	 */
	getTitleKeyFromMwUrl: function( url ){
		// try for title key param
		var titleKey = mw.parseUri( url ).queryKey['title'];
		if( titleKey ){
			return decodeURIComponent( titleKey ).replace( / /g, "_");
		}
		// else try for title url map
		titleKey = url.replace( this.provider.detailsUrl.replace( '$1', ''), '' );
		if( titleKey != url ){
			return decodeURI(titleKey).replace( / /g, "_" );
		}
		mw.log("Error: mediaWikiSearch:: getResourceFromUrl could not get title form url: " + url );
		return false;
	},

	/**
	* Get recent upload by user and add them as results
	*
	* @param {String} user Name of the user
	* @param {Function} callback Function to call once user upload list has been populated
	*/
	getUserRecentUploads: function( user, callback, timeoutCallback ) {
		var _this = this;
		var request = {
			'list' : 'recentchanges',
			'rcnamespace' : 6, // only files
			'rcuser' : user,
			'rclimit' : 10 // get last 10 uploaded files
		};
		mw.getJSON( this.provider.apiUrl, request, function( data ) {
			var titleSet = { };
			var titleStr = ''
			var pound = '';
			// loop over the data and group by title
			if ( data.query && data.query.recentchanges ) {
				for ( var i=0; i < data.query.recentchanges.length; i++ ) {
					var rc = data.query.recentchanges[i];
					if ( !titleSet[ rc.title ] ) {
						titleSet[ rc.title ] = true;
						titleStr += pound + rc.title;
						pound = '|';
					}
				}
			}
			// Run the actual query ( too bad we can't use recentchanges as a generator )
			// bug 20563
			var resourceQuery = {
				'titles'	: titleStr,
				'prop'		: 'imageinfo|revisions|categories',
				'iiprop'	: 'url|mime|size|metadata',
				'iiurlwidth': parseInt( _this.rsd.thumb_width ),
				'rvprop':'content'
			};
			mw.getJSON( _this.provider.apiUrl, resourceQuery, function( data ) {
				_this.clearResults();
				_this.addResults( data );
				if ( callback )
					callback();
			} );
		}, timeoutCallback );
	},

	/**
	* Get search results
	*
	* @param {String} search_query Text search string
	*/
	getProviderResults: function( search_query, callback ) {
		// Set local ref:
		var _this = this;

		mw.log( 'metavid: getProviderResults for:' + search_query );

		// Build the image request
		var request = {
			'action':'query',
			'generator':    'search',
			'gsrsearch':    search_query ,
			'gsrnamespace': 6, // (only search the "file" namespace (audio, video, images)
			'gsrwhat':      'text',
			'gsrlimit':     this.provider.limit,
			'gsroffset':    this.provider.offset,
			'prop':         'imageinfo|revisions|categories',
			'iiprop':       'url|mime|size|metadata',
			'iiurlwidth':   parseInt( this.rsd.thumb_width ),
			'rvprop':       'content',
			'redirects':    true
		};

		// Do the api request:
		mw.getJSON( this.provider.apiUrl, request, function( data ) {
				// Add result data:
				_this.addResults( data );
				callback();
		} );
	},

	/**
	* Adds a single result to resultsObj and returns the resource
	*
	* @param {Object} data Api resource result data to be transformed into a "resource"
	*/
	addSingleResult: function( data ) {
		return this.addResults( data, true );
	},

	/**
	* Covert api data results into common search resources and insert into the resultsObj
	*
	* @param {Object} data Api resource result data to be transformed into a resources
	* @param {Boolean} returnFirst Flag to return the first added resource
	*/
	addResults:function( data, returnFirst ) {
		mw.log( "f:addResults" );
		var _this = this
		// check if we have
		if ( typeof data['query-continue'] != 'undefined' ) {
			if ( typeof data['query-continue'].search != 'undefined' )
				this.more_results = true;
		}

		// Make sure we have pages to iterate:
		if ( data.query && data.query.pages ) {
			for ( var page_id in data.query.pages ) {
				var page = data.query.pages[ page_id ];

				// Make sure the reop is shared (don't show for now it confusing things)
				// @@todo support remote repository better
				if ( page.imagerepository == 'shared' ) {
					continue;
				}

				// Skip if its an empty or missing imageinfo:
				if ( !page.imageinfo ){
					continue;
				}

				// Get the url safe titleKey from the descriptionurl
				var titleKey = page.title.replace( / /g, "_" );

				var resource = 	{
					'id'		 : page_id,
					'titleKey'	 : titleKey,
					'link'		 : page.imageinfo[0].descriptionurl,
					'title'		 : page.title.replace(/Image:|File:|.jpg|.png|.svg|.ogg|.ogv|.oga/ig, ''),
					'poster'	 : page.imageinfo[0].thumburl,
					'thumbwidth'     : page.imageinfo[0].thumbwidth,
					'thumbheight'    : page.imageinfo[0].thumbheight,
					'width'	         : page.imageinfo[0].width,
					'height'	 : page.imageinfo[0].height,
					'mime'		 : page.imageinfo[0].mime,
					'src'		 : page.imageinfo[0].url,
					'desc'		 : page.revisions[0]['*'],
					// add pointer to parent search obj:
					'pSobj'		 :_this,

					'meta': {
						'categories':page.categories
					}
				};

				for( var i=0; page.imageinfo[0].metadata && i < page.imageinfo[0].metadata.length; i++ ){
					if( page.imageinfo[0].metadata[i].name == 'length' ){
						resource.duration = page.imageinfo[0].metadata[i].value;
					}
				}

				/*
				 //to use once we get the wiki-text parser in shape
				var pObj = mw.parser( resource.desc );
				//structured data on commons is based on the "information" template:
				var tmplInfo = pObj.templates( 'information' );
				resource.desc = tmplInfo.description
				*/

				// Attempt to parse out the description current user desc from the commons template:
				// @@todo these could be combined to a single regEx
				// or better improve the wiki-text parsing and use above
				var desc = resource.desc.match( /\|\s*description\s*=\s*(([^\n]*\n)*)\|\s*source=/i );
				if ( desc && desc[1] ) {
					resource.desc = $j.trim( desc[1] );
					// attempt to get the user language if set:
					if ( typeof wgUserLanguage != 'undefined' && wgUserLanguage ) {
						// for some reason the RegExp object is not happy:
						var reg = new RegExp( '\{\{\s*' + wgUserLanguage + '([^=]*)=([^\}]*)\}\}', 'gim' );
						var langMatch = reg.exec( resource.desc );
						if ( langMatch && langMatch[2] ) {
							resource.desc = langMatch[2];
						} else {
							// Try simple lang template form:
							var reg = new RegExp( '\{\{\s*' + wgUserLanguage + '\\|([^\}]*)\}\}', 'gim' );
							var langMatch = reg.exec( resource.desc );
							if ( langMatch && langMatch[1] ) {
								resource.desc = langMatch[1];
							}
						}
					}
				}

				// Likely a audio clip if no poster and type application/ogg
				// @@todo we should return audio/ogg for the mime type or some other way to specify its "audio"
				if ( ! resource.poster && resource.mime == 'application/ogg' ) {
					resource.mime = 'audio/ogg';
				}
				// Add to the resultObj
				this.resultsObj[page_id] = resource;

				// If returnFirst flag:
				// xxx this is kind of hacky .. we should have abstract getter / adder to result list
				if ( returnFirst ){
					return this.resultsObj[page_id];
				}


				this.num_results++;
				// for(var i in this.resultsObj[page_id]) {
				//	mw.log('added: '+ i +' '+ this.resultsObj[page_id][i]);
				// }
			}
		} else {
			mw.log( 'no results:' + data );
		}
	},

	/*
	* Check request done
	* Used when we have multiple requests to check before formating results.
	*/
	checkRequestDone:function() {
		// Display output if done:
		this.completed_req++;
		if ( this.completed_req == this.num_req ) {
			this.loading = 0;
		}
	},

	/**
	* Get an Image object of requested size from a resource
	*
	* @param {Object} resource Source resource
	* @param {Object} size Requested size: .width and .height
	* @param {Function} callbcak Function to be called once image has been reqeusted
	*/
	getImageObj: function( resource, size, callback ) {
		mw.log( 'mediaWiki: getImageObj' );
		if ( resource.mime == 'application/ogg' ){
			return callback( { 'url':resource.src, 'poster' : resource.url } );
		}

		// This could be depreciated if thumb.php support is standard
		var request = {
			'action':'query',
			'format':'json',
			'titles' : resource.titleKey,
			'prop':'imageinfo',
			'iiprop':'url|size|mime'
		}
		// Set the width:
		if ( size.width ) {
			request['iiurlwidth'] = size.width;
		}

		mw.getJSON( this.provider.apiUrl, request, function( data ) {
			var imObj = { };
			for ( var page_id in data.query.pages ) {
				if( page_id == -1 ) {
					mw.log( 'Error: missing page for title: ' + resource.titleKey )
					continue;
				}

				var iminfo = data.query.pages[ page_id ].imageinfo[0];

				// Store the original width:
				imObj['org_width'] = iminfo.width;

				// Check if thumb size > than image size and is jpeg or png (it will not scale well above its max res)
				if ( ( iminfo.mime == 'image/jpeg' || iminfo == 'image/png' )
					&& iminfo.thumbwidth > iminfo.width
					|| !iminfo.thumburl )
				{
					imObj['url'] = iminfo.url;
					imObj['width'] = iminfo.width;
					imObj['height'] = iminfo.height;
				} else {
					imObj['url'] = iminfo.thumburl;
					imObj['width'] = iminfo.thumbwidth;
					imObj['height'] = iminfo.thumbheight;
				}
			}

			mw.log( 'getImageObj: get: ' + size.width + ' got url:' + imObj.url );
			callback( imObj );
		} );
	},

	/*
	* Gets an inline description of the resource
	*
	* @param {Object} resource Resource to get description of.
	*/
	getInlineDescWiki: function( resource ) {
		var desc = this.parent_getInlineDescWiki( resource );

		// Strip categories for inline Desc: (should strip license tags too but not as easy)
		desc = desc.replace( /\[\[Category\:[^\]]*\]\]/gi, '' );

		// Just grab the description tag for inline desc:
		var descMatch = new RegExp( /Description=(\{\{en\|)?([^|]*|)/ );
		var dparts = desc.match( descMatch );

		if ( dparts && dparts.length > 1 ) {
			desc = ( dparts.length == 2 ) ? dparts[1] : dparts[2].replace( '}}', '' );
			desc = ( desc.substr( 0, 2 ) == '1=' ) ? desc.substr( 2 ): desc;
			return desc;
		}
		// Hackish attempt to strip templates
		desc = desc.replace( /\{\{[^\}]*\}\}/gi, '' );
		// strip any nexted template closures
		desc = desc.replace( /\}\}/gi, '' );
		// strip titles
		desc = desc.replace( /\=\=[^\=]*\=\=/gi, '' );

		// else return the title since we could not find the desc:
		mw.log( 'Error: No Description Tag, Using::' + desc );
		return desc;
	},

	/**
	* Returns the wikitext for embedding the resource in a wiki article
	*
	* @param {Object} resource Resource to get wiki text embed code
	*/
	getEmbedWikiCode: function( resource ) {
			// Set default layout to right justified
			var layout = ( resource.layout ) ? resource.layout:"right"
			// if crop is null do base output:
			if ( resource.crop == null )
				return this.parent_getEmbedWikiCode( resource );
			// Using the preview crop template: http://en.wikipedia.org/wiki/Template:Preview_Crop
			// @@todo should be replaced with server side cropping
			return '{{Preview Crop ' + "\n" +
						'|Image   = ' + resource.target_resource_title + "\n" +
						'|bSize   = ' + resource.width + "\n" +
						'|cWidth  = ' + resource.crop.w + "\n" +
						'|cHeight = ' + resource.crop.h + "\n" +
						'|oTop	= ' + resource.crop.y + "\n" +
						'|oLeft   = ' + resource.crop.x + "\n" +
						'|Location =' + layout + "\n" +
						'|Description =' + resource.inlineDesc + "\n" +
					'}}';
	}
}
