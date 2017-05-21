<?php
/**
 * @package plugins.viewHistory
 * @subpackage api.filters
 */
class KalturaViewHistoryUserEntryFilter extends KalturaUserEntryFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = ViewHistoryPlugin::getApiValue(ViewHistoryUserEntryType::VIEW_HISTORY);
		$response = parent::getListResponse($pager, $responseProfile);
		
		return $response;
	}
	
	protected function modifyCriteria (Criteria $c)
	{
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$privacyContexts = kEntitlementUtils::getKsPrivacyContextArray();
			$c->addAnd(UserEntryPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
	}
}
