<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaStorageProfilePlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaStorageProfileService
	 */
	public $storageProfile = null;

	protected function __construct()
	{
		parent::__construct();
		$this->storageProfile = new KalturaStorageProfileService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaStorageProfilePlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->storageProfile,
		);
		return $services;
	}
}

