<?php
/**
 * Flavor Params Output service
 *
 * @service flavorParamsOutput
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class FlavorParamsOutputService extends KalturaBaseService
{
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * List flavor params output objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaFlavorParamsOutputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaFlavorParamsOutputListResponse
	 */
	function listAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaFlavorParamsOutputFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$flavorParamsOutputFilter = new assetParamsOutputFilter();
		
		$filter->toObject($flavorParamsOutputFilter);

		$c = new Criteria();
		$flavorParamsOutputFilter->attachToCriteria($c);
		
		$totalCount = flavorParamsOutputPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = flavorParamsOutputPeer::doSelect($c);
		
		$list = KalturaFlavorParamsOutputArray::fromDbArray($dbList);
		$response = new KalturaFlavorParamsOutputListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
