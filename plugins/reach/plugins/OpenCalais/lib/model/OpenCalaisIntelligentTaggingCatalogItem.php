<?php


class OpenCalaisIntelligentTaggingCatalogItem extends VendorIntelligentTaggingCatalogItem
{

    public function calculateVersionByEngineType ($entry)
    {
        $transcriptAssets = assetPeer::retrieveByEntryId($entry->getId(), array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)), array(asset::ASSET_STATUS_READY));

        foreach ($transcriptAssets as $transcriptAsset)
        {
            /* @var $transcriptAsset TranscriptAsset */
            if ($transcriptAsset->getFormat() == AttachmentType::TEXT)
            {
                KalturaLog::info('Text transcript asset found: ' . $transcriptAsset->getId());
                break;
            }
        }

        $transcriptContent = '';
        if ($transcriptAsset)
        {
            $transcriptContent = kFileSyncUtils::file_get_contents($transcriptAsset->getSyncKey());
        }

        $version = crc32($transcriptContent . $entry->getName() . $entry->getDescription());
        KalturaLog::info("Vendor task version calculated: $version");

        return $version;
    }

}