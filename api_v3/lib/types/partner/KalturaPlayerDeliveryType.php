<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayerDeliveryType extends KalturaObject
{
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $label;
	
	/**
	 * @var KalturaKeyValueArray
	 */
	public $flashvars;
	
	/**
	 * @var string
	 */
	public $minVersion;
	
	private static $map_between_objects = array(
		'label', 
		'flashvars',
		'minVersion',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}