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
class KalturaMyspaceDistributionProfileOrderBy
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
class KalturaMyspaceDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMyspaceDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMyspaceDistributionProfileFilter extends KalturaMyspaceDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMyspaceDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMyspaceDistributionProviderFilter extends KalturaMyspaceDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMyspaceDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaMyspaceDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaMyspaceDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaMyspaceDistributionClientPlugin($client);
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
		return 'myspaceDistribution';
	}
}

