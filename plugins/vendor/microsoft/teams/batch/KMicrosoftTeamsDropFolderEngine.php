<?php

/**
 * @package plugins.microsoftTeamsDropFolder
 * @subpackage batch
 */
class KMicrosoftTeamsDropFolderEngine extends KDropFolderEngine
{
	const ADMIN_TAG_TEAMS = 'msTeams';

	const LOCKED_FILE_MIDFIX = '.locked.';

	const LAST_TIMESTAMP_POSTFIX = '_lastTimestamp';

	/**
	 * @var VendorPlugin
	 */
	protected $vendorPlugin;

	/**
	 * @var KMicrosoftGraphClient
	 */
	protected $graphClient;

	/**
	 * @var array
	 */
	protected $singleRunFoundItems;

	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaMicrosoftTeamsDropFolder */
		$this->dropFolder = $dropFolder;
		$this->vendorPlugin = KalturaVendorClientPlugin::get(KBatchBase::$kClient);
		$this->graphClient = new KMicrosoftGraphClient($dropFolder->tenantId, $dropFolder->path, $dropFolder->clientId, $dropFolder->clientSecret);

		$vendorIntegrationSetting = $this->vendorPlugin->vendorIntegration->get($dropFolder->integrationId);
		KalturaLog::info('Watching folder ['.$this->dropFolder->id.']');
		$driveLocationsDir =  KBatchBase::$taskConfig->params->teams->driveLocationsDir;
		$driveLocationsFileNamePrefix = KBatchBase::$taskConfig->params->teams->driveLocationsFileNamePrefix;

		$existingDropFolderFiles = $this->loadDropFolderFiles();

		$files = scandir($driveLocationsDir);

		$currentFile = null;
		foreach ($files as $file)
		{
			if ($file == "." || $file == ".." || strpos($file,self::LOCKED_FILE_MIDFIX) || strpos($file, $driveLocationsFileNamePrefix . $this->dropFolder->id) === false)
			{
				continue;
			}
			else
			{
				$currentFile = $file;
				break;
			}
		}

		if (!$currentFile)
		{
			KalturaLog::info('No iterable file found. Skipping.');
			return;
		}

		KalturaLog::info("On this iteration, processing file $currentFile");

		$currentFilePieces = explode('.', $currentFile, 2);
		$lockedFileName = $currentFilePieces[0] . self::LOCKED_FILE_MIDFIX . $currentFilePieces[1];

		rename($driveLocationsDir . DIRECTORY_SEPARATOR . $currentFile, $driveLocationsDir . DIRECTORY_SEPARATOR . $lockedFileName);

		$itemCount = 0;

		$drivesFileHandle = fopen($driveLocationsDir . DIRECTORY_SEPARATOR . $lockedFileName, 'r+');

		$newDriveUrlAssoc = array();
		$line = fgetcsv($drivesFileHandle);

		while ($line)
		{
			list ($userId, $driveLastPageUrl) = $line;
			KalturaLog::info("Handling drive for user ID: $userId, drive URL: $driveLastPageUrl");
			$items = $this->graphClient->sendGraphRequest($driveLastPageUrl);
			foreach ($items['value'] as $item)
			{
				if (isset($item['folder']))
				{
					KalturaLog::info('Item ' . $item['id'] . ' is a folder. Skipping');
					continue;
				}

				if (isset($item['deleted']))
				{
					KalturaLog::info('Item ' . $item['id'] . ' is deleted. Skipping');
					continue;
				}

				//Get extended item (provided it is a recorded meeting) and its download URL
				$extendedItem = $this->graphClient->getDriveItem($item['parentReference']['driveId'], $item['id']);
				if ($extendedItem)
				{
					$this->singleRunFoundItems[$extendedItem['id']] = $extendedItem;
					$result = null;
					if (in_array($extendedItem['id'], array_keys($existingDropFolderFiles)))
					{
						$currentDropFolderFile = $existingDropFolderFiles[$extendedItem['id']];
						unset ($existingDropFolderFiles[$extendedItem['id']]);
						$result = $this->handleExistingDropFolderFile($currentDropFolderFile);
					}
					else
					{
						$result = $this->handleFileAdded($extendedItem, $dropFolder->id, $vendorIntegrationSetting);
					}

					if ($result)
					{
						$itemCount++;
					}
				}
			}

			$nextLink = '';
			if(isset($items['@odata.nextLink']))
			{
				$nextLink = $items['@odata.nextLink'];
			}
			else
			{
				$nextLink = $items['@odata.deltaLink'];
			}
			KalturaLog::info("Next page for user $userId: $nextLink");
			$newDriveUrlAssoc[] = array($userId, $nextLink);
			$line = fgetcsv($drivesFileHandle);
		}

