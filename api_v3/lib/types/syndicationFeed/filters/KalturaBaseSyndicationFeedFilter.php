<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBaseSyndicationFeedFilter extends KalturaBaseSyndicationFeedBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new syndicationFeedFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		if ($this->orderBy === null)
			$this->orderBy = KalturaBaseSyndicationFeedOrderBy::CREATED_AT_DESC;
			
		$syndicationFilter = $this->toObject();

		$c = new Criteria();
		$syndicationFilter->attachToCriteria($c);
		$c->add(syndicationFeedPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		
		$totalCount = syndicationFeedPeer::doCount($c);
                
        if($pager === null)
        	$pager = new KalturaFilterPager();
                
        $pager->attachToCriteria($c);
		$dbList = syndicationFeedPeer::doSelect($c);
		
		$list = KalturaBaseSyndicationFeedArray::fromDbArray($dbList, $responseProfile);
		$response = new KalturaBaseSyndicationFeedListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
