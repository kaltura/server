<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorClipsCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type) {
            $type = KalturaVendorServiceFeature::CLIPS;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
