<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorQuizCatalogItem extends KalturaVendorCatalogItem
{
    protected function getServiceFeature(): int
    {
        return VendorServiceFeature::QUIZ;
    }

    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if (is_null($object_to_fill)) {
            $object_to_fill = new VendorQuizCatalogItem();
        }

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if(is_null($object_to_fill)) {
            $object_to_fill = new VendorQuizCatalogItem();
        }

        return parent::toObject($object_to_fill, $props_to_skip);
    }
}