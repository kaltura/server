<?php
/**
 * @package plugins.drm
 * @subpackage api.filters
 */
class KalturaDrmProfileFilter extends KalturaDrmProfileBaseFilter
{
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new DrmProfileFilter();
						
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
