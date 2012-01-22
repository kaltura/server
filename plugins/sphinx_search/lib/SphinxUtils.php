<?php

class SphinxUtils
{

	public static function escapeString($str, $iterations = 2)
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
}
