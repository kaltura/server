<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAdCuePointOrderBy
{
	const END_TIME_ASC = "+endTime";
	const END_TIME_DESC = "-endTime";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const START_TIME_ASC = "+startTime";
	const START_TIME_DESC = "-startTime";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
}

class KalturaAdCuePointProviderType
{
	const VAST = "1";
	const FREEWHEEL = "2";
}

class KalturaAdType
{
	const MIDROLL = "1";
	const OVERLAY = "2";
}

abstract class KalturaAdCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeLessThanOrEqual = null;


}

class KalturaAdCuePointFilter extends KalturaAdCuePointBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaAdCuePointProviderType
	 */
	public $providerTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerTypeIn = null;


}

class KalturaAdCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var KalturaAdCuePointProviderType
	 * @insertonly
	 */
	public $providerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceUrl = null;

	/**
	 * 
	 *
	 * @var KalturaAdType
	 */
	public $adType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTime = null;


}

class KalturaAdCuePointClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAdCuePointClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaAdCuePointClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAdCuePointClientPlugin($client);
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
		return 'adCuePoint';
	}
}

