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
}
