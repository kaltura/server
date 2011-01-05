/*
 * Kaltura aggregated search:
 */

mw.addMessages( {
	"rsd-media-filter-title": "Media",
	"rsd-media-filter-videos": "Videos",
	"rsd-media-filter-images": "Images",
	"rsd-provider-filter-title": "Providers"
} );

var kalturaFilters = function ( options ) {
	this.init( options );
}

kalturaFilters.prototype = {
		// List of filters
		filterList: {},

		// A callback function to be called once filter changes are updated
		filterChangeCallBack: function() {},

		// Flag to reset the filter set
		resetRequired: false,

		/**
		* Constructor
		* @param Object options Set of options for the search
		*/
		init: function( options ) {
			this.resetFilters();
		},

		resetFilters: function() {
			this.filterList = {};
			this.buildFilter('media',
					gM( 'rsd-media-filter-title' ), {
				movie: gM ( 'rsd-media-filter-videos' ),
				image: gM ( 'rsd-media-filter-images' )
			});

			this.buildFilter('providers',
					gM( 'rsd-provider-filter-title' ), {
				wiki_commons: gM( 'rsd-wiki_commons-title'),
				archive_org: gM( 'rsd-archive_org-title' ),
				metavid: gM( 'rsd-metavid-title' ),
				flickr: gM( 'rsd-flickr-title' )
			});

		},

		/**
		 * Returns an array of selected/deselected option keys
		 *
		 * @param {String} ID of the requested filter (e.g. media, providers)
		 * @param {Boolean} if set, this will return deselected values instead of
		 * 					selected values.
		 */
		getFilterValues: function( filterID, deselected ) {

			result = new Array();

			optionsList = this.filterList[ filterID ].options;

			for ( option in optionsList ) {
				// Run a XOR to produce correct inclusion/exclusion in all scenarios.
				if ( optionsList[ option ].selected? !deselected : deselected ) {
					result.push( option );
				}
			}

			return result;
		},

		buildFilter: function( filterID, filterTitle, filterOptions ) {
			var options = {};
			for ( option in filterOptions ) {
				options[ option ] = {};
				options[ option ].text = filterOptions[ option ];
				options[ option ].selected = true;
			}

			var filterEntry = {};
			filterEntry.title = filterTitle;
			filterEntry.options = options;

			this.filterList[ filterID ] = filterEntry;
		},

		/**
		 * Create an HTML representation of the available search filters and append
		 * them to the given element.
		 *
		 *  @return {Object} The base element to which HTML items should be
		 *  appended.
		 */
		getHTML: function() {
			var _this = this;
			mw.log( 'f: populateFilterContainer ' );

			$filtersContainer = $j( '<div />' );

			for (filter in this.filterList) {
				$filtersContainer.append(
					this.getFilterBox( 'rsd_' + filter + '_filter',
						this.filterList[ filter ].title,
						this.filterList[ filter ].options ));
			}

			$selectAll = $j( '<div />' ).text('Select All').addClass('rsd_clickable')
				.attr( {
					id: 'rsd_select_all'
				} )
				.click( function() {
					$j('input[type=checkbox]', $filtersContainer).attr('checked',true);
					// TODO: avoid code duplication (with individual click event).

					_this.resetFilters();
					// Request a paging reset
					_this.resetRequired = true;
					_this.filterChangeCallBack();
				});

			$filtersContainer.append($selectAll);

			return $filtersContainer;
		},

		/**
		 * Creates a single filter box with given selection options
		 *
		 * @id {string} unique id for this filter box an residing elements
		 * @title {string} title of the filter box
		 * @options {array} array of strings describing the options in the filter box
		 *
		 * @return {jQuery element} The filter box
		 *
		 */

		getFilterBox: function( id, title, filterOptions ) {
			_this = this;

			$box = $j( '<div />' ).addClass( 'ui-filter-box' ).attr({
				'id': id
			});

			$title = $j( '<div />' ).addClass( 'ui-filter-title' ).text( title );
			$box.append( $title );

			for (optionID in filterOptions) {
				$option = $j( '<div />' ).addClass( 'ui-filter-option' ).text( filterOptions[ optionID ].text );

				$checkbox = $j( '<input />' )
					.attr( {
						type: 'checkbox',
						name: id + '_' + title + '_' + optionID,
						value: optionID,
						checked: filterOptions[ optionID ].selected
					} )
					.click( function (ID) {
						return function() {
							filterOptions[ ID ].selected = !(filterOptions[ ID ].selected);
							// Request a paging reset
							_this.resetRequired = true;
							_this.filterChangeCallBack();
						};
					}(optionID) );

				$option.prepend( $checkbox );
				$box.append( $option );
			}

			return $box;
		}
};

