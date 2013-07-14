<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
abstract class ConfigurableDistributionProvider implements IDistributionProvider
{
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null)
	{
	    $distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile || !($distributionProfile instanceof ConfigurableDistributionProfile))	
			return array();
		return $distributionProfile->getUpdateRequiredEntryFields();
	}
	

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
	    $distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile || !($distributionProfile instanceof ConfigurableDistributionProfile))	
			return array();
			
		return $distributionProfile->getUpdateRequiredMetadataXPaths();
	}
}