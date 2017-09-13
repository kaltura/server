<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters.enum
 */
class KalturaBeaconOrderBy extends KalturaStringEnum
{
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const OBJECT_ID_ASC = "+objectId";
	const OBJECT_ID_DESC = "-objectId";
}
