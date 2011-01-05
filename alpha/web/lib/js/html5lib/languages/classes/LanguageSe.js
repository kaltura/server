
/**
 *
 * @ingroup Language
 */
	mw.Language.convertPlural = function( count, forms ) {		
		if( count == 0 ) { 
			return ''; 
		}
		// plural forms per http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#se
		forms = mw.Language.preConvertPlural( forms, 3 );

		if ( count == 1 ) {
			index = 1;
		} else if( count == 2 ) {
			index = 2;
		} else {
			return ''		
		}
		return forms[ index ];
	}
