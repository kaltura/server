<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorAvatarVodCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if (!$type)
		{
            $type = KalturaVendorServiceFeature::AVATAR_VOD;
        }

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}
