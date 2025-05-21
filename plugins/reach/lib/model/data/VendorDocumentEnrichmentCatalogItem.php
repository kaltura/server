<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorDocumentEnrichmentCatalogItem extends VendorCatalogItem
{
	public function applyDefaultValues(): void
	{
		$this->setServiceFeature(VendorServiceFeature::DOCUMENT_ENRICHMENT);
	}
}
