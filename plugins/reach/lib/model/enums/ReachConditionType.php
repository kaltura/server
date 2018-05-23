<?php

/**
 * @package plugins.reach
 * @subpackage model.enum
 */

class ReachConditionType implements IKalturaPluginEnum, ConditionType
{
	const EVENT_CATEGORY_ENTRY = 'CategoryEntry';
	
	public static function getAdditionalValues()
	{
		return array(
			'EVENT_CATEGORY_ENTRY' => self::EVENT_CATEGORY_ENTRY,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ReachPlugin::getApiValue(self::EVENT_CATEGORY_ENTRY) => 'Check if active category entry exists on entry and validate the user how added is has permission level as defined',
		);
	}
}
