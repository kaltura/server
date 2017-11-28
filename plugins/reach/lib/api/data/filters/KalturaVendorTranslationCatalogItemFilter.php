<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorTranslationCatalogItemFilter extends KalturaVendorTranslationCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::TRANSLATION;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
	
	public function getTypeListTemplatesResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::TRANSLATION;
		
		return parent::getTypeListTemplatesResponse($pager, $responseProfile, $type);
	}
}
