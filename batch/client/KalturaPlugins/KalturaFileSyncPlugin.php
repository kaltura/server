<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaFileSyncPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaFileSyncService
	 */
	public $fileSync = null;

	protected function __construct()
	{
		parent::__construct();
		$this->fileSync = new KalturaFileSyncService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaFileSyncPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->fileSync,
		);
		return $services;
	}
}

