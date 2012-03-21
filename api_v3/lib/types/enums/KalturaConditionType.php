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
}