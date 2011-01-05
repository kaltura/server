

/** Maltese (Malti)
 *
 * @ingroup Language
 *
 * @author Niklas Laxström
 */

	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 4 );
		
		if ( count == 1 ){			
			index = 0;			
		} else if ( count == 0 || ( count % 100 > 1 && count % 100 < 11) ){
			index = 1;
		} else if ( count % 100 > 10 && count % 100 < 20 ){
			index = 2;
		}else {
			index = 3;
		}
		return forms[index];
	}
