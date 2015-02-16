<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionItemFilter extends KalturaPermissionItemBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new PermissionItemFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$permissionItemFilter = $this->toObject();
		
		$c = new Criteria();
		$permissionItemFilter->attachToCriteria($c);
		$count = PermissionItemPeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		$list = PermissionItemPeer::doSelect($c);
		
		$response = new KalturaPermissionItemListResponse();
		$response->objects = KalturaPermissionItemArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
		
		return $response;
	}
}
