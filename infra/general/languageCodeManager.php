<?php

final class languageCodeManager
{
    private static $instance;

    private static $arrayTwoCodeUppercaseKey;
    private static $arrayThreeCodeTKeyToTwoCodeUppercase;
    private static $arrayKalturaLanguageNameKeyToTwoCodeUppercase;

    const TWO_CODE_LOWERCASE = 0;
    const THREE_CODE_T = 1;
    const THREE_CODE_B = 2;
    const LANGUAGE_NAME_ISO = 3;
    const NATIVE_NAME = 4;
    const LANGUAGE_NAME_KALTURA = 5;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new languageCodeManager();
        }
        return self::$instance;
    }

    private function __clone(){ }

    /**
     * @param $twoLettersUpper - upper case language code as in ISO 639-1
     * @param $twoLettersLower - lower case language code as in ISO 639-1
     * @param $threeLetters_T - lower case three letters language code as in ISO 639-2/T  - if two code is not official then the 3 code is made up
     * @param $threeLetters_B - lower case three letters language code as in ISO 639-2/B  - if two code is not official then the 3 code is made up
     * @param $languageName - language name
     * @param $nativeName - native language name
     * @param $kalturaName - kaltura language name as in KalturaLanguage, if the language is not defined in kaltura then $kalturaName is the same as $languageName
     */

    private static function addLanguageToArrays($twoLettersUpper ,$twoLettersLower,$threeLettersT,$threeLettersB,$languageName,$nativeName,$kalturaName)
    {
        self::$arrayTwoCodeUppercaseKey[$twoLettersUpper] = array($twoLettersLower,$threeLettersT,$threeLettersB,$languageName,$nativeName,$kalturaName);
        self::$arrayThreeCodeTKeyToTwoCodeUppercase[$threeLettersT] = $twoLettersUpper;
        self::$arrayKalturaLanguageNameKeyToTwoCodeUppercase[$kalturaName] = $twoLettersUpper;
    }

    private function __construct()
    {
        self::addLanguageToArrays('AB', "ab", "abk", "abk", "Abkhaz", "\xd0\xb0\xd2\xa7\xd1\x81\xd1\x83\xd0\xb0 \xd0\xb1\xd1\x8b\xd0\xb7\xd1\x88\xd3\x99\xd0\xb0, \xd0\xb0\xd2\xa7\xd1\x81\xd1\x88\xd3\x99\xd0\xb0",'Abkhazian');
        self::addLanguageToArrays('AA', "aa", "aar", "aar", "Afar", "Afaraf",'Afar');
        self::addLanguageToArrays('AF', "af", "afr", "afr", "Afrikaans", "Afrikaans",'Afrikaans');
        self::addLanguageToArrays('AK', "ak", "aka", "aka", "Akan", "Akan",'Akan');  //not  yet defined in kaltura
        self::addLanguageToArrays('SQ', "sq", "sqi", "alb", "Albanian", "Shqip",'Albanian');
        self::addLanguageToArrays('AM', "am", "amh", "amh", "Amharic", "\xe1\x8a\xa0\xe1\x88\x9b\xe1\x88\xad\xe1\x8a\x9b",'Amharic');
        self::addLanguageToArrays('AR', "ar", "ara", "ara", "Arabic", "\xd8\xa7\xd9\x84\xd8\xb9\xd8\xb1\xd8\xa8\xd9\x8a\xd8\xa9",'Arabic');
        self::addLanguageToArrays('AN', "an", "arg", "arg", "Aragonese", "aragon\xc3\xa9s",'Aragonese');//not  yet defined in kaltura
        self::addLanguageToArrays('HY', "hy", "hye", "arm", "Armenian", "\xd5\x80\xd5\xa1\xd5\xb5\xd5\xa5\xd6\x80\xd5\xa5\xd5\xb6",'Armenian');
        self::addLanguageToArrays('AS_', "as", "asm", "asm", "Assamese", "\xe0\xa6\x85\xe0\xa6\xb8\xe0\xa6\xae\xe0\xa7\x80\xe0\xa6\xaf\xe0\xa6\xbc\xe0\xa6\xbe",'Assamese');
        self::addLanguageToArrays('AV', "av", "ava", "ava", "Avaric", "\xd0\xb0\xd0\xb2\xd0\xb0\xd1\x80 \xd0\xbc\xd0\xb0\xd1\x86\xd3\x80, \xd0\xbc\xd0\xb0\xd0\xb3\xd3\x80\xd0\xb0\xd1\x80\xd1\x83\xd0\xbb \xd0\xbc\xd0\xb0\xd1\x86\xd3\x80",'Avaric');//not  yet defined in kaltura
        self::addLanguageToArrays('AE', "ae", "ave", "ave", "Avestan", "avesta",'Avestan');
        self::addLanguageToArrays('AY', "ay", "aym", "aym", "Aymara", "aymar aru",'Aymara');
        self::addLanguageToArrays('AZ', "az", "aze", "aze", "Azerbaijani", "az\xc9\x99rbaycan dili",'Azerbaijani');
        self::addLanguageToArrays('BM', "bm", "bam", "bam", "Bambara", "bamanankan",'Bambara'); // no two letter code in kalturaLanguauge  // bug in languagekey
        self::addLanguageToArrays('BA', "ba", "bak", "bak", "Bashkir", "\xd0\xb1\xd0\xb0\xd1\x88\xd2\xa1\xd0\xbe\xd1\x80\xd1\x82 \xd1\x82\xd0\xb5\xd0\xbb\xd0\xb5",'Bashkir');
        self::addLanguageToArrays('EU', "eu", "eus", "baq", "Basque", "euskara, euskera",'Basque');
        self::addLanguageToArrays('BE', "be", "bel", "bel", "Belarusian", "\xd0\xb1\xd0\xb5\xd0\xbb\xd0\xb0\xd1\x80\xd1\x83\xd1\x81\xd0\xba\xd0\xb0\xd1\x8f \xd0\xbc\xd0\xbe\xd0\xb2\xd0\xb0",'Byelorussian (Belarusian)');
        self::addLanguageToArrays('BN', "bn", "ben", "ben", "Bengali, Bangla", "\xe0\xa6\xac\xe0\xa6\xbe\xe0\xa6\x82\xe0\xa6\xb2\xe0\xa6\xbe",'Bengali (Bangla)');
        self::addLanguageToArrays('BH', "bh", "bih", "bih", "Bihari", "\xe0\xa4\xad\xe0\xa5\x8b\xe0\xa4\x9c\xe0\xa4\xaa\xe0\xa5\x81\xe0\xa4\xb0\xe0\xa5\x80",'Bihari');
        self::addLanguageToArrays('BI', "bi", "bis", "bis", "Bislama", "Bislama",'Bislama');
        self::addLanguageToArrays('BS', "bs", "bos", "bos", "Bosnian", "bosanski jezik",'Bosnian');//not  yet defined in kaltura
        self::addLanguageToArrays('BR', "br", "bre", "bre", "Breton", "brezhoneg",'Breton');
        self::addLanguageToArrays('BG', "bg", "bul", "bul", "Bulgarian", "\xd0\xb1\xd1\x8a\xd0\xbb\xd0\xb3\xd0\xb0\xd1\x80\xd1\x81\xd0\xba\xd0\xb8 \xd0\xb5\xd0\xb7\xd0\xb8\xd0\xba",'Bulgarian');
        self::addLanguageToArrays('MY', "my", "mya", "bur", "Burmese", "\xe1\x80\x97\xe1\x80\x99\xe1\x80\xac\xe1\x80\x85\xe1\x80\xac",'Burmese');
        self::addLanguageToArrays('CA', "ca", "cat", "cat", "Catalan", "catal\xc3\xa0",'Catalan');
        self::addLanguageToArrays('CH', "ch", "cha", "cha", "Chamorro", "Chamoru",'Chamorro');//not  yet defined in kaltura
        self::addLanguageToArrays('CE', "ce", "che", "che", "Chechen", "\xd0\xbd\xd0\xbe\xd1\x85\xd1\x87\xd0\xb8\xd0\xb9\xd0\xbd \xd0\xbc\xd0\xbe\xd1\x82\xd1\x82",'Chechen');//not  yet defined in kaltura
        self::addLanguageToArrays('NY', "ny", "nya", "nya", "Chichewa, Chewa, Nyanja", "chiChe\xc5\xb5",'Chichewa'); //not  yet defined in kaltura
        self::addLanguageToArrays('ZH', "zh", "zho", "chi", "Chinese", "\xe4\xb8\xad\xe6\x96\x87 (Zh\xc5\x8dngw\xc3\xa9n), \xe6\xb1\x89\xe8\xaf\xad, \xe6\xbc\xa2\xe8\xaa\x9e",'Chinese');
        self::addLanguageToArrays('CV', "cv", "chv", "chv", "Chuvash", "\xd1\x87\xd3\x91\xd0\xb2\xd0\xb0\xd1\x88 \xd1\x87\xd3\x97\xd0\xbb\xd1\x85\xd0\xb8",'Chuvash');//not  yet defined in kaltura
        self::addLanguageToArrays('KW', "kw", "cor", "cor", "Cornish", "Kernewek",'Cornish');//not  yet defined in kaltura
        self::addLanguageToArrays('CO', "co", "cos", "cos", "Corsican", "corsu, lingua corsa",'Corsican');
        self::addLanguageToArrays('CR', "cr", "cre", "cre", "Cree", "\xe1\x93\x80\xe1\x90\xa6\xe1\x90\x83\xe1\x94\xad\xe1\x90\x8d\xe1\x90\x8f\xe1\x90\xa3",'Cree');//not  yet defined in kaltura
        self::addLanguageToArrays('HR', "hr", "hrv", "hrv", "Croatian", "hrvatski jezik",'Croatian');
        self::addLanguageToArrays('CS', "cs", "ces", "cze", "Czech", "\xc4\x8d" ,'Czech');//here
        self::addLanguageToArrays('DA', "da", "dan", "dan", "Danish", "dansk",'Danish');
        self::addLanguageToArrays('DV', "dv", "div", "div", "Divehi, Dhivehi, Maldivian", "\xde\x8b\xde\xa8\xde\x88\xde\xac\xde\x80\xde\xa8",'Divehi, Dhivehi, Maldivian');//not  yet defined in kaltura
        self::addLanguageToArrays('NL', "nl", "nld", "dut", "Dutch", "Nederlands, Vlaams",'Dutch');
        self::addLanguageToArrays('DZ', "dz", "dzo", "dzo", "Dzongkha", "\xe0\xbd\xa2\xe0\xbe\xab\xe0\xbd\xbc\xe0\xbd\x84\xe0\xbc\x8b\xe0\xbd\x81",'Bhutani');
        self::addLanguageToArrays('EN', "en", "eng", "eng", "English", "English",'English');
        self::addLanguageToArrays('EO', "eo", "epo", "epo", "Esperanto", "Esperanto",'Esperanto');
        self::addLanguageToArrays('ET', "et", "est", "est", "Estonian", "eesti, eesti keel",'Estonian');
        self::addLanguageToArrays('EE', "ee", "ewe", "ewe", "Ewe", "E\xca\x8b",'Ewe'); //not  yet defined in kaltura
        self::addLanguageToArrays('FO', "fo", "fao", "fao", "Faroese", "f\xc3\xb8royskt",'Faeroese');
        self::addLanguageToArrays('FJ', "fj", "fij", "fij", "Fijian", "vosa Vakaviti",'Fiji');
        self::addLanguageToArrays('FI', "fi", "fin", "fin", "Finnish", "suomi, suomen kieli",'Finnish');
        self::addLanguageToArrays('FR', "fr", "fra", "fre", "French", "fran\xc3\xa7",'French');
        self::addLanguageToArrays('FF', "ff", "ful", "ful", "Fula, Fulah, Pulaar, Pular", "Fulfulde, Pulaar, Pular",'Fula, Fulah, Pulaar, Pular');//not  yet defined in kaltura
        self::addLanguageToArrays('GL', "gl", "glg", "glg", "Galician", "galego",'Galician');
        self::addLanguageToArrays('KA', "ka", "kat", "geo", "Georgian", "\xe1\x83\xa5\xe1\x83\x90\xe1\x83\xa0\xe1\x83\x97\xe1\x83\xa3\xe1\x83\x9a\xe1\x83\x98",'Georgian');
        self::addLanguageToArrays('DE', "de", "deu", "ger", "German", "Deutsch",'German');
        self::addLanguageToArrays('EL', "el", "ell", "gre", "Greek (modern)", "\xce\xb5\xce\xbb\xce\xbb\xce\xb7\xce\xbd\xce\xb9\xce\xba\xce\xac",'Greek');
        self::addLanguageToArrays('GN', "gn", "grn", "grn", "Guaran\xc3\xad", "Ava\xc3\xb1",'Guarani');
        self::addLanguageToArrays('GU', "gu", "guj", "guj", "Gujarati", "\xe0\xaa\x97\xe0\xab\x81\xe0\xaa\x9c\xe0\xaa\xb0\xe0\xaa\xbe\xe0\xaa\xa4\xe0\xab\x80",'Gujarati');
        self::addLanguageToArrays('HT', "ht", "hat", "hat", "Haitian, Haitian Creole", "Krey\xc3\xb2l ayisyen",'Haitian, Haitian Creole');//not  yet defined in kaltura
        self::addLanguageToArrays('HA', "ha", "hau", "hau", "Hausa", "(Hausa) \xd9\x87\xd9\x8e\xd9\x88\xd9\x8f\xd8\xb3\xd9\x8e",'Hausa');
        self::addLanguageToArrays('HE', "he", "heb", "heb", "Hebrew (modern)", "\xd7\xa2\xd7\x91\xd7\xa8\xd7\x99\xd7\xaa",'Hebrew');
        self::addLanguageToArrays('HZ', "hz", "her", "her", "Herero", "Otjiherero",'Herero');//not  yet defined in kaltura
        self::addLanguageToArrays('HI', "hi", "hin", "hin", "Hindi", "\xe0\xa4\xb9\xe0\xa4\xbf\xe0\xa4\xa8\xe0\xa5\x8d\xe0\xa4\xa6\xe0\xa5\x80, \xe0\xa4\xb9\xe0\xa4\xbf\xe0\xa4\x82\xe0\xa4\xa6\xe0\xa5\x80",'Hindi');
        self::addLanguageToArrays('HO', "ho", "hmo", "hmo", "Hiri Motu", "Hiri Motu",'Hiri Motu');//not  yet defined in kaltura
        self::addLanguageToArrays('HU', "hu", "hun", "hun", "Hungarian", "magyar",'Hungarian');
        self::addLanguageToArrays('IA', "ia", "ina", "ina", "Interlingua", "Interlingua",'Interlingua');
        self::addLanguageToArrays('ID', "id", "ind", "ind", "Indonesian", "Bahasa Indonesia",'Indonesian');
        self::addLanguageToArrays('IE', "ie", "ile", "ile", "Interlingue", "Originally called Occidental; then Interlingue after WWII",'Interlingue');
        self::addLanguageToArrays('GA', "ga", "gle", "gle", "Irish", "Gaeilge",'Irish');
        self::addLanguageToArrays('IG', "ig", "ibo", "ibo", "Igbo", "As\xe1\xbb\xa5s\xe1\xbb\xa5 Igbo",'Igbo');//not  yet defined in kaltura
        self::addLanguageToArrays('IK', "ik", "ipk", "ipk", "Inupiaq", "I\xc3\xb1upiaq, I\xc3\xb1upiatun",'Inupiak');
        self::addLanguageToArrays('IO', "io", "ido", "ido", "Ido", "Ido",'Ido');//not  yet defined in kaltura  - remove duplicate
        self::addLanguageToArrays('IS', "is", "isl", "ice", "Icelandic", "\xc3\x8dslenska",'Icelandic');
        self::addLanguageToArrays('IT', "it", "ita", "ita", "Italian", "italiano",'Italian');
        self::addLanguageToArrays('IU', "iu", "iku", "iku", "Inuktitut", "\xe1\x90\x83\xe1\x93\x84\xe1\x92\x83\xe1\x91\x8e\xe1\x91\x90\xe1\x91\xa6",'Inuktitut');
        self::addLanguageToArrays('JA', "ja", "jpn", "jpn", "Japanese", "\xe6\x97\xa5\xe6\x9c\xac\xe8\xaa\x9e (\xe3\x81\xab\xe3\x81\xbb\xe3\x82\x93\xe3\x81\x94)",'Japanese');
        self::addLanguageToArrays('JV', "jv", "jav", "jav", "Javanese", "\xea\xa6\xa7\xea\xa6\xb1\xea\xa6\x97\xea\xa6\xae",'Javanese');
        self::addLanguageToArrays('KL', "kl", "kal", "kal", "Kalaallisut, Greenlandic", "kalaallisut, kalaallit oqaasii",'Greenlandic');
        self::addLanguageToArrays('KN', "kn", "kan", "kan", "Kannada", "\xe0\xb2\x95\xe0\xb2\xa8\xe0\xb3\x8d\xe0\xb2\xa8\xe0\xb2\xa1",'Kannada');
        self::addLanguageToArrays('KR', "kr", "kau", "kau", "Kanuri", "Kanuri",'Kanuri');//not  yet defined in kaltura
        self::addLanguageToArrays('KS', "ks", "kas", "kas", "Kashmiri", "\xe0\xa4\x95\xe0\xa4\xb6\xe0\xa5\x8d\xe0\xa4\xae\xe0\xa5\x80\xe0\xa4\xb0\xe0\xa5\x80, \xd9\x83\xd8\xb4\xd9\x85\xd9\x8a\xd8\xb1\xd9\x8a\xe2\x80\x8e",'Kashmiri');
        self::addLanguageToArrays('KK', "kk", "kaz", "kaz", "Kazakh", "\xd2\x9b\xd0\xb0\xd0\xb7\xd0\xb0\xd2\x9b \xd1\x82\xd1\x96\xd0\xbb\xd1\x96",'Kazakh');
        self::addLanguageToArrays('KM', "km", "khm", "khm", "Khmer", "\xe1\x9e\x81\xe1\x9f\x92\xe1\x9e\x98\xe1\x9f\x82\xe1\x9e\x9a, \xe1\x9e\x81\xe1\x9f\x81\xe1\x9e\x98\xe1\x9e\x9a\xe1\x9e\x97\xe1\x9e\xb6\xe1\x9e\x9f\xe1\x9e\xb6, \xe1\x9e\x97\xe1\x9e\xb6\xe1\x9e\x9f\xe1\x9e\xb6\xe1\x9e\x81\xe1\x9f\x92\xe1\x9e\x98\xe1\x9f\x82\xe1\x9e\x9a",'Cambodian');
        self::addLanguageToArrays('KI', "ki", "kik", "kik", "Kikuyu, Gikuyu", "G\xc4\xa9k\xc5\xa9y\xc5\xa9",'Kikuyu, Gikuyu');//not  yet defined in kaltura
        self::addLanguageToArrays('RW', "rw", "kin", "kin", "Kinyarwanda", "Ikinyarwanda",'Kinyarwanda (Ruanda)');
        self::addLanguageToArrays('KY', "ky", "kir", "kir", "Kyrgyz", "\xd0\x9a\xd1\x8b\xd1\x80\xd0\xb3\xd1\x8b\xd0\xb7\xd1\x87\xd0\xb0, \xd0\x9a\xd1\x8b\xd1\x80\xd0\xb3\xd1\x8b\xd0\xb7 \xd1\x82\xd0\xb8\xd0\xbb\xd0\xb8",'Kirghiz');
        self::addLanguageToArrays('KV', "kv", "kom", "kom", "Komi", "\xd0\xba\xd0\xbe\xd0\xbc\xd0\xb8 \xd0\xba\xd1\x8b\xd0\xb2",'Komi');//not  yet defined in kaltura
        self::addLanguageToArrays('KG', "kg", "kon", "kon", "Kongo", "Kikongo",'Kongo');//not  yet defined in kaltura
        self::addLanguageToArrays('KO', "ko", "kor", "kor", "Korean", "\xed\x95\x9c\xea\xb5\xad\xec\x96\xb4, \xec\xa1\xb0\xec\x84\xa0\xec\x96\xb4",'Korean');
        self::addLanguageToArrays('KU', "ku", "kur", "kur", "Kurdish", "Kurd\xc3\xae, \xd9\x83\xd9\x88\xd8\xb1\xd8\xaf\xdb\x8c\xe2\x80\x8e",'Kurdish');
        self::addLanguageToArrays('KJ', "kj", "kua", "kua", "Kwanyama, Kuanyama", "Kuanyama".'Kwanyama, Kuanyama','Kwanyama, Kuanyama');//not  yet defined in kaltura
        self::addLanguageToArrays('LA', "la", "lat", "lat", "Latin", "latine, lingua latina",'Latin');
        self::addLanguageToArrays('LB', "lb", "ltz", "ltz", "Luxembourgish, Letzeburgesch", "L\xc3\xabtzebuergesch",'Luxembourgish, Letzeburgesch');//not  yet defined in kaltura
        self::addLanguageToArrays('LG', "lg", "lug", "lug", "Ganda", "Luganda",'Ganda');//not  yet defined in kaltura
        self::addLanguageToArrays('LI', "li", "lim", "lim", "Limburgish, Limburgan, Limburger", "Limburgs",'Limburgish ( Limburger)');
        self::addLanguageToArrays('LN', "ln", "lin", "lin", "Lingala", "Ling\xc3\xa1la",'Lingala');
        self::addLanguageToArrays('LO', "lo", "lao", "lao", "Lao", "\xe0\xba\x9e\xe0\xba\xb2\xe0\xba\xaa\xe0\xba\xb2\xe0\xba\xa5\xe0\xba\xb2\xe0\xba\xa7",'Laothian');
        self::addLanguageToArrays('LT', "lt", "lit", "lit", "Lithuanian", "lietuvi\xc5\xb3 kalba",'Lithuanian');
        self::addLanguageToArrays('LU', "lu", "lub", "lub", "Luba-Katanga", "Tshiluba",'Luba-Katanga');//not  yet defined in kaltura
        self::addLanguageToArrays('LV', "lv", "lav", "lav", "Latvian", "latvie\xc5\xa1u valoda",'Latvian (Lettish)');
        self::addLanguageToArrays('GV', "gv", "glv", "glv", "Manx", "Gaelg, Gailck",'Gaelic (Manx)');
        self::addLanguageToArrays('MK', "mk", "mkd", "mac", "Macedonian", "\xd0\xbc\xd0\xb0\xd0\xba\xd0\xb5\xd0\xb4\xd0\xbe\xd0\xbd\xd1\x81\xd0\xba\xd0\xb8 \xd1\x98\xd0\xb0\xd0\xb7\xd0\xb8\xd0\xba",'Macedonian');
        self::addLanguageToArrays('MG', "mg", "mlg", "mlg", "Malagasy", "fiteny malagasy",'Malagasy');
        self::addLanguageToArrays('MS', "ms", "msa", "may", "Malay", "bahasa Melayu, \xd8\xa8\xd9\x87\xd8\xa7\xd8\xb3 \xd9\x85\xd9\x84\xd8\xa7\xd9\x8a\xd9\x88\xe2\x80\x8e",'Malay');
        self::addLanguageToArrays('ML', "ml", "mal", "mal", "Malayalam", "\xe0\xb4\xae\xe0\xb4\xb2\xe0\xb4\xaf\xe0\xb4\xbe\xe0\xb4\xb3\xe0\xb4\x82",'Malayalam');
        self::addLanguageToArrays('MT', "mt", "mlt", "mlt", "Maltese", "Malti",'Maltese');
        self::addLanguageToArrays('MI', "mi", "mri", "mao", "M\xc4\x81ori", "te reo M\xc4\x81ori",'Maori');
        self::addLanguageToArrays('MR', "mr", "mar", "mar", "Marathi (Mar\xc4\x81\xe1\xb9\xadh\xc4\xab)", "\xe0\xa4\xae\xe0\xa4\xb0\xe0\xa4\xbe\xe0\xa4\xa0\xe0\xa5\x80",'Marathi');
        self::addLanguageToArrays('MH', "mh", "mah", "mah", "Marshallese", "Kajin M\xcc\xa7",'Marshallese');//not  yet defined in kaltura
        self::addLanguageToArrays('MN', "mn", "mon", "mon", "Mongolian", "\xd0\x9c\xd0\xbe\xd0\xbd\xd0\xb3\xd0\xbe\xd0\xbb \xd1\x85\xd1\x8d\xd0\xbb",'Mongolian');
        self::addLanguageToArrays('NA', "na", "nau", "nau", "Nauruan", "Dorerin Naoero",'Nauru');
        self::addLanguageToArrays('NV', "nv", "nav", "nav", "Navajo, Navaho", "Din\xc3\xa9 bizaad",'Navajo, Navaho');//not  yet defined in kaltura
        self::addLanguageToArrays('ND', "nd", "nde", "nde", "Northern Ndebele", "isiNdebele",'Northern Ndebele');//not  yet defined in kaltura
        self::addLanguageToArrays('NE', "ne", "nep", "nep", "Nepali", "\xe0\xa4\xa8\xe0\xa5\x87\xe0\xa4\xaa\xe0\xa4\xbe\xe0\xa4\xb2\xe0\xa5\x80",'Nepali');
        self::addLanguageToArrays('NG', "ng", "ndo", "ndo", "Ndonga", "Owambo",'Ndonga');//not  yet defined in kaltura
        self::addLanguageToArrays('NB', "nb", "nob", "nob", "Norwegian Bokm\xc3\xa5l", "Norsk bokm\xc3\xa5l","Norwegian Bokm\xc3\xa5l");//not  yet defined in kaltura
        self::addLanguageToArrays('NN', "nn", "nno", "nno", "Norwegian Nynorsk", "Norsk nynorsk",'Norwegian Nynorsk');//not  yet defined in kaltura
        self::addLanguageToArrays('NO', "no", "nor", "nor", "Norwegian", "Norsk",'Norwegian');
        self::addLanguageToArrays('II', "ii", "iii", "iii", "Nuosu", "\xea\x86\x88\xea\x8c\xa0\xea\x92\xbf Nuosuhxop",'Nuosu');//not  yet defined in kaltura
        self::addLanguageToArrays('NR', "nr", "nbl", "nbl", "Southern Ndebele", "isiNdebele",'Southern Ndebele');//not  yet defined in kaltura
        self::addLanguageToArrays('OC', "oc", "oci", "oci", "Occitan", "occitan, lenga d'\xc3\xb2",'Occitan');
        self::addLanguageToArrays('OJ', "oj", "oji", "oji", "Ojibwe, Ojibwa", "\xe1\x90\x8a\xe1\x93\x82\xe1\x94\x91\xe1\x93\x88\xe1\x90\xaf\xe1\x92\xa7\xe1\x90\x8e\xe1\x93\x90",'Ojibwe, Ojibwa');//not  yet defined in kaltura
        self::addLanguageToArrays('CU', "cu", "chu", "chu", "Old Church Slavonic, Church Slavonic, Old Bulgarian", "\xd1\xa9\xd0\xb7\xd1\x8b\xd0\xba\xd1\x8a \xd1\x81\xd0\xbb\xd0\xbe\xd0\xb2\xd1\xa3\xd0\xbd\xd1\x8c\xd1\x81\xd0\xba\xd1\x8a",'Old Church Slavonic, Church Slavonic, Old Bulgarian');//not  yet defined in kaltura
        self::addLanguageToArrays('OM', "om", "orm", "orm", "Oromo", "Afaan Oromoo",'Oromo (Afan, Galla)');
        self::addLanguageToArrays('OR_', "or", "ori", "ori", "Oriya", "\xe0\xac\x93\xe0\xac\xa1\xe0\xac\xbc\xe0\xac\xbf\xe0\xac\x86",'Oriya');
        self::addLanguageToArrays('OS', "os", "oss", "oss", "Ossetian, Ossetic", "\xd0\xb8\xd1\x80\xd0\xbe\xd0\xbd \xc3\xa6\xd0\xb2\xd0\xb7\xd0\xb0\xd0\xb3",'Ossetian, Ossetic');//not  yet defined in kaltura
        self::addLanguageToArrays('PA', "pa", "pan", "pan", "Panjabi, Punjabi", "\xe0\xa8\xaa\xe0\xa9\xb0\xe0\xa8\x9c\xe0\xa8\xbe\xe0\xa8\xac\xe0\xa9\x80, \xd9\xbe\xd9\x86\xd8\xac\xd8\xa7\xd8\xa8\xdb\x8c\xe2\x80\x8e",'Punjabi');
        self::addLanguageToArrays('PI', "pi", "pli", "pli", "P\xc4\x81li", "\xe0\xa4\xaa\xe0\xa4\xbe\xe0\xa4\xb4\xe0\xa4\xbf","P\xc4\x81li");//not  yet defined in kaltura
        self::addLanguageToArrays('FA', "fa", "fas", "per", "Persian (Farsi)", "\xd9\x81\xd8\xa7\xd8\xb1\xd8\xb3\xdb\x8c",'Farsi');
        self::addLanguageToArrays('PL', "pl", "pol", "pol", "Polish", "j\xc4\x99zyk polski, polszczyzna",'Polish');
        self::addLanguageToArrays('PS', "ps", "pus", "pus", "Pashto, Pushto", "\xd9\xbe\xda\x9a\xd8\xaa\xd9\x88",'Pashto (Pushto)');
        self::addLanguageToArrays('PT', "pt", "por", "por", "Portuguese", "portugu\xc3\xaas",'Portuguese');
        self::addLanguageToArrays('QU', "qu", "que", "que", "Quechua", "Runa Simi, Kichwa",'Quechua');
        self::addLanguageToArrays('RM', "rm", "roh", "roh", "Romansh", "rumantsch grischun",'Rhaeto-Romance');
        self::addLanguageToArrays('RN', "rn", "run", "run", "Kirundi", "Ikirundi",'Kirundi (Rundi)');
        self::addLanguageToArrays('RO', "ro", "ron", "rum", "Romanian", "limba rom\xc3\xa2n\xc4\x83",'Romanian');
        self::addLanguageToArrays('RU', "ru", "rus", "rus", "Russian", "\xd0\xa0\xd1\x83\xd1\x81\xd1\x81\xd0\xba\xd0\xb8\xd0\xb9",'Russian');
        self::addLanguageToArrays('SA', "sa", "san", "san", "Sanskrit (Sa\xe1\xb9\x81sk\xe1\xb9\x9bta)", "\xe0\xa4\xb8\xe0\xa4\x82\xe0\xa4\xb8\xe0\xa5\x8d\xe0\xa4\x95\xe0\xa5\x83\xe0\xa4\xa4\xe0\xa4\xae\xe0\xa5\x8d",'Sanskrit');
        self::addLanguageToArrays('SC', "sc", "srd", "srd", "Sardinian", "sardu",'Sardinian');//not  yet defined in kaltura
        self::addLanguageToArrays('SD', "sd", "snd", "snd", "Sindhi", "\xe0\xa4\xb8\xe0\xa4\xbf\xe0\xa4\xa8\xe0\xa5\x8d\xe0\xa4\xa7\xe0\xa5\x80, \xd8\xb3\xd9\x86\xda\x8c\xd9\x8a\xd8\x8c \xd8\xb3\xd9\x86\xd8\xaf\xda\xbe\xdb\x8c\xe2\x80\x8e",'Sindhi');
        self::addLanguageToArrays('SE', "se", "sme", "sme", "Northern Sami", "Davvis\xc3\xa1megiella",'Northern Sami');//not  yet defined in kaltura
        self::addLanguageToArrays('SM', "sm", "smo", "smo", "Samoan", "gagana fa'a Samoa",'Samoan');
        self::addLanguageToArrays('SG', "sg", "sag", "sag", "Sango", "y\xc3\xa2ng\xc3\xa2 t\xc3\xae s\xc3\xa4ng\xc3\xb6",'Sangro');
        self::addLanguageToArrays('SR', "sr", "srp", "srp", "Serbian", "\xd1\x81\xd1\x80\xd0\xbf\xd1\x81\xd0\xba\xd0\xb8 \xd1\x98\xd0\xb5\xd0\xb7\xd0\xb8\xd0\xba",'Serbian');
        self::addLanguageToArrays('GD', "gd", "gla", "gla", "Scottish Gaelic, Gaelic", "G\xc3\xa0idhlig",'Gaelic (Scottish)');
        self::addLanguageToArrays('SN', "sn", "sna", "sna", "Shona", "chiShona",'Shona');
        self::addLanguageToArrays('SI', "si", "sin", "sin", "Sinhala, Sinhalese", "\xe0\xb7\x83\xe0\xb7\x92\xe0\xb6\x82\xe0\xb7\x84\xe0\xb6\xbd",'Sinhalese');
        self::addLanguageToArrays('SK', "sk", "slk", "slo", "Slovak", "sloven\xc4\x8dina, slovensk\xc3\xbd jazyk",'Slovak');
        self::addLanguageToArrays('SL', "sl", "slv", "slv", "Slovene", "slovenski jezik, sloven\xc5\xa1\xc4\x8dina",'Slovenian');
        self::addLanguageToArrays('SO', "so", "som", "som", "Somali", "Soomaaliga, af Soomaali",'Somali');
        self::addLanguageToArrays('ST', "st", "sot", "sot", "Southern Sotho", "Sesotho",'Sesotho');
        self::addLanguageToArrays('ES', "es", "spa", "spa", "Spanish", "espa\xc3\xb1ol",'Spanish');
        self::addLanguageToArrays('SU', "su", "sun", "sun", "Sundanese", "Basa Sunda",'Sundanese');
        self::addLanguageToArrays('SW', "sw", "swa", "swa", "Swahili", "Kiswahili",'Swahili (Kiswahili)');
        self::addLanguageToArrays('SS', "ss", "ssw", "ssw", "Swati", "SiSwati",'Siswati');
        self::addLanguageToArrays('SV', "sv", "swe", "swe", "Swedish", "svenska",'Swedish');
        self::addLanguageToArrays('TA', "ta", "tam", "tam", "Tamil", "\xe0\xae\xa4\xe0\xae\xae\xe0\xae\xbf\xe0\xae\xb4\xe0\xaf\x8d",'Tamil');
        self::addLanguageToArrays('TE', "te", "tel", "tel", "Telugu", "\xe0\xb0\xa4\xe0\xb1\x86\xe0\xb0\xb2\xe0\xb1\x81\xe0\xb0\x97\xe0\xb1\x81",'Telugu');
        self::addLanguageToArrays('TG', "tg", "tgk", "tgk", "Tajik", "\xd1\x82\xd0\xbe\xd2\xb7\xd0\xb8\xd0\xba\xd3\xa3, to\xc3\xa7ik\xc4\xab, \xd8\xaa\xd8\xa7\xd8\xac\xdb\x8c\xda\xa9\xdb\x8c\xe2\x80\x8e",'Tajik');
        self::addLanguageToArrays('TH', "th", "tha", "tha", "Thai", "\xe0\xb9\x84\xe0\xb8\x97\xe0\xb8\xa2",'Thai');
        self::addLanguageToArrays('TI', "ti", "tir", "tir", "Tigrinya", "\xe1\x89\xb5\xe1\x8c\x8d\xe1\x88\xad\xe1\x8a\x9b",'Tigrinya');
        self::addLanguageToArrays('BO', "bo", "bod", "tib", "Tibetan Standard, Tibetan, Central", "\xe0\xbd\x96\xe0\xbd\xbc\xe0\xbd\x91\xe0\xbc\x8b\xe0\xbd\xa1\xe0\xbd\xb2\xe0\xbd\x82",'Tibetan');
        self::addLanguageToArrays('TK', "tk", "tuk", "tuk", "Turkmen", "T\xc3\xbcrkmen, \xd0\xa2\xd2\xaf\xd1\x80\xd0\xba\xd0\xbc\xd0\xb5\xd0\xbd",'Turkmen');
        self::addLanguageToArrays('TL', "tl", "tgl", "tgl", "Tagalog", "Wikang Tagalog",'Tagalog');
        self::addLanguageToArrays('TN', "tn", "tsn", "tsn", "Tswana", "Setswana",'Setswana');
        self::addLanguageToArrays('TO', "to", "ton", "ton", "Tonga (Tonga Islands)", "faka Tonga",'Tonga');
        self::addLanguageToArrays('TR', "tr", "tur", "tur", "Turkish", "T\xc3\xbcrk\xc3\xa7",'Turkish');
        self::addLanguageToArrays('TS', "ts", "tso", "tso", "Tsonga", "Xitsonga",'Tsonga');
        self::addLanguageToArrays('TT', "tt", "tat", "tat", "Tatar", "\xd1\x82\xd0\xb0\xd1\x82\xd0\xb0\xd1\x80 \xd1\x82\xd0\xb5\xd0\xbb\xd0\xb5, tatar tele",'Tatar');
        self::addLanguageToArrays('TW', "tw", "twi", "twi", "Twi", "Twi",'Twi');
        self::addLanguageToArrays('TY', "ty", "tah", "tah", "Tahitian", "Reo Tahiti",'Tahitian');//not  yet defined in kaltura
        self::addLanguageToArrays('UG', "ug", "uig", "uig", "Uyghur", "\xd8\xa6\xdb\x87\xd9\x8a\xd8\xba\xdb\x87\xd8\xb1\xda\x86\xdb\x95\xe2\x80\x8e, Uyghurche",'Uighur');
        self::addLanguageToArrays('UK', "uk", "ukr", "ukr", "Ukrainian", "\xd0\xa3\xd0\xba\xd1\x80\xd0\xb0\xd1\x97\xd0\xbd\xd1\x81\xd1\x8c\xd0\xba\xd0\xb0",'Ukrainian');
        self::addLanguageToArrays('UR', "ur", "urd", "urd", "Urdu", "\xd8\xa7\xd8\xb1\xd8\xaf\xd9\x88",'Urdu');
        self::addLanguageToArrays('UZ', "uz", "uzb", "uzb", "Uzbek", "O\xca\xbbzbek, \xd0\x8e\xd0\xb7\xd0\xb1\xd0\xb5\xd0\xba, \xd8\xa3\xdb\x87\xd8\xb2\xd8\xa8\xdb\x90\xd9\x83\xe2\x80\x8e",'Uzbek');
        self::addLanguageToArrays('VE', "ve", "ven", "ven", "Venda", "Tshiven\xe1\xb8\x93",'Venda');//not  yet defined in kaltura
        self::addLanguageToArrays('VI', "vi", "vie", "vie", "Vietnamese", "Ti\xe1\xba\xbfng Vi\xe1\xbb\x87t",'Vietnamese');
        self::addLanguageToArrays('VO', "vo", "vol", "vol", "Volap\xc3\xbck", "Volap\xc3\xbck",'Volapuk');
        self::addLanguageToArrays('WA', "wa", "wln", "wln", "Walloon", "walon",'Walloon');//not  yet defined in kaltura
        self::addLanguageToArrays('CY', "cy", "cym", "wel", "Welsh", "Cymraeg",'Welsh');
        self::addLanguageToArrays('WO', "wo", "wol", "wol", "Wolof", "Wollof",'Wolof');
        self::addLanguageToArrays('FY', "fy", "fry", "fry", "Western Frisian", "Frysk",'Frisian');
        self::addLanguageToArrays('XH', "xh", "xho", "xho", "Xhosa", "isiXhosa",'Xhosa');
        self::addLanguageToArrays('YI', "yi", "yid", "yid", "Yiddish", "\xd7\x99\xd7\x99\xd6\xb4\xd7\x93\xd7\x99\xd7\xa9",'Yiddish');
        self::addLanguageToArrays('YO', "yo", "yor", "yor", "Yoruba", "Yor\xc3\xb9",'Yoruba');
        self::addLanguageToArrays('ZA', "za", "zha", "zha", "Zhuang, Chuang", "Sa\xc9\xaf cue\xc5\x8b\xc6\x85, Saw cuengh",'Zhuang, Chuang');//not  yet defined in kaltura
        self::addLanguageToArrays('ZU', "zu", "zul", "zul", "Zulu", "isiZulu",'Zulu');

        //The following cases are not part of language ISO , they are added to support backward compatibility
        self::addLanguageToArrays('SH','sh','shc','shc','Serbo-Croatian','Serbo-Croatian' ,'Serbo-Croatian');// i made that up;
        self::addLanguageToArrays('EN_GB', "en", "enb", "enb", "English (British)", "English (British)",'English (British)');
        self::addLanguageToArrays('EN_US', "en", "enu", "enu", "English (American)", "English (American)",'English (American)');
        self::addLanguageToArrays('MO','mo','mol','mol','Moldavian','Moldavian','Moldavian'); //i made that up
        self::addLanguageToArrays('MU','mu','mul','mul','Multilingual','Multilingual','Multilingual'); //i made that up
        self::addLanguageToArrays('UN', "un", "und", "und", "Undefined", "Undefined","Undefined"); //i made up some of this

    }

    public function getLanguageObjectFromTwoCode($codeUppercase)
    {
        return self::$arrayTwoCodeUppercaseKey[$codeUppercase];
    }

    public function getLanguageObjectFromThreeCode($codeT)
    {
        return $this->getLanguageObjectFromTwoCode(self::$arrayThreeCodeTKeyToTwoCodeUppercase[$codeT]);
    }

    public function getLanguageObjectFromKalturaLanguageName($kalturaName)
    {
        return $this->getLanguageObjectFromTwoCode(self::$arrayKalturaLanguageNameKeyToTwoCodeUppercase[$kalturaName]);
    }

    public function getTwoCodeFromKalturaName($kalturaName)
    {
        return self::$arrayKalturaLanguageNameKeyToTwoCodeUppercase[$kalturaName];
    }

    /**
     * @param $language - the language to search
     * @return the 2 code key or false if not known
     */
    public function isKnownLanguage($language)
    {
        $arrayKeys =  array_keys(self::$arrayTwoCodeUppercaseKey);
        $val = array_search(strtolower($language), array_map('strtolower',$arrayKeys));
        if($val)
            return $arrayKeys[$val];

        foreach (self::$arrayTwoCodeUppercaseKey as $key => $value)
        {
           $ret = array_search(strtolower($language), array_map('strtolower', $value));
            if($ret)
                return $key;
        }
        return false;
    }

}