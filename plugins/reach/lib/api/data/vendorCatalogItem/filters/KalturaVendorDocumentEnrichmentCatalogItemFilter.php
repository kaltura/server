<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorDocumentEnrichmentCatalogItemFilter extends KalturaVendorCatalogItemFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if (!$type)
		{
			$type = KalturaVendorServiceFeature::DOCUMENT_ENRICHMENT;
		}

		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
