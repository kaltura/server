mw.addMessages( {
	"ajax-add-category" : "[Add Category]",
	"ajax-add-category-submit" : "[Add]",
	"ajax-confirm-prompt" : "[Confirmation Text]",
	"ajax-confirm-title" : "[Confirmation Title]",
	"ajax-confirm-save" : "[Save]",
	"ajax-add-category-summary" : "[Add category $1]",
	"ajax-remove-category-summary" : "[Remove category $2]",
	"ajax-confirm-actionsummary" : "[Summary]",
	"ajax-error-title" : "Error",
	"ajax-error-dismiss" : "OK",
	"ajax-remove-category-error" : "[RemoveErr]"
} );

var defaultAjaxCategoriesOptions = {	
	// The edit mode can be api, or text box.
	// When set to api, category changes are saved to the target article page
	// when set to textbox category changes are outputted to a text box.  
	'editMode' : 'api'	
}

/**
* AjaxCategories jQuery binder 
* lets you bind a ajaxCategories tool to a div  
*/ 
( function( $ ) {
	$.fn.AjaxCategories = function( options ) {
		var hostElement = $j( this.selector ).get(0);	
		// Merge options with defaults and create new mw.AjaxCategories instance	
		hostElement.AjaxCategories = mw.AjaxCategories(
			$j.extend( {},
				defaultAjaxCategoriesOptions, 
				{'target' : this.selector}, 
				options 
			)
		);
	}	
} )( jQuery );

/**
 * AjaxCategories constructor
 */
