<?php
/**
 * @package plugins.ZoomDropFolder
 */
class KZoomDropFolderEngine extends KDropFolderFileTransferEngine
{
	const DEFAULT_ZOOM_QUERY_TIMERANGE = 259200; // 3 days
	const ONE_DAY = 86400;
	const HOUR = 3600;
	const ONE_MINUTE = 600;
	const MAX_PAGE_SIZE = 300;
	const MEETINGS = 'meetings';
	const RECORDING_FILES = 'recording_files';
	const UUID = 'uuid';
	const ID = 'id';
	const TOPIC = 'topic';
	const START_TIME = 'start_time';
	const ACCOUNT_ID = 'account_id';
	const HOST_ID = 'host_id';
	const TYPE = 'type';
	const DOWNLOAD_URL = 'download_url';
	const RECORDING_START = 'recording_start';
	const FILE_SIZE = 'file_size';
	const FILE_EXTENSION = 'file_extension';
	const RECORDING_FILE_TYPE = 'file_type';
	const RECORDING_TYPE = 'recording_type';
	const NEXT_PAGE_TOKEN = 'next_page_token';
	const ME = 'me';
	const TRANSCRIPT = 'TRANSCRIPT';
	const CC = 'CC';
	const MP4 = 'MP4';
	const M4A = 'M4A';
	
	/**
	 * @var kZoomClient
	 */
	protected $zoomClient;
	
	protected function getZoomParam($paramName, $default = 0)
	{
		$val = $default;
		if(KBatchBase::$taskConfig->params->zoom && KBatchBase::$taskConfig->params->zoom->$paramName)
		{
			$val = KBatchBase::$taskConfig->params->zoom->$paramName;
		}
		return $val;
	}
	
	protected function createZuluDateTime($timestamp)
	{
		$dateTime = new DateTime();
		$dateTime->setTimezone(new DateTimeZone("Zulu"));
		$dateTime->setTimestamp($timestamp);
		$dateTime->setTime(0, 0); // set time part to midnight
		return $dateTime;
	}
	
	protected function isDayInThePast($startRunTime, $timestamp)
	{
		$today = $this->createZuluDateTime($startRunTime);
		$lastDayScanned = $this->createZuluDateTime($timestamp);
		
		$diff = $today->diff( $lastDayScanned );
		$diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval
		
		return ($diffDays < 0);
	}
	
	protected function shouldAdvanceByDay($startRunTime, $fileInStatusProcessingExists)
	{
		/*
		- "lastHandledMeetingTime" should be interpreted as "dayToScan".
		- If we didn't scan today, but some day in the past, we might want to advance to the next day (might be today):
			- If all files from the day in the past were handled, advance.
			- Or if we're done waiting to some files to be completed.
		 */
		
		if( !$this->isDayInThePast($startRunTime, $this->dropFolder->lastHandledMeetingTime) )
		{
			return false;
		}
		
		$secondsFromMidnight = $startRunTime % self::ONE_DAY;
		$meetingGracePeriod = $this->getZoomParam('meetingGracePeriod');
		
		if($secondsFromMidnight <= $meetingGracePeriod)
		{
			KalturaLog::info("DropFolderId {$this->dropFolder->id}: A new day is here, but still waiting for new meetings to arrive");
			return false;
		}
		
		if($fileInStatusProcessingExists)
		{
			$fileProcessingGracePeriod = $this->getZoomParam('fileProcessingGracePeriod');
			if($secondsFromMidnight <= $fileProcessingGracePeriod)
			{
				KalturaLog::info("DropFolderId {$this->dropFolder->id}: A new day is here, but found files in status Processing. Waiting for status completed");
				return false;
			}
			KalturaLog::info("DropFolderId {$this->dropFolder->id}: ignoring files with status Processing");
		}
		
		KalturaLog::info("DropFolderId {$this->dropFolder->id}: Returning true");
		return true;
	}
	
