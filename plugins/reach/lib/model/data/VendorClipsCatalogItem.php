<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorClipsCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues(): void
    {
        $this->setServiceFeature(VendorServiceFeature::CLIPS);
    }

    public function isDuplicateTask(entry $entry): bool
    {
        return false;
    }

    public function isEntryTypeSupported($type, $mediaType = null): bool
    {
        $supportedMediaTypes = [entry::ENTRY_MEDIA_TYPE_VIDEO, entry::ENTRY_MEDIA_TYPE_AUDIO];
        return $type === entryType::MEDIA_CLIP && in_array($mediaType, $supportedMediaTypes);
    }
}
