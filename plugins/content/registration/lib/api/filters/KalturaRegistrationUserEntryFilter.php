<?php
/**
 * @package plugins.registration
 * @subpackage api.filters
 */
class KalturaRegistrationUserEntryFilter extends KalturaUserEntryFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = RegistrationPlugin::getApiValue(RegistrationUserEntryType::REGISTRATION);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}
}
