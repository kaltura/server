

/** Polish (polski)
 *
 * @ingroup Language
 */
	mw.Language.convertPlural = function( count, forms ) {
		
		forms = mw.Language.preConvertPlural( forms, 3 );
		count = Math.abs( count );
		if ( count == 1 )
			return forms[0];     // singular
		switch ( count % 10 ) {
			case 2:
			case 3:
			case 4:
				if ( count / 10 % 10 != 1 )
					return forms[1]; // plural
			default:
				return forms[2];   // plural genitive
		}
	}
