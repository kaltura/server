
/**
 * Slovak (Slovenčina)
 *
 * @ingroup Language
 */
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 3 );

		if ( count == 1 ) {
			$index = 0;
		} else if ( count == 2 || count == 3 || count == 4 ) {
			$index = 1;
		} else {
			$index = 2;
		}
		return forms[$index];
	}
