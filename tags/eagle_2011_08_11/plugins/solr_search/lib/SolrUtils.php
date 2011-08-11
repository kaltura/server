<?php

class SolrUtils
{

	public static function escapeString($str)
	{
		$from = array('+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '\\');
		$to = array('\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\"', '\\~', '\\*', '\\?', '\\:', '\\\\');

		return str_replace($from, $to, $str);
	}
}