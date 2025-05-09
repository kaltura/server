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

    public function isDuplicateTask($entryId, $entryObjectType, $partnerId): bool
    {
        return false;
    }

    public function calculateVersion(entry $entry)
    {
        return $entry->getVersion();
    }

    public function requiresEntryReady()
    {
        return false;
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

    public function isEntryTypeSupported($type, $mediaType = null)
    {
        $supportedTypes = KalturaPluginManager::getExtendedTypes(entryPeer::OM_CLASS, entryType::MEDIA_CLIP);
        $supportedTypes[] = entryType::LIVE_STREAM;

        return in_array($type, $supportedTypes);
    }
}
