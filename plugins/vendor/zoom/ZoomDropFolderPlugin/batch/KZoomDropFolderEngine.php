<?php
/**
 * @package plugins.ZoomDropFolder
 */
class KZoomDropFolderEngine extends KDropFolderFileTransferEngine
{
	const MAX_DATE_RANGE_DAYS = 14;
	const ONE_DAY = 86400;
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
	const RECORDING_FILE_TYPE = 'file_type';
	const NEXT_PAGE_TOKEN = 'next_page_token';
	const ME = 'me';
	
	/**
	 * @var kZoomClient
	 */
	protected $zoomClient;
	
	protected static $lastHandledMeetingTime;
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->zoomClient = new kZoomClient($this->dropFolder->baseURL, $this->dropFolder->jwtToken, null, null, null,
		                                    $this->dropFolder->accessToken);
		$this->dropFolder = $dropFolder;
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');
		$meetingFilesOrdered = $this->getMeetingsInStartTimeOrder();
		$dropFolderFilesMap = $this->loadDropFolderFiles();
		if ($meetingFilesOrdered)
		{
			$this->handleMeetingFiles($meetingFilesOrdered, $dropFolderFilesMap);
		}
		else
		{
			KalturaLog::info('No new files to handle at this time');
		}
		
		foreach ($dropFolderFilesMap as $dropFolderFile)
		{
			$this->handleFilePurged($dropFolderFile->id);
		}
		
