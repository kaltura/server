<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorCaptionsCatalogItemFilter extends KalturaVendorCaptionsCatalogItemBaseFilter
{
	protected function getListResponseType()
	{
		return KalturaVendorServiceFeature::CAPTIONS;
	}
}
