<?php
/**
 * Thumb Params Output service
 *
 * @service thumbParamsOutput
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class ThumbParamsOutputService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
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
		
		$thumbTypes = KalturaPluginManager::getExtendedTypes(assetParamsOutputPeer::OM_CLASS, assetType::FLAVOR);
		$c->add(assetParamsOutputPeer::TYPE, $thumbTypes, Criteria::IN);
		
		$totalCount = assetParamsOutputPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = assetParamsOutputPeer::doSelect($c);
		
		$list = KalturaThumbParamsOutputArray::fromDbArray($dbList);
		$response = new KalturaThumbParamsOutputListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
