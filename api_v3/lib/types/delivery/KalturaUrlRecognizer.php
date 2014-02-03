<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlRecognizer extends KalturaObject {

	/**
	 * The id of the Delivery
	 *
	 * @var string
	 */
	public $hosts;
	
	private static $map_between_objects = array
	(
			"hosts",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kUrlRecognizer();
			
		parent::toObject($dbObject, $skip);

		return $dbObject;
	}
	
}
