<?php
/**
 * @package plugins.codeCuePointEventNotifications
 * @subpackage lib
 */
class CodeCuePointEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const CODE_CUE_POINT = 'CodeCuePoint';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CODE_CUE_POINT' => self::CODE_CUE_POINT,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			CodeCuePointEventNotificationsPlugin::getApiValue(self::CODE_CUE_POINT) => 'Code cue point object',
		);
	}
}
