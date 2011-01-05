/**
 * Simple language selection uses. 
 * 
 * @dependcy 
 */


/**
 * Get the language box 
 * 
 */
mw.ui.languageSelectBox = function( options ){
		// Build a select object 
		// TODO test string construction instead of jQuery build out for performance  
		var $langSelect = $j('<select />')
		if( options.id )
			$langSelect.attr('id',  options.id);
		
		if( options['class'] )
			$langSelect.addClass(options['class'] );
		
		var selectedLanguageKey = ( options.selectedLanguage )? options.selectedLanguage : this.getLanguageKey();
		
		// For every language ( mw.Language.names is a dependency of mw.LanguageSelect ) 
		for( var langKey in mw.Language.names ){
			var optionAttr = {
				'value': langKey
			};
			if( langKey == selectedLanguageKey ){
				optionAttr['selected'] = 'true';
			}
			$langSelect.append(
				$j('<option />')
					.attr( optionAttr )
					.text( langKey + ', ' + mw.Language.names[langKey] )
			);
		}
		return $langSelect;
	},
	// For now most uploaded videos are in English ( we don't try and detect the language )
	// TOOD we could check the user content settings and guess maybe they are viewing a video in that
	// language ?
	getDefaultLanguageKey: function(){
		return 'en';
	}
};