<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorChapteringCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::CHAPTERING;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
