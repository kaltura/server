<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaEntryIndexAdvancedFilter extends KalturaIndexAdvancedFilter
{	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kEntryIndexAdvancedFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
