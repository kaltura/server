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
		$json = json_encode($this->unsetNull($object));
		return $json;
	}

	protected function unsetNull($object)
	{
		if(!is_array($object) && !is_object($object))
			return $object;
		
		$array = (array) $object;
		foreach($array as $key => $value)
		{
			if(is_null($value))
				unset($array[$key]);
			
			$array[$key] = $this->unsetNull($value);
		}
		
		return $array;
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
