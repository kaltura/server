<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage lib
 */
class SphinxUtils
{
	public static function escapeString($str, $escapeType = SearchIndexFieldEscapeType::DEFAULT_ESCAPE, $iterations = 2)
	{
		return KalturaCriteria::escapeString($str, $escapeType, $iterations);
	}

	public static function handleEscaping($str)
	{
		return str_replace('\\','\\\\',$str);
	}
}
