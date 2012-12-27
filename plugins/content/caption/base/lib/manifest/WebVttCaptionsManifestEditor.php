<?php
/**
 * @package plugins.caption
 * @subpackage manifest
 *
 */
class WebVttCaptionsManifestEditor extends BaseManifestEditor
{
	/**
	 * Structured array containing captions information
	 * @var array
	 */
	public $captions;
	
	/**
	 * Static map between language format used by the whole system and the captions format used by the M3U8 file
	 * @var array
	 */
	//TODO fill this map
	public static $captionsFormatMap = array (
				'Abkhazian' =>	'abk',
				'Afar' =>	'aar',
				'Afrikaans' =>	'afr',
				'Albanian' =>	'sqi',
				'Amharic' =>	'amh',
				'Arabic' =>	'ara',
				'Armenian' =>	'hye',
				'Assamese' =>	'asm',
				'Aymara' =>	'aym',
				'Azerbaijani' =>	'aze',
				'Bashkir' =>	'bam',
				'Basque' =>	'eus',
				'Bengali (Bangla)' =>	'ben',
				'Bhutani' =>	'dzo',
				'Bislama' =>	'bis',
				'Breton' =>	'bre',
				'Bulgarian' =>	'bul',
				'Burmese' =>	'mya',
				'Byelorussian (Belarusian)' =>	'bel',
				'Cambodian' =>	'khm',
				'Catalan' =>	'cat',
				'Chinese' =>	'zho',
				'Corsican' =>	'cos',
				'Croatian' =>	'hrv',
				'Czech' =>	'ces',
				'Danish' =>	'dan',
				'Dutch' =>	'nld',
				'English' =>	'eng',
				'Esperanto' =>	'epo',
				'Estonian' =>	'est',
				'Faeroese' =>	'fao',
				'Farsi' =>	'fas',
				'Fiji' =>	'fij',
				'Finnish' =>	'fin',
				'French' =>	'fra',
				'Frisian' =>	'fry',
				'Galician' =>	'glg',
				'Gaelic (Scottish)' =>	'gla',
				'Gaelic (Manx)' =>	'glv',
				'Georgian' =>	'kat',
				'German' =>	'deu',
				'Greek' =>	'ell',
				'Greenlandic' =>	'kal',
				'Guarani' =>	'grn',
				'Gujarati' =>	'guj',
				'Hausa' =>	'hau',
				'Hebrew' =>	'heb',
				'Hindi' =>	'hin',
				'Hungarian' =>	'hun',
				'Icelandic' =>	'isl',
				'Indonesian' =>	'ind',
				'Interlingua' =>	'ina',
				'Interlingue' =>	'ile',
				'Inuktitut' =>	'iku',
				'Inupiak' =>	'ipk',
				'Irish' =>	'gle',
				'Italian' =>	'ita',
				'Japanese' =>	'jpn',
				'Javanese' =>	'jav',
				'Kannada' =>	'kan',
				'Kashmiri' =>	'kas',
				'Kazakh' =>	'kaz',
				'Kinyarwanda (Ruanda)' =>	'kin',
				'Kirghiz' =>	'kir',
				'Kirundi (Rundi)' =>	'run',
				'Korean' =>	'kor',
				'Kurdish' =>	'kur',
				'Laothian' =>	'lao',
				'Latin' =>	'lat',
				'Latvian (Lettish)' =>	'lav',
				'Limburgish ( Limburger)' =>	'lim',
				'Lingala' =>	'lin',
				'Lithuanian' =>	'lit',
				'Macedonian' =>	'mkd',
				'Malagasy' =>	'mlg',
				'Malay' =>	'msa',
				'Malayalam' =>	'mal',
				'Maltese' =>	'mlt',
				'Maori' =>	'mri',
				'Marathi' =>	'mar',
				'Mongolian' =>	'mon',
				'Nauru' =>	'nau',
				'Nepali' =>	'nep',
				'Norwegian' =>	'nor',
				'Occitan' =>	'oci',
				'Oriya' =>	'ori',
				'Oromo (Afan, Galla)' =>	'orm',
				'Pashto (Pushto)' =>	'pus',
				'Polish' =>	'pol',
				'Portuguese' =>	'por',
				'Punjabi' =>	'pan',
				'Quechua' =>	'que',
				'Rhaeto-Romance' =>	'roh',
				'Romanian' =>	'ron',
				'Russian' =>	'rus',
				'Samoan' =>	'smo',
				'Sangro' =>	'sag',
				'Sanskrit' =>	'san',
				'Serbian' =>	'srp',
				'Sesotho' =>	'sot',
				'Setswana' =>	'tsn',
				'Shona' =>	'sna',
				'Sindhi' =>	'snd',
				'Sinhalese' =>	'sin',
				'Siswati' =>	'ssw',
				'Slovak' =>	'slk',
				'Slovenian' =>	'slv',
				'Somali' =>	'som',
				'Spanish' =>	'spa',
				'Sundanese' =>	'sun',
				'Swahili (Kiswahili)' =>	'swa',
				'Swedish' =>	'swe',
				'Tagalog' =>	'tgl',
				'Tajik' =>	'tgk',
				'Tamil' =>	'tam',
				'Tatar' =>	'tat',
				'Telugu' =>	'tel',
				'Thai' =>	'tha',
				'Tibetan' =>	'bod',
				'Tigrinya' =>	'tir',
				'Tonga' =>	'ton',
				'Tsonga' =>	'tso',
				'Turkish' =>	'tur',
				'Turkmen' =>	'tuk',
				'Twi' =>	'twi',
				'Uighur' =>	'uig',
				'Ukrainian' =>	'ukr',
				'Urdu' =>	'urd',
				'Uzbek' =>	'uzb',
				'Vietnamese' =>	'vie',
				'Volapuk' =>	'vol',
				'Welsh' =>	'cym',
				'Wolof' =>	'wol',
				'Xhosa' =>	'xho',
				'Yiddish' =>	'yid',
				'Yoruba' =>	'yor',
				'Zulu' =>	'zul',
	
		);
	
	/* (non-PHPdoc)
	 * @see BaseManifestEditor::editManifestHeader()
	 */
	public function editManifestHeader ($manifestHeader)
	{
		foreach ($this->captions as $captionItem)
		{
			$manifestHeader .= "\n";
			$manifestHeader .= '#EXT-X-MEDIA:TYPE=SUBTITLES,GROUP-ID="subs",NAME="' . 
				$captionItem["label"] . '",DEFAULT='.$captionItem["default"] . 
				',AUTOSELECT=YES,FORCED=NO,LANGUAGE="' . self::$captionsFormatMap[$captionItem["language"]] . '",URI="' . $captionItem["url"] . '"';
		}
		
		return $manifestHeader;
	}
	
	/* (non-PHPdoc)
	 * @see BaseManifestEditor::editManifestFooter()
	 */
	public function editManifestFooter ($manifestFooter)
	{
		return $manifestFooter;
	}
	
	/* (non-PHPdoc)
	 * @see BaseManifestEditor::editManifestFlavors()
	 */
	public function editManifestFlavors (array $manifestFlavors)
	{
		if ($this->captions)
		{
			foreach ($manifestFlavors as &$flavor)
			{
				$flavorParts = explode("\n", $flavor);
				$flavorParts[0] .= ',SUBTITLES="subs"';
				$flavor = implode("\n", $flavorParts);
			}
		}
		
		return $manifestFlavors;
	}
}