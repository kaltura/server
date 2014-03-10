<?php
/**
 * @package plugins.drm
 * @subpackage api.filters.enum
 */
class KalturaDrmPolicyOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const SYSTEM_NAME_ASC = "+systemName";
	const SYSTEM_NAME_DESC = "-systemName";
}
