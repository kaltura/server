<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaMsnDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaMsnDistributionProviderOrderBy
{
}

abstract class KalturaMsnDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaMsnDistributionProfileFilter extends KalturaMsnDistributionProfileBaseFilter
{

}

abstract class KalturaMsnDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaMsnDistributionProviderFilter extends KalturaMsnDistributionProviderBaseFilter
{

}

class KalturaMsnDistributionProfile extends KalturaDistributionProfile
{
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
	 * @var string
	 */
	public $csId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $source = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $movFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flvFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $wmvFlavorParamsId = null;


}

class KalturaMsnDistributionProvider extends KalturaDistributionProvider
{

}

class KalturaMsnDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaMsnDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaMsnDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaMsnDistributionClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'msnDistribution';
	}
}

