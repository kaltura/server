/* firefox 3.6 drag-drop uploading
*
* Note: this file is still under development
*/
mw.addMessages( {
	"mwe-upload-multi" : "Upload {{PLURAL:$1|file|files}}",
	"mwe-review-upload": "Review file {{PLURAL:$1|upload|uploads}}"
} );

( function( $ ) {
	$.fn.dragDropFile = function () {
		mw.log( "drag drop: " + this.selector );
		// setup drag binding and highlight
		var dC = $j( this.selector ).get( 0 );
		dC.addEventListener( "dragenter",
			function( event ) {
				$j( dC ).css( 'border', 'solid red' );
				event.stopPropagation();
				event.preventDefault();
			}, false );
		dC.addEventListener( "dragleave",
			function( event ) {
				// default textbox css (should be an easy way to do this)
				$j( dC ).css( 'border', '' );
				event.stopPropagation();
				event.preventDefault();
			}, false );
		dC.addEventListener( "dragover",
			function( event ) {
				event.stopPropagation();
				event.preventDefault();
			}, false );
		dC.addEventListener( "drop",
			function( event ) {
				doDrop( event );
				// handle the drop loader and call event
			}, false );
		function doDrop( event ) {
			var dt = event.dataTransfer,
				files = dt.files,
				fileCount = files.length;

			event.stopPropagation();
			event.preventDefault();

			$j( '#multiple_file_input' ).remove();

			$j( 'body' ).append(
				$j('<div />')
				.attr( {
					'title' : gM( 'mwe-upload-multi', fileCount ),
					'id' : 'multiple_file_input'
				} )
				.css({
					'position' : 'absolute',
					'bottom' : '5em',
					'top' : '3em',
					'right' : '0px',
					'left' : '0px'
				})
			);


			var buttons = { };
			buttons[ gM( 'mwe-cancel' ) ] = function() {
				$j( this ).dialog( 'close' );
			}
			buttons[ gM( 'mwe-upload-multi', fileCount ) ] = function() {
				alert( 'do multiple file upload' );
			}
			// open up the dialog
			$j( '#multiple_file_input' ).dialog( {
				bgiframe: true,
				autoOpen: true,
				modal: true,
				draggable:false,
				resizable:false,
				buttons : buttons
			} );
			$j( '#multiple_file_input' ).dialogFitWindow();
			$j( window ).resize( function() {
				$j( '#multiple_file_input' ).dialogFitWindow();
			} );
			// add the inital table / title:
			$j( '#multiple_file_input' ).empty().html(
				$j('<h3 />')
				.text( gM( 'mwe-review-upload' ) ),

				$j( '<table />' )
				.attr({
					'width' : "100%",
					'border' : "1",
					'border' : 'none'
				})
				.addClass( 'table_list' )
			);

			$j.each( files, function( i, file ) {
				if ( file.fileSize < 64048576 ) {
					$j( '#multiple_file_input .table_list' ).append(
						$j('<tr />').append(
							$j('<td />').css({
								'width': '300px',
								'padding' : '5px'
							}).append(
								$j('<img />').attr( {
									'width' : '250',
									'src' : file.getAsDataURL()
								} )
							),

							$j('<td />')
							.attr('valign', 'top')
							.append(
								'File Name: <input name="file[' + i + '][title]" value="' + file.name + '"><br>' +
								'File Desc: <textarea style="width:300px;" name="file[' + i + '][desc]"></textarea><br>'
							)
						)
					)
					// do the add-media-wizard with the upload tab
				} else {
					alert( "file is too big, needs to be below 64mb" );
				}
			} );
		}
	}
} )( jQuery );
