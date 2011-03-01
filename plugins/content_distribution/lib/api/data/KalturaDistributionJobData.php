<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
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

	/**
	 * The data as sent to the remote destination
	 * @var string
	 */
	public $sentData;
	
	/**
	 * Stores array of media files that submitted to the destination site
	 * Could be used later for media update 
	 * @var KalturaDistributionRemoteMediaFileArray
	 */
	public $mediaFiles;
	
	
	private static $map_between_objects = array
	(
		"distributionProfileId" ,
		"entryDistributionId" ,
		"remoteId" ,
		"providerType" ,
		"results" ,
		"sentData" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		$this->mediaFiles = KalturaDistributionRemoteMediaFileArray::fromDbArray($sourceObject->getMediaFiles());
		
		if(!$this->distributionProfileId)
			return;
			
		if(!$this->entryDistributionId)
			return;
			
		$distributionProfile = DistributionProfilePeer::retrieveByPK($this->distributionProfileId);
		if(!$distributionProfile || $distributionProfile->getStatus() != DistributionProfileStatus::ENABLED)
			return;
			
		$this->distributionProfile = KalturaDistributionProfileFactory::createKalturaDistributionProfile($distributionProfile->getProviderType());
		$this->distributionProfile->fromObject($distributionProfile);
		
		$entryDistribution = EntryDistributionPeer::retrieveByPK($this->entryDistributionId);
		if($entryDistribution)
		{
			$this->entryDistribution = new KalturaEntryDistribution();
			$this->entryDistribution->fromObject($entryDistribution);
		}
		
		$providerType = $sourceObject->getProviderType();
		if($providerType)
		{
			if($providerType == KalturaDistributionProviderType::GENERIC)
			{
				$this->providerData = new KalturaGenericDistributionJobProviderData($this);
			}
			else 
			{
				$this->providerData = KalturaPluginManager::loadObject('KalturaDistributionJobProviderData', $providerType, array($this));
			}
			
			$providerData = $sourceObject->getProviderData();
			if($this->providerData && $providerData && $providerData instanceof kDistributionJobProviderData)
				$this->providerData->fromObject($providerData);
		}
	}
	
	public function toObject($object = null, $skip = array())
	{
		$object = parent::toObject($object, $skip);
				
		if($this->mediaFiles)
		{
			$mediaFiles = array();
			foreach($this->mediaFiles as $mediaFile)
				$mediaFiles[] = $mediaFile;
				
			$object->setMediaFiles($mediaFiles);
		}
		
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
		return kPluginableEnumsManager::apiToCore('DistributionProviderType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('DistributionProviderType', $subType);
	}
}
