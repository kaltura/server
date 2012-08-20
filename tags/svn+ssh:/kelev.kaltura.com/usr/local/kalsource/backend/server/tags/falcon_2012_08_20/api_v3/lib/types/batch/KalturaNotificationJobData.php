<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNotificationJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $userId;
	
	/**
	 * @var KalturaNotificationType
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $typeAsString;
	
	/**
	 * @var string
	 */
	public $objectId;
	
	/**
	 * @var KalturaNotificationStatus
	 */
	public $status;
	
	/**
	 * @var string
	 */
	public $data;
	
	/**
	 * @var int
	 */
	public $numberOfAttempts;
	
	/**
	 * @var string
	 */
	public $notificationResult;
	
	/**
	 * @var KalturaNotificationObjectType
	 */
	public $objType;
	
	private static $map_between_objects = array("userId", "type", "typeAsString", "objectId", "data", "numberOfAttempts", "notificationResult", "objType");
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kNotificationJobData();
		
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>