<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.enum
 */
class KalturaESearchEntryFieldName extends KalturaStringEnum implements ESearchEntryFieldName
{
	public static function getEnumClass()
	{
		return 'ESearchEntryFieldName';
	}
}