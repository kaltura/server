<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaJsonSerializer extends KalturaSerializer
{	
	public function setHttpHeaders()
	{
		header("Content-Type: application/json");
	}

	function serialize($object)
	{
		$object = parent::prepareSerializedObject($object);
		return json_encode($object);
	}
	
	public function getHeader()
	{
		return '';
	}
	
	public function getFooter($execTime = null)
	{
		return '';
	}
	
	public function getItemHeader($itemIndex = null)
	{
		return '';
	}
	
	public function getItemFooter()
	{
		return ',';
	}
	
	public function getMulitRequestHeader($itemsCount = null)
	{
		return '[';
	}
	
	public function getMulitRequestFooter()
	{
		return ']';
	}
}
