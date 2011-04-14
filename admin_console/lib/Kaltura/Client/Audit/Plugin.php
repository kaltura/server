<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Audit_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_Audit_Plugin
	 */
	protected static $instance;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return Kaltura_Client_Audit_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_Audit_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'audit';
	}
}

