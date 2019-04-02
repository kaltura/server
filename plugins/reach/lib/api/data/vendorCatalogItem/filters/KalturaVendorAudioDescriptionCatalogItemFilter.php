<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorAudioDescriptionCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KalturaVendorServiceFeature::AUDIO_DESCRIPTION;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
