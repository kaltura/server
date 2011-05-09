<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaMyspaceDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaMyspaceDistributionProviderOrderBy
{
}

abstract class KalturaMyspaceDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaMyspaceDistributionProfileFilter extends KalturaMyspaceDistributionProfileBaseFilter
{

}

abstract class KalturaMyspaceDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaMyspaceDistributionProviderFilter extends KalturaMyspaceDistributionProviderBaseFilter
{

}

class KalturaMyspaceDistributionProfile extends KalturaDistributionProfile
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
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $myspFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedContact = null;


}

class KalturaMyspaceDistributionProvider extends KalturaDistributionProvider
{

}

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

