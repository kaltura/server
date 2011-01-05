/**
 * The smil body also see:
 * http://www.w3.org/TR/2008/REC-SMIL3-20081201/smil-structure.html#edef-body
 */
mw.SmilBody = function( smilObject ){
	return this.init( smilObject );
}

mw.SmilBody.prototype = {

	// Used to store elements for getElementsForTime method
	elementsInRange: [],

	// Used to store elements out of range for getElementsForTime method
	elementsOutOfRange: [],

	// Index of auto assigned ids
	idIndex : 0,

	// Cache of ids of previous list of active elements
	// This lets us cache a valid element list for a given amount of time
	cacheElementList: {},

	smilBlockTypeMap: {
		'body':'seq',
		'ref' : 'ref',
		'animation':'ref',
		'audio' : 'ref',
		'img' : 'ref',
		'textstream' : 'ref',
		'video' : 'ref',
		'smiltext' : 'ref',
		'mwtemplate' : 'ref'
	},

	// Constructor:
	init: function( smilObject ){
		this.smil = smilObject;

		// Assign ids to smil body elements
		this.assignIds( this.getDom() );
	},

	getDom: function(){
		return this.smil.getDom().find('body');
	},

	/**
	* Assigns body smil elements id (for content that has a html representation "ref" & "smilText" )
	*	( enables fast sync between smilDom and htmlDom )
	*/
	assignIds: function( $node ) {
		var _this = this;
		// Don't give ids to nodes that have id's or are of type param
		if( !$node.attr('id')
			&& !$node.attr( 'xml:id' )
			&& _this.getNodeSmilType( $node ).toLowerCase() != 'param'
		){
			var idString = _this.getNodeSmilType( $node ) + '_' + _this.idIndex;
			// Make sure the id does not already exist ( should be a rare case )
			while( this.getDom().find( '#' + idString ).length != 0 ){
				_this.idIndex++;
				idString = _this.getNodeSmilType( $node ) + '_' + _this.idIndex;
			}
			// Apply the unique id attribute
			$node.attr('id', idString);
			mw.log('SmilBody:: gave: ' + $node.get(0).nodeName + ' id: ' + $node.attr('id') );
			// Increment the index:
			_this.idIndex++;
		}

		// Recurse to all the nodes children
		if( $node.children().length ) {
			$node.children().each( function( inx, childNode ){
				_this.assignIds( $j( childNode ) );
			});
		}
	},

	/**
	* Render the body elements for a given time, use layout engine to draw elements
	* if a callback is supplied
	* @param time
	* 	number time in seconds to seek to
	* @param deltaTime
	* 	optional duration of time to render ( 250ms or so ) 
	* @param {function=} callback
	* 	optional function to call once time has been rendered
	*/	
	renderTime: function( time, deltaTime, callback ){
		var _this = this;
		//mw.log( "SmilBody::renderTime:: " + time + ' delta: '+ deltaTime);
		var elementForTimeStack = 0;
		// Get all the draw elements from the body this time:
		this.getElementsForTime( time ,
			/* SMIL Element in Range */
			function( smilElement) {
				//mw.log("SmilBody::renderTime: Element in Range:" + $j( smilElement ).attr('id'));
				// var relativeTime = time - smilElement.parentTimeOffset;
				var relativeTime = time - $j( smilElement ).data ( 'startOffset' );

				// Render the active elements using the layout engine
				_this.smil.getLayout().drawElement( smilElement );

				// Transform the elements via animate engine
				elementForTimeStack++
				_this.smil.getAnimate().animateTransform( smilElement, relativeTime, deltaTime, function(){					
					elementForTimeStack--;
					if( elementForTimeStack == 0 && callback){
						mw.log('SmilBody::renderTime: all active elements animateTransform callback');
						callback()
					}
				});
			},
			/* SMIL Element out of range */
			function( smilElement ){
				//mw.log("SmilBody::renderTime: Element out of Range:" + $j( smilElement ).attr('id'));
				// Stop the animation or playback
				_this.smil.getAnimate().pauseAnimation( smilElement )

				// Hide the element in the layout
				_this.smil.getLayout().hideElement( smilElement );

				// Update activePlayback flag
				$j( smilElement ).data('activePlayback', false)

				// Expire transitions if needed
				_this.smil.getTransitions().hideTransitionElements( smilElement, time );
			}
		);
	},

	/**
	 * Send a pause request to the animation engine for all active body elements
	 */
	pause: function( currentTime ){
		var _this = this;
		this.getElementsForTime( currentTime , function( smilElement ){
			mw.log("SmilBody::pause: Element in Range" + $j( smilElement ).attr('id'));
			_this.smil.getAnimate().pauseAnimation( smilElement )
		});
	},

	/**
	 * Firefogg flattener can presently only sequence flat sequence of audio
	 * Also See: http://www.firefogg.org/dev/render.html
	 *
	 * Note if we could "blend" or play two audio files at the same time
	 *  that would be more ideal.
	 *
	 * @return {Object} an array of audio with the following properties:
	 * 	start The start offset of the audio asset.
	 * 	duration Duration of the audio asset
	 * 	src The source url, if set to false, silence is inserted for duration
	 * 	type Used internally to let audio overlay video.
	 */
	getFlatAudioTimeLine: function(){
		var _this = this;

		// Setup some flags:
		var maxAudioTime =0;

		var elementsWithAudio = [];
		var audioTimeline = [];

		// xxx could probably do this a bit cleaner
		var getEarliest = function ( audioTimeline ){
			var smallTime = null;
			var smallIndex = null
			for( var i =0; i < audioTimeline.length; i++ ){
				if( smallTime === null ){
					smallTime = audioTimeline[i]['startTime'];
					smallIndex = i;
				}
				if( audioTimeline[i]['startTime'] < smallTime ){
					smallTime = audioTimeline[i]['startTime'];
					smallIndex = i;
				}
			}
			return smallIndex = i;
		}
		// Build an audio timeline starting from the top level node:
		this.getRefElementsRecurse( this.getDom(), 0, function( $node ){
			var nodeType = _this.smil.getRefType( $node ) ;
			// Check if the node is audio ( first in wins / "audio" wins over video)
			if( nodeType == 'audio' || nodeType == 'video' ) {
				var audioObj = {
					'type' : nodeType,
					'src' : _this.smil.getAssetUrl ( $node.attr('src') ),
					'duration' : _this.getClipDuration( $node ),
					'startTime' : $node.data( 'startOffset' ),
					'offset' : _this.smil.parseTime( $node.attr( 'clipBegin') )
				};

				// If audioTimeline is empty insert directly
				if( audioTimeline.length == 0 ){
					audioTimeline.push( audioObj )
					return ;
				}

				// fill time
				var addedAudioFlag = false;
				for( var i = 0; i < audioTimeline.length; i++ ){
					var currentAudioObj = audioTimeline[i];
					var audioEndTime = audioObj['startTime'] + audioObj['duration'];
					var currentAudioEndTime = currentAudioObj['startTime'] + currentAudioObj['duration'];
					if( audioObj['startTime'] < currentAudioObj['startTime'] ){
						addedAudioFlag = true;
						var beforeAudioObj = $j.extend( audioObj, {
							'duration': ( currentAudioObj['startTime'] - audioObj['startTime'] )
						});
						// Add before
						audioTimeline.splice( i, 0, beforeAudioObj );

						// Update the audioObj if it extends past the currentAudioObject
						if( audioEndTime > currentAudioEndTime){
							audioObj['startTime'] = currentAudioEndTime;
							audioObj['duration'] = audioEndTime - currentAudioEndTime;
						} else {
							// done adding audioObj
							break;
						}
					}
				}
				// Add audioObject to end ( currentAudioObj has latest startTime
				if( ! addedAudioFlag && audioEndTime > currentAudioEndTime ){
					var audioObjDuration = ( audioEndTime - currentAudioEndTime );
					if( currentAudioEndTime + audioObjDuration > _this.getDuration() ){
						audioObjDuration = _this.getDuration() - currentAudioEndTime;
					}
					audioTimeline.push( $j.extend( audioObj, {
							'duration': audioObjDuration,
							'startTime' : currentAudioEndTime
						})
					);
				}

				// Keep audioTimeline sorted via startTime
				audioTimeline.sort( function( a, b){
					return a.startTime - b.startTime;
				});
			}
		});
		return audioTimeline;
	},

	/**
	 * Gets all the elements for a given time.
	 *
	 * Note this gets called all the time we may need to build a more efficient structure to access this info
	 */
	getElementsForTime: function ( time , inRangeCallback, outOfRangeCallback ) {
		var _this = this;
		if( !time ) {
			time =0;
		}
		// Recurse on every ref element and run relevant callbacks
		this.getRefElementsRecurse( this.getDom(), 0, function( $node ){

			var startOffset = $node.data( 'startOffset' );
			var nodeDuration = _this.getClipDuration( $node );


			/*mw.log("Checking if ref: " + $node.attr('id') + ' is in range:' + time + ' >= ' +
					$node.data( 'startOffset' ) + ' && '+ time +' < ' +startOffset + ' + ' + nodeDuration + "\n" +
					' inrage cb: ' + typeof inRangeCallback + ' eval::' +
					( time >= startOffset && time < ( startOffset + nodeDuration) ) + "\n\n" );*/

			// Check if element is in range:
			if( time >= startOffset && time < ( startOffset + nodeDuration) ){
				if( typeof inRangeCallback == 'function' ){
					inRangeCallback( $node );
				}
			} else {
				if( typeof outOfRangeCallback == 'function'){
					outOfRangeCallback( $node );
				}
			}
		});
	},

	/**
	 * get the sequence elements from a given par node if no node is provided assume root
	 * @param {element=} $parNode Optional parNode to list all sequence timelines
	 */
	getSeqElements: function( $node ){
		if( ! $node ){
			$node = this.getDom();
		}
		return $node.find('seq');
	},


	/**
	 * Recurse over all body elements, issues a callback on all ref and smilText nodes
	 *  @param {Object} $node Node Starting point
	 *  @param {Number} startOffset Stores startOffset for given node
	 *  @param {Function} callback Function called for every ref node
	 */
	getRefElementsRecurse: function( $node, startOffset, callback ){
		var _this = this;
		if( ! $node ){
			$node = this.getDom();
		}
		// Make sure $node is wrapped in jQuery object
		$node = $j( $node );

		// Setup local pointers:
		var nodeType = this.getNodeSmilType( $node );

		// If 'par' or 'seq' recurse on children
		if( nodeType == 'par' || nodeType == 'seq' ) {
			if( $node.children().length ) {
				$node.children().each( function( inx, childNode ){
					// mw.log(" recurse:: startOffset:" + nodeType + ' start offset:' + startOffset );
					var childDur = _this.getRefElementsRecurse(
						$j( childNode ),
						startOffset,
						callback
					);
					// If element parent is a 'seq' increment startOffset as we recurse for each child
					if( nodeType == 'seq' ) {
						//mw.log(" Parent Seq:: add child dur: " + childDur );
						startOffset += childDur;
					}
				});
			}
		}

		// If the nodeType is "ref" or smilText run the callback
		if( nodeType == 'ref' || nodeType == 'smilText' ) {

			// Update the startOffsets if the node has a "begin" attribute
			if( $node.attr('begin') ){
				startOffset+= this.smil.parseTime( $node.attr('begin') );
			}

			// Add the parent startOffset
			$node.data( 'startOffset', startOffset );

			callback( $node )
		}
		// Return the node Duration for tracking startOffset
		return this.getClipDuration( $node );
	},

	/**
	 * Returns the smil body duration
	 * ( wraps getDurationRecurse to get top level node duration )
	 */
	getDuration: function( forceRefresh ){
		this.duration = this.getClipDuration( this.getDom(), forceRefresh );
		return this.duration;
	},

	/**
	 * Gets the duration recursing from a supplied $node
	 * @param {jQueryObject} $node
	 * @param {boolean} forceRefresh If a fresh duration should be calculated
	 */
	getClipDuration: function( $node, forceRefresh ){
		/**
		 * mw.log( 'SmilBody::getClipDuration: node children::' +
		 *		$node.children().length + 'calle: ' + arguments.callee.toString() );
		*/

		if( !forceRefresh &&
			$node.data('computedDuration') != null
		) {
			return $node.data('computedDuration');
		}

		if( forceRefresh ){
			//clear out implictDuration
			$node.data( 'implictDuration', 0);
			$node.data( 'computedDuration', false );
		}

		var _this = this;
		var duration = 0;

		// Set the block type to
		var blockType = this.getNodeSmilType( $node );

		// Recurse on children
		if( $node.children().length ){
			$node.children().each( function( inx, childNode ){
				// If in a sequence add to duration
				var childDuration = _this.getClipDuration( $j( childNode ), forceRefresh );
				if( blockType == 'seq' ){
					$node.data( 'implictDuration', $node.data('implictDuration') + childDuration );
				}
				// With par blocks ImplictDuration is longest duration child
				if( blockType == 'par' ) {
					if( childDuration > $node.data( 'implictDuration' ) ){
						$node.data( 'implictDuration', childDuration);
					}
				}
			});
		}

		// Check the explicit duration attribute:
		if( $node.attr('dur') ) {
			var computedDuration = this.smil.parseTime( $node.attr('dur') ) ;
			// Check for "begin" that extends the duration by begin time
			if( $node.attr( 'begin') ){
				computedDuration += this.smil.parseTime( $node.attr('begin') );
			}
			//mw.log(" return dur: " + mw.smil.parseTime( $node.attr('dur') ) );
			$node.data('computedDuration', computedDuration );
		} else {
			// Else return use implictDuration ( built from its children )
			if( $node.data( 'implictDuration' ) ){
				//mw.log(" implictDuration:: " + $node.data( 'implictDuration' ) );
				$node.data('computedDuration', $node.data( 'implictDuration' ) );
			} else {
				$node.data('computedDuration', 0 );
			}
		}
		return $node.data('computedDuration');
	},

	/**
	 * Get the asset duration for a clip
	 * @param {element} $node the smil clip that we want to get duration for
	 * @param {function} callback Function to be called once asset duration is known.
	 */
	getClipAssetDuration: function( $node, callback ){
		this.smil.getBuffer().loadElement( $node );
		// xxx check if the type is "video or audio" else nothing to return

		var vid = $j( '#' + this.smil.getSmilElementPlayerID( $node ) ).find('audio,video').get(0);
		if( vid.duration ){
			callback( vid.duration );
		}
		// Duration ready callback:
		var durationReady = function(){
			callback( vid.duration );
		}
		// else setup a load biding
		vid.removeEventListener( "loadedmetadata", durationReady, true );
		vid.addEventListener( "loadedmetadata", durationReady, true );
	},

	/**
	 * Sync the in page assets with the smil dom
	 */
	syncPageDom: function(){
		var _this = this;
		//  Check that all top level layout items exist in the smil dom
		$j.each( $j( this.smil.getEmbedPlayer() ).find('.smilRootLayout'), function(inx, pageNode){
			// Check if the node is in the smil dom
			if( _this.smil.$dom.find( '#' + _this.smil.getSmilDomId( pageNode ) ).length == 0 ){
				// check for parent layout helper:
				if( $j( pageNode ).parent('.refTransformWrap').length ){
					$j( pageNode ).parent('.refTransformWrap').remove();
				} else {
					// Remove the pageNode
					$j( pageNode ).remove();
				}
			}
		});
	},

	/**
	 * Maps a few smil tags to smil types
	 *
	 * http://www.w3.org/TR/2008/REC-SMIL3-20081201/smil-structure.html#edef-body
	 * 'body' -> 'seq'
	 *
	 * http://www.w3.org/TR/2008/REC-SMIL3-20081201/smil-extended-media-object.html#edef-ref
	 * animation, audio, img, text, textstream and video -> 'ref',
	 */
	getNodeSmilType: function( $node ){
		var blockType = $j( $node ).get(0).nodeName;
		//mw.log( 'getNodeSmilType for: ' + blockType );
		if( this.smilBlockTypeMap[ blockType ] ){
			blockType = this.smilBlockTypeMap[ blockType ];
		}
		return blockType;
	}
}