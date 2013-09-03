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
		$dropFolderFiles = $this->loadDropFolderFiles();
		
		if (!count($physicalFiles))
		{
			KalturaLog::info('No new files to handle at this time');			
			return;
		}
		
		foreach ($physicalFiles as $physicalFile)
		{
			/* @var $physicalFile WebexXmlEpRecordingType */
			$physicalFileName = $physicalFile->getName() . '_' . $physicalFile->getRecordingID();
			if(!array_key_exists($physicalFileName, $dropFolderFilesMap))
			{
				$this->handleFileAdded ($physicalFile);
			}
		}
		
	}
	
	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::$kClient->impersonate ($job->partnerId);
		
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
	}
	
	protected function listRecordings ()
	{
		$securityContext = new WebexXmlSecurityContext();
		$securityContext->setUid($this->dropFolder->webexUserId); // webex username
		$securityContext->setPwd($this->dropFolder->webexPassword); // webex password
		$securityContext->setSid($this->dropFolder->webexSiteId); // webex site id
		$securityContext->setPid($this->dropFolder->webexPartnerId); // webex partner id
		
		$listRecordingRequest = new WebexXmlListRecordingRequest();
		if($this->dropFolder->incremental)
		{
			$createTimeScope = new WebexXmlEpCreateTimeScopeType();
			$createTimeScope->setCreateTimeStart(date('m/j/Y H:i:s', $this->dropFolder->lastFileTimestamp));
			$createTimeScope->setCreateTimeEnd(date('m/j/Y H:i:s'));
			$listRecordingRequest->setCreateTimeScope($createTimeScope);
		}
			
		$xmlClient = new WebexXmlClient($this->dropFolder->webexServiceUrl, $securityContext);
		$listRecordingResponse = $xmlClient->send($listRecordingRequest);
		
		return $listRecordingResponse;
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
			//No such thing as an 'uploading' webex drop folder file - if the file is detected, it is ready for upload. Immediately update status to 'pending'
			KBatchBase::$kClient->startMultiRequest();
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
			$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
			$result = KBatchBase::$kClient->doMultiRequest();
			
			return $result[1];
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$fileName.'] - '.$e->getMessage());
			return null;
		}
	}

	protected function addAsNewContent (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		$resource = $this->getIngestionResource($job, $data);
		$newEntry = new KalturaBaseEntry();
		$newEntry->conversionProfileId = $data->conversionProfileId;
		$newEntry->name = $data->parsedSlug;
		$newEntry->referenceId = $data->parsedSlug;
			
		KBatchBase::$kClient->startMultiRequest();
		$addedEntry = KBatchBase::$kClient->baseEntry->add($newEntry, null);
		KBatchBase::$kClient->baseEntry->addContent($addedEntry->id, $resource);
		$result = KBatchBase::$kClient->doMultiRequest();
	}
}
