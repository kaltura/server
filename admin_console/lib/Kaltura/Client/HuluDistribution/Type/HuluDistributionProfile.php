<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_HuluDistribution_Type_HuluDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaHuluDistributionProfile';
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

