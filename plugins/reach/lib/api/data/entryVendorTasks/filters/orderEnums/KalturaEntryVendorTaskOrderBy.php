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
	const QUEUE_TIME_ASC = "+queueTime";
	const QUEUE_TIME_DESC = "-queueTime";
	const FINISH_TIME_ASC = "+finishTime";
	const FINISH_TIME_DESC = "-finishTime";
	const STATUS_ASC = "+status";
	const STATUS_DESC = "-status";
	const PRICE_ASC = "+price";
	const PRICE_DESC = "-price";
	const EXPECTED_FINISH_TIME_ASC = "+expectedFinishTime";
	const EXPECTED_FINISH_TIME_DESC = "-expectedFinishTime";
}
