<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaMetadataPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaMetadataService
	 */
	public $metadata = null;

	/**
	 * @var KalturaMetadataBatchService
	 */
	public $metadataBatch = null;

	protected function __construct()
	{
		parent::__construct();
		$this->metadata = new KalturaMetadataService();
		$this->metadataBatch = new KalturaMetadataBatchService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaMetadataPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'metadata' => $this->metadata,
			'metadataBatch' => $this->metadataBatch,
		);
		return $services;
	}
}

