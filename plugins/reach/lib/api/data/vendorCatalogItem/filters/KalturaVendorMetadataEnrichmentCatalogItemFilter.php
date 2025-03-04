<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorMetadataEnrichmentCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type) {
            $type = KalturaVendorServiceFeature::METADATA_ENRICHMENT;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
