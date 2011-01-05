/**
* Sequencer add by url support ( ties into mwEmbed AddMedia remoteSearchDriver module )
*/

//Wrap in mw closure
( function( mw ) {

mw.SequencerAddByUri = function( sequencer ) {
	return this.init( sequencer );
};
mw.SequencerAddByUri.prototype = {
	init: function( sequencer ){
		this.sequencer = sequencer;
	},

	/**
	 * Does a basic parseUri check to see if a string is likely a url:
	 */
	isUri: function( inputString ){
		// Check for file: type key
		if( inputString.indexOf('File:') === 0 ){
			return true;
		}
		return ( mw.parseUri( inputString ).protocol ) ;
	},

	/**
	 * Try to add media via url and present a dialog if failed
	 *  or user input is required
	 *
	 *  Uses remoteSearchDriver to help in retrieving entry info
	 *  @param  {Object} remoteSearchDriver The remote search driver
	 */
	addByUriDialog: function( remoteSearchDriver, importString ){
		var _this = this;
		var importString = unescape( importString );
		mw.log('SequencerAddByUri::addByUrlDialog:'+ importString);
		var $dialog = mw.addLoaderDialog( gM( 'mwe-sequencer-loading-asset' ) );

		// Close / empty the toolWindow
		_this.sequencer.getTools().setDefaultText();

		// Check for file type uri direct key with local
		if( importString.indexOf('File:') === 0 ){
			// make sure we have commons or local_wiki as a resource source:
			var provider = remoteSearchDriver.content_providers[ 'this_wiki' ];
			if( ! provider ){
				 provider = remoteSearchDriver.content_providers[ 'wiki_commons' ];
			}
			// Try to import from the given provider:
			if( provider ){
				remoteSearchDriver.getResourceFromTitleKey( provider, importString, function( resource ){
					if( ! resource ){
						$dialog.html( 'Error loading asset');
						return ;
					}
					// Get convert resource to smilClip and insert into the timeline
					_this
					.sequencer
					.getAddMedia()
					.getSmilClipFromResource( resource, function( smilClip ) {
						_this.sequencer.getTimeline().insertSmilClipEdit( smilClip );
						mw.closeLoaderDialog();
					});
				});
				return ;
			}
		}

		var foundImportUrl = false;
		// See if the asset matches the detailsUrl key type of any enabled content provider:
		$j.each( remoteSearchDriver.getEnabledProviders(), function(providerName, provider){
			if( mw.parseUri( provider.detailsUrl ).host == mw.parseUri( importString).host ){
				foundImportUrl = true;
				mw.log( "addByUrlDialog: matching host getResourceFromUrl::"
						+ mw.parseUri( provider.detailsUrl ).host
						+ ' == ' + mw.parseUri( importString ).host );

				// Do special check for mediawiki templates and pages as 'special' smil types
				if( provider.lib == 'mediaWiki' ){
					// xxx we should do a query to the api to determine namespace instead of hard coded checks
					remoteSearchDriver.loadSearchLib( provider, function( provider ){
						var titleKey = provider.sObj.getTitleKeyFromMwUrl( importString );
						if( !titleKey ){
							$dialog.html( gM('mwe-sequencer-import-url-not-supported', 'commons.wikimedia.org' ) )
							// continue for loop ( if we can't get a title from the mediaWiki url )
							return true;
						}

						// Check the title type
						// xxx should use wgFormattedNamespacess
						if( titleKey.indexOf('File:') == 0 ){
							// Asset is a file import resource as a file:
							remoteSearchDriver.getResourceFromUrl( provider, importString, function( resource ){
								if( ! resource ){
									$dialog.html( 'Error loading asset');
									return ;
								}
								// Get convert resource to smilClip and insert into the timeline
								_this
								.sequencer
								.getAddMedia()
								.getSmilClipFromResource( resource, function( smilClip ) {
									_this.sequencer.getTimeline().insertSmilClipEdit( smilClip );
									mw.closeLoaderDialog();
								});
							});
						} else if( titleKey.indexOf('Template:') == 0 ) {
							// Parse any parameters we can find:
							var apiProvider = '';
							if( mw.parseUri(provider.apiUrl ).host == 'commons.wikimedia.org' ){
								apiProvider = 'commons'
							} else {
								// xxx we need to abstract the remoteSearch driver provider logic
								// into a provider class
								apiProvider = 'local';
							}
							// Get template smilClip:
							var smilClip = _this
							.sequencer
							.getAddMedia()
							.getSmilClipFromWikiTemplate( titleKey, apiProvider );

							// Add the smil clip to the sequencer
							_this.sequencer.getTimeline().insertSmilClipEdit( smilClip );

							// Close the dialog loading:
							mw.closeLoaderDialog();

						/*
						 * Soon sequence transclution fun:
						 * else if( titleKey.indexOf('Sequence:') == 0 ) {
						 */

						} else {
							$dialog.html( 'Error loading asset type');
						}

					});
				} else {
					mw.log(" only MediaWiki URLs supported as resources right now");
				}
			}
		});

		if( ! foundImportUrl ){
			$dialog.html( gM('mwe-sequencer-import-url-not-supported', 'commons.wikimedia.org' ) );
		}

		// xxx support direct asset include
		if( mw.getConfig( 'Sequencer.AddAssetByUrl' )){
			// try directly adding the asset
		}
	}
};

} )( window.mw );