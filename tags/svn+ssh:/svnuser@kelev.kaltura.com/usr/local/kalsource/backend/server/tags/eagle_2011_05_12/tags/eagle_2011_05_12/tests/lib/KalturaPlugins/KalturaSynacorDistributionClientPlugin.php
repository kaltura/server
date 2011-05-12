<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaSynacorDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

class KalturaSynacorDistributionProviderOrderBy
{
}

abstract class KalturaSynacorDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

class KalturaSynacorDistributionProfileFilter extends KalturaSynacorDistributionProfileBaseFilter
{

}

abstract class KalturaSynacorDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

class KalturaSynacorDistributionProviderFilter extends KalturaSynacorDistributionProviderBaseFilter
{

}

class KalturaSynacorDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $user = null;

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

class KalturaSynacorDistributionProvider extends KalturaDistributionProvider
{

}

class KalturaSynacorDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaSynacorDistributionClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaSynacorDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaSynacorDistributionClientPlugin($client);
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
		return 'synacorDistribution';
	}
}

