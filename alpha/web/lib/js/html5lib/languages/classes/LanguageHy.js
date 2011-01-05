

/** Armenian (Հայերեն)
 *
 * @ingroup Language
 * @author Ruben Vardanyan (Me@RubenVardanyan.com)
 */
	
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 2 );

		return (Math.abs(count) <= 1) ? forms[0] : forms[1];
	}
	