var kalturaSearch = function ( options ) {
	return this.init( options );
}

kalturaSearch.prototype = {

	// Stores search library pointers
	searchLibs: { },

	/**
	* Initialize the Search with provided options
	*
	* @param {Object} options Initial options for the kalturaSearch class
	*/
	init:function( options ) {
		this.options = options;
		this.filters = new kalturaFilters( options );
		var baseSearch = new baseRemoteSearch( options );
		for ( var i in baseSearch ) {
			if ( typeof this[i] == 'undefined' ) {
				this[i] = baseSearch[i];
			} else {
				this['parent_' + i] = baseSearch[i];
			}
		}

		return this;
	},

	/**
	* Get the Search results setting _loading flag to false once results have been added
	*
	* Runs an api call then calls addResults with the resulting data
	* @param {String} search_query Text search string
	*/
	getProviderResults: function( search_query, callback ) {
		var _this = this;

		// Check if the filter requires a paging reset.
		if ( this.filters.resetRequired ) {
			this.provider.offset = 0;
			this.filters.resetRequired = false;
		}

		// Setup the request:
		var request = {
			's' : search_query,
			'page' : this.provider.offset/this.provider.limit + 1
		};

		// Add optional parameters
		var media = this.filters.getFilterValues( 'media', false );
		var providers = this.filters.getFilterValues( 'providers', true );

		if ( media.length > 0 ) {
			request[ 'media' ] = media.join( ',' );
		}

		if ( providers.length > 0 ) {
			request[ 'disable' ] = providers.join( ',' );
		}

		mw.log( "Kaltura::getProviderResults query: " + request['s'] + " page: " + request['page']);
		mw.getJSON( this.provider.apiUrl + '?callback=?', request, function( data ) {
			_this.addResults( data );
			callback( 'ok' );
		} );
	},

	/**
	* Adds results from kaltura api data response object
	*
	* @param {Object} response data
	*/
	addResults:function( data ) {
		var _this = this;
		this.provider_libs = { };

		if ( data ) {

			// Display option for more results as long as results are coming in
			this.more_results = ( data.length == this.limit )

			_this.resultsObj = {};
			this.num_results = 0;

			for ( var result_id in data ) {
				var result = data[ result_id ];

				// Skip the resource if the license is not compatible
				if( result.license_url && ! _this.rsd.checkCompatibleLicense( result.license_url ) ) {
					continue;
				}


				// Update mappings:
				result[ 'poster' ] = result[ 'thumbnail' ];
				result[ 'pSobj' ] = _this;
				result[ 'link' ] = result[ 'item_details_page' ];

				var fileExtension = _this.getMimeExtension( result[ 'mime' ] );
				result[ 'titleKey' ] = result[ 'titleKey' ] || ( result[ 'title' ] + '.' + fileExtension );

				if ( result.license_url ) {
					result[ 'license' ] = this.rsd.getLicenseFromUrl( result.license_url );
				}

				this.num_results++;
				_this.resultsObj[ result_id ] = result;

			}
		}
	},

	/**
	* Return image transform via callback
	* Maps the image request to the proper search library helper
	*
	* @param {Object} resource Resource object
	* @param {Number} size Requested size
	* @param {Function} callback Callback function for image resource
	*/
	getImageObj: function( resource, size, callback ) {
		var _this = this;
		this.getSerachLib( resource.content_provider_id, function( searchLib ) {
			searchLib.getImageObj( resource, size, callback );
		});
	},

	/*
	* Get extra resource info via a library specific callback
	* NOTE: this info should be included in the original kaltura search results
	*/
	addResourceInfoCallback: function( resource, callback ) {
		mw.log('Kaltura: addResourceInfoCallback');
		this.getSerachLib( resource.content_provider_id, function( searchLib ) {
			searchLib.addResourceInfoCallback( resource, callback );
		});
	},

	/**
	* Get and load provider via id
	* @param {String} provider_id The id of the content provider
	* @param {Function} callback Function to call once provider search lib is loaded
	*	callback is passed the search object
	*/
	getSerachLib: function( provider_id, callback ) {
		var _this = this;
		// Check if we already have the library loaded:
		if( this.searchLibs[ provider_id ] ) {
			callback ( this.searchLibs[ provider_id ] );
			return ;
		}
		// Else load the provider lib:
		var provider = this.rsd.content_providers [ provider_id ];
		mw.load( provider.lib + 'Search', function() {
			//Set up the search lib options
			var options = {
				'provider': provider,
				// Same remote search driver as KalturaSearch
				'rsd': _this.rsd
			}
			_this.searchLibs[ provider_id ] = new window[ provider.lib + 'Search' ]( options );
			callback ( _this.searchLibs[ provider_id ] );
		} );
	}
};
