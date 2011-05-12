<?php


class ComcastEncoding extends SoapObject
{				
	const _ARABIC_CASMO_708D = 'Arabic CASMO 708D';
					
	const _ARABIC_CISOD = 'Arabic CISOD';
					
	const _BALTIC_CISOD = 'Baltic CISOD';
					
	const _BALTIC_CWINDOWSD = 'Baltic CWindowsD';
					
	const _CENTRAL_EUROPEAN_CDOSD = 'Central European CDOSD';
					
	const _CENTRAL_EUROPEAN_CISOD = 'Central European CISOD';
					
	const _CENTRAL_EUROPEAN_CWINDOWSD = 'Central European CWindowsD';
					
	const _CHINESE_SIMPLIFIED_CGB2312D = 'Chinese Simplified CGB2312D';
					
	const _CHINESE_TRADITIONAL = 'Chinese Traditional';
					
	const _CYRILLIC_CDOSD = 'Cyrillic CDOSD';
					
	const _CYRILLIC_CISOD = 'Cyrillic CISOD';
					
	const _CYRILLIC_CKOI8_RD = 'Cyrillic CKOI8-RD';
					
	const _CYRILLIC_CWINDOWSD = 'Cyrillic CWindowsD';
					
	const _GREEK_CISOD = 'Greek CISOD';
					
	const _HEBREW_CDOSD = 'Hebrew CDOSD';
					
	const _HEBREW_CISO_VISUALD = 'Hebrew CISO-VisualD';
					
	const _HEBREW_CWINDOWSD = 'Hebrew CWindowsD';
					
	const _JAPANESE_CEUCD = 'Japanese CEUCD';
					
	const _JAPANESE_CJISD = 'Japanese CJISD';
					
	const _JAPANESE_CJIS_ALLOW_1_BYTE_KANAD = 'Japanese CJIS-Allow 1 byte KanaD';
					
	const _JAPANESE_CSHIFT_JISD = 'Japanese CShift-JISD';
					
	const _KOREAN = 'Korean';
					
	const _LATIN_3_CISOD = 'Latin 3 CISOD';
					
	const _LATIN_9_CISOD = 'Latin 9 CISOD';
					
	const _THAI = 'Thai';
					
	const _TURKISH_CISOD = 'Turkish CISOD';
					
	const _TURKISH_CWINDOWSD = 'Turkish CWindowsD';
					
	const _US_ASCII = 'US-ASCII';
					
	const _UNICODE = 'Unicode';
					
	const _UNICODE_CBIG_ENDIAND = 'Unicode CBig-EndianD';
					
	const _UNICODE_CUTF_8D = 'Unicode CUTF-8D';
					
	const _VIETNAMESE = 'Vietnamese';
					
	const _WESTERN_EUROPEAN_CISOD = 'Western European CISOD';
					
	const _WESTERN_EUROPEAN_CWINDOWSD = 'Western European CWindowsD';
					
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


