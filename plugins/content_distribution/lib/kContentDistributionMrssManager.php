<?php
class kContentDistributionMrssManager implements IKalturaMrssContributor
{
	/**
	 * @var kContentDistributionMrssManager
	 */
	protected static $instance;
	
	protected function __construct()
	{
	}
	
	/**
	 * @return kContentDistributionMrssManager
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new kContentDistributionMrssManager();
			
		return self::$instance;
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contribute(entry $entry, SimpleXMLElement $mrss)
	{
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
			$this->contributeDistribution($entryDistribution, $mrss);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param SimpleXMLElement $xmlElement
	 * @return SimpleXMLElement
	 */
	public function contributeDistribution(EntryDistribution $entryDistribution, SimpleXMLElement $mrss)
	{
		$distributionsProvider = null;
		$distributionsProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		if($distributionsProfile)
			$distributionsProvider = $distributionsProfile->getProvider();
		
		$distribution = $mrss->addChild('distribution');
		$distribution->addAttribute('entryDistributionId', $entryDistribution->getId());
		$distribution->addAttribute('distributionProfileId', $entryDistribution->getDistributionProfileId());
		
		if($distributionsProvider)
		{
			$distribution->addAttribute('provider', $distributionsProvider->getName());
			if($distributionsProvider->getType() == DistributionProviderType::GENERIC)
			{
				$distribution->addAttribute('distributionProviderId', $distributionsProvider->getId());
			}
			else
			{
				// TODO append data from the provider plugin
			}
		}
			
		if($entryDistribution->getRemoteId())
			$distribution->addChild('remoteId', $entryDistribution->getRemoteId());
			
		if($entryDistribution->getSunrise(null))
			$distribution->addChild('sunrise', $entryDistribution->getSunrise(null));
			
		if($entryDistribution->getSunset(null))
			$distribution->addChild('sunset', $entryDistribution->getSunset(null));
			
		$flavorAssetIds = explode(',', $entryDistribution->getFlavorAssetIds());
		$flavorAssetIdsNode = $distribution->addChild('flavorAssetIds');
		foreach($flavorAssetIds as $flavorAssetId)
			$flavorAssetIdsNode->addChild('flavorAssetId', $flavorAssetId);
			
		$thumbAssetIds = explode(',', $entryDistribution->getThumbAssetIds());
		$thumbAssetIdsNode = $distribution->addChild('thumbAssetIds');
		foreach($thumbAssetIds as $thumbAssetId)
			$thumbAssetIdsNode->addChild('thumbAssetId', $thumbAssetId);
	}

	/* (non-PHPdoc)
	 * @see IKalturaBase::getInstance()
	 */
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		$plugin = KalturaPluginManager::getPluginInstance(ContentDistributionPlugin::getPluginName());
		if($plugin)
			return $plugin->getInstance($interface);
		
		return null;
	}
	
}