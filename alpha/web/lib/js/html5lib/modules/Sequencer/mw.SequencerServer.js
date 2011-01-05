/**
* Sequencer Server bridges a server API with sequence actions like 'load', 'save', 'revision history' etc.
* ( for now only mediaWiki api is supported )
* We will abstract all the method calls once we add another api backend
*/

//Wrap in mw closure
( function( mw ) {

	var SEQUENCER_PAYLOADKEY = '@gadgetSequencePayload@_$%^%';

	mw.SequencerServer = function( sequencer ) {
		return this.init( sequencer );
	};

	// Set up the SequencerServer prototype method object
	mw.SequencerServer.prototype = {

		// lazy init save token for the server config
		saveToken : null,

		// Api type ( always mediaWiki for now)
		apiType: null,

		// Api url ( url to query for api updates )
		apiUrl: null,

		// The sequence title key for api queries
		titleKey: null,

		// The Url path the the sequence page with $1 where the title should be
		pagePathUrl: null,

		// Stores the most recent version of the sequence xml from the server
		serverSmilXml: null,

		// Flags if the sequence was successfully saved in this session
		sequenceSaved: false,

		//Template cache of wikitext for templates loaded in this session
		templateTextCache: [],

		// Current sequence page
		currentSequencePage: {},

		/**
		 * init the sequencer
		 */
		init: function( sequencer ){
			this.sequencer = sequencer;
			// Set local config from sequencer options
			var serverConfig = this.sequencer.getOption( 'server' );

			// NOTE this should trigger an apiHandler once we have more than one api backend
			if( serverConfig ){

				if( serverConfig.type )
					this.apiType = serverConfig.type;

				if( serverConfig.url )
					this.apiUrl = serverConfig.url;

				if( serverConfig.titleKey )
					this.titleKey = serverConfig.titleKey;

				if( serverConfig.pagePathUrl ){
					this.pagePathUrl = serverConfig.pagePathUrl;
				}

				if( serverConfig.userName ){
					this.userName = serverConfig.userName;
				}

			}
			if( this.isConfigured() ){
				mw.log("Error: Sequencer server needs a full serverConfig to be initialized");
				return false;
			}
			return this;
		},
		getUserName: function(){
			return this.userName;
		},
		getTitleKey: function(){
			return this.titleKey;
		},
		// Check if the server exists / is configured
		isConfigured: function( ){
			if( !this.apiUrl || !this.titleKey){
				return false;
			}
			return true;
		},
		getApiUrl: function(){
			return this.apiUrl;
		},
		/**
		 * Check if the user in the current session can save to the server
		 */
		userCanSave: function( callback ){
			this.getSaveToken( callback );
		},

		/**
		 * Get up to date sequence xml from the server
		 */
		getSmilXml: function( callback ){
			var _this = this;
			mw.getTitleText( this.getApiUrl(), this.getTitleKey(), function( smilPage ){
				// Check for remote payload wrapper
				// XXX need to support multiple pages in single context
				_this.currentSequencePage = _this.parseSequencerPage( smilPage );
				// Cache the latest serverSmil ( for local change checks )
				// ( save requests automatically respond with warnings on other user updates )
				if( _this.currentSequencePage.sequenceXML ){
					_this.serverSmilXml = _this.currentSequencePage.sequenceXML ;
				} else {
					// empty result
					_this.serverSmilXml = '';
				}

				// Cache the pre / post bits
				callback( _this.serverSmilXml );
			});
		},
		wrapSequencerWikiText : function( xmlString ){
			var _this = this;
			if( !_this.currentSequencePage ){
				_this.currentSequencePage= {};
			}
			if( !_this.currentSequencePage.pageStart ){
				 _this.currentSequencePage.pageStart ="\nTo edit or view this sequence " +
					'[{{fullurl:{{FULLPAGENAME}}|withJS=MediaWiki:MwEmbed.js}} enable the sequencer] for this page';
			}
			if(!_this.currentSequencePage.pageEnd ){
				_this.currentSequencePage.pageEnd ='';
			}
			return _this.currentSequencePage.pageStart +
				"\n\n<!-- " + SEQUENCER_PAYLOADKEY + "\n" +
				xmlString +
				"\n\n " + SEQUENCER_PAYLOADKEY + " -->\n" +
				_this.currentSequencePage.pageEnd;
		},

		parseSequencerPage : function( pageText ){
			var _this = this;
			var startKey = '<!-- ' + SEQUENCER_PAYLOADKEY;
			var endKey = SEQUENCER_PAYLOADKEY + ' -->';
			// If the key is not found fail
			if( !pageText || pageText.indexOf( startKey ) == -1 || pageText.indexOf(endKey) == -1 ){
				mw.log( "Error could not find sequence payload" );
				return {};
			}
			// trim the output:
			return {
				'pageStart' :	$j.trim(
					pageText.substring(0, pageText.indexOf( startKey ) )
				),
				'sequenceXML' :	$j.trim(
					pageText.substring( pageText.indexOf( startKey ) + startKey.length,
										pageText.indexOf(endKey )
									)
				),
				'pageEnd' : $j.trim(
					pageText.substring( pageText.indexOf(endKey) + endKey.length )
				)
			};
		},


		getTemplateText: function( templateTitle, callback ){
			var _this = this;
			if( this.templateTextCache[templateTitle] ){
				callback( this.templateTextCache[templateTitle] );
				return ;
			}
			mw.getTitleText( this.getApiUrl(),templateTitle, function( templateText ){
				_this.templateTextCache[templateTitle] = templateText;
				callback(templateText);
			});
		},
		// Check if there have been local changes
		hasLocalChanges: function(){
			return ( this.serverSmilXml != this.sequencer.getSmil().getXMLString() );
		},
		// Check if the sequence was saved in this edit session
		hasSequenceBeenSavedOrPublished: function(){
			return ( this.sequenceSaved || this.sequencePublished );
		},
		// Get a save token, if unable to do so return false
		getSaveToken: function( callback ){
			var _this = this;
			if( this.saveToken != null ){
				callback ( this.saveToken );
				return ;
			}
			mw.getToken( this.getApiUrl(), this.getTitleKey(), function( saveToken ){
				_this.saveToken = saveToken;
				callback ( _this.saveToken );
			});
		},

		// Save the sequence
		save: function( saveSummary, sequenceXML, callback){
			var _this = this;
			mw.log("SequenceServer::Save: " + saveSummary );
			this.getSaveToken( function( token ){
				if( !token ){
					callback( false, 'could not get edit token');
					return ;
				}
				var request = {
					'action' : 'edit',
					'summary' : saveSummary,
					'title' : _this.titleKey,
					'text' : _this.wrapSequencerWikiText( sequenceXML ),
					'token': token
				};
				mw.getJSON( _this.getApiUrl(), request, function( data ) {
					if( data.edit && data.edit.result == 'Success' ) {
						// Update the latest local variables
						_this.saveSummary = saveSummary;
						_this.sequenceSaved = true;
						_this.serverSmilXml = sequenceXML;
						callback( true );
					} else {
						// xxx Should have more error handling ( conflict version save etc )
						callback( false, 'failed to save to server');
					}
				});
			});
		},

		/**
		 * Check if the published file is up-to-date with the saved sequence
		 * ( higher page revision for file than sequence )
		 */
		isPublished: function( callback ){
			var _this = this;
			var request = {
				'prop':'revisions',
				'titles' : 'File:' + this.getVideoFileName() + '|' + this.titleKey,
				'rvprop' : 'ids'
			};
			var videoPageRevision = null;
			var xmlPageRevision = null;
			mw.getJSON( _this.getApiUrl(), request, function( data ) {
				if( data.query && data.query.pages ){
					for( page_id in data.query.pages ){
						var page = data.query.pages[page_id];
						if( page.revisions && page.revisions[0] && page.revisions[0].revid ){
							if( page.title == _this.titleKey ){
								xmlPageRevision = page.revisions[0].revid;
							} else {
								videoPageRevision = page.revisions[0].revid;
							}
						}
					}
				}
				if( videoPageRevision != null && xmlPageRevision != null){
					callback ( ( videoPageRevision > xmlPageRevision ) );
					return ;
				}
				callback( null );
			});
		},

		/**
		 * Get a save summary and run a callback
		 */
		getSaveSummary: function( callback ){
			var _this = this;
			if( this.saveSummary ){
				callback( this.saveSummary );
				return ;
			}
			// Get the save summary for the latest revision
			var request = {
				'prop':'revisions',
				'titles' : _this.titleKey,
				'rvprop' : 'user|comment|timestamp'
			};
			mw.getJSON( _this.getApiUrl(), request, function( data ) {
				if( data.query && data.query.pages ){
					for( page_id in data.query.pages ){
						var page = data.query.pages[page_id];
						if( page.revisions && page.revisions[0] && page.revisions[0].comment ){
							callback( page.revisions[0].comment );
							return;
						}
					}
				}
				callback( false );
			});
		},

		updateSequenceFileDescription: function( callback ){
			var _this = this;
			mw.getToken( _this.getApiUrl(), 'File:' + _this.getVideoFileName(), function( token ){
				var pageText = '';
				// Check if we should use commons asset description template:
				if( mw.parseUri( _this.getApiUrl() ).host == 'commons.wikimedia.org' ){
					pageText = _this.getCommonsDescriptionText();
				} else {
					pageText = _this.getBaseFileDescription();
				}
				var request = {
					'action': 'edit',
					'token' : token,
					'title' : 'File:' + _this.getVideoFileName(),
					'summary' : 'Automated sequence description page for published sequence: ' + _this.getTitleKey(),
					'text' : pageText
				};

				mw.getJSON( _this.getApiUrl(), request, function( data ){
					if( data && data.edit && data.edit.result == "Success" ){
						callback( true );
					} else {
						callback( false );
					}
				});
			});
		},

		getBaseFileDescription: function(){
			return 'Published sequence for [[' + this.getTitleKey() + ']]';
		},

		getCommonsDescriptionText: function(){
			var _this = this;

			var descText = "<!-- " +
			"Note: this is an automated file description for a published video sequence. \n" +
			"Changes to this wikitext will be overwiten. Please add metadata and categories to\n" +
			_this.getTitleKey() +
			" instead --> \n" +
			 "{{Information\n" +
			"|Description=" + _this.getBaseFileDescription() + "\n" +
			"|Source= Sequence Sources assets include:\n";



			// loop over every asset:
			this.sequencer.getSmil().getBody().getRefElementsRecurse(null, 0, function( $node ){
				var $apiKeyParam = $node.children("param[name='apiTitleKey']");
				if( $apiKeyParam.length ){
					descText+= "* [[:" + $apiKeyParam.attr('value') + "]]\n";
				}
			});
			var pad2 = function(num){
				if( parseInt( num ) < 10 ){
					return '0' + num;
				}
				return num;
			};
			var dt = new Date();
			descText+='|Date=' + dt.getFullYear() + '-' +
						pad2(dt.getMonth()+1) + '-' +
						pad2(dt.getDate()) + "\n" +
				"|Author=Published by [[User:" + _this.getUserName() + "]]\n" +
				"For full editor list see history page of [[" + _this.getTitleKey() + "]] \n" +
				"|Permission={{Cc-by-sa-3.0}} and {{GFDL|migration=redundant}}" + "\n" +
				"}}";

			// Add Published Sequence category ( for now )
			descText += "\n[[Category:Published sequences]]\n";

			return descText;
		},

		/**
		 * Get the sequence description page url
		 * @param {String} Optional Sequence title key
		 */
		getSequenceViewUrl: function( titleKey ){
			if( !titleKey )
				titleKey = this.getTitleKey();
			// Check that we have a pagePathUrl config:
			if( !this.pagePathUrl ){
				return false;
			}
			return this.pagePathUrl.replace( '$1', 'Sequence:' + encodeURI( titleKey ));
		},

		getAssetViewUrl: function( titleKey ){
			// Check that we have a pagePathUrl config:
			if( !this.pagePathUrl ){
				return false;
			}
			return this.pagePathUrl.replace( '$1', encodeURI( titleKey ) );
		},

		/**
		 * Get the sequencer 'edit' url
		 */
		getSequenceEditUrl: function( titleKey ){
			var viewUrl = this.getSequenceViewUrl( titleKey );
			return mw.replaceUrlParams(viewUrl, {'action':'edit'});
		},

		/**
		 * Get the video file name for saving the flat video asset to the server
		 * @return {String}
		 */
		getVideoFileName: function(){
			return this.getTitleKey().replace( /:/g, '-') + '.ogv';
		},

		// get upload settings runs the callback with the post url and request data
		getUploadRequestConfig: function( callback ){
			var _this = this;
			mw.getToken( this.getApiUrl(), 'File:' + this.getVideoFileName(), function( saveToken ){
				// xxx Get the latest save comment
				_this.getSaveSummary( function( saveSummary ){
					var request = {
						'token' : saveToken,
						'action' : 'upload',
						'format': 'json',
						'filename': _this.getVideoFileName(),
						'comment': 'Published [[' + _this.getTitleKey() + ']] : ' + saveSummary,
						'ignorewarnings' : true
					};
					// Return the apiUrl and request
					callback( _this.getApiUrl(), request );
				});
			});
		},
		// Setter for sequencePublished
		sequencePublishUploadDone: function(){
			this.sequencePublished = true;
		}
	};


} )( window.mw );
