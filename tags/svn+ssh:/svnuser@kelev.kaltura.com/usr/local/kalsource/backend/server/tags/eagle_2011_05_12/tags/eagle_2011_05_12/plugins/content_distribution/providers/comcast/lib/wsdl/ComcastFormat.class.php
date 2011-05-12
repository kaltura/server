<?php


class ComcastFormat extends SoapObject
{				
	const _UNKNOWN = 'Unknown';
					
	const _ANY = 'Any';
					
	const _3GPP = '3GPP';
					
	const _3GPP2 = '3GPP2';
					
	const _AAC = 'AAC';
					
	const _ASX = 'ASX';
					
	const _AVI = 'AVI';
					
	const _BMP = 'BMP';
					
	const _CSS = 'CSS';
					
	const _DFXP = 'DFXP';
					
	const _DV = 'DV';
					
	const _EMF = 'EMF';
					
	const _EXE = 'EXE';
					
	const _EXCEL = 'Excel';
					
	const _F4M = 'F4M';
					
	const _FLV = 'FLV';
					
	const _FLX = 'FLX';
					
	const _FLASH = 'Flash';
					
	const _GIF = 'GIF';
					
	const _HTML = 'HTML';
					
	const _ISM = 'ISM';
					
	const _ICON = 'Icon';
					
	const _JPEG = 'JPEG';
					
	const _LXF = 'LXF';
					
	const _M3U = 'M3U';
					
	const _MP3 = 'MP3';
					
	const _MPEG = 'MPEG';
					
	const _MPEG4 = 'MPEG4';
					
	const _MSI = 'MSI';
					
	const _MXF = 'MXF';
					
	const _MOVE = 'Move';
					
	const _OGG = 'Ogg';
					
	const _PDF = 'PDF';
					
	const _PLS = 'PLS';
					
	const _PNG = 'PNG';
					
	const _PPT = 'PPT';
					
	const _QT = 'QT';
					
	const _RAM = 'RAM';
					
	const _REAL = 'Real';
					
	const _SAMI = 'SAMI';
					
	const _SCC = 'SCC';
					
	const _SMIL = 'SMIL';
					
	const _SRT = 'SRT';
					
	const _SCRIPT = 'Script';
					
	const _TIFF = 'TIFF';
					
	const _TEXT = 'Text';
					
	const _VAST = 'VAST';
					
	const _WAV = 'WAV';
					
	const _WM = 'WM';
					
	const _WEBM = 'WebM';
					
	const _WORD = 'Word';
					
	const _XML = 'XML';
					
	const _ZIP = 'Zip';
					
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


