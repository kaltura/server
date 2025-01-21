<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage api.filters
 */
class KalturaPermissionLevelUserEntryFilter extends KalturaUserEntryFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = EntryPermissionLevelPlugin::getApiValue(PermissionLevelUserEntryType::PERMISSION_LEVEL);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}
}
