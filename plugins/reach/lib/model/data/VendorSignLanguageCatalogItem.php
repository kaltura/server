<?php
/**
 *  VendorSignLanguageCatalogItem
 * @package plugins.reach
 * @subpackage model
 */

class VendorSignLanguageCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues()
    {
        $this->setServiceFeature(VendorServiceFeature::SIGN_LANGUAGE);
    }

    public function isDuplicateTask(entry $entry)
    {
        return false;
    }

    public function requiresEntryReady()
    {
        return true;
    }

    /**
     * @param $object
     * @return kSignLanguageVendorTaskData|null
     */
    public function getTaskJobData($object)
    {
        if($object instanceof asset)
        {
            $taskJobData = new kSignLanguageVendorTaskData();
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
