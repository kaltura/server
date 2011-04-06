<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters.enum
 */
class KalturaDropFolderFileOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const FILE_NAME_ASC = "+fileName";
	const FILE_NAME_DESC = "-fileName";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_DESC = "-fileSize";
	const LAST_FILE_SIZE_CHECK_AT_ASC = "+lastFileSizeCheckAt";
	const LAST_FILE_SIZE_CHECK_AT_DESC = "-lastFileSizeCheckAt";
	const PARSED_SLUG_ASC = "+parsedSlug";
	const PARSED_SLUG_DESC = "-parsedSlug";
	const PARSED_FLAVOR_ID_ASC = "+parsedFlavorId";
	const PARSED_FLAVOR_ID_DESC = "-parsedFlavorId";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}
