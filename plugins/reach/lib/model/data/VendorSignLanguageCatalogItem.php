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
        if($object instanceof flavorAsset)
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

	protected function getPrivileges($entryId, $shouldModerateOutput)
	{
		$privileges = parent::getPrivileges($entryId, $shouldModerateOutput);

		$privileges .= ',' . kSessionBase::PRIVILEGE_EDIT_ADMIN_TAGS. ':*';

		return $privileges;
	}
}
