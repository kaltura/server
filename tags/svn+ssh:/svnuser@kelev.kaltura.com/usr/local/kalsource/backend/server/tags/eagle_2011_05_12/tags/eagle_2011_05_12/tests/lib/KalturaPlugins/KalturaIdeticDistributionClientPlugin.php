<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaIdeticDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaIdeticDistributionProviderOrderBy
{
}

abstract class KalturaIdeticDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaIdeticDistributionProfileFilter extends KalturaIdeticDistributionProfileBaseFilter
{

}

abstract class KalturaIdeticDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaIdeticDistributionProviderFilter extends KalturaIdeticDistributionProviderBaseFilter
{

}

class KalturaIdeticDistributionProfile extends KalturaDistributionProfile
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


}

class KalturaIdeticDistributionProvider extends KalturaDistributionProvider
{

}

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

