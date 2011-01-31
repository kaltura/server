<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAuditClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaAuditTrailService
	 */
	public $auditTrail = null;

	protected function __construct()
	{
		parent::__construct();
		$this->auditTrail = new KalturaAuditTrailService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaAuditClientPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'auditTrail' => $this->auditTrail,
		);
		return $services;
	}
}

