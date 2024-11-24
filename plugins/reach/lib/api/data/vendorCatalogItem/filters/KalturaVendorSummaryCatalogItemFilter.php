<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorSummaryCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type) {
			$type = KalturaVendorServiceFeature::SUMMARY;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}