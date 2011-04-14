<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_StorageProfile_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_StorageProfile_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_StorageProfile_StorageProfileService
	 */
	public $storageProfile = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->storageProfile = new Kaltura_Client_StorageProfile_StorageProfileService($client);
	}

	/**
	 * @return Kaltura_Client_StorageProfile_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_StorageProfile_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'storageProfile' => $this->storageProfile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'storageProfile';
	}
}

