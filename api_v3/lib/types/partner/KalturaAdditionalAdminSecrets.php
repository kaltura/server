<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAdditionalAdminSecrets extends KalturaObject
{
	/**
	 * @var array enableAdminSecret
	 */
	public $enableAdminSecrets;


	/**
	 * @var array disableAdminSecret
	 */
	public $disableAdminSecrets;


	private static $map_between_objects = array
	(
		"enableAdminSecrets" ,
		"disableAdminSecrets"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kAdditionalAdminSecrets();

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}