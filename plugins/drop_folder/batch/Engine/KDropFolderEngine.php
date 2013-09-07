<?php
/**
 * 
 */
abstract class KDropFolderEngine
{
	protected $dropFolder;
	
	protected $dropFolderPlugin;
	
	protected $dropFolderFileService;
	
	public function __construct ()
	{
		$this->dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		$this->dropFolderFileService = $this->dropFolderPlugin->dropFolderFile;
	}
	
	public static function getInstance ($dropFolderType)
	{
		switch ($dropFolderType) {
			case KalturaDropFolderType::FTP:
			case KalturaDropFolderType::SFTP:
			case KalturaDropFolderType::LOCAL:
				return new KDropFolderFileTransferEngine ();
				break;
			
			default:
				return KalturaPluginManager::loadObject('KDropFolderEngine', $dropFolderType);
				break;
		}
	}
	
	abstract public function watchFolder (KalturaDropFolder $dropFolder);
	
	abstract public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data);
	
	/**
	 * Load all the files from the database that their status is not PURGED, PARSED or DETECTED
	 * @param KalturaDropFolder $folder
	 */
	protected function loadDropFolderFiles()
	{
		$dropFolderFilesMap = array();
		$dropFolderFiles =null;
		
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$dropFolderFileFilter->statusNotIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;	

		do
		{
			$pager->pageIndex++;
			KalturaLog::debug('getting page ['.$pager->pageIndex. '] from Drop Folder File ');
			$dropFolderFiles = $this->dropFolderFileService->listAction($dropFolderFileFilter, $pager);
			KalturaLog::debug('response: '. print_r($dropFolderFiles, true));
			$dropFolderFiles = $dropFolderFiles->objects;
			foreach ($dropFolderFiles as $dropFolderFile) 
			{
				$dropFolderFilesMap[$dropFolderFile->fileName] = $dropFolderFile;
			}
		}while (count($dropFolderFiles) >= $pager->pageSize);
			
		return $dropFolderFilesMap;
	}

	/**
 	 * Update drop folder entity with error
	 * @param int $dropFolderFileId
	 * @param int $errorStatus
	 * @param int $errorCode
	 * @param string $errorMessage
	 * @param Exception $e
	 */
	protected function handleFileError($dropFolderFileId, $errorStatus, $errorCode, $errorMessage, Exception $e = null)
	{
		try 
		{
			if($e)
				KalturaLog::err('Error for drop folder file with id ['.$dropFolderFileId.'] - '.$e->getMessage());
			else
				KalturaLog::err('Error for drop folder file with id ['.$dropFolderFileId.'] - '.$errorMessage);
			
			$updateDropFolderFile = new KalturaDropFolderFile();
			$updateDropFolderFile->errorCode = $errorCode;
			$updateDropFolderFile->errorDescription = $errorMessage;
			$this->dropFolderFileService->update($dropFolderFileId, $updateDropFolderFile);
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, $errorStatus);				
		}
		catch (KalturaException $e) 
		{
			KalturaLog::err('Cannot set error details for drop folder file id ['.$dropFolderFileId.'] - '.$e->getMessage());
			return null;
		}
	}
	
	/**
	 * Mark file status as PURGED
	 * @param int $dropFolderFileId
	 */
	protected function handleFilePurged($dropFolderFileId)
	{
		KalturaLog::debug('Handling drop folder file purged id ['.$dropFolderFileId.']');
		try 
		{
			return $this->dropFolderFileService->updateStatus($dropFolderFileId, KalturaDropFolderFileStatus::PURGED);
		}
		catch(Exception $e)
		{
			$this->handleFileError($dropFolderFileId, KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::ERROR_UPDATE_FILE, 
									DropFolderPlugin::ERROR_UPDATE_FILE_MESSAGE, $e);
			
			return null;
		}		
	}
	
	/**
	 * Retrieve all the relevant drop folder files according to the list of id's passed on the job data.
	 * Create resource object based on the conversion profile as an input to the ingestion API
	 * @param KalturaBatchJob $job
	 * @param KalturaDropFolderContentProcessorJobData $data
	 */
	protected function getIngestionResource(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		$filter = new KalturaDropFolderFileFilter();
		$filter->idIn = $data->dropFolderFileIds;
		$dropFolderFiles = $this->dropFolderFileService->listAction($filter); 
		
		$resource = null;
		if($dropFolderFiles->totalCount == 1 && is_null($dropFolderFiles->objects[0]->parsedFlavor)) //only source is ingested
		{
			$resource = new KalturaDropFolderFileResource();
			$resource->dropFolderFileId = $dropFolderFiles->objects[0]->id;			
		}
		else //ingest all the required flavors
		{			
			$fileToFlavorMap = array();
			foreach ($dropFolderFiles->objects as $dropFolderFile) 
			{
				$fileToFlavorMap[$dropFolderFile->parsedFlavor] = $dropFolderFile->id;			
			}
			
			$assetContainerArray = array();
		
			$assetParamsFilter = new KalturaConversionProfileAssetParamsFilter();
			$assetParamsFilter->conversionProfileIdEqual = $data->conversionProfileId;
			$assetParamsList = KBatchBase::$kClient->conversionProfileAssetParams->listAction($assetParamsFilter);
			foreach ($assetParamsList->objects as $assetParams)
			{
				if(array_key_exists($assetParams->systemName, $fileToFlavorMap))
				{
					$assetContainer = new KalturaAssetParamsResourceContainer();
					$assetContainer->assetParamsId = $assetParams->assetParamsId;
					$assetContainer->resource = new KalturaDropFolderFileResource();
					$assetContainer->resource->dropFolderFileId = $fileToFlavorMap[$assetParams->systemName];
					$assetContainerArray[] = $assetContainer;				
				}			
			}		
			$resource = new KalturaAssetsParamsResourceContainers();
			$resource->resources = $assetContainerArray;
		}
		return $resource;		
	}

}
