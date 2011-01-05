

/** Latvian (Latviešu)
 *
 * @ingroup Language
 *
 * @author Niklas Laxström
 *
 * @copyright Copyright © 2006, Niklas Laxström
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
	/**
	 * Plural form transformations. Using the first form for words with the last digit 1, but not for words with the last digits 11, and the second form for all the others.
	 *
	 * Example: {{plural:{{NUMBEROFARTICLES}}|article|articles}}
	 *
	 * @param integer count
	 * @param string $wordform1
	 * @param string $wordform2
	 * @param string $wordform3 (not used)
	 * @return string
	 */
	mw.Language.convertPlural = function( count, forms ) {
		

		// FIXME: CLDR defines 3 plural forms instead of 2.  Form for 0 is missing.
		//        http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#lv
		forms = mw.Language.preConvertPlural( forms, 2 );

		return ( ( count % 10 == 1 ) && ( count % 100 != 11 ) ) ? forms[0] : forms[1];
	}
