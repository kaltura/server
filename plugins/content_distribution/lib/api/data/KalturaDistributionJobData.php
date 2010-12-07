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
	
	
	private static $map_between_objects = array
	(
		"distributionProfileId" ,
		"entryDistributionId" ,
		"remoteId" ,
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
	}
	
	/**
	 * @param string $subType
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
