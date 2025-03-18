<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorMetadataEnrichmentCatalogItem extends KalturaVendorCatalogItem
{
    protected function getServiceFeature(): int
    {
        return VendorServiceFeature::METADATA_ENRICHMENT;
    }

    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if (is_null($object_to_fill)) {
            $object_to_fill = new VendorMetadataEnrichmentCatalogItem();
        }

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if(is_null($object_to_fill)) {
            $object_to_fill = new VendorMetadataEnrichmentCatalogItem();
        }

        return parent::toObject($object_to_fill, $props_to_skip);
    }
}
