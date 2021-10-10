<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters.enum
 */
class KalturaVirtualEventOrderBy extends KalturaStringEnum
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const DESCRIPTION_ASC = "+description";
	const DESCRIPTION_DESC = "-description";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DELETION_DUE_DATE_ASC = "+deletionDueDate";
	const DELETION_DUE_DATE_DESC = "-deletionDueDate";
}
