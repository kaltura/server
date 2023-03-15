<?php

/**
 * @package plugins.microsoftTeamsDropFolder
 * @subpackage batch
 */
class KMicrosoftTeamsDropFolderEngine extends KDropFolderEngine
{
	const ADMIN_TAG_TEAMS = 'msTeams';
    const MS_GRAPH_METADATA = 'ms_graph_metadata';

	const ASC_LAST_ACCESSED_DATE = "+/*[local-name()='metadata']/*[local-name()='LastAccessed']";
	const OPT_IN_XPATH = "/*[local-name()='metadata']/*[local-name()='HasOptIn']";
	const OPT_IN_VALUE = 'True';
	const PERSONAL_RECORDINGS_DIRECTORY_URL = 'me/drive/root:/Recordings:/delta?token=%s';

	/**
	 * @var VendorPlugin
	 */
	protected $vendorPlugin;

	/**
	 * @var array
	 */
	protected $singleRunFoundItems;

	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaMicrosoftTeamsDropFolder */
		$this->dropFolder = $dropFolder;
		$this->vendorPlugin = KalturaVendorClientPlugin::get(KBatchBase::$kClient);

		$vendorIntegrationSetting = $this->vendorPlugin->vendorIntegration->get($dropFolder->integrationId);
		KalturaLog::info('Watching folder ['.$this->dropFolder->id.']');

		$existingDropFolderFiles = $this->loadDropFolderFiles();

		// Retrieve all user custom metadata objects for the metadata profile specified on the drop folder's integration ID
		$userMetadataSearchItem = new KalturaMetadataSearchItem();
		$userMetadataSearchItem->metadataProfileId = $vendorIntegrationSetting->userMetadataProfileId;
		$userMetadataSearchItem->orderBy = self::ASC_LAST_ACCESSED_DATE;

		$condition = new KalturaSearchMatchCondition();
		$condition->field = self::OPT_IN_XPATH;
		$condition->value = self::OPT_IN_VALUE;

		$userMetadataSearchItem->items = [ $condition ];

		$userFilter = new KalturaUserFilter();
		$userFilter->advancedSearch = $userMetadataSearchItem;

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = KBatchBase::$taskConfig->params->teams->userPageSize;

		KBatchBase::$kClient->setResponseProfile($this->constructResponseProfile($vendorIntegrationSetting->userMetadataProfileId));

		$response = KBatchBase::$kClient->user->listAction($userFilter, $pager);

