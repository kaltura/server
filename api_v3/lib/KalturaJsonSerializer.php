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
		$json = json_encode($object);
		$json = preg_replace(array('/,?"[^"]+":null/', '/([\{\[]),/'), array('', '\1'), $json);
		return $json;
	}

	public function getItemFooter($lastItem = false)
	{
		if(!$lastItem)
			return ',';
		
		return '';
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
