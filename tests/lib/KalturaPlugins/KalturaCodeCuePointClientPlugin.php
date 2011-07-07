<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaCodeCuePointOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const START_TIME_ASC = "+startTime";
	const START_TIME_DESC = "-startTime";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
}

abstract class KalturaCodeCuePointBaseFilter extends KalturaCuePointFilter
{

}

class KalturaCodeCuePointFilter extends KalturaCodeCuePointBaseFilter
{

}

class KalturaCodeCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $code = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;


}

class KalturaCodeCuePointClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaCodeCuePointClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaCodeCuePointClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaCodeCuePointClientPlugin($client);
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
		return 'codeCuePoint';
	}
}

