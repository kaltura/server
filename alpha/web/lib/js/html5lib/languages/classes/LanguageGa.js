

/** Irish (Gaeilge)
 *
 * @ingroup Language
 */

	mw.Language.convertPlural = function( count, forms ) {
		

		// plural forms per http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#ga
		forms = mw.Language.preConvertPlural( forms, 3 );

		if ( count == 1 ) {
			$index = 0;
		} else if( count == 2 ) {
			$index = 1;
		} else {
			$index = 2;
		}
		return forms[$index];
	}
