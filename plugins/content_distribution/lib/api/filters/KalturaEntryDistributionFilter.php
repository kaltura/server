<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters
 */
class KalturaEntryDistributionFilter extends KalturaEntryDistributionBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new EntryDistributionFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$c = new Criteria();
		$entryDistributionFilter = $this->toObject();
		
		$entryDistributionFilter->attachToCriteria($c);
		$count = EntryDistributionPeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		$list = EntryDistributionPeer::doSelect($c);
		
		$response = new KalturaEntryDistributionListResponse();
		$response->objects = KalturaEntryDistributionArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
	
		return $response;
	}
}
