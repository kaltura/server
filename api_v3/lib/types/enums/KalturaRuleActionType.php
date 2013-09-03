<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaRuleActionType extends KalturaDynamicEnum implements RuleActionType
{
	public static function getEnumClass()
	{
		return 'RuleActionType';
	}
}