mw.AjaxCategories = function( options ){	
	this.setupAJAXCategories( options );
}
mw.AjaxCategories.prototype = {
	
	setupAJAXCategories : function( options ) {		

		var clElement = $j( '.catlinks' );

		// Unhide hidden category holders.
		clElement.removeClass( 'catlinks-allhidden' );

		var addLink = $j( '<a/>' );
		addLink.addClass( 'mw-ajax-addcategory' );

		// Create [Add Category] link
		addLink.text( gM( 'ajax-add-category' ) );
		addLink.attr( 'href', '#' );
		addLink.click( ajaxCategories.handleAddLink );
		clElement.append( addLink );

		// Create add category prompt
		var $promptContainer = $j( '<div />')
		.attr( {
			'id' : "mw-addcategory-prompt"
		} );
		
		var $promptTextbox = $j( '<input />')
		.attr( {
			'type' : "text",
			'size' : "45",
			'id' : "mw-addcategory-input"
		} );
		
		var $addButton = $j( '<input />')
		.attr({
			'type' : "button",
			'id' : "mw-addcategory-button"
		});
		$addButton.val( gM( 'ajax-add-category-submit' ) );

		$promptTextbox.keypress( ajaxCategories.handleCategoryInput );
		$addButton.click( ajaxCategories.handleCategoryAdd );

		$promptContainer.append( $promptTextbox );
		$promptContainer.append( $addButton );
		$promptContainer.hide();

		// Create delete link for each category.
		$j( '.catlinks div span a' ).each( function( e ) {
			// Create a remove link
			var deleteLink = $j( '<a class="mw-remove-category" href="#"/>' );

			deleteLink.click( ajaxCategories.handleDeleteLink );

			$j( this ).after( deleteLink );
		} );

		clElement.append( $promptContainer );
	},
	
	handleAddLink : function( e ) {
		
		e.preventDefault();

		// Make sure the suggestion plugin is loaded. Load everything else while we're at it
		mw.load(
		[
			'$j.ui',		
			'$j.widget',
			'$j.ui.position',
			'$j.ui.dialog', 
			'$j.fn.suggestions'
		],
			function() {
				$j( '#mw-addcategory-prompt' ).toggle();

				$j( '#mw-addcategory-input' ).suggestions( {
					'fetch':ajaxCategories.fetchSuggestions,
					'cancel': function() {
						// TODO support abort mechnism for mwEmbed
						mw.log('Abort request');
					}
				} );
				$j( '#mw-addcategory-input' ).suggestions();
			}
		);
	},

	fetchSuggestions : function( query ) {
		var that = this;
		var request = {
			'list': 'allpages',
			'apnamespace': 14,
			'apprefix': $j( this ).val()
		};
		mw.getJSON( request, function( data ) {
			// Process data.query.allpages into an array of titles
			var pages = data.query.allpages;
			var titleArr = [];

			$j.each( pages, function( i, page ) {
				var title = page.title.split( ':', 2 )[1];
				titleArr.push( title );
			} );

			$j( that ).suggestions( 'suggestions', titleArr );
		} );

		ajaxCategories.request = request;
	},

	reloadCategoryList : function( response ) {
		var holder = $j( '<div/>' );

		holder.load(
			window.location.href + ' .catlinks',
			function() {
				$j( '.catlinks' ).replaceWith( holder.find( '.catlinks' ) );
				ajaxCategories.setupAJAXCategories();
				ajaxCategories.removeProgressIndicator( $j( '.catlinks' ) );
			}
		);
	},

	confirmEdit : function( page, fn, actionSummary, doneFn ) {
		// Load jQuery UI
		mw.load(
			['$j.ui', '$j.ui.position', '$j.ui.dialog', '$j.fn.suggestions'],
			function() {
				// Produce a confirmation dialog

				var dialog = $j( '<div/>' );

				dialog.addClass( 'mw-ajax-confirm-dialog' );
				dialog.attr( 'title', gM( 'ajax-confirm-title' ) );

				// Intro text.
				var confirmIntro = $j( '<p/>' );
				confirmIntro.text( gM( 'ajax-confirm-prompt' ) );
				dialog.append( confirmIntro );

				// Summary of the action to be taken
				var summaryHolder = $j( '<p/>' );
				var summaryLabel = $j( '<strong/>' );
				summaryLabel.text( gM( 'ajax-confirm-actionsummary' ) + " " );
				summaryHolder.text( actionSummary );
				summaryHolder.prepend( summaryLabel );
				dialog.append( summaryHolder );

				// Reason textbox.
				var reasonBox = $j( '<input type="text" size="45" />' );
				reasonBox.addClass( 'mw-ajax-confirm-reason' );
				dialog.append( reasonBox );

				// Submit button
				var submitButton = $j( '<input type="button"/>' );
				submitButton.val( gM( 'ajax-confirm-save' ) );

				var submitFunction = function() {
					dialog.loadingSpinner();
					ajaxCategories.doEdit(
						page,
						fn,
						reasonBox.val(),
						function() {
							doneFn();
							dialog.dialog( 'close' );
							ajaxCategories.removeProgressIndicator( dialog );
						}
					);
				};

				var buttons = { };
				buttons[ gM( 'ajax-confirm-save' ) ] = submitFunction;
				var dialogOptions = {
					'AutoOpen' : true,
					'buttons' : buttons,
					'width' : 450
				};

				$j( '#catlinks' ).prepend( dialog );
				dialog.dialog( dialogOptions );
			}
		);
	},

	doEdit : function( page, fn, summary, doneFn ) {
		// Get an edit token and page revision info
		var getTokenVars = {			
			'prop' : 'info|revisions',
			'intoken' : 'edit',
			'titles' : page,
			'rvprop' : 'content|timestamp'
		};
		mw.getJSON( getTokenVars, function( reply ) {
				var infos = reply.query.pages;
				$j.each(
					infos,
					function( pageid, data ) {
						var token = data.edittoken;
						var timestamp = data.revisions[0].timestamp;
						var oldText = data.revisions[0]['*'];

						var newText = fn( oldText );

						if ( newText === false ) return;

						var postEditVars = {
							'action':'edit',
							'title':page,
							'text':newText,
							'summary':summary,
							'token':token,
							'basetimestamp':timestamp,
							'format':'json'
						};

						mw.getJSON( postEditVars, doneFn );
					}
				);
			} );
	},

	addProgressIndicator : function( elem ) {
		var indicator = $j( '<div/>' );

		indicator.addClass( 'mw-ajax-loader' );

		elem.append( indicator );
	},

	removeProgressIndicator : function( elem ) {
		elem.find( '.mw-ajax-loader' ).remove();
	},

	handleCategoryAdd : function( e ) {
		// Grab category text
		var category = $j( '#mw-addcategory-input' ).val();
		var appendText = "\n[[" + wgFormattedNamespaces[14] + ":" + category + "]]\n";
		var summary = gM( 'ajax-add-category-summary', category );

		ajaxCategories.confirmEdit(
			wgPageName,
			function( oldText ) { return oldText + appendText },
			summary,
			ajaxCategories.reloadCategoryList
		);
	},

	handleDeleteLink : function( e ) {
		e.preventDefault();

		var category = $j( this ).parent().find( 'a' ).text();

		// Build a regex that matches legal invocations of that category.

		// In theory I should escape the aliases, but there's no JS function for it
		//  Shouldn't have any real impact, can't be exploited or anything, so we'll
		//  leave it for now.
		var categoryNSFragment = '';
		$j.each( wgNamespaceIds, function( name, id ) {
			if ( id == 14 ) {
				// Allow the first character to be any case
				var firstChar = name.charAt( 0 );
				firstChar = '[' + firstChar.toUpperCase() + firstChar.toLowerCase() + ']';
				categoryNSFragment += '|' + firstChar + name.substr( 1 );
			}
		} );
		categoryNSFragment = categoryNSFragment.substr( 1 ); // Remove leading |

		// Build the regex
		var titleFragment = category;

		firstChar = category.charAt( 0 );
		firstChar = '[' + firstChar.toUpperCase() + firstChar.toLowerCase() + ']';
		titleFragment = firstChar + category.substr( 1 );
		var categoryRegex = '\\[\\[' + categoryNSFragment + ':' + titleFragment + '(\\|[^\\]]*)?\\]\\]';
		categoryRegex = new RegExp( categoryRegex, 'g' );

		var summary = gM( 'ajax-remove-category-summary', category );

		ajaxCategories.confirmEdit(
			wgPageName,
			function( oldText ) {
				var newText = oldText.replace( categoryRegex, '' );

				if ( newText == oldText ) {
					var error = gM( 'ajax-remove-category-error' );
					ajaxCategories.showError( error );
					ajaxCategories.removeProgressIndicator( $j( '.mw-ajax-confirm-dialog' ) );
					$j( '.mw-ajax-confirm-dialog' ).dialog( 'close' );
					return false;
				}

				return newText;
			},
			summary, ajaxCategories.reloadCategoryList
		);
	},

	showError : function( str ) {
		var dialog = $j( '<div/>' );
		dialog.text( str );

		$j( '#bodyContent' ).append( dialog );

		var buttons = { };
		buttons[gM( 'ajax-error-dismiss' )] = function( e ) {
			dialog.dialog( 'close' );
		};
		var dialogOptions = {
			'buttons' : buttons,
			'AutoOpen' : true,
			'title' : gM( 'ajax-error-title' )
		};

		dialog.dialog( dialogOptions );
	}	
};
