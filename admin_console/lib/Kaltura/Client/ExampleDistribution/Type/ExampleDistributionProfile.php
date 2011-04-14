<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ExampleDistribution_Type_ExampleDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaExampleDistributionProfile';
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
	 * @var string
	 */
	public $accountId = null;


}

