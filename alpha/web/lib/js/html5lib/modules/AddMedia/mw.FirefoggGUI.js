/**
 * Advanced Firefogg support. Lets you control many aspects of video encoding.
 */

mw.addMessages({
	"fogg-wont-upload-to-server" : "Note: Your video file will be locally encoded and not upload to any server",
	"fogg-save_local_file" : "Encode to File",
	"fogg-help-sticky" : "Help (click to stick)",
	"fogg-cg-preset" : "Preset: <strong>$1<\/strong>",
	"fogg-cg-quality" : "Basic quality and resolution control",
	"fogg-cg-meta" : "Metadata for the clip",
	"fogg-cg-range" : "Encoding range",
	"fogg-cg-advVideo" : "Advanced video encoding controls",
	"fogg-cg-advAudio" : "Advanced audio encoding controls",
	"fogg-preset-custom" : "Custom settings",
	"fogg-webvideo-desc" : "Ogg Web video Theora, Vorbis 600 kbit\/s and 400px maximum width",
	"fogg-savebandwidth-desc" : "Ogg Low bandwidth Theora, Vorbis 164 kbit\/s and 200px maximum width",
	"fogg-highquality-desc" : "Ogg High quality Theora, Vorbis 1080px maximum width",
	"fogg-webvideo-webm-desc" : "Webm Web video VP8 600 kbit\/s and 480px maximum width",
	"fogg-highquality-webm-desc" : "Webm High quality VP8 1080px maximum width",
	"fogg-videoQuality-title" : "Video quality",
	"fogg-videoQuality-help" : "Used to set the <i>visual quality<\/i> of the encoded video (not used if you set bitrate in advanced controls below).",
	"fogg-starttime-title" : "Start second",
	"fogg-starttime-help" : "Only encode from time in seconds",
	"fogg-endtime-title" : "End second",
	"fogg-endtime-help" : "Only encode to time in seconds",
	"fogg-audioQuality-title" : "Audio quality",
	"fogg-audioQuality-help" : "Used to set the <i>acoustic quality<\/i> of the encoded audio (not used if you set bitrate in advanced controls below).",
	"fogg-videoCodec-title" : "Video codec",
	"fogg-videoCodec-help" : "Select the clip video codec. More about the <a target=\"_new\" href=\"http:\/\/en.wikipedia.org\/wiki\/Theora\">Theora codec<\/a>. More about the <a target=\"_new\" href=\"http:\/\/en.wikipedia.org\/wiki\/Webm\">VP8 codec<\/a>",
	"fogg-audioCodec-title" : "Audio codec",
	"fogg-audioCodec-help" : "Used to set the clip audio codec. Presently only Vorbis is supported. More about the <a target=\"_new\" href=\"http:\/\/en.wikipedia.org\/wiki\/Vorbis\">Vorbis codec<\/a>",
	"fogg-width-title" : "Video width",
	"fogg-width-help" : "Resize to given width.",
	"fogg-height-title" : "Video height",
	"fogg-height-help" : "Resize to given height.",
	"fogg-videoBitrate-title" : "Video bitrate",
	"fogg-videoBitrate-help" : "Video bitrate sets the encoding bitrate for video in (kb\/s)",
	"fogg-twopass-title" : "Two pass encoding",
	"fogg-twopass-help" : "Two pass encoding enables more constant quality by making two passes over the video file",
	"fogg-framerate-title" : "Frame rate",
	"fogg-framerate-help" : "The video frame rate. More about <a target=\"_new\" href=\"http:\/\/en.wikipedia.org\/wiki\/Frame_rate\">frame rate<\/a>.",
	"fogg-aspect-title" : "Aspect ratio",
	"fogg-aspect-help" : "The video aspect ratio can be 4:3 or 16:9. More about <a target=\"_new\" href=\"http:\/\/en.wikipedia.org\/wiki\/Aspect_ratio_%28image%29\">aspect ratios<\/a>.",
	"fogg-keyframeInterval-title" : "Key frame interval",
	"fogg-keyframeInterval-help" : "The keyframe interval in frames. Note: Most codecs force keyframes if the difference between frames is greater than keyframe encode size. More about <a href=\"http:\/\/en.wikipedia.org\/wiki\/I-frame\">keyframes<\/a>.",
	"fogg-denoise-title" : "Denoise filter",
	"fogg-denoise-help" : "Denoise input video. More about <a href=\"http:\/\/en.wikipedia.org\/wiki\/Video_denoising\">denoise<\/a>.",
	"fogg-novideo-title" : "No video",
	"fogg-novideo-help" : "disable video in the output",
	"fogg-audioBitrate-title" : "Audio bitrate",
	"fogg-samplerate-title" : "Audio sampling rate",
	"fogg-samplerate-help" : "set output sample rate (in Hz).",
	"fogg-noaudio-title" : "No audio",
	"fogg-noaudio-help" : "disable audio in the output",
	"fogg-title-title" : "Title",
	"fogg-title-help" : "A title for your clip",
	"fogg-artist-title" : "Creator name",
	"fogg-artist-help" : "The creator of this clip",
	"fogg-date-title" : "Date",
	"fogg-date-help" : "The date the footage was created or released",
	"fogg-location-title" : "Location",
	"fogg-location-help" : "The location of the footage",
	"fogg-organization-title" : "Organization",
	"fogg-organization-help" : "Name of organization (studio)",
	"fogg-copyright-title" : "Copyright",
	"fogg-copyright-help" : "The copyright of the clip",
	"fogg-license-title" : "License",
	"fogg-license-help" : "The license of the clip (preferably a Creative Commons URL).",
	"fogg-contact-title" : "Contact",
	"fogg-contact-help" : "Contact link",
	"fogg-gui-title" : "Make web video"
});

