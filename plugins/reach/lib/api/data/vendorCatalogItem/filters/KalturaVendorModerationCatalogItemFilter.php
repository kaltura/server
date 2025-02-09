<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorModerationCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type) {
            $type = KalturaVendorServiceFeature::MODERATION;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
