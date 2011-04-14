<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_FileSync_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_FileSync_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_FileSync_FileSyncService
	 */
	public $fileSync = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->fileSync = new Kaltura_Client_FileSync_FileSyncService($client);
	}

	/**
	 * @return Kaltura_Client_FileSync_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_FileSync_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'fileSync' => $this->fileSync,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'fileSync';
	}
}

