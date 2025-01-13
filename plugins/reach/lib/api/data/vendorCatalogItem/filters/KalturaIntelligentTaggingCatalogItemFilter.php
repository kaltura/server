<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */

class KalturaIntelligentTaggingCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type)
		{
			$type = KalturaVendorServiceFeature::INTELLIGENT_TAGGING;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
