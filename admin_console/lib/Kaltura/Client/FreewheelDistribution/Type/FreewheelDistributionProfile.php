<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_FreewheelDistribution_Type_FreewheelDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaFreewheelDistributionProfile';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

