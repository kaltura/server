<?php
/**
 * 
 */
class KWebexDropFolderEngine extends KDropFolderEngine implements IKalturaLogger
{
	const ZERO_DATE = '12/31/1971 00:00:01';
	
	public function watchFolder (KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaWebexDropFolder */
		$this->dropFolder = $dropFolder;
		KalturaLog::debug('Watching folder ['.$this->dropFolder->id.']');
		$physicalFiles = $this->listRecordings();
		KalturaLog::info('Recordings fetched: '.print_r($physicalFiles, true) );
		
		if (!count($physicalFiles))
		{
			KalturaLog::info('No new files to handle at this time');			
			return;
		}
		
		$dropFolderFilesMap = $this->loadDropFolderFiles();
		$maxTime = $this->dropFolder->lastFileTimestamp;
		foreach ($physicalFiles as $physicalFile)
		{
			/* @var $physicalFile WebexXmlEpRecordingType */
			$physicalFileName = $physicalFile->getName() . '_' . $physicalFile->getRecordingID();
			if(!array_key_exists($physicalFileName, $dropFolderFilesMap))
			{
				$this->handleFileAdded ($physicalFile);
				$maxTime = max(strtotime($physicalFile->getCreateTime()), $maxTime);
				KalturaLog::info("maxTime updated: $maxTime");
			}
		}
		
		if ($this->dropFolder->incremental && $maxTime > $this->dropFolder->lastFileTimestamp)
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->lastFileTimestamp = $maxTime;
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		}
		
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
	
