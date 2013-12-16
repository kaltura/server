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
}
