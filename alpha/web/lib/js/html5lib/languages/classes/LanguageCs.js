

/** Czech (čeština [subst.], český [adj.], česky [adv.])
 *
 * @ingroup Language
 */

// Plural transformations
// Invoked by putting
//   {{plural:count|form1|form2-4|form0,5+}} for two forms plurals
//   {{plural:count|form1|form0,2+}} for single form plurals
// in a message
mw.Language.convertPlural = function( count, forms ) {
	
	forms = mw.Language.preConvertPlural( forms, 3 );
	
	switch ( count ) {
		case 1:			
			return forms[0];
		break;
		case 2:
		case 3:
		case 4:			
			return forms[1];
		break;
		default:			
			return forms[2];
	}
}
