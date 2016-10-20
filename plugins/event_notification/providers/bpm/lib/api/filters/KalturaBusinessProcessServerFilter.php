<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.filters
 */
class KalturaBusinessProcessServerFilter extends KalturaBusinessProcessServerBaseFilter
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $currentDcOrNull;

	/**
	 * @var KalturaNullableBoolean
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
		if(!$this->isNull('currentDc') && KalturaNullableBoolean::toBoolean($this->currentDc))
			$this->dcEqual = kDataCenterMgr::getCurrentDcId();

		elseif(!$this->isNull('currentDcOrNull') && KalturaNullableBoolean::toBoolean($this->currentDcOrNull))
		{
			if(kDataCenterMgr::getCurrentDcId())
				$this->dcGreaterThenOrNull = kDataCenterMgr::getCurrentDcId() - 1;
			else
				$this->dcLessThenOrNull = kDataCenterMgr::getCurrentDcId() + 1;
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
