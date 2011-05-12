<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api
 */
class KalturaDistributionProfileFactory
{	
	/**
	 * @param int $providerType
	 * @return KalturaDistributionProfile
	 */
	public static function createKalturaDistributionProfile($providerType)
	{
		if($providerType == KalturaDistributionProviderType::GENERIC)
			return new KalturaGenericDistributionProfile();
			
		if($providerType == KalturaDistributionProviderType::SYNDICATION)
			return new KalturaSyndicationDistributionProfile();
			
		$distributionProfile = KalturaPluginManager::loadObject('KalturaDistributionProfile', $providerType);
		if($distributionProfile)
			return $distributionProfile;
		
		return null;
	}
}