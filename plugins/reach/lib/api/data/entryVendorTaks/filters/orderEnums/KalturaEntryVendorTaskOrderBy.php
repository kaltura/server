<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.enum
 */
class KalturaEntryVendorTaskOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const QUEUED_AT_ASC = "+queuedAt";
	const QUEUED_AT_DESC = "-queuedAt";
	const FINISHED_AT_ASC = "+finishedAt";
	const FINISHED_AT_DESC = "-finishedAt";
}
