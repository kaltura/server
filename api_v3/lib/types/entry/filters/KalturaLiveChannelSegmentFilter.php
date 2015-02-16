<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveChannelSegmentFilter extends KalturaLiveChannelSegmentBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new LiveChannelSegmentFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$liveChannelSegmentFilter = $this->toObject();

		$c = new Criteria();
		$liveChannelSegmentFilter->attachToCriteria($c);
		
		$totalCount = LiveChannelSegmentPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = LiveChannelSegmentPeer::doSelect($c);
		
		$list = KalturaLiveChannelSegmentArray::fromDbArray($dbList, $responseProfile);
		$response = new KalturaLiveChannelSegmentListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
}
