<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorLiveTranslationCatalogItemFilter extends KalturaVendorLiveCaptionCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::LIVE_TRANSLATION;
			
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
