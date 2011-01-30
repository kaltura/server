<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAdminConsolePlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaFlavorParamsOutputService
	 */
	public $flavorParamsOutput = null;

	/**
	 * @var KalturaThumbParamsOutputService
	 */
	public $thumbParamsOutput = null;

	/**
	 * @var KalturaMediaInfoService
	 */
	public $mediaInfo = null;

	/**
	 * @var KalturaEntryAdminService
	 */
	public $entryAdmin = null;

	protected function __construct()
	{
		parent::__construct();
		$this->flavorParamsOutput = new KalturaFlavorParamsOutputService();
		$this->thumbParamsOutput = new KalturaThumbParamsOutputService();
		$this->mediaInfo = new KalturaMediaInfoService();
		$this->entryAdmin = new KalturaEntryAdminService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaAdminConsolePlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->flavorParamsOutput,
			$this->thumbParamsOutput,
			$this->mediaInfo,
			$this->entryAdmin,
		);
		return $services;
	}
}

