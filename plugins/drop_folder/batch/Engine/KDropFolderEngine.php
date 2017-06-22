<?php
/**
 * 
 */
abstract class KDropFolderEngine implements IKalturaLogger
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
	 * @param KalturaFilterPager $pager
	 * @return array
	 */
	protected function loadDropFolderFilesByPage($pager)
	{
		$dropFolderFiles =null;

		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$dropFolderFileFilter->statusNotIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED;

		$dropFolderFiles = $this->dropFolderFileService->listAction($dropFolderFileFilter, $pager);
		return $dropFolderFiles->objects;
	}

	/**
	 * Load all the files from the database that their status is not PURGED, PARSED or DETECTED
	 * @param KalturaDropFolder $folder
	 * @return array
	 */
	protected function loadDropFolderFiles()
	{
		$dropFolderFilesMap = array();
		$dropFolderFiles =null;

		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$dropFolderFileFilter->statusNotIn = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED;
		$dropFolderFileFilter->orderBy = KalturaDropFolderFileOrderBy::CREATED_AT_ASC;

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if(KBatchBase::$taskConfig->params->pageSize)
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		$totalCount = 0;
		do
		{
			$pager->pageIndex++;
			$dropFolderFiles = $this->dropFolderFileService->listAction($dropFolderFileFilter, $pager);
			if (!$totalCount)
				$totalCount = $dropFolderFiles->totalCount;
			$dropFolderFiles = $dropFolderFiles->objects;
			foreach ($dropFolderFiles as $dropFolderFile)
			{
				$dropFolderFilesMap[$dropFolderFile->fileName] = $dropFolderFile;
			}
		}while (count($dropFolderFiles) >= $pager->pageSize);
		if ($totalCount != count($dropFolderFilesMap))
			KalturaLog::info("Map is missing files - Drop folder [" . $this->dropFolder->id . "] has [$totalCount] file from list BUT has [" . count($dropFolderFilesMap) ."] files in map");
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

	protected function createCategoryAssociations (KalturaDropFolder $folder, $userId, $entryId)
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
				$categories = array();
				foreach ($categoriesXPathRes as $catXPath)
				{
					$categories[] = strval($catXPath);
				}
				
				$categoryFilter = new KalturaCategoryFilter();
				$categoryFilter->idIn = implode(',', $categories);
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
			$ks = $client->generateSessionV2($partnerInfo->adminSecret, $userId, KalturaSessionType::ADMIN, $partnerInfo->id, 86400, 'enableentitlement,privacycontext:'.$category->privacyContexts);
			$client->setKs($ks);
			$categoryEntry = new KalturaCategoryEntry();
			$categoryEntry->categoryId = $category->id;
			$categoryEntry->entryId = $entryId;
			try
			{
				$client->categoryEntry->add ($categoryEntry);
			}
			catch (Exception $e)
			{
				KalturaLog::err("Could not add entry $entryId to category {$category->id}. Exception thrown.");
			}
		}
	}
	
	function log($message)
	{
		KalturaLog::log($message);
	}
}
