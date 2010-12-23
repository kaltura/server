<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaDistributionJobData extends KalturaJobData
{
	/**
	 * @var int
	 */
	public $distributionProfileId;
	
	/**
	 * @var KalturaDistributionProfile
	 */
	public $distributionProfile;
	
	/**
	 * @var int
	 */
	public $entryDistributionId;
	
	/**
	 * @var KalturaEntryDistribution
	 */
	public $entryDistribution;

	/**
	 * Id of the media in the remote system
	 * @var string
	 */
	public $remoteId;

	/**
	 * @var KalturaDistributionProviderType
	 */
	public $providerType;

	/**
	 * Additional data that relevant for the provider only
	 * @var KalturaDistributionJobProviderData
	 */
	public $providerData;

	/**
	 * The results as returned from the remote destination
	 * @var string
	 */
	public $results;
	
	
	private static $map_between_objects = array
	(
		"distributionProfileId" ,
		"entryDistributionId" ,
		"remoteId" ,
		"providerType" ,
		"results" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		if($this->distributionProfileId)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($this->distributionProfileId);
			if($distributionProfile)
			{
				$this->distributionProfile = KalturaDistributionProfileFactory::createKalturaDistributionProfile($distributionProfile->getProviderType());
				$this->distributionProfile->fromObject($distributionProfile);
			}
		}
		
		if($this->entryDistributionId)
		{
			$entryDistribution = EntryDistributionPeer::retrieveByPK($this->entryDistributionId);
			if($entryDistribution)
			{
				$this->entryDistribution = new KalturaEntryDistribution();
				$this->entryDistribution->fromObject($entryDistribution);
			}
		}
		
		$providerData = $sourceObject->getProviderData();
		$providerType = $sourceObject->getProviderType();
		if($providerType && $providerData && $providerData instanceof kDistributionJobProviderData)
		{
			if($providerType == KalturaDistributionProviderType::GENERIC)
			{
				$this->providerData = new KalturaGenericDistributionJobProviderData($this);
			}
			else 
			{
				$this->providerData = KalturaPluginManager::loadObject('KalturaDistributionJobProviderData', $providerType, array($this));
			}
			
			if($this->providerData)
				$this->providerData->fromObject($providerData);
		}
	}
	
	public function toObject($object = null, $skip = array())
	{
		$object = parent::toObject($object, $skip);
		
		if($this->providerType && $this->providerData && $this->providerData instanceof KalturaDistributionJobProviderData)
		{
			$providerData = null;
			if($this->providerType == KalturaDistributionProviderType::GENERIC)
			{
				$providerData = new kGenericDistributionJobProviderData($object);
			}
			else 
			{
				$providerData = KalturaPluginManager::loadObject('kDistributionJobProviderData', $this->providerType, array($object));
			}
			
			if($providerData)
			{
				$providerData = $this->providerData->toObject($providerData);
				$object->setProviderData($providerData);
			}
		}
		
		return $object;
	}
	
	/**
	 * @param string $subType is the provider type
	 * @return int
	 */
	public function toSubType($subType)
	{
		return $this->toDynamicEnumValue('KalturaDistributionProviderType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return $this->fromDynamicEnumValue('KalturaDistributionProviderType', $subType);
	}
}
