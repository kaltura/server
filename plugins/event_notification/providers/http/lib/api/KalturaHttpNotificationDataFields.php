<?php
/**
 * If this class used as the template data, the fields will be taken from the content parameters
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationDataFields extends KalturaHttpNotificationData
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new kHttpNotificationDataFields();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}
