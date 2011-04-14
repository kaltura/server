<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_AdminConsole_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_AdminConsole_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_AdminConsole_FlavorParamsOutputService
	 */
	public $flavorParamsOutput = null;

	/**
	 * @var Kaltura_Client_AdminConsole_ThumbParamsOutputService
	 */
	public $thumbParamsOutput = null;

	/**
	 * @var Kaltura_Client_AdminConsole_MediaInfoService
	 */
	public $mediaInfo = null;

	/**
	 * @var Kaltura_Client_AdminConsole_EntryAdminService
	 */
	public $entryAdmin = null;

	/**
	 * @var Kaltura_Client_AdminConsole_UiConfAdminService
	 */
	public $uiConfAdmin = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->flavorParamsOutput = new Kaltura_Client_AdminConsole_FlavorParamsOutputService($client);
		$this->thumbParamsOutput = new Kaltura_Client_AdminConsole_ThumbParamsOutputService($client);
		$this->mediaInfo = new Kaltura_Client_AdminConsole_MediaInfoService($client);
		$this->entryAdmin = new Kaltura_Client_AdminConsole_EntryAdminService($client);
		$this->uiConfAdmin = new Kaltura_Client_AdminConsole_UiConfAdminService($client);
	}

	/**
	 * @return Kaltura_Client_AdminConsole_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_AdminConsole_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'flavorParamsOutput' => $this->flavorParamsOutput,
			'thumbParamsOutput' => $this->thumbParamsOutput,
			'mediaInfo' => $this->mediaInfo,
			'entryAdmin' => $this->entryAdmin,
			'uiConfAdmin' => $this->uiConfAdmin,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'adminConsole';
	}
}

