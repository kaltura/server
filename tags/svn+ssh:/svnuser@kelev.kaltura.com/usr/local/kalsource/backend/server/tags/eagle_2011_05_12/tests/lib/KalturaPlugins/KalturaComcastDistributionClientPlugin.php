<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaComcastDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaComcastDistributionProviderOrderBy
{
}

abstract class KalturaComcastDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaComcastDistributionProfileFilter extends KalturaComcastDistributionProfileBaseFilter
{

}

abstract class KalturaComcastDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaComcastDistributionProviderFilter extends KalturaComcastDistributionProviderBaseFilter
{

}

class KalturaComcastDistributionProfile extends KalturaDistributionProfile
{
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

class KalturaComcastDistributionProvider extends KalturaDistributionProvider
{

}

class KalturaComcastDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaComcastDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaComcastDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaComcastDistributionClientPlugin($client);
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
		return 'comcastDistribution';
	}
}

