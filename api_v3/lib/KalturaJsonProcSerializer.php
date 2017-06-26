<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaJsonProcSerializer extends KalturaJsonSerializer
{
	public function setHttpHeaders()
	{
		header("Content-Type: application/javascript");
	}
	
	public function getHeader()
	{
		$ALLOWED_REGEX = "/^[0-9_a-zA-Z\.]*$/";
		$callback = isset($_GET["callback"]) ? $_GET["callback"] : null;
		// check for a valid callback, prevent xss
		if (is_null($callback) || !preg_match($ALLOWED_REGEX, $callback))
			die("Expecting \"callback\" parameter for jsonp format");
			
		return $callback .  "(";
	}
	
	public function getFooter($execTime = null)
	{
		return ");";
	}
}
