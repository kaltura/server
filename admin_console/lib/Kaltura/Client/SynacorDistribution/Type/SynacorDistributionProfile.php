<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SynacorDistribution_Type_SynacorDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaSynacorDistributionProfile';
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
	 * @var string
	 */
	public $domain = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

