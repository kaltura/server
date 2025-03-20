<?php
/**
 * @package plugins.rsvp
 * @subpackage api.filters
 */

class KalturaRsvpUserEntryFilter extends KalturaRsvpUserEntryBaseFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = RsvpPlugin::getApiValue(RsvpUserEntryType::RSVP);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}

	public function getCoreFilter()
	{
		return new RsvpUserEntryFilter();
	}
}
