<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventObjectChangedCondition extends KalturaCondition
{	
	/**
	 * Comma seperated column names to be tested
	 * @var string
	 */
	public $modifiedColumns;

	private static $map_between_objects = array
	(
		'modifiedColumns' ,
	);

	/* (non-PHPdoc)
	 * @see KalturaCondition::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEventObjectChangedCondition();
	
		return parent::toObject($dbObject, $skip);
	}
}
