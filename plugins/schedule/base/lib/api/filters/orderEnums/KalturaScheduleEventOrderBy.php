<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters.enum
 */
class KalturaScheduleEventOrderBy extends KalturaStringEnum
{
	const SUMMARY_ASC = "+summary";
	const SUMMARY_DESC = "-summary";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PRIORITY_ASC = "+priority";
	const PRIORITY_DESC = "-priority";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}
