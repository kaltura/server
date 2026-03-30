<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorAvatarVodCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues(): void
    {
        $this->setServiceFeature(VendorServiceFeature::AVATAR_VOD);
    }

    public function isDuplicateTask($entryId, $entryObjectType, $partnerId): bool
    {
        return false;
    }

    public function isEntryTypeSupported($type, $mediaType = null): bool
    {
        return $type === entryType::MEDIA_CLIP;
    }
}
