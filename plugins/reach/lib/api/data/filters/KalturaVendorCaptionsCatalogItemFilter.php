<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorCaptionsCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::CAPTIONS;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
	
	public function getTypeListTemplatesResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::CAPTIONS;
		
		return parent::getTypeListTemplatesResponse($pager, $responseProfile, $type);
	}
}
