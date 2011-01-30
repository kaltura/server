<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaKalturaInternalToolsPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaKalturaInternalToolsService
	 */
	public $KalturaInternalTools = null;

	/**
	 * @var KalturaKalturaInternalToolsSystemHelperService
	 */
	public $KalturaInternalToolsSystemHelper = null;

	protected function __construct()
	{
		parent::__construct();
		$this->KalturaInternalTools = new KalturaKalturaInternalToolsService();
		$this->KalturaInternalToolsSystemHelper = new KalturaKalturaInternalToolsSystemHelperService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaKalturaInternalToolsPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->KalturaInternalTools,
			$this->KalturaInternalToolsSystemHelper,
		);
		return $services;
	}
}