	protected function listRecordings ()
	{
		KalturaLog::info('Fetching list of recordings from Webex');
		$securityContext = new WebexXmlSecurityContext();
		$securityContext->setUid($this->dropFolder->webexUserId); // webex username
		$securityContext->setPwd($this->dropFolder->webexPassword); // webex password
		$securityContext->setSid($this->dropFolder->webexSiteId); // webex site id
		$securityContext->setPid($this->dropFolder->webexPartnerId); // webex partner id
		
		$fileList = array();
		$startFrom = 1;
		try{
			
			do
			{
				$listControl = new WebexXmlEpListControlType();
				$listControl->setStartFrom($startFrom);
				$listRecordingRequest = new WebexXmlListRecordingRequest();
				$listRecordingRequest->setListControl($listControl);
				
				$servicesTypes = new WebexXmlArray('WebexXmlComServiceTypeType');
				$servicesTypes[] = new WebexXmlComServiceTypeType(WebexXmlComServiceTypeType::_MEETINGCENTER);
				$listRecordingRequest->setServiceTypes($servicesTypes);
	 			
				if($this->dropFolder->incremental)
				{
					$createTimeScope = new WebexXmlEpCreateTimeScopeType();
					$createTimeScope->setCreateTimeStart($this->dropFolder->lastFileTimestamp ? date('m/j/Y H:i:s', $this->dropFolder->lastFileTimestamp) :  self::ZERO_DATE);
					KalturaLog::debug($createTimeScope->getCreateTimeStart());
					//24 hours forward, so as not to run into problems with different timezones.
					$createTimeScope->setCreateTimeEnd(date('m/j/Y H:i:s', time()+86400));
					KalturaLog::debug($createTimeScope->getCreateTimeEnd());
					$listRecordingRequest->setCreateTimeScope($createTimeScope);
				}
				
				$xmlClient = new WebexXmlClient($this->dropFolder->webexServiceUrl . '/' . $this->dropFolder->path, $securityContext);
				$listRecordingResponse = $xmlClient->send($listRecordingRequest);
				
				$fileList = array_merge($fileList, $listRecordingResponse->getRecording());
				$startFrom = $listRecordingResponse->getMatchingRecords()->getStartFrom();
			}while (count ($fileList) < $listRecordingResponse->getMatchingRecords()->getTotal());
		}
		catch (Exception $e)
		{
			if ($e->getCode() != 15 && $e->getMessage() != 'Status: FAILURE, Reason: Sorry, no record found')
			{
				throw $e;
			}
		}
		
		
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

	protected function addAsNewContent (KalturaBatchJob $job, KalturaWebexDropFolderContentProcessorJobData $data, KalturaWebexDropFolder $folder)
	{
		/* @var $data KalturaWebexDropFolderContentProcessorJobData */
		$resource = $this->getIngestionResource($job, $data);
		$newEntry = new KalturaMediaEntry();
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $data->parsedSlug;
		$newEntry->description = $data->description;
		$newEntry->userId = $this->retrieveUserFromWebexHostId($data, $folder);
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
	
	protected function createCategoryAssociations (KalturaWebexDropFolder $folder, $userId, $entryId)
	{
		if ($folder->metadataProfileId && $folder->categoriesMetadataFieldName)
		{
			$filter = new KalturaMetadataFilter();
			$filter->metadataProfileIdEqual = $folder->metadataProfileId;
			$filter->objectIdEqual = $userId;
			$filter->metadataObjectTypeEqual = KalturaMetadataObjectType::USER;
			
			try
			{
				$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
				//Expect only one result
				$res = $metadataPlugin->metadata->listAction($filter, new KalturaFilterPager());
				$metadataObj = $res->objects[0];
				$xmlElem = new SimpleXMLElement($metadataObj->xml);
				$categoriesXPathRes = $xmlElem->xpath($folder->categoriesMetadataFieldName);
				
				$categories = strval($categoriesXPathRes[0]);
				$categoryFilter = new KalturaCategoryFilter();
				$categoryFilter->idIn = $categories;
				$categoryListResponse = KBatchBase::$kClient->category->listAction ($categoryFilter, new KalturaFilterPager());
				if ($categoryListResponse->objects && count($categoryListResponse->objects))
				{
					if (!$folder->enforceEntitlement)
					{
						//easy
						$this->createCategoryEntriesNoEntitlement ($categoryListResponse->objects, $entryId);
					}
					else {
						//write your will
						$this->createCategoryEntriesWithEntitlement ($categoryListResponse->objects, $entryId, $userId);
					}
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err('Error encountered. Code: ['. $e->getCode() . '] Message: [' . $e->getMessage() . ']');
			}
		}
	}

	private function createCategoryEntriesNoEntitlement (array $categoriesArr, $entryId)
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach ($categoriesArr as $category)
		{
			$categoryEntry = new KalturaCategoryEntry();
			$categoryEntry->entryId = $entryId;
			$categoryEntry->categoryId = $category->id;
			KBatchBase::$kClient->categoryEntry->add($categoryEntry);
		}
		KBatchBase::$kClient->doMultiRequest();
	}
	
	private function createCategoryEntriesWithEntitlement (array $categoriesArr, $entryId, $userId)
	{
		$partnerInfo = KBatchBase::$kClient->partner->get(KBatchBase::$kClientConfig->partnerId);
		
		$clientConfig = new KalturaConfiguration($partnerInfo->id);
		$clientConfig->serviceUrl = KBatchBase::$kClient->getConfig()->serviceUrl;
		$clientConfig->setLogger($this);
		$client = new KalturaClient($clientConfig);
		foreach ($categoriesArr as $category)
		{
			/* @var $category KalturaCategory */
			$ks = $client->generateSessionV2($partnerInfo->adminSecret, $userId, KalturaSessionType::ADMIN, $partnerInfo->id, 86400, 'enableentitlement,privacycontext:'.$category->privacyContext);
			$client->setKs($ks);
			$categoryEntry = new KalturaCategoryEntry();
			$categoryEntry->categoryId = $category->id;
			$categoryEntry->entryId = $entryId;
			$client->categoryEntry->add ($categoryEntry);
		}
	}
	
	function log($message)
	{
		KalturaLog::log($message);
	}
}
