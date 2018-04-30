<?php
/**
 * 
 */
class KWebexDropFolderEngine extends KDropFolderEngine
{
	const ZERO_DATE = '12/31/1971 00:00:01';
	const ARF_FORMAT = 'ARF';
	const MAX_QUERY_DATE_RANGE_DAYS = 25; //Maximum querying date range is 28 days we define it as less than that
	private static $unsupported_file_formats = array('WARF');

	private $dropFolderFilesMap = null;
	/**
	 * Webex wrapper
	 * @var webexWrapper
	 */
	private $webexWrapper;

	/**
	 * @param $dropFolder KalturaWebexDropFolder
	 */
	public function setDropFolder($dropFolder)
	{
		$this->dropFolder = $dropFolder;
	}

	public function watchFolder (KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaWebexDropFolder */
		$this->dropFolder = $dropFolder;
		$this->webexWrapper = new webexWrapper($this->dropFolder->webexServiceUrl . '/' . $this->dropFolder->path, $this->getWebexClientSecurityContext(),
			array('KalturaLog', 'err'), array('KalturaLog', 'debug'));

		KalturaLog::info('Watching folder ['.$this->dropFolder->id.']');
		$startTime = null;
		$endTime = null;
		if ($this->dropFolder->incremental)
		{
			$startTime = time()-self::MAX_QUERY_DATE_RANGE_DAYS*86400;
			$pastPeriod = $this->getMaximumExecutionTime() ?  $this->getMaximumExecutionTime() : 3600;
			if ( $this->dropFolder->lastFileTimestamp && ( ($this->dropFolder->lastFileTimestamp - $pastPeriod) > (time()-self::MAX_QUERY_DATE_RANGE_DAYS*86400)) )
				$startTime = $this->dropFolder->lastFileTimestamp - $pastPeriod;
			
			$startTime = date('m/j/Y H:i:s', $startTime);
			$endTime = (date('m/j/Y H:i:s', time()+86400));
		}

		$result = $this->listAllRecordings($startTime, $endTime);
		if (!empty($result))
		{
			$this->HandleNewFiles($result);
		}
		else
		{
			KalturaLog::info('No new files to handle at this time');
		}

		if ($this->dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::MANUAL_DELETE)
		{
			$this->purgeFiles();
		}
	}

	private function getDropFolderFilesMap()
	{
		if(!$this->dropFolderFilesMap)
		{
			$this->dropFolderFilesMap = $this->loadDropFolderFiles();
		}

		return $this->dropFolderFilesMap;
	}

	/**
	 * @param $physicalFiles array
	 * @return kWebexHandleFilesResult
	 */
	public function HandleNewFiles($physicalFiles)
	{
		$result = new kWebexHandleFilesResult();
		$dropFolderFilesMap = $this->getDropFolderFilesMap();
		$maxTime = $this->dropFolder->lastFileTimestamp;
		foreach ($physicalFiles as $physicalFile)
		{
			/* @var $physicalFile WebexXmlEpRecordingType */
			$physicalFileName = $physicalFile->getName() . '_' . $physicalFile->getRecordingID();
			if (in_array($physicalFile->getFormat(),self::$unsupported_file_formats))
			{
				KalturaLog::info('Recording with id [' . $physicalFile->getRecordingID() . '] format [' . $physicalFile->getFormat() . '] is incompatible with the Kaltura conversion processes. Ignoring.');
				$result->addFileName(kWebexHandleFilesResult::FILE_NOT_ADDED_TO_DROP_FOLDER, $physicalFileName);
				continue;
			}

			if(!array_key_exists($physicalFileName, $dropFolderFilesMap))
			{
				if($this->handleFileAdded($physicalFile))
				{
					$maxTime = max(strtotime($physicalFile->getCreateTime()), $maxTime);
					KalturaLog::info("Added new file with name [$physicalFileName]. maxTime updated: $maxTime");
					$result->addFileName(kWebexHandleFilesResult::FILE_ADDED_TO_DROP_FOLDER, $physicalFileName);
				}
				else
					$result->addFileName(kWebexHandleFilesResult::FILE_NOT_ADDED_TO_DROP_FOLDER, $physicalFileName);
			}
			else //drop folder file entry found
			{
				$dropFolderFile = $dropFolderFilesMap[$physicalFileName];
				unset($dropFolderFilesMap[$physicalFileName]);
				if ($dropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING && $this->handleExistingDropFolderFile($dropFolderFile))
					$result->addFileName(kWebexHandleFilesResult::FILE_HANDLED, $physicalFileName);
				else
					$result->addFileName(kWebexHandleFilesResult::FILE_NOT_HANDLED, $physicalFileName);
			}
		}

		if ($this->dropFolder->incremental && $maxTime > $this->dropFolder->lastFileTimestamp)
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->lastFileTimestamp = $maxTime;
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		}

