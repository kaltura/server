<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorImmersiveAgentChatCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type) {
			$type = KalturaVendorServiceFeature::IMMERSIVE_AGENT_CHAT;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
