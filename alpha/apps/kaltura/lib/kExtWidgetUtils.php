<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class kExtWidgetUtils {

    public static function parseObjectId($objectIdStr)
    {
        $objectId = $version = $subType = $isAsset = $entryId = null;

        $parts = explode('_', $objectIdStr);
        if(count($parts) == 4)
        {
            $objectId = $parts[0].'_'.$parts[1];
            $subType = $parts[2];
            $version = $parts[3];
        }
        else if(count($parts) == 5)
        {
            $entryId = $parts[0].'_'.$parts[1];
            $objectId = $parts[2].'_'.$parts[3];
            $version = $parts[4];
            $isAsset = true;
        }

        return array($objectId, $version, $subType, $isAsset, $entryId);
    }


    private static function getReplacedAndReplacingFileNames($asset, $fileSyncObjectSubType ,$fetch_from_remote_if_no_local = false)
    {
        $replacingFileName = null;
        $fileName = null;
        $syncKey = $asset->getSyncKey($fileSyncObjectSubType);
        list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, $fetch_from_remote_if_no_local);
        if($fileSync)
        {
            $replacingFileName = basename($fileSync->getFilePath());
            $fileExt = pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION);
            $fileName = $asset->getEntryId().'_'.$asset->getId().'_'.$fileSync->getVersion().'.'.$fileExt;
        }
        return array($replacingFileName, $fileName);
    }


    public static function fixIsmManifestForReplacedEntry($syncKey, entry $entry, $fetch_from_remote_if_no_local = false)
    {
        $fileData = kFileSyncUtils::file_get_contents($syncKey);
        $xml = new SimpleXMLElement($fileData);
        $ismcFileName = $xml->head->meta['content'];
        list($ismcObjectId, $version, $subType, $isAsset, $entryId) = self::parseObjectId($ismcFileName);

        if($entryId != $entry->getId())
        {
            //replacement flow
            $flavorAssets = assetPeer::retrieveByEntryIdAndStatus($entry->getId(), asset::ASSET_STATUS_READY);
            foreach ($flavorAssets as $asset)
            {
                if($asset->hasTag(assetParams::TAG_ISM_MANIFEST))
                {
                    list($replacingFileName, $fileName) = self::getReplacedAndReplacingFileNames($asset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC, $fetch_from_remote_if_no_local);
                    if($replacingFileName && $fileName)
                        $fileData = str_replace("content=\"$replacingFileName\"", "content=\"$fileName\"", $fileData);
                }
                else
                {
                    list($replacingFileName, $fileName) = self::getReplacedAndReplacingFileNames($asset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $fetch_from_remote_if_no_local);
                    if($replacingFileName && $fileName)
                        $fileData = str_replace("src=\"$replacingFileName\"", "src=\"$fileName\"", $fileData);
                }
            }
        }
        return $fileData;
    }

}