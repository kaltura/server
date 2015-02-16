<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionFilter extends KalturaPermissionBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new PermissionFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$permissionFilter = $this->toObject();
		
		$c = new Criteria();
		$permissionFilter->attachToCriteria($c);
		$count = PermissionPeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		
		$list = PermissionPeer::doSelect($c);
		
		$response = new KalturaPermissionListResponse();
		$response->objects = KalturaPermissionArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
		
		return $response;
	}
}
