<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.filters
 */
class KalturaBusinessProcessServerFilter extends KalturaBusinessProcessServerBaseFilter
{
	/**
	 * @var bool
	 */
	public $currentDcOrExternal;

	/**
	 * @var bool
	 */
	public $currentDc;

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new BusinessProcessServerFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(!$this->isNull('currentDc') && $this->currentDc)
			$this->dcEqual = kDataCenterMgr::getCurrentDcId();

		elseif(!$this->isNull('currentDcOrExternal') && $this->currentDcOrExternal)
		{
			$this->dcEqOrNull = kDataCenterMgr::getCurrentDcId();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
