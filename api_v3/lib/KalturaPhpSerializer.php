<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaPhpSerializer extends KalturaSerializer
{
	function serialize($object)
	{
		$object = parent::prepareSerializedObject($object);
		$result = serialize($object); // Let PHP's built-in serialize() function do the work
		return $result;
	}

	public function getItemHeader($itemIndex = null)
	{
		return 'i:' .$itemIndex . ';';
	}
	
	public function getMulitRequestHeader($itemsCount = null)
	{
		return 'a:' . $itemsCount . ':{';
	}
	
	public function getMulitRequestFooter()
	{
		return '}';
	}
}
