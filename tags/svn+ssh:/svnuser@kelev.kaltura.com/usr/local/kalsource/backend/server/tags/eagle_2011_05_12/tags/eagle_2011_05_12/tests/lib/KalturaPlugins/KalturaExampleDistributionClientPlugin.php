<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaExampleDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaExampleDistributionProviderOrderBy
{
}

abstract class KalturaExampleDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaExampleDistributionProfileFilter extends KalturaExampleDistributionProfileBaseFilter
{

}

abstract class KalturaExampleDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaExampleDistributionProviderFilter extends KalturaExampleDistributionProviderBaseFilter
{

}

class KalturaExampleDistributionProfile extends KalturaDistributionProfile
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
	public $accountId = null;


}

class KalturaExampleDistributionProvider extends KalturaDistributionProvider
{

}

class KalturaExampleDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaExampleDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaExampleDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaExampleDistributionClientPlugin($client);
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
		return 'exampleDistribution';
	}
}

