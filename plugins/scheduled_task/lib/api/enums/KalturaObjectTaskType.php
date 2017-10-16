<?php
/**
 * @package plugins.scheduledTask
 * @subpackage api.enum
 * @see ObjectTaskType
 */
class KalturaObjectTaskType extends KalturaDynamicEnum implements ObjectTaskType
{
	public static function getEnumClass()
	{
		return 'ObjectTaskType';
	}

	public static function getDescriptions()
	{
		$descriptions = array(
			self::DELETE_ENTRY => 'Deletes an entry.',
			self::MODIFY_CATEGORIES => 'Modifies entry categories.',
			self::DELETE_ENTRY_FLAVORS => 'Deletes entry flavors.',
			self::CONVERT_ENTRY_FLAVORS => 'Convert entry flavors by the given flavor params.',
			self::DELETE_LOCAL_CONTENT => 'Delete the local file syncs of an entry.',
			self::MODIFY_ENTRY => 'Modifies entries',
			self::MAIL_NOTIFICATION => 'Send mail notification',
		);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}
}
