<?php
/**
 * Generic Distribution Provider service
 *
 * @service genericDistributionProvider
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class GenericDistributionProviderService extends KalturaBaseService
{
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);

		if($this->getPartnerId() != Partner::ADMIN_CONSOLE_PARTNER_ID)
			myPartnerUtils::addPartnerToCriteria(new GenericDistributionProviderPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner(kCurrentContext::$master_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Add new Generic Distribution Provider
	 * 
	 * @action add
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider
	 * @return KalturaGenericDistributionProvider
	 */
	function addAction(KalturaGenericDistributionProvider $genericDistributionProvider)
	{
		$genericDistributionProvider->validatePropertyMinLength("name", 1);
		
		$dbGenericDistributionProvider = new GenericDistributionProvider();
		$genericDistributionProvider->toInsertableObject($dbGenericDistributionProvider);
		$dbGenericDistributionProvider->setPartnerId($this->impersonatedPartnerId);			
		$dbGenericDistributionProvider->setStatus(GenericDistributionProviderStatus::ACTIVE);
		$dbGenericDistributionProvider->save();
		
		$genericDistributionProvider = new KalturaGenericDistributionProvider();
		$genericDistributionProvider->fromObject($dbGenericDistributionProvider);
		return $genericDistributionProvider;
	}
	
	/**
	 * Get Generic Distribution Provider by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaGenericDistributionProvider
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbGenericDistributionProvider = GenericDistributionProviderPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProvider)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);
			
		$genericDistributionProvider = new KalturaGenericDistributionProvider();
		$genericDistributionProvider->fromObject($dbGenericDistributionProvider);
		return $genericDistributionProvider;
	}
	
	/**
	 * Update Generic Distribution Provider by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider
	 * @return KalturaGenericDistributionProvider
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function updateAction($id, KalturaGenericDistributionProvider $genericDistributionProvider)
	{
		$dbGenericDistributionProvider = GenericDistributionProviderPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProvider)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);
		
		if ($genericDistributionProvider->name !== null)
			$genericDistributionProvider->validatePropertyMinLength("name", 1);
			
		$genericDistributionProvider->toUpdatableObject($dbGenericDistributionProvider);
		$dbGenericDistributionProvider->save();
		
		$genericDistributionProvider = new KalturaGenericDistributionProvider();
		$genericDistributionProvider->fromObject($dbGenericDistributionProvider);
		return $genericDistributionProvider;
	}
	
	/**
	 * Delete Generic Distribution Provider by id
	 * 
	 * @action delete
	 * @param int $id
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 * @throws ContentDistributionErrors::CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER
	 */
	function deleteAction($id)
	{
		$dbGenericDistributionProvider = GenericDistributionProviderPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProvider)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);

		if($this->getPartnerId() != Partner::ADMIN_CONSOLE_PARTNER_ID && $dbGenericDistributionProvider->getIsDefault())
			throw new KalturaAPIException(ContentDistributionErrors::CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER);
			
		$dbGenericDistributionProvider->setStatus(GenericDistributionProviderStatus::DELETED);
		$dbGenericDistributionProvider->save();
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaGenericDistributionProviderFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaGenericDistributionProviderListResponse
	 */
	function listAction(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaGenericDistributionProviderFilter();
			
		$c = new Criteria();
		$genericDistributionProviderFilter = new GenericDistributionProviderFilter();
		$filter->toObject($genericDistributionProviderFilter);
		
		$genericDistributionProviderFilter->attachToCriteria($c);
		$count = GenericDistributionProviderPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = GenericDistributionProviderPeer::doSelect($c);
		
		$response = new KalturaGenericDistributionProviderListResponse();
		$response->objects = KalturaGenericDistributionProviderArray::fromGenericDistributionProvidersArray($list);
		$response->totalCount = $count;
	
		return $response;
	}	
}
