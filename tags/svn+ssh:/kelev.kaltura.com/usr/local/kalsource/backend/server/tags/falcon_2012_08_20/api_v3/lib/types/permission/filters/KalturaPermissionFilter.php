<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionFilter extends KalturaPermissionBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new PermissionFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
