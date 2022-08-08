<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorLiveCaptionCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type)
		{
			$type = KalturaVendorServiceFeature::LIVE_CAPTION;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}