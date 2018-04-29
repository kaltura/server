<?php
/**
 * Effects attributes
 *
 * @package api
 * @subpackage objects
 */

class KalturaEffect extends KalturaObject
{

	/**
	 * @var KalturaEffectType
	 */
	public $effectType;


	/**
	 * value
	 * @var string
	 */
	public $value;



	private static $map_between_objects = array
	(
		"effectType" ,
		"value"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kEffect();

		return parent::toObject($object_to_fill, $props_to_skip);
	}


}