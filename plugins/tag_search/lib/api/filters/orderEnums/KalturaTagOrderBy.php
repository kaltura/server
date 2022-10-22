<?php
/**
 * @package plugins.tagSearch
 * @subpackage filters.enum
 */
class KalturaTagOrderBy extends KalturaStringEnum
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const INSTANCE_COUNT_ASC = "+instanceCount";
	const INSTANCE_COUNT_DESC = "-instanceCount";
}
