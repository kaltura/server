<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaCategoryOrderBy extends KalturaStringEnum
{
	const DEPTH_ASC = "+depth";
	const DEPTH_DESC = "-depth";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const FULL_NAME_ASC = "+fullName";
	const FULL_NAME_DESC = "-fullName";
	const ENTRIES_COUNT_ASC = "+entriesCount";
	const ENTRIES_COUNT_DESC = "-entriesCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DIRECT_ENTRIES_COUNT_ASC = "+directEntriesCount";
	const DIRECT_ENTRIES_COUNT_DESC = "-directEntriesCount";
	const MEMBERS_COUNT_ASC = "+membersCount";
	const MEMBERS_COUNT_DESC = "-membersCount";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const DIRECT_SUB_CATEGORIES_COUNT_ASC = "+directSubCategoriesCount";
	const DIRECT_SUB_CATEGORIES_COUNT_DESC = "-directSubCategoriesCount";
}
