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
			ConditionType::AUTHENTICATED => 'Validate that user is authenticated, specific privileges may be defined.',
			ConditionType::COUNTRY => 'Validate that the request came from a specific country, calculated according to request IP.',
			ConditionType::IP_ADDRESS => 'Validate that request came from a specific IP range.',
			ConditionType::SITE => 'Validate that the request came from specific domain, wildcards supported.',
			ConditionType::USER_AGENT => 'Validate that request came from specific user agent, regular expressions supported.',
			ConditionType::FIELD_MATCH => 'Validate that the field text matches any of listed textual values.',
			ConditionType::FIELD_COMPARE => 'Validate that the field number compared correctly to all listed numeric values.',
			ConditionType::GEO_DISTANCE => 'Validate that request came from an IP within a certain geo distance.',
			ConditionType::ANONYMOUS_IP => 'Validate that request came from an IP which fits an anonymous profile (e.g. anonymous, proxy).',
			);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}
}