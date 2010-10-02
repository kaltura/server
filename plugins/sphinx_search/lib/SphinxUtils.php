<?php

class SphinxUtils
{

	public static function escapeString($str)
	{
		$from = array ( '\'',	'\\',	'(',')',	'|',	'-',	'!',	'@',	'~',	'"',	'&',	'/',	'^',	'$',	'=',	'_',	'%'		);
		$to   = array ( '\\\'',	'\\\\',	'\(','\)',	'\|',	'\-',	'\!',	'\@',	'\~',	'\"',	'\&',	'\/',	'\^',	'\$',	'\=',	'\_',	'\%'	);

		return str_replace($from, $to, $str);
	}
}
	