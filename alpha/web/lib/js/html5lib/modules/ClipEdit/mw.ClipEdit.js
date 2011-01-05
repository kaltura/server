/*
*	mw.ClipEdit handles the edit interfaces for images and video
*/

// include all module messages
mw.includeAllModuleMessages();

/**
* The default clipEdit values
*/
var default_clipedit_values = {

	// Resource object for editing
	'resource':	null,

	// Target clip display container
	'target_clip_display':null,

	// Target control container
	'target_control_display':null,

	// Media type (if not supplied its autodetected via: mvClipEdit.getMediaType()
	'media_type': null,

	// Parent Container
	'parent_container': null,

	// Parent remote search driver object pointer
	'parentRemoteSearchDriver': null,

	// Parent sequence object pointer (optional)
	'parentSequence': null,

	/**
	* Object that configures the clip action callbacks
	* 	function :: updateInsertControlActions drives display of these callback
	*
	* supported callback types are: insert_seq, insert, preview, cancel
	*/
	'controlActionsCallback' : null,

	/**
	* The set of tools to enable by default 'all' tools are enabled
	* Can be any sub array of mvClipEdit.toolset
	*
	* crop: tool for cropping the image layout
	* layout: tool for adjusting the layout of the image
	*/
	'enabled_tools' : 'all',

	// Edit profile either "inpage" or "sequence"
	'profile': 'inpage'
}
mw.ClipEdit = function( options ) {
	return this.init( options );
};
mw.ClipEdit.prototype = {
	// Selected tool
	selectedTool : null,

	// Crop values ( populated via the crop tool )
	crop : null,

	// All of the available tools ( displayed tools are set via enabled_tools option )
	toolset : ['crop', 'layout'],

	/**
	* Initialisation function
	*
	* initialises a clipEdit object with provided options.
	*/
	init:function( options ) {
		mw.log('init: mw.ClipEdit' );
		// init object:
		for ( var i in default_clipedit_values ) {
			if ( options[i] ) {
				this[i] = options[i];
			}
		}
		// Display control based on profile
		this.showControlEdit();
	},

	/**
	* Shows the control edit interface based on the clipEdit profile
	*
	* Clip edit profile is either "sequence" or "clip"
	*/
	showControlEdit: function() {
		mw.log( 'showControlEdit' );
		if ( this.profile == 'sequence' ) {
			this.showEditTypesMenu();
			this.showEditUI();
		} else {
			// check the media_type:
			// mw.log('mvClipEdit:: media type:' + this.media_type + ' base width: ' + this.resource.width + ' bh: ' + this.resource.height);
			// could separate out into media Types objects for now just call method
			if ( this.getMediaType() == 'image' ) {
				this.showImageControls();
			} else if (
				this.getMediaType() == 'video'
				||
				this.getMediaType() == 'audio'
			) {
				this.showVideoControls();
			}
		}
	},

	/**
	* Get the mediatype for the current resource
	*/
	getMediaType: function () {
		if( this.media_type )
			return this.media_type;
		// If media type was not supplied detect for resource if possible:
		// @@todo more advanced detection.
		if ( !this.media_type && this.resource && this.resource.type ) {
			if ( this.resource.type.indexOf( "image/" ) === 0 ) {
				this.media_type = 'image';
			} else if ( this.resource.type.indexOf( "video/" ) === 0 ) {
				this.media_type = 'video';
			} else if ( this.resource.type.indexOf( "text/" ) === 0 ) {
				this.media_type = 'template';
			}
		}
		if( this.media_type )
			return this.media_type;
		return false;
	},

	/**
	* Edit types object contains edit types
	*
	* Iterate edit_types media types on a given resource to expose relevent tools
	*
	* NOTE: we could re-factor these into their own classes
	* which extend a base edit object ( and we would only load the requested toolset )
	*/
	edit_types: {
		/*
		* Edit the "duration" of a given resource
		*
		* supports resource types:
		*	['image', 'mwtemplate']
		*/
		'duration': {
			'media' : ['image', 'mwtemplate'],
			'doEdit':function( _this, target ) {
				function doUpdateDur( inputElm ) {
					mw.log( "update duration:" + $j( inputElm ).val() );
					// update the parent sequence object:
					_this.resource.dur = smilParseTime( $j( inputElm ).val() );
					// update the playlist:
					_this.parentSequence.do_refresh_timeline( true );
				}

				$j( target ).empty().html(

					$j('<label />')
					.attr('for','ce_dur')
					.text( gM( 'mwe-clipedit-duration' ) ),

					$j('<input />')
					.attr({
						'name' : "ce_dur",
						'tabindex' : "1",
						'maxlength' : "11",
						'size' : "10"
					})
					.val( mw.seconds2npt( _this.resource.getDuration() ) )

				).children( "input[name='ce_dur']" ).change( function() {
					 doUpdateDur( this );
				} );
				// Strange can't chain this binding for some reason...
				$j( target ).find( "input[name='ce_dur']" ).upDownTimeInputBind( doUpdateDur );
			}
		},

		/**
		* Edit the in and out points for a resource
		*
		* supports resource types:
		* 	['video']
		*/
		'inoutpoints': {
			'media':['video'],
			'doEdit':function( _this, target ) {
				// do clock mouse scroll duration editor
				var end_ntp = ( _this.resource.embed.end_ntp ) ? _this.resource.embed.end_ntp : _this.resource.embed.getDuration();
				if ( !end_ntp )
					end_ntp = mw.seconds2npt( _this.resource.dur );

				var start_ntp = ( _this.resource.embed.start_ntp ) ? _this.resource.embed.start_ntp : mw.seconds2npt( 0 );
				if ( !start_ntp )
					mw.seconds2npt( 0 );
				// make sure we have an end time
				if ( end_ntp ) {
					$j( target ).html(
						_this.getStartEndHtml( {
							'start_ntp'	: start_ntp,
							'end_ntp'	: end_ntp
						} )
					);
					_this.bindStartEndControls();
				}
			}
		},

		/**
		* Edit clip attributes for a given resource.
		*
		* Attributes are dynamically driven via asset type
		*
		* supports resource types:
		* 	['image', 'video', 'template']
		*/
		'attributes': {
			'media': ['image', 'video', 'template'],
			'doEdit':function( _this, target ) {
				// if media type is template we have to query to get its URI to get its parameters
				if ( _this.getMediaType() == 'template' && !_this.resource.tVars ) {
					$j( '#sub_cliplib_ic' ).loadingSpinner()
					var request = {
						'action':'query',
						'prop':'revisions',
						'titles': _this.resource.uri,
						'rvprop':'content'
					};
					// get the interface uri from the plObject
					var apiUrl = _this.parentSequence.plObj.interface_url;
					// first check
					mw.getJSON( apiUrl, request, function( data ) {
						if ( typeof data.query.pages == 'undefined' )
							return _this.showEditOptions( target );
						for ( var i in data.query.pages ) {
							var page = data.query.pages[i];
							if ( !page['revisions'] || !page['revisions'][0]['*'] ) {
								return _this.showEditOptions( target );
							} else {
								var template_rev = page['revisions'][0]['*'];
							}
						}
						var parserObj = mw.parser( template_rev );
						_this.resource.tVars = parserObj.getTemplateVars();
						// Run the editor now that we have updated the template variables:
						_this.showEditOptions( target );
					} );
				} else {
					_this.showEditOptions( target );
				}
			}
		}
		/**
		* Stub for overlay edit support
		*
		* supports resource types:
		* 	['image', 'video']

		'overlays': {
			'media':['image', 'video'],
			'doEdit':function( _this, target ) {
				// do clock mouse scroll duration editor
				$j( target ).html( '<h3>Current Overlays:</h3>Add,Remove,Modify' );
			}
		},
		*/

		/**
		* Stub for audio support
		*
		* supports resource types:
		* 	['image', 'video', 'template'],
		'audio': {
			'media':['image', 'video', 'template'],
			'doEdit':function( _this, target ) {
				// do clock mouse scroll duration editor
				$j( target ).html( '<h3>Audio Volume:</h3>' );
			}
		}
		*/
	},

	/*
	* Outputs the Edit options to a given target
	* @param {String} target Output target for edit options
	*/
	showEditOptions : function( target ) {
		var _this = this;
		// Add html for resource resource:
		var o =	'<table>' +
				'<tr>' +
				'<td colspan="2"><b>' + gM( 'mwe-clipedit-edit_properties' ) + '</b></td>' +
				'</tr>' +
				'<tr>' +
				'<td>' +
				gM( 'mwe-clipedit-custom_title' ) +
				'</td>' +
				'<td><input type="text" size="15" maxwidth="255" value="';

				//Output the resource title if present:
				if ( _this.resource.title != null )
					o += _this.resource.title;

				o += '">' +
				'</td>' +
				'</tr>';

		// Output the resource template var input form
		if ( _this.resource.tVars ) {
			var existing_p = _this.resource.params;
			var testing_a = _this.resource.tVars;
			// debugger;
			o += '<tr>' +
					'<td colspan="2"><b>' + gM( 'mwe-clipedit-template_properties' ) + '</b></td>' +
				'</tr>';
			for ( var i = 0; i < _this.resource.tVars.length; i++ ) {
				o += '<tr>' +
					'<td>' +
						_this.resource.tVars[i] +
					'</td>' +
					'<td><input name="' + _this.resource.tVars[i] + '" class="ic_tparam" type="text" size="15" maxwidth="255" value="';
				if ( _this.resource.params[ _this.resource.tVars[i] ] ) {
					o += _this.resource.params[ _this.resource.tVars[i] ];
				}
				o += '">' +
					'</td>' +
				'</tr>';
			}
		}
		if ( typeof wgArticlePath != 'undefined' ) {
			var res_src = wgArticlePath.replace( /\$1/, _this.resource.uri );
			var res_title = _this.resource.uri;
		} else {
			// var res_page =
			var res_src = _this.resource.src;
			var res_title = mw.parseUri( _this.resource.src ).file;
		}
		o +='<tr>' +
			'<td colspan="2"><b>' + gM( 'mwe-clipedit-other_properties' ) + '</b></td>' +
			'</tr>' +
			'<tr>' +
			'<td>' +
			gM( 'mwe-clipedit-resource_page' ) +
			'</td>' +
			'<td>' +
			'<a href="' + res_src + '" ' +
				'target="new">' +
			res_title +
			'</a>' +
			'</td>' +
			'</tr>' +
			'</table>';

		$j( target ).html ( o );

		// Add update bindings
		$j( target + ' .ic_tparam' ).change( function() {
			mw.log( "updated tparam::" + $j( this ).attr( "name" ) );
			// Update param value:
			_this.resource.params[ $j( this ).attr( "name" ) ] = $j( this ).val();
			// Re-parse & update template
			var template_wiki_text = '{{' + _this.resource.uri;
			for ( var i = 0; i < _this.resource.tVars.length; i++ ) {

				template_wiki_text += "\n|" + _this.resource.tVars[i] + ' = ' + _this.resource.params[ _this.resource.tVars[i] ];
			}
			template_wiki_text += "\n}}";
			var request = {
				'action':'parse',
				'title'	: _this.parentSequence.plObj.mTitle,
				'text'	:	template_wiki_text
			};
			$j( _this.resource.embed ).loadingSpinner();

			var apiUrl = _this.parentSequence.plObj.interface_url;
			mw.getJSON( apiUrl, request, function( data ) {
				if ( data.parse.text['*'] ) {
					// update the target
					$j( _this.resource.embed ).html( data.parse.text['*'] );
				}
			} );
		} )

		// Update doFocusBindings
		if ( _this.parentSequence )
			_this.parentSequence.doFocusBindings();
	},

	/**
	* Show Edit Types Menu
	*/
	showEditTypesMenu:function() {
		var _this = this;

		// Add in relevant subMenus
		var o = '';
		var tabc = '';
		o += '<div id="mv_submenu_clipedit">';
		o += '<ul>';
		var first_tab = false;
		$j.each( this.edit_types, function( sInx, editType ) {
			// check if the given editType is valid for our given media type
			var include = false;
			for ( var i = 0; i < editType.media.length; i++ ) {
				if ( editType.media[i] == _this.getMediaType() ) {
					include = true;
					if ( !first_tab )
						first_tab = sInx;
				}
			}
			if ( include ) {
				o +='<li>' +
						'<a id="mv_smi_' + sInx + '" href="#sc_' + sInx + '">' + gM( 'mwe-clipedit-sc_' + sInx ) + '</a>' +
					'</li>';
				tabc += '<div id="sc_' + sInx + '" style="overflow:auto;" ></div>';
			}
		} );
		o += '</ul>' + tabc;
		o += '</div>';

		// Add sub menu container with menu html:
		$j( '#' + this.target_control_display ).html( o );

		// Do clip edit bindings:
		$j( '#mv_submenu_clipedit' ).tabs( {
			selected: 0,
			select: function( event, ui ) {
				_this.showEditUI( $j( ui.tab ).attr( 'id' ).replace( 'mv_smi_', '' ) );
			}
		} ).addClass( 'ui-tabs-vertical ui-helper-clearfix' );

		// Close left:
		$j( "#mv_submenu_clipedit li" ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-left' );
		// update the default edit display:
		_this.showEditUI( first_tab );
	},

	/**
	* Show the edit User Interface for edit type
	*
	* @param {String} edit_type key for the edit interface
	*/
	showEditUI:function( edit_type ) {
		if ( !edit_type )
			return false;
		mw.log( 'showEditUI: ' + edit_type );
		if ( this.edit_types[ edit_type ].doEdit )
			this.edit_types[ edit_type ].doEdit( this, '#sc_' + edit_type );
	},

	/**
	* Show Video Controls for the resource edit
	*/
	showVideoControls: function() {
		mw.log( 'showVideoControls:f' );
		var _this = this;
		var eb = $j( '#embed_vid' ).get( 0 );
		// turn on preview to avoid onDone actions
		eb.previewMode = true;
		$j( '#' + this.target_control_display ).html( '<h3>' + gM( 'mwe-clipedit-edit-video-tools' ) + '</h3>' );
		if ( eb.supportsURLTimeEncoding() ) {
			if ( eb.end_ntp ) {
				$j( '#' + this.target_control_display ).append(
					_this.getStartEndHtml( {
						'start_ntp'	: eb.start_ntp,
						'end_ntp'	: eb.end_ntp
					} )
				);
				_this.bindStartEndControls();
			}
		}
		// If in a Sequence we have no need for insertDesc
		if ( !_this.parentSequence ) {
			$j( '#' + this.target_control_display ).append(	_this.getInsertHtml() );
		}
		// update control actions
		this.updateInsertControlActions();
	},

	/**
	* Bind the Start End video controls
	*/
	bindStartEndControls:function() {
		var _this = this;
		// Setup a top level shortcut:
		var $target = $j( '#' + this.target_control_display );

		var start_sec = mw.npt2seconds( $target.find( '.startInOut' ).val() );
		var end_sec   = mw.npt2seconds( $target.find( '.endInOut' ).val() );

		// If we don't have 0 as start then assume we are in a range request and give some buffer area:
		var min_slider = ( start_sec - 60 < 0 ) ? 0 : start_sec - 60;
		if ( min_slider != 0 ) {
			var max_slider = end_sec + 60;
		} else {
			max_slider = end_sec;
		}

		$target.find( '.inOutSlider' ).slider( {
			range: true,
			min: min_slider,
			max: max_slider,
			animate: true,
			values: [start_sec, end_sec],
			slide: function( event, ui ) {
				// mw.log(" vals:"+ mw.seconds2npt( ui.values[0] ) + ' : ' + mw.seconds2npt( ui.values[1]) );
				$target.find( '.startInOut' ).val( mw.seconds2npt( ui.values[0] ) );
				$target.find( '.endInOut' ).val( mw.seconds2npt( ui.values[1] ) );
			},
			change:function( event, ui ) {
				_this.updateVideoTime( mw.seconds2npt( ui.values[0] ), mw.seconds2npt( ui.values[1] ) );
			}
		} );

		// Bind up and down press when focus on start or end
		$target.find( '.startInOut' ).upDownTimeInputBind( function( inputElm ) {
			var s_sec = mw.npt2seconds( $j( inputElm ).val() );
			var e_sec = mw.npt2seconds( $target.find( '.endInOut' ).val() )
			if ( s_sec > e_sec )
				$j( inputElm ).val( mw.seconds2npt( e_sec - 1 ) );

			// Update the slider:
			var values = $target.find( '.inOutSlider' ).slider( 'option', 'values' );
			mw.log( 'in slider len: ' + $target.find( '.inOutSlider' ).length );

			$target.find( '.inOutSlider' ).slider( 'value', 10 );
			$target.find( '.inOutSlider' ).slider( 'option', 'values', [s_sec, e_sec] );
			var values = $target.find( '.inOutSlider' ).slider( 'option', 'values' );
			mw.log( 'values (after update):' + values );
		} );

		$target.find( '.endInOut' ).upDownTimeInputBind( function( inputElm ) {
			var s_sec = mw.npt2seconds( $target.find( '.startInOut' ).val() );
			var e_sec = mw.npt2seconds( $j( inputElm ).val() );
			if ( e_sec < s_sec )
				$j( inputElm ).val( mw.seconds2npt( s_sec + 1 ) );
			// update the slider:
			$target.find( '.inOutSlider' ).slider( 'option', 'values', [ s_sec, e_sec ] );
		} );

		// Preview button:
		$j( '#' + this.target_control_display + ' .inOutPreviewClip' ).buttonHover().click( function() {
			$j( '#embed_vid' ).get( 0 ).stop();
			$j( '#embed_vid' ).get( 0 ).play();
		} );
	},

	/**
	* Update the video time
	* Target video is hard coded to #embed_vid for now
	*
	* @param {String} start_npt Start time in npt format
	* @param {String} end_npt End time in npt format
	*/
	updateVideoTime : function ( start_npt, end_npt )	{
		// Update the video title:
		var ebvid = $j( '#embed_vid' ).get( 0 );
		if ( ebvid ) {
			ebvid.stop();
			ebvid.updateVideoTime( start_time, end_time );
			mw.log( 'update thumb: ' + start_time );
			ebvid.updateThumbTimeNPT( start_time );
		}
	},

	/**
	* Get the start end html
	*
	* start end html supports setting start and end times for video clips
	*
	* @param {Object} defaultTime Provides start and end time default values
	*/
	getStartEndHtml: function( defaultTime ) {
		return '<strong>' + gM( 'mwe-clipedit-set_in_out_points' ) + '</strong>' +
			'<table border="0" style="background: transparent; width:94%;height:50px;">' +
			'<tr>' +
			'<td style="width:90px">' +
				gM( 'mwe-clipedit-start_time' ) +
			'<input class="ui-widget-content ui-corner-all startInOut" size="9" value="' + defaultTime.start_ntp + '">' +
			'</td>' +
			'<td>' +
			'<div class="inOutSlider"></div>' +
			'</td>' +
			'<td style="width:90px;text-align:right;">' +
						gM( 'mwe-clipedit-end_time' ) +
			'<input class="ui-widget-content ui-corner-all endInOut" size="9" value="' + defaultTime.end_ntp + '">' +
			'</td>' +
			'</tr>' +
			'</table>' +
			$j.btnHtml( gM( 'mwe-clipedit-preview_inout' ), 'inOutPreviewClip', 'video' );
	},

	/**
	* Get the Insert Html form text area
	*/
	getInsertHtml: function() {
		var o = '<h3>' + gM( 'mwe-clipedit-inline-description' ) + '</h3>' +
					'<textarea style="width:95%" id="mv_inline_img_desc" rows="5" cols="30">';
		if ( this.parentRemoteSearchDriver ) {
			// If we have a parent remote search driver let it parse the inline description
			o += this.resource.pSobj.getInlineDescWiki( this.resource );
		}
		o += '</textarea><br>';
		// mw.log('getInsertHtml: ' + o );
		return o;
	},

	/**
	* Update Insert Control Actions
	*
	* Loops over the local controlActionsCallback
	*/
	updateInsertControlActions: function() {
		var _this = this;
		var b_target = _this.parentRemoteSearchDriver.target_container + '~ .ui-dialog-buttonpane';
		// Empty the ui-dialog-buttonpane bar:
		$j( b_target ).empty();
		for ( var callbackType in _this.controlActionsCallback ) {
			switch( callbackType ) {
				case 'insert_seq':
					$j( b_target ).append( $j.btnHtml( gM( 'mwe-clipedit-insert_into_sequence' ), 'mv_insert_sequence', 'check' ) + ' ' )
						.children( '.mv_insert_sequence' )
						.buttonHover()
						.click( function() {
							_this.applyEdit();
							_this.controlActionsCallback[ 'insert_seq' ]( _this.resource );
						} );
				break;
				case 'insert':
					$j( b_target ).append( $j.btnHtml( gM( 'mwe-clipedit-insert_image_page' ), 'mv_insert_image_page', 'check' ) + ' ' )
						.children( '.mv_insert_image_page' )
						.buttonHover()
						.click( function() {
							_this.applyEdit();
							_this.controlActionsCallback['insert']( _this.resource );
						} ).show( 'slow' );
				break;
				case 'preview':
					$j( b_target ).append( $j.btnHtml( gM( 'mwe-clipedit-preview_insert' ), 'mv_preview_insert', 'refresh' ) + ' ' )
						.children( '.mv_preview_insert' )
						.buttonHover()
						.click( function() {
							_this.applyEdit();
							_this.controlActionsCallback[ 'preview' ]( _this.resource );
						} ).show( 'slow' );
				break;
				case 'cancel':
					$j( b_target ).append( $j.btnHtml( gM( 'mwe-clipedit-cancel_image_insert' ), 'mv_cancel_img_edit', 'close' ) + ' ' )
						.children( '.mv_cancel_img_edit' )
						.buttonHover()
						.click( function() {
							// no cancel action;
							_this.controlActionsCallback['cancel']( _this.resource );
						} ).show( 'slow' );
				break;
			}
		}
	},

	/**
	* Applies the current edit to the resource object
	* supports "crop" and "videoAdjustment"
	*/
	applyEdit:function() {
		var _this = this;
		mw.log( 'applyEdit::' + this.getMediaType() );
		if ( this.getMediaType() == 'image' ) {
			this.applyCrop();
		} else if ( this.getMediaType() == 'video' ) {
			this.applyVideoStartEnd();
		}
		// copy over the description text to the resource object
		_this.resource['inlineDesc'] = $j( '#mv_inline_img_desc' ).val();
	},

	/**
	* Adds a tool to the supplied target
	*
	* @param {Object} $target jQuery object to append the tool to
	* @param {Object} tool_type Type key for the tool to be added
	*/
	addTool: function( $target, tool_type ) {
		var _this = this;
		switch( tool_type ) {
			case 'layout':

				$target.append(
					$j( '<span />' )
					.css({
						"float" : "left"
					})
					.text( gM( 'mwe-clipedit-layout' ) ),

					// Left layout
					$j('<input />')
					.attr({
						"type" : "radio",
						"name" : "mw_layout",
						"id" : "mw_layout_left"
					})
					.css({
						'float' : 'left'
					}),
					$j( '<div /> ')
					.attr({
						'id' : 'mw_layout_left_img',
						'title': gM( 'mwe-clipedit-layout_left' )
					}),

					// Right Layout
					$j('<input />')
					.attr({
						"type" : "radio",
						"name" : "mw_layout",
						"id" : "mw_layout_right"
					})
					.css({
						'float' : 'left'
					}),
					$j( '<div /> ')
					.attr({
						'id' : 'mw_layout_right_img',
						'title': gM( 'mwe-clipedit-layout_right' )
					}),

					$j('<hr />')
					.css({
						"clear" : "both"
					})
				);

				// Make sure the default is reflected:
				if ( ! _this.resource.layout ) {
					_this.resource.layout = 'right';
				}

				$j( '#mw_layout_' + _this.resource.layout )[0].checked = true;

				// Left radio click
				$j( '#mw_layout_left,#mw_layout_left_img' ).click( function( event ) {
					$j( '#mw_layout_right' )[0].checked = false;
					$j( '#mw_layout_left' )[0].checked = true;
					_this.resource.layout = 'left';
					event.stopPropagation();
				} );

				// Right radio click
				$j( '#mw_layout_right,#mw_layout_right_img' ).click( function( event ) {
					$j( '#mw_layout_left' )[0].checked = false;
					$j( '#mw_layout_right' )[0].checked = true;
					_this.resource.layout = 'right';
					event.stopPropagation();
				} );
			break;
			case 'crop':
				$target.append(
					$j( '<div /> ')
					.addClass( 'mw_edit_button mw_crop_button_base' )
					.attr({
						'id' : 'mw_crop_button',
						'alt' : 'crop',
						'title' : gM( 'mwe-clipedit-crop' )
					}),

					$j( '<a />')
					.attr({
						'href': '#'
					})
					.addClass( 'mw_crop_msg' )
					.text( gM( 'mwe-clipedit-crop' ) ),

					$j( '<span />' )
					.addClass( 'mw_crop_msg_load')
					.text( gM( 'mwe-loading_txt' ) )
					.hide(),

					$j( '<a />' )
					.attr({
						'href': '#'
					})
					.css({
						'display': 'inline'
					})
					.addClass( 'mw_apply_crop' )
					.text( gM( 'mwe-clipedit-apply_crop' ) )
					.hide(),

					// some space between apply and rest
					$j('<span />')
					.css('display','inline')
					.text(' '),

					$j( '<a />' )
					.attr( 'href','#' )
					.css({
						'display': 'inline'
					})
					.addClass( 'mw_reset_crop' )
					.text( gM( 'mwe-clipedit-reset_crop' ) )
					.hide(),

					$j( '<hr />' )
					.css('clear', 'both'),

					$j( '<br />' )

				);
				// Add binding:
				$j( '#mw_crop_button,.mw_crop_msg,.mw_apply_crop' ).click( function() {
					mw.log( 'click:mv_crop_button: base width: ' + _this.resource.width + ' bh: ' + _this.resource.height );
					if ( $j( '#mw_crop_button' ).hasClass( 'mw_crop_button_selected' ) ) {
						_this.applyCrop();
					} else {
						_this.doCropInterface();
					}
				} );
				$j( '.mw_reset_crop' ).click( function() {
					$j( '.mw_apply_crop,.mw_reset_crop' ).hide();
					$j( '.mw_crop_msg' ).show();
					$j( '#mw_crop_button' ).removeClass( 'mw_crop_button_selected' ).addClass( 'mw_crop_button_base' ).attr( 'title', gM( 'mwe-clipedit-crop' ) );
					_this.resource.crop = null;
					$j( '#' + _this.target_clip_display ).empty().html(
						$j('<img />')
						.attr({
							'src' : _this.resource.edit_url,
							'id' : 'rsd_edit_img'
						})
					);
				} );
			break;
			/* TODO support setting the image "scale":
			case 'scale':

			break;
			*/
		}
	},

	/**
	* Show Image Controls
	*/
	showImageControls: function() {
		var _this = this;
		var $tool_target = $j( '#' + this.target_control_display );
		mw.log( 'tool target len: ' + $tool_target.length );
		// By default apply Crop tool
		if ( _this.enabled_tools == 'all' || _this.enabled_tools.length > 0 ) {
			$tool_target.html(
				$j( '<h3 />' )
				.text( gM( 'mwe-clipedit-edit-tools' ) )
			);
			for ( var i in _this.toolset ) {
				var toolid = _this.toolset[i];
				if ( $j.inArray( toolid, _this.enabled_tools ) != -1 || _this.enabled_tools == 'all' )
					_this.addTool( $tool_target, toolid );
			}
		}

		// Add the insert description text field:
		$tool_target.append( _this.getInsertHtml() );

		// Add the actions to the 'button bar'
		_this.updateInsertControlActions();
	},

	/**
	* Apply Image Crop to the edit resource image
	*/
	applyCrop: function() {
		var _this = this;
		$j( '.mw_apply_crop' ).hide();
		$j( '.mw_crop_msg' ).show();

		// Update the crop button:
		$j( '#mw_crop_button' )
			.removeClass( 'mw_crop_button_selected' )
			.addClass( 'mw_crop_button_base' )
			.attr( 'title', gM( 'mwe-clipedit-crop' ) );

		if ( _this.resource.crop ) {
			// Empty out and display cropped:
			$j( '#' + _this.target_clip_display )
				.empty()
				.append(
					$j('<div />')
					.css({
						'overflow' : 'hidden',
						'position' : 'absolute',
						'width' : parseInt( _this.resource.crop.w ) + 'px',
						'height' : parseInt( _this.resource.crop.h ) + 'px'
					})
					.append(
						$j( '<div />' )
						.css({
							'position' : 'absolute',
							'top' : '-' + parseInt( _this.resource.crop.y ) + 'px',
							'left' : '-' + parseInt( _this.resource.crop.x ) + 'px'
						})
						.append(
							$j('<img />')
							.attr( 'src', _this.resource.edit_url )
						)
					)
				)
		}
		return true;
	},

	/**
	* Apply the video Start End Adjustments to the resource
	*/
	applyVideoStartEnd:function() {
		mw.log( 'apply Video StartEnd updates::' );
		$target = $j( '#' + this.target_control_display );

		// Be sure to "stop the video (some plugins can't have DOM elements on top of them)
		$j( '#embed_vid' ).get( 0 ).stop();

		// Update video related keys
		this.resource['start_time'] = $target.find( '.startInOut' ).val();
		this.resource['end_time'] = $target.find( '.endInOut' ).val();

		// Do the local video adjust
		if ( typeof this.resource.pSobj['applyVideoAdj'] != 'undefined' ) {
			this.resource.pSobj.applyVideoAdj( this.resource );
		}
	},

	/**
	* Do the crop Interface
	*/
	doCropInterface: function() {
		var _this = this;
		$j( '.mw_crop_msg' ).hide();
		$j( '.mw_crop_msg_load' ).show();
		// load the jcrop library if needed:
		mw.load( [
			'$j.Jcrop',
			'mw.style.Jcrop'
		], function() {
			_this.bindCrop();
		} );
	},

	/**
	* Bind the Crop once the library $j.Jcrop is ready:
	*/
	bindCrop: function() {
		var _this = this;
		$j( '.mw_crop_msg_load' ).hide();
		$j( '.mw_reset_crop,.mw_apply_crop' ).show();
		$j( '#mw_crop_button' ).removeClass( 'mw_crop_button_base' ).addClass( 'mw_crop_button_selected' ).attr( 'title', gM( 'mwe-clipedit-crop_done' ) );
		$j( '#' + _this.target_clip_display + ' img' ).Jcrop( {
			 onSelect: function( c ) {
				 mw.log( 'on select:' + c.x + ',' + c.y + ',' + c.x2 + ',' + c.y2 + ',' + c.w + ',' + c.h );
				 _this.resource.crop = c;
			 },
			 onChange: function( c ) {
			 }
		} );
		// Temporary hack (@@todo need to debug why rsd_res_item gets moved )
		$j( '#clip_edit_disp .rsd_res_item' ).css( {
			'top' : '0px',
			'left' : '0px'
		} );
	}
};

( function( $ ) {
	// jQuery Binding for upDownTimeInputBind
	$.fn.upDownTimeInputBind = function( inputCB ) {
		$( this.selector ).unbind( 'focus' ).focus( function() {
			var doDelayCall = true;
			$( this ).addClass( 'ui-state-focus' );
			// Bind up down keys
			$( this ).unbind( 'keydown' ).keydown( function ( e ) {
				var sec = mw.npt2seconds( $j( this ).val() );
				var k = e.which;
				if ( k == 38 ) {// up
					$( this ).val( mw.seconds2npt( sec + 1 ) );
				} else if ( k == 40 ) { // down
					var sval = ( ( sec - 1 ) < 0 ) ? 0 : ( sec - 1 )
					$( this ).val( mw.seconds2npt( sval ) );
				}
				// Set the delay updates:
				if ( k == 38 || k == 40 ) {
					var _inputElm = this;
					if ( doDelayCall ) {
						setTimeout( function() {
							inputCB( _inputElm );
							doDelayCall = true;
						}, 500 );
						doDelayCall = false;
					}
				}
			} );
		} ).unbind( 'blur' ).blur( function() {
			$( this ).removeClass( 'ui-state-focus' );
		} );
	}
} )( jQuery );
