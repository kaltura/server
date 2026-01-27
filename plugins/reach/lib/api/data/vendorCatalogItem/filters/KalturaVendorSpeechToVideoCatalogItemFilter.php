<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorSpeechToVideoCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type) {
            $type = KalturaVendorServiceFeature::SPEECH_TO_VIDEO;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
