
/**
 *
 * @ingroup Language
 */
	/**
	 * Use singular form for zero
	 * http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#ln

	 */
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 2 );

		return (count <= 1) ? forms[0] : forms[1];
	}
