<?php
/**
 * @package plugins.watchLater
 * @subpackage api.filters
 */
class KalturaWatchLaterUserEntryFilter extends KalturaUserEntryFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = WatchLaterPlugin::getApiValue(WatchLaterUserEntryType::WATCH_LATER);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}
}
