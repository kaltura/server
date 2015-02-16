<?php
/**
 * @package api
 * @subpackage filters
 * @deprecated use KalturaAccessControlProfileFilter instead
 */
class KalturaAccessControlFilter extends KalturaAccessControlBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new accessControlFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$accessControlFilter = $this->toObject();

		$c = new Criteria();
		$accessControlFilter->attachToCriteria($c);
		
		$totalCount = accessControlPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = accessControlPeer::doSelect($c);
		
		$list = KalturaAccessControlArray::fromDbArray($dbList, $responseProfile);
		$response = new KalturaAccessControlListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response; 
	}
}
