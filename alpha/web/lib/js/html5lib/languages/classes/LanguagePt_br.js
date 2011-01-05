

/** Brazilian Portugese (Portuguêsi do Brasil)
 *
 * @ingroup Language
 */
	/**
	 * Use singular form for zero (see bug 7309)
	 *
	 * removed per bug 25507 
	 *  
	 * 
	 * mw.Language.convertPlural = function( count, forms ) {
	 *	
	 *	forms = mw.Language.preConvertPlural( forms, 2 );
	 *
	 *	return (count <= 1) ? forms[0] : forms[1];
	 *}
	 */
