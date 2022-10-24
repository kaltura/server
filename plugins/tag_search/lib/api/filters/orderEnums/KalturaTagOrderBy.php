<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.filters.enum
 */
class KalturaTagOrderBy extends KalturaStringEnum
{
	const INSTANCE_COUNT_ASC = "+instanceCount";
	const INSTANCE_COUNT_DESC = "-instanceCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}
