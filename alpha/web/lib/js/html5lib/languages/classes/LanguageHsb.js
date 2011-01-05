
/** Upper Sorbian (Hornjoserbsce)
 *
 * @ingroup Language
 */

	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 4 );

		switch ( Math.abs( count ) % 100 ) {
			case 1:  return forms[0]; // singular
			case 2:  return forms[1]; // dual
			case 3:
			case 4:  return forms[2]; // plural
			default: return forms[3]; // pluralgen
		}
	}
