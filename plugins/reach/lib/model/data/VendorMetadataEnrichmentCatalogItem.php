<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorMetadataEnrichmentCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues(): void
    {
        $this->setServiceFeature(VendorServiceFeature::METADATA_ENRICHMENT);
    }

    public function isDuplicateTask($entry, $entryObjectType): bool
    {
        return false;
    }

    public function isEntryTypeSupported($type, $mediaType = null): bool
    {
        $supportedMediaTypes = [entry::ENTRY_MEDIA_TYPE_VIDEO, entry::ENTRY_MEDIA_TYPE_AUDIO];
        return $type === entryType::MEDIA_CLIP && in_array($mediaType, $supportedMediaTypes);
    }
}
