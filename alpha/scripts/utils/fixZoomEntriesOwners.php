<?php
// Script to fix Zoom entry owners based on Zoom user info and zoom integration user mapping
require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
{
    echo "Usage: php {$argv[0]} entry_ids.txt partnerId \n";
    exit(1);
}

$entryIdsFile = $argv[1];
$partnerId = (int)$argv[2];

echo "\nPartnerId={$partnerId} \n";

if (!file_exists($entryIdsFile))
{
    echo "Entry IDs file not found: $entryIdsFile\n";
    exit(1);
}

$entryIds = file($entryIdsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$entryIds)
{
    echo "No entry IDs found in file.\n";
    exit(1);
}

foreach ($entryIds as $entryId)
{
    $entryId = trim($entryId);
    if ($entryId === '')
    {
        continue;
    }

    echo "\nProcessing entry: $entryId\n";

    // 1. Find the zoom drop folder file associated with this entry
    $c = new Criteria();
    $c->add(DropFolderFilePeer::PARTNER_ID, $partnerId);
    $c->add(DropFolderFilePeer::ENTRY_ID, $entryId);
    $c->add(DropFolderFilePeer::TYPE, ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM));

    $dropFolderFile = DropFolderFilePeer::doSelectOne($c);

    if (!$dropFolderFile)
    {
        echo "No Zoom drop folder file found for entry ID: $entryId\n";
        continue;
    }

	// Extract host ID
	$hostId = $dropFolderFile->getMeetingMetadata()->getHostId();
    echo "Host ID: $hostId\n";

    $dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());

	$zoomVendorIntegrationId = $dropFolder->getZoomVendorIntegrationId();
    if (!$zoomVendorIntegrationId)
    {
        echo "No zoom vendor integration ID found in drop folder for entry ID: $entryId\n";
        continue;
    }

    $zoomVendorIntegration = VendorIntegrationPeer::retrieveByPK($zoomVendorIntegrationId);

    $accountId = $zoomVendorIntegration->getAccountId();
    echo "Account ID: $accountId\n";

    // Get the Zoom configuration
    $zoomConfiguration = kConf::get(ZoomHelper::ZOOM_ACCOUNT_PARAM, ZoomHelper::VENDOR_MAP);
    $zoomBaseURL = $zoomConfiguration[kZoomClient::ZOOM_BASE_URL];

    $refreshToken = $zoomVendorIntegration->getRefreshToken();
    $accessToken = $zoomVendorIntegration->getAccessToken();
    $accessExpiresIn = $zoomVendorIntegration->getExpiresIn();
    $zoomAuthType = $zoomVendorIntegration->getZoomAuthType();

    try
    {
        $zoomClient = new kZoomClient($zoomBaseURL, $accountId, $refreshToken, $accessToken, $accessExpiresIn, $zoomAuthType);
    }
    catch (Exception $e)
    {
        echo "Failed to create Zoom client: " . $e->getMessage() . "\n";
        continue;
    }

    $zoomUser = $zoomClient->retrieveZoomUser($hostId);
    if (!$zoomUser)
    {
        echo "Failed to retrieve user from Zoom for host ID: $hostId\n";
        continue;
    }

    $hostEmail = '';
    if (isset($zoomUser[ZoomBatchUtils::EMAIL]) && !empty($zoomUser[ZoomBatchUtils::EMAIL]))
    {
        $hostEmail = $zoomUser[ZoomBatchUtils::EMAIL];
    }
    echo "Host email: {$hostEmail}\n";

    $userId = kZoomEventHanlder::KALTURA_ZOOM_DEFAULT_USER;
    if ($hostEmail == '')
    {
        $userId = $zoomVendorIntegration->getCreateUserIfNotExist() ? $userId : $zoomVendorIntegration->getDefaultUserEMail();
    }
    else
    {
        $puserId = kZoomEventHanlder::processZoomUserName($hostEmail, $zoomVendorIntegration, $zoomClient);

        KalturaLog::debug('Finding Zoom user name: ' . $puserId);
        $user = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);

        if (!$user)
        {
            switch ($zoomVendorIntegration->getUserSearchMethod())
            {
                case kZoomUsersSearchMethod::EXTERNAL:
                {
                    KalturaLog::debug('Could not find by id. Searching by external_id');
                    $user = kZoomEventHanlder::getKuserExternalId($puserId);
                    break;
                }
                case kZoomUsersSearchMethod::EMAIL:
                default:
                {
                    KalturaLog::debug('Could not find by id. Searching by email');
                    $user = kuserPeer::getKuserByEmail($hostEmail, $partnerId);
                    break;
                }
            }
        }

        if (!$user)
        {
            if ($zoomVendorIntegration->getCreateUserIfNotExist())
            {
                $userId = $puserId;
                KalturaLog::debug('User not found. Creating new user with id [' . $userId . ']');
            }
            else if ($zoomVendorIntegration->getDefaultUserEMail())
            {
                $userId = $zoomVendorIntegration->getDefaultUserEMail();
                KalturaLog::debug('User not found. Returning default with id [' . $userId . ']');
            }
        }
        else
        {
            $userId = $user->getPuserId();
            KalturaLog::debug('Found user with id [' . $userId . ']');
        }
    }

    $ownerId = $userId;

    if (!$ownerId)
    {
        echo "Entry $entryId: No matching Kaltura owner found for email {$hostEmail}\n";
        continue;
    }

    if ($ownerId)
    {
        echo "Entry $entryId: Resolved owner = $ownerId\n";

        $entry = entryPeer::retrieveByPK($entryId);
        if (!$entry)
        {
            echo "Entry $entryId: Could not retrieve entry from database\n";
            continue;
        }

        $kuser = kuserPeer::getActiveKuserByPartnerAndUid($partnerId, $ownerId);
        if (!$kuser)
        {
            echo "Entry $entryId: Could not find kuser for owner $ownerId\n";
            continue;
        }

        // Update the entry owner
        $entry->setKuserId($kuser->getId());
        $entry->setPuserId($ownerId);
        $entry->save();

        kEventsManager::flushEvents();
        kMemoryManager::clearMemory();

        echo "Entry $entryId: Successfully updated owner to $ownerId\n";

    }
}

