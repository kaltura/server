/**
 * The Smil object
 *
 * @copyright kaltura
 * @author: Michael Dale mdale@wikimedia.org
 * @license GPL2
 *
 * Sequence player wraps smil into the video tag
 *
 * Provides an html5 video tag like api to a smil document.
 *
 * Supports frame by frame rendering of "smil" Supports "drop frame" realtime
 * playback of "smil"
 *
 * Extends the "embedPlayer" and represents the playlist as a single video
 * stream
 *
 */

mw.includeAllModuleMessages();

/* Add the hooks needed for playback */
mw.Smil = function(options) {
	return this.init(options);
};
mw.Smil.prototype = {

	// Store the mw.SmilLayout object
	layout : null,

	// Stores the mw.SmilBody object
	body : null,

	// Stores the mw.SmilBuffer object
	buffer : null,

	// Stores the mw.SmilAnimate object
	animate : null,

	// Stores the mw.SmilTransisions object
	transitions : null,

	// Stores the smil document for this object ( for relative image paths )
	smilContextUrl : null,

	// The abstract embed player parent
	embedPlayer : null,

	// The jQuery dom object of the smil xml
	$dom : null,

	// Cache for the duration of the smil sequence
	duration: null,
	/**
	 * Constructor
	 *
	 * @param {Object}
	 *            embedPlayer Reference to the embedPlayer driving the smil
	 *            object
	 */
	init : function(embedPlayer) {
		mw.log(" Smil:: init with player: " + embedPlayer.id);
		this.embedPlayer = embedPlayer;
	},

	/**
	 * Load smil into this object from a url
	 *
	 * @parm {string} url Source url of smil XML
	 * @param {function}
	 *            callback Function to call once smil is loaded and ready
	 */
	loadFromUrl : function(url, callback) {
		var _this = this;
		// Check for data url
		var dataUrlKey = 'data:text/xml;charset=utf-8,';
		if( url.indexOf( dataUrlKey ) === 0 ){
			// Load the smil document from the data url:
			_this.loadFromString(
				decodeURIComponent( url.substr( dataUrlKey.length ) )
			);
			// xxx Note we could in theory have a data url with remote 'context'
			// ie cross domain smil loading ( for now assume document.URL context for data urls )
			this.smilContextUrl = document.URL;
			callback();
			return ;
		}
		// Else context url is the remote
		this.smilContextUrl = url;
		mw.log('Smil::loadFromUrl : ' + url);

		// Try for direct load ( api cross domain loading is handled outside of
		// SmilInterface
		$j.get(url, function( xmlData) {
			_this.loadFromXMLData( xmlData );
			// XXX check success or failure
			callback();
		});
	},
	// Set the $dom from xmlData
	loadFromXMLData: function( xmlData ){
		this.$dom = $j( xmlData );
	},
	/**
	 * Set smil from xml string
	 *
	 * @param {string}
	 *            SmilXmlString Xml string of smil to be loaded
	 */
	loadFromString: function( smilXmlString ) {
		// Load the parsed string into the local "dom"
		this.$dom = $j( this.getXMLDomObject( smilXmlString ) );
		mw.log("Smil::loadFromString: loaded smil dom: " + this.$dom.children().length + "\n" + smilXmlString );
	},
	/**
	 * Update the smil dom via an xmlString
	 */
	updateFromString: function( smilXmlString ){
		delete this.$dom;
		// jQuery strips some html native tags when parsing xml passed into jQuery
		// since smil has html tags ( "body" "head" ) we need to first convert it to
		// an xml object::
		this.$dom = $j( this.getXMLDomObject( smilXmlString ) );

		// Remove any non-smil nodes that are in the page dom
		this.getBody().syncPageDom();
	},

	// simple XML DOMParser object parser wrapper
	// xxx Add error handling
	getXMLDomObject: function( smilXmlString ){
		if (window.DOMParser){
			parser=new DOMParser();
			xmlDoc=parser.parseFromString(smilXmlString, "text/xml");
		} else // Internet Explorer
		{
			xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
			xmlDoc.async="false";
			xmlDoc.loadXML( smilXmlString );
		}
		return xmlDoc;
	},

	/**
	 * Internal function to get the jQuery smil dom
	 */
	getDom : function() {
		if ( this.$dom ) {
			return this.$dom;
		}
		mw.log("Error SMIL Dom not available");
		return;
	},

	getXMLString: function(){
		return (new XMLSerializer()).serializeToString(this.$dom.get(0));
	},

	/**
	 * Render a specific time
	 */
	renderTime : function(time, callback) {
		// Setup the layout if not already setup:
		this.getLayout().setupLayout( this.embedPlayer.getRenderTarget() );

		// Update the render target with bodyElements for the requested time
		this.getBody().renderTime( time , null, callback);
	},

	/**
	 * We use animateTime instead of a tight framerate loop so that we can
	 * optimize with browser css transformations
	 */
	animateTime : function(time, timeDelta) {
		// mw.log("Smil::animateTime: " + time + ' delta: ' + timeDelta );
		this.getBody().renderTime( time, timeDelta );
	},

	/**
	 * Checks if two times are within the framerate time range
	 * useful for results of a seek request not exactly matching
	 * the seek time, but within a single frame.
	 */
	isSameFrameTime: function( time1, time2 ){
		var frameRange = 1 / mw.getConfig( 'SmilPlayer.framerate');
		if ( Math.abs( time1 - time2 ) < frameRange ) {
			//mw.log( Math.abs( time1 - time2 ) + ' IS < ' + frameRange );
			return true;
		} else {
			return false;
		}
	},

	/**
	 * Pause all animations and playback
	 */
	pause : function(currentTime) {
		this.getBody().pause(currentTime);
	},

	/**
	 * Checks if the playback is in sync with the current time
	 *
	 * @return {boolean} true if playback is insync, false if not in sync with
	 *         all animation elements ( video tag for now )
	 */
	getPlaybackSyncDelta : function(currentTime) {
		return this.getAnimate().getPlaybackSyncDelta(currentTime);
	},

	getBufferedPercent : function() {
		// Get the clip buffered percent
		return this.getBuffer().getBufferedPercent();
	},

	/**
	 * Get the set of audio ranges for flattening.
	 */
	getAudioTimeSet : function() {
		return this.getBody().getFlatAudioTimeLine();
	},

	/**
	 * Pass on the request to start buffering the entire sequence of clips
	 */
	startBuffer : function() {
		this.getBuffer().startBuffer();
	},

	/**
	 * Get the smil buffer object
	 */
	getBuffer : function() {
		if (!this.buffer) {
			this.buffer = new mw.SmilBuffer(this);
		}
		return this.buffer;
	},

	/**
	 * Get the animate object
	 */
	getAnimate : function() {
		if (!this.animate) {
			this.animate = new mw.SmilAnimate(this);
		}
		return this.animate;
	},

	/**
	 * Get the smil layout object, with reference to the body
	 */
	getLayout : function() {
		if (!this.layout) {
			this.layout = new mw.SmilLayout(this);
		}
		return this.layout;
	},

	/**
	 * Get the smil body object
	 */
	getBody : function() {
		if (!this.body) {
			this.body = new mw.SmilBody( this );
		}
		return this.body;
	},
	/**
	 * Get the transitions object
	 */
	getTransitions : function() {
		if (!this.transitions) {
			this.transitions = new mw.SmilTransitions(this);
		}
		return this.transitions;
	},

	/**
	 * Function called continuously to keep sync smil "in sync" Checks buffer
	 * states...
	 */
	syncWithTime : function(time) {
		/* .. not yet supported... */
		mw.log('smil sync: ' + time);
	},

	/**
	 * Get the duration form the smil body
	 */
	getDuration : function( forceRefresh ) {
		//
		// return 0 while we don't have the $dom loaded
		if (!this.$dom) {
			return 0;
		}
		if ( this.duration == null || forceRefresh === true ) {
			var orgDuration = this.duration;
			this.duration = this.getBody().getDuration( forceRefresh );
			// Trigger the duration change event:
			if( orgDuration != this.duration ){
				mw.log("Smil::getDuration: forceRefresh:" + this.duration + ' != ' + ' old duraiton: ' + orgDuration );	
				$j( this.getEmbedPlayer() ).trigger('durationchange');
			}
		}
		return this.duration;
	},

	removeById: function ( smilElementId ) {
		var $smilElement = this.$dom.find( '#' + smilElementId );

		// Remove from layout
		this.getLayout().getRootLayout().find( '#' + this.getSmilElementPlayerID( $smilElement ) )
			.remove();

		// Remove from dom
		$smilElement.remove();

		// Invalidate dom duration cache
		this.duration = null;
	},
	/**
	 * Some Smil Utility functions
	 */

	/**
	 * maps a smil element id to a html 'safer' id as a decedent subname of the
	 * embedPlayer parent
	 *
	 * @param {Object}
	 *            smilElement Element to get id for
	 */
	getSmilElementPlayerID : function( smilElement ) {
		if (! $j( smilElement ).attr('id') ) {
			mw.log("Error: getAssetId smilElement missing id ");
			return false;
		}
		var embedPlayer = this.getEmbedPlayer();
		if ( !embedPlayer || !embedPlayer.id ) {
			mw.log("Error: getAssetId missing parent embedPlayer");
			return false;
		}
		return embedPlayer.id + '_' + $j( smilElement ).attr('id');
	},

	/**
	 * Get the smil id for an pageNode
	 */
	getSmilDomId: function ( pageNode ){
		if( !$j( pageNode ).length ) {
			mw.log("Error: getSmilDomId for pageNode that is not in dom");
			return false;
		}
		return $j( pageNode ).attr('id').replace( '/' + this.getEmbedPlayer().id + '_/', '');
	},

	/**
	 * get the embed player
	 */
	getEmbedPlayer: function(){
		return this.embedPlayer;
	},

	/**
	 * Get an absolute path to asset based on the smil URL
	 *
	 * @param {string}
	 *            assetPath Path to asset to be transformed into url
	 */
	getAssetUrl : function( assetPath ) {
		// Context url is the smil document url:
		var contextUrl = mw.absoluteUrl(this.smilContextUrl);
		var absoluteUrl = mw.absoluteUrl( assetPath, contextUrl );
		// Restrict any display url
		if( mw.getConfig( 'SmilPlayer.AssetDomainWhiteList' ) != '*' ){
			var approvedDomainList = mw.getConfig( 'SmilPlayer.AssetDomainWhiteList' );
			var approved = false;
			for( var i =0; i < approvedDomainList.length; i++){
				if( mw.parseUri( absoluteUrl ).host == approvedDomainList[i] ){
					approved = true;
				}
			}
			if( ! approved ){
				mw.log("Error: getAssetUrl: Asset url " + absoluteUrl + ' is not smil player asset domains:' + approvedDomainList);
				return mw.getConfig('imagesPath') + 'vid_default_thumb.jpg';
			}
		}

		return absoluteUrl;
	},

	// filter 'raw' user htmlData
	getFilterdHtml: function( htmlData ){
		var _this = this;
		// We pass the htmlData via jQuery fragment creation, this runs
		// jquery.clean() and filters the result html of script tags and the like
		var $html = $j( '<div />' ).append(
			$j( htmlData )
		);
		// Links go to a new window and are disable when smaller than player size
		$html.find('a').each( function(inx, link ){
			// Escape link output as to not include scirpt execution
			$j(link).attr('href',
				mw.escapeQuotesHTML( $j(link).attr('href') )
			);
		});

		// Make every asset url absolute and restrict domain of assets
		// ( if player is configured to restrict asset domains )
		$html.find('img,video,audio,track,iframe,object,embed,form').each(function(inx, node){
			if( $j(node).attr('src') ){
				$j(node).attr('src',
					_this.getAssetUrl( $j(node).attr('src') )
				);
			}
			if( $j(node).attr('data') ){
				$j(node).attr('data',
					_this.getAssetUrl( $j(node).attr('src') )
				);
			}
			// remove form action
			if( $j(node).attr('action') ){
				if( $j(node).attr('action').toLowerCase().indexOf('javascript') != -1 ){
					 $j(node).attr('action', '');
				} else {
					$j(node).attr('action',
						_this.getAssetUrl( $j(node).attr('src') )
					);
				}
			}
		})

		// Script and html bindings should have been striped with $j.clean
		// but just in case remove any suspect elements with 'script' attributes
		$html.find('script,' +
				// body and frameset event attributes
				'[onload],[onunload],' +

				// Form element events:
				'[onblur],[onchange],[onfocus],[onreset],[onselect],[onsubmit],' +

				// Image events:
				'[onabort],' +

				// Keyboard events
				'[onkeydown],[onkeypress],[onkeyup],',

				// Mouse events
				'[onclick],[onclick],[ondblclick],[onmousedown],' +
				'[onmousemove],[onmouseout],[onmouseover],[onmouseup]'
		).remove();

		return $html;
	},
	getTitleKey: function( smilElement ){
		// check directly for the attribute:
		if( $j(smilElement).attr('apititlekey') ){
			return $j(smilElement).attr('apititlekey') ;
		}
		if( $j(smilElement).find("param[name='apiTitleKey']").length ) {
			return $j(smilElement).find("param[name='apiTitleKey']").attr('value');
		}
		return false;
	},
	/**
	 * Get the smil resource type based on nodeName and type attribute
	 */
	getRefType : function( smilElement ) {
		if ($j(smilElement).length == 0) {
			mw.log('Error: Smil::getRefType on empty smilElement');
			return;
		}
		// Get the smil type
		var smilType = $j(smilElement).get(0).nodeName.toLowerCase();

		if (this.getBody().smilBlockTypeMap[smilType] != 'ref') {
			mw.log("Error: trying to get ref type of node that is not a ref"
					+ smilType);
			return null;
		}

		// If the smilType is ref, check for a content type
		if (smilType == 'ref') {
			switch ($j(smilElement).attr('type')) {
			case 'application/x-wikitemplate':
				smilType= 'mwtemplate';
				break;
			case 'text/html':
				smilType = 'cdata_html';
				break;
			case 'video/ogg':
			case 'video/h.264':
			case 'video/webm':
				smilType = 'video';
				break;
			case 'audio/ogg':
				smilType = 'audio';
				break;
			}
		}
		return smilType;
	},

	/**
	 * Parse smil time function
	 * http://www.w3.org/TR/SMIL3/smil-timing.html#Timing-ClockValueSyntax
	 *
	 * Smil time has the following structure:
	 *
	 * Clock-value ::= ( Full-clock-value | Partial-clock-value |
	 * Timecount-value ) Full-clock-value ::= Hours ":" Minutes ":" Seconds ("."
	 * Fraction)? Partial-clock-value ::= Minutes ":" Seconds ("." Fraction)?
	 * Timecount-value ::= Timecount ("." Fraction)? (Metric)? Metric ::= "h" |
	 * "min" | "s" | "ms" Hours ::= DIGIT+ // any positive number Minutes ::=
	 * 2DIGIT // range from 00 to 59 Seconds ::= 2DIGIT // range from 00 to 59
	 * Fraction ::= DIGIT+ Timecount ::= DIGIT+ 2DIGIT ::= DIGIT DIGIT DIGIT ::=
	 * [0-9]
	 *
	 * @param {mixed}
	 *            timeValue time value of smil structure
	 * @return {float} Seconds from time value, if timeValue is empty or null
	 *         return 0
	 */
	parseTime : function(timeValue) {
		if (!timeValue)
			return 0;

		// If timeValue is already a clean number of seconds, return seconds:
	if (!isNaN(timeValue)) {
		return parseFloat(timeValue);
	}
	// Trim whitespace if empty return zero
	timeValue = $j.trim(timeValue);
	if (timeValue == '') {
		return 0;
	}

	// First check for hh:mm:ss time:
	if (timeValue.split(':').length == 3 || timeValue.split(':').length == 2) {
		return mw.npt2seconds(timeValue);
	}

	var timeFactor = null
	// Check for metric hours
	if (timeValue.substr(-1) == 'h') {
		timeFactor = 3600;
	}
	// Min metric
	if (timeValue.substr(-3) == 'min') {
		timeFactor = 60;
	}
	// Seconds
	if (timeValue.substr(-1) == 's') {
		timeFactor = 1;
	}
	// Millaseconds
	if (timeValue.substr(-2) == 'ms') {
		timeFactor = .001;
	}

	if (timeFactor) {
		return parseFloat(parseFloat(timeValue) * timeFactor);
	}
	mw.log("Error could not parse time: " + timeValue);
	return 0;
}
}
