<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SystemPartner_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_SystemPartner_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_SystemPartner_SystemPartnerService
	 */
	public $systemPartner = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->systemPartner = new Kaltura_Client_SystemPartner_SystemPartnerService($client);
	}

	/**
	 * @return Kaltura_Client_SystemPartner_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_SystemPartner_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'systemPartner' => $this->systemPartner,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'systemPartner';
	}
}

