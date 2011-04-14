<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_DailymotionDistribution_Type_DailymotionDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaDailymotionDistributionProfile';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $user = null;

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

