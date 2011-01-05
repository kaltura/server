

/** Old Church Slavonic (Ѩзыкъ словѣньскъ)
 *
 * @ingroup Language
 */	

mw.Language.convertPlural = function( count, forms ) {
	
	forms = mw.Language.preConvertPlural( forms, 4 );

	switch (count % 10) {
		case 1:  return forms[0];
		case 2:  return forms[1];
		case 3:
		case 4:  return forms[2];
		default: return forms[3];
	}
}

