mw.SmilLayout = function( $layout ){
	return this.init( $layout );
};

mw.SmilLayout.prototype = {
	// Stores the number of assets we are currently loading
	mediaLoadingCount : 0,

	// Stores the callback function for once assets are loaded
	mediaLoadedCallback : null,

	// Stores the current top z-index for "putting things on top"
	topZindex: 1,

	// Font size em map
	// based on: http://style.cleverchimp.com/font_size_intervals/altintervals.html
	emFontSizeMap : {
		'xx-small' : '.57em',
		'x-small' : '.69em',
		'small' : '.83em',
		'medium' : '1em',
		'large' : '1.2em',
		'x-large' : '1.43em',
		'xx-large' : '1.72em'
	},

	// Constructor:
	init: function( smilObject ){
		// Setup a pointer to parent smil Object
		this.smil = smilObject;

		// Set the smil layout dom:
		this.$dom = this.smil.getDom().find( 'layout' );

		// Reset the htmlDOM cache
		this.$rootLayout = null;
	},

	/**
	 * Setup the layout if not already setup
	 */
	setupLayout: function( $renderTarget ){
		if( ! $renderTarget.find( '.smilRootLayout').length ) {
			$renderTarget.append( this.getRootLayout() );
		}
		this.$rootLayout =$renderTarget.find( '.smilRootLayout');
	},

	getTargetAspectRatio:function(){
		return this.smil.embedPlayer.getHeight() / this.smil.embedPlayer.getWidth();
	},

	/*
	 * Get layout
	 */
	getRootLayout: function(){
		var _this = this;
		mw.log( "SmilLayout::getRootLayout:" );
		if( !this.$rootLayout ) {
			// Setup target Size:
			this.targetWidth = this.smil.embedPlayer.getWidth();
			this.targetHeight = this.smil.embedPlayer.getHeight();

			this.$rootLayout = $j('<div />' )
				.attr( 'id', _this.smil.embedPlayer.id + '_smil-root-layout' )
				.addClass( 'smilRootLayout' )
				.css( {
					'position': 'absolute',
					'width' : '100%',
					'height' : '100%',
					'overflow': 'hidden'
				});

			// Update the root layout css
			this.$rootLayout.css( _this.getRootLayoutCss() );

			// Update the root layout html
			this.$rootLayout.html( _this.getRootLayoutHtml() );
		}
		return this.$rootLayout;
	},

	/**
	 * Get and increment the top z-index counter:
	 */
	getTopZIndex: function(){
		return this.topZindex++;
	},

	/**
	 * Draw a smilElement to the layout.
	 *
	 * If the element does not exist in the html dom add it.
	 *
	 * @parma {Element} smilElement to be drawn.
	 */
	drawElement: function( smilElement ) {
		var _this = this;

		// Check for quick "show" path:
		var $targetElement = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) );

		if( $targetElement.length ){
			$targetElement.show();
			return ;
		}
		var $regionTarget = this.getRegionTarget( smilElement );
		// Make sure we have a $regionTarget
		if( !$regionTarget ){
			mw.log("Error missing regionTarget");
			return ;
		}

		// Append the Smil element to the target region
		_this.drawPlayerSmilElement(smilElement, $regionTarget );

	},

	/**
	 * Add the transformed smil element to the $regionTarget
	 *
	 * @param
	 */
	drawPlayerSmilElement: function( smilElement, $regionTarget ) {
		var _this = this;
		mw.log('SmilLayout:: drawPlayerSmilElement: ' );
		var smilType = this.smil.getRefType( smilElement );
		
		// Give the static content a getSmilElementPlayerID for player layer control
		var $target = $j('<div />')
			.attr('id', _this.smil.getSmilElementPlayerID( smilElement ) )
			.css({
				'width':'100%',
				'height':'100%',
				'position' : 'absolute',
				'top' : '0px',
				'left' : '0px'
			})
		$regionTarget.append( $target );
		
		switch( smilType ){
			// Static content can use drawSmilElementToTarget function:
			case 'mwtemplate':
			case 'img':
			case 'cdata_html':
			case 'smiltext':
				this.drawSmilElementToTarget( smilElement, $target );
				return ;
			break;
			case 'video':
				$target.append( this.getSmilVideoPlayerHtml( smilElement ) );
				return ;
			break;
			case 'audio':
				$target.append( this.getSmilAudioPlayerHtml( smilElement ) );
				return ;
			break;
		}

		mw.log( "Error: Could not find smil layout transform for element type: " +
				smilType + ' of type ' + $j( smilElement ).attr( 'type' ) );
		$regionTarget.append( $j('<span />')
				.attr( 'id' , this.smil.getSmilElementPlayerID( smilElement ) )
				.text( 'Error: unknown type:' + smilType )
		)
	},

	drawSmilElementToTarget: function( smilElement, $target, relativeTime, callback ){
		var _this = this;
		if( $target.length == 0 ){
			mw.log("Error drawSmilElementToTarget to empty target");
			return ;
		}
		// Parse the time in case it came in as human input
		relativeTime = this.smil.parseTime( relativeTime );
		
		mw.log('SmilLayout::drawSmilElementToTarget: ' + $j(smilElement).attr('id') + ' relative time:' + relativeTime );

		switch ( this.smil.getRefType( smilElement ) ){
			case 'video':
				this.getVideoCanvasThumb( smilElement, $target, relativeTime, callback );
				return false;
			break;
			case 'img':
				// xxx We should use canvas here but for now just hack it up:
				$target.html(
					$j('<img />')
					.attr({
						'src' : this.smil.getAssetUrl(
								$j( smilElement).attr( 'src' )
							)
					})
				);
				var img = $target.find('img').get(0)
				_this.getNaturalSize( img, function( natrualSize ){
					_this.fitMeetBest(
						img,
						natrualSize,
						{
							'width' : $target.width(),
							'height' : $target.height()
						}
					);
					
					// Check for panZoom attribute
					if( $j( smilElement ).attr('panZoom') ){
						_this.panZoomLayout( smilElement, $target, img );
					}
					
					if( callback ){
						callback();
					}
				});
				return
			break;
			case 'mwtemplate':
				$target.loadingSpinner();
				this.getSmilTemplateHtml( smilElement, $target, callback );
				return;
			break;
			case 'cdata_html':
				// Put the cdata into the smil element:
				$target.append(
					this.getSmilCDATAHtml( smilElement, $target.width() )
				);
			break;
			// Smil Text: http://www.w3.org/TR/SMIL/smil-text.html
			// We support a subset
			case 'smiltext':
				$target.append( this.getSmilTextHtml( smilElement ) ) ;
				return ;
			break;
			case 'audio':
				var titleStr = ( $j( smilElement ).attr('title') ) ?
						$j( smilElement ).attr('title') :
						gM( 'mwe-sequencer-untitled-audio' );

				// draw an audio icon / title the target
				$target.append(
					$j('<span />')
					.addClass( 'ui-icon ui-icon-volume-on')
					.attr('title', titleStr)
					.css( 'position', 'absolute')
					,
					$j('<span />')
					.attr('title', titleStr)
					.css({
						'position': 'absolute',
						'left':'16px',
						'font-size' : 'x-small'
					})
					.text( titleStr )
				)
			break;
		}
		// assume direct callback if callback not passed in content type switch
		if( callback )
			callback();
	},

	getVideoCanvasThumb: function( smilElement, $target, relativeTime, callback ){
		var _this = this;
		var naturaSize = {};
		var drawElement = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).find('video').get(0);
		mw.log( "SmilLayout:: getVideoCanvasThumb ");
		var drawFrame = function( drawElement ){			
			if( !drawElement ){
				mw.log( 'Error: SmilLayout::getVideoCanvasThumb:Draw element not loaded or defined')
				return ;
			}
			mw.log( "SmilLayout::getVideoCanvasThumb: drawFrame " );
			naturaSize.height = drawElement.videoHeight;
			naturaSize.width = drawElement.videoWidth;
			
			// @@todo we should call the panzoom transform
			
			// Draw the thumb via canvas grab
			// NOTE canvas scale issue prevents redraw at thumb resolution
			// xxx should revisit thumb size issue:
			try{
				$target.html( $j('<canvas />')
					.attr({
						height: naturaSize.height,
						width : naturaSize.width
					}).css( {
						height:'100%',
						widht:'100%'
					})
					.addClass("ui-corner-all")
				)
				.find( 'canvas')
					.get(0)
					.getContext('2d')
					.drawImage( drawElement, 0, 0);
			} catch (e){
				mw.log("Error:: getVideoCanvasThumb : could not draw canvas image");
			}
			if( callback ){
				callback();
			}
		}

		// Check if relativeTime transform matches current absolute time then
		// render directly:
		var drawTime = ( relativeTime + this.smil.parseTime( $j( smilElement ).attr('clipBegin') ) );
		if( this.smil.isSameFrameTime( drawElement.currentTime, drawTime ) ) {
			mw.log("SmilLayout::getVideoCanvasThumb: Draw time:" + drawTime + " matches video time drawFrame NOW:" +drawElement.currentTime );
			drawFrame( drawElement );
		} else {
			// check if we need to spawn a video copy for the draw request
			mw.log( 'SmilLayout::getVideoCanvasThumb: Clone object' );
			// span new draw element
			var $tmpFrameNode = $j( smilElement ).clone();
			$tmpFrameNode.attr('id', $j( smilElement).attr('id') + '_tmpFrameNode' );
			this.smil.getBuffer().bufferedSeekRelativeTime( $tmpFrameNode, relativeTime, function(){
				// update the drawElement
				drawElement = $j( '#' + _this.smil.getSmilElementPlayerID( $tmpFrameNode ) ).get(0);
				drawFrame( drawElement );
				// Remove the temporary node from dom
				$j( drawElement ).remove();
			});
		}
	},

	/**
	 * Get a region target for a given smilElement
	 */
	getRegionTarget: function( smilElement ){
		var regionId = $j( smilElement ).attr( 'region');
		if( regionId ){
			var $regionTarget = this.$rootLayout.find( '#' + regionId );
			// Check for region target in $rootLayout
			if( $regionTarget.length == 0 ) {
				mw.log( "Error in SmilLayout::renderElement, Could not find region:" + regionId );
				return false;
			}
		} else {
			// No region provided use the rootLayout:
			$regionTarget = this.$rootLayout;
		}
		return $regionTarget;
	},

	/**
	 * Hide a smilElement in the layout
	 */
	hideElement: function( smilElement ){
		//mw.log(" hide: " + this.smil.getSmilElementPlayerID( smilElement ));
		// Check that the element is already in the dom
		var $targetElement = this.$rootLayout.find( '#' + this.smil.getSmilElementPlayerID( smilElement ) );
		if( $targetElement.length ){
			// Issue a quick hide request
			$targetElement.hide();
		}
	},



	/**
	 * Return the video
	 */
	getSmilVideoPlayerHtml: function( smilElement ){
		return $j('<video />')
			.attr( {
				'src' : this.smil.getAssetUrl( $j( smilElement ).attr( 'src' ) )
			} )
			.addClass( 'smilFillWindow' )
	},

	/**
	 * Return audio element ( by default audio tracks are hidden )
	 */
	getSmilAudioPlayerHtml: function ( smilElement ){
		return $j('<audio />')
		.attr( {
			'src' : this.smil.getAssetUrl( $j( smilElement ).attr( 'src' ) )
		} )
		.css( {
			'width': '0px',
			'height' : '0px'
		});
	},

	/**
	 * Add Smil Template to a $target
	 */
	getSmilTemplateHtml: function( smilElement, $target, callback ){
		var _this = this;
		var addTemplateHtmlToTarget = function(){
			// Add the html to the target:
			mw.log( 'addTemplateHtmlToTarget:: with width:' + $target.width() );
			$target.empty().append(
				_this.getScaledHtml(
					 	// The scaled template:
						$j( $j( smilElement).data('templateHtmlCache') ),

						// The smilElement
						smilElement,

						// The target width to be scaled
						$target.width()
					)
			);
			// Run the callback
			if( callback )
				callback();
		}
		// Check if we have the result data in the cache:
		if( $j( smilElement).data('templateHtmlCache') ){
			addTemplateHtmlToTarget()
			if( callback )
				callback();
			return ;
		}

		mw.log("getSmilTemplateHtml:: x-wikitemplate:: " + $j( smilElement).attr( 'apititlekey' ) + " to target:" + $target.attr('class'));;
		// build a wikitext call ( xml keys lose case when put into xml )
		var templateKey = $j( smilElement).attr( 'apititlekey' );
		if(!templateKey){
			mw.log("Error: wikitemplate without title key")
			return ;
		} else {
			templateKey = templateKey.replace('Template:', '');
		}
		var apiProviderUrl = mw.getApiProviderURL( $j( smilElement).attr('apiprovider') );
		if(!apiProviderUrl){
			mw.log("Error: wikitemplate without api provider url")
		}

		var wikiTextTemplateCall = '{{' + templateKey ;
		var paramText = '';
		$j( smilElement).find('param').each(function( inx, paramNode ){
			paramText +='|' + $j( paramNode ).attr('name') +
						'= ' +
						$j( paramNode ).attr('value') +
						"\n";
		});
		// Close up the template call
		if( paramText!= ''){
			wikiTextTemplateCall+="\n" + paramText + '}}';
		} else{
			wikiTextTemplateCall+='}}'
		}

		var request = {
			'action' : 'parse',
			'text': wikiTextTemplateCall,
			'_method' : 'post'
		};
		// Check if we have the titleKey for the sequence and use that as context title
		var titleKey = this.smil.getEmbedPlayer().apiTitleKey;
		if( titleKey ){
			request['title'] = titleKey;
		}

		mw.getJSON( apiProviderUrl, request, function( data ){
			if( data && data.parse && data.parse.text && data.parse.text['*'] ){
				// Mediawiki protects against xss but filter the parsed text 'just in case'
				$j( smilElement).data('templateHtmlCache',
					_this.smil.getFilterdHtml( data.parse.text['*'] ).html()
				)
				// Check if we have a load callback associated with the smilElement:
				if( $j( smilElement ).data('loadCallback') ){
					 $j( smilElement ).data('loadCallback')();
				}
				addTemplateHtmlToTarget();
			} else{
				mw.log("Error: addSmilCDATAHtml could not get template data from the wiki")
			}
			if( callback )
				callback();
		});
	},

	getSmilCDATAHtml: function( smilElement, targetWidth){

		mw.log("getSmilCDATAHtml:" + $j( smilElement ).attr('id') +' :' + targetWidth );

		// Get "clean" smil data
		var el = $j( smilElement ).get(0);
		var xmlCdata = '';
		for ( var i=0; i < el.childNodes.length; i++ ) {
			var node = el.childNodes[i];
			// Check for text cdata Node type:
			if( node.nodeType == 4 ) {
				xmlCdata += node.nodeValue;
			}
		}
		var $htmlLayout = this.smil.getFilterdHtml( xmlCdata );

		// Return scaled and filtered html
		return this.getScaledHtml( $htmlLayout,	smilElement, targetWidth );
	},

	getScaledHtml: function( $htmlLayout, smilElement, targetWidth){
		var _this = this;
		var textCss = this.transformSmilCss( smilElement , targetWidth);

		// See if we need to scale
		var scalePercent = ( targetWidth / this.getVirtualWidth() );

		// Don't scale fonts as dramatically:
		var fontScalePercent = Math.sqrt( scalePercent );

		// Scale the
		if( scalePercent != 1 ){
			$htmlLayout.find('img').each( function(inx, image ){
				// make sure each image is loaded before we transform,
				// AND updates $htmlLayout output in-place
				$j( image ).load(function(){
					// if the image has an height or width scale by scalePercent
					if ( $j( image ).width() ){
						var imageTargetWidth = scalePercent* $j( image ).width();
						var imageTargetHeight = scalePercent* $j( image ).height()
					} else if( image.naturalWidth ){
						// check natural width?
						imageTargetWidth = scalePercent * image.naturalWidth;
						imageTargetHeight = scalePercent * image.naturalHeight;
					}
					// scale the image:
					$j( image ).css({
						 'width' : imageTargetWidth,
						 'height' :imageTargetHeight
					})
				});
			});

			// Switch any named font-size attribute to em
			$htmlLayout.find('[style]').each( function(inx, node){
				if( $j(node).css('font-size') ){
					if( _this.emFontSizeMap[ $j(node).css('font-size') ] ){
						$j(node).css('font-size', _this.emFontSizeMap[ $j(node).css('font-size') ] );
					} else if( $j(node).css('font-size').indexOf('px') != -1 ) {
						// Translate absolute pixel size to relative
						$j(node).css('font-size',
							( ( fontScalePercent  ) * parseFloat( $j(node).css('font-size') ) ) + 'px'
						);
					}
				}
			});

			// Strip any links for thumbs of player
			$htmlLayout.find('a').attr('href', '#');
		}

		// Return the cdata
		return $j('<div />')
			// Wrap in font-size percentage relative to virtual size
			.css( {
				'font-size': ( scalePercent *100 ) + '%',
				'width': '100%',
				'height' : '100%'
			})
			.append(
				$htmlLayout.css( textCss )
			);
	},

	/**
	 * Get a text element html
	 */
	getSmilTextHtml: function( textElement ) {
		var _this = this;

		// Empty initial text value
		var textValue = '';

		// If the textElement has no child node directly set the text value
		// ( if has child nodes, text will be selected by time in
		// SmilAnimate.transformTextForTime )
		if( $j( textElement ).children().length == 0 ){
			mw.log( 'Direct text value to: ' + textValue);
			textValue = $j( textElement ).text();
		}

		var textCss = _this.transformSmilCss( textElement );

		// Return the htmlElement
		return $j('<span />')
			.attr( 'id' , this.smil.getSmilElementPlayerID( textElement ) )
			// Wrap in font-size percentage relative to virtual size
			.css( 'font-size', ( ( this.targetWidth / this.getVirtualWidth() )*100 ) + '%' )
			.html(
				$j('<span />')
				// Transform smil css into html css:
				.css( textCss	)
				// Add the text value
				.text( textValue )
			);
	},

	/**
	 * Get Image html per given smil element
	 *
	 * @param {element}
	 *            imgElement The image tag element to be updated
	 */
	getSmilImgHtml: function( smilImg ) {
		var _this = this;
		var $image = $j('<img />')
		.attr( {
			'id' : this.smil.getSmilElementPlayerID( smilImg ),
			'src' : this.smil.getAssetUrl( $j( smilImg ).attr( 'src' ) )
		} )
		// default width 100% upper left
		.css({
			'position' : 'absolute',
			'top' : '0px',
			'left' : '0px',
			'width': '100%'
		})

		return $image;
	},
	doSmilElementLayout: function( smilElement ){
		var _this = this;

		var img = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).get(0);
		_this.getNaturalSize( img, function( naturalSize) {
			_this.doAssetLayout( smilElement , naturalSize);
		});
	},
	
	/**
	 * Get the natural size of a media asset
	 * @param img
	 * @param callback
	 * @return
	 */
	getNaturalSize: function( media , callback){
		// note this just works for images atm
		if( !media ){
			mw.log("Error getNaturalSize for null image ");
			callback( false );
			return ;
		}
		if( media.naturalWidth ){
			callback( {
				'width' : media.naturalWidth,
				'height' : media.naturalHeight
			} )
		} else {
			$j( media ).load(function(){
				callback( {
					'width' : media.naturalWidth,
					'height' : media.naturalHeight
				} )
			});
		}
	},
	/**
	 * Layout an asset
	 */
	doAssetLayout: function( smilElement, naturalSize ){
		var _this = this;
		// We default smil layout to meetBest
		var fitMode = $j( smilElement).attr('fit');
		if( !fitMode ){
			fitMode = 'meetBest'
		}
		if( fitMode == 'meetBest' ){
			var targetSize = {
				'width' : this.smil.embedPlayer.getWidth(),
				'height' : this.smil.embedPlayer.getHeight()
			}
			this.fitMeetBest(
				$j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).get(0),
				naturalSize,
				targetSize
			);
		} else {
			mw.log("Layout mode: " + fitMode + ' not yet supported');
		}

		// Check for panZoom attribute
		if( $j( smilElement).attr('panZoom') ){
			_this.panZoomLayout( smilElement );
		}
		// Check for rotate property: 
		if( $j( smilElement).attr('rotate') ){
			_this.rotateLayout( smilElement );
		}
	},

	// http://www.w3.org/TR/SMIL/smil-layout.html#adef-fit
	// xxx should add the other fitting modes
	fitMeetBest: function( element, natrualSize, targetSize ){
		var _this = this;


		// xxx Should read smil "imgElement" fill type
		var imageCss = _this.getDominateAspectTransform( natrualSize, targetSize, 100 );
		mw.log('SmilLayout::fitMeetBest: ns'+ natrualSize.width + ' ts: ' + targetSize.width +
				' css: w:' + imageCss.width + ' h:' + imageCss.height);
		// update the layout of the element
		$j( element ).css( imageCss );
	},

	getDominateAspectTransform: function( natrualSize, targetSize, transformPercent ){
		var _this = this;
		var transformCss = {}
		if( ! targetSize ){
			targetSize = {
				'width' : this.smil.embedPlayer.getWidth(),
				'height' : this.smil.embedPlayer.getHeight()
			};
		}

		// Fit the image per the provided targetWidth closure
		/*mw.log( 'getDominateAspectTransform:: naspect:' +
				( natrualSize.width / natrualSize.height ) +
				' taspect: ' + targetSize.width + '/' + targetSize.height + ' = ' + ( targetSize.width / targetSize.height )
			);*/

		var targetAspect = ( parseFloat( targetSize.width ) / parseFloat( targetSize.height ) )
		var natrualAspect = ( natrualSize.width / natrualSize.height );

		// pad the natural size ratio by .01 so that aspect ratio rounding does not
		// xxx height domination here may be confused refactor this check
		if( natrualAspect >= targetAspect ){
			transformCss.width = parseFloat( transformPercent ) + '%';
			transformCss.height = null;
			/*transformCss.height = ( parseFloat( transformPercent ) * (
					( natrualSize.height / natrualSize.width ) /
						( targetSize.height / targetSize.width )
					)
				) + '%';*/
		}

		// Fit vertically
		if(! transformCss.height || natrualAspect < targetAspect ){
			transformCss.height = parseFloat( transformPercent ) + '%';
			transformCss.width = null;
			/*transformCss.width = ( parseFloat( transformPercent ) *
					( natrualSize.height / natrualSize.width ) /
					( targetSize.width / targetSize.height )
				) + '%';*/
		}
		return transformCss;
	},
	/**
	 * Rotate layout update ( just wraps the animate update element call ) 
	 */
	rotateLayout: function( smilElement, $target ){
		this.smil.getAnimate().updateElementRotation( smilElement, $target );
	},
	
	/**
	 * layout function
	 */
	panZoomLayout: function( smilElement, $target, layoutElement ){
		var _this = this;
		//mw.log( 'panZoomLayout:' + $j( smilElement).attr('id') );
		var panZoom = $j( smilElement).attr('panZoom').split(',');
		if( !layoutElement ){
			var layoutElement = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).find('img,video').get(0);
			if( !layoutElement){
				mw.log('Error getting layoutElement for ' + $j( smilElement).attr('id') );
			}
		}

		_this.getNaturalSize( layoutElement, function( natrualSize ){
			// Check if the transfrom is needed:
			if( parseInt( panZoom.left ) == 0
				&&
				parseInt( panZoom.top ) == 0
				&&
				( parseInt( panZoom.width ) == 100 && panZoom.width.indexOf('%') != -1 )
				&&
				( parseInt( panZoom.height ) == 100 && panZoom.height.indexOf('%') != -1 )
			){
				// no transform is needed
				mw.log("no transofmr needed: " + parseInt( panZoom.left ) + ' = 0 && ' +
						( parseInt( panZoom.width ) ) );
				return ;
			}
			// Get percent values
			var percentValues = _this.smil.getAnimate().getPercentFromPanZoomValues( panZoom, natrualSize );
			//mw.log('panZoomLayout::' + 'l:' + percentValues.left + ' t:' + percentValues.top + ' w:' + percentValues.width + ' h:' + percentValues.height );
			// Update the layout via the animation engine updateElementLayout method
			_this.smil.getAnimate().updateElementLayout( smilElement, percentValues, $target, layoutElement );
		});
	},
	
	/**
	 * Parse pan zoom attribute string
	 *
	 * @param panZoomString
	 */
	parsePanZoom: function( panZoomString ){
		var pz = panZoomString.split(',');
		if( pz.length != 4){
			mw.log("Error Could not parse panZoom Attribute: " + panZoomString);
			return {};
		}
		return {
			'left' : pz[0],
			'top' : pz[1],
			'width' : pz[2],
			'height': pz[3]
		}
	},

	/**
	 * Add all the regions to the root layout
	 */
	getRootLayoutHtml: function(){
		var _this = this;
		var $layoutContainer = $j( '<div />' );
		this.$dom.find( 'region' ).each( function( inx, regionElement ) {
			$layoutContainer.append(
				$j( '<div />' )
				.addClass('smilRegion' )
				.css({
					'position' : 'absolute'
				})
				// Transform the smil attributes into html attributes
				.attr( _this.transformSmilAttributes( regionElement ) )
				// Transform the css attributes into percentages
				.css(
					_this.transformVirtualPixleToPercent(
						_this.transformSmilCss( regionElement )
					)
				)
			);
		});
		return $layoutContainer.children();
	},

	/**
	 * Get the root layout object with updated html properties
	 */
	getRootLayoutCss: function( ){

		if( this.$dom.find( 'root-layout').length ) {
			if( this.$dom.find( 'root-layout').length > 1 ) {
				mw.log( "Error document should only contain one root-layout element" );
				return ;
			}
			mw.log("getRootLayout:: Found root layout" );

			// Get the root layout in css
			var rootLayoutCss = this.transformSmilCss( this.$dom.find( 'root-layout') );

			if( rootLayoutCss['width'] ) {
				this.virtualWidth = rootLayoutCss['width'];
			} else {
				this.virtualWidth = this.smil.getEmbedPlayer().getWidth();
			}
			if( rootLayoutCss['height'] ) {
				this.virtualHeight = rootLayoutCss['height'];
			} else {
				this.virtualHeight = this.smil.getEmbedPlayer().getHeight();
			}

			// Merge in transform size to target
			$j.extend( rootLayoutCss, this.transformSizeToTarget() );

			// Update the layout css
			return rootLayoutCss;
		}
		return {};
	},
	getVirtualWidth: function(){
		if( !this.virtualWidth ){
			this.virtualWidth = this.smil.getEmbedPlayer().getWidth();
		}
		return this.virtualWidth;
	},
	getVirtualHeight: function(){
		if( !this.virtualHeight ){
			this.virtualHeight = this.smil.getEmbedPlayer().getHeight();
		}
		return this.virtualHeight;
	},

	/**
	 * Translate a root layout pixel point into a percent location using all
	 * percentages instead of pixels lets us scale internal layout browser side
	 * transforms ( instead of a lot javascript css updates )
	 *
	 * @param {object}
	 *            layout Css layout to be translated from virtualWidth &
	 *            virtualHeight
	 */
	transformVirtualPixleToPercent: function( layout, virtualLayout ){
		var percent = { };
		if( !virtualLayout){
			virtualLayout = { 'width' : this.virtualWidth, 'height' : this.virtualHeight };
		}
		if( layout['width'] ) {
			percent['width'] = ( layout['width'] / virtualLayout.width )*100 + '%';
		}
		if( layout['left'] ){
			percent['left'] = ( layout['left'] / virtualLayout.width )*100 + '%';
		}
		if( layout['height'] ) {
			percent['height'] = ( layout['height'] / virtualLayout.height )*100 + '%';
		}
		if( layout['top'] ){
			percent['top'] = ( layout['top'] / virtualLayout.height )*100 + '%';
		}
		return percent;
	},

	/**
	 * Transform virtual height width into target size
	 */
	transformSizeToTarget: function(){

		// Setup target height width based on max window size
		var fullWidth = this.targetWidth - 2 ;
		var fullHeight = this.targetHeight ;

		// Set target width
		var targetWidth = fullWidth;
		var targetHeight = targetWidth * ( this.virtualHeight / this.virtualWidth )

		// Check if it exceeds the height constraint:
		if( targetHeight > fullHeight ){
			targetHeight = fullHeight;
			targetWidth = targetHeight * ( this.virtualWidth / this.virtualHeight );
		}

		var offsetTop = ( targetHeight < fullHeight )? ( fullHeight- targetHeight ) / 2 : 0;
		var offsetLeft = ( targetWidth < fullWidth )? ( fullWidth- targetWidth ) / 2 : 0;

		// mw.log(" targetWidth: " + targetWidth + ' fullwidth: ' + fullWidth +
		// ' :: ' + ( fullWidth- targetWidth ) / 2 );
		return {
			'height': targetHeight,
			'width' : targetWidth,
			'top' : offsetTop,
			'left': offsetLeft
		};

	},

	/**
	 * Transform smil attributes into html attributes
	 */
	transformSmilAttributes: function ( smilElement ){
		$smilElement = $j( smilElement );
		var smilAttributes = {
			'xml:id' : 'id',
			'id' : 'id'
		}
		var attributes = {};
		// Map all the "smil" properties to css
		for( var attr in smilAttributes ){
			if( $smilElement.attr( attr ) ){
				attributes[ smilAttributes[ attr ] ] = $smilElement.attr( attr );
			}
		}
		// XXX TODO Locally scope all ids into embedPlayer.id + _id

		// Translate rootLayout properties into div
		return attributes;
	},

	/**
	 * Transform smil attributes into css attributes
	 *
	 * @param {object}
	 *            $smilElement The smil element to be transformed
	 */
	transformSmilCss: function( smilElement, targetWidth ){
		$smilElement = $j( smilElement );

		// Set target with to master targetWidth if unset.
		if( ! targetWidth ){
			targetWidth = this.targetWidth
		}

		var smilAttributeToCss = {
			'backgroundColor' : 'background-color',
			'backgroundOpacity' : 'opacity',
			'z-index' : 'z-index',
			'width' : 'width',
			'height' : 'height',
			'top' : 'top',
			'right' : 'right',
			'left' : 'left',

			'textColor' : 'color',
			'textFontSize' : 'font-size',
			'textFontStyle' : 'font-style'
		};

		var cssAttributes = {};
		for(var i =0; i < $smilElement[0].attributes.length; i++ ){
			var attr = $smilElement[0].attributes[i];
			if( smilAttributeToCss[ attr.nodeName ] ){
				cssAttributes[ smilAttributeToCss[ attr.nodeName ]] = attr.nodeValue;
			}
		}

		// convert named font sizes to em:
		if( this.emFontSizeMap[ cssAttributes['font-size'] ] ){
			cssAttributes['font-size'] = this.emFontSizeMap[ cssAttributes['font-size'] ];
		}

		// If the font size is pixel based parent span will have no effect,
		// directly resize the pixels
		if( cssAttributes['font-size'] && cssAttributes['font-size'].indexOf('px') != -1 ){
			cssAttributes['font-size'] = ( parseFloat( cssAttributes['font-size'] )
				* ( targetWidth / this.getVirtualWidth() ) ) + 'px';
		}


		// Translate rootLayout properties into div
		return cssAttributes;
	}
}
