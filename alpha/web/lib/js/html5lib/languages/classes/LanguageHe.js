

/**
 * Hebrew (עברית)
 *
 * @ingroup Language
 *
 * @author Rotem Liss
 */

	/**
	 * Gets a number and uses the suited form of the word.
	 *
	 * @param integer the number of items
	 * @param string the first form (singular)
	 * @param string the second form (plural)
	 * @param string the third form (2 items, plural is used if not applicable and not specified
	 * @param not used (for compatibility with ancestor)
	 * @param not used (for compatibility with ancestor)
	 *
	 * @return string of the suited form of word
	 */
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 3 );

		if ( count == '1' ) {
			return forms[0];
		} else if ( count == '2' && forms[2] ) {
			return forms[2];
		} else {
			return forms[1];
		}
	}
