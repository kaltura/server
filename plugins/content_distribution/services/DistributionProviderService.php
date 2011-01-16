<?php
/**
 * Distribution Provider service
 *
 * @service distributionProvider
 */
class DistributionProviderService extends KalturaBaseService
{
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);

		if($this->getPartnerId() != Partner::ADMIN_CONSOLE_PARTNER_ID)
			myPartnerUtils::addPartnerToCriteria(new GenericDistributionProviderPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaDistributionProviderFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDistributionProviderListResponse
	 */
	function listAction(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDistributionProviderFilter();
			
		$c = new Criteria();
		if($filter instanceof KalturaGenericDistributionProviderFilter)
		{
			$genericDistributionProviderFilter = new GenericDistributionProviderFilter();
			$filter->toObject($genericDistributionProviderFilter);
			
			$genericDistributionProviderFilter->attachToCriteria($c);
		}
		$count = GenericDistributionProviderPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = GenericDistributionProviderPeer::doSelect($c);
		
		$response = new KalturaDistributionProviderListResponse();
		$response->objects = KalturaDistributionProviderArray::fromGenericDistributionProvidersArray($list);
		$response->totalCount = $count;
	
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaContentDistributionProvider');
		foreach($pluginInstances as $pluginInstance)
		{
			$provider = $pluginInstance->getKalturaProvider();
			if($provider)
			{
				$response->objects[] = $provider;
				$response->totalCount++;
			}
		}
		
		return $response;
	}	
}
