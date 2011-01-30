<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaKalturaInternalToolsClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaKalturaInternalToolsSystemHelperService
	 */
	public $KalturaInternalToolsSystemHelper = null;

	protected function __construct()
	{
		parent::__construct();
		$this->KalturaInternalToolsSystemHelper = new KalturaKalturaInternalToolsSystemHelperService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaKalturaInternalToolsClientPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'KalturaInternalToolsSystemHelper' => $this->KalturaInternalToolsSystemHelper,
		);
		return $services;
	}
}

