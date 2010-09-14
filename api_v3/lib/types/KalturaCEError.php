<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCEError extends KalturaObject 
{
	/**
	 * @var string
	 * @readonly
	 */
	public $id;

	/**
	 * @var int
	 */
	public $partnerId;
	
	/**
	 * @var string
	 */	
	public $browser;

	/**
	 * @var string
	 */	
	public $serverIp;

	/**
	 * @var string
	 */	
	public $serverOs;


	/**
	 * @var string
	 */	
	public $phpVersion;

	/**
	 * @var string
	 */	
	public $ceAdminEmail;

	/**
	 * @var string
	 */	
	public $type;

	/**
	 * @var string
	 */	
	public $description;

	/**
	 * @var string
	 */	
	public $data;
	
	private static $map_between_objects = array
	(
		"id" => "id", 
		"partnerId",
		"browser",
		"serverIp",
		"serverOs",
		"ceAdminEmail",
		"phpVersion",
		"type",
		"description",
		"data",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromKceInstallationError ( KceInstallationError $kceError )
	{
		parent::fromObject( $kceError );
	}
	
	public function toKceInstallationError () 
	{
		$kceError = new KceInstallationError();
		return parent::toObject( $kceError );
	}
}
?>