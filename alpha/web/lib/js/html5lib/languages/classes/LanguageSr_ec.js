/**
 * @ingroup Language
 */
	mw.Language.convertPlural = function(  count, forms ) {		
		forms = mw.Language.preConvertPlural( forms, 3 );

		if (count > 10 && Math.floor((count % 100) / 10) == 1) {
			return forms[2];
		} else {
			switch (count % 10) {
				case 1:  return forms[0];
				case 2:
				case 3:
				case 4:  return forms[1];
				default: return forms[2];
			}
		}
	}