		$newFileName = $driveLocationsDir . DIRECTORY_SEPARATOR . $driveLocationsFileNamePrefix . $this->dropFolder->id . self::LAST_TIMESTAMP_POSTFIX . time() . '.csv';
		$newFileHandle = fopen($newFileName, 'w+');
		foreach ($newDriveUrlAssoc as $line)
		{
			fputcsv($newFileHandle, $line);
		}
		fclose($newFileHandle);
		fclose($drivesFileHandle);

		unlink($driveLocationsDir . DIRECTORY_SEPARATOR . $lockedFileName);
	}


	protected function handleFileAdded($extendedItem, $dropFolderId, KalturaIntegrationSetting $integrationData)
	{
		KalturaLog::info('Handling drive item with ID ' . $extendedItem['id']);
		$dropFolderFile = new KalturaMicrosoftTeamsDropFolderFile();
		$dropFolderFile->dropFolderId = $dropFolderId;
		$dropFolderFile->fileSize = $extendedItem['size'];
		$dropFolderFile->fileName = $extendedItem['id'];
		$dropFolderFile->remoteId = $extendedItem['id'];
		$dropFolderFile->name = $extendedItem['name'];
		$dropFolderFile->contentUrl = $extendedItem['@microsoft.graph.downloadUrl'];
		if (isset($extendedItem['description']))
		{
			$dropFolderFile->description = $extendedItem['description'];
		}

		$dropFolderFile->ownerId = $this->retrieveUserId($extendedItem['createdBy']);
		$dropFolderFile->additionalUserIds = $this->retrieveParticipants($extendedItem);

		try
		{
			$dropFolderFile = $this->dropFolderFileService->add($dropFolderFile);

			$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$extendedItem['id'].'] - '.$e->getMessage());
			return null;
		}

	}

	protected function retrieveParticipants($item)
	{
		$participants = array();

		$callRecordId = $item['source']['externalId'];
		$callRecord = $this->graphClient->getCallRecord($callRecordId);

		if ($callRecord)
		{
			foreach ($callRecord['participants'] as $participant)
			{
				$userId = $participant['user']['id'];
				$user = $this->graphClient->getUser($userId);
				if ($user)
				{
					$participants[] = $user['mail'];
				}
			}
		}

		return implode(',', $participants);
	}

	protected function getUpdatedFileSize (KalturaDropFolderFile $dropFolderFile)
	{
		if (isset($this->singleRunFoundItems[$dropFolderFile->fileName]))
		{
			$item = $this->singleRunFoundItems[$dropFolderFile->fileName];
			return $item['size'];
		}

		return null;
	}

	protected function retrieveUserId ($creatorInfo)
	{
		$userInfo = $creatorInfo['user'];
		if(isset($userInfo['email']))
		{
			return $userInfo['email'];
		}

		return $userInfo['id'];
	}

	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate ($job->partnerId);

		/* @var $data KalturaDropFolderContentProcessorJobData */
		$dropFolder = $this->dropFolderPlugin->dropFolder->get ($data->dropFolderId);
		//In the case of the microsoft teams drop folder engine, the only possible contentMatch policy is ADD_AS_NEW.
		//Any other policy should cause an error.
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$this->addAsNewContent($job, $data, $dropFolder);
				break;
			default:
				throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, 'Content match policy not allowed for Microsoft Teams drop folders');
				break;
		}

		KBatchBase::unimpersonate();
	}

	protected function addAsNewContent (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data, KalturaMicrosoftTeamsDropFolder $folder)
	{
		/* @var $data KalturaDropFolderContentProcessorJobData */
		$resource = $this->getIngestionResource($job, $data);
		$dropFolderFile = $this->dropFolderFileService->get($job->jobObjectId);
		$newEntry = new KalturaMediaEntry();
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $dropFolderFile->name;
		$newEntry->description = $dropFolderFile->description;
		$newEntry->userId = $dropFolderFile->ownerId;
		$newEntry->creatorId = $newEntry->userId;
		$newEntry->referenceId = $dropFolderFile->remoteId;
		$newEntry->adminTags = self::ADMIN_TAG_TEAMS;
		$newEntry->entitledUsersPublish = $dropFolderFile->additionalUserIds;
		$newEntry->entitledUsersView = $dropFolderFile->additionalUserIds;
		$newEntry->entitledUsersEdit = $dropFolderFile->additionalUserIds;

		KBatchBase::$kClient->startMultiRequest();
		$addedEntry = KBatchBase::$kClient->media->add($newEntry, null);
		KBatchBase::$kClient->baseEntry->addContent($addedEntry->id, $resource);
		$result = KBatchBase::$kClient->doMultiRequest();

		if ($result [1] && $result[1] instanceof KalturaBaseEntry)
		{
			$entry = $result [1];
			$this->createCategoryAssociations ($folder, $entry->userId, $entry->id);
		}
	}
}