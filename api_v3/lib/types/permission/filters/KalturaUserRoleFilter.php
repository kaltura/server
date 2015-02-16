<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserRoleFilter extends KalturaUserRoleBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new UserRoleFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$userRoleFilter = $this->toObject();

		$c = new Criteria();
		$userRoleFilter->attachToCriteria($c);
		$count = UserRolePeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		$list = UserRolePeer::doSelect($c);
		
		$response = new KalturaUserRoleListResponse();
		$response->objects = KalturaUserRoleArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
		
		return $response;
	}
}
