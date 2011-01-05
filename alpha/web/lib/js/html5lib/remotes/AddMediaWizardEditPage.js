// Setup configuration vars (if not set already)
if ( !mwAddMediaConfig ) {
	var mwAddMediaConfig = { };
}

// The default editPage AMW config ( should move to mw.setConfig && mw.getConfig )
var defaultAddMediaConfig = {
		'profile': 'mediawiki_edit',
		'target_textbox': '#wpTextbox1',
		// Note: selections in the textbox will take over the default query
		'default_query': wgTitle,
		'target_title': wgPageName,

		// If the info overlay per asset should be displayed
		'displayResourceInfoIcons' : true,

		// If we should display buttons to switch between "box" and "detailed" view
		'displayResultFormatButton' : false,

		// To use the "uploadWizard
		'uploadEngine' : 'uploadWizard',

		// Here we can setup the content provider overrides
		'enabled_providers': ['wiki_commons'],
		// The local wiki API URL:
		'local_wiki_apiUrl': wgServer + wgScriptPath + '/api.php'
};

mw.ready( function() {
	var amwConf = $j.extend( true, defaultAddMediaConfig, mwAddMediaConfig );
	// Kind of tricky, it would be nice to use run on ready "loader" call here
	var didWikiEditorBind = false;

	// Set-up the drag drop binding (will only work for html5 upload browsers)
	// $j('textarea#wpTextbox1').dragFileUpload();

	// set up the add-media-wizard binding:
	if ( typeof $j.wikiEditor != 'undefined' ) {
			// the below seems to be broken :(
			$j( 'textarea#wpTextbox1' ).bind( 'wikiEditor-toolbar-buildSection-main',
			function( e, section ) {
				didWikiEditorBind = true;
				if ( typeof section.groups.insert.tools.file !== 'undefined' ) {
					section.groups.insert.tools.file.action = {
						'type': 'callback',
						'execute': function() {
							mw.log( 'Added via wikiEditor bind' );
							// Display a loader ( since its triggered onClick )
							mw.addLoaderDialog( gM( 'mwe-loading-add-media-wiz' ) );
							mw.load( 'AddMedia.addMediaWizard', function() {
								mw.closeLoaderDialog();
								$j.addMediaWizard( amwConf );
							});
						}
					};
				}
			}
		);
	}
	// Add to old toolbar if wikiEditor did not remove '#toolbar' from the page:
	setTimeout( function() {
		if ( $j( '#btn-add-media-wiz' ).length == 0 && $j( '#toolbar' ).length != 0 ) {
			mw.log( 'Do old toolbar bind:' );
			didWikiEditorBind = true;
			$j( '#toolbar' ).append( '<img style="cursor:pointer" id="btn-add-media-wiz" src="' +
				mw.getConfig( 'imagesPath' ) + 'Button_add_media.png">' );

			$j( '#btn-add-media-wiz' ).attr( 'title', gM( 'mwe-loading-add-media-wiz' ) );
			mw.load( 'AddMedia.addMediaWizard', function() {
				$j( '#btn-add-media-wiz' ).addMediaWizard(
					amwConf
				);
			});

		} else {
			// Make sure the wikieditor got binded:
			if ( !didWikiEditorBind ) {
				mw.log( 'Failed to bind via build section bind via target:' );
				var $targetFileButton = $j( ".toolbar [rel='file']" );

				$targetFileButton
				.attr( 'title', gM( 'mwe-loading-add-media-wiz' ) )
				.unbind()

				mw.load( 'AddMedia.addMediaWizard', function() {
					if( $targetFileButton.length != 0 ) {
						$targetFileButton.unbind().addMediaWizard( amwConf );
					}
				} );
			}
		}
	}, 100 )

} );