/**
* Setup firefoggGUI jquery binding
*/
( function( $ ) {
	$.fn.firefoggGUI = function( options ) {
		if ( !options )
			options = { };

		// Add the selector
		options['selector'] = this.selector;

		// Setup the firefogg interface:
		var myFogg = new mw.FirefoggGUI( options );

		if ( myFogg ) {
			myFogg.setupForm( );
			var selectorElement = $j( options.selector ).get( 0 );
			selectorElement['firefogg'] = myFogg;
		}
	}
} )( jQuery );

mw.FirefoggGUI = function( iObj ) {
	return this.init( iObj );
};
var default_mvAdvFirefogg_config = {
	// Config groups to include
	'config_groups': [ 'preset', 'range', 'quality', 'meta', 'advVideo', 'advAudio' ],

	// If you want to load any custom presets must follow the mvAdvFirefogg.presetConf json outline below
	'custom_presets': {},

	// Any Firefog config properties that may need to be excluded from options
	'exclude_settings': [],

	// The control container
	'target_control_container': false
};

mw.FirefoggGUI.prototype = {
	// The configuration group names
	config_groups: [ 'preset', 'range', 'quality', 'meta', 'advVideo', 'advAudio' ],

	// Default configuration for this class
	default_local_settings: {
		'default': 'webvideo',
		'type': 'select',
		'selectVal': ['webvideo'],
		'group': "preset",
		'presets': {
			'custom': {
				'descKey': 'fogg-preset-custom',
				'conf': {}
			},
			'webvideo': {
				'desc': gM( 'fogg-webvideo-desc' ),
				'conf': {
					'videoCodec' : 'theora',
					'maxSize'      : 400,
					'videoBitrate' : 544,
					'audioBitrate' : 96,
					'noUpscaling'  : true
				}
			},
			'savebandwidth': {
				'desc': gM( 'fogg-savebandwidth-desc' ),
				'conf': {
					'videoCodec' : 'theora',
					'maxSize'       : 200,
					'videoBitrate'  : 164,
					'audioBitrate'  : 32,
					'samplerate'    : 22050,
					'framerate'     : 15,
					'channels'      : 1,
					'noUpscaling'   : true
				}
			},
			'hqstream': {
				'desc': gM( 'fogg-highquality-desc' ),
				'conf': {
					'videoCodec' : 'theora',
					'maxSize'      : 1080,
					'videoQuality' : 6,
					'audioQuality' : 3,
					'noUpscaling'  : true
				}
			},
			'webvideo_webm': {
				'desc': gM( 'fogg-webvideo-webm-desc' ),
				'conf': {
					'videoCodec' : 'vp8',
					'maxSize'      : 480,
					'videoBitrate' : 544,
					'audioBitrate' : 96,
					'noUpscaling'  : true
				}
			},
			'webvideo_webmhq': {
				'desc' : gM('fogg-highquality-webm-desc'),
				'conf' : {
					'videoCodec' : 'vp8',
					'maxSize'      : 1080,
					'videoQuality' : 6,
					'audioQuality' : 3,
					'noUpscaling'  : true
				}
			}
		}
	},

	// Customized configuration hashtable
	local_settings: {},

	// Core Firefogg default encoder configuration
	// See encoder options here: http://www.firefogg.org/dev/index.html
	default_encoder_config: {
		// Base quality settings
		'videoQuality': {
			'default'   : 5,
			'range'     : { 'min': 0,'max': 10 },
			'type'      : 'slider',
			'group'     : 'quality'
		},
		'starttime': {
			'type'      : "float",
			'group'     : "range"
		},
		'endtime': {
			'type'      : "float",
			'group'     : "range"
		},
		'audioQuality': {
			'default'   : 1,
			'range'     : { 'min': -1, 'max': 10 },
			'type'      : 'slider',
			'group'     : 'quality'
		},
		'videoCodec': {
			'default'   : "theora",
			'selectVal' : [ 'theora', 'vp8' ],
			'type'      : "select",
			'group'     : "quality"
		},
		'audioCodec': {
			'default'   : "vorbis",
			'selectVal' : [ 'vorbis' ],
			'type'      : "select",
			'group'     : "quality"
		},
		'width': {
			'range'     : { 'min': 0, 'max': 4096 },
			'step'      : 4,
			'type'      : 'slider',
			'group'     : "quality"
		},
		'height': {
			'range'     : { 'min': 0, 'max' : 3072 },
			'step'      : 4,
			'type'      : "slider",
			'group'     : "quality"
		},

		// Advanced video control
		'videoBitrate': {
			'range'     : { 'min' : 1, 'max' : 16778 },
			'type'      : "slider",
			'group'     : "advVideo"
		} ,
		'twopass': {
			'type'      : "boolean",
			'group'     : "advVideo"
		},
		'framerate': {
			'default'   : '24',
			'selectVal' : [ '12', '16', { '24000:1001' : '23.97' }, '24', '25',
				{ '30000:1001' : '29.97' }, '30' ],
			'type'      : "select",
			'group'     : "advVideo"
		},
		'aspect': {
			'default'   : '4:3',
			'type'      : "select",
			'selectVal' : [ '4:3', '16:9' ],
			'group'     : "advVideo"
		},
		'keyframeInterval': {
			'default'   : '64',
			'range'     : { 'min': 0, 'max': 65536 },
			'numberType': 'force keyframe every $1 frames',
			'type'      : 'int',
			'group'     : 'advVideo'
		},
		'denoise': {
			'type'      : "boolean",
			'group'     : 'advVideo'
		},
		'novideo': {
			'type'      : "boolean",
			'group'     : 'advVideo'
		},

		// Advanced audio control
		'audioBitrate': {
			'range'     : { 'min': 32, 'max': 500 },
			'numberType': '$1 kbs',
			'type'      : 'slider'
		},
		'samplerate': {
			'type'      : 'select',
			'selectVal' : [ { '22050': '22 kHz' }, { '44100': '44 khz' }, { '48000': '48 khz' } ],
			'formatSelect' : function( val ) {
				return ( Math.round( val / 100 ) * 10 ) + ' Hz';
			}
		},
		'noaudio': {
			'type'      : 'boolean',
			'group'     : 'advAudio'
		},

		// Meta tags
		'title': {
			'type'      : 'string',
			'group'     : 'meta'
		},
		'artist': {
			'type'      : 'string',
			'group'     : 'meta'
		},
		'date': {
			'group'     : 'meta',
			'type'      : 'date'
		},
		'location': {
			'type'      : 'string',
			'group'     : 'meta'
		},
		'organization': {
			'type'      : 'string',
			'group'     : 'meta'
		},
		'copyright':  {
			'type'      : 'string',
			'group'     : 'meta'
		},
		'license': {
			'type'      : 'string'
		},
		'contact': {
			'type'      : 'string',
			'group'     : 'meta'
		}
	},

	/**
	 * Initialize this object
	 */
	init: function( options ) {

		// Set up a supported object:
		for ( var key in options ) {
			if ( typeof default_mvAdvFirefogg_config[key] != 'undefined' ) {
				this[key] = options[key];
			}
		}

		// Inherit the base mvFirefogg class:
		var baseFirefogg = new mw.Firefogg( options );
		for ( var key in baseFirefogg ) {
			if ( typeof this[key] != 'undefined' ) {
				this[ 'basefogg_' + key ] = baseFirefogg[ key ];
			} else {
				this[ key ] = baseFirefogg[ key ];
			}
		}
		return this;
	},

	/**
	* Setup the form
	*/
	setupForm: function() {
		//empty out the selector:
		$j( this.selector ).empty();
		// Check for firefogg:
		if ( ! this.getFirefogg() ) {
			// Show install firefogg msg
			this.showInstallFirefog();
			$j('.target_please_install').css( { 'width': 400, 'margin':'auto' } );
			return;
		}
		this.createControls();
		this.bindControls();
	},

	createControls: function() {
		mw.log( "adv createControls" );
		var _this = this;
		// Load presets from the cookie
		this.loadEncSettings();

		// Add the base control buttons
		this.basefogg_createControls();

		// Build the config group output
		var gdout = '';
		$j.each( this.config_groups, function( inx, group_key ) {
			gdout += '<div> ' +
				'<h3><a href="#" class="gd_' + group_key + '" >' +
				gM( 'fogg-cg-' + group_key ) + '</a></h3>' +
				'<div>';
			// Output this group's control options:
			gdout += '<table width="' + ( $j( _this.selector ).width() - 60 ) + '" >' +
				'<tr><td width="35%"></td><td width="65%"></td></tr>';
			// If this is the preset group, output the preset control
			if ( group_key == 'preset' ) {
				gdout += _this.getPresetControlHtml();
			}
			// Output the encoder config controls
			for ( var configKey in _this.default_encoder_config ) {
				var confEntry = _this.default_encoder_config[ configKey ];
				if( confEntry.group == group_key ) {
					gdout += _this.getConfigControlHtml( configKey );
				}
			}
			gdout += '</table></div></div>';
		});
		// Add the control container
		if( !this.target_control_container ) {
			this.target_control_container = this.selector + ' .control_container';
			$j( this.selector ).append( '<p><div class="control_container"></div>' );
		}
		// Hide the container and add the output
		$j( this.target_control_container ).hide();
		$j( this.target_control_container ).html( gdout );
	},

	// Custom advanced target rewrites
	getControlHtml: function( target ) {
		switch ( target ) {
			case 'target_btn_select_file':
			case 'target_btn_select_new_file':
			case 'target_btn_save_local_file':
				var icon;
				if ( target == 'target_btn_save_local_file' ) {
					icon = 'ui-icon-video';
				} else {
					icon = 'ui-icon-folder-open';
				}
				var linkText = gM( target.replace( /^target_btn_/, 'fogg-' ) );
				return '<a class="ui-state-default ui-corner-all ui-icon_link ' +
							target + '" href="#"><span class="ui-icon ' + icon + '"/>' +
							linkText +
						'</a>';
			case 'target_btn_select_url':
				return $j.btnHtml( gM( 'fogg-select_url' ), target, 'link' );
			case 'target_use_latest_firefox':
			case 'target_please_install':
			case 'target_passthrough_mode':
				var text = gM( target.replace( /^target_/, 'fogg-' ) );
				return '<div ' +
						'style="margin-top:1em;padding: 0pt 0.7em;" ' +
						'class="ui-state-error ui-corner-all ' +
						target + '">' +
					'<p>' +
					'<span style="float: left; margin-right: 0.3em;" ' +
						'class="ui-icon ui-icon-alert"/>' +
					text +
					'</p>' +
					'</div>';
			case 'target_input_file_name':
				var text = gM( 'fogg-input_file_name' );
				return '<br><br><input style="" ' +
					'class="text ui-widget-content ui-corner-all ' + target + '" ' +
					'type="text" value="' + text + '" size="60" /> ';
			default:
				mw.log( 'call : basefogg_getTargetHtml for:' + target );
				return this.basefogg_getControlHtml( target );
		}
	},

	getPresetControlHtml: function() {
		var out = '';
		var _this = this;
		mw.log( 'getPresetControlHtml::' );
		if ( typeof this.local_settings.presets != 'undefined' ) {
			out += '<select class="_preset_select">';
			$j.each( this.local_settings.presets, function( presetKey, preset ) {
				var presetDesc = preset.descKey ? gM( preset.descKey ) : preset.desc;
				var sel = ( _this.local_settings['default'] == presetKey ) ? ' selected' : '';
				out += '<option value="' + presetKey + '" ' + sel + '>' + presetDesc + '</option>';
			});
			out += '</select>';
		}
		return out;
	},

	getConfigControlHtml : function( configKey ) {
		var configEntry = this.default_encoder_config[configKey];
		var out = '';
		out += '<tr><td valign="top">' +
			'<label for="_' + configKey + '">' +
			gM( 'fogg-' + configKey + '-title' ) + ':' +
			'<span title="' + gM( 'fogg-help-sticky' ) + '" ' +
				'class="help_' + configKey + ' ui-icon ui-icon-info" style="float:left">' +
			'</span>' +
			'</label></td><td valign="top">';
		// Get the default value (or an empty string if there is no default)
		var defaultValue = this.default_encoder_config[configKey]['default'];
		if ( !defaultValue ) {
			defaultValue = '';
		}
		var type = configEntry.type; // shortcut

		// Switch on the config type
		switch( type ) {
			case 'string':
			case 'date':
			case 'int':
			case 'float':
				var size = ( type == 'string' || type == 'date' ) ? '14' : '4';
				out += '<input ' +
					'size="' + size + '" ' +
					'type="text" ' +
					'class="_' + configKey + ' text ui-widget-content ui-corner-all" ' +
					'value="' + defaultValue + '" >';
				break;
			case 'boolean':
				var checked_attr = ( defaultValue === true ) ? ' checked="true"' : '';
				out += '<input ' +
					'type="checkbox" ' +
					'class="_' + configKey + ' ui-widget-content ui-corner-all" ' +
					checked_attr + '>';
				break;
			case 'slider':
				var strMax = this.default_encoder_config[ configKey ].range.max + '';
				maxDigits = strMax.length + 1;
				out += '<input ' +
					'type="text" ' +
					'maxlength="' + maxDigits + '" ' +
					'size="' + maxDigits + '" ' +
					'class="_' + configKey + ' text ui-widget-content ui-corner-all" ' +
					'style="display:inline; color:#f6931f; padding: 2px; font-weight:bold;" ' +
					'value="' + defaultValue + '" >' +
					'<div class="slider_' + configKey + '"></div>';
				break;
			case 'select':
				out += '<select class="_' + configKey + '">' +
						'<option value=""> </option>';
				for ( var i in configEntry.selectVal ) {
					var val = configEntry.selectVal[i];
					if ( typeof val == 'string' ) {
						var sel = ( defaultValue == val ) ? ' selected' : '';
						out += '<option value="' + val + '"'+sel+'>' + val + '</option>';
					} else if ( typeof val == 'object' ) {
						for ( var key in val ) {
							hr_val = val[key];
						}
						var sel = ( configEntry.selectVal[i] == key ) ? ' selected' : '';

						out += '<option value="' + key + '"' + sel + '>' + hr_val + '</option>';
					}
				}
				out += '</select>';
				break;
		}
		// output the help row:
		out += '<div class="helpRow_' + configKey + '">' +
				'<span class="helpClose_' + configKey + ' ui-icon ui-icon-circle-close" ' +
				'title="Close Help"' +
				'style="float:left"/>' +
				gM( 'fogg-'+ configKey + '-help' ) +
				'</div>';
		out += '</td></tr><tr><td colspan="2" height="10"></td></tr>';
		return out;
	},

	bindControls: function() {
		var _this = this;
		_this.basefogg_bindControls();

		// Show the select by URL if present
		/*$j( this.target_btn_select_url ).unbind()
			.attr( 'disabled', false )
			.css( { 'display': 'inline' } )
			.click( function() {
				_this.selectSourceUrl();
			});
		*/

		// Hide the base advanced controls until a file is selected:
		$j( this.target_control_container ).hide();

		var helpState = {};
		// Do some display tweaks
		mw.log( 'tw:' + $j( this.selector ).width() +
			' ssf:' + $j( this.target_btn_select_new_file ).width() +
			' sf:' + $j( this.target_btn_save_local_file ).width() );

		// Set width to 250
		$j( this.target_input_file_name ).width( 250 );

		// Special preset action
		$j( this.selector + ' ._preset_select' ).change( function() {
			_this.updatePresetSelection( $j( this ).val() );
		});

		// Bind control actions
		for ( var configKey in this.default_encoder_config ) {
			var confEntry = this.default_encoder_config[configKey];

			// Initial state is hidden
			$j( this.selector + ' .helpRow_' + configKey ).hide();

			$j( this.selector + ' .help_' + configKey )
				.click(
					function() {
						// Get the config key (assume it's the last class)
						var configKey = _this.getClassId( this, 'help_' );

						if ( helpState[configKey] ) {
							$j( _this.selector + ' .helpRow_' + configKey ).hide( 'slow' );
							helpState[configKey] = false;
						} else {
							$j( _this.selector + ' .helpRow_' + configKey ).show( 'slow' );
							helpState[configKey] = true;
						}
						return false;
					}
				)
				.hover(
					function() {
						// get the config key ( assume it's the last class )
						var configKey = _this.getClassId( this, 'help_' );
						$j( _this.selector + ' .helpRow_' + configKey ).show( 'slow' );
					},
					function() {
						var configKey = _this.getClassId( this, 'help_' );
						if( !helpState[configKey] )
							$j( _this.selector + ' .helpRow_' + configKey ).hide( 'slow' );
					}
				);

			$j( this.selector + ' .helpClose_' + configKey )
				.click(
					function() {
						mw.log( "close help: " + configKey );
						// get the config key (assume it's the last class)
						var configKey = _this.getClassId( this, 'helpClose_' );
						$j( _this.selector + ' .helpRow_' + configKey ).hide( 'slow' );
						helpState[configKey] = false;
						return false;
					}
				)
				.css( 'cursor', 'pointer' );

			// Set up bindings for the change events (validate input)

			switch ( confEntry.type ) {
				case 'boolean':
					$j( this.selector + ' ._' + configKey)
						.click( function() {
							_this.updateLocalValue( _this.getClassId( this ),
							$j( this ).is( ":checked" ) );
							_this.updatePresetSelection( 'custom' );
						});
					break;
				case 'select':
				case 'string':
				case 'int':
				case 'float':
					// Check if we have a validate function on the string
					$j( this.selector + ' ._' + configKey ).change( function() {
						$j( this ).val( _this.updateLocalValue(
							_this.getClassId( this ),
							$j( this ).val() )
						);
						_this.updatePresetSelection( 'custom' );
					});
					break;
				case 'date':
					$j( this.selector + ' ._' + configKey ).datepicker({
							changeMonth: true,
							changeYear: true,
							dateFormat: 'd MM, yy',
							onSelect: function( dateText ) {
								_this.updateInterfaceValue( _this.getClassId( this ), dateText );
							}
					});
					break;
				case 'slider':
					/**
					* Return true or false of out of range and update the related value
					*/
					var keepAspectRatio = function( sliderId, value ){
						// Maintain source video aspect ratio
						if ( sliderId == 'width' ) {
							var sourceHeight = _this.sourceFileInfo.video[0]['height'];
							var sourceWidth = _this.sourceFileInfo.video[0]['width'];
							var newHeight = parseInt( sourceHeight / sourceWidth * value );
							// Reject the update if the new height is above the maximum
							if ( newHeight > _this.updateInterfaceValue( 'height', newHeight ) )
								return false;
						}
						if ( sliderId == 'height' ) {
							var sourceHeight = _this.sourceFileInfo.video[0]['height'];
							var sourceWidth = _this.sourceFileInfo.video[0]['width'];
							var newWidth = parseInt( sourceWidth / sourceHeight * value );
							// Reject the update if the new width is above the maximum
							if ( newWidth > _this.updateInterfaceValue( 'width', newWidth ) )
								return false;
						}
					};

					$j( this.selector + ' .slider_' + configKey ).slider({
						range: "min",
						animate: true,
						step: confEntry.step ? confEntry.step : 1,
						value: $j( this.selector + ' ._' + configKey ).val(),
						min: this.default_encoder_config[ configKey ].range.min,
						max: this.default_encoder_config[ configKey ].range.max,
						slide: function( event, ui ) {
							var sliderId = _this.getClassId( this, 'slider_' );
							$j( _this.selector + ' ._' + sliderId ).val( ui.value );

							keepAspectRatio( sliderId, ui.value );
						},
						change: function( event, ui ) {
							var sliderId = _this.getClassId( this, 'slider_' );
							_this.updateLocalValue( sliderId, ui.value );
							_this.updatePresetSelection( 'custom' );
						}
					});

					$j( this.selector + ' ._' + configKey ).change( function() {
						var classId = _this.getClassId( this );
						var validValue = _this.updateLocalValue( classId,
							$j( this ).val()
						);
						_this.updatePresetSelection( 'custom' );
						// Change it to the validated value
						$j( this ).val( validValue );
						// update the slider
						//mw.log( "update: " + _this.selector + ' .slider' + classId );
						$j( _this.selector + ' .slider_' + classId )
							.slider('value', validValue );
						// Keep aspect ratio:
						keepAspectRatio( classId, validValue );
					});
					break;
			}
		}

		$j( this.target_control_container ).accordion({
			header: "h3",
			collapsible: true,
			active: false,
			fillSpace: true
		});

		// Do the config value updates if there are any
		this.updateValuesInHtml();
	},

	/**
	 * Update the UI due to a change in preset
	 */
	updatePresetSelection: function( presetKey ) {
		var _this = this;
		// Update the local configuration
		this.local_settings['default'] = presetKey;
		mw.log( 'update preset desc: ' + presetKey );
		var presetDesc = '';
		if ( this.local_settings.presets[presetKey].desc ) {
			presetDesc = this.local_settings.presets[presetKey].desc;
		} else {
			presetDesc = gM( 'fogg-preset-' + presetKey );
		}
		if( presetKey != 'custom' ){
			// Copy the preset into custom settings
			this.local_settings.presets['custom']['conf'] = $j.extend( {}, this.local_settings.presets[presetKey]['conf'] );

		    // Set the actual HTML & widgets based on any local settings values:
		    $j.each( _this.local_settings.presets['custom']['conf'], function( inx, val ) {
			    if ( $j( _this.selector + ' ._' + inx ).length != 0 ) {
				    $j( _this.selector + ' ._' + inx ).val( val );
			    }
		    } );

		}
		// Update the preset title
		$j( this.selector + ' .gd_preset' )
			.html( gM( 'fogg-cg-preset', presetDesc ) );
		// update the selector
		$j( this.selector + ' ._preset_select' ).val( presetKey );
	},

	/**
	 * Update the interface due to a change in a particular config key
	 */
	updateInterfaceValue: function( confKey, val ) {
		var _this = this;
		if ( !val ) {
			return;
		}
		// Look up the type
		if ( typeof this.default_encoder_config[confKey] == 'undefined' ) {
			mw.log( 'error: missing default key: ' + confKey );
			return false;
		}

		// Update the local value (if it's not already up-to-date)
		if ( this.local_settings.presets['custom']['conf'][confKey] != val ) {
			val = this.updateLocalValue( confKey, val );
		}
		// Update the text field
		$j( _this.selector + ' ._' + confKey ).val( val );
		// Update the interface widget
		switch ( this.default_encoder_config[confKey].type ) {
			case 'slider':
				$j( _this.selector + ' .slider_' + confKey )
					.slider( 'option', 'value', $j( _this.selector + ' ._' + confKey ).val() );
				break;
		}
		return val;
	},

	/**
	 * Validate the new config setting, fixing its type and bounding it within a
	 * range if required. Update the configuration with the validated value and
	 * return it.
	 */
	updateLocalValue: function( confKey, value ) {
		if ( typeof this.default_encoder_config[ confKey ] == 'undefined' ) {
			mw.log( "Error: could not update conf key: " + confKey )
			return value;
		}
		var confEntry = this.default_encoder_config[confKey];
		var range = confEntry.range;
		if ( range ) {
			value = parseInt( value );
			var min = ( range.local_min ) ? range.local_min : range.min;
			if ( value < min )
				value = min;
			var max = ( range.local_max ) ? range.local_max : range.max;
			if (value > max )
				value = max;
		}
		if ( confEntry.type == 'int' )
			value = parseInt( value );

		// step value:
		/* if( confEntry.step ) {
			if ( ( value % confEntry.step ) != 0 ) {
				value = value - (value % confEntry.step);
			}
		}*/

		mw.log( 'update:local_settings:custom:conf:' + confKey + ' = ' + value );
		this.local_settings.presets['custom']['conf'][confKey] = value;

		return value;
	},

	/**
	 * Get a local config value from the custom preset
	 */
	getLocalValue: function( confKey ) {
		return this.local_settings.presets['custom']['conf'][confKey];
	},

	/**
	 * Given an element or selector, get its primary class, and strip a given
	 * prefix from it.
	 *
	 * If no prefix is given, "_" is assumed.
	 */
	getClassId: function( element, prefix ) {

		var eltClass = $j( element ).attr( "class" ).split( ' ' ).slice( 0, 1 ).toString();
		if ( !prefix ) {
			prefix = '_';
		}
		if ( eltClass.substr( 0, prefix.length ) == prefix ) {
			eltClass = eltClass.substr( prefix.length );
		}
		return eltClass;
	},

	/**
	 * Get the appropriate encoder settings for the current Firefogg object,
	 * into which a video has already been selected. Overrides the base method.
	 */
	getEncoderSettings: function() {
		// update the encoder settings (from local settings)
		var pKey = this.local_settings['default'];
		// Update the current encoder settings:
		this.current_encoder_settings = this.local_settings.presets[ pKey ].conf;

		// Call the base function
		// Note that settings will be a reference and can be modified
		this.basefogg_getEncoderSettings();


		// Allow re-encoding of files that are already ogg (unlike in the base class)
		if ( this.isOggFormat() ) {
			this.current_encoder_settings['passthrough'] = false;
		}
		return this.current_encoder_settings;
	},

	/**
	 * Do the necessary UI updates due to the source file changing.
	 * Overrides the parent method.
	 */
	updateSourceFileUI: function() {
		var _this = this;

		// Call the parent
		_this.basefogg_updateSourceFileUI();

		var settings = this.getEncoderSettings();
		var fileInfo = this.getSourceFileInfo();

		// In passthrough mode, hide encoder controls
		if ( settings['passthrough'] ) {
			mw.log( "in passthrough mode (hide control)" );
			$j( this.target_control_container ).hide( 'slow' );
			$j( this.target_passthrough_mode ).show( 'slow' );
			return;
		}

		// Show encoder controls
		$j( this.target_control_container ).show( 'slow' );
		$j( this.target_passthrough_mode ).hide( 'slow' );

		// do set up settings based on local_settings /default_encoder_config with sourceFileInfo
		// see: http://firefogg.org/dev/sourceInfo_example.html
		var setValues = function( k, val, maxVal ) {
			if ( k !== false ) {
				// update the value if unset:
				_this.updateLocalValue( k, val );
			}
			if ( maxVal ) {
				// update the local range:
				if ( _this.default_encoder_config[k].range ) {
					_this.default_encoder_config[k].range.local_max = maxVal;
				}
			}
		}
		// container level settings
		for ( var i in fileInfo ) {
			var val = fileInfo[i];
			var k = false;
			var maxVal = false;
			switch ( i ) {
				// do nothing with these:
				case 'bitrate':
					k = 'videoBitrate';
					if ( val * 2 > this.default_encoder_config[k] ) {
						maxVal = this.default_encoder_config[k];
					} else {
						maxVal = val * 2;
					}
					break;
			}
			setValues( k, val, maxVal );
		}
		if( fileInfo.code && fileInfo.code == 'badfile') {
			// Can't read file info ( but maybe can still encode it?)
			this.updateValuesInHtml();
			return ;
		}
		// Video stream settings
		for ( var i in fileInfo.video[0] ) {
			var val = fileInfo.video[0][i];
			var k = false;
			var maxVal= false;
			switch( i ) {
				case 'width':
				case 'height':
					k = i;
					maxVal = val;
					break;
			}
			setValues( k, val, maxVal );
		}

		// Audio stream settings, assumes for now there is only one stream
		for ( var i in fileInfo.audio[0] ) {
			var val = fileInfo.audio[0][i];
			var k = false;
			var maxVal = false;
			switch ( i ) {
				case 'bitrate':
					k = 'audioBitrate';
					if ( val * 2 > this.default_encoder_config[k] ) {
						maxVal = this.default_encoder_config[k];
					} else {
						maxVal = val * 2;
					}
					break;
			}
			setValues( k, val, maxVal );
		}

		// set all values to new default ranges & update slider:
		$j.each( this.default_encoder_config, function( inx, val ) {
			if ( $j( _this.selector + ' ._' + inx ).length != 0 ) {
				if ( typeof val.range != 'undefined' ) {
					// update slider range
					var new_max = (val.range.local_max) ? val.range.local_max : val.range.max
					$j( _this.selector + ' .slider_' + inx ).slider( 'option', 'max', new_max );

					// update slider/input value:
					_this.updateInterfaceValue( inx,
						_this.local_settings.presets['custom']['conf'][inx] );
				}
			}
		});
		// update values
		this.updateValuesInHtml();
	},

	doEncode: function( progressCallback, doneCallback ) {
		this.basefogg_doEncode( progressCallback, doneCallback );
	},

	/**
	 * Set the HTML control values to whatever is currently present in this.local_settings
	 */
	updateValuesInHtml: function() {
		mw.log( 'updateValuesInHtml::' );
		var _this = this;
		var pKey = this.local_settings[ 'default' ];
		this.updatePresetSelection( pKey );

		// Set the actual HTML & widgets based on any local settings values:
		$j.each( _this.local_settings.presets['custom']['conf'], function( inx, val ) {
			if ( $j( _this.selector + ' ._' + inx ).length != 0 ) {
				$j( _this.selector + ' ._' + inx ).val( val );
			}
		} );
	},

	/**
	 * Restore settings from a cookie (if available)
	 */
	loadEncSettings: function( ) {
		if ( $j.cookie( 'fogg_encoder_config' ) ) {
			mw.log( "load:fogg_encoder_config from cookie " );
			this.local_settings = JSON.parse( $j.cookie( 'fogg_settings' ) );
		}
		// set to default if not loaded yet:
		if ( this.local_settings && this.local_settings.presets
			&& this.local_settings.presets['custom']['conf'] )
		{
			mw.log( 'local settings already populated' );
		} else {
			this.local_settings = this.default_local_settings;
		}
	},

	/**
	 * Clear preset settings
	 * FIXME: not called, does nothing
	 */
	clearSettings: function( force ) {

	},

	/**
	 * Save the current encoder settings to a cookie.
	 */
	saveEncSettings: function() {
		$j.cookie( 'fogg_settings', JSON.stringify( this.local_settings ) );
	}
};
