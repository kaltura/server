<?php

class SphinxUtils
{

	public static function escapeString($str, $iterations = 2)
	{
		// NOTE: it appears that sphinx performs double decoding on SELECT values, so we perform 2 iterations.
		//		iterations should be set to 1 by default, if they fix it in some future release.
		for ($iter = 0; $iter < $iterations; $iter++)
		{
			$from = array ('\\',	'(',	')',	'|',	'-',	'!',	'@',	'~',	'"',	'&',	'/',	'^',	'$',	'=',	'_',	'%', 	'\'',	);
			$to   = array ('\\\\',	'\(',	'\)',	'\|',	'\-',	'\!',	'\@',	'\~',	'\\\"',	'\&',	'\/',	'\^',	'\$',	'\=',	'\_',	'\%',	'\\\'',	);
			$str = str_replace($from, $to, $str);
		}
		return $str;
	}
}
