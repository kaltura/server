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
	
	protected function modifyCriteria ($c)
	{
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$privacyContexts = kEntitlementUtils::getKsPrivacyContextArray();
			$c->addAnd(UserEntryPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
	}
	
	public function toObject ($object_to_fill = null, $props_to_skip = array())
	{
		if (kCurrentContext::getCurrentSessionType() == SessionType::USER)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuser()->getPuserId();
			$this->userIdIn = null;
			$this->userIdNotIn = null;
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
