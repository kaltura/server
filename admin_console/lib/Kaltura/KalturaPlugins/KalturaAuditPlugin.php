<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAuditPlugin extends KalturaClientPlugin
{
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
			self::$instance = new KalturaAuditPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->auditTrail,
		);
		return $services;
	}
}

