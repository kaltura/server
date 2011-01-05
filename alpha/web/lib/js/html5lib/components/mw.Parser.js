/**
* Mediawiki language text parser
*/

// Setup swap string constants
var JQUERY_SWAP_STRING = 'ZjQuerySwapZ';
var LINK_SWAP_STRING = 'ZreplaceZ';

( function( mw ) {

	// The parser magic global
	var pMagicSet = { };

	/**
	 * addTemplateTransform to the parser
	 *
	 * Lets you add a set template key to be transformed by a callback function
	 *
	 * @param {Object} magicSet key:callback
	 */
	mw.addTemplateTransform = function( magicSet ) {
		for ( var i in magicSet ) {
			pMagicSet[ i ] = magicSet[i];
		}
	};

	/**
	* MediaWiki wikitext "Parser" constructor
	*
	* @param {String} wikiText the wikitext to be parsed
	* @return {Object} parserObj returns a parser object that has methods for getting at
	* things you would want
	*/
	mw.Parser = function( wikiText, options) {
		// return the parserObj
		this.init( wikiText, options ) ;
		return this;
	};

	mw.Parser.prototype = {

		// the parser output string container
		pOut: false,

		init: function( wikiText, parserOptions ) {
			this.wikiText = wikiText;

			var defaultParserOptions = {
				'templateParCount' : 2
			};

			this.options = $j.extend( defaultParserOptions, parserOptions);
		},

		// Update the text value
		updateText : function( wikiText ) {
			this.wikiText = wikiText;

			// invalidate the output ( will force a re-parse )
			this.pOut = false;
		},
		// checks if the required number of parenthesis are found
		// xxx this is just a stop gap solution
		checkParlookAheadOpen: function(text, a){
			if( this.options.templateParCount == 2 ){
				return ( text[a] == '{' && text[a + 1] == '{' );
			} else if( this.options.templateParCount == 3 ) {
				return ( text[a] == '{' && text[a + 1] == '{' && text[a + 2] == '{');
			}
		},
		checkParlookAheadClose: function( text, a){
			if( this.options.templateParCount == 2 ){
				return ( text[a] == '}' && text[a + 1] == '}' );
			} else if( this.options.templateParCount == 3 ) {
				return ( text[a] == '}' && text[a + 1] == '}' && text[a + 2] == '}');
			}
		},
		/**
		 * Quickly recursive / parse out templates:
		 */
		parse: function() {
			var _this = this;
			function recurseTokenizeNodes ( text ) {
				var node = { };
				// Inspect each char
				for ( var a = 0; a < text.length; a++ ) {
					if ( _this.checkParlookAheadOpen( text, a ) ) {
						a = a + _this.options.templateParCount;
						node['parent'] = node;
						if ( !node['child'] ) {
							node['child'] = new Array();
						}

						node['child'].push( recurseTokenizeNodes( text.substr( a ) ) );
					} else if ( _this.checkParlookAheadClose( text, a ) ) {
						a++;
						if ( !node['parent'] ) {
							return node;
						}
						node = node['parent'];
					}
					if ( !node['text'] ) {
						node['text'] = '';
					}
					// Don't put }} closures into output:
					if ( text[a] && text[a] != '}' ) {
							node['text'] += text[a];
					}
				}
				return node;
			}

			/**
			 * Parse template text as template name and named params
			 * @param {String} templateString Template String to be parsed
			 */
			function parseTmplTxt( templateString ) {
				var templateObject = { };

				// Get template name:
				templateName = templateString.split( '\|' ).shift() ;
				templateName = templateName.split( '\{' ).shift() ;
				templateName = templateName.replace( /^\s+|\s+$/g, "" ); //trim

				// Check for arguments:
				if ( templateName.split( ':' ).length == 1 ) {
					templateObject["name"] = templateName;
				} else {
					templateObject["name"] = templateName.split( ':' ).shift();
					templateObject["arg"] = templateName.split( ':' ).pop();
				}

				var paramSet = templateString.split( '\|' );
				paramSet.splice( 0, 1 );
				if ( paramSet.length ) {
					templateObject.param = new Array();
					for ( var pInx =0; pInx < paramSet.length; pInx++ ) {
						var paramString = paramSet[ pInx ];
						// check for empty param
						if ( paramString == '' ) {
							templateObject.param[ pInx ] = '';
							continue;
						}
						for ( var b = 0 ; b < paramString.length ; b++ ) {
							if ( paramString[b] == '=' && b > 0 && b < paramString.length && paramString[b - 1] != '\\' ) {
								// named param
								templateObject.param[ paramString.split( '=' ).shift() ] =	paramString.split( '=' ).pop();
							} else {
								// indexed param
								templateObject.param[ pInx ] = paramString;
							}
						}
					}
				}
				return templateObject;
			}

			/**
			 * Get the Magic text from a template node
			 */
			function getMagicTxtFromTempNode( node ) {
				node.templateObject = parseTmplTxt ( node.text );
				// Do magic swap if template key found in pMagicSet
				if ( node.templateObject.name in pMagicSet ) {
					var nodeText = pMagicSet[ node.templateObject.name ]( node.templateObject );
					return nodeText;
				} else {
					// don't swap just return text
					return node.text;
				}
			}

			/**
			* swap links of form [ ] for html a links or jquery helper spans
			* NOTE: this could be integrated into the tokenizer but for now
			* is a staged process.
			*
			* @param text to swapped
			*/
			function linkSwapText( text ) {
				//mw.log( "linkSwapText::" + text );
				var re = new RegExp( /\[([^\s]+[\s]+[^\]]*)\]/g );
				var matchSet = text.match( re );

				if( !matchSet ){
					return text;
				}

				text = text.replace( re , LINK_SWAP_STRING );

				for( var i=0; i < matchSet.length; i++ ) {
					// Strip the leading [ and trailing ]
					var matchParts = matchSet[i].substr(1, matchSet[i].length-2);
					
					// Check for special jQuery type swap and replace inner JQUERY_SWAP_STRING not value
					if( matchParts.indexOf( JQUERY_SWAP_STRING ) !== -1 ) {
						// parse the link as html
						var $matchParts = $j('<span>' + matchParts + '</span>' );
					
						$jQuerySpan = $matchParts.find('#' +JQUERY_SWAP_STRING + i );
					
						var linkText = $matchParts.text();
						//mw.log(" going to swap in linktext: " + linkText );
						$jQuerySpan.text( linkText );
					
						text = text.replace( LINK_SWAP_STRING, $j('<span />' ).append( $jQuerySpan ).html() );
					} else {
					 	// do text string replace
					 	matchParts = matchParts.split(/ /);
					 	var link = matchParts[0];
					 	matchParts.shift();
					 	var linkText = matchParts.join(' ');
					
					 	text = text.replace( LINK_SWAP_STRING, '<a href="' + link + '">' + linkText + '</a>' );
					}
				}
				return text;
			}

			/**
			 * recurse_magic_swap
			 *
			 * Go last child first swap upward:
			 */
			var pNode = null;
			function recurse_magic_swap( node ) {
				if ( !pNode )
					pNode = node;

				if ( node['child'] ) {
					// swap all the kids:
					for ( var i in node['child'] ) {
						var nodeText = recurse_magic_swap( node['child'][i] );
						// swap it into current
						if ( node.text ) {
							node.text = node.text.replace( node['child'][i].text, nodeText );
						}
						// swap into parent
						pNode.text = pNode.text.replace( node['child'][i].text, nodeText );
					}
					// Get the updated node text
					var nodeText = getMagicTxtFromTempNode( node );
					pNode.text = pNode.text.replace( node.text , nodeText );
					// return the node text
					return node.text;
				} else {
					return getMagicTxtFromTempNode( node );
				}
			}

			// Parse out the template node structure:
			this.pNode = recurseTokenizeNodes ( this.wikiText );

			// Strip out the parent from the root
			this.pNode['parent'] = null;

			// Do the recursive magic swap text:
			this.pOut = recurse_magic_swap( this.pNode );

			// Do link swap
			this.pOut = linkSwapText( this.pOut );
		},

		/**
		 * templates
		 *
		 * Get a requested template from the wikitext (if available)
		 * @param templateName
		 */
		templates: function( templateName ) {
			this.parse();
			var tmplSet = new Array();
			function getMatchingTmpl( node ) {
				if ( node['child'] ) {
					for ( var i in node['child'] ) {
						getMatchingTmpl( node['child'] );
					}
				}
				if ( templateName && node.templateObject ) {
					if ( node.templateObject['name'] == templateName )
						tmplSet.push( node.templateObject );
				} else if ( node.templateObject ) {
					tmplSet.push( node.templateObject );
				}
			}
			getMatchingTmpl( this.pNode );
			return tmplSet;
		},

		/**
		* getTemplateVars
		* returns a set of template values in a given wikitext page
		*
		* NOTE: should be integrated with the usability wikitext parser
		*/
		getTemplateVars: function() {
			//mw.log('matching against: ' + wikiText);
			templateVars = new Array();
			var tempVars = wikiText.match(/\{\{\{([^\}]*)\}\}\}/gi);

			// Clean up results:
			for(var i=0; i < tempVars.length; i++) {
				//match
				var tvar = tempVars[i].replace('{{{','').replace('}}}','');

				// Strip anything after a |
				if(tvar.indexOf('|') != -1) {
					tvar = tvar.substr(0, tvar.indexOf('|'));
				}

				// Check for duplicates:
				var do_add=true;
				for(var j=0; j < templateVars.length; j++) {
					if( templateVars[j] == tvar)
						do_add=false;
				}

				// Add the template vars to the output obj
				if(do_add)
					templateVars.push( tvar );
			}
			return templateVars;
		},

		/**
		 * Returns the transformed wikitext
		 *
		 * Build output from swappable index
		 * 		(all transforms must be expanded in parse stage and linearly rebuilt)
		 * Alternatively we could build output using a place-holder & replace system
		 * 		(this lets us be slightly more sloppy with ordering and indexes, but probably slower)
		 *
		 * Ideal: we build a 'wiki DOM'
		 * 		When editing you update the data structure directly
		 * 		Then in output time you just go DOM->html-ish output without re-parsing anything
		 */
		getHTML: function() {
			// wikiText updates should invalidate pOut
			if ( ! this.pOut ) {
				this.parse();
			}
			return this.pOut;
		}
	};

}) ( window.mw );