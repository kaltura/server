<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters
 */
class KalturaDropFolderFilter extends KalturaDropFolderBaseFilter
{
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new DropFolderFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
	
	
}
