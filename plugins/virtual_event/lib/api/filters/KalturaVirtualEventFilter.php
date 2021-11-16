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
		$response = new KalturaVirtualEventListResponse();
		$virtualEventFilter = $this->toObject();
		
		$c = new Criteria();
//		$c->add(VirtualEventPeer::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$virtualEventFilter->attachToCriteria($c);
		$response->totalCount = VirtualEventPeer::doCount($c);
		
		$pager->attachToCriteria ($c);
		
		$list = VirtualEventPeer::doSelect($c);
		
		$response->objects = KalturaVirtualEventArray::fromDbArray($list, $responseProfile);
		
		return $response;
	}
	
}
