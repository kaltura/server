<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaConversionProfileFilter extends KalturaConversionProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ConversionProfileFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$conversionProfile2Filter = $this->toObject($conversionProfile2Filter);

		$c = new Criteria();
		$conversionProfile2Filter->attachToCriteria($c);
		
		$totalCount = conversionProfile2Peer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = conversionProfile2Peer::doSelect($c);
		
		$list = KalturaConversionProfileArray::fromDbArray($dbList, $responseProfile);
		$list->loadFlavorParamsIds();
		$response = new KalturaConversionProfileListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;  
	}
}
