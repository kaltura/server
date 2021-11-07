<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters.enum
 */
class KalturaVirtualEventOrderBy extends KalturaStringEnum
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}
