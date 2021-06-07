<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorIntelligentTaggingCatalogItem extends KalturaVendorCatalogItem
{

    protected function getServiceFeature()
    {
        return VendorServiceFeature::INTELLIGENT_TAGGING;
    }

    /* (non-PHPdoc)
    * @see KalturaObject::toInsertableObject()
    */
    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if (is_null($object_to_fill))
            $object_to_fill = new VendorIntelligentTaggingCatalogItem();

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    /* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
    public function toObject($sourceObject = null, $propertiesToSkip = array())
    {
        if(is_null($sourceObject))
        {
            $sourceObject = new VendorIntelligentTaggingCatalogItem();
        }

        return parent::toObject($sourceObject, $propertiesToSkip);
    }
}