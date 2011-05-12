<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters
 */
class KalturaDropFolderFileFilter extends KalturaDropFolderFileBaseFilter
{
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new DropFolderFileFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
	
	
}
