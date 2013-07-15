<?php

class KWidevineOperationEngine extends KOperationEngine
{
	const PACKAGE_FILE_EXT = '.wvm';
		
	/**
	 * @var array
	 * batch job parameters
	 */
	private $params;
	
	/**
	 * @var string
	 * folder for the widevine package sources
	 */
	private $sourceFolder;

	/**
	 * @var array
	 * List of file names that are sources for the encrypted package
	 */
	private $packageFiles = array();
	
	/**
	 * @var string
	 * Name of the package, used as asset name in Widevine. Unique for the provider
	 */
	private $packageName;
	
	private $actualSrcAssetParams = array();
	
	private $originalEntryId;
	
	public function __construct($params, $outFilePath)
	{
		$this->params = $params;
	}
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::getCmdLine()
	 */
	protected function getCmdLine() {}

	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doOperation()
	 * 
	 * prepare PackageNotify request and send it to Widevine VOD Packager for encryption
	 */
	protected function doOperation()
	{
		KBatchBase::impersonate($this->job->partnerId);
		$entry = KBatchBase::$kClient->baseEntry->get($this->job->entryId);
		$this->buildPackageName($entry);
		$vodPackagerHost = $this->calcVodPackagerHost();
		KalturaLog::debug('start Widevine packaging: '.$this->packageName.' on '.$vodPackagerHost);
		
		$this->preparePackageFolders();
		$requestXml = $this->preparePackageNotifyRequestXml();	
		$responseXml = WidevinePackageNotifyRequest::sendPostRequest($vodPackagerHost . WidevinePlugin::PACKAGE_NOTIFY_CGI, $requestXml);
		$response = WidevinePackagerResponse::createWidevinePackagerResponse($responseXml);
		$this->handleResponseError($response);	

		$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
		$updatedFlavorAsset->actualSourceAssetParamsIds = implode(',', $this->actualSrcAssetParams);
		KBatchBase::$kClient->flavorAsset->update($this->data->flavorAssetId, $updatedFlavorAsset);
		KBatchBase::unimpersonate();	
		
		while(($this->job->queueTime + $this->params->maxTimeBeforeFail)>= time())
		{
			$res = $this->queryPackage($response->getId(), $vodPackagerHost);
			if($res)
				return true;
			sleep($this->params->retryInterval);
		}
		
		throw new KOperationEngineException("Job execution timed-out");
	}
	
	private function queryPackage($wvJobId, $vodPackagerHost)
	{
		KalturaLog::debug('start Widevine package query for WV job: '.$wvJobId);
		$requestXmlObj = new SimpleXMLElement('<PackageQuery/>');
		$requestXmlObj->addAttribute('id', $wvJobId);		
		$requestXml = $requestXmlObj->asXML();
		
		$responseXml = WidevinePackageNotifyRequest::sendPostRequest($vodPackagerHost . WidevinePlugin::PACKAGE_QUERY_CGI, $requestXml);
		$response = WidevinePackagerResponse::createWidevinePackagerResponse($responseXml);
		KalturaLog::debug("Package status: ".$response->getStatus());
		if($response->isSuccess())
		{
			KBatchBase::impersonate($this->job->partnerId);			
			$this->updateFlavorAsset($response->getAssetid());
			KBatchBase::unimpersonate();
			return true;
		}
		else 
		{
			$this->handleResponseError($response);
			return false;
		}		
	}
	
	private function getAssetIdsWithRedundantBitrates()
	{
		$srcAssetIds = array();
		foreach ($this->data->srcFileSyncs as $srcFileSyncDesc) 
		{
			$srcAssetIds[] = $srcFileSyncDesc->assetId;
		}		
		$srcAssetIds = implode(',', $srcAssetIds);
		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $this->job->entryId;
		$filter->idIn = $srcAssetIds;
		$flavorAssetList = KBatchBase::$kClient->flavorAsset->listAction($filter);	

		$redundantAssets = array();
		if(count($flavorAssetList->objects) > 0)
		{
			$bitrates = array();			
			foreach ($flavorAssetList->objects as $flavorAsset) 
			{
				/* @var $flavorAsset KalturaFlavorAsset */
				if(in_array($flavorAsset->bitrate, $bitrates))
					$redundantAssets[] = $flavorAsset->id;
				else 
					$bitrates[] = $flavorAsset->bitrate;
			}
		}		
		return $redundantAssets;
	}
	
