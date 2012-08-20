<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage lib
 */
class SphinxUtils
{
	public static function escapeString($str, $escapeType = SearchIndexFieldEscapeType::DEFAULT_ESCAPE, $iterations = 2)
	{
		if($escapeType == SearchIndexFieldEscapeType::DEFAULT_ESCAPE)
		{
			// NOTE: it appears that sphinx performs double decoding on SELECT values, so we encode twice.
			//		" and ! are escaped once to enable clients to use them, " = exact match, ! = AND NOT
			//	This code could have been implemented more elegantly using array_map, but this implementation is the fastest
			$from 		= array ('\\', 		'"', 		'!',		'(',		')',		'|',		'-',		'@',		'~',		'&',		'/',		'^',		'$',		'=',		'_',		'%', 		'\'',		);
	
			if ($iterations == 2)
			{
				$toDouble   = array ('\\\\', 	'\\"', 		'\\!',		'\\\\\\(',	'\\\\\\)',	'\\\\\\|',	'\\\\\\-',	'\\\\\\@',	'\\\\\\~',	'\\\\\\&',	'\\\\\\/',	'\\\\\\^',	'\\\\\\$',	'\\\\\\=',	'\\\\\\_',	'\\\\\\%',	'\\\\\\\'',	);
				return str_replace($from, $toDouble, $str);
			}
			else
			{
				$toSingle   = array ('\\\\', 	'\\"', 		'\\!',		'\\(',		'\\)',		'\\|',		'\\-',		'\\@',		'\\~',		'\\&',		'\\/',		'\\^',		'\\$',		'\\=',		'\\_',		'\\%',		'\\\'',		);
				return str_replace($from, $toSingle, $str);
			}
		}
		elseif($escapeType == SearchIndexFieldEscapeType::MD5_LOWER_CASE)
		{				
			$str = strtolower($str);
			
			if(substr($str, -2) == '\*')
				return md5(substr($str, 0, strlen($str) - 2)) . '\\\*';
				
			$md5Str = md5($str);	
			KalturaLog::debug('md5(' . $str . ')' . ' = ' . $md5Str );
			
			return $md5Str;
		}
		elseif($escapeType == SearchIndexFieldEscapeType::NO_ESCAPE)
		{
			return $str;
		}
	}
}
