<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionItemFilter extends KalturaPermissionItemBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new PermissionItemFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
