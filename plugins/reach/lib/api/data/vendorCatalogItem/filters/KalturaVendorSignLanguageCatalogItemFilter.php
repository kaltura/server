<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorSignLanguageCatalogItemFilter extends KalturaVendorDubbingCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
		{
			$type = KalturaVendorServiceFeature::SIGN_LANGUAGE;
		}
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
