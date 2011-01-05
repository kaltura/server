
/** Welsh (Cymraeg)
 *
 * @ingroup Language
 *
 * @author Niklas Laxström
 */
	mw.Language.convertPlural = function( count, forms ) {
		

		// FIXME: CLDR defines 4 plural forms; very different, actually.
		// See http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#cy
		forms = mw.Language.preConvertPlural( forms, 6 );
		count = Math.abs( count );
		if ( count >= 0 && count <= 3 ) {
			return forms[count];
		} else if ( count == 6 ) {
			return forms[4];
		} else {
			return forms[5];
		}
	}