	protected function handleExistingDropFolderFiles()
	{
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 0;
		$pager->pageSize = 500;
		if(KBatchBase::$taskConfig && KBatchBase::$taskConfig->params->pageSize)
		{
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		}
		
		$fromCreatedAt = time() - self::DEFAULT_ZOOM_QUERY_TIMERANGE;
		do
		{
			$pager->pageIndex++;
			$dropFolderFiles = $this->loadDropFolderFilesByPage($pager, $fromCreatedAt);
			foreach ($dropFolderFiles as $dropFolderFile)
			{
				$this->handleExistingDropFolderFile($dropFolderFile);
			}
			
		} while (count($dropFolderFiles) >= $pager->pageSize);
	}
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->zoomClient = $this->initZoomClient($dropFolder);
		$this->dropFolder = $dropFolder;
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');
		$startRunTime = time();
		$meetingFiles = $this->getMeetingsFromZoom();
		$fileInStatusProcessingExists = false;
		
		if ($meetingFiles)
		{
			$this->handleMeetingFiles($meetingFiles, $fileInStatusProcessingExists);
		}
		else
		{
			KalturaLog::info('No files to handle at this time');
		}
		
		if($this->shouldAdvanceByDay($startRunTime, $fileInStatusProcessingExists))
		{
			KalturaLog::info("Advancing DropFolderId {$this->dropFolder->id} in a day");
			$this->updateDropFolderLastMeetingHandled($this->dropFolder->lastHandledMeetingTime + self::ONE_DAY);
		}
		
