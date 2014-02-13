<?php
/**
 * @package plugins.scheduledTask
 * @subpackage api.filters.enum
 */
class KalturaScheduledTaskProfileOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const LAST_EXECUTION_STARTED_AT_ASC = "+lastExecutionStartedAt";
	const LAST_EXECUTION_STARTED_AT_DESC = "-lastExecutionStartedAt";
}
