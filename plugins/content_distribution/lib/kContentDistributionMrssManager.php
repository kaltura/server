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
		$distribution = $mrss->addChild('distribution');
		$distribution->addAttribute('entryDistributionId', $entryDistribution->getId());
		$distribution->addAttribute('distributionProfileId', $entryDistribution->getDistributionProfileId());
		$distribution->addChild('sunrise', $entryDistribution->getSunrise());
		$distribution->addChild('sunset', $entryDistribution->getSunset());
		$distribution->addChild('flavorAssetIds', $entryDistribution->getFlavorAssetIds());
		$distribution->addChild('thumbAssetIds', $entryDistribution->getThumbAssetIds());
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