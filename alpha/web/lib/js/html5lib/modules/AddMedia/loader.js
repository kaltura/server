/**
* Loader for libAddMedia module:
*/

// Wrap in mw to not pollute global namespace
( function( mw ) {

	mw.addMessages( {
		"mwe-loading-add-media-wiz" : "Loading add media wizard"
	});

	// Add class file paths ( From ROOT )
	mw.addResourcePaths( {
		"$j.fn.dragDropFile"	: "jquery.dragDropFile.js",

		"mw.UploadForm"			: "mw.UploadForm.js",

		"mw.UploadHandler"		: "mw.UploadHandler.js",
		"mw.UploadInterface"	: "mw.UploadInterface.js",
		"mw.Firefogg"			: "mw.Firefogg.js",
		"mw.FirefoggGUI"		: "mw.FirefoggGUI.js",

		"mw.RemoteSearchDriver"	: "mw.RemoteSearchDriver.js",

		"mw.style.AddMedia" : "css/mw.style.AddMedia.css",

		"baseRemoteSearch"		: "searchLibs/baseRemoteSearch.js",
		"mediaWikiSearch"		: "searchLibs/mediaWikiSearch.js",
		"metavidSearch"			: "searchLibs/metavidSearch.js",
		"archiveOrgSearch"		: "searchLibs/archiveOrgSearch.js",
		"flickrSearch"			: "searchLibs/flickrSearch.js",
		"baseRemoteSearch"		: "searchLibs/baseRemoteSearch.js",
		"kalturaSearch"			: "searchLibs/kalturaSearch.js"

	} );

	// Upload form includes "datapicker"
	mw.addModuleLoader( 'AddMedia.UploadForm', [
			[
				'mw.UploadForm',
				'$j.ui'
			],
			[
				'$j.widget',
				'$j.ui.mouse',
				'$j.ui.datepicker'
			]
		]
	);

	//Setup the addMediaWizard module
	mw.addModuleLoader( 'AddMedia.addMediaWizard',
		// Define loader set:
		[
			[	'mw.RemoteSearchDriver',
				'mw.style.AddMedia',
				'$j.cookie',
				'$j.fn.textSelection',
				'$j.browserTest', // ( textSelection uses browserTest )
				'$j.ui'
			], [
				'$j.widget',
				'$j.ui.mouse',
				'$j.ui.resizable',
				'$j.ui.position',
				'$j.ui.draggable',
				'$j.ui.dialog',
				'$j.ui.tabs',
				'$j.ui.sortable'
			]
		] );

	//Set a variable for the base upload interface for easy inclusion
	var baseUploadlibs = [
		[
			'mw.UploadHandler',
			'mw.UploadInterface',
			'$j.ui'
		],
		[
			'$j.widget',
			'$j.ui.mouse',
			'$j.ui.progressbar',
			'$j.ui.position',
			'$j.ui.dialog',
			'$j.ui.draggable'
		]
	];

	/*
	* Upload interface loader:
	*/

	mw.addModuleLoader( 'AddMedia.UploadHandler', baseUploadlibs );

	/**
	 * The Firefogg loaders
	 *
	 * Includes both firefogg & firefogg "GUI" which share some loading logic:
	 */

	// Clone the baseUploadlibs array and add the firefogg lib:
	var mwBaseFirefoggReq = baseUploadlibs.slice( 0 )
	mwBaseFirefoggReq[ 0 ].push( 'mw.Firefogg' );

	mw.addModuleLoader( 'AddMedia.firefogg', mwBaseFirefoggReq );

	mw.addModuleLoader( 'AddMedia.FirefoggGUI', function() {
		// Clone the array:
		var request = mwBaseFirefoggReq.slice( 0 ) ;

		// Add firefogg gui classes to a new "request" var:
		request.push( [
			'mw.FirefoggGUI',
			'$j.cookie',
			'$j.ui.accordion',
			'$j.ui.slider',
			'$j.ui.datepicker'
		] );

		return request;
	} );

} )( window.mw );
