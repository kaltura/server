<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters
 */
class KalturaVirtualEventFilter extends KalturaVirtualEventBaseFilter
{
	protected function getCoreFilter()
	{
		return new VirtualEventFilter();
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$virtualEventFilter = $this->toObject();
		
		$c = new Criteria();
		$virtualEventFilter->attachToCriteria($c);
		$count = VirtualEventPeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		
		$list = VirtualEventPeer::doSelect($c);
		
		$response = new KalturaPermissionListResponse();
		$response->objects = KalturaVirtualEventArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
		
		return $response;
	}
	
//	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
//	{
//		$virtualEventFilter = $this->toObject();
//
//		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
//		$virtualEventFilter->attachToCriteria($c);
//		$pager->attachToCriteria($c);
//		$dbList = VirtualEventPeer::doSelect($c);
//		$totalCount = $c->getRecordsCount();
//
//		$list = KalturaVirtualEventArray::fromDbArray($dbList, $responseProfile);
//
//		$response = new KalturaVirtualEventFilterListResponse();
//		$response->objects = $list;
//		$response->totalCount = $totalCount;
//		return $response;
//	}
	
}
