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
		$this->applyPartnerFilterForClass('GenericDistributionProvider');
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ContentDistributionPlugin::PLUGIN_NAME);
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
		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria($c);
		$list = GenericDistributionProviderPeer::doSelect($c);
		
		$response = new KalturaDistributionProviderListResponse();
		$response->objects = KalturaDistributionProviderArray::fromDbArray($list, $this->getResponseProfile());
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
