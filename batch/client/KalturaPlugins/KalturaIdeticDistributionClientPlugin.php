<?php
/**
 * @package Scheduler
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIdeticDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIdeticDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaIdeticDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIdeticDistributionProfileFilter extends KalturaIdeticDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaIdeticDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIdeticDistributionProviderFilter extends KalturaIdeticDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIdeticDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaIdeticDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaIdeticDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaIdeticDistributionClientPlugin($client);
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
		return 'ideticDistribution';
	}
}

