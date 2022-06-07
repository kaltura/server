<?php
/**
 * Flavor Params Output service
 *
 * @service flavorParamsOutput
 * @package api
 * @subpackage services
 */
class FlavorParamsOutputService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if($this->getPartnerId() != Partner::BATCH_PARTNER_ID && $this->getPartnerId() != Partner::ADMIN_CONSOLE_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Get flavor params output object by ID
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaFlavorParamsOutput
	 * @throws KalturaErrors::FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND
	 */
	public function getAction($id)
	{
		$flavorParamsOutputDb = assetParamsOutputPeer::retrieveByPK($id);
		
		if (!$flavorParamsOutputDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND, $id);
			
		$flavorParamsOutput = KalturaFlavorParamsFactory::getFlavorParamsOutputInstance($flavorParamsOutputDb->getType());
		$flavorParamsOutput->fromObject($flavorParamsOutputDb, $this->getResponseProfile());
		
		return $flavorParamsOutput;
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
			
		if(!$pager)
		{
			$pager = new KalturaFilterPager();
		}
			
		$types = KalturaPluginManager::getExtendedTypes(assetParamsOutputPeer::OM_CLASS, assetType::FLAVOR);
		return $filter->getTypeListResponse($pager, $this->getResponseProfile(), $types);
	}
}
