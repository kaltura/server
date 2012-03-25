<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaConditionType extends KalturaDynamicEnum implements ConditionType
{
	public static function getEnumClass()
	{
		return 'ConditionType';
	}

	public static function getDescriptions()
	{
		$descriptions = array(
			self::AUTHENTICATED => 'Validate that user is authenticated, specific privilegs could be defined.',
			self::COUNTRY => 'Validate that request came from specific country, calculated according to request IP.',
			self::IP_ADDRESS => 'Validate that request came from specific IP range.',
			self::SITE => 'Validate that request came from specific domain, wildcards supported.',
			self::USER_AGENT => 'Validate that request came from specific user agent, regular expressions supported.',
			self::FIELD_MATCH => 'Validate that field text matches any of listed textual values.',
			self::FIELD_COMPARE => 'Validate that field number compared correctly to all listed numeric values.',
		);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}
}