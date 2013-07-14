<?php
/**
 * @package plugins.annotationEventNotifications
 * @subpackage lib
 */
class AnnotationEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const ANNOTATION = 'Annotation';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ANNOTATION' => self::ANNOTATION,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			AnnotationEventNotificationsPlugin::getApiValue(self::ANNOTATION) => 'Annotation object',
		);
	}
}
