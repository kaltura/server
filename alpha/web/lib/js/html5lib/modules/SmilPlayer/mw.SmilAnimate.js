/**
* Handles the smil animate class
*/
mw.SmilAnimate = function( smilObject ){
	return this.init( smilObject );
}
mw.SmilAnimate.prototype = {

	// Constructor:
	init: function( smilObject ){
		this.smil = smilObject;

		this.framerate = mw.getConfig( 'SmilPlayer.framerate');

		this.callbackRate = 1000 / this.framerate;
		this.animateInterval = [];
	},

	/**
	 * Pause any active animation or video playback
	 */
	pauseAnimation: function( smilElement ){
		// Check if the element is in the html player dom:
		if( !$j ( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).length ){
			return ;
		}
		// Pause the animation of a given element ( presently just video )
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
			case 'audio':
				$j ( '#' + this.smil.getSmilElementPlayerID( smilElement ) )
					.find('audio,video').get( 0 ).pause();
			break;
		}
		// non-video elements just pause by clearing any animation loops
		if( this.animateInterval[ this.smil.getSmilElementPlayerID( smilElement ) ] ){
			clearInterval( this.animateInterval[ this.smil.getSmilElementPlayerID( smilElement ) ] );
		}
	},

	/**
	 * Checks if assets are insync
	 */
	getPlaybackSyncDelta: function( time ){
		var _this = this;
		// Get all the elements for the current time:
		var maxOutOfSync = 0;
		this.smil.getBody().getElementsForTime( time, function( smilElement ){
			//mw.log( 'check element: '+ time + ' ' + _this.smil.getSmilElementPlayerID( smilElement ) );
			// var relativeTime = time - smilElement.parentTimeOffset;
			var relativeTime = time - $j( smilElement ).data ( 'startOffset' );
			switch( _this.smil.getRefType( smilElement ) ){
				case 'auido':
				case 'video':
					var media = $j ( '#' + _this.smil.getSmilElementPlayerID( smilElement ) )
						.find('audio,video').get( 0 );
					var mediaTime = ( !media || !media.currentTime )? 0 : media.currentTime;
					//mw.log( "getPlaybackSyncDelta:: mediaeo time should be: " + relativeTime + ' video time is: ' + vidTime );

					var syncOffset = ( relativeTime -mediaTime );
					if( syncOffset > maxOutOfSync ){
						maxOutOfSync = syncOffset;
					}
				break;
			}
		});
		// Return the max out of sync element
		return maxOutOfSync;
	},

	/**
	* Animate a smil transform per supplied time.
	* @param {Element} smilElement Smil element to be animated
	* @param {float} animateTime Float time target for element transform
	* @param {float} deltaTime Extra time interval to be animated between animateTransform calls
	* @param {function} callback Optional function to call once the transform is complete
	*/
	animateTransform: function( smilElement, animateTime, deltaTime, callback ){
		var _this = this;
		//mw.log("SmilAnimate::animateTransform:" + $j( smilElement).attr('id') + ' AnimateTime: ' + animateTime + ' delta:' + deltaTime);

		// Check for deltaTime to animate over, if zero
		if( !deltaTime || deltaTime === 0 ){
			// transformElement directly ( no playback or animation loop )
			_this.transformElement( smilElement, animateTime, callback);

			// Also update the smil Element transition directly
			this.smil.getTransitions().transformTransitionOverlay( smilElement, animateTime );

			// We are not playing return:
			return ;
		}


		// Check for special playback types that for playback animation action:
		if( this.smil.getRefType( smilElement ) == 'video'
			||
			this.smil.getRefType( smilElement ) == 'audio' )
		{
			this.transformMediaForPlayback( smilElement, animateTime, callback );
		} else {
			if( callback ){
				callback();
			}
		}
		// Check if the current smilElement has any transforms to be done
		if( ! this.checkForTransformUpdate( smilElement, animateTime, deltaTime ) ){
			// xxx no animate loop needed for element: smilElement
			return ;
		}		

		// We have a delta spawn an short animateInterval

		// Clear any old animation loop	( can be caused by overlapping play requests or slow animation )
		clearInterval( this.animateInterval[ this.smil.getSmilElementPlayerID( smilElement ) ] );

		// Start a new animation interval
		var animationStartTime = new Date().getTime();
		var animateTimeDelta = 0;

		this.animateInterval[ this.smil.getSmilElementPlayerID( smilElement ) ] =
			setInterval(
				function(){
					var timeElapsed = new Date().getTime() - animationStartTime;
					// Set the animate Time delta
					animateTimeDelta += _this.callbackRate;

					// See if the animation has expired:
					if( animateTimeDelta > deltaTime || timeElapsed > deltaTime ){
						// Stop animating:
						clearInterval( _this.animateInterval[ _this.smil.getSmilElementPlayerID( smilElement ) ] );
						return ;
					}

					// Check if there is lag in animations
					if( Math.abs( timeElapsed - animateTimeDelta ) > 100 ){
						mw.log( "Error more than 100ms lag within animateTransform loop: te:" + timeElapsed +
							' td:' + animateTimeDelta + ' diff: ' + Math.abs( timeElapsed - animateTimeDelta ) );
					}

					// Do the transform request:
					_this.transformAnimateFrame( smilElement, animateTime + ( animateTimeDelta/1000 ) );
				},
				this.callbackRate
			);
	},

	/**
	* Quickly check if a given smil element needs to be updated for a given time delta
	*/
	checkForTransformUpdate: function( smilElement, animateTime, deltaTime ){
		// Get the node type:
		var refType = this.smil.getRefType( smilElement )
		// Check for transtion in range
		if( refType != 'audio'
			&&
			this.smil.getTransitions().hasTransitionInRange( smilElement, animateTime )
		){
			return true;
		}

		// Let transition check for updates
		if( refType == 'img' || refType=='video' ){
			 if( $j( smilElement ).attr('transIn') || $j( smilElement ).attr('transOut') ){
				return true;
			 }
		}

		// NOTE: our img node check avoids deltaTime check but its assumed to not matter much
		// since any our supported keyframe granularity will be equal to deltaTime ie 1/4 a second.
		if( refType == 'img' ){
			// Confirm a child animate is in-range
			if( $j( smilElement ).find( 'animate' ).length ) {
				// Check if there are animate elements in range:
				if( this.getSmilAnimateInRange( smilElement, animateTime) ){
					return true;
				}
			}
		}

		// Check if we need to do a smilText clear:
		if( refType == 'smiltext' ){
			var el = $j( smilElement ).get(0);
			for ( var i=0; i < el.childNodes.length; i++ ) {
				var node = el.childNodes[i];
				// Check for text Node type:
				if( node.nodeName == 'clear' ) {
					var clearTime = this.smil.parseTime( $j( node ).attr( 'begin') );
					//mw.log( ' ct: ' + clearTime + ' >= ' + animateTime + ' , ' + deltaTime );
					if( clearTime >= animateTime && clearTime <= ( animateTime + deltaTime ) ) {
						return true;
					}
				}
			}
		}
		//mw.log( 'checkForTransformUpdate::' + nodeName +' ' + animateTime );
		return false;
	},

	/**
	 * Transform Element in an inner animation loop
	 */
	transformAnimateFrame: function( smilElement, animateTime ){
		var refType = this.smil.getRefType( smilElement );
		// Audio / Video has no inner animation per-frame transforms ( aside from
		if( refType != 'video'	&& refType != 'audio' ){
			this.transformElement( smilElement, animateTime );
		}
		// Update the smil Element transition ( applies to all visual media types )
		if( refType != 'audio' ){
			this.smil.getTransitions().transformTransitionOverlay( smilElement, animateTime );
		}
	},

	/**
	* Transform a smil element for a requested time.
	*
	* @param {Element} smilElement Element to be transformed
	* @param {float} animateTime The relative time to be transformed.
	*/
	transformElement: function( smilElement, animateTime , callback) {
		//mw.log("SmilAnimate::transformForTime:" + animateTime );
		switch( this.smil.getRefType( smilElement ) ){
			case 'smiltext':
				this.transformTextForTime( smilElement, animateTime );
			break;
			case 'img':
				this.transformImageForTime( smilElement, animateTime );		
			break;
			case 'video':
			case 'audio':
				// media transforms can take more time so pass the callback
				this.transformMediaForTime( smilElement, animateTime , callback);
				return ;
			break;
		}
		if( callback ){
			callback();
		}
	},

	/**
	 * Transform video for time
	 * @param {Element} smilElement Smil video element to be transformed
	 * @param {time} animateTime Relative time to be transformed
	 */
	transformMediaForTime: function( smilElement, animateTime, callback ){
		// Get the video element
		var assetId = this.smil.getSmilElementPlayerID( smilElement );
		var media = $j('#' + assetId ).find('audio,video').get( 0 );
		
		if( !media ){
			mw.log("Error: transformMediaForTime could not find media asest: " + assetId );
		}


		var mediaSeekTime = animateTime;
		//Add the clipBegin if set
		if( $j( smilElement ).attr( 'clipBegin') &&
			this.smil.parseTime( $j( smilElement ).attr( 'clipBegin') ) )
		{
			mediaSeekTime += this.smil.parseTime( $j( smilElement ).attr( 'clipBegin') );
		}

		// Register a buffer ready callback
		this.smil.getBuffer().mediaBufferSeek( smilElement, mediaSeekTime, function() {
			mw.log( "SmilAnimate::transformMediaForTime: seek complete:" + $j( smilElement ).attr('id') );
			if( callback )
				callback();
		});
	},

	/**
	 * Used to support video playback
	 */
	transformMediaForPlayback: function( smilElement, animateTime, callback){
		var $media = $j ( '#' + this.smil.getSmilElementPlayerID( smilElement ) );

		// Set activePlayback flag ( informs edit and buffer actions )
		$j( smilElement ).data('activePlayback', true)

		// Make the video is being displayed and get a pointer to the video element:
		var media = $media.show().find('audio,video').get( 0 );

		// Set volume to master volume
		media.volume = this.smil.embedPlayer.volume;

		// @@TODO check sync Seek to correct time if off by more than 1 second
		// ( buffer delays management things insync below this range )

		// Bind to play event and issue the initial "play" request
		$j( media ).bind('play', function(){
			if( callback ){
				callback();
				callback = null;
			}
		});		
		media.play();
	},

	/**
	* Transform Text For Time
	*/
	transformTextForTime: function( textElement, animateTime ) {
		//mw.log("transformTextForTime:: " + animateTime );

		if( $j( textElement ).children().length == 0 ){
			// no text transform children
			return ;
		}
		// xxx Note: we could have text transforms in the future:
		var textCss = this.smil.getLayout().transformSmilCss( textElement );

		// Set initial textValue:
		var textValue ='';

		var el = $j( textElement ).get(0);
		for ( var i=0; i < el.childNodes.length; i++ ) {
			var node = el.childNodes[i];
			// Check for text Node type:
			if( node.nodeType == 3 ) {
				textValue += node.nodeValue;
			} else if( node.nodeName == 'clear' ){
				var clearTime = this.smil.parseTime( $j( node ).attr( 'begin') );
				if( clearTime > animateTime ){
					break;
				}
				// Clear the bucket text collection
				textValue = '';
			}
		}

		// Update the text value target
		// xxx need to profile update vs check value
		$j( '#' + this.smil.getSmilElementPlayerID( textElement ) )
		.html(
			$j('<span />')
			// Add the text value
			.text( textValue )
			.css( textCss	)
		)
	},

	transformImageForTime: function( smilImgElement, animateTime ) {
		var _this = this;
		//mw.log( "transformImageForTime:: animateTime:" + animateTime );

		if( $j( smilImgElement ).children().length == 0 ){
			// No animation transform children
			return ;
		}

		var animateInRange = _this.getSmilAnimateInRange( smilImgElement, animateTime, function( animateElement ){
			// mw.log('animateInRange callback::' + $j( animateElement ).attr( 'attributeName' ) );
			switch( $j( animateElement ).attr( 'attributeName' ) ) {
				case 'panZoom':
					// Get the pan zoom css for "this" time
					_this.transformPanZoom ( smilImgElement, animateElement, animateTime );
				break;
				default:
					mw.log("Error unrecognized Animation attributName: " +
						 $j( animateElement ).attr( 'attributeName' ) );
			}
		});
		// No animate elements in range, make sure we transform to previous or to initial state if time is zero
		if( !animateInRange ) {
			if( animateTime == 0 ) {
				// Check if we have native resolution information
				// xxx here would be a good place to check the "fit" criteria
				// http://www.w3.org/TR/SMIL3/smil-layout.html#adef-fit
				// for now we assume fit "attribute" value is "meet"
			}
			// xxx should check for transform to previous
		}
	},

	/**
	* Calls a callback with Smil Animate In Range for a requested time
	*
	* @param {Element} smilImgElement The smil element to search for child animate
	* @param {float} animateTime The target animation time
	* @param {function=} callback Optional function to call with elements in range.
	* return boolean true if animate elements are in range false if none found
	*/
	getSmilAnimateInRange: function( smilImgElement, animateTime, callback ){
		var _this = this;
		var animateInRange = false;
		// Get transform elements in range
		$j( smilImgElement ).find( 'animate' ).each( function( inx, animateElement ){
			var begin = _this.smil.parseTime( $j( animateElement ).attr( 'begin') );
			var duration = _this.smil.parseTime( $j( animateElement ).attr( 'dur') );
			//mw.log( "getSmilAnimateInRange:: b:" + begin +" < " + animateTime + " && b+d: " + ( begin + duration ) + " > " + animateTime );

			// Check if the animate element is in range
			var cssTransform = {};
			if( begin <= animateTime && ( begin + duration ) >= animateTime ) {
				animateInRange = true;
				if( callback ) {
					callback( animateElement );
				}
			}
		});
		return animateInRange;
	},

	/**
	* Get the css layout transforms for a panzoom transform type
	*
	* http://www.w3.org/TR/SMIL/smil-extended-media-object.html#q32
	*/
	transformPanZoom: function( smilImgElement, animateElement, animateTime ){
		var _this = this;
		var begin = this.smil.parseTime( $j( animateElement ).attr( 'begin') );
		var duration = this.smil.parseTime( $j( animateElement ).attr( 'dur') );

		// internal offset
		var relativeAnimationTime = animateTime - begin;

		// Get target panZoom for given animateTime
		var animatePoints = $j( animateElement ).attr('values').split( ';' );

		// Get the target interpreted value
		var targetValue = this.getInterpolatePointsValue( animatePoints, relativeAnimationTime, duration );

		//mw.log( "SmilAnimate::transformPanZoom: source points: " + $j( animateElement ).attr('values') + " target:" + targetValue.join(',') );

		// Let Top Width Height
		// translate values into % values
		// NOTE this is dependent on the media being "loaded" and having natural width and height
		this.smil.getLayout().getNaturalSize(smilImgElement, function( naturalSize ){
			var percentValues = _this.getPercentFromPanZoomValues( targetValue, naturalSize );
			// Now we have naturalSize layout info try and render it.
			_this.updateElementLayout( smilImgElement, percentValues );
		});
	},
	// transforms pan zoom target value into layout percentages
	getPercentFromPanZoomValues: function(targetValue, naturalSize){
		var namedValueOrder = [ 'left', 'top', 'width', 'height' ];
		var percentValues = {};
		for( var i =0 ;i < targetValue.length ; i++ ){
			if( targetValue[i].indexOf('%') == -1 ) {
				switch( namedValueOrder[i] ){
					case 'left':
					case 'width':
						percentValues[ namedValueOrder[i] ] =
							( ( parseFloat( targetValue[i] ) 	/ naturalSize.width ) * 100 ) + '%';
					break;
					case 'height':
					case 'top':
						percentValues[ namedValueOrder[i] ] =
							( ( parseFloat( targetValue[i] ) / naturalSize.height ) * 100 ) + '%';
					break;
				}
			} else {
				percentValues[ namedValueOrder[i] ] = parseFloat( targetValue[i] ) + '%';
			}
		}
		return percentValues;
	},

	// xxx need to refactor move to "smilLayout"
	updateElementLayout: function( smilElement, percentValues, $target, htmlElement ){
		var _this = this;
		//mw.log("updateElementLayout::" + ' ' + percentValues.left + ' ' + percentValues.top + ' ' + percentValues.width + ' ' + percentValues.height );

		// get a pointer to the html target:
		if( !$target ) {
			$target = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ));
		}
		if( !htmlElement){
			htmlElement = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ) ).get(0);
		}
		
		_this.checkForRefTransformWrap( $target );
		

		_this.smil.getLayout().getNaturalSize( htmlElement, function( natrualSize ){
			// XXX note we have locked aspect so we can use 'width' here:

			var sizeCss = _this.smil.getLayout().getDominateAspectTransform( natrualSize, null, percentValues.width );
			//mw.log( ' w: ' + sizeCss.width + ' h ' + sizeCss.height + ' of : ' + $target.get(0).nodeName );
			// Run the css transform
			$target.css( {
				'position' : 'absolute',
				'left' : percentValues.left,
				'top' : percentValues.top
			})
			.css( sizeCss );
			//mw.log(' target width: ' + $target.css('width') );
		});
	},
	updateElementRotation:  function( smilElement, $target ){
		var _this = this;
		// Check if we even need to do a rotate operation:
		var rotateDeg = $j( smilElement ).attr('rotate');
		if( ! rotateDeg ){
			return ;
		}
		if( !$target ) {
			$target = $j( '#' + this.smil.getSmilElementPlayerID( smilElement ));
		}
		_this.checkForRefTransformWrap( $target );
		
		$target.css( {
			'-webkit-transform': 'rotate(' + rotateDeg + 'deg) ',
			'-moz-transform': 'rotate(' + rotateDeg + 'deg)'
		});
	},
	checkForRefTransformWrap: function( $target ){
		// Wrap the target with its natural size ( if not already )
		if( $target.parent( '.refTransformWrap' ).length == 0 ){			
			$target
			.wrap(
				$j( '<div />' )
				.css( {
					'top' : '0px',
					'left' : '0px',
					'position' : 'absolute',
					'overflow' : 'hidden',
					'width'	: '100%',
					'height' : '100%'
				} )
				.addClass( 'refTransformWrap' )
			);
		}
	},
	/**
	* getInterpolatePointsValue
	*
	* @param animatePoints Set of points to be interpolated
	* @param relativeAnimationTime Time to be animated
	* @param duration
	*/
	getInterpolatePointsValue: function( animatePoints, relativeAnimationTime, duration ){
		// For now only support "linear" transforms
		// What two points are we animating between:
		var timeInx = ( relativeAnimationTime / duration ) * animatePoints.length ;
		// if timeInx is zero just return the first point:
		if( timeInx == 0 ){
			return animatePoints[0].split(',');
		}

		// Make sure we are in bounds:
		var startInx = ( Math.floor( timeInx ) -1 );
		startInx = ( startInx < 0 ) ? 0 : startInx;
		var startPointSet = animatePoints[ startInx ].split( ',' );
		var endPointSet = animatePoints[ Math.ceil( timeInx) -1 ].split( ',' );

		var interptPercent = ( relativeAnimationTime / duration ) / ( animatePoints.length -1 );

		// Interpolate between start and end points to get target "value"
		var targetValue = [];
		for( var i = 0 ; i < startPointSet.length ; i++ ){
			targetValue[ i ] = parseFloat( startPointSet[i] ) + ( parseFloat( endPointSet[i] ) - parseFloat( startPointSet[i] ) ) * interptPercent;
			// Retain percent measurement
			targetValue[ i ] += ( startPointSet[i].indexOf('%') != -1 ) ? '%' : '';
		}
		return targetValue;
	}

}