		$this->handleExistingDropFolderFiles();
	}

	protected function refreshZoomClientTokens()
	{
		KalturaLog::debug("Going to refresh Zoom tokens");
		try
		{
			$this->dropFolder = $this->dropFolderService->get($this->dropFolder->id);
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error handling drop folder Id [" . $this->dropFolder->id . "] - could not refresh access token " . $e->getMessage());
			return false;
		}
		$this->zoomClient = $this->initZoomClient($this->dropFolder);
		return true;
	}

	protected function getLastHandledMeetingTime($meetingFilesOrdered)
	{
		$lastMeeting = end($meetingFilesOrdered);
		$lastHandledMeetingTime = kTimeZoneUtils::strToZuluTime($lastMeeting[self::START_TIME]);
		KalturaLog::info('Last meeting is: '. print_r($lastMeeting, true));
		KalturaLog::info('Last handled meeting time from DF is: '. $this->dropFolder->lastHandledMeetingTime);
		KalturaLog::info('Last meeting time'. $lastHandledMeetingTime);
		return $lastHandledMeetingTime;
	}

	protected function initZoomClient(KalturaDropFolder $dropFolder)
	{
		$jwtToken = isset($dropFolder->jwtToken) ? $dropFolder->jwtToken : null;
		$refreshToken = isset($dropFolder->refreshToken) ? $dropFolder->refreshToken : null;
		$clientId = isset($dropFolder->clientId) ? $dropFolder->clientId : null;
		$clientSecret = isset($dropFolder->clientSecret) ? $dropFolder->clientSecret : null;
		$accessToken = isset($dropFolder->accessToken) ? $dropFolder->accessToken : null;
		$accessExpiresIn = isset($dropFolder->accessExpiresIn) ? $dropFolder->accessExpiresIn : null;
		return new kZoomClient($dropFolder->baseURL, $jwtToken, $refreshToken, $clientId, $clientSecret, $accessToken, $accessExpiresIn);
	}
	
	protected function getMeetingsFromZoom()
	{
		$dayToScan = date('Y-m-d', $this->dropFolder->lastHandledMeetingTime);

		$pageSize = self::MAX_PAGE_SIZE;
		$maxMeetings = $this->getZoomParam('maxMeetings', 3000);
		$maxPages =  ceil($maxMeetings / $pageSize);
		
		$pageIndex = 0;
		$nextPageToken = '';
		$meetingFilesList = array();
		do
		{
			$resultZoomList = $this->zoomClient->listRecordings(self::ME, $dayToScan, $nextPageToken, $pageSize);
			$meetingFiles = $this->getMeetings($resultZoomList);
			if (!$meetingFiles)
			{
				break;
			}
			$meetingFilesList = array_merge($meetingFilesList, $meetingFiles);
			$pageIndex++;
			$nextPageToken = $resultZoomList && $resultZoomList[self::NEXT_PAGE_TOKEN] ?
				$resultZoomList[self::NEXT_PAGE_TOKEN] : '';
			
		} while ($nextPageToken !== '' && $pageIndex < $maxPages);

		return $meetingFilesList;
	}
	
	protected function getMeetings($resultZoomList)
	{
		$meetings = $resultZoomList[self::MEETINGS];
		if ($meetings)
		{
			KalturaLog::log('Found ['.count($meetings).'] in the folder');
		}
		else
		{
			KalturaLog::info('No physical files found for drop folder id ['.$this->dropFolder->id.']');
			$meetings = array();
		}
		
		KalturaLog::info('physical files: ');
		foreach ($meetings as $meeting)
		{
			KalturaLog::info('Meeting UUID: '. $meeting[self::UUID]);
		}
		return $meetings;
	}
	
	protected function handleMeetingFiles($meetingFiles, &$fileInStatusProcessingExists)
	{
		$groupParticipationType = $this->dropFolder->zoomVendorIntegration->groupParticipationType;
		$optInGroupNames = explode("\r\n", $this->dropFolder->zoomVendorIntegration->optInGroupNames);
		$optOutGroupNames = explode("\r\n", $this->dropFolder->zoomVendorIntegration->optOutGroupNames);
		foreach ($meetingFiles as $meetingFile)
		{
			if($this->getEntryByReferenceId(zoomProcessor::ZOOM_PREFIX . $meetingFile[self::UUID]))
			{
				KalturaLog::debug('found entry with old reference id - continue to the next meeting');
				continue;
			}
			$partnerId = $this->dropFolder->partnerId;
			if ($groupParticipationType != KalturaZoomGroupParticipationType::NO_CLASSIFICATION)
			{
				$userId = ZoomBatchUtils::getUserId($this->zoomClient, $partnerId, $meetingFile, $this->dropFolder->zoomVendorIntegration);
				if (!$userId)
				{
					KalturaLog::err('Could not find user');
					continue;
				}
				if (ZoomBatchUtils::shouldExcludeUserRecordingIngest($userId, $groupParticipationType, $optInGroupNames, $optOutGroupNames, $partnerId))
				{
					KalturaLog::debug('The user [' . $meetingFile[self::HOST_ID] . '] is configured to not save recordings - Not processing');
					continue;
				}
			}

			KalturaLog::debug('meeting file is: ' . print_r($meetingFile, true));
			$kZoomRecording = new kZoomRecording();
			$kZoomRecording->parseType($meetingFile[self::TYPE]);
			if (($kZoomRecording->recordingType == KalturaRecordingType::WEBINAR && !$this->dropFolder->zoomVendorIntegration->enableWebinarUploads)||
				($kZoomRecording->recordingType == KalturaRecordingType::MEETING && $this->dropFolder->zoomVendorIntegration->enableMeetingUpload === 0))
			{
				KalturaLog::debug('webinar uploads is disabled for vendor integration id: ' . $this->dropFolder->zoomVendorIntegration->id);
				continue;
			}
			$recordingFilesOrdered = ZoomHelper::orderRecordingFiles($meetingFile[self::RECORDING_FILES],
				self::RECORDING_START,
				self::RECORDING_TYPE,
				$fileInStatusProcessingExists);
			KalturaLog::debug('recording files ordered are: ' . print_r($recordingFilesOrdered, true));
			foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
			{
				$parentEntry = null;
				$this->handleAudioFiles($recordingFilesPerTimeSlot, $meetingFile[self::UUID]);
				foreach ($recordingFilesPerTimeSlot as $recordingFile)
				{
					$recordingFileName = $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM;
					$dropFolderFilesMap = $this->loadDropFolderFiles($recordingFileName);

					if (count($dropFolderFilesMap) === 0)
					{
						$isTranscript = in_array($recordingFile[self::RECORDING_FILE_TYPE], array(self::TRANSCRIPT, self::CC));
						if ($isTranscript && isset($this->dropFolder->zoomVendorIntegration->enableZoomTranscription) &&
							!$this->dropFolder->zoomVendorIntegration->enableZoomTranscription)
						{
							continue;
						}
						if (ZoomHelper::shouldHandleFileType($recordingFile[self::RECORDING_FILE_TYPE]))
						{
							if (!$parentEntry)
							{
								$parentEntry = $this->getEntryByReferenceId(zoomProcessor::ZOOM_PREFIX . $meetingFile[self::UUID] . $recordingFile[self::RECORDING_START]);
								if ($parentEntry)
								{
									$this->addDropFolderFile($meetingFile, $recordingFile, $parentEntry->id, false);
								}
								else if (!$isTranscript)
								{
									$parentEntry = $this->createEntry($meetingFile[self::UUID],
									                                  $this->dropFolder->zoomVendorIntegration->enableZoomTranscription, $recordingFile[self::RECORDING_START]);
									$this->addDropFolderFile($meetingFile, $recordingFile, $parentEntry->id, true);
								}
							}
							else
							{
								$this->addDropFolderFile($meetingFile, $recordingFile, $parentEntry->id, false);
							}
						}
					}
				}
			}
		}
	}
	
	protected function getEntryByReferenceId($referenceId)
	{
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->referenceIdEqual = $referenceId;
		$entryFilter->updatedAtGreaterThanOrEqual = time() - self::DEFAULT_ZOOM_QUERY_TIMERANGE;
		$entryFilter->statusNotIn = KalturaEntryStatus::DELETED . ',' . KalturaEntryStatus::ERROR_CONVERTING . ',' .
			KalturaEntryStatus::ERROR_IMPORTING;
		
		$entryPager = new KalturaFilterPager();
		$entryPager->pageSize = 1;
		$entryPager->pageIndex = 1;
		
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$entryList = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $entryPager);
		KBatchBase::unimpersonate();
		if (is_array($entryList->objects) && isset($entryList->objects[0]) )
		{
			return $entryList->objects[0];
		}
		return null;
	}
	
	protected function createEntry($uuid, $enableTranscriptionViaZoom, $recordingStartTime)
	{
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->referenceId = zoomProcessor::ZOOM_PREFIX . $uuid . $recordingStartTime;
		$newEntry->blockAutoTranscript = $enableTranscriptionViaZoom;
		$newEntry->conversionProfileId = $this->dropFolder->conversionProfileId;
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$entry = KBatchBase::$kClient->baseEntry->add($newEntry);
		KBatchBase::unimpersonate();
		return $entry;
	}

	protected function updateDropFolderLastMeetingHandled($lastHandledMeetingTime)
	{
		$updateDropFolder = new KalturaZoomDropFolder();
		$updateDropFolder->lastHandledMeetingTime = $lastHandledMeetingTime;
		$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		KalturaLog::debug('Last handled meetings time is: '. $lastHandledMeetingTime);
	}

	protected function handleAudioFiles(&$recordingFilesPerTimeSlot, $meetingFileUuid)
	{
		$foundMP4 = false;
		$audioKeys = array();
		foreach ($recordingFilesPerTimeSlot as $key => $recordingFile)
		{
			if ($recordingFile[self::RECORDING_FILE_TYPE] === self::MP4)
			{
				$foundMP4 = true;
			}
			if ($recordingFile[self::RECORDING_FILE_TYPE] === self::M4A)
			{
				$audioKeys[] = $key;
			}
		}
		if ($foundMP4)
		{
			foreach ($audioKeys as $audioKey)
			{
				$audioRecordingFile = $recordingFilesPerTimeSlot[$audioKey];
				KalturaLog::debug('Video and Audio files were found. audio file is ' . print_r($audioRecordingFile, true) . ' , unsetting Audio');
				unset($recordingFilesPerTimeSlot[$audioKey]);
			}
		}
	}

	protected function addDropFolderFile($meetingFile, $recordingFile, $parentEntryId, $isParentEntry = false)
	{
		try
		{
			$kMeetingMetaData = self::allocateMeetingMetaData($meetingFile);
			$kRecordingFile = self::allocateZoomRecordingFile($recordingFile);
			$zoomDropFolderFile = $this->allocateZoomDropFolderFile($meetingFile, $recordingFile, $kMeetingMetaData, $kRecordingFile, $parentEntryId,
			                                               $isParentEntry);

			KalturaLog::debug("Adding new ZoomDropFolderFile: " . print_r($zoomDropFolderFile, true));
			$dropFolderFile = $this->dropFolderFileService->add($zoomDropFolderFile);
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.
			                $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM .'] - '.$e->getMessage());
			return null;
		}
	}

	protected static function allocateMeetingMetaData($meetingFile)
	{
		$kMeetingMetaData = new kalturaZoomMeetingMetadata();
		$kMeetingMetaData->meetingId = $meetingFile[self::ID];
		$kMeetingMetaData->uuid = $meetingFile[self::UUID];
		$kMeetingMetaData->topic = $meetingFile[self::TOPIC];
		$kMeetingMetaData->meetingStartTime = kTimeZoneUtils::strToZuluTime($meetingFile[self::START_TIME]);
		$kMeetingMetaData->accountId = $meetingFile[self::ACCOUNT_ID];
		$kMeetingMetaData->hostId = $meetingFile[self::HOST_ID];
		$kZoomRecording = new kZoomRecording();
		$kZoomRecording->parseType($meetingFile[self::TYPE]);
		$kMeetingMetaData->type = $kZoomRecording->recordingType;
		return $kMeetingMetaData;
	}
	
	protected static function allocateZoomRecordingFile($recordingFile)
	{
		$kRecordingFile = new KalturaZoomRecordingFile();
		$kRecordingFile->id = $recordingFile[self::ID];
		$kRecordingFile->downloadUrl = $recordingFile[self::DOWNLOAD_URL];
		$kRecordingFile->fileExtension = $recordingFile[self::FILE_EXTENSION];
		$kRecordingFile->recordingStart = kTimeZoneUtils::strToZuluTime($recordingFile[self::RECORDING_START]);
		$kZoomRecordingFile = new kZoomRecordingFile();
		$kZoomRecordingFile->parseFileType($recordingFile[self::RECORDING_FILE_TYPE]);
		$kRecordingFile->fileType = $kZoomRecordingFile->recordingFileType;
		return $kRecordingFile;
	}
	
	protected function allocateZoomDropFolderFile($meetingFile, $recordingFile, $kMeetingMetaData, $kRecordingFile, $parentEntryId, $isParentEntry)
	{
		$zoomDropFolderFile = new KalturaZoomDropFolderFile();
		$zoomDropFolderFile->dropFolderId = $this->dropFolder->id;
		$zoomDropFolderFile->fileName = $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM;
		$zoomDropFolderFile->fileSize = $recordingFile[self::FILE_SIZE];
		$zoomDropFolderFile->meetingMetadata = $kMeetingMetaData;
		$zoomDropFolderFile->recordingFile = $kRecordingFile;
		$zoomDropFolderFile->parentEntryId = $parentEntryId;
		$zoomDropFolderFile->isParentEntry = $isParentEntry;
		return $zoomDropFolderFile;
	}
	
	protected function handleExistingDropFolderFile (KalturaDropFolderFile $dropFolderFile)
	{
		if($this->zoomClient->getAccessExpiresIn() && $this->zoomClient->getAccessExpiresIn() <= time() + self::ONE_MINUTE)
		{
			if(!$this->refreshZoomClientTokens())
			{
				return;
			}
		}
		
		$fileSize = $this->zoomClient->getFileSize($dropFolderFile->meetingMetadata->uuid, $dropFolderFile->recordingFile->id);
		if (!$fileSize)
		{
			KalturaLog::info('Current file size is empty');
			return;
		}
		
		if($dropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
		{
			$this->handleUploadingDropFolderFile($dropFolderFile, $fileSize, 0);
		}
		else
		{
			$deleteTime = $dropFolderFile->updatedAt + $this->dropFolder->autoFileDeleteDays*86400;
			if(($dropFolderFile->status == KalturaDropFolderFileStatus::HANDLED && $this->dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::MANUAL_DELETE && time() > $deleteTime) ||
				$dropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
			{
				$this->purgeFile($dropFolderFile);
			}
		}
	}
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
		$fullPath = $dropFolderFile->fileName;
		try
		{
			$this->zoomClient->deleteRecordingFile($dropFolderFile->meetingMetadata->uuid, $dropFolderFile->recordingFile->id);
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error when deleting drop folder file - ".$e->getMessage());
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileErrorCode::ERROR_DELETING_FILE,
			                       DropFolderPlugin::ERROR_DELETING_FILE_MESSAGE. '['.$fullPath.']');
		}
		$this->handleFilePurged($dropFolderFile->id);

		if($dropFolderFile->recordingFile->fileType == KalturaRecordingFileType::VIDEO)
		{
			$this->purgeAudioFiles($dropFolderFile->meetingMetadata->uuid);
		}
	}

	protected function purgeAudioFiles($meetingId)
	{
		try
		{
			$meetingRecordings = $this->zoomClient->getMeetingRecordings($meetingId);
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error when listing meeting files for meeting id [$meetingId]: " . $e->getMessage());
			return;
		}

		if (!$meetingRecordings || !isset($meetingRecordings[kZoomRecording::RECORDING_FILES]))
		{
			return;
		}

		$recordingFiles = $meetingRecordings[kZoomRecording::RECORDING_FILES];
		foreach ($recordingFiles as $recordingFile)
		{
			if ($recordingFile[self::RECORDING_FILE_TYPE] === self::M4A)
			{
				KalturaLog::debug('Deleting Audio File From Zoom, file ID: ' . $recordingFile[self::ID]);
				try
				{
					$this->zoomClient->deleteRecordingFile($meetingId, $recordingFile[self::ID]);
				}
				catch (Exception $e)
				{
					KalturaLog::err("Error when deleting audio file ID: " . $recordingFile[self::ID] . " Error: " . $e->getMessage());
				}
			}
		}
	}
	
	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate($job->partnerId);
		$dropFolderFileId = $data->dropFolderFileIds;
		/* @var KalturaZoomDropFolderFile $dropFolderFile*/
		$dropFolderFile = $this->dropFolderFileService->get($dropFolderFileId);

		/* @var KalturaZoomDropFolder $dropFolder */
		$dropFolder = $this->dropFolderPlugin->dropFolder->get($data->dropFolderId);
		if(!$dropFolder->zoomVendorIntegration)
		{
			throw new kExternalException(KalturaDropFolderErrorCode::MISSING_CONFIG, DropFolderPlugin::MISSING_CONFIG_MESSAGE);
		}

		$zoomBaseUrl = $dropFolder->baseURL;
		$entry = KBatchBase::$kClient->baseEntry->get($dropFolderFile->parentEntryId);
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:

				$isTranscript = in_array($dropFolderFile->recordingFile->fileType, array(KalturaRecordingFileType::TRANSCRIPT, KalturaRecordingFileType::CC));

				if ($isTranscript)
				{
					$transcriptProcessor = new zoomTranscriptProcessor($zoomBaseUrl, $dropFolder);
					$transcriptProcessor->handleRecordingTranscriptComplete($dropFolderFile, $entry);
					$this->updateDropFolderFile($dropFolderFile->parentEntryId , $dropFolderFile);
				}
				else if (in_array($dropFolderFile->recordingFile->fileType, array(KalturaRecordingFileType::VIDEO, KalturaRecordingFileType::AUDIO,
				                                                                  KalturaRecordingFileType::CHAT)))
				{
					if ($dropFolderFile->meetingMetadata->type == KalturaRecordingType::WEBINAR)
					{
						$zoomRecordingProcessor = new zoomWebinarProcessor($zoomBaseUrl, $dropFolder);
					}
					else
					{
						$zoomRecordingProcessor = new zoomMeetingProcessor($zoomBaseUrl, $dropFolder);
					}
					$zoomRecordingProcessor->mainEntry = $entry;
					$entry = $zoomRecordingProcessor->handleRecordingVideoComplete($dropFolderFile);
					$this->updateDropFolderFile($entry->id , $dropFolderFile);
				}
				break;
			default:
				throw new kApplicativeException(KalturaDropFolderErrorCode::CONTENT_MATCH_POLICY_UNDEFINED, 'No content match policy is defined for drop folder');
		}
		
		KBatchBase::unimpersonate();
	}
	
	function updateDropFolderFile($entryId , $dropFolderFile)
	{
		$kZoomDropFolderFile = new KalturaZoomDropFolderFile();
		$kZoomDropFolderFile->entryId = $entryId;
		$this->dropFolderFileService->update($dropFolderFile->id, $kZoomDropFolderFile);
		$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::HANDLED);
	}
}
