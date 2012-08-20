<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserRoleFilter extends KalturaUserRoleBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new UserRoleFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
