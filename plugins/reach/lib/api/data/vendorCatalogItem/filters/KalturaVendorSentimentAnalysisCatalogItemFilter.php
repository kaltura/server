<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorSentimentAnalysisCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type)
        {
            $type = KalturaVendorServiceFeature::SENTIMENT_ANALYSIS;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
