<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaUiConfOrderBy extends KalturaStringEnum
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const ID_AT_ASC = "+id";
	const ID_AT_DESC = "-id";
	const NAME_AT_ASC = "+name";
	const NAME_AT_DESC = "-name";
}
