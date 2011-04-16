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
class KalturaBulkUploadCsvVersion
{
	const V1 = 1;
	const V2 = 2;
	const V3 = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadCsvJobData extends KalturaBulkUploadJobData
{
	/**
	 * The version of the csv file
	 * 
	 *
	 * @var KalturaBulkUploadCsvVersion
	 */
	public $csvVersion = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadCsvClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaBulkUploadCsvClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaBulkUploadCsvClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaBulkUploadCsvClientPlugin($client);
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
		return 'bulkUploadCsv';
	}
}

