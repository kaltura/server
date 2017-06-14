<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.enum
 */
class KalturaESearchCuePointFieldName extends KalturaStringEnum implements ESearchCuePointFieldName
{
	public static function getEnumClass()
	{
		return 'ESearchCuePointFieldName';
	}
}