/**
 * mw.RemoteSearchDriver
 *
 * Provides a base interface for the Add-Media-Wizard
 * supporting remote searching of http archives for free images/audio/video assets
 *
 * Is optionally extended by Sequence Remote Search Driver
 */

mw.addMessages( {
	"mwe-add_media_wizard" : "Add media wizard",
	"mwe-media_search" : "Media search",
	"rsd_box_layout" : "Box layout",
	"rsd_list_layout" : "List layout",
	"rsd_results_desc" : "Results $1 to $2",
	"rsd_results_desc_total" : "Results $1 to $2 of $3",
	"rsd_results_next" : "next",
	"rsd_results_prev" : "previous",
	"rsd_no_results" : "No search results for <b>$1<\/b>",
	"mwe-upload_tab" : "Upload file",
	"rsd_layout" : "Layout:",
	"rsd_resource_edit" : "Edit resource: $1",
	"mwe-resource_description_page" : "Resource description page",
	"mwe-link" : "link",
	"rsd_do_insert" : "Do insert",
	"mwe-cc_title" : "Creative Commons",
	"mwe-cc_by_title" : "Attribution",
	"mwe-cc_nc_title" : "Noncommercial",
	"mwe-cc_nd_title" : "No Derivative Works",
	"mwe-cc_sa_title" : "Share Alike",
	"mwe-cc_pd_title" : "Public Domain",
	"mwe-unknown_license" : "Unknown license",
	"mwe-no-import-by-url" : "This user or wiki <b>cannot<\/b> import assets from remote URLs.<p>Do you need to login?<\/p><p>Is upload_by_url permission set for you?<br \/>Does the wiki have $1 enabled?<\/p>",
	"mwe-no-import-by-url-linktext" : "$wgAllowCopyUploads",
	"mwe-results_from" : "Results from $1",
	"mwe-missing_desc_see_source" : "This asset is missing a description. Please check the [$1 source] and help describe it.",

	"rsd_config_error" : "Add media wizard configuration error: $1",
	"mwe-your-recent-uploads" : "Your recent uploads to $1",
	"mwe-no_recent_uploads" : "No recent uploads",

	"mwe-not-logged-in-uploads" : "You may not be logged in so no recent uploads can be displayed. $1 login and $2",
	"mwe-ie-eye-permision" : "If using Internet Explorer and logged in, you may need to adjust your privacy settings",

	"mwe-loggin-link" : "Please login",
	"mwe-try-again-link" : "try again",

	"mwe-upload-a-file" : "Upload a new file",
	"mwe-upload-a-file-to" : "Upload a new file to $1",

	"mwe-resource_page_desc" : "Resource page description:",
	"mwe-edit_resource_desc" : "Edit wiki text resource description:",
	"mwe-local_resource_title" : "Local resource title:",
	"mwe-watch_this_page" : "Watch this page",
	"mwe-do_import_resource" : "Import resource",
	"mwe-update_preview" : "Update resource page preview",
	"mwe-return-search-results" : "Return to search results",
	"mwe-importing_asset" : "Importing asset",
	"mwe-preview_insert_resource" : "Preview insert of resource: $1",
	"mwe-do-more-modification" : "Do more modification",
	"mwe-checking-resource" : "Checking for resource",
	"mwe-resource-needs-import" : "Resource $1 needs to be imported to $2",

	"mwe-warning-upload-to-commons" : "$1 recommends you upload to Wikimedia Commons, only upload locally after you have read $2",
	"mwe-local-upload-policy-link" : "local upload policy" ,

	"mwe-ftype-svg" : "SVG vector file",
	"mwe-ftype-jpg" : "JPEG image file",
	"mwe-ftype-png" : "PNG image file",
	"mwe-ftype-oga" : "Ogg audio file",
	"mwe-ftype-ogg" : "Ogg video file",
	"mwe-ftype-unk" : "Unknown file format",

	"rsd-wiki_commons-title": "Wikimedia Commons",
	"rsd-wiki_commons": "Wikimedia Commons, an archive of freely-licensed educational media content (images, sound and video clips)",

	"rsd-kaltura-title" : "Kaltura search ( all sources )",
	"rsd-kaltura" : "Kaltura aggregated search for free-licensed media across multiple search providers",

	"rsd-this_wiki-title" : "This wiki",
	"rsd-this_wiki-desc" : "The local wiki",

	"rsd-archive_org-title": "Archive.org",
	"rsd-archive_org-desc" : "The Internet Archive, a digital library of cultural artefacts",

	"rsd-flickr-title" : "Flickr.com",
	"rsd-flickr-desc" : "Flickr.com, an online photo sharing site",
	"rsd-metavid-title" : "Metavid.org",
	"rsd-metavid-desc" : "Metavid.org, a community archive of US House and Senate floor proceedings",

	"rsd-search-timeout" : "The search request did not complete. The server may be down experiencing heavy load. You can try again later"
} );

/**
* default_remote_search_options
*
* Options for initializing the remote search driver
*/
var default_remote_search_options = {

	// The div that will hold the search interface
	'target_container': null,

	// The target button or link that will invoke the search interface
	'target_invoke_button': null,

	// Default id for search target input
	'target_search_input' : '#rsd_q',

	/**
	 * Callback functions:
	 */
	'resourceSelectionCallback' : null,

	'displaySearchResultsCallback' : null,

	/**
	* import_url_mode
	*  Can be 'api', 'autodetect', 'remote_link'
	*  api: uses the mediawiki api to insert the media asset
	*  autodetect: checks for api support before using the api to insert the media asset
	*  remote_link: hot-links the media directly into the page as html
	*/
	'import_url_mode': 'api',

	// The api target can be "local" or the url or remote api entry point
	'upload_api_target': 'local',

	// Name of the upload target
	'upload_api_name': null,

	// Target title used for previews of wiki page usually: wgPageName
	'target_title': null,

	// Edit tools (can be an array of tools or keyword 'all')
	'enabled_tools': 'all',

	// Target text box
	'target_textbox': null,

	// Where output render should go:
	'target_render_area': null,

	// Default search query
	'default_query': null,

	// Canonical namespace prefix for images/ files
	'canonicalFileNS': 'File',

	// Enabled providers can be keyword 'all' or an array of enabled content provider keys
	'enabled_providers': 'all',

	// Enabled license types can any set of
	// 'pd' (public domain), 'by' ( attribution ) , 'sa' (share alike ),
	// 'nd' ( no derivatives )
	// 'nc' ( non-commercial ), 'all' ( all found licenses are "ok")
	'enabled_licenses' : ['pd', 'by', 'sa' ],

	// If the input text should be displayed
	'displaySearchInput' : true,

	// If we should display resource icons
	'displayResourceInfoIcons' : true,

	// If we should display the result format button.
	'displayResultFormatButton': true,

	// Set a default provider
	'default_provider': null,

	// The timeout for search providers ( in seconds )
	'search_provider_timeout': 10
};

/**
* Set the jQuery bindings:
*/
( function( $ ) {

	$.fn.addMediaWizard = function( options, callback ) {
		options['target_invoke_button'] = this.selector;
		window['rsdMVRS'] = new mw.RemoteSearchDriver( options );
		if( callback ) {
			callback( window['rsdMVRS'] );
		}
	}

	$.addMediaWizard = function( options ) {
		$.fn.addMediaWizard ( options, function( amwObj ) {
			// Do the add-media-wizard display
			amwObj.createUI();
		} );
	}

} )( jQuery );

/**
* Set the mediaWiki globals if unset
*/
if ( typeof wgServer == 'undefined' )
	wgServer = '';
if ( typeof wgScriptPath == 'undefined' )
	wgScriptPath = '';
if ( typeof stylepath == 'undefined' )
	stylepath = '';

/**
* Base remoteSearch Driver interface
*/
mw.RemoteSearchDriver = function( options ) {
	return this.init( options );
}

