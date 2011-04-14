<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_DistributionJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionJobData';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_DistributionProfile
	 */
	public $distributionProfile;

	/**
	 * 
	 *
	 * @var int
	 */
	public $entryDistributionId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_EntryDistribution
	 */
	public $entryDistribution;

	/**
	 * Id of the media in the remote system
	 *
	 * @var string
	 */
	public $remoteId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Enum_DistributionProviderType
	 */
	public $providerType = null;

	/**
	 * Additional data that relevant for the provider only
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_DistributionJobProviderData
	 */
	public $providerData;

	/**
	 * The results as returned from the remote destination
	 *
	 * @var string
	 */
	public $results = null;

	/**
	 * The data as sent to the remote destination
	 *
	 * @var string
	 */
	public $sentData = null;

	/**
	 * Stores array of media files that submitted to the destination site
	 * Could be used later for media update 
	 *
	 * @var array of KalturaDistributionRemoteMediaFile
	 */
	public $mediaFiles;


}

