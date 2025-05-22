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

	public function isEntryTypeSupported($type, $mediaType = null): bool
	{
		$supportedMediaTypes = [entry::ENTRY_MEDIA_TYPE_DOCUMENT, entry::ENTRY_MEDIA_TYPE_PDF];
		return $type === entryType::DOCUMENT && in_array($mediaType, $supportedMediaTypes);
	}
}
