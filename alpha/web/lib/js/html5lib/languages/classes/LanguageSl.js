

/** Slovenian (Slovenščina)
 *
 * @ingroup Language
 */

	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 5 );

		if ( count % 100 == 1 ) {
			$index = 0;
		} else if ( count % 100 == 2 ) {
			$index = 1;
		} else if ( count % 100 == 3 || count % 100 == 4 ) {
			$index = 2;
		} else if ( count != 0 ) {
			$index = 3;
		} else {
			$index = 4;
		}
		return forms[$index];
	}
