<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorTranslationCatalogItemFilter extends KalturaVendorTranslationCatalogItemBaseFilter
{
	protected function getListResponseType()
	{
		return KalturaVendorServiceFeature::TRANSLATION;
	}
}
