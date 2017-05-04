<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaMatchConditionType extends KalturaDynamicEnum implements MatchConditionType
{
	public static function getEnumClass()
	{
		return 'MatchConditionType';
	}

	public static function getDescriptions()
	{
		$descriptions = array(
			MatchConditionType::MATCH_ANY => 'Match at least one field value',
			MatchConditionType::MATCH_ALL => 'Match all field values.',
			);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}
}