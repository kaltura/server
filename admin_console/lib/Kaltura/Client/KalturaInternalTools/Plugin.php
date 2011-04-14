<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_KalturaInternalTools_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_KalturaInternalTools_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_KalturaInternalTools_KalturaInternalToolsSystemHelperService
	 */
	public $kalturaInternalToolsSystemHelper = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->kalturaInternalToolsSystemHelper = new Kaltura_Client_KalturaInternalTools_KalturaInternalToolsSystemHelperService($client);
	}

	/**
	 * @return Kaltura_Client_KalturaInternalTools_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_KalturaInternalTools_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'kalturaInternalToolsSystemHelper' => $this->kalturaInternalToolsSystemHelper,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'KalturaInternalTools';
	}
}

