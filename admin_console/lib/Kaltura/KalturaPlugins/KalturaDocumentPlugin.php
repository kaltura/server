<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaDocumentPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDocumentsService
	 */
	public $documents = null;

	protected function __construct()
	{
		parent::__construct();
		$this->documents = new KalturaDocumentsService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaDocumentPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->documents,
		);
		return $services;
	}
}

