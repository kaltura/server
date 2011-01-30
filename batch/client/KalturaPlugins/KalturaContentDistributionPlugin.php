<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaContentDistributionPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaContentDistributionBatchService
	 */
	public $contentDistributionBatch = null;

	protected function __construct()
	{
		parent::__construct();
		$this->contentDistributionBatch = new KalturaContentDistributionBatchService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaContentDistributionPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'contentDistributionBatch' => $this->contentDistributionBatch,
		);
		return $services;
	}
}

