<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAssetParamsFilter extends KalturaAssetParamsBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new assetParamsFilter();
	}

	protected function doGetListResponse(KalturaFilterPager $pager, array $types = null)
	{
		$flavorParamsFilter = $this->toObject();
		
		$c = new Criteria();
		$flavorParamsFilter->attachToCriteria($c);
		
		$pager->attachToCriteria($c);
		
		if($types)
		{
			$c->add(assetParamsPeer::TYPE, $types, Criteria::IN);
		}
		
		$list = assetParamsPeer::doSelect($c);
		
		$c->setLimit(null);
		$totalCount = assetParamsPeer::doCount($c);

		return array($list, $totalCount);
	}

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaFlavorParamsListResponse();
		$response->objects = KalturaFlavorParamsArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);  
	}
}
