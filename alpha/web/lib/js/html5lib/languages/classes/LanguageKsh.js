

/** Ripuarian (Ripoarėsh)
 *
 * @ingroup Language
 *
 * @author Purodha Blissenbach
 */

	/**
	 * Handle cases of (1, other, 0) or (1, other)
	 */
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 3 );

		if ( count == 1 ) {
			return forms[0];
		} else if ( count == 0 ) {
			return forms[2];
		} else {
			return forms[1];
		}
	}
