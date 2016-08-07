<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveEntryServerNodeFilter extends KalturaLiveEntryServerNodeBaseFilter
{
	/**
	 * @var bool
	 */
	public $currentDcOnly;
	
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if($this->currentDcOnly)
			$this->dcEqual = kDataCenterMgr::getCurrentDcId();
		
		return parent::getListResponse($pager, $responseProfile);
	}
}
