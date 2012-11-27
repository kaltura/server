<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters
 */
class KalturaDropFolderFilter extends KalturaDropFolderBaseFilter
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $currentDc;
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new DropFolderFilter();
			
		if(!$this->isNull('currentDc'))
			$this->dcEqual = kDataCenterMgr::getCurrentDcId();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
	
	
}
