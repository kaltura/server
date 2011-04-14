<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_DropFolder_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_DropFolder_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_DropFolder_DropFolderService
	 */
	public $dropFolder = null;

	/**
	 * @var Kaltura_Client_DropFolder_DropFolderFileService
	 */
	public $dropFolderFile = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->dropFolder = new Kaltura_Client_DropFolder_DropFolderService($client);
		$this->dropFolderFile = new Kaltura_Client_DropFolder_DropFolderFileService($client);
	}

	/**
	 * @return Kaltura_Client_DropFolder_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_DropFolder_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'dropFolder' => $this->dropFolder,
			'dropFolderFile' => $this->dropFolderFile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'dropFolder';
	}
}

