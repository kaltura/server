<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorOcrCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type) {
			$type = KalturaVendorServiceFeature::OCR;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