		return $result;
	}

	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate ($job->partnerId);
		
		/* @var $data KalturaWebexDropFolderContentProcessorJobData */
		$dropFolder = $this->dropFolderPlugin->dropFolder->get ($data->dropFolderId);
		//In the case of the webex drop folder engine, the only possible contentMatch policy is ADD_AS_NEW.
		//Any other policy should cause an error.
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$this->addAsNewContent($job, $data, $dropFolder);
				break;
			default:
				throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, 'Content match policy not allowed for Webex drop folders');
				break;
		}
		
		KBatchBase::unimpersonate();
	}

	protected function listRecordings ($startTime = null, $endTime = null, $startFrom = 1)
	{
		$dropFolderServiceTypes = $this->dropFolder->webexServiceType ? explode(',', $this->dropFolder->webexServiceType) :
			array(WebexXmlComServiceTypeType::_MEETINGCENTER);
		KalturaLog::info("Fetching list of recordings from Webex, startTime [$startTime], endTime [$endTime] of types ".print_r($dropFolderServiceTypes));
		$serviceTypes = webexWrapper::stringServicesTypesToWebexXmlArray($dropFolderServiceTypes);
		$result = $this->webexWrapper->listRecordings($serviceTypes, $startTime, $endTime, $startFrom);
		if($result)
		{
			$recording = $result->getRecording();
			KalturaLog::info('Recordings fetched: ' . print_r($recording, true));
		}

		return $result;
	}

	protected function listAllRecordings ($startTime = null, $endTime = null)
	{
		$dropFolderServiceTypes = $this->dropFolder->webexServiceType ? explode(',', $this->dropFolder->webexServiceType) :
			array(WebexXmlComServiceTypeType::_MEETINGCENTER);
		KalturaLog::info("Fetching list of all recordings from Webex, startTime [$startTime], endTime [$endTime] of types ".print_r($dropFolderServiceTypes));
		$serviceTypes = webexWrapper::stringServicesTypesToWebexXmlArray($dropFolderServiceTypes);
		$result = $this->webexWrapper->listAllRecordings($serviceTypes, $startTime, $endTime);
		KalturaLog::info('Recordings fetched: '.print_r($result, true) );
		return $result;
	}

	public function getWebexClientSecurityContext()
	{
		$securityContext = new WebexXmlSecurityContext();
		$securityContext->setUid($this->dropFolder->webexUserId); // webex username
		$securityContext->setPwd($this->dropFolder->webexPassword); // webex password
		$securityContext->setSiteName($this->dropFolder->webexSiteName); // webex partner id
		$securityContext->setSid($this->dropFolder->webexSiteId); // webex site id
		$securityContext->setPid($this->dropFolder->webexPartnerId); // webex partner id

		return $securityContext;
	}
	
	/**
	 * @throws Exception
	 */
	protected function purgeFiles ()
	{
		$createTimeEnd = date('m/j/Y H:i:s');
		$createTimeStart = date('m/j/Y H:i:s', time() - self::MAX_QUERY_DATE_RANGE_DAYS * 86400);
		if ($this->dropFolder->deleteFromTimestamp && $this->dropFolder->deleteFromTimestamp > (time() - self::MAX_QUERY_DATE_RANGE_DAYS * 86400))
			$createTimeStart = date('m/j/Y H:i:s', $this->dropFolder->deleteFromTimestamp);

		$result = $this->listAllRecordings($createTimeStart, $createTimeEnd);
		if($result)
		{
			KalturaLog::info("Files to delete: " . count($result));
			$dropFolderFilesMap = $this->getDropFolderFilesMap();
		}

		foreach ($result as $file)
		{
			$physicalFileName = $file->getName() . '_' . $file->getRecordingID();
			if (!$this->shouldPurgeFile($dropFolderFilesMap, $physicalFileName))
				continue;

			try
			{
				$this->webexWrapper->deleteRecordById($file->getRecordingID());
			}
			catch (Exception $e)
			{
				KalturaLog::err('Error occurred: ' . print_r($e, true));
				continue;
			}

			if ($this->dropFolder->deleteFromRecycleBin && !$this->deleteFileFromRecycleBin($file->getCreateTime()))
			{
				KalturaLog::err("File [$physicalFileName] could not be removed from recycle bin. Purge manually");
				continue;
			}

			KalturaLog::info("File [$physicalFileName] successfully purged. Purging drop folder file");
			$this->dropFolderFileService->updateStatus($dropFolderFilesMap[$physicalFileName]->id, KalturaDropFolderFileStatus::PURGED);
		}
	}

	/**
	 * @param string $fileCreateTime
	 * @throws Exception
	 */
	private function deleteFileFromRecycleBin($fileCreateTime)
	{
		$recordingArr = $this->webexWrapper->getRecordingsFromRecycleBinByCreationTime($fileCreateTime);
		$recordingId = $recordingArr[0]->getRecordingID();
		KalturaLog::info("Permanently deleting recording with webex ID: [$recordingId], recording: " . print_r($recordingArr[0], true));
		$response = $this->webexWrapper->deleteRecordFromRecycleBinById($recordingId);
		return $response->getSuccessfulRecordingsCount();
	}

	/**
	 * @param array $dropFolderFilesMap
	 * @param string $physicalFileName
	 * @return bool
	 */
	private function shouldPurgeFile($dropFolderFilesMap, $physicalFileName)
	{
		if (!array_key_exists($physicalFileName, $dropFolderFilesMap))
		{
			KalturaLog::info("File with name $physicalFileName not handled yet. Ignoring");
			return false;
		}

		$dropFolderFile = $dropFolderFilesMap[$physicalFileName];
		/* @var $dropFolderFile KalturaWebexDropFolderFile */
		if (!in_array($dropFolderFile->status, array(KalturaDropFolderFileStatus::HANDLED, KalturaDropFolderFileStatus::DELETED)))
		{
			KalturaLog::info("File with name $physicalFileName not in final status. Ignoring");
			return false;
		}

		$deleteTime = $dropFolderFile->updatedAt + $this->dropFolder->autoFileDeleteDays*86400;
		if (time() < $deleteTime)
		{
			KalturaLog::info("File with name $physicalFileName- not time to delete yet. Ignoring");
			return false;
		}

		return true;
	}
	
	protected function handleFileAdded (WebexXmlEpRecordingType $webexFile)
	{
		try 
		{
			$newDropFolderFile = new KalturaWebexDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
	    	$newDropFolderFile->fileName = $webexFile->getName() . '_' . $webexFile->getRecordingID();
	    	$newDropFolderFile->fileSize = WebexPlugin::getSizeFromWebexContentUrl($webexFile->getFileURL());
	    	$newDropFolderFile->lastModificationTime = $webexFile->getCreateTime(); 
			$newDropFolderFile->description = $webexFile->getDescription();
			$newDropFolderFile->confId = $webexFile->getConfID();
			$newDropFolderFile->recordingId = $webexFile->getRecordingID();
			$newDropFolderFile->webexHostId = $webexFile->getHostWebExID();
			$newDropFolderFile->contentUrl = $webexFile->getFileURL();
			KalturaLog::debug("Adding new WebexDropFolderFile: " . print_r($newDropFolderFile, true));
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
			
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$webexFile->getName() . '_' . $webexFile->getRecordingID().'] - '.$e->getMessage());
			return null;
		}
	}

	protected function handleExistingDropFolderFile (KalturaWebexDropFolderFile $dropFolderFile)
	{
		try
		{
			$updatedFileSize = WebexPlugin::getSizeFromWebexContentUrl($dropFolderFile->contentUrl);
		}
		catch (Exception $e)
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE,
					DropFolderPlugin::ERROR_READING_FILE_MESSAGE, $e);
			return null;
		}

		if (!$dropFolderFile->fileSize)
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_READING_FILE,
				DropFolderPlugin::ERROR_READING_FILE_MESSAGE . '[' . $dropFolderFile->contentUrl . ']');
		}
		else if ($dropFolderFile->fileSize < $updatedFileSize)
		{
			try
			{
				$updateDropFolderFile = new KalturaDropFolderFile();
				$updateDropFolderFile->fileSize = $updatedFileSize;

				return $this->dropFolderFileService->update($dropFolderFile->id, $updateDropFolderFile);
			}
			catch (Exception $e)
			{
				$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE,
					DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
				return null;
			}
		}
		else // file sizes are equal
		{
			$time = time();
			$fileSizeLastSetAt = $this->dropFolder->fileSizeCheckInterval + $dropFolderFile->fileSizeLastSetAt;

			KalturaLog::info("time [$time] fileSizeLastSetAt [$fileSizeLastSetAt]");

			// check if fileSizeCheckInterval time has passed since the last file size update
			if ($time > $fileSizeLastSetAt) {
				try {
					return $this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
				} catch (KalturaException $e) {
					$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE,
						DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
					return null;
				}
			}
		}
	}

	protected function addAsNewContent (KalturaBatchJob $job, KalturaWebexDropFolderContentProcessorJobData $data, KalturaWebexDropFolder $folder)
	{
		/* @var $data KalturaWebexDropFolderContentProcessorJobData */
		$resource = $this->getIngestionResource($job, $data);
		$newEntry = new KalturaMediaEntry();
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $data->parsedSlug;
		$newEntry->description = $data->description;
		$newEntry->userId = $data->parsedUserId ? $data->parsedUserId : $this->retrieveUserFromWebexHostId($data, $folder);
		$newEntry->creatorId = $newEntry->userId;
		$newEntry->referenceId = $data->parsedSlug;
			
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

	protected function retrieveUserFromWebexHostId (KalturaWebexDropFolderContentProcessorJobData $data, KalturaWebexDropFolder $folder)
	{
		if ($folder->metadataProfileId && $folder->webexHostIdMetadataFieldName && $data->webexHostId)
		{
			$filter = new KalturaUserFilter();
			$filter->advancedSearch = new KalturaMetadataSearchItem();
			$filter->advancedSearch->metadataProfileId = $folder->metadataProfileId;
			$webexHostIdSearchCondition = new KalturaSearchCondition();
			$webexHostIdSearchCondition->field = $folder->webexHostIdMetadataFieldName;
			$webexHostIdSearchCondition->value = $data->webexHostId;
			$filter->advancedSearch->items = array($webexHostIdSearchCondition);
			try
			{
				$result = KBatchBase::$kClient->user->listAction ($filter, new KalturaFilterPager());
				
				if ($result->totalCount)
				{
					$user = $result->objects[0];
					return $user->id;
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err('Error encountered. Code: ['. $e->getCode() . '] Message: [' . $e->getMessage() . ']');
			}

		}
		
		return $data->webexHostId;
	}
	
}
