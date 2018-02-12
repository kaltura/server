<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaFlavorParamsOutputFilter extends KalturaFlavorParamsOutputBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new assetParamsOutputFilter();
	}
	
	protected function doGetListResponse(KalturaFilterPager $pager, array $types = null)
	{
		$flavorParamsOutputFilter = $this->toObject();
	
		$c = new Criteria();
		$flavorParamsOutputFilter->attachToCriteria($c);
	
		$pager->attachToCriteria($c);
	
		if($types)
		{
			$c->add(assetParamsOutputPeer::TYPE, $types, Criteria::IN);
		}
	
		$list = assetParamsOutputPeer::doSelect($c);
	
		$c->setLimit(null);
		$totalCount = assetParamsOutputPeer::doCount($c);
	
		return array($list, $totalCount);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAssetParamsFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaFlavorParamsOutputListResponse();
		$response->objects = KalturaFlavorParamsOutputArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
