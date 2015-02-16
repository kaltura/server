<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserLoginDataFilter extends KalturaUserLoginDataBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new UserLoginDataFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{	
		$userLoginDataFilter = $this->toObject();
		
		$c = new Criteria();
		$userLoginDataFilter->attachToCriteria($c);
		
		$totalCount = UserLoginDataPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = UserLoginDataPeer::doSelect($c);
		$newList = KalturaUserLoginDataArray::fromDbArray($list, $responseProfile);
		
		$response = new KalturaUserLoginDataListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
}
