<?php
/**
 * @package plugins.rating
 * @subpackage api.filters
 */
class KalturaRatingCountFilter extends KalturaRatingCountBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	public function getCoreFilter()
	{
		return new RatingFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		//Logically, it is impossible to end up with more than 30 results in this case since there are only 5 rank values available.
		$results = kRatingPeer::countEntryKvotesByRank($this->entryIdEqual, explode(',', $this->rankIn));
		
		$response = new KalturaRatingCountListResponse();
		
		$response->totalCount = count($results);
		$response->objects = KalturaRatingCountArray::fromDbArray($results, $responseProfile);
		
		return $response;
	}
}
