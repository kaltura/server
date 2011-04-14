<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ComcastDistribution_Type_ComcastDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaComcastDistributionProfile';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

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
	public $account = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $keywords = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $author = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $album = null;


}

