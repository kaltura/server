<?php
/**
 * 
 */
class KWebexDropFolderEngine extends KDropFolderEngine
{
	public function watchFolder (KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaWebexDropFolder */
		$this->dropFolder = $dropFolder;
		$physicalFiles = $this->listRecordings();
		KalturaLog::info('Recordings fetched: '.print_r($physicalFiles, true) );
		$dropFolderFilesMap = $this->loadDropFolderFiles();
		
		if (!count($physicalFiles))
		{
			KalturaLog::info('No new files to handle at this time');			
			return;
		}
		
		$maxTime = $this->dropFolder->lastFileTimestamp;
		foreach ($physicalFiles as $physicalFile)
		{
			/* @var $physicalFile WebexXmlEpRecordingType */
			$physicalFileName = $physicalFile->getName() . '_' . $physicalFile->getRecordingID();
			if(!array_key_exists($physicalFileName, $dropFolderFilesMap))
			{
				$this->handleFileAdded ($physicalFile);
				$maxTime = strtotime($physicalFile->getCreateTime()) > $maxTime ? strtotime($physicalFile->getCreateTime()) : $maxTime;
				KalturaLog::debug("new maxTime val: $maxTime");
			}
		}
		
		if ($this->dropFolder->incremental)
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->lastFileTimestamp = $maxTime;
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		}
		
	}
	
	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate ($job->partnerId);
		
		//In the case of the webex drop folder engine, the only possible contentMatch policy is ADD_AS_NEW.
		//Any other policy should cause an error.
		switch ($data->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$this->addAsNewContent($job, $data);
				break;
			default:
				//throw error
				break;
		}
		
		KBatchBase::unimpersonate();
	}
	
	protected function listRecordings ()
	{
		//1.ADD WHILE LOOP- GRAB ALL THE DROP FOLDER FILES (EVEN IF IT IS A LOT)
		
		//2. CALCUlate latest creation date
		
		KalturaLog::info('Fetching list of recordings from Webex');
		$securityContext = new WebexXmlSecurityContext();
		$securityContext->setUid($this->dropFolder->webexUserId); // webex username
		$securityContext->setPwd($this->dropFolder->webexPassword); // webex password
		$securityContext->setSid($this->dropFolder->webexSiteId); // webex site id
		$securityContext->setPid($this->dropFolder->webexPartnerId); // webex partner id
		
		$fileList = array();
		$startFrom = 1;
		do
		{
			$listControl = new WebexXmlEpListControlType();
			$listControl->setStartFrom($startFrom);
			$listRecordingRequest = new WebexXmlListRecordingRequest();
			$listRecordingRequest->setListControl($listControl);
			if($this->dropFolder->incremental)
			{
				$createTimeScope = new WebexXmlEpCreateTimeScopeType();
				$createTimeScope->setCreateTimeStart(date('m/j/Y H:i:s', $this->dropFolder->lastFileTimestamp));
				KalturaLog::debug($createTimeScope->getCreateTimeStart());
				$createTimeScope->setCreateTimeEnd(date('m/j/Y H:i:s'));
				KalturaLog::debug($createTimeScope->getCreateTimeEnd());
				$listRecordingRequest->setCreateTimeScope($createTimeScope);
			}
				
			$xmlClient = new WebexXmlClient($this->dropFolder->webexServiceUrl . '/' . $this->dropFolder->path, $securityContext);
			$listRecordingResponse = $xmlClient->send($listRecordingRequest);
			
			$fileList = array_merge($fileList, $listRecordingResponse->getRecording());
			$startFrom = $listRecordingResponse->getMatchingRecords()->getStartFrom();
		}while (count ($fileList) < $listRecordingResponse->getMatchingRecords()->getTotal());
		
		return $fileList;
	}
	
	protected function handleFileAdded (WebexXmlEpRecordingType $webexFile)
	{
		KalturaLog::debug('Add drop folder file ['.$webexFile->getName().'] last modification time ['.$webexFile->getCreateTime().'] file size ['.$webexFile->getSize().']');
		try 
		{
			$newDropFolderFile = new KalturaWebexDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
	    	$newDropFolderFile->fileName = $webexFile->getName() . '_' . $webexFile->getRecordingID();
	    	$newDropFolderFile->fileSize = $webexFile->getSize();
	    	$newDropFolderFile->lastModificationTime = $webexFile->getCreateTime(); 
			$newDropFolderFile->description = $webexFile->getDescription();
			$newDropFolderFile->confId = $webexFile->getConfID();
			$newDropFolderFile->recordingId = $webexFile->getRecordingID();
			$newDropFolderFile->webexHostId = $webexFile->getHostWebExID();
			$newDropFolderFile->contentUrl = $webexFile->getFileURL();
			KalturaLog::debug('content url '. $newDropFolderFile->contentUrl . ' file url: ' .$webexFile->getFileURL() );
			//No such thing as an 'uploading' webex drop folder file - if the file is detected, it is ready for upload. Immediately update status to 'pending'
			KBatchBase::$kClient->startMultiRequest();
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
			$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
			$result = KBatchBase::$kClient->doMultiRequest();
			
			return $result[1];
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$webexFile->getName() . '_' . $webexFile->getRecordingID().'] - '.$e->getMessage());
			return null;
		}
	}

	protected function addAsNewContent (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		/* @var $data KalturaWebexDropFolderContentProcessorJobData */
		$resource = $this->getIngestionResource($job, $data);
		$newEntry = new KalturaMediaEntry();
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $data->parsedSlug;
		$newEntry->description = $data->description;
		$newEntry->userId = $this->retrieveUserFromWebexHostId($data);
		$newEntry->referenceId = $data->parsedSlug;
			
		KBatchBase::$kClient->startMultiRequest();
		$addedEntry = KBatchBase::$kClient->media->add($newEntry, null);
		KBatchBase::$kClient->baseEntry->addContent($addedEntry->id, $resource);
		$result = KBatchBase::$kClient->doMultiRequest();
		
		if ($result [1] && $result[1] instanceof KalturaBaseEntry)
		{
			$entry = $result [1];
			$this->createCategoryAssociations ($data, $entry->userId, $entry->id);
		}
	}

	
	protected function retrieveUserFromWebexHostId (KalturaWebexDropFolderContentProcessorJobData $data)
	{
		if ($data->metadataProfileId && $data->webexHostIdMetadataFieldName && $data->webexHostId)
		{
			$filter = new KalturaUserFilter();
			$filter->advancedSearch = new KalturaMetadataSearchItem();
			$filter->advancedSearch->metadataProfileId = $data->metadataProfileId;
			$webexHostIdSearchCondition = new KalturaSearchCondition();
			$webexHostIdSearchCondition->field = $data->webexHostIdMetadataFieldName;
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
	
	protected function createCategoryAssociations (KalturaWebexDropFolderContentProcessorJobData $data, $userId, $entryId)
	{
		if ($data->metadataProfileId && $data->categoriesIdsMetadataFieldName)
		{
			$filter = new KalturaMetadataFilter();
			$filter->metadataProfileIdEqual = $data->metadataProfileId;
			$filter->objectIdEqual = $userId;
			$filter->metadataObjectTypeEqual = KalturaMetadataObjectType::USER;
			
			try
			{
				$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
				//Expect only one result
				$res = $metadataPlugin->metadata->listAction($filter, new KalturaFilterPager());
				$metadataObj = $res->objects[0];
				$xmlElem = new SimpleXMLElement($metadataObj->xml);
				$categoriesXPathRes = $xmlElem->xpath($data->categoriesIdsMetadataFieldName);
				
				$categories = strval($categoriesXPathRes[0]);
				$categoryFilter = new KalturaCategoryFilter();
				$categoryFilter->idIn = $categories;
				$categoryListResponse = KBatchBase::$kClient->category->listAction ($categoryFilter, new KalturaFilterPager());
				if ($categoryListResponse->objects && count($categoryListResponse->objects))
				{
					KBatchBase::$kClient->startMultiRequest();
					foreach ($categoryListResponse->objects as $category)
					{
						$categoryEntry = new KalturaCategoryEntry();
						$categoryEntry->entryId = $entryId;
						$categoryEntry->categoryId = $category->id;
						KBatchBase::$kClient->categoryEntry->add($categoryEntry);
					}
					KBatchBase::$kClient->doMultiRequest();
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err('Error encountered. Code: ['. $e->getCode() . '] Message: [' . $e->getMessage() . ']');
			}
		}
	}

}
