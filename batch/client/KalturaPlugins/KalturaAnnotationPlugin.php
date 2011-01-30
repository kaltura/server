<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAnnotationPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAnnotationService
	 */
	public $annotation = null;

	protected function __construct()
	{
		parent::__construct();
		$this->annotation = new KalturaAnnotationService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaAnnotationPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->annotation,
		);
		return $services;
	}
}

