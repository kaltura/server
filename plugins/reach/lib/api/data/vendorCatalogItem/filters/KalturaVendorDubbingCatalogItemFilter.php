<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorDubbingCatalogItemFilter extends KalturaVendorTranslationCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
		{
			$type = KalturaVendorServiceFeature::DUBBING;
		}
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}