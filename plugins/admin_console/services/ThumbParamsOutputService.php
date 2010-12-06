<?php
/**
 * Thumb Params Output service
 *
 * @service thumbParamsOutput
 * @package api
 * @subpackage extServices
 */
class ThumbParamsOutputService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(SystemUserErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * List thumb params output objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaThumbParamsOutputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaThumbParamsOutputListResponse
	 */
	function listAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaThumbParamsOutputFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$thumbParamsOutputFilter = new assetParamsOutputFilter();
		
		$filter->toObject($thumbParamsOutputFilter);

		$c = new Criteria();
		$thumbParamsOutputFilter->attachToCriteria($c);
		
		$totalCount = thumbParamsOutputPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = thumbParamsOutputPeer::doSelect($c);
		
		$list = KalturaThumbParamsOutputArray::fromDbArray($dbList);
		$response = new KalturaThumbParamsOutputListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
