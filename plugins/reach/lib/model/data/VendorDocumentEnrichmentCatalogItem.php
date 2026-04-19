<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorDocumentEnrichmentCatalogItem extends VendorCatalogItem
{
	const CUSTOM_DATA_DOCUMENT_ENRICHMENT_TYPE = 'document_enrichment_type';

	public function setDocumentEnrichmentType($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DOCUMENT_ENRICHMENT_TYPE, $v);
	}

	public function getDocumentEnrichmentType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DOCUMENT_ENRICHMENT_TYPE, null,
			VendorDocumentEnrichmentType::MD_CONVERSION);
	}


	public function applyDefaultValues(): void
	{
		$this->setServiceFeature(VendorServiceFeature::DOCUMENT_ENRICHMENT);
	}

	public function isEntryTypeSupported($type, $mediaType = null): bool
	{
		$supportedDocumentMediaTypes = [entry::ENTRY_MEDIA_TYPE_DOCUMENT, entry::ENTRY_MEDIA_TYPE_PDF];
		return ($type === entryType::DOCUMENT && in_array($mediaType, $supportedDocumentMediaTypes)) ||
			   ($type === entryType::MEDIA_CLIP && $mediaType === entry::ENTRY_MEDIA_TYPE_IMAGE);
	}

	public function isAssetSupported($asset): bool
	{
		$supportedFileExts = ["pdf", "pptx", "docx", "ppt", "doc", "csv", "jpg", "jpeg", "png", "gif", "webp"];
		return $asset instanceof AttachmentAsset && in_array($asset->getFileExt(), $supportedFileExts);
	}
}
