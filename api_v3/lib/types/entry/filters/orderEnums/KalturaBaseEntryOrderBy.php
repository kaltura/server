<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaBaseEntryOrderBy extends KalturaStringEnum
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}
