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
		);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}
}