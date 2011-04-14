<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_MultiCenters_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_MultiCenters_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_MultiCenters_FilesyncImportBatchService
	 */
	public $filesyncImportBatch = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->filesyncImportBatch = new Kaltura_Client_MultiCenters_FilesyncImportBatchService($client);
	}

	/**
	 * @return Kaltura_Client_MultiCenters_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_MultiCenters_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'filesyncImportBatch' => $this->filesyncImportBatch,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'multiCenters';
	}
}

