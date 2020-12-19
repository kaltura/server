<?php
/**
 *  VendorIntelligentTaggingCatalogItem
 * @package plugins.reach
 * @subpackage model
 */


class VendorIntelligentTaggingCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues()
    {
        $this->setServiceFeature(VendorServiceFeature::INTELLIGENT_TAGGING);
    }

    public function isDuplicateTask(entry $entry)
    {
        return false;
    }

    public function calculateVersion(entry $entry)
    {
        return $entry->getVersion();
    }
}