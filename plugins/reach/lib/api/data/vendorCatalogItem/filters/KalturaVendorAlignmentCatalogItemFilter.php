<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorAlignmentCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::ALIGNMENT;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
