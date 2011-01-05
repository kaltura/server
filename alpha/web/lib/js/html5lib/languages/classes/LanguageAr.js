/** Arabic (العربية)
 *
 * @ingroup Language
 *
 */
mw.Language.convertPlural = function( count, forms ){		
	forms = mw.Language.preConvertPlural( forms, 6 );	
	
	if ( count == 0 ) {
		index = 0;
	} else if ( count == 1 ) {
		index = 1;
	} else if( count == 2 ) {
		index = 2;
	} else if( count % 100 >= 3 && count % 100 <= 10 ) {
		index = 3;
	} else if( count % 100 >= 11 && count % 100 <= 99 ) {
		index = 4;
	} else {
		index = 5;
	}
		
	return forms[ index ];
}

// Update the digitTransformTable for ar language key
mw.Language.digitTransformTable = {
	'0' : '٠', // &#x0660;
	'1' : '١', // &#x0661;
	'2' : '٢', // &#x0662;
	'3' : '٣', // &#x0663;
	'4' : '٤', // &#x0664;
	'5' : '٥', // &#x0665;
	'6' : '٦', // &#x0666;
	'7' : '٧', // &#x0667;
	'8' : '٨', // &#x0668;
	'9' : '٩', // &#x0669;
	'.' : '٫', // &#x066b; wrong table ?
	',' : '٬'  // &#x066c;
};