<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaAnnotationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

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
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAnnotationClientPlugin();
		return self::$instance;
	}

	public function getName()
	{
	
	}
	
	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'annotation' => $this->annotation,
		);
		return $services;
	}
}

