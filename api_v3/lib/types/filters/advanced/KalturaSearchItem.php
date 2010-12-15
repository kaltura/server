<?php
/**
 * @package api
 * @subpackage filters
 * @abstract
 */
class KalturaSearchItem extends KalturaObject
{
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			return null;
			
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		$object_to_fill->setKalturaClass(get_class($this));
		
		return $object_to_fill;		
	}
}
