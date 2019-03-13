<?php
/**
 * @package plugins.group
 * @subpackage api.objects
 */
class KalturaGroup extends KalturaUser
{
	/**
	 * @var KalturaUserType
	 * @insertonly
	 */
	public $type;

	/**
	 * @var int
	 * @readonly
	 */
	public $membersCount;

	private static $map_between_objects = array("membersCount");

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new kuser();
			$dbObject->setType(KuserType::GROUP);
		}
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}
}