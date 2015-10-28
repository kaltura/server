<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaServerNodeOrderBy extends KalturaStringEnum
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const HEARTBEAT_TIME_ASC = "+heartbeatTime";
	const HEARTBEAT_TIME_DESC = "-heartbeatTime";
}
