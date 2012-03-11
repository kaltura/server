<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaSearchConditionComparison extends KalturaDynamicEnum implements searchConditionComparison
{
	public static function getEnumClass()
	{
		return 'searchConditionComparison';
	}
}