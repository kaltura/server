<?php
/**
 * API class for recipient provider which constructs a dynamic list of recipients according to a user filter
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationGroupRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * @var string
	 */
	public $groupId;
	
	private static $map_between_objects = array(
		'groupId',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationGroupRecipientProvider();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}