<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorQuizCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type) {
            $type = KalturaVendorServiceFeature::QUIZ;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
