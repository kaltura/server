<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaScheduleResourceFilter extends KalturaScheduleResourceBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ScheduleResourceFilter();
	}
	
	protected function getListResponseType()
	{
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$type = $this->getListResponseType();

		if(!isset($this->statusEqual) && !isset($this->statusIn))
		{
			$allowedStatus = array(ScheduleResourceStatus::DISABLED,ScheduleResourceStatus::ACTIVE);
			$this->statusIn =  implode(',' , $allowedStatus);
		}

		$c = new Criteria();
		if($type)
		{
			$c->add(ScheduleResourcePeer::TYPE, $type);
		}

		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);

		$retrieveStatusDeleted = (isset($this->statusEqual) && $this->statusEqual == ScheduleResourceStatus::DELETED) ||
			(isset($this->statusIn) && strpos($this->statusIn,(string)ScheduleResourceStatus::DELETED) !== false );
		if ($retrieveStatusDeleted)
		{
			ScheduleResourcePeer::setUseCriteriaFilter(false);
			$c->add(ScheduleResourcePeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		}
		$list = ScheduleResourcePeer::doSelect($c);
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = ScheduleResourcePeer::doCount($c);
		}
		ScheduleResourcePeer::setUseCriteriaFilter(true);
		$response = new KalturaScheduleResourceListResponse();
		$response->objects = KalturaScheduleResourceArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
}
