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
updateFlavorsFileSyncOnCurrentDC($assets,$realRun);
function getSourceFlavorAssetsWithNoExtensionPerPartner($partnerId,$fromDate)
{
    $c = new Criteria();
    $c->add(assetPeer::PARTNER_ID, $partnerId);
    $c->add(assetPeer::FILE_EXT, "");
    $c->add(assetPeer::TYPE, assetType::FLAVOR);
    $c->add(assetPeer::IS_ORIGINAL, 1);
    $c->add(assetPeer::CREATED_AT, $fromDate,Criteria::GREATER_THAN);
    return assetPeer::doSelect($c);
}

function retrieveAllByFileSyncKey(FileSyncKey $key,$current_dc_only = false)
{
    $c = new Criteria();
    $c->addAnd ( fileSyncPeer::OBJECT_ID , $key->object_id );
    $c->addAnd ( fileSyncPeer::OBJECT_TYPE , $key->object_type );
    $c->addAnd ( fileSyncPeer::OBJECT_SUB_TYPE , $key->object_sub_type );
    $c->addAnd ( fileSyncPeer::VERSION , $key->version );

    if($current_dc_only)
        $c->add(fileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());

    return fileSyncPeer::doSelect($c);
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
function updateFlavorsFileSyncOnCurrentDC($assets,$realRun)
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
        $fileSyncKey = $dbAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
        FileSyncPeer::setUseCriteriaFilter(false);
        $fileSyncs = retrieveAllByFileSyncKey($fileSyncKey,true);
        FileSyncPeer::setUseCriteriaFilter(true);
        foreach ($fileSyncs as $fileSync) {
            $oldFileSyncFilePath = str_replace("//","/",$fileSync->getFullPath());
            $newFileSyncFilePath = $oldFileSyncFilePath . "." . $ext;
            $fileSyncFilePath = $fileSync->getFilePath();
            KalturaLog::debug('Going to move file from [' . $oldFileSyncFilePath . '] to [' . $newFileSyncFilePath . ']');
            if ($realRun)
            {
                if(!rename($oldFileSyncFilePath, $newFileSyncFilePath))
                {
                    KalturaLog::debug('ERROR: couldn\'t move file from [' . $oldFileSyncFilePath . '] to [' . $newFileSyncFilePath . ']');
                    print_r(error_get_last());
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