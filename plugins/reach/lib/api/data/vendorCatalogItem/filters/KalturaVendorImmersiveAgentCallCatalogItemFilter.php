<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorImmersiveAgentCallCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type)
		{
			$type = KalturaVendorServiceFeature::IMMERSIVE_AGENT_CALL;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
