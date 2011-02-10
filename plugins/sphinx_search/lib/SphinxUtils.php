<?php

class SphinxUtils
{

	public static function escapeString($str)
	{
		$from = array ('\\',	'(',')',	'|',	'-',	'!',	'@',	'~',	'"',	'&',	'/',	'^',	'$',	'=',	'_',	'%'		);
		$to   = array ('\\\\',	'\(','\)',	'\|',	'\-',	'\!',	'\@',	'\~',	'\\\"',	'\&',	'\/',	'\^',	'\$',	'\=',	'\_',	'\%'	);
		$returnStr = str_replace($from, $to, $str);
		return str_replace('\'', '\\\'', $returnStr);
	}
}
	