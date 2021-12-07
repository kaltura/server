<?php

/**
 * @package plugins.KTeams
 * @subpackage batch
 */
class KTeamsDropFolderEngine extends KDropFolderEngine
{
	const ADMIN_TAG_TEAMS = 'msTeams';


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
	
	/**
	 * @var array
	 */
	protected $existingDropFolderFiles;
	
	/**
	 * @var KalturaIntegrationSetting
	 */
	protected $vendorIntegrationSetting;
	
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->initializeEngine($dropFolder);
		
		//$teamsUsers = $this->retrieveTeamsUsers($dropFolder->partnerId, $dropFolder->userFilterTag);
		
		$filter = new KalturaUserFilter();
		$filter->partnerIdEqual = $dropFolder->partnerId;
		$filter->statusEqual = KalturaUserStatus::ACTIVE;
		if ($dropFolder->userFilterTag)
		{
			$filter->tagsMultiLikeOr = $dropFolder->userFilterTag;
		}
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 5;
		$pager->pageIndex = 1;
		
		do
		{
			$usersList = KBatchBase::$kClient->vendorIntegrationUser->listAction($filter, $pager);
			
			foreach ($usersList->objects as $kalturaTeamsVendorIntegrationUser)
			{
				$this->watchTeamsUserFiles($kalturaTeamsVendorIntegrationUser);
			}
			
			$returnedSize = $usersList->objects ? count($usersList->objects) : 0;
		}
		while ($pager->pageSize == $returnedSize);
	}
	
	protected function initializeEngine($dropFolder)
	{
		/* @var $dropFolder KalturaMicrosoftTeamsDropFolder */
		$this->dropFolder = $dropFolder;
		$this->vendorPlugin = KalturaVendorClientPlugin::get(KBatchBase::$kClient);
		$this->graphClient = new KMicrosoftGraphClient($dropFolder->tenantId, $dropFolder->path, $dropFolder->clientId, $dropFolder->clientSecret);
		$this->existingDropFolderFiles = $this->loadDropFolderFiles();
		$this->vendorIntegrationSetting = $this->vendorPlugin->vendorIntegration->get($dropFolder->integrationId);
	}
	
	protected function watchTeamsUserFiles($kalturaUser)
	{
		if ($kalturaUser->email)
		{
			$updateCurrentUser = false;
			
			$teamsUserObject = new KalturaTeamsVendorIntegrationUser();
			$teamsUserObject->id = $kalturaUser->id;
			$teamsUserObject->userId = $kalturaUser->email;
			
			// ADD - if has no teams user
			$teamsUserObject->teamsId = $this->retrieveTeamsUser($kalturaUser);
			if ($teamsUserObject->teamsId)
			{
				$updateCurrentUser = true;
			}
			
			// ADD - if has no recordings folder
			$teamsUserObject->recordingsFolderId = $this->getRecordingsFolderId($teamsUserObject->teamsId);
			if ($teamsUserObject->recordingsFolderId)
			{
				$updateCurrentUser = true;
			}
			
			if ($teamsUserObject->teamsId && $teamsUserObject->recordingsFolderId)
			{
				$newDeltaLink = $this->downloadUserFilesFromDrive($teamsUserObject);
				if ($newDeltaLink)
				{
					$teamsUserObject->deltaToken = $newDeltaLink;
					$updateCurrentUser = true;
				}
			}
			
			if ($updateCurrentUser)
			{
				$this->vendorPlugin->vendorIntegrationUser->update($teamsUserObject->id, $teamsUserObject);
			}
		}
	}
	
	protected function retrieveTeamsUser($kalturaUser)
	{
		$teamsUsers = $this->graphClient->getUserByMail($kalturaUser->email);
		if (isset($teamsUsers[MicrosoftGraphFieldNames::VALUE]))
		{
			foreach ($teamsUsers[MicrosoftGraphFieldNames::VALUE] as $teamsUser)
			{
				if ($teamsUser['id'])
				{
					return $teamsUser['id'];
				}
			}
		}
		
		return null;
	}
	
	protected function getRecordingsFolderId($teamsId)
	{
		$result = $this->graphClient->getDriveDeltaPage($teamsId);
		foreach ($result[MicrosoftGraphFieldNames::VALUE] as $item)
		{
			if (isset($item['specialFolder']['name']) && $item['specialFolder']['name'] == 'recordings')
			{
				return $item['id'];
			}
		}
		
		return null;
	}
	
	protected function downloadUserFilesFromDrive($teamsUserObject)
	{
		$teamsId = $teamsUserObject->teamsId;
		$recordingsFolderId = $teamsUserObject->recordingsFolderId;
		
		if ($teamsUserObject->deltaToken)
		{
			$filesFromDrive = $this->graphClient->sendGraphRequest($teamsUserObject->deltaToken);
		}
		else
		{
			$filesFromDrive = $this->graphClient->getRecordingFolderDeltaPage($teamsId, $recordingsFolderId);
		}
		
		if (isset($filesFromDrive[MicrosoftGraphFieldNames::VALUE]))
		{
			foreach ($filesFromDrive[MicrosoftGraphFieldNames::VALUE] as $fileInRecordings)
			{
				if (isset($fileInRecordings['name']) && $fileInRecordings['name'] != 'Recordings'
					&& !isset($item[MicrosoftGraphFieldNames::FOLDER_FACET]) && !isset($item[MicrosoftGraphFieldNames::DELETED_FACET]))
				{
					$this->getDriveItem($fileInRecordings);
				}
			}
		}
		
		if ($filesFromDrive)
		{
		 	if (isset($filesFromDrive['@odata.nextLink']))
			{
				return $filesFromDrive['@odata.nextLink'];
			}
		 	elseif (isset($filesFromDrive['@odata.deltaLink'])) 
			{
				return $filesFromDrive['@odata.deltaLink'];
			}
		}
		
		return null;
	}
	
	protected function getDriveItem($item)
	{
		$extendedItem = $this->graphClient->getDriveItem($item[MicrosoftGraphFieldNames::PARENT_REFERENCE][MicrosoftGraphFieldNames::DRIVE_ID], $item[MicrosoftGraphFieldNames::ID_FIELD]);
		$result = null;
		if ($extendedItem)
		{
			$this->singleRunFoundItems[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]] = $extendedItem;
			if (in_array($extendedItem[MicrosoftGraphFieldNames::ID_FIELD], array_keys($this->existingDropFolderFiles))) {
				$currentDropFolderFile = $this->existingDropFolderFiles[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]];
				unset ($this->existingDropFolderFiles[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]]);
				if ($currentDropFolderFile->fileSize == $extendedItem[MicrosoftGraphFieldNames::SIZE]) {
					KalturaLog::info('Drive item with ID ' . $extendedItem[MicrosoftGraphFieldNames::ID_FIELD] . ' already exists in the system, and the content size remains the same. Skipping.');
				}
				else
				{
					$currentDropFolderFile = $this->updateDropFolderFile($currentDropFolderFile, $extendedItem);
					$result = $this->handleExistingDropFolderFile($currentDropFolderFile);
				}
			}
			else
			{
				$result = $this->handleFileAdded($extendedItem, $this->dropFolder->id, $this->vendorIntegrationSetting);
			}
		}
		
		return $result;
	}

	protected function updateDropFolderFile(KalturaDropFolderFile $currentDropFolderFile, array $driveItem)
	{
		KalturaLog::info("Updating drop folder file $currentDropFolderFile->id");

		$updateDropFolderFile = new KalturaMicrosoftTeamsDropFolderFile();
		$updateDropFolderFile->name = $driveItem[MicrosoftGraphFieldNames::NAME];
		$updateDropFolderFile->description = $driveItem[MicrosoftGraphFieldNames::DESCRIPTION];
		$updateDropFolderFile->fileSize = $driveItem[MicrosoftGraphFieldNames::SIZE];
		$updateDropFolderFile->contentUrl = $driveItem[MicrosoftGraphFieldNames::DOWNLOAD_URL];

		try
		{
			$dropFolderFile = $this->dropFolderFileService->update($currentDropFolderFile->id, $updateDropFolderFile);
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot update drop folder file with name ['.$driveItem[MicrosoftGraphFieldNames::ID_FIELD].'] - '.$e->getMessage());
			return null;
		}
	}

	protected function handleFileAdded($extendedItem, $dropFolderId, KalturaIntegrationSetting $integrationData)
	{
		KalturaLog::info('Handling drive item with ID ' . $extendedItem[MicrosoftGraphFieldNames::ID_FIELD]);
		$dropFolderFile = new KalturaMicrosoftTeamsDropFolderFile();
		$dropFolderFile->dropFolderId = $dropFolderId;
		$dropFolderFile->fileSize = $extendedItem[MicrosoftGraphFieldNames::SIZE];
		$dropFolderFile->fileName = $extendedItem[MicrosoftGraphFieldNames::ID_FIELD];
		$dropFolderFile->remoteId = $extendedItem[MicrosoftGraphFieldNames::ID_FIELD];
		$dropFolderFile->name = $extendedItem[MicrosoftGraphFieldNames::NAME];
		$dropFolderFile->contentUrl = $extendedItem[MicrosoftGraphFieldNames::DOWNLOAD_URL];
		if (isset($extendedItem[MicrosoftGraphFieldNames::DESCRIPTION]))
		{
			$dropFolderFile->description = $extendedItem[MicrosoftGraphFieldNames::DESCRIPTION];
		}

		$dropFolderFile->ownerId = $this->retrieveUserId($extendedItem[MicrosoftGraphFieldNames::CREATED_BY]);
		$dropFolderFile->additionalUserIds = $this->retrieveParticipants($extendedItem);

		try
		{
			$dropFolderFile = $this->dropFolderFileService->add($dropFolderFile);

			$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$extendedItem[MicrosoftGraphFieldNames::ID_FIELD].'] - '.$e->getMessage());
			return null;
		}

	}
	
	protected function retrieveUserId($creatorInfo)
	{
		$userInfo = $creatorInfo[MicrosoftGraphFieldNames::USER];
		if(isset($userInfo[MicrosoftGraphFieldNames::EMAIL]))
		{
			return $userInfo[MicrosoftGraphFieldNames::EMAIL];
		}
		
		return $userInfo[MicrosoftGraphFieldNames::ID_FIELD];
	}

	protected function retrieveParticipants($item)
	{
		$participants = array();

		$callRecordId = $item[MicrosoftGraphFieldNames::SOURCE][MicrosoftGraphFieldNames::EXTERNAL_ID];
		$callRecord = $this->graphClient->getCallRecord($callRecordId);

		if ($callRecord)
		{
			foreach ($callRecord[MicrosoftGraphFieldNames::PARTICIPANTS] as $participant)
			{
				$userId = $participant[MicrosoftGraphFieldNames::USER][MicrosoftGraphFieldNames::ID_FIELD];
				$user = $this->graphClient->getUser($userId);
				if ($user)
				{
					$participants[] = $user[MicrosoftGraphFieldNames::MAIL];
				}
			}
		}

		return implode(',', $participants);
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

	protected function addAsNewContent(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data, KalturaMicrosoftTeamsDropFolder $folder)
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