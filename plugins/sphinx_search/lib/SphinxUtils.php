<?php

class SphinxUtils
{
	public static $count = 0;
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
				$toDouble   = array ('\\\\', 	'\\"', 		'\\\\!',		'\\\\\\(',	'\\\\\\)',	'\\\\\\|',	'\\\\\\-',	'\\\\\\@',	'\\\\\\~',	'\\\\\\&',	'\\\\\\/',	'\\\\\\^',	'\\\\\\$',	'\\\\\\=',	'\\\\\\_',	'\\\\\\%',	'\\\\\\\'',	);
				return str_replace($from, $toDouble, $str);
			}
			else
			{
				$toSingle   = array ('\\\\', 	'\\"', 		'\\!',		'\\(',		'\\)',		'\\|',		'\\-',		'\\@',		'\\~',		'\\&',		'\\/',		'\\^',		'\\$',		'\\=',		'\\_',		'\\%',		'\\\'',		);
				return str_replace($from, $toSingle, $str);
			}
		}
		elseif($escapeType == SearchIndexFieldEscapeType::MD5)
		{				
			$str = trim($str);
			
			if(substr($str, -2) == '\*')
				return md5(substr($str, -2)) . '\\\*';
				
			return md5($str);
		}elseif($escapeType == SearchIndexFieldEscapeType::NO_ESCAPE)
		{
			return $str;
		}
	}
}