		if (self::$lastHandledMeetingTime)
		{
			self::updateDropFolderLastMeetingHandled(self::$lastHandledMeetingTime);
		}
	}
	
	protected function getMeetingsInStartTimeOrder()
	{
		$lastHandledDate = $this->dropFolder->lastHandledMeetingTime ? date('Y-m-d', $this->dropFolder->lastHandledMeetingTime) : 0;
		$from = $lastHandledDate ? $lastHandledDate : date('Y-m-d',time() - self::MAX_DATE_RANGE_DAYS * self::ONE_DAY);
		$to = date('Y-m-d', time());
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
				$meetingsStartTime = self::convertTimeToUnix($meetingFile[self::START_TIME]);
				$meetingFilesByStartTime[$meetingsStartTime] = $meetingFile;
			}
			
			$pageIndex++;
			$nextPageToken = $resultZoomList && $resultZoomList[self::NEXT_PAGE_TOKEN] ?
				$resultZoomList[self::NEXT_PAGE_TOKEN] : '';
			
		} while ($nextPageToken !== '' && $pageIndex < 10);
		
		return ksort($meetingFilesByStartTime);
	}
	
	protected function getMeetings($resultZoomList)
	{
		$meetings = json_decode($resultZoomList, true)[self::MEETINGS];
		if ($meetings)
		{
			KalturaLog::log('Found ['.count($meetings).'] in the folder');
		}
		else
		{
			KalturaLog::info('No physical files found for drop folder id ['.$this->dropFolder->id.']');
			$meetings = array();
		}
		
		KalturaLog::info("physical files: ");
		foreach ($meetings as $meeting)
		{
			KalturaLog::info('Meeting UUID: '. $meeting[self::UUID]);
		}
		return $meetings;
	}

	protected function handleMeetingFiles($meetingFiles, $dropFolderFilesMap)
	{
		foreach ($meetingFiles as $meetingFile)
		{
			KalturaLog::debug("meeting file is: " . print_r($meetingFile, true));
			foreach ($meetingFile[self::RECORDING_FILES] as $recordingFile)
			{
				$recordingFileName = $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM;
				if (!array_key_exists($recordingFileName, $dropFolderFilesMap) &&
					ZoomHelper::shouldHandleFileType($recordingFile[self::RECORDING_FILE_TYPE]))
				{
					$this->addDropFolderFile($meetingFile, $recordingFile);
					self::$lastHandledMeetingTime = self::convertTimeToUnix($meetingFile[self::START_TIME]);
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

	protected function updateDropFolderLastMeetingHandled($lastHandledMeetingTime)
	{
		$updateDropFolder = new KalturaZoomDropFolder();
		$updateDropFolder->lastHandledMeetingTime = $lastHandledMeetingTime;
		try
		{
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
			KalturaLog::debug('Last handled meetings time is: '. $lastHandledMeetingTime);
		}
		catch(Exception $e)
		{
			KalturaLog::debug('Cannot update drop folder with last Meeting Handled Time - '.$e->getMessage());
			return null;
		}
	}

	protected function addDropFolderFile($meetingFile, $recordingFile)
	{
		try
		{
			$kMeetingMetaData = new kalturaMeetingMetadata();
			$kMeetingMetaData->meetingId = $meetingFile[self::ID];
			$kMeetingMetaData->uuid = $meetingFile[self::UUID];
			$kMeetingMetaData->topic = $meetingFile[self::TOPIC];
			$kMeetingMetaData->meetingStartTime = self::convertTimeToUnix($meetingFile[self::START_TIME]);
			$kMeetingMetaData->accountId = $meetingFile[self::ACCOUNT_ID];
			$kMeetingMetaData->hostId = $meetingFile[self::HOST_ID];
			$kMeetingMetaData->type = $meetingFile[self::TYPE];
			$recording = new kZoomRecording();
			$recording->parseType($meetingFile[self::TYPE]);
			$kMeetingMetaData->type = $recording->recordingType;
			
			$kRecordingFile = new kalturaRecordingFile();
			$kRecordingFile->id = $recordingFile[self::ID]->id;
			$kRecordingFile->downloadUrl = $recordingFile[self::DOWNLOAD_URL];
			$kRecordingFile->recordingStart = self::convertTimeToUnix($recordingFile[self::RECORDING_START]);
			$recordingFile = new kZoomRecordingFile();
			$recordingFile->parseFileType($recordingFile[self::RECORDING_FILE_TYPE]);
			$kRecordingFile->fileType = $recordingFile->recordingFileType;
			
			$zoomDropFolderFile = new KalturaZoomDropFolderFile();
			$zoomDropFolderFile->dropFolderId = $this->dropFolder->id;
			$zoomDropFolderFile->fileName = $meetingFile[self::UUID] . '_' . $recordingFile[self::ID] . ZoomHelper::SUFFIX_ZOOM;
			$zoomDropFolderFile->fileSize = $recordingFile[self::FILE_SIZE];
			$zoomDropFolderFile->meetingMetadata = $kMeetingMetaData;
			$zoomDropFolderFile->recordingFile = $kRecordingFile;

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

	protected function convertTimeToUnix($time)
	{
		$newTime = str_replace(array('T','Z'),array(' ',''),$time);
		return strtotime($newTime);
	}
	
	protected function handleExistingDropFolderFile (KalturaDropFolderFile $dropFolderFile)
	{
		try
		{
			$fullPath = $dropFolderFile->fileName;
			$fileSize = $this->zoomClient->getFileSize($dropFolderFile->meetingMetadata->uuid, $dropFolderFile->recordingFile->id);
		}
		catch (Exception $e)
		{
			$closedStatuses = array(
				KalturaDropFolderFileStatus::HANDLED,
				KalturaDropFolderFileStatus::PURGED,
				KalturaDropFolderFileStatus::DELETED
			);
			
			//In cases drop folder is not configured with auto delete we want to verify that the status file is not in one of the closed statuses so
			//we won't update it to error status
			if(!in_array($dropFolderFile->status, $closedStatuses))
			{
				KalturaLog::err('Failed to get file size for file ['.$fullPath.']');
				$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE,
				                       DropFolderPlugin::ERROR_READING_FILE_MESSAGE. '['.$fullPath.']', $e);
			}
			return false;
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
	}
	
	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate($job->partnerId);
		
		/* @var KalturaZoomDropFolderFile $dropFolderFile*/
		$dropFolderFile = $this->dropFolderFileService->get($data->dropFolderId);
		$zoomBaseUrl = $this->dropFolder->baseURL;
		
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$matchedEntry = $this->getEntryByReferenceId($dropFolderFile);
				if ($matchedEntry)
				{
					if ($dropFolderFile->recordingFile->fileType == kRecordingFileType::TRANSCRIPT)
					{
						$transcriptProcessor = new zoomTranscriptProcessor($zoomBaseUrl, $this->dropFolder);
						$transcriptProcessor->handleRecordingTranscriptComplete($dropFolderFile);
					}
					else if (in_array($dropFolderFile->recordingFile->fileType, array(kRecordingFileType::VIDEO, kRecordingFileType::CHAT)))
					{
						$zoomRecordingProcessor = new zoomMeetingProcessor($zoomBaseUrl, $this->dropFolder);
						$zoomRecordingProcessor->mainEntry = $matchedEntry;
						$zoomRecordingProcessor->handleRecordingVideoComplete($dropFolderFile);
					}
				}
				else if ($dropFolderFile->recordingFile->fileType == kRecordingFileType::VIDEO)
				{
					$zoomRecordingProcessor = new zoomMeetingProcessor($zoomBaseUrl, $this->dropFolder);
					$zoomRecordingProcessor->handleRecordingVideoComplete($dropFolderFile);
				}
				break;
			default:
				throw new kApplicativeException(KalturaDropFolderErrorCode::CONTENT_MATCH_POLICY_UNDEFINED, 'No content match policy is defined for drop folder');
				break;
		}
		
		KBatchBase::unimpersonate();
	}
	
	protected function getEntryByReferenceId(KalturaZoomDropFolderFile $dropFolderFile)
	{
		try
		{
			$entryFilter = new KalturaBaseEntryFilter();
			$entryFilter->referenceIdEqual = zoomProcessor::ZOOM_PREFIX . $dropFolderFile->meetingMetadata->uuid;
			
			$entryPager = new KalturaFilterPager();
			$entryPager->pageSize = 1;
			$entryPager->pageIndex = 1;
			$entryList = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $entryPager);
			
			if (is_array($entryList->objects) && isset($entryList->objects[0]) )
			{
				$result = $entryList->objects[0];
				if ($result->referenceId === zoomProcessor::ZOOM_PREFIX . $dropFolderFile->meetingMetadata->uuid)
				{
					return $result;
				}
			}
			
			return false;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to get entry by reference id: '. zoomProcessor::ZOOM_PREFIX . $dropFolderFile->meetingMetadata->uuid .
			$e->getMessage() );
			return false;
		}
	}
}