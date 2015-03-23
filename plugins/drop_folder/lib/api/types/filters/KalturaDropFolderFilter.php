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

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DropFolderFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(!$this->isNull('currentDc'))
			$this->dcEqual = kDataCenterMgr::getCurrentDcId();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}	
}
