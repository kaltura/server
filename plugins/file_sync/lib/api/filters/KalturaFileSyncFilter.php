<?php
/**
 * @package plugins.fileSync
 * @subpackage api.filters
 */
class KalturaFileSyncFilter extends KalturaFileSyncBaseFilter
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $currentDc;
	
	static private $map_between_objects = array
	(
		"fileObjectTypeEqual" => "_eq_object_type",
		"fileObjectTypeIn" => "_in_object_type",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new FileSyncFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(!$this->isNull('currentDc'))
		{
			if($this->currentDc == KalturaNullableBoolean::TRUE_VALUE)
				$this->dcEqual = kDataCenterMgr::getCurrentDcId();
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
