<?php

class SphinxUtils
{
	const REPLACE_CHARS  = 'zzz'; 

	public static function escapeString($str, $escapeType = SphinxFieldEscapeType::DEFAULT_ESCAPE, $iterations = 2)
	{
		if($escapeType == SphinxFieldEscapeType::DEFAULT_ESCAPE)
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
		elseif($escapeType == SphinxFieldEscapeType::STRIP)
		{
			return preg_replace("/[^a-zA-Z0-9]/" , self::REPLACE_CHARS , trim($str));
		}
	}
}
