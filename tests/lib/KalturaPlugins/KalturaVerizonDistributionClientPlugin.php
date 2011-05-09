<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaVerizonDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaVerizonDistributionProviderOrderBy
{
}

abstract class KalturaVerizonDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaVerizonDistributionProfileFilter extends KalturaVerizonDistributionProfileBaseFilter
{

}

abstract class KalturaVerizonDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaVerizonDistributionProviderFilter extends KalturaVerizonDistributionProviderBaseFilter
{

}

class KalturaVerizonDistributionProfile extends KalturaDistributionProfile
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
	public $providerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $vrzFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

class KalturaVerizonDistributionProvider extends KalturaDistributionProvider
{

}

class KalturaVerizonDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaVerizonDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaVerizonDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaVerizonDistributionClientPlugin($client);
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
		return 'verizonDistribution';
	}
}

