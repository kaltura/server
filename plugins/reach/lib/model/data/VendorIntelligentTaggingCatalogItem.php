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

    /**
     * @param $object
     * @return kTranslationVendorTaskData|null
     */
    public function getTaskJobData($object)
    {
        if($object instanceof asset)
        {
            $taskJobData = new kIntelligentTaggingVendorTaskData();
            $taskJobData->assetId = $object->getId();
            return $taskJobData;
        }

        return null;
    }
}