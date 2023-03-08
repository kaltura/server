<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorExtendedAudioDescriptionCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
		{
			$type = KalturaVendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION;
		}
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}