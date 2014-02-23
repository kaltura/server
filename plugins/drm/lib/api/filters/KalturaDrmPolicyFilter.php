<?php
/**
 * @package plugins.drm
 * @subpackage api.filters
 */
class KalturaDrmPolicyFilter extends KalturaDrmPolicyBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new DrmPolicyFilter();
						
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
