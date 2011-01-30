<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaMetadataClientPlugin extends KalturaClientPlugin
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
	 * @var KalturaMetadataProfileService
	 */
	public $metadataProfile = null;

	protected function __construct()
	{
		parent::__construct();
		$this->metadata = new KalturaMetadataService();
		$this->metadataProfile = new KalturaMetadataProfileService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaMetadataClientPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'metadata' => $this->metadata,
			'metadataProfile' => $this->metadataProfile,
		);
		return $services;
	}
}

