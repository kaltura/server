<?php
ini_set("memory_limit","1024M");
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
{
    die ('Partner ID and From Date are REQUIRED.\n');
}

$partnerId = $argv[1];
$fromDate = $argv[2];
$realRun = isset($argv[3]) && ($argv[3] == 'true');

$assets = getSourceFlavorAssetsWithNoExtensionPerPartner($partnerId,$fromDate);
updateFlavorAssetExtensionAndFileSync($assets,$realRun);


function getSourceFlavorAssetsWithNoExtensionPerPartner($partnerId,$fromDate)
{
    $c = new Criteria();
    $c->add(assetPeer::PARTNER_ID, $partnerId);
    $c->add(assetPeer::FILE_EXT, "");
    $c->add(assetPeer::TYPE, 1);
    $c->add(assetPeer::IS_ORIGINAL, 1);
    $c->add(assetPeer::CREATED_AT, $fromDate,Criteria::GREATER_THAN);
    return assetPeer::doSelect($c);
}

function getExtensionByContainerFormat($containerFormat)
{
    switch($containerFormat)
    {
        case "qt":
            return "mov";
        case "jpeg":
        case "jpeg_pipe":
            return "jpg";
        case "isom":
            return "mp4";
        case "windows media":
            return "wmv";
        case "flash video":
            return "flv";
        default:
            return null;
    }
}

function updateFlavorAssetExtensionAndFileSync($assets,$realRun)
{

    if(!count($assets))
    {
        KalturaLog::debug('ERROR: Could not find flavors with no extensions for partner');
        return;
    }

    KalturaLog::debug('Going to update '.count($assets).' flavor assets');

    $updatedSuccessfully = 0;

    foreach ($assets as $dbAsset) {

        $flavorId = $dbAsset->getID();
        $flavorContainerFormat = $dbAsset->getContainerFormat();

        $ext = getExtensionByContainerFormat($flavorContainerFormat);
        if (is_null($ext))
        {
            KalturaLog::debug('ERROR: Cannot update flavor [' . $flavorId . '], Please update container_format [' . $flavorContainerFormat . '] matching extension in script');
            continue;
        }

        /*   UPDATE FLAVOR ASSET    */

        KalturaLog::debug('Going to set flavor [' . $flavorId . '] with extension [' . $ext . ']');

        if ($realRun) {
            try {
                $dbAsset->setFileExt($ext);
                $dbAsset->save();
            } catch (Exception $e) {
                KalturaLog::debug('ERROR: couldn\'t set flavor [' . $flavorId . '] with extension [' . $ext . ']');
                KalturaLog::err($e);
                continue;
            }
        }


        /*   UPDATE FILE SYNC    */
        $fileSyncKey = $dbAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
        FileSyncPeer::setUseCriteriaFilter(false);
        $fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($fileSyncKey);
        FileSyncPeer::setUseCriteriaFilter(true);

        foreach ($fileSyncs as $fileSync) {
            $oldFileSyncFilePath = str_replace("//","/",$fileSync->getFullPath());
            $newFileSyncFilePath = $oldFileSyncFilePath . "." . $ext;
            $fileSyncFilePath = $fileSync->getFilePath();

            KalturaLog::debug('Going to move file from [' . $oldFileSyncFilePath . '] to [' . $newFileSyncFilePath . ']');

            if ($realRun) {
                try {
                    rename($oldFileSyncFilePath, $newFileSyncFilePath);
                } catch (Exception $e) {
                    KalturaLog::debug('ERROR: couldn\'t move file from [' . $oldFileSyncFilePath . '] to [' . $newFileSyncFilePath . ']');
                    KalturaLog::err($e);
                    continue;
                }
            }

            KalturaLog::debug('Going to set file_sync [' . $fileSync->getId() . '] with file_path [' . $fileSyncFilePath . '.' . $ext . ']');

            if ($realRun) {
                try {
                    $fileSync->setFilePath($fileSyncFilePath . "." . $ext);
                    $fileSync->save();
                } catch (Exception $e) {
                    KalturaLog::debug('ERROR: couldn\'t move file from [' . $fileSyncFilePath . '] to [' . $fileSyncFilePath . '.' . $ext . ']');
                    KalturaLog::err($e);
                    continue;
                }
            }
        }

        $updatedSuccessfully++;
    }

    KalturaLog::debug('DONE - updated successfully '.$updatedSuccessfully.' flavor assets');
}