mw.RemoteSearchDriver.prototype = {

	// Result cleared flag
	'results_cleared': false,

	// Current provider stores the current provider
	'current_provider': null,

	// Previous provider stores the previous provider for provider switching when calling search
	// NOTE: can be removed once we clean up "upload" tab abstraction
	'previus_provider': null,

	// Caret position of target text area ( lazy initialized )
	'caretPos': null,

	// Text area value ( lazy initialized )
	'textboxValue': null,

	/** the default content providers list.
	 *
	 * (should be note that special tabs like "upload" and "combined" don't go into the content providers list:
	 * @note do not use double underscore in content providers names (used for id lookup)
	 *
	 * @@todo we will want to load more per user-preference and per category lookup
	 */
	content_providers: {
		/**
		*  Content_providers documentation
		*
		*	@enabled: whether the search provider can be selected
		*
		*	@default: default: if the current provider should be displayed (only one should be the default)
		*
		*	@title: the title of the search provider
		*
		*	@desc: can use html
		*
		* 	@homepage: the homepage url for the search provider
		*
		*	@apiUrl: the url to query against given the library type:
		*
		*	@lib: the search library to use corresponding to the
		*		search object ie: 'mediaWiki' = new mediaWikiSearch()
		*
		*	@tab_img: the tab image (if set to false use title text)
		*		if === "true" use standard location skin/images/{provider_id}_tab.png
		*		if === string use as url for image
		*
		*	@linkback_icon default is: /wiki/skins/common/images/magnify-clip.png
		*
		*	//domain insert: two modes: simple config or domain list:
		*	@local : if the content provider assets need to be imported or not.
		*
		*	@local_domains : sets of domains for which the content is local
		*
		*	@resource_prefix : A string to prepend to the title key
		*
		* 	@check_shared :  if we should check for shared repository asset
		*
	 	*/

		/*
		* Local wiki search
		*/
		'this_wiki': {
			'enabled': 1,
			'apiUrl': ( wgServer && wgScriptPath ) ?
				wgServer + wgScriptPath + '/api.php' : null,

			'detailsUrl' : 	( wgServer && wgArticlePath )? wgServer + wgArticlePath : null,

			'lib': 'mediaWiki',
			'homepage' : ( wgServer && wgScript ) ?
				 wgServer + wgScript : null,
			'local': true,
			'tab_img': false
		},

		/**
		* Kaltura aggregated search
		*/
		'kaltura': {
			'enabled': 1,
			'homepage': 'http://kaltura.com',
			'apiUrl': 'http://kaldev.kaltura.com/michael/aggregator.php',

			'detailsUrl' : 	'http://videos.kaltura.com/$1',

			'lib': 'kaltura',
			'resource_prefix' : '',
			'tab_image':false
		},

		/**
		* Wikipedia Commons search provider configuration
		*/
		'wiki_commons': {
			'enabled': 1,
			'homepage': 'http://commons.wikimedia.org/wiki/Main_Page',
			'apiUrl': 'http://commons.wikimedia.org/w/api.php',
			'detailsUrl' : 	'http://commons.wikimedia.org/wiki/$1',

			'lib': 'mediaWiki',
			'tab_img': true,

			// Prefix on imported resources (not applicable if the repository is local or shared)
			'resource_prefix': 'WC_',

			// Commons can be enabled as a remote repo do check shared
			'check_shared': true,

			// List all the domains where commons is local ( lets you avoid an api check for "shared" repo )
			'local_domains': [ 'wikimedia', 'wikipedia', 'wikibooks' ],

			// Specific to wiki commons config:
			// If we should search the title
			'search_title': false
		},

		/**
		* Internet archive search provider configuration
		*/
		'archive_org': {
			'enabled': 1,
			'homepage': 'http://www.archive.org/about/about.php',

			'apiUrl': 'http://www.archive.org/advancedsearch.php',
			'detailsUrl' : 	'http://www.archive.org/details/$1',

			'lib': 'archiveOrg',
			'local': false,
			'resource_prefix': 'AO_',
			'tab_img': true
		},

		/**
		* Flickr search provider configuration
		*/
		'flickr': {
			'enabled': 1,
			'homepage': 'http://www.flickr.com/about/',
			'apiUrl': 'http://www.flickr.com/services/rest/',
			'detailsUrl' : 'http://www.flickr.com/photos/',

			'lib': 'flickr',
			'local': false,
			// Just prefix with Flickr_ for now.
			'resource_prefix': 'Flickr_',
			'tab_img': true
		},

		/**
		* Metavid search provider configuration
		*/
		'metavid': {
			'enabled': 1,
			'homepage': 'http://metavid.org/wiki/Metavid_Overview',
			'apiUrl': 'http://metavid.org/w/index.php?title=Special:MvExportSearch',
			'detailsUrl' : 'http://metavid.org/wiki/Stream:$1',

			'lib': 'metavid',
			'local': false,

			// MV prefix for metavid imported resources
			'resource_prefix': 'MV_',

			// if the domain name contains metavid
			// no need to import metavid content to metavid sites
			'local_domains': [ 'metavid' ],

			// which stream to import, could be mv_ogg_high_quality
			// or flash stream, see ROE xml for keys
			'stream_import_key': 'mv_ogg_low_quality',

			// if running the remoteEmbed extension no need to copy local
			// syntax will be [remoteEmbed:roe_url link title]
			'remote_embed_ext': false,

			'tab_img': true
		},

		/**
		* Special Upload tab provider
		*/
		'upload': {
			'enabled': 1,
			'title': 'Upload'
		}
	},

	/**
	* License define:
	*
	* NOTE: we only support creative commons type licenses
	*
	* Based on listing: http://creativecommons.org/licenses/
	*/
	licenses: {
		'cc': {
			'base_img_url':'http://upload.wikimedia.org/wikipedia/commons/thumb/',
			'base_license_url': 'http://creativecommons.org/licenses/',
			'licenses': [
				'by',
				'by-sa',
				'by-nc-nd',
				'by-nc',
				'by-nd',
				'by-nc-sa',
				'by-sa',
				'pd'
			],
			'license_images': {
				'by': {
					'image_url': '1/11/Cc-by_new_white.svg/20px-Cc-by_new_white.svg.png'
				},
				'nc': {
					'image_url': '2/2f/Cc-nc_white.svg/20px-Cc-nc_white.svg.png'
				},
				'nd': {
					'image_url': 'b/b3/Cc-nd_white.svg/20px-Cc-nd_white.svg.png'
				},
				'sa': {
					'image_url': 'd/df/Cc-sa_white.svg/20px-Cc-sa_white.svg.png'
				},
				'pd': {
					'image_url': '5/51/Cc-pd-new_white.svg/20px-Cc-pd-new_white.svg.png'
				}
			}
		}
	},

	// Width of image resources
	thumb_width: 80,

	// The width of an image when editing
	defaultImageEditWidth: 400,

	// The width of the video embed while editing the resource
	defaultVideoEditWidth: 400,

	// The insert position of the asset (overwritten by cursor position)
	insert_text_pos: 0,

	// Default display mode of search results
	displayMode : 'box', // box or list

	// The ClipEdit Object
	clipEdit: null,

	/**
	* The initialisation function
	*
	* @param {Object} options Options to override: default_remote_search_options
	*/
	init: function( options ) {
		var _this = this;
		mw.log( 'RemoteSearchDriver:init' );

		// Add in a local "id" reference to each provider
		for ( var provider_id in this.content_providers ) {
			this.content_providers[ provider_id ].id = provider_id;
		}
		// Merge in the options
		$j.extend( _this, default_remote_search_options, options );

		// Quick fix for cases where {object} ['all'] is used instead of {string} 'all' for enabled_providers:
		if ( _this.enabled_providers.length == 1 && _this.enabled_providers[0] == 'all' )
			_this.enabled_providers = 'all';

		// Set the current_provider from default_provider
		if( this.default_provider && this.content_providers[ this.default_provider ] ) {
			this.current_provider = this.default_provider;
		}

		// Set up content_providers
		for ( var provider_id in this.content_providers ) {
			var provider = this.content_providers[ provider_id ];
			// Set the provider id
			provider[ 'id' ] = provider_id

			if ( _this.enabled_providers == 'all' && !this.current_provider && provider.apiUrl ) {
				this.current_provider = provider_id;
				break;
			} else {
				if ( $j.inArray( provider_id, _this.enabled_providers ) != -1 ) {
					// This provider is enabled
					this.content_providers[ provider_id ].enabled = true;
					// Set the current provider to the first enabled one
					if ( !this.current_provider ) {
						this.current_provider = provider_id;
					}
				} else {
					// This provider is disabled
					if ( _this.enabled_providers != 'all' ) {
						this.content_providers[ provider_id ].enabled = false;
					}
				}
			}
		}

		// Set the upload target name if unset
		if ( _this.upload_api_target == 'local'
			&& ! _this.upload_api_name
			&& typeof wgServer != 'undefined' )
		{
			_this.upload_api_name = mw.parseUri( wgServer ).host;
		} else {
			// Disable upload tab if no target is avaliable
			this.content_providers[ 'upload' ].enabled = false;
		}


		// Set up the local API upload URL
		if ( _this.upload_api_target == 'local' ) {
			if ( ! mw.getLocalApiUrl() ) {
				$j( this.target_container ).html( gM( 'rsd_config_error', 'missing_local_apiUrl' ) );
				return false;
			} else {
				_this.upload_api_target = mw.getLocalApiUrl();
			}
		}

		// Set up the "add media wizard" button, which invokes this object
		if ( !this.target_invoke_button || $j( this.target_invoke_button ).length == 0 ) {
			mw.log( "RemoteSearchDriver:: no target invocation provided " );
		} else {
			if ( this.target_invoke_button ) {
				$j( this.target_invoke_button )
					.css( 'cursor', 'pointer' )
					.attr( 'title', gM( 'mwe-add_media_wizard' ) )
					.click( function() {
						_this.createUI();
						return false;
					} );
			}
		}
		return this;
	},



	/**
	 * Get license icon html
	 * @param license_key  the license key (ie "by-sa" or "by-nc-sa" etc)
	 *
	 * @return {jQuery element} A div containing the license icons.
	 */
	getLicenseIconHtml: function( licenseObj ) {

		var $licenseLink = $j( '<a />' )
			.attr( {
				'target' : '_new',
				'href' : licenseObj.lurl,
				'title' : licenseObj.title
			} )
			.append( licenseObj.img_html );

		$licenseBox = $j( '<div />' )
			.addClass( 'rsd_license' )
			.attr( {
				title: licenseObj.title
			} )
			.append( $licenseLink );

		return $licenseBox;
	},

	/**
	 * Get License From License Key
	 * @param license_key the key of the license (must be defined in: this.licenses.cc.licenses)
	 */
	getLicenseFromKey: function( license_key, force_url ) {
		// Set the current license pointer:
		var cl = this.licenses.cc;
		var title = gM( 'mwe-cc_title' );
		var imgs = '';
		var license_set = license_key.split( '-' );
		for ( var i = 0; i < license_set.length; i++ ) {
			var lkey = license_set[i];
			if ( !cl.license_images[ lkey ] ) {
				mw.log( "MISSING::" + lkey );
			}

			title += ' ' + gM( 'mwe-cc_' + lkey + '_title' );
			imgs += '<img class="license_desc" width="20" src="' +
				cl.base_img_url + cl.license_images[ lkey ].image_url + '">';
		}
		var url = ( force_url ) ? force_url : cl.base_license_url + cl.licenses[ license_key ];
		return {
			'title': title,
			'img_html': imgs,
			'key': license_key,
			'lurl': url
		};
	},

	/**
	 * Get license key from a license Url
	 *
	 * @param license_url the url of the license
	 */
	getLicenseFromUrl: function( license_url ) {
		// Get the license key:
		var licenseKey = this.getLicenseKeyFromUrl( license_url );
		if( licenseKey ) {
			// Return the license object:
			return this.getLicenseFromKey( licenseKey , license_url );
		}
		// Could not find it return mwe-unknown_license
		return {
			'title': gM( 'mwe-unknown_license' ),
			'img_html': '<span>' + gM( 'mwe-unknown_license' ) + '</span>',
			'lurl': license_url
		};
	},

	/**
	* Get a license key from a url string
	* @parma {String} license_url License url to get key from
	* @return mixed license key or false if not found.
	*/
	getLicenseKeyFromUrl: function( license_url ) {
		// Check for some pre-defined us gov url:
		if ( license_url == 'http://www.usa.gov/copyright.shtml' ||
			license_url == 'http://creativecommons.org/licenses/publicdomain' ) {
			return 'pd';
		}
		// First do a direct lookup check:
		for ( var j = 0; j < this.licenses.cc.licenses.length; j++ ) {
			var jLicense = this.licenses.cc.licenses[ j ];
			// Special 'pd' case:
			if ( jLicense == 'pd' ) {
				var keyCheck = 'publicdomain';
			} else {
				var keyCheck = jLicense;
			}
			// Check the license_url for a given key
			if ( mw.parseUri( license_url ).path.indexOf( '/' + keyCheck + '/' ) != -1 ) {
				return jLicense;
			}
		}
		return false;
	},

	/**
	* Check if the license is compatible with this.enabled_licenses
	* @
	* @retrun true if license is compatible and false if not
	*/
	checkCompatibleLicense: function( license_url ) {
		var licenseKey = this.getLicenseKeyFromUrl( license_url );
		if( ! licenseKey )
			return false;
		var licenseSet = licenseKey.split( '-' );
		for ( var i = 0; i < licenseSet.length; i++ ) {
			if( $j.inArray( licenseSet[i], this.enabled_licenses ) == -1) {
				return false;
			}
		}
		return true;
	},

	/**
	* Get mime type icon from a provided mime type
	* @param str mime type of the requested file
	*/
	getTypeIcon: function( mimetype ) {
		var type = 'unk';
		switch ( mimetype ) {
			case 'image/svg+xml':
				type = 'svg';
				break;
			case 'image/jpeg':
				type = 'jpg'
				break;
			case 'image/png':
				type = 'png';
				break;
			case 'audio/ogg':
				type = 'oga';
			case 'video/ogg':
			case 'application/ogg':
				type = 'ogg';
				break;
		}

		if ( type == 'unk' ) {
			mw.log( "unkown ftype: " + mimetype );
			return '';
		}

		return $j( '<div />' )
			.addClass( 'rsd_file_type ui-corner-all ui-state-default ui-widget-content' )
			.attr( 'title', gM( 'mwe-ftype-' + type ) )
			.text( type );
	},

	/**
	* createUI
	*
	* Creates the remote search driver User Interface
	*/
	createUI: function() {
		var _this = this;
		this.clearTextboxCache();

		// Setup the parent container (if not already created)
		mw.log(" looking for: " + _this.target_container);
		if( !_this.target_container || $j( _this.target_container ).length == 0 ) {
			this.createDialogContainer();
		}else{
			// Empty out the target
			$j( _this.target_container ).empty();
		}

		// Setup remote search dialog & bindings
		this.initDialog();

		// Update the target binding to just un-hide the dialog:
		if ( this.target_invoke_button ) {
			$j( this.target_invoke_button )
				.unbind()
				.click( function() {
					mw.log( "createUI:target_invoke_button: click showDialog" );
					 _this.showDialog();
					 return false;
				 } );
		}
	},

	/**
	* showDialog
	* Displays a dialog
	*/
	showDialog: function() {
		var _this = this;
		mw.log( "showDialog::" );

		// Create the UI
		this.createUI();


		_this.clearTextboxCache();
		var query = _this.getDefaultQuery();

		// Refresh the container if "upload" or "changed query"
		if ( query != $j( this.target_search_input ).val()
			||
			this.current_provider == 'upload' )
		{
			$j( this.target_search_input ).val( query );
			_this.updateResults();
		}
		// $j(_this.target_container).dialog("open");
		$j( _this.target_container ).parents( '.ui-dialog' ).fadeIn( 'slow' );



		// re-center the dialog:
		$j( _this.target_container ).dialog( 'option', 'position', 'center' );
	},

	/**
	* Clears the textbox cache.
	*/
	clearTextboxCache: function() {
		this.caretPos = null;
		this.textboxValue = null;
	},

	/**
	* Get the current position of the text cursor
	*/
	getCaretPos: function() {
		if ( this.caretPos == null ) {
			if ( this.target_textbox ) {
				this.caretPos = $j( this.target_textbox ).getCaretPosition();
			} else {
				this.caretPos = false;
			}
		}
		return this.caretPos;
	},

	/**
	* Get the value of the target textbox.
	*/
	getTextboxValue: function() {
		if ( this.textboxValue == null ) {
			if ( this.target_textbox ) {
				this.textboxValue = $j( this.target_textbox ).val();
			} else {
				this.textboxValue = '';
			}
		}
		return this.textboxValue;
	},

	/**
	* Get the default query from the text selection
	*/
	getDefaultQuery: function() {
		if ( this.default_query == null ) {
			if ( this.target_textbox ) {
				var ts = $j( this.target_textbox ).textSelection();
				if ( ts != '' ) {
					this.default_query = ts;
				} else {
					this.default_query = '';
				}
			}
		}
		// If the query is still empty try the page title:
		if( this.default_query != '' && typeof wgTitle != 'undefined' )
			this.default_query = wgTitle;

		return this.default_query;
	},

	/**
	* Creates the dialog container
	*/
	createDialogContainer: function() {
		mw.log( "createDialogContainer" );
		var _this = this;

		_this.target_container = '#rsd_modal_target';

		// add the parent target_container if not provided or missing
		if ( _this.target_container && $j( _this.target_container ).length != 0 ) {
			//remove old dialog
			$j( _this.target_container ).remove();
		}

		$j( 'body' ).append(
			$j('<div>')
				.attr({
					'id' : 'rsd_modal_target',
					'title' : gM( 'mwe-add_media_wizard' )
				})
				.css("position", 'relative')
		);
		// Get layout
		mw.log( 'width: ' + $j( window ).width() + ' height: ' + $j( window ).height() );

		// Build cancel button
		var cancelButton = {};
		cancelButton[ gM( 'mwe-cancel' ) ] = function() {
			_this.onCancelResourceEdit();
		}

		$j( _this.target_container ).dialog( {
			bgiframe: true,
			autoOpen: true,
			//modal: true,
			width: $j(window).width()-50,
			height: $j(window).height()-50,
			position : 'center',
			draggable: false,
			resizable: false,
			buttons: cancelButton,
			close: function() {
				// if we are 'editing' a item close that
				// @@todo maybe prompt the user?
				_this.onCancelResourceEdit();
				$j( this ).parents( '.ui-dialog' ).fadeOut( 'slow' );
			}
		} )
		//$j( _this.target_container ).dialogFitWindow();

		// Add the window resize hook to keep dialog layout
		$j( window ).resize( function() {
			$j( _this.target_container ).dialogFitWindow();
		} );
	},

	/**
	* Sets up the initial html interface
	*/
	initDialog: function() {
		mw.log( 'f::initDialog' );
		var _this = this;

		var $mainContainer = $j( this.target_container );

		// Add the provider seleciton
		$mainContainer.append( this.createProviderSelection() );

		// Add the searchInput control if it should be displayed:
		if( this.displaySearchInput ){
			$mainContainer.append( this.createSearchInput() );
		};

		this.$resultsContainer = $j('<div />').attr({
			id : "rsd_results_container"
		});

		$mainContainer.append( this.$filtersContainer );
		$mainContainer.append( this.$resultsContainer );

		// Run the default search:
		if ( this.getDefaultQuery() ){
			_this.updateResults();
		}

		// Add bindings
		$j( '#mso_selprovider,#mso_selprovider_close' )
			.unbind()
			.click( function() {
				if ( $j( '#rsd_options_bar:hidden' ).length != 0 ) {
					$j( '#rsd_options_bar' ).animate( {
						'height': '110px',
						'opacity': 1
					}, "normal" );
				} else {
					$j( '#rsd_options_bar' ).animate( {
						'height': '0px',
						'opacity': 0
					}, "normal", function() {
						$j( this ).hide();
					} );
				}
				return false;
			} );

		// Set form bindings
		$j( '#rsd_form' )
			.unbind()
			.submit( function() {
				_this.updateResults();
				// Don't submit the form
				return false;
			} );

		// Setup base cancel button binding
		this.onCancelResourceEdit();
	},
	/**
	 * public function to get enabled content providers
	 */
	getEnabledProviders: function(){
		var enabledProviders = {};
		for ( var providerName in this.content_providers ) {
			var content_providers = this.content_providers;
			var provider = content_providers[ providerName ];
			if ( provider.enabled && provider.apiUrl ) {
				enabledProviders[providerName] = provider;
			}
		}
		return enabledProviders;
	},

	createProviderSelection: function(){
		var _this = this;
		var $providerSelection = $j( '<ul />' )
			.addClass( "ui-provider-selection" );
		// Add enabled search providers.
		$j.each( _this.getEnabledProviders(), function(providerName, provider){
			var $anchor = $j( '<div />' )
				.text( gM( 'rsd-' + providerName + '-title' ) )
				.attr({
					name: providerName
				});
			if ( _this.current_provider == providerName) {
				$anchor.addClass( 'ui-selected' );
			}

			$anchor.click( function() {
				$j( this ).parent().parent().find( '.ui-selected' )
					.removeClass( 'ui-selected' );
				$j( this ).addClass( 'ui-selected' );
				_this.current_provider = $j( this ).attr( "name" );
				// Update the search results on provider selection
				_this.updateResults( _this.current_provider, true );
				return false;
			});

			var $listItem = $j( '<li />' );
			$listItem.append( $anchor );
			$providerSelection.append( $listItem );
		});
		return $providerSelection;
	},
	/**
	 * Creates the search control (i.e. Search textbox, search button, provider filter).
	 * @return A jQuery-generated HTML element ready to be injected in the main container.
	 */
	createSearchInput: function() {
		var _this = this;
		var $controlContainer = $j( '<div />' )
			.addClass( "rsd_control_container" );

		var $searchForm = $j( '<form />' ).attr({
			id : "rsd_form",
			action : "javascript:return false"
		});


		var $searchButton = $j.button({
				icon: 'search',
				text: gM( 'mwe-media_search' )
			})
			.addClass( 'rsd_search_button' )
			.click(function () {
				if( _this.current_provider == 'upload' ){
					_this.current_provider = _this.previus_provider;
				}
				_this.updateResults( _this.current_provider, true );
				return false;
			});

		var $searchBox = $j( '<input />' )
			.addClass( 'ui-corner-all' )
			.attr({
				'type' : "text",
				'tabindex' : 1,
				'value' : _this.getDefaultQuery(),
				'maxlength' : 512,
				'id' : "rsd_q",
				'name' : "rsd_q",
				'size' : 20,
				'autocomplete' : "off"
			})
		// Prevent searching for empty input.
		.keyup(function () {
			if ( $searchBox.val().length == 0 ) {
				$searchButton.addClass('ui-button-disabled');
			}
			else {
				$searchButton.removeClass("ui-button-disabled");
			}
		});

		$searchForm.append( $searchBox );
		$searchForm.append( $searchButton );

		// Add optional upload buttons.
		if ( this.content_providers['upload'].enabled) {
			$uploadButton = $j.button( { icon: 'disk', text: gM( 'mwe-upload_tab' ) })
				.addClass("rsd_upload_button")
				.click(function() {
					// Update the previus_provider to swap back
					if( _this.current_provider != 'upload' ) {
						_this.previus_provider = _this.current_provider;
					}
					_this.current_provider = 'upload';
					_this.showUploadTab( );
					return false;
				});
			$searchForm.append( $uploadButton );
			/*
			// Import functionality not yet supported
			$importButton = $j.button({icon: 'import', text: 'import'})
				.addClass("rsd_import_button");
			.append( $importButton );
			*/
		}

		$controlContainer.append( $searchForm );

		return $controlContainer;
	},

	/**
	* Shows the upload tab loader and issues a call to showUploadForm
	*/
	showUploadTab: function() {
		mw.log( "showUploadTab::" );
		var _this = this;

		// Set the tab container to loading:
		this.$resultsContainer.loadingSpinner();

		// Show the upload form (use the standard module AddMedia.firefogg
		//  This way we get a high cache hit rate by using a general module
		//  and not grouping mw.UploadForm into the upload code set
		mw.load( [ 'AddMedia.firefogg', 'AddMedia.UploadForm' ], function() {
			var provider = _this.content_providers[ 'this_wiki' ];
			// Load this_wiki search system to grab the resource
			_this.loadSearchLib( provider, function() {
				_this.showUploadForm( provider );
			} );
		} );
	},

	/**
	* Once the uploadForm is ready display it for the upload provider
	*
	* @param {Object} provider Provider object for Upload From
	*/
	showUploadForm: function( provider ) {
		var _this = this;

		// Do basic layout form on left upload "bin" on right
		$uploadTableRow = $j('<tr />').append(
			$j('<td />').attr( {
				'valign':'top'
			} )
			.css({
				 'width' : '350px',
				 'padding-right' : '12px'
			})
			.append(
				$j('<h3 />')
				.addClass( 'upload-a-file-msg' )
				.text( gM( 'mwe-upload-a-file' ) ),

				$j('<div />').attr({
					'id': 'rsd_upload_form'
				})
				.loadingSpinner()
			),

			$j('<td />').attr( {
				'valign' : 'top',
				'id':'upload_bin'
			} )
			.loadingSpinner()
		)
		this.$resultsContainer.html(
			$j('<table />').append(
				$uploadTableRow
			)
		);

		this.showRecentUserUploads( '#upload_bin' );

		// Send the upload target menu from UploadForm class
		mw.UploadForm.getUploadMenu( {
			'target': '#rsd_upload_form',
			'uploadTargets' : _this.getUploadTargets(),
			'remoteSearchDriver' : this,
			'selectUploadProviderCb' : function( uploadProvider ){
				_this.$resultsContainer.find( '.upload-a-file-msg' ).html(
					gM( 'mwe-upload-a-file-to', uploadProvider.title )
				);
			}
		} );
	},

	/**
	* Show recent user uploads
	*/
	showRecentUserUploads: function( target ){
		var _this = this;
		var uploadTargets = this.getUploadTargets();

		$j( target ).empty();

		// Show recent uploads for each upload target
		for( var uploadTargetId in uploadTargets ){
			var uploadTarget = uploadTargets[ uploadTargetId ];

			$j( target ).append(
				$j( '<h3 />' )
				.append(
					gM( 'mwe-your-recent-uploads', uploadTarget.title )
				),

				// Add the targetUpload container
				$j('<div />')
				.attr( 'id', 'user-results-' + uploadTargetId )
			)
			// Issue the call to get the recent uploads:
			_this.showUserRecentUploads( uploadTargetId );
		}

	},

	/**
	* Show recent uploads
	* @param {String} uploadTargetId The upload target id
	*/
	showUserRecentUploads: function( uploadTargetId ){
		var _this = this;
		var provider = _this.content_providers[ uploadTargetId ];
		var uploadTargets = _this.getUploadTargets();
		var uploadApiUrl = uploadTargets[ uploadTargetId ].apiUrl ;

		// Set the target to a loadingSpinner
		$j('#user-results-' + uploadTargetId ).loadingSpinner();

		// If the target is not local or we don't have a userName
		// ( try and grab the user name via api call (will be a proxy call if remote) )
		mw.getUserName( uploadApiUrl, function( userName ) {
			if ( userName === false ) {
				var logInLink = uploadApiUrl.replace( 'api.php', 'index.php' ) + '?title=Special:UserLogin';
				// Timed out or proxy not setup ( for remotes )
				$j( '#user-results-' + uploadTargetId ).html(
					gM( "mwe-not-logged-in-uploads",
						$j( '<a />' )
						.attr( {
							'href': logInLink,
							'target' : '_new'
						})
						.append( gM( 'mwe-loggin-link' ) ),

						$j( '<a />' )
						.attr( {
							'href': '#'
						})
						.addClass('try-again')
						.append( gM( 'mwe-try-again-link' ) )
					)
				);

				// If using Internet Exploer it could be IE privacy settings ( aka "evil eye" )
				// http://stackoverflow.com/questions/389456/cookie-blocked-not-saved-in-iframe-in-internet-explorer
				if( $j.browser.msie ){
					$j( '#user-results-' + uploadTargetId ).append(
						$j('<br />'),
						$j('<br />'),

						$j('<span />')
						.text( gM('mwe-ie-eye-permision' ) ),

						$j('<div />' )
						.attr( {
							'alt' : gM('mwe-ie-eye-permision' )
						})
						.addClass( 'rsd_cookies_blocked_MSIE' )
					);
				}

				// Note if we updated gM to return jQuery ojbects then we could
				// bind above
				$j( '#user-results-' + uploadTargetId )
				.find( '.try-again' )
				.click(function(){
					mw.log(" try again:: " + uploadTargetId);
					$j( '#user-results-' + uploadTargetId ).empty().loadingSpinner();
					// Refresh the user uploads
					_this.showUserRecentUploads( uploadTargetId );
				})
			} else {
				_this.showUserRecentUploadsWithUser( uploadTargetId, userName );
			}
		} );
	},

	showUserRecentUploadsWithUser: function( uploadTargetId, userId ){
		var _this = this;
		var provider = this.content_providers[ uploadTargetId ];

		// Setup a local scope function to call the search
		//  ( since we may have to load the provider search lib )
		function doProviderSearch(){
			provider.sObj.getUserRecentUploads( userId, function( ) {
				_this.showResults( {
					'resultsContainer' : $j( '#user-results-' + uploadTargetId ),
					'provider' : provider,
					'hideResultsHeader' : true
				});
			} );
		}

		// Make sure the provider has a search object:
		if (!provider.sObj) {
			this.loadSearchLib( provider, function() {
				doProviderSearch();
			} );
		} else {
			doProviderSearch();
		}
	},

	/**
	* Get the upload targets
	* NOTE: this should be configurable
	*/
	getUploadTargets: function(){
		// Setup upload targets
		var uploadTargets = { };

		// Always include commons upload target:
		// Setup commons upload target
		var commonsProvider = this.content_providers[ 'wiki_commons' ];
		// Check for commons upload page
		var commonsUploadPage = 'Commons:Upload';

		// Add the user language of the commonsUploadPage link
		if( typeof wgUserLanguage != 'undefined' && wgUserLanguage != 'en' ) {
			commonsUploadPage += '/' + wgUserLanguage;
		}

		uploadTargets[ 'wiki_commons' ] = {
			'apiUrl' : commonsProvider.apiUrl,
			'title' : gM( 'rsd-wiki_commons-title'),
			'uploadPage' : commonsProvider.apiUrl.replace( 'api.php', 'index.php' ) + '?title=' + commonsUploadPage
		}

		// If we are ~on commons~ no other links needed:
		if( mw.parseUri( document.URL ).host == 'commons.wikimedia.org' ) {
			return uploadTargets;
		}

		// Check if we have a link to commons for our t-upload toolbox link:
		// ( ie the project does not support local uploads )
		$uploadLink = $j( '#t-upload' ).find('a');
		if( $uploadLink.length
			&& mw.parseUri( $uploadLink.attr('href') ).host == 'commons.wikimedia.org' )
		{
			return uploadTargets;
		}

		// Else this_wiki accepts uploads setup upload links:
		// NOTE this should be set via setConfig calls
		var thisWikiProvider = this.content_providers[ 'this_wiki' ];
		uploadTargets[ 'this_wiki' ] = {
			'apiUrl' : thisWikiProvider.apiUrl,
			 // Add a warning that the user should really target commons:
			'providerDescription' : gM('mwe-warning-upload-to-commons',
				mw.parseUri( thisWikiProvider.apiUrl ).host,
				$j( '<a />' )
				.attr( {
					'href' : $uploadLink.attr('href'),
					'target' : '_new'
				} )
				.text( gM('mwe-local-upload-policy-link') )
			),
			// Unfortunately mediaWiki pages don't expose the title of the wiki
			// Could get in an api request ( just use domain for now)
			'title' : mw.parseUri( thisWikiProvider.apiUrl ).host,
			'uploadPage' : $uploadLink.attr('href')
		}
		return uploadTargets;
	},

	/**
	* Refresh the results container ( based on current_provider var )
	*/
	updateResults: function() {
		if ( this.current_provider == 'upload' ) {
			this.showUploadTab();
		} else {
			this.updateSearchResults( this.current_provider, false );
		}
	},

	/**
	* Show updated search results for a given providerName
	*
	* @param {String} providerName name of the content provider
	* @param {Bollean} resetPaging if the pagging should be reset
	*/
	updateSearchResults: function( providerName, resetPaging ) {
		mw.log( "f:updateSearchResults::" + providerName );

		var draw_direct_flag = true;
		var provider = this.content_providers[ providerName ];

		// Check if we need to update:
		if ( typeof provider.sObj != 'undefined' ) {
			if ( provider.sObj.last_query == $j( this.target_search_input ).val()
				&& provider.sObj.last_offset == provider.offset ) {

				mw.log( 'last query is: ' + provider.sObj.last_query +
					' matches: ' + $j( this.target_search_input ).val() + ' no search needed');

				// Show search results directly
				this.showResults( );
				// Done with processing
				return true;
			}
		}

		// See if we should reset the paging
		if ( resetPaging ) {
			provider.offset = 0;
			if (provider.sObj && provider.sObj.offset) {
				provider.sObj.offset = 0;
			}
		}

		if ( $j ( this.target_search_input ).val().length == 0 ) {
			this.$resultsContainer.empty();
			this.$resultsContainer.text( 'Please insert a search string above.' );
			return;
		}

		// Set the content to loading while we do the search:
		this.$resultsContainer.loadingSpinner();

		// Make sure the search library is loaded and issue the search request
		this.performProviderSearch( provider );
	},

	/*
	* Issue a api request & cache the result this check can be avoided by setting the
	* this.import_url_mode = 'api' | 'form' | instead of 'autodetect' or 'none'
	*
	* @param {function} callback function to be called once we have checked for copy by url support
	*/
	checkForCopyURLSupport: function ( callback ) {
		var _this = this;
		mw.log( 'checkForCopyURLSupport:: ' );

		// See if we already have the import mode:
		if ( this.import_url_mode != 'autodetect' ) {
			mw.log( 'import mode: ' + _this.import_url_mode );
			callback();
		}
		// If we don't have the local wiki api defined we can't auto-detect use "link"
		if ( ! _this.upload_api_target ) {
			mw.log( 'import mode: remote link (no import_wiki_apiUrl)' );
			_this.import_url_mode = 'remote_link';
			callback();
		}
		if ( this.import_url_mode == 'autodetect' ) {
			var request = {
				'action': 'paraminfo',
				'modules': 'upload'
			}
			mw.getJSON( _this.upload_api_target, request, function( data ) {
					_this.checkCopyURLApiResult( data, callback )
			} );
		}
	},

	/**
	* Evaluate the result of an api copyURL permision request
	*
	* @param {Object} data Result data to be checked
	* @param {Function} callback Function to call once api returns value
	*/
	checkCopyURLApiResult: function( data, callback ) {
		var _this = this;
		// Api checks:
		for ( var i=0; i < data.paraminfo.modules[0].parameters.length; i++ ) {
			var pname = data.paraminfo.modules[0].parameters[i].name;
			if ( pname == 'url' ) {
				mw.log( 'Autodetect Upload Mode: api: copy by url:: ' );
				// Check permission too:
				_this.checkForCopyURLPermission( function( canCopyUrl ) {
					if ( canCopyUrl ) {
						_this.import_url_mode = 'api';
						mw.log( 'import mode: ' + _this.import_url_mode );
						callback();
					} else {
						_this.import_url_mode = 'none';
						mw.log( 'import mode: ' + _this.import_url_mode );
						callback();
					}
				} );
				// End the pname search once we found the the "url" param
				break;
			}
		}
	},

	/**
	 * checkForCopyURLPermission:
	 * not really necessary the api request to upload will return appropriate error
	 * if the user lacks permission. or $wgAllowCopyUploads is set to false
	 * (use this function if we want to issue a warning up front)
	 *
	 * @param {Function} callback Function to call with URL permission
	 * @return
	 * 	false callback user does not have permission
	 */
	checkForCopyURLPermission: function( callback ) {
		var _this = this;
		// do api check:
		var request = {
			'meta' : 'userinfo',
			'uiprop' : 'rights'
		};
		mw.getJSON( _this.upload_api_target, request, function( data ) {
			for ( var i=0; i < data.query.userinfo.rights.length; i++ ) {
				var right = data.query.userinfo.rights[i];
				// mw.log('checking: ' + right ) ;
				if ( right == 'upload_by_url' ) {
					callback( true );
					return true; // break out of the function
				}
			}
			callback( false );
		} );
	},

	/**
	* Performs the search for a given content provider
	*
	* Calls showResults once search results are ready
	*
	* @param {Object} provider the provider to be searched.
	*/
	performProviderSearch: function( provider ) {
		var _this = this;
		mw.log( 'f: performProviderSearch ' );
		// First check if we should even run the search at all (can we import / insert
		// into the page? )
		if ( !this.isProviderLocal( provider ) && this.import_url_mode == 'autodetect' ) {
			// provider is not local check if we can support the import mode:
			this.checkForCopyURLSupport( function() {
				_this.performProviderSearch( provider );
			} );
			return false;
		} else if ( !this.isProviderLocal( provider ) && this.import_url_mode == 'none' ) {
			if ( this.current_provider == 'combined' ) {
				// Combined results are harder to error handle just ignore that repo
				provider.sObj.loading = false;
			} else {
				this.$resultsContainer.html (
					gM( 'mwe-no-import-by-url',
						$j('<a />')
						.attr({
							'href' : 'http:\/\/www.mediawiki.org\/wiki\/Manual:$wgAllowCopyUploads',
							'title' : gM( 'mwe-no-import-by-url-linktext' )
						})
						.text( gM( 'mwe-no-import-by-url-linktext' ) )
					)
				);
			}
			return false;
		}

		if (!provider.sObj) {
			this.loadSearchLib( provider, this.getProviderCallback() );
		}
		else {
			this.getProviderCallback()( provider );
		}
	},

	/**
	 * Callback for performing a search, given to providers for provider-activated
	 * searches e.g. filter state changes. This is probably also the future way to
	 * implement "pushing" results.
	 *
	 * The returned callback accepts two arguments.
	 *
	 * The first, mandatory, is the
	 * provider object. This should be curried with the current provider object
	 * before handing over. (i.e. this.curry(this.getProviderCallback(), provider).
	 *
	 * The second, optional, is the current results list to be replaced by a spinner.
	 */
	getProviderCallback: function() {

		var _this = this;

		return function ( provider, $location ) {
			var d = new Date();
			var searchTime = d.getMilliseconds();

			// If we are given a result location, we hide them.
			if ($location) {
				$location.loadingSpinner();
			}

			var d = new Date();
			var context = _this.storeContext( d.getTime() );
			_this.currentRequest = context();
			mw.log( "ProviderCallBack Generated " + context() )
			provider.sObj.getSearchResults( $j( _this.target_search_input ).val() ,
				function( resultStatus ) {
					mw.log( "ProviderCallBack Received " + context() );
					if( _this.currentRequest != context() ) {
						mw.log( "Context mismatch for request " + _this.currentRequest + ' != ' + context );
						// do not update the results this.currentRequest
						// does not match the interface request state.
						return false;
					}
					//else update search results
					_this.showResults();
				}
			);

			// Set a timeout of 20 seconds
			setTimeout( function() {
			}, 20 * 1000 );
		};
	},

	/**
	 * Persists an object via closure to enable later context checking.
	 * This can be used e.g. when sending multiple getJSON requests and
	 * wanting to act only on the last request sent.
	 *
	 * @param {Object} Object to store in context.
	 *
	 * @return {function} A callback to retrieve the context.
	 */
	storeContext: function( contextObject ) {
		var context = contextObject;
		return function() {
			return context;
		}
	},

	/**
	* Loads a providers search library
	*
	* @param {Object} provider content provider to be loaded
	* @param {Function} callback Function to call once provider is loaded
	* ( provider is passed back in callback to avoid possible concurancy issues in multiple load calls)
	*/
	loadSearchLib: function( provider, callback ) {
		var _this = this;
		mw.log( ' loadSearchLib: ' + provider );
		// Set up the library req:
		mw.load( [
			'baseRemoteSearch',
			provider.lib + 'Search'
		], function() {
			mw.log( "loaded lib:: " + provider.lib );
			// Else we need to run the search:
			var options = {
				'provider': provider,
				'rsd': _this
			};
			provider.sObj = new window[ provider.lib + 'Search' ]( options );
			if ( !provider.sObj ) {
				mw.log( 'Error: could not find search lib for ' + provider_id );
				return false;
			}

			// inherit defaults if not set:
			provider.limit = provider.limit ? provider.limit : provider.sObj.limit;
			provider.offset = provider.offset ? provider.offset : provider.sObj.offset;

			callback( provider );
		} );
	},

	/**
	 * Get a resource from a url loads the provider if not already initialized
	 */
	getResourceFromUrl: function ( provider, url, callback){
		if (!provider.sObj) {
			this.loadSearchLib( provider, function( provider ){
				provider.sObj.getResourceFromUrl( url, callback);
			});
		}
		else {
			provider.sObj.getResourceFromUrl( url, callback);
		}
	},

	/**
	 * Get a resource from a titleKey loads the provider if not already initialized
	 */
	getResourceFromTitleKey: function ( provider, title, callback){
		if (!provider.sObj) {
			this.loadSearchLib( provider, function( provider ){
				provider.sObj.getByTitle( title, callback);
			});
		}
		else {
			provider.sObj.getByTitle( title, callback);
		}
	},
	/**
	* Get a resource object from a resource id
	*
	* NOTE: We could bind resource objects to html elements to avoid this lookup
	*
	* @param {String} id Id attribute the resource object
	*/
	getResourceFromId: function( id ) {
		var parts = id.replace( /^res_/, '' ).split( '__' );
		var providerName = parts[0];
		var resIndex = parts[1];

		// Set the upload helper providerName (to render recent uploads by this user)
		if ( providerName == 'upload' )
			providerName = 'this_wiki';

		var provider = this.content_providers[providerName];
		if ( provider && provider['sObj'] && provider.sObj.resultsObj[resIndex] ) {
			return provider.sObj.resultsObj[resIndex];
		}
		mw.log( "ERROR: could not find " + resIndex );
		return false;
	},

	// TODO: turn this into a global helper function.
	curry: function ( fn ) {
		var args = [];
		for (var i = 1, len = arguments.length; i <len; ++i) {
			args.push( arguments[i] );
		};
		return function() {
			fn.apply( window, args );
		};
	},

	/**
	* Show Results and apply bindings
	*
	* @param {Object} options Configuration optiosn can inclue:
	* 'resultsContainer' - {jQuery Object} $resultsContainer The container for the results
	* 'provider' - {Object} provider The search provider to grab results from.
	*/
	showResults: function( options ) {
		var _this = this;

		if( !options ) {
			options = { };
		}

		// Set all the option defaults if not provided:
		var $resultsContainer = ( options.resultsContainer )
			? options.resultsContainer
			: _this.$resultsContainer;

		var provider = ( options.provider )
			? options.provider
			: _this.content_providers[ _this.current_provider ];

		mw.log( 'f:showResults::' + provider.id );
		// Result page structure:
		//
		// resultsContainer
		//   header
		//   resultBody
		//     filter form
		//       filters...
		//     resultList
		//       results...
		//   footer

		var $resultsBody = $j( '<div />' ).addClass( 'rsd_results_body' );
		var $resultsList = $j( '<div />' ).addClass( 'rsd_results_list' );

		// Add the results header:
		$resultsContainer.empty();

		if( ! options.hideResultsHeader ){
			$resultsContainer.append( this.createResultsHeader() )
		}

		// Enable search filters, if the provider supports them.
		if ( provider.sObj.filters && !(provider.disable_filters) ) {
			provider.sObj.filters.filterChangeCallBack =
				this.curry( this.getProviderCallback(), provider, $resultsList );
			$resultsBody.append( provider.sObj.filters.getHTML().attr ({
				id: 'rsd_filters_container'
			}));
		}

		var numResults = 0;

		// Output all the results for the current current_provider
		if ( typeof provider['sObj'] != 'undefined' ) {
			$j.each( provider.sObj.resultsObj, function( resIndex, resource ) {
				$resultsList.append( _this.getResultHtml( provider, resIndex, resource ) );
				numResults++;
			} );
			// Put in the tab output (plus clear the output)
			$resultsList.append( '<div style="clear: both" />' );
		}

		$resultsBody.append( $resultsList );
		$resultsContainer.append( $resultsBody );

		// @@TODO should abstract footer and header ~outside~ of search results
		// to have less leakage with upload tab
		if ( this.current_provider != 'upload' ) {
			$resultsContainer.append( _this.createResultsFooter() );
		}

		mw.log( 'did numResults :: ' + numResults + ' append: ' + $j( this.target_search_input ).val() );

		// Add "no search results" text
		$j( '#rsd_no_search_res' ).remove();
		if ( numResults == 0 ) {
			// NOTE: we should handle no-results with a callback not with condition check
			if( _this.current_provider == 'upload' ) {
			 	$resultsContainer.append(
			 		gM( 'mwe-no_recent_uploads' )
			 	);
			} else {
				$resultsContainer.append(
					gM( 'rsd_no_results', $j( this.target_search_input ).val() )
				) ;
			}
		}
		this.addResultBindings();

		if( typeof this.displaySearchResultsCallback == 'function'){
			this.displaySearchResultsCallback();
		}
	},

	/**
	 * Show failure
	 */
	showFailure : function( resultStatus ) {
		//only one type of resultStatus right now:
		if( resultStatus == 'timeout' )
			$j( '#tab-' + this.current_provider ).text(
				gM('rsd-search-timeout')
			)
	},

	/**
	* Get result html, calls getResultHtmlBox or

	* @param {Object} provider the content provider for result
	* @param {Number} resIndex the resource index to build unique ids
	* @param {Object} resource the resource object
	*/
	getResultHtml: function( provider, resIndex, resource ) {
		if ( this.displayMode == 'box' ) {
			return this.getResultHtmlBox( provider, resIndex, resource );
		}else{
			return this.getResultHtmlList( provider, resIndex, resource );
		}
	},

	/**
	* Get result html for box layout
	*
	* @param {Object} provider the content provider for result
	* @param {Number} resIndex the resource index to build unique ids
	* @param {Object} resource the resource object
	*/
	getResultHtmlBox: function( provider, resIndex, resource ) {

		var $resultBox = $j( '<div />' )
			.addClass( 'mv_clip_box_result rsd_box_result' )
			.attr( {
				id: 'mv_result_' + resIndex
			} )
			.width( this.thumb_width )
			.height( this.thumb_width - 20 );

		// TODO we need to move these images sound_music_icon-80.png etc into the
		// AddMedia module and use the style sheet to refrence them

		// Check for missing poster types for audio
		if ( (resource.mime == 'audio/ogg' || resource.mime == 'application/ogg')
			&& !resource.poster ) {
			resource.poster = mw.getConfig( 'imagesPath' ) + 'sound_music_icon-80.png';
		}

		var $resultThumb = $j( '<img />' )
			.addClass( 'rsd_res_item' )
			.attr( {
				id: 'res_' + provider.id + '__' + resIndex,
				title: resource.title,
				src: provider.sObj.getImageTransform( resource, {
					'width': this.thumb_width
				} )
			} )
			.width( this.thumb_width )
			.height( parseInt( this.thumb_width * ( resource.height / resource.width ) ) )

		$resultBox.append( $resultThumb );

		if ( resource.link && this.displayResourceInfoIcons ) {
			var $resultPageLink = $j( '<div />' )
				.addClass( 'rsd_linkback ui-corner-all ui-state-default ui-widget-content' )
				.append( $j( '<a />' )
							.attr( {
								'target' : '_new',
								'title' : gM( 'mwe-resource_description_page' ),
								'href' : resource.link
							} )
							.append( gM( 'mwe-link' )));

			$resultBox.append( $resultPageLink );
		}

		if ( resource.mime && this.displayResourceInfoIcons ) {
			$resultBox.append( this.getTypeIcon( resource.mime ) );
		}

		// Add license icons if present
		if ( resource.license && this.displayResourceInfoIcons ) {
			$resultBox.append( this.getLicenseIconHtml( resource.license ) );
		}

		$resultBox.append( '<div style="clear: both" />' );

		return $resultBox;
	},

	/**
	* Get result html for list layout
	*
	* @param {Object} provider the content provider for result
	* @param {Number} resIndex the resource index to build unique ids
	* @param {Object} resource the resource object
	*/
	getResultHtmlList:function( provider, resIndex, resource ) {

		var $resultBox = $j( '<div />' )
			.addClass( 'mv_clip_list_result' )
			.attr( {
				id: 'mv_result_' + resIndex
			} )
			.width( '90%' );

		if ( resource.description ) {
			$resultBox.text( resource.description );
		}

		var $resultThumb = $j( '<img />' )
			.addClass( 'rsd_res_item rsd_list_item' )
			.attr( {
				id: 'res_' + provider.id + '__' + resIndex,
				title: resource.title,
				src: provider.sObj.getImageTransform( resource, { 'width': this.thumb_width } )
			} )
			.width( this.thumb_width );

		$resultBox.prepend( $resultThumb );

		// Add license icons if present
		if ( resource.license && this.displayResourceInfoIcons ) {
			$resultBox.append( this.getLicenseIconHtml( resource.license ) );
		}

		$resultBox.append( '<div style="clear: both" />' );

		return $resultBox;
	},

	/**
	* Add result bindings
	*
	* called after results have been displayed
	* Set bindings to showResourceEditor
	*/
	addResultBindings: function() {
		var _this = this;
		$j( '.mv_clip_' + _this.displayMode + '_result' ).hover(
			function() {
				$j( this ).addClass( 'mv_clip_' + _this.displayMode + '_result_over' );
				// Also set the animated image if available
				var res_id = $j( this ).children( '.rsd_res_item' ).attr( 'id' );
				var resource = _this.getResourceFromId( res_id );
				if ( resource.poster_ani )
					$j( '#' + res_id ).attr( 'src', resource.poster_ani );
			}, function() {
				$j( this ).removeClass(
					'mv_clip_' + _this.displayMode + '_result_over' );
				var res_id = $j( this ).children( '.rsd_res_item' ).attr( 'id' );
				var resource = _this.getResourceFromId( res_id );
				// Restore the original (non animated)
				if ( resource.poster_ani )
					$j( '#' + res_id ).attr( 'src', resource.poster );
			}
		);

		// Resource click action: (bring up the resource editor)
		$j( '.rsd_res_item' ).unbind().click( function() {
			var resource = _this.getResourceFromId( $j( this ).attr( "id" ) );

			// xxx These hooks should really be managed via bindings not options like this:
			var showResourceEditor = true;
			if( typeof _this.resourceSelectionCallback == 'function' ){
				showResourceEditor = _this.resourceSelectionCallback( resource );
			}
			if( showResourceEditor ){
				_this.showResourceEditor( resource );
			}
			return false;
		} )
		// Add a "bind" class
		.addClass( 'rsd_res_item_bind' ) ;
	},

	/**
	* Add Resource edit layout and display a loader.
	*/
	addResourceEditLoader: function( ) {
		mw.log()
		var _this = this;
		editWidth = 400;
		// Remove any old instance:
		$j( _this.target_container ).find( '#rsd_resource_edit' ).remove();

		// Hide the results container
		this.$resultsContainer.hide();

		// Set up the interface compoents:
		var $clipEditControl =	$j('<div />')
			.attr( 'id', 'clip_edit_ctrl' )
			.addClass('ui-widget ui-widget-content ui-corner-all')
			.css( {
				'position' : 'absolute',
				'left' : '2px',
				'top' : '5px',
				'bottom' : '10px',
				'width' : ( editWidth + 5 ) + 'px',
				'overflow' : 'auto',
				'padding' : '5px'
			} )
			.loadingSpinner();

		mw.log(" clip edit control ");

		var $clipEditDisplay = $j('<div />')
			.attr( 'id', 'clip_edit_disp' )
			.addClass( 'ui-widget ui-widget-content ui-corner-all' )
			.css({
				'position' : 'absolute',
				'overflow' : 'auto',
				'left' : ( editWidth + 25 ) + 'px',
				'right' :'0px',
				'top' : '5px',
				'bottom' : '10px',
				'padding' : '5px'
			})
			.loadingSpinner();

		// Add the edit layout window with loading place holders
		$j( _this.target_container ).append(
			$j('<div />')
			.attr( 'id', 'rsd_resource_edit' )
			.css( {
				'position' : 'absolute',
				'top' : '0px',
				'left' : '0px',
				'bottom' : '30px',
				'right' : '4px',
				'background-color' : '#FFF'
			} )
			.append(
				$clipEditControl,
				$clipEditDisplay
			)
		);
	},

	/**
	* Get the edit width of a resource
	*
	* @param {Object} resource get width of resource
	*/
	getDefaultEditWidth: function( resource ) {
		var mediaType = this.getMediaType( resource );
		if ( mediaType == 'image' ) {
			return this.defaultImageEditWidth;
		} else {
			return this.defaultVideoEditWidth;
		}
	},

	/**
	* Get the media Type of a resource
	*
	* @param {Object} resource get media type of resource
	*/
	getMediaType: function( resource ) {
		var types = [ 'image', 'audio', 'video'];
		for( var i=0; i < types.length ; i++ ) {
			if ( resource.mime.indexOf( types[i] ) !== -1) {
				return types[i];
			}
		}
		if( resource.mime == 'application/ogg' )
			return 'video'
		// Media type not found:
		return false;
	},

	/**
	* Removes the resource editor
	*/
	removeResourceEditor: function() {
		$j( '#rsd_resource_edit' ).remove();
		$j( '#rsd_edit_img' ).remove();
	},

	/**
	* Show the resource editor
	* @param {Object} resource Resource to be edited
	*/
	showResourceEditor: function( resource ) {
		mw.log( 'f:showResourceEditor:' + resource.title );
		var _this = this;

		// Remove any existing resource edit interface
		_this.removeResourceEditor();
		mw.log(" removed old resource ");

		// Append to the top level of model window:
		_this.addResourceEditLoader();
		mw.log("done adding resource editor");

		var mediaType = _this.getMediaType( resource );
		var targetWidth = _this.getDefaultEditWidth( resource );

		var targetHeight = parseInt( targetWidth * ( resource.height / resource.width ) );

		if( targetHeight > $j('#clip_edit_disp').height() ){
			targetHeight = $j('#clip_edit_disp').height();
			targetWidth = targetHeight * ( resource.width / resource.height);
		}

		//mw.log("org h/w" + resource.width + ' / ' + resource.height +' new w' + width + ' new h:' + height );

		// Update add media wizard title:
		var dialogTitle = gM( 'mwe-add_media_wizard' ) + ': ' +
			gM( 'rsd_resource_edit', resource.title );

		$j( _this.target_container ).dialog( 'option', 'title', dialogTitle );

		mw.log( 'did append to: ' + _this.target_container );

		// check if the size is small
		if( resource.width < targetWidth ){
			targetWidth = resource.width;
			targetHeight = resource.height;
		}		
		
		// issue a loadResourceImage request:
		_this.loadResourceImage(
			resource,	
			{
				'width': targetWidth,
				'height' : targetHeight
			},
			function( img_src ) {
				$j('#clip_edit_disp').empty().append(
					$j( '<img />' )
					.attr( {
						'id' : 'rsd_edit_img',
						'src' : img_src,
						'width': targetWidth,
						'height' : targetHeight
					} )
				);
			}
		);

		// Also fade in the container:
		$j( '#rsd_resource_edit' ).animate( {
			'opacity': 1,
			'background-color': '#FFF',
			'z-index': 99
		} );

		// Show image editor tools
		if ( mediaType == 'image' ) {
			_this.showImageEditor( resource );
		} else if ( mediaType == 'video' || mediaType == 'audio' ) {
			_this.showVideoEditor( resource );
		}
	},

	/*
	* Loads a resource image of set size
	*
	* @param {Object} resource requested resource for higher quality image
	* @param {Object} size the requested size of the higher quality image
	* @param {Function} callback the function to be calle once the image is loaded
	*/
	loadResourceImage: function( resource, size, callback ) {
		mw.log( "loadResourceImage" );
		// Get the image url:
		resource.pSobj.getImageObj( resource, size, function( imObj ) {
			resource['edit_url'] = imObj.url;

			mw.log( "edit url: " + resource.edit_url );
			// Update the resource::
			resource['width'] = imObj.width;
			resource['height'] = imObj.height;

			var width = imObj.width;
			var height = imObj.height;

			// Don't swap it in until its loaded
			var img = new Image();
			// Load the image
			$j( img ).load( function () {
				 // Update changes using the callback
				 callback( resource.edit_url );
			} ).error( function () {
				mw.log( "Error with: " + resource.edit_url );
			} ).attr( 'src', resource.edit_url );
		} );
	},

	/**
	* Do cancel edit callbacks and interface updates.
	*/
	onCancelResourceEdit: function() {
		var _this = this;
		mw.log( 'onCancelResourceEdit' );
		var b_target = _this.target_container + '~ .ui-dialog-buttonpane';
		$j( '#rsd_resource_edit' ).remove();

		// Remove preview if its 'on'
		$j( '#rsd_preview_display' ).remove();

		// Remove resource import if present
		$j( '#rsd_resource_import' ).remove();
		// Restore the resource container:
		this.$resultsContainer.show();

		// Restore the title:
		$j( _this.target_container ).dialog( 'option', 'title', gM( 'mwe-add_media_wizard' ) );
		mw.log( "should update: " + b_target + ' with: cancel' );
		// Restore the buttons:
		$j( b_target )
			.html( $j.btnHtml( gM( 'mwe-cancel' ) , 'mv_cancel_rsd', 'close' ) )
			.children( '.mv_cancel_rsd' )
			.buttonHover()
			.click( function() {
				$j( _this.target_container ).dialog( 'close' );
				return false;
			} );
	},

	/**
	 * Get the control actions for clipEdit with relevant callbacks
	 * @param {Object} provider the provider object to
	 */
	getClipEditControlActions: function( provider ) {
		var _this = this;
		var actions = { };

		actions['insert'] = function( resource ) {
			_this.insertResource( resource );
		}
		// If not directly inserting the resource, support a preview option:
		if ( _this.import_url_mode != 'remote_link' ) {
			actions['preview'] = function( resource ) {
				_this.showPreview( resource )
			};
		}
		actions['cancel'] = function() {
			_this.onCancelResourceEdit()
		}
		return actions;
	},

	/**
	* Clip edit options
	*/
	getClipEditOptions: function( resource ) {
		return {
			'resource' : resource,
			'parent_container': 'rsd_modal_target',
			'target_clip_display': 'clip_edit_disp',
			'target_control_display': 'clip_edit_ctrl',
			'media_type': this.getMediaType( resource ),
			'parentRemoteSearchDriver': this,
			'controlActionsCallback': this.getClipEditControlActions( resource.pSobj.cp ),
			'enabled_tools': this.enabled_tools
		};
	},

	/**
	 * Internal function called by showResourceEditor() to show an image editor
	 * @param {Object} resource Resource for Image Editor display
	 */
	showImageEditor: function( resource ) {
		var _this = this;
		var options = _this.getClipEditOptions( resource );

		mw.load( [
			'mw.ClipEdit',
		 	'mw.style.ClipEdit'
		 ], function() {
			_this.clipEdit = new mw.ClipEdit( options );
		});
	},

	/**
	 * Internal function called by showResourceEditor() to show a video or audio
	 * editor.
	 * @param {Object} resource Show video editor for this resource
	 */
	showVideoEditor: function( resource ) {
		var _this = this;
		var options = _this.getClipEditOptions( resource );
		var mediaType = this.getMediaType( resource );

		mw.log( 'media type:: ' + mediaType );

		// Get any additional embedding helper meta data prior to doing the actual embed
		// normally this meta should be provided in the search result
		// (but archive.org has another query for more media meta)
		resource.pSobj.addResourceInfoCallback( resource, function() {
			var runFlag = false;

			// Embed the video html
			var embedHtml = resource.pSobj.getEmbedHTML( resource,
				{
					'id' : 'embed_vid'
				}
			);
			mw.log( 'append html: ' + embedHtml );
			$j( '#clip_edit_disp' ).html( embedHtml );

			// Make sure we have the 'EmbedPlayer' module:
			mw.load( 'EmbedPlayer', function() {
				// Strange concurrency issue with callbacks
				// @@todo try and figure out why this callback is fired twice
				if ( !runFlag ) {
					runFlag = true;
				} else {
					mw.log( 'Error: embedPlayerCheck run twice' );
					return false;
				}

				mw.log( "about to call $j.embedPlayer::embed_vid" );

				// Rewrite by id
				$j( '#embed_vid' ).embedPlayer ( function() {
					// Add extra space at the top if the embed player is less than 90px high
					// bug 22189
					if( $j('#embed_vid').get(0).getPlayerHeight() < 90 ) {
						$j( '#clip_edit_disp' ).prepend(
							$j( '<span />' )
							.css({
								'height': '90px',
								'display': 'block'
							})
						);
					}

					// Grab information available from the embed instance
					resource.pSobj.addEmbedInfo( resource, 'embed_vid' );

					// Add libraries resizable and hoverIntent to support video edit tools
					var librarySet = [
						'mw.ClipEdit',
						'mw.style.ClipEdit',
						'$j.ui.resizable'
					];
					mw.load( librarySet, function() {
						// Make sure the rsd_edit_img is removed:
						$j( '#rsd_edit_img' ).remove();
						// Run the image clip tools
						_this.clipEdit = new mw.ClipEdit( options );
					} );
				} );
			} );
		} );
	},

	/**
	* Check if a given content provider is local.
	* @param {Object} provider Provider object to be checked
	* @return
	*/
	isProviderLocal: function( provider ) {
		if ( provider.local ) {
			return true;
		} else {
			// Check if we can embed the content locally per a domain name check:
			var localHost = mw.parseUri( mw.getLocalApiUrl() ).host;
			if ( provider.local_domains ) {
				for ( var i = 0; i < provider.local_domains.length; i++ ) {
					var domain = provider.local_domains[i];
					if ( localHost.indexOf( domain ) != -1 )
						return true;
				}
			}
			return false;
		}
	},

	/**
	 * Check if the file is either a local upload, or if it has already been
	 * imported under the standard filename scheme.
	 *
	 * @param {Object} resource Resouce to check for local file
	 * @param {Function} callback Function to call once check is done
	 *
	 * Calls the callback with two parameters:
	 *     callback( resource, status )
	 *
	 * resource: A resource object pointing to the local file if there is one,
	 *    or false if not
	 *
	 * status: may be 'local', 'shared', 'imported' or 'missing'
	 */
	isFileLocallyAvailable: function( resource, callback ) {
		var _this = this;
		// Add a loader on top
		mw.addLoaderDialog( gM( 'mwe-checking-resource' ) );

		// Extend the callback, closing the loader dialog before calling
		var myCallback = function( status ) {
			mw.closeLoaderDialog();
			if ( typeof callback == 'function' ) {
				callback( status );
			}
		};

		// NOTE: get the localized File/Image namespace name or do a general {NS}:Title
		var provider = resource.pSobj.provider;
		var _this = this;

		// Clone the resource. Not sure why this not-working clone was put here...
		// using the actual resource should be fine
		/*
		var proto = {};
		proto.prototype = resource;
		var myRes = new proto;
		*/

		// Update base target_resource_title:
		resource.target_resource_title = resource.titleKey.replace( /^(File:|Image:)/ , '' )

		// Check if local repository
		// or if import mode if just "linking" ( we should already have the 'url' )

		if ( this.isProviderLocal( provider ) || this.import_url_mode == 'remote_link' ) {
			// Local repo or in remote_link mode, jump directly to the callback:
			myCallback( 'local' );
			return ;
		} else {
			// Check if the file is local ( can be shared repo )
			if ( provider.check_shared ) {
				var fileTitle =_this.canonicalFileNS + ':' + resource.target_resource_title;
				_this.findFileInLocalWiki( fileTitle, function( imagePage ) {
					if ( imagePage && imagePage['imagerepository'] == 'shared' ||
									  imagePage['imagerepository'] == 'commons') {
						resource.commonsShareRepoFlag = true;
						myCallback( 'shared' );
					} else {
						_this.isFileAlreadyImported( resource, myCallback );
					}
				} );
			} else {
				_this.isFileAlreadyImported( resource, myCallback );
			}
		}
	},

	/**
	 * Check if the file is already imported with this extension's filename scheme
	 *
	 * Calls the callback with two parameters:
	 *     callback( resource, status )
	 *
	 * If the image is found, the status will be 'imported' and the resource
	 * will be the new local resource.
	 *
	 * If the image is not found, the status will be 'missing' and the resource
	 * will be false.
	 */
	isFileAlreadyImported: function( resource, callback ) {
		mw.log( '::isFileAlreadyImported:: ' );
		var _this = this;

		// Clone the resource
		//( not really needed and confuses the resource pointer role)
		/*var proto = {};
		proto.prototype = resource;
		var myRes = new proto;
		*/
		var provider = resource.pSobj.provider;

		// Update target_resource_title with resource repository prefix:
		resource.target_resource_title = provider.resource_prefix + resource.target_resource_title;

		// Check if the file exists:
		_this.findFileInLocalWiki( resource.target_resource_title, function( imagePage ) {
			if ( imagePage ) {
				// Update to local src
				resource.local_src = imagePage['imageinfo'][0].url;
				// @@todo maybe update poster too?
				resource.local_poster = imagePage['imageinfo'][0].thumburl;
				// Update the title:
				resource.target_resource_title = imagePage.title.replace(/^(File:|Image:)/ , '' );
				callback( 'imported' );
			} else {
				callback( 'missing' );
			}
		} );
	},

	/**
	* Show Import User Interface
	*
	* @param {Object} resource Resource Object to be imported
	* @param {Function} callback Function to be called once the resource is imported
	*/
	showImportUI: function( resource, callback ) {
		var _this = this;
		mw.log( "showImportUI:: update:" + _this.canonicalFileNS + ':' +
			resource.target_resource_title );

		var description = _this.getTemplateDescription( resource );


		// Remove any old resource imports
		$j( '#rsd_resource_import' ).remove();

		// Update the interface
		$j( _this.target_container ).append(
			_this.getResourceImportInterface( resource , description )
		);

		var buttonPaneSelector = _this.target_container + '~ .ui-dialog-buttonpane';
		$j( buttonPaneSelector ).html (
			// Add the buttons to the bottom:
			$j.btnHtml( gM( 'mwe-do_import_resource' ), 'rsd_import_doimport', 'check' ) +
			' ' +
			$j.btnHtml( gM( 'mwe-return-search-results' ), 'rsd_import_acancel', 'close' ) + ' '
		);

		// Update video tag (if a video)
		if ( resource.mime.indexOf( 'video/' ) !== -1 ) {
			var target_rewrite_id = $j( _this.target_container ).attr( 'id' ) + '_rsd_pv_vid';
			$j('#' + target_rewrite_id ).embedPlayer();
		}

		// Load the preview text:
		mw.parseWikiText(
			description, _this.canonicalFileNS + ':' + resource.target_resource_title,
			function( descHtml ) {
				$j( '#rsd_import_desc' ).html( descHtml );
			}
		);

		// Add bindings:
		$j( _this.target_container + ' .rsd_import_apreview' )
			.buttonHover()
			.click( function() {
				mw.log( " Do preview asset update" );
				$j( '#rsd_import_desc' ).loadingSpinner() ;
				// load the preview text:
				mw.parseWikiText(
					$j( '#wpUploadDescription' ).val(),
					_this.canonicalFileNS + ':' + resource.target_resource_title,
					function( parseHtml ) {
						mw.log( 'got updated preview: ' );
						$j( '#rsd_import_desc' ).html( parseHtml );
					}
				);
				return false;
			} );

		$j( buttonPaneSelector + ' .rsd_import_doimport' )
			.buttonHover()
			.click( function() {
				mw.log( "do import asset:" + _this.import_url_mode );
				// check import mode:
				if ( _this.import_url_mode == 'api' ) {
					_this.doApiImport( resource, function() {
						$j( '#rsd_resource_import' ).remove();
						_this.clipEdit.updateInsertControlActions();
						if( callback )
							callback();
					});
				} else {
					mw.log( "Error: import mode is not form or API (can not copy asset)" );
				}
				return false;
			} );
		$j( buttonPaneSelector + ' .rsd_import_acancel' )
			.buttonHover()
			.click( function() {
				$j( '#rsd_resource_import' ).fadeOut( "fast", function() {
					$j( this ).remove();
					// restore buttons (from the clipEdit object::)
					_this.clipEdit.updateInsertControlActions();
					$j( buttonPaneSelector ).removeClass( 'ui-state-error' );
				} );
				return false;
			} );
	},

	/**
	* Get the resource Import interface
	*/
	getResourceImportInterface: function( resource, description ) {
		var _this = this;
		var $rsdResourceImport = $j('<div />')
			.attr( 'id', 'rsd_resource_import' )
			.addClass( 'ui-widget-content' )
			.css( {
				'position' : 'absolute',
				'top' : '0px',
				'left' : '0px',
				'right' : '0px',
				'bottom' : '0px',
				'z-index' : '5'
			} );

		var $rsdPreviewContainer = $j( '<div />')
			.attr( 'id', 'rsd_preview_import_container' )
			.css( {
				'position' : 'absolute',
				'width' : '49%',
				'bottom' : '0px',
				'left' : '5px',
				'overflow' : 'auto',
				'top' : '30px'
			} )
			.append(
			// Get embedHTML with small thumb:
			resource.pSobj.getEmbedHTML( resource, {
				'id': _this.target_container + '_rsd_pv_vid',
				'max_height': '220',
				'only_poster': true
			} )
		)
		.append(
			$j('<br />')
				.css( 'clear', 'both' ),
			$j( '<span />' )
				.css( { 'font-weight' : 'bold' } )
				.text( gM( 'mwe-resource_page_desc' ) ),
			$j( '<div />' )
				.attr( 'id', 'rsd_import_desc' )
				.css( 'display', 'inline' )
				.loadingSpinner()
		)

		var $importResourceTitle = $j( '<h3 />' )
			.css( {
				'color' : 'red',
				'padding' : '5px'
			} )
			.text(
				gM( 'mwe-resource-needs-import', [resource.title, _this.upload_api_name] )
			);

		var $importTitle = $j( '<span />' )
			.css( { 'font-weight' : 'bold' } )
			.text( gM( 'mwe-local_resource_title' ) );

		var $importDestFile = $j( '<input />' )
			.attr( {
				'id' : 'wpDestFile',
				'type' : 'text',
				'size' : '30'
			} )
			.val ( resource.target_resource_title );

		var $importUploadDescription = $j('<div />')
			.append(
				$j( '<span />' )
					.css( { 'font-weight' : 'bold' } )
					.text( gM( 'mwe-edit_resource_desc' ) ),
				$j( '<textarea />' )
					.attr( {
						'id' : 'wpUploadDescription',
						'rows' : 8,
						'cols' : 50
					})
					.css( {
						'width': '90%'
					} )
					.text( description ),
				$j( '<input />' )
					.attr( {
						'type' : 'checkbox',
						'id' : 'wpWatchthis',
						'name' : 'wpWatchthis',
						'tabindex' : '7'
					} )
			);

		var $editImportContainer = $j( '<div />' )
			.css( {
				'position' : 'absolute',
				'left' : '50%',
				'bottom' : '0px',
				'top' : '30px',
				'right' : '0px',
				'overflow' : 'auto'
			})
			.append(
				$importTitle,
				$j( '<br />' ),

				$importDestFile,
				$j( '<br />' ),

				$importUploadDescription,
				$j( '<br />' ),

				// Add the watchlist button
				$j( '<label />' )
					.attr( {
						'for' : 'wpWatchthis'
					} )
					.text(
						gM( 'mwe-watch_this_page' )
					),
				$j( '<br />' ),

				// Add the update preview button:
				$j( '<br />' ),
				$j('<span />').append(
					$j.btnHtml( gM( 'mwe-update_preview' ), 'rsd_import_apreview', 'refresh' )
				)
			);

		$rsdResourceImport.append(
			$importResourceTitle,
			$rsdPreviewContainer,
			$editImportContainer
		)
		return $rsdResourceImport;
	},

	/**
	* Get Template Description wikitext
	* @pram {Object} resource Resource source for description
	*/
	getTemplateDescription: function( resource ) {
		// setup the resource description from resource description:
		// FIXME: i18n, namespace
		var description ='{{Information ' + "\n";

		if ( resource.desc ) {
			description += '|Description= ' + resource.desc + "\n";
		} else {
			description += '|Description= ' + gM( 'mwe-missing_desc_see_source', resource.link ) + "\n";
		}

		// Output search specific info
		description += '|Source=' + resource.pSobj.getImportResourceDescWiki( resource ) + "\n";

		if ( resource.author )
			description += '|Author=' + resource.author + "\n";

		if ( resource.date )
			description += '|Date=' + resource.date + "\n";

		// Add the Permission info:
		description += '|Permission=' + resource.pSobj.getPermissionWikiTag( resource ) + "\n";

		if ( resource.other_versions )
			description += '|other_versions=' + resource.other_versions + "\n";

		description += '}}';

		// Get any extra categories or helpful links
		description += resource.pSobj.getExtraResourceDescWiki( resource );
		return description;
	},

	/**
	* Check the local wiki for a given fileName
	*
	* @param {String} fileName File Name of the requested file
	* @param {Function} callback
	* 	Called with the result api result object OR
	* 	Callback is called with "false" if the file is not found
	*/
	findFileInLocalWiki: function( fileName, callback ) {
		mw.log( "findFileInLocalWiki::" + fileName );
		var _this = this;
		var request = {
			'action': 'query',
			'titles': 'File:' + fileName.replace( /^(File:|Image:)/ , '' ),
			'prop': 'imageinfo',
			'iiprop': 'url',
			'iiurlwidth': '400',
			'redirects' : true // automatically follow redirects
		};
		// First check the api for imagerepository
		mw.getJSON( mw.getLocalApiUrl(), request, function( data ) {
			if ( data.query.pages ) {
				for ( var i in data.query.pages ) {
					if( i != 'undefined' ){
						//If assset is missing check if is shared or on commons
						if( data.query.pages[i]['missing'] || i == -1 ) {
							if( data.query.pages[i].imagerepository != 'shared'
								&& data.query.pages[i].imagerepository != 'commons' )
							{
								callback( false );
								return;
							}
						}
						// else page is found:
						mw.log( fileName + " found" );
						callback( data.query.pages[i] );
					}
				}
			}
		} );
	},

	/**
	* Do import a resource via API import call
	*
	* @param {Object} resource Resource to import
	* @param {Function} callback Function to be called once api import call is done
	*/
	doApiImport: function( resource, callback ) {
		var _this = this;
		mw.log( ":doApiImport:" );
		mw.addLoaderDialog( gM( 'mwe-importing_asset' ) );

		alert( 'do copy-by-url import currently disabled');

		// Load the BaseUploadInterface:
		mw.load(
			[
				'mw.UploadInterface',
				'$j.ui.progressbar'
			],
			function() {
				mw.log( 'mw.UploadInterface ready' );
				// Initiate a upload object ( similar to url copy ):
				// ( mvBaseUploadInterface handles upload errors )
				var uploader = new mw.UploadDialogInterface( {
					'apiUrl' : _this.upload_api_target,
					'doneUploadCb': function() {
						mw.log( 'doApiImport:: run callback::' );
						// We have finished the upload:

						// Close up the rsd_resource_import
						$j( '#rsd_resource_import' ).remove();
						// return the parent callback:
						return callback();
					}
				} );

				// Get the edit token
				mw.getToken( _this.upload_api_target, function( token ) {
					uploader.editToken = token;

					// Close the loader now that we are ready to present the progress dialog::
					mw.closeLoaderDialog();
					uploader.doHttpUpload( {
						'url' : resource.src,
						'filename' : $j( '#wpDestFile' ).val(),
						'comment' : $j( '#wpUploadDescription' ).val()
					} );
				} );
			}
		);
	},

	/**
	* Shows a preview of the given resource
	*/
	showPreview: function( resource ) {
		var _this = this;
		this.isFileLocallyAvailable( resource, function( status ) {

			// If status is missing show import UI
			if ( status === 'missing' ) {
				_this.showImportUI( resource, function() {
					// Once the image is imported re-issue the showPreview request:
					_this.showPreview( resource );
				} );
				return;
			}

			// Put another window ontop:
			$j( _this.target_container ).append(
				$j('<div>').attr({
					'id': 'rsd_preview_display'
				})
				.css({
					'position' : 'absolute',
					'overflow' : 'auto',
					'z-index' : 4,
					'top' : '0px',
					'bottom' : '0px',
					'right' : '0px',
					'left' : '0px',
					'background-color' : '#FFF',
					'padding' : '1em'
				}).loadingSpinner()
			);

			var buttonPaneSelector = _this.target_container + '~ .ui-dialog-buttonpane';
			// Update title:
			$j( _this.target_container ).dialog( 'option', 'title',
				gM( 'mwe-preview_insert_resource', resource.title ) );

			// Update buttons
			$j( buttonPaneSelector )
				.html(
					$j.btnHtml( gM( 'rsd_do_insert' ), 'preview_do_insert', 'check' ) + ' ' )
				.children( '.preview_do_insert' )
				.click( function() {
					_this.insertResource( resource );
					return false;
				} );

			// Update cancel button
			$j( buttonPaneSelector ).append(
				$j.btnHtml( gM('mwe-do-more-modification' ), 'preview_close', 'pencil' )				
			);
			$j( buttonPaneSelector ).find('.preview_close')
			.click( function() {
				$j( '#rsd_preview_display' ).remove();
				var restoreTitle = gM( 'mwe-add_media_wizard' ) + ': ' +
					gM( 'rsd_resource_edit', resource.title );

				// Restore title:
				$j( _this.target_container ).dialog( 'option', 'title', restoreTitle );

				// Restore buttons (from the clipEdit object::)
				_this.clipEdit.updateInsertControlActions();
				return false;
			})
			// Get the preview wikitext
			var embed_code = _this.getEmbedCode( resource );
			var pos = $j( _this.target_textbox ).textSelection( 'getCaretPosition' );
			var editWikiText = $j( _this.target_textbox ).val();
			var wikiText = editWikiText.substr(0, pos) + embed_code + editWikiText.substr( pos );

			mw.parseWikiText(
				wikiText,
				_this.target_title,
				function( previewHtml ) {
					$j( '#rsd_preview_display' ).html( previewHtml );
					if( mw.documentHasPlayerTags() ) {
						mw.load( 'EmbedPlayer', function() {
							// Update the display of video tag items (if any)
							$j.embedPlayers();
						});
					}
				}
			);
		} );
	},

	/**
	* Get the embed code
	*
	* based on import_url_mode:
	* calls the resource providers getEmbedWithDescription method
	* 	or
	* calls the resource providers getEmbedWikiCode method
	*/
	getEmbedCode: function( resource ) {
		if ( this.import_url_mode == 'remote_link' ) {
			return resource.pSobj.getEmbedWithDescription( resource );
		} else {
			return resource.pSobj.getEmbedWikiCode( resource );
		}
	},

	/**
	* Insert a resource
	*
	* Calls updateTextArea with the passed resource
	* once we confirm the resource is available
	*
	* @param {Object} resource Resource to be inserted
	*/
	insertResource: function( resource ) {
		mw.log( 'insertResource: ' + resource.title );
		var _this = this;
		// If doing a remote link jump directly to resource output:
		if( this.import_url_mode == 'remote_link' ){
			_this.insertResourceToOutput( resource );
			return ;
		}

		// Double check that the resource is present:
		this.isFileLocallyAvailable( resource, function( status ) {
			if ( status === 'missing' ) {
				_this.showImportUI( resource, function() {
					_this.insertResourceToOutput( resource );
				} );
				return;
			}
			if ( status === 'local' || status === 'shared' || status === 'imported' ) {
				_this.insertResourceToOutput( resource );
			}
			//NOTE: should handle errors or other status states?
		} );
	},

	/**
	* Finish up the insertResource request by outputing the resource to output targets
	*
	* @param {Object} resource Resource to be inserted into the output targets
	*/
	insertResourceToOutput: function( resource ) {
		var _this = this;

		// Get the embed code ( can be html remote refrence or wiki-text depending on import_url_mode )
		var embed_code = _this.getEmbedCode( resource );

		// If outputing to a text box
		if( _this.target_textbox ) {
			$j( _this.target_textbox ).textSelection( 'encapsulateSelection', { 'post' : embed_code } );
		}

		// Update the render area for HTML output of video tag with mwEmbed "player"
		var embedCode = _this.getEmbedCode( resource );
		if ( _this.target_render_area && embedCode ) {

			// Output with some padding:
			$j( _this.target_render_area )
				.append( embedCode + '<div style="clear:both;height:10px">' )

			// Update the player if video or audio:
			if ( resource.mime.indexOf( 'audio' ) != -1 ||
				resource.mime.indexOf( 'video' ) != -1 ||
				resource.mime.indexOf( '/ogg' ) != -1 )
			{
				// Re-load the player module ( will scan page for mw.getConfig( 'EmbedPlayer.RewriteTags' ) )
				$j.embedPlayers();
			}
		}

		// Close up the add-media-wizard dialog
		_this.closeAll();
	},

	/**
	* Close up the remote search driver
	*/
	closeAll: function() {
		var _this = this;
		mw.log( "close all:: " + _this.target_container );
		_this.onCancelResourceEdit();

		$j( _this.target_container ).dialog( 'close' );
		// Give a chance for the events to complete
		// (somehow at least in firefox a rare condition occurs where
		// the modal of the edit-box stick around even after the
		// close request has been issued. )
		setTimeout(
			function() {
				$j( _this.target_container ).dialog( 'close' );
				$j( _this.target_container ).remove();
			}, 25
		);
	},

	/**
	 * Create controls for selecting result display layout (e.g. box, list)
	 *
	 * @return {jQuery element} The layout element to embed in the page.
	 */
	createLayoutSelector: function() {

		var _this = this;
		// TODO this should be refactored so that the images paths are in css
		var darkBoxUrl = mw.getConfig( 'imagesPath' ) + 'box_layout_icon_dark.png';
		var lightBoxUrl = mw.getConfig( 'imagesPath' ) + 'box_layout_icon.png';
		var darkListUrl = mw.getConfig( 'imagesPath' ) + 'list_layout_icon_dark.png';
		var lightListUrl = mw.getConfig( 'imagesPath' ) + 'list_layout_icon.png';

		var defaultBoxUrl, defaultListUrl;
		if ( _this.displayMode == 'box' ) {
			defaultBoxUrl = darkBoxUrl;
			defaultListUrl = lightListUrl;
		} else {
			defaultBoxUrl = lightBoxUrl;
			defaultListUrl = darkListUrl;
		}

		$boxLayout = $j( '<img />' ).addClass( 'layout_selector' )
			.attr({
				id: 'msc_box_layout',
				title: gM( 'rsd_box_layout' ),
				src: defaultBoxUrl
			})
			.hover(
				function() {
					$j( this ).attr( "src", darkBoxUrl );
				},
				function() {
					$j( this ).attr( "src", defaultBoxUrl );
				} )
			.click( function() {
				$boxLayout.attr( "src", darkBoxUrl );
				$listLayout.attr( "src", lightListUrl );
				_this.setDisplayMode( 'box' );
				return false;
			} );
		$listLayout = $j( '<img />' ).addClass( 'layout_selector' )
			.attr({
				id: 'msc_list_layout',
				title: gM( 'rsd_list_layout' ),
				src: defaultListUrl
			})
			.hover(
				function() {
					$j( this ).attr( "src", darkListUrl );
				},
				function() {
					$j( this ).attr( "src", defaultListUrl );
				} )
			.click( function() {
				$listLayout.attr( "src", darkListUrl );
				$boxLayout.attr( "src", lightBoxUrl );
				_this.setDisplayMode( 'list' );
				return false;
			} );

		$layoutSelector = $j( '<span />' )
							.append( $boxLayout )
							.append( $listLayout );

		return $layoutSelector;
	},
	/**
	 * Create a string indicating the source of the results + link
	 *
	 * @param The current content provider.
	 *
	 * @return {jQuery element} A description element for embedding.
	 */
	createSearchDescription: function( provider ) {
		var resultsFromMsg = gM( 'mwe-results_from',
			$j('<a />')
			.attr({
				'href' : provider.homepage,
				'target' : '_new'
			} )
			.append( gM( 'rsd-' + provider.id + '-title' ) )
		);

		var $searchContent = $j( '<span />' ).html( resultsFromMsg );
		var $searchDescription = $j( '<span />' ).addClass( 'rsd_search_description' )
			.attr({
				id: 'rsd_search_description'
			})
			.append( $searchContent );

		return $searchDescription;
	},

	/**
	* Results Header controls like box vs list view
	* & search description
	*
	* @return {jQuery element} The header for embedding in the result set.
	*/
	createResultsHeader: function() {
		var _this = this;

		if ( !this.content_providers[ this.current_provider ] ) {
			return;
		}
		var provider = this.content_providers[ this.current_provider ];

		var $header = $j( '<div />' )
			.attr({
				id: 'rsd_results_header'
			});

		if( this.displayResultFormatButton ){
			$header.append( this.createLayoutSelector() )
		}

		$header.append( this.createSearchDescription( provider ) );

		return $header;
	},

	/**
	 * Creates the footer of the search results (paging).
	 *
	 * @return {jQuery element} The footer for embedding in the result set.
	 */
	createResultsFooter: function() {
		var _this = this;

		var $footer = $j( '<div />' )
		.attr({
			id: 'rsd_results_footer'
		})
		.append( this.createPagingControl() );

		return $footer;
	},

	/**
	* Generates an HTML control for paging between search results.
	*
	* @return {jQuery element} paging control for current results
	*/
	createPagingControl: function( target ) {
		var _this = this;
		var provider = _this.content_providers[ _this.current_provider ];
		var search = provider.sObj;

		mw.log( 'Paging Control for ' + _this.current_provider + ' num of results: ' + search.num_results );
		var to_num = ( provider.limit > search.num_results ) ?
			( parseInt( provider.offset ) + parseInt( search.num_results ) ) :
			( parseInt( provider.offset ) + parseInt( provider.limit ) );

		var $pagingControl = $j( '<span />' ).attr({
			id: 'rsd_paging_control'
		});

		// This puts enumeration text e.g. Results 1 to 30.
		var resultEnumeration = '';
		// @@todo we should instead support the wiki number format template system instead of inline calls
		if ( search.num_results != 0 ) {
			if ( search.num_results > provider.limit ) {
				resultEnumeration = gM( 'rsd_results_desc_total', [( provider.offset + 1 ), to_num,
					mw.Language.formatNumber( search.num_results )] );
			} else {
				resultEnumeration = gM( 'rsd_results_desc', [( provider.offset + 1 ), to_num] );
			}
		}

		var $resultEnumeration = $j( '<span />' ).text( resultEnumeration )
												 .addClass( 'rsd_result_enumeration' );
		$pagingControl.append( $resultEnumeration );

		// Place the previous results link
		if ( provider.offset >= provider.limit ) {
			var prevLinkText = gM( 'rsd_results_prev' ) + ' ' + provider.limit;
			var $prevLink = $j( '<a />' )
				.attr({
					href: '#',
					id: 'rsd_pprev'
				} )
				.text( prevLinkText )
				.click( function() {
					provider.offset -= provider.limit;
					if ( provider.offset < 0 ){
						provider.offset = 0;
					}
					_this.updateResults();
					return false;
				} );
			$pagingControl.prepend( $prevLink );
		}

		// Place the next results link
		if ( search.more_results ) {
			var nextLinkText = gM( 'rsd_results_next' ) + ' ' + provider.limit;
			var $nextLink = $j( '<a />' )
				.attr({
					href: '#',
					id: 'rsd_pnext'
				} )
				.text( nextLinkText )
				.click( function() {
					provider.offset += provider.limit;
					_this.updateResults();
					return false;
				} );
			$pagingControl.append( $nextLink );
		}

		return $pagingControl;
	},

	/**
	* Select a given search provider
	* @param {String} provider_id Provider id to select and display
	*/
	selectTab: function( provider_id ) {
		mw.log( 'select tab: ' + provider_id );
		this.current_provider = provider_id;
		if ( this.current_provider == 'upload' ) {
			this.showUploadTab();
		} else {
			// Update the search results:
			this.updateResults();
		}
	},

	/*
	* Sets the display mode
	* @param {String} mode Either "box" or "list"
	*/
	setDisplayMode: function( mode ) {
		mw.log( 'setDisplayMode:' + mode );
		this.displayMode = mode;
		// Run / update search display:
		this.showResults( );
	}
};
