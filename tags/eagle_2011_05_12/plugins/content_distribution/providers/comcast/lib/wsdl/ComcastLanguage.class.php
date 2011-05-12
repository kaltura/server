<?php


class ComcastLanguage extends SoapObject
{				
	const _CUNKNOWND = 'CUnknownD';
					
	const _AFRIKAANS = 'Afrikaans';
					
	const _ALBANIAN = 'Albanian';
					
	const _AMHARIC = 'Amharic';
					
	const _ARABIC = 'Arabic';
					
	const _ARMENIAN = 'Armenian';
					
	const _ASSAMESE = 'Assamese';
					
	const _AZERI = 'Azeri';
					
	const _BASQUE = 'Basque';
					
	const _BELARUSIAN = 'Belarusian';
					
	const _BENGALI = 'Bengali';
					
	const _BULGARIAN = 'Bulgarian';
					
	const _BURMESE = 'Burmese';
					
	const _CANTONESE = 'Cantonese';
					
	const _CATALAN = 'Catalan';
					
	const _CHEROKEE = 'Cherokee';
					
	const _CHINESE = 'Chinese';
					
	const _CROATIAN = 'Croatian';
					
	const _CZECH = 'Czech';
					
	const _DANISH = 'Danish';
					
	const _DIVEHI = 'Divehi';
					
	const _DUTCH = 'Dutch';
					
	const _DZONGKHA = 'Dzongkha';
					
	const _EDO = 'Edo';
					
	const _ENGLISH = 'English';
					
	const _ESTONIAN = 'Estonian';
					
	const _FYRO_MACEDONIAN = 'FYRO Macedonian';
					
	const _FAEROESE = 'Faeroese';
					
	const _FARSI = 'Farsi';
					
	const _FILIPINO = 'Filipino';
					
	const _FINNISH = 'Finnish';
					
	const _FRENCH = 'French';
					
	const _FRISIAN = 'Frisian';
					
	const _FULFULDE = 'Fulfulde';
					
	const _GAELIC = 'Gaelic';
					
	const _GALICIAN = 'Galician';
					
	const _GEORGIAN = 'Georgian';
					
	const _GERMAN = 'German';
					
	const _GREEK = 'Greek';
					
	const _GUARANI = 'Guarani';
					
	const _GUJARATI = 'Gujarati';
					
	const _HAUSA = 'Hausa';
					
	const _HAWAIIAN = 'Hawaiian';
					
	const _HEBREW = 'Hebrew';
					
	const _HINDI = 'Hindi';
					
	const _HUNGARIAN = 'Hungarian';
					
	const _IBIBIO = 'Ibibio';
					
	const _ICELANDIC = 'Icelandic';
					
	const _IGBO = 'Igbo';
					
	const _INDONESIAN = 'Indonesian';
					
	const _INUKTITUT = 'Inuktitut';
					
	const _INUPIAK = 'Inupiak';
					
	const _ITALIAN = 'Italian';
					
	const _JAPANESE = 'Japanese';
					
	const _KANNADA = 'Kannada';
					
	const _KANURI = 'Kanuri';
					
	const _KASHMIRI = 'Kashmiri';
					
	const _KAZAKH = 'Kazakh';
					
	const _KHMER = 'Khmer';
					
	const _KONKANI = 'Konkani';
					
	const _KOREAN = 'Korean';
					
	const _KYRGYZ = 'Kyrgyz';
					
	const _LAO = 'Lao';
					
	const _LATIN = 'Latin';
					
	const _LATVIAN = 'Latvian';
					
	const _LITHUANIAN = 'Lithuanian';
					
	const _MALAGASY = 'Malagasy';
					
	const _MALAY = 'Malay';
					
	const _MALAYALAM = 'Malayalam';
					
	const _MALTESE = 'Maltese';
					
	const _MANDARIN = 'Mandarin';
					
	const _MANIPURI = 'Manipuri';
					
	const _MARATHI = 'Marathi';
					
	const _MONGOLIAN = 'Mongolian';
					
	const _NEPALI = 'Nepali';
					
	const _NORWEGIAN = 'Norwegian';
					
	const _ORIYA = 'Oriya';
					
	const _OROMO = 'Oromo';
					
	const _PASHTO = 'Pashto';
					
	const _POLISH = 'Polish';
					
	const _PORTUGUESE = 'Portuguese';
					
	const _PUNJABI = 'Punjabi';
					
	const _QUECHUA = 'Quechua';
					
	const _RHAETO_ROMANIC = 'Rhaeto-Romanic';
					
	const _ROMANIAN = 'Romanian';
					
	const _RUSSIAN = 'Russian';
					
	const _SAMI = 'Sami';
					
	const _SANSKRIT = 'Sanskrit';
					
	const _SERBIAN = 'Serbian';
					
	const _SINDHI = 'Sindhi';
					
	const _SINHALESE = 'Sinhalese';
					
	const _SLOVAK = 'Slovak';
					
	const _SLOVENIAN = 'Slovenian';
					
	const _SOMALI = 'Somali';
					
	const _SORBIAN = 'Sorbian';
					
	const _SPANISH = 'Spanish';
					
	const _SUTU = 'Sutu';
					
	const _SWAHILI = 'Swahili';
					
	const _SWEDISH = 'Swedish';
					
	const _SYRIAC = 'Syriac';
					
	const _TAJIK = 'Tajik';
					
	const _TAMAZIGHT = 'Tamazight';
					
	const _TAMIL = 'Tamil';
					
	const _TATAR = 'Tatar';
					
	const _TELUGU = 'Telugu';
					
	const _THAI = 'Thai';
					
	const _TIBETAN = 'Tibetan';
					
	const _TIGRIGNA = 'Tigrigna';
					
	const _TSONGA = 'Tsonga';
					
	const _TSWANA = 'Tswana';
					
	const _TURKISH = 'Turkish';
					
	const _TURKMEN = 'Turkmen';
					
	const _UKRAINIAN = 'Ukrainian';
					
	const _URDU = 'Urdu';
					
	const _UZBEK = 'Uzbek';
					
	const _VENDA = 'Venda';
					
	const _VIETNAMESE = 'Vietnamese';
					
	const _WELSH = 'Welsh';
					
	const _XHOSA = 'Xhosa';
					
	const _YI = 'Yi';
					
	const _YIDDISH = 'Yiddish';
					
	const _YORUBA = 'Yoruba';
					
	const _ZULU = 'Zulu';
					
	const _COTHERD = 'COtherD';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