	private function preparePackageFolders()
	{
		$this->sourceFolder = $this->params->sourceRootPath . DIRECTORY_SEPARATOR . basename($this->data->destFileSyncLocalPath);
		
		KalturaLog::debug('Creating sources directory: '.$this->sourceFolder);
		mkdir($this->sourceFolder);
		
		$redundantAssets = $this->getAssetIdsWithRedundantBitrates();
		
		foreach ($this->data->srcFileSyncs as $srcFileSyncDescriptor) 
		{
			if(in_array($srcFileSyncDescriptor->assetId, $redundantAssets))
			{
				KalturaLog::debug('Skipping flavor asset due to redundant bitrate: '.$srcFileSyncDescriptor->assetId);
			}
			else 
			{
				$fileName = basename($srcFileSyncDescriptor->actualFileSyncLocalPath);		
				KalturaLog::debug('Creating symlink in the source folder: '.$fileName);
				symlink($srcFileSyncDescriptor->actualFileSyncLocalPath, $this->sourceFolder . DIRECTORY_SEPARATOR . $fileName);
				$this->packageFiles[] = $fileName;
				$this->actualSrcAssetParams[] = $srcFileSyncDescriptor->assetParamsId;
			}
		}		
		$this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath . self::PACKAGE_FILE_EXT;
		
		KalturaLog::debug('Target package file name: '.$this->data->destFileSyncLocalPath);
	}
	
	private function preparePackageNotifyRequestXml()
	{
		$outputFileName = basename($this->data->destFileSyncLocalPath);
		$targetFolder = dirname($this->data->destFileSyncLocalPath);
		
		$requestInput = new WidevinePackageNotifyRequest($this->packageName, $this->sourceFolder, $targetFolder, $outputFileName, $this->packageFiles, $this->params->portal);
		
		if($this->operator->params)
		{
			$params = explode(',', $this->operator->params);
			foreach ($params as $paramStr) 
			{
				$param = explode('=', $paramStr);
				if(isset($param[0]) && $param[0] == 'policy')
				{
					$requestInput->setPolicy($param[1]);
				}
			}
		}
		$requestInput->setLicenseStartDate($this->data->flavorParamsOutput->widevineDistributionStartDate);
		$requestInput->setLicenseEndDate($this->data->flavorParamsOutput->widevineDistributionEndDate);
		$requestXml = $requestInput->createPackageNotifyRequestXml();
			
		KalturaLog::debug('Package notify request: '.$requestXml);	
													  
		return $requestXml;
	}
	
	private function handleResponseError(WidevinePackagerResponse $response)
	{
		KalturaLog::debug('Response status: '. $response->getStatus());
		if($response->isError())
		{
			KBatchBase::unimpersonate();
			$logMessage = 'Package Notify request failed, package name: '.$response->getName().' error: '.$response->getErrorText();
			KalturaLog::err($logMessage);
			throw new KOperationEngineException($logMessage);
		}
	}	
	
	private function buildPackageName($entry)
	{	
		$flavorAssetId = $this->data->flavorAssetId;
		$this->originalEntryId = $this->job->entryId;
			
		if($entry->replacedEntryId)
		{
			$this->originalEntryId = $entry->replacedEntryId;
			$filter = new KalturaAssetFilter();
			$filter->entryIdEqual = $entry->replacedEntryId;
			$filter->tagsLike = 'widevine'; 
			$flavorAssetList = KBatchBase::$kClient->flavorAsset->listAction($filter);
			
			if(count($flavorAssetList->objects) > 0)
			{
				$replacedFlavorParamsId = $this->data->flavorParamsOutput->flavorParamsId;
				foreach ($flavorAssetList->objects as $flavorAsset) 
				{
					/* @var $flavorAsset KalturaFlavorAsset */
					if($flavorAsset->flavorParamsId == $replacedFlavorParamsId)
					{
						$flavorAssetId = $flavorAsset->id;
						break;
					}
				}
			}
		}
		
		$this->packageName = $this->originalEntryId.'_'.$flavorAssetId;
	}
	
	private function updateFlavorAsset($wvAssetId)
	{
		$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
		$updatedFlavorAsset->widevineAssetId = $wvAssetId;
		
		$wvDistributionStartDate = $this->data->flavorParamsOutput->widevineDistributionStartDate;
		$wvDistributionEndDate = $this->data->flavorParamsOutput->widevineDistributionEndDate;
		$updatedFlavorAsset->widevineDistributionStartDate = $wvDistributionStartDate;
		$updatedFlavorAsset->widevineDistributionEndDate = $wvDistributionEndDate;
		KBatchBase::$kClient->flavorAsset->update($this->data->flavorAssetId, $updatedFlavorAsset);		
	}
	
	private function calcVodPackagerHost()
	{
		$hosts = explode(',', $this->params->vodPackagerHost);
		if(!count($hosts))
			throw new KOperationEngineException("VOD packager host is not defined");
		$index = crc32($this->originalEntryId) % count($hosts);
		return trim($hosts[$index]);
	}
}