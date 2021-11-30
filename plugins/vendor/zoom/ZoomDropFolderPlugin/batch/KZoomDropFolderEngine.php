<?php
/**
 * @package plugins.ZoomDropFolder
 */
class KZoomDropFolderEngine extends KDropFolderFileTransferEngine
{
	const DEFAULT_ZOOM_QUERY_TIMERANGE = 259200; // 3 days
	const MAX_DATE_RANGE_DAYS = 14;
	const ONE_DAY = 86400;
	const HOUR = 3600;
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
	const MP4 = 'MP4';
	const M4A = 'M4A';
	
	/**
	 * @var kZoomClient
	 */
	protected $zoomClient;

	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->zoomClient = $this->initZoomClient($dropFolder);
		$this->dropFolder = $dropFolder;
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');
		$meetingFilesOrdered = $this->getMeetingsInStartTimeOrder();
		$dropFolderFilesMap = $this->loadDropFolderFiles(self::DEFAULT_ZOOM_QUERY_TIMERANGE);
		if ($meetingFilesOrdered)
		{
			$this->handleMeetingFiles($meetingFilesOrdered, $dropFolderFilesMap);
			$lastHandledMeetingTime = $this->getLastHandledMeetingTime($meetingFilesOrdered);
			if(($this->dropFolder->lastHandledMeetingTime >= $lastHandledMeetingTime) && ($lastHandledMeetingTime + self::ONE_DAY <= time()))
			{
				$lastHandledMeetingTime += self::ONE_DAY;
			}
			self::updateDropFolderLastMeetingHandled($lastHandledMeetingTime);
		}
		else
		{
			KalturaLog::info('No new files to handle at this time');
			if ($this->dropFolder->lastHandledMeetingTime + self::ONE_DAY <= time())
			{
				self::updateDropFolderLastMeetingHandled($this->dropFolder->lastHandledMeetingTime + self::ONE_DAY);
			}
		}
		
		foreach ($dropFolderFilesMap as $recordingFileName => $dropFolderFile)
		{
			$this->handleExistingDropFolderFile($dropFolderFile);
		}
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
		return new kZoomClient($dropFolder->baseURL, $jwtToken, $refreshToken, $clientId, $clientSecret, $accessToken);
	}
	
	protected function getMeetingsInStartTimeOrder()
	{
		$fromInSec  = $this->dropFolder->lastHandledMeetingTime;
		if($fromInSec)
		{
			if($fromInSec > time() - self::DEFAULT_ZOOM_QUERY_TIMERANGE)
			{
				$fromInSec = max($fromInSec - self::ONE_DAY, time() - self::DEFAULT_ZOOM_QUERY_TIMERANGE);
			}
		}
		else
		{
			$fromInSec = time() - self::MAX_DATE_RANGE_DAYS * self::ONE_DAY;
		}

		$toInSec = min(time(), $fromInSec + self::ONE_DAY);
		$from = date('Y-m-d', $fromInSec);
		$to = date('Y-m-d', $toInSec);
		$nextPageToken = '';
		$pageSize = self::MAX_PAGE_SIZE;
		$pageIndex = 0;
		$meetingFilesByStartTime = array();
		do
		{
			$resultZoomList = $this->zoomClient->listRecordings(self::ME, $from, $to, $nextPageToken, $pageSize);
			$meetingFiles = $this->getMeetings($resultZoomList);
			if (!$meetingFiles)
			{
				break;
			}
			foreach ($meetingFiles as $meetingFile)
			{
				$meetingsStartTime = kTimeZoneUtils::strToZuluTime($meetingFile[self::START_TIME]);
				$meetingFilesByStartTime[$meetingsStartTime] = $meetingFile;
			}
			
			$pageIndex++;
			$nextPageToken = $resultZoomList && $resultZoomList[self::NEXT_PAGE_TOKEN] ?
				$resultZoomList[self::NEXT_PAGE_TOKEN] : '';
			
		} while ($nextPageToken !== '' && $pageIndex < 10);
		
		ksort($meetingFilesByStartTime);
		return $meetingFilesByStartTime;
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

	protected function handleMeetingFiles($meetingFiles, &$dropFolderFilesMap)
	{
		foreach ($meetingFiles as $meetingFile)
		{
			if($this->getEntryByReferenceId(zoomProcessor::ZOOM_PREFIX . $meetingFile[self::UUID]))
			{
				KalturaLog::debug('found entry with old reference id - continue to the next meeting');
				continue;
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
			$recordingFilesOrdered = ZoomHelper::orderRecordingFiles($meetingFile[self::RECORDING_FILES], self::RECORDING_START,
			                                                         self::RECORDING_TYPE);
			KalturaLog::debug('recording files ordered are: ' . print_r($recordingFilesOrdered, true));
			foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
			{
				$parentEntry = null;
				$this->handleAudioFiles($recordingFilesPerTimeSlot, $meetingFile[self::UUID]);
				foreach ($recordingFilesPerTimeSlot as $recordingFile)
				{
					$recordingFileName = $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM;
					$dropFolderFilesMap = $this->loadDropFolderFiles(self::DEFAULT_ZOOM_QUERY_TIMERANGE);
					if (!array_key_exists($recordingFileName, $dropFolderFilesMap))
					{
						if ($recordingFile[self::RECORDING_FILE_TYPE] === self::TRANSCRIPT && isset($this->dropFolder->zoomVendorIntegration->enableZoomTranscription) &&
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
								else if ($recordingFile[self::RECORDING_FILE_TYPE] !== self::TRANSCRIPT)
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
					else
					{
						$dropFolderFile = $dropFolderFilesMap[$recordingFileName];
						unset($dropFolderFilesMap[$recordingFileName]);
						$this->handleExistingDropFolderFile($dropFolderFile);
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
				if ($dropFolderFile->recordingFile->fileType == KalturaRecordingFileType::TRANSCRIPT)
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
