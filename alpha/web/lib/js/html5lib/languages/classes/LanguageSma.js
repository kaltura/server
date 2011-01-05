
/**
 *
 * @ingroup Language
 */
	mw.Language.convertPlural = function( count, forms ) {
		
		
		// plural forms per http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#sma
		forms = mw.Language.preConvertPlural( forms, 4 );
		
		if ( count == 1 ) {
			index = 1;
		} else if( count == 2 ) {
			index = 2;
		} else {
			index = 3;
		}
		
		return forms[ index ];
	}
