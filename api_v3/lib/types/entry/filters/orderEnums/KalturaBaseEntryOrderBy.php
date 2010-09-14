<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBaseEntryOrderBy extends KalturaStringEnum
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
}
