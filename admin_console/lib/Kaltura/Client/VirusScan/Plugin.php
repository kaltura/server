<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_VirusScan_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_VirusScan_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_VirusScan_VirusScanProfileService
	 */
	public $virusScanProfile = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->virusScanProfile = new Kaltura_Client_VirusScan_VirusScanProfileService($client);
	}

	/**
	 * @return Kaltura_Client_VirusScan_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_VirusScan_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'virusScanProfile' => $this->virusScanProfile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'virusScan';
	}
}

