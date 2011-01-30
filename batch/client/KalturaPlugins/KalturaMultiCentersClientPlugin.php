<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaMultiCentersClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaFileSyncImportBatchService
	 */
	public $fileSyncImportBatch = null;

	protected function __construct()
	{
		parent::__construct();
		$this->fileSyncImportBatch = new KalturaFileSyncImportBatchService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaMultiCentersClientPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'fileSyncImportBatch' => $this->fileSyncImportBatch,
		);
		return $services;
	}
}