		foreach ($response->objects as $user)
		{
		    /* @var $user KalturaUser  */
			$userMetadataObj = $user->relatedObjects[self::MS_GRAPH_METADATA]->objects[0];

			try {
				$userTeamsData = new KUserGraphMetadata($userMetadataObj->xml, $vendorIntegrationSetting->encryptionKey);
				if (!$userTeamsData)
				{
					if ($user instanceof KalturaGroup)
					{
					    $userTeamsData->recordingType = 'GROUP';
					}
					else
					{
					    $userTeamsData->recordingType = 'PERSONAL';
					}
				}

			} catch (Exception $e) {
			    KalturaLog::err('Could not instantiate this user\'s MS Graph data. Continuing to the next user.');
			    continue;
			}

			$graphClient = new KMicrosoftGraphClient($dropFolder->tenantId, $dropFolder->path, $dropFolder->clientId, $dropFolder->clientSecret);
			if ($userTeamsData->authTokenExpiry < time())
			{
				KalturaLog::info('User ' . $user->id . ' requires a new bearer token.');
				list($userTeamsData->authToken, $userTeamsData->refreshToken, $userTeamsData->authTokenExpiry) = $graphClient->refreshToken($userTeamsData->refreshToken, $vendorIntegrationSetting->scopes);
			}
			else
			{
				$graphClient->bearerToken = $userTeamsData->authToken;
			}

			$driveLastPageUrl = sprintf(self::PERSONAL_RECORDINGS_DIRECTORY_URL, $userTeamsData->deltaToken ? $userTeamsData->deltaToken : 'latest' );
			KalturaLog::info("Handling drive for user ID: {$user->id}, drive URL: $driveLastPageUrl");
			$items = $graphClient->sendGraphRequest($driveLastPageUrl);
			if (!$items)
			{
				KalturaLog::info('Graph request could not be completed. This drive URL will be retried on the next iteration of this file.');
				continue;
			}

			foreach ($items[MicrosoftGraphFieldNames::VALUE] as $item)
			{
				//Get extended item (provided it is a recorded meeting) and its download URL
				$extendedItem = $graphClient->getDriveItem($item[MicrosoftGraphFieldNames::PARENT_REFERENCE][MicrosoftGraphFieldNames::DRIVE_ID], $item[MicrosoftGraphFieldNames::ID_FIELD]);
				if ($extendedItem)
				{
					$this->singleRunFoundItems[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]] = $extendedItem;
					$result = null;
					if (in_array($extendedItem[MicrosoftGraphFieldNames::ID_FIELD], array_keys($existingDropFolderFiles))) {
						$currentDropFolderFile = $existingDropFolderFiles[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]];
						unset ($existingDropFolderFiles[$extendedItem[MicrosoftGraphFieldNames::ID_FIELD]]);
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
						$result = $this->handleFileAdded($extendedItem, $dropFolder->id, $vendorIntegrationSetting);
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

			KalturaLog::info("Next page for user {$user->id}: $nextLink");
			$args = explode('&', parse_url($nextLink, PHP_URL_QUERY));

			foreach ($args as $arg)
			{
				list($key, $value) = explode('=', $arg);
				if ($key == MicrosoftGraphFieldNames::TOKEN_QUERY_PARAM)
				{
					$userTeamsData->deltaToken = $value;
					break;
				}
			}

			$userTeamsData->lastAccessed = time();

			$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
			try
			{
				$metadataPlugin->metadata->update($userMetadataObj->id, $userTeamsData->getXmlFormatted());
			}
			catch (Exception $e)
			{
				KalturaLog::err('An error occurred attempting to save the user metadata. ' . $e->getMessage());
			}
		}
	}

	protected function updateDropFolderFile (KalturaDropFolderFile $currentDropFolderFile, array $driveItem)
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

	/**
	 * @param $metadataProfileId
	 * @return KalturaDetachedResponseProfile
	 * @throws KalturaClientException
	 */
	protected function constructResponseProfile($metadataProfileId)
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		$responseProfile->relatedProfiles = array();

		$metadataItemProfile = new KalturaDetachedResponseProfile();
		$metadataItemProfile->name = self::MS_GRAPH_METADATA;
		$metadataItemProfile->filter = new KalturaMetadataFilter();
		$metadataItemProfile->filter->metadataObjectTypeEqual = KalturaMetadataObjectType::USER;
		$metadataItemProfile->filter->metadataProfileIdEqual = $metadataProfileId;

		$metadataItemProfile->mappings = array();
		$mapping = new KalturaResponseProfileMapping();
		$mapping->parentProperty = 'id';
		$mapping->filterProperty = 'objectIdEqual';
		$metadataItemProfile->mappings[] = $mapping;
		$responseProfile->relatedProfiles[] = $metadataItemProfile;

		return $responseProfile;
	}

	protected function getUpdatedFileSize (KalturaDropFolderFile $dropFolderFile)
	{
		if (isset($this->singleRunFoundItems[$dropFolderFile->fileName]))
		{
			$item = $this->singleRunFoundItems[$dropFolderFile->fileName];
			return $item[MicrosoftGraphFieldNames::SIZE];
		}

		return null;
	}

	protected function retrieveUserId ($creatorInfo)
	{
		$userInfo = $creatorInfo[MicrosoftGraphFieldNames::USER];

		if (isset($userInfo[MicrosoftGraphFieldNames::EMAIL]))
		{
			// find user by email and return id if found
			$filter = new KalturaUserFilter();
			$filter->emailStartsWith = $userInfo[MicrosoftGraphFieldNames::EMAIL];
			$response = KBatchBase::$kClient->user->listAction($filter);
			if ($response->totalCount > 0)
			{
				return $response->objects[0]->id;
			}

			// else return email
			return $userInfo[MicrosoftGraphFieldNames::EMAIL];
		}

		return $userInfo[MicrosoftGraphFieldNames::ID_FIELD];
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