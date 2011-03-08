<?php
/**
 * Distribution Provider service
 *
 * @service distributionProvider
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class DistributionProviderService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

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
	
		$syndicationProvider = new KalturaSyndicationDistributionProvider();
		$syndicationProvider->fromObject(SyndicationDistributionProvider::get());
		$response->objects[] = $syndicationProvider;
		$response->totalCount++;
		
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
