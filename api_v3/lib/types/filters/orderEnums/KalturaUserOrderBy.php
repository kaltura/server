<?php
/**
 * @package api
 * @subpackage api.filters.enum
 */
class KalturaUserOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}
