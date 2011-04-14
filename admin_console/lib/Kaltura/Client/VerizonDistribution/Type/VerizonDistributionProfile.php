<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_VerizonDistribution_Type_VerizonDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaVerizonDistributionProfile';
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
	public $domain = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

