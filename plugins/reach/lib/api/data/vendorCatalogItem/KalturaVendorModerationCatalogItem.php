<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorModerationCatalogItem extends KalturaVendorCatalogItem
{
    protected function getServiceFeature(): int
    {
        return VendorServiceFeature::MODERATION;
    }

    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if (is_null($object_to_fill))
        {
            $object_to_fill = new VendorModerationCatalogItem();
        }

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if(is_null($object_to_fill))
        {
            $object_to_fill = new VendorModerationCatalogItem();
        }

        return parent::toObject($object_to_fill, $props_to_skip);
    }
}
