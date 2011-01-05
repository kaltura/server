

/** Russian (русский язык)
  *
  * You can contact Alexander Sigachov (alexander.sigachov at Googgle Mail)
  *
  * @ingroup Language
  */
	
	/**
	 * Plural form transformations
	 *
	 * forms[0] - singular form (for 1, 21, 31, 41...)
	 * forms[1] - paucal form (for 2, 3, 4, 22, 23, 24, 32, 33, 34...)
	 * forms[2] - plural form (for 0, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 26...)
	 *
	 * Examples:
	 *   message with number
	 *     "Сделано $1 {{PLURAL:$1|изменение|изменения|изменений}}"
	 *   message without number
	 *     "Действие не может быть выполнено по {{PLURAL:$1|следующей причине|следующим причинам}}:"
	 *
	 */

	mw.Language.convertPlural = function( count, forms ) {
		

		//if no number with word, then use $form[0] for singular and $form[1] for plural or zero
		if( forms.length === 2 ) return count == 1 ? forms[0] : forms[1];

		// FIXME: CLDR defines 4 plural forms. Form with decimals missing.
		// See http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#ru
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

	