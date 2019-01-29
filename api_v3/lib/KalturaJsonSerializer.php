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
		if(is_null($object))
			return 'null';
			
		$object = parent::prepareSerializedObject($object);
		$options = 0;
		if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR'))
		{
			$options |= JSON_PARTIAL_OUTPUT_ON_ERROR;
		}
		
		$json = json_encode($this->unsetNull($object), $options);
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
			{
				unset($array[$key]);
			}
			else
			{
				$array[$key] = $this->unsetNull($value);
			}
		}
		
		if(is_object($object) && $object instanceof KalturaObject)
		{
			$array['objectType'] = get_class($object);
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
