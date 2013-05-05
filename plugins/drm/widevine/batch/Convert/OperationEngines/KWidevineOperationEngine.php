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
		$this->impersonate($this->job->partnerId);
		$entry = $this->client->baseEntry->get($this->job->entryId);
		$this->buildPackageName($entry);
		$this->unimpersonate();		
		KalturaLog::debug('start Widevine packaging: '.$this->packageName);
		
		$this->preparePackageFolders();
		$requestXml = $this->preparePackageNotifyRequestXml();
		$responseXml = WidevinePackageNotifyRequest::sendPostRequest($this->params->vodPackagerHost . WidevinePlugin::PACKAGE_NOTIFY_CGI, $requestXml);
		$response = WidevinePackagerResponse::createWidevinePackagerResponse($responseXml);
		$this->handleResponseError($response);
		
		return false;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doCloseOperation()
	 */
	protected function doCloseOperation()
	{
		$this->impersonate($this->job->partnerId);
		$entry = $this->client->baseEntry->get($this->job->entryId);
		$this->buildPackageName($entry);
		
		KalturaLog::debug('start Widevine package closer: '.$this->packageName);
		$requestXmlObj = new SimpleXMLElement('<PackageQuery/>');
		$requestXmlObj->addAttribute('name', $this->packageName);		
		$requestXml = $requestXmlObj->asXML();
		
		$responseXml = WidevinePackageNotifyRequest::sendPostRequest($this->params->vodPackagerHost . WidevinePlugin::PACKAGE_QUERY_CGI, $requestXml);
		$response = WidevinePackagerResponse::createWidevinePackagerResponse($responseXml);
		$this->message = "Package status: ".$response->getStatus();
		if($response->isSuccess())
		{
			$this->updateFlavorAsset($response->getAssetid());
			$this->unimpersonate();
			return true;
		}
		else 
		{
			$this->unimpersonate();
			$this->handleResponseError($response);
			return false;
		}		
	}
	
	private function preparePackageFolders()
	{
		$this->sourceFolder = $this->params->sourceRootPath . DIRECTORY_SEPARATOR . basename($this->data->destFileSyncLocalPath);
		
		KalturaLog::debug('Creating sources directory: '.$this->sourceFolder);
		mkdir($this->sourceFolder);
		
		foreach ($this->data->srcFileSyncs as $srcFileSyncDescriptor) 
		{
			$fileName = basename($srcFileSyncDescriptor->actualFileSyncLocalPath);		
			KalturaLog::debug('Creating symlink in the source folder: '.$fileName);
			symlink($srcFileSyncDescriptor->actualFileSyncLocalPath, $this->sourceFolder . DIRECTORY_SEPARATOR . $fileName);
			$this->packageFiles[] = $fileName;
		}		
		$this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath . self::PACKAGE_FILE_EXT;
		
		KalturaLog::debug('Target package file name: '.$this->data->destFileSyncLocalPath);
	}
	
	private function preparePackageNotifyRequestXml()
	{
		$outputFileName = basename($this->data->destFileSyncLocalPath);
		$targetFolder = dirname($this->data->destFileSyncLocalPath);
		
		$requestInput = new WidevinePackageNotifyRequest($this->packageName, $this->sourceFolder, $targetFolder, $outputFileName, $this->packageFiles);
		
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
			$logMessage = 'Package Notify request failed, package name: '.$response->getName().' error: '.$response->getErrorText();
			KalturaLog::err($logMessage);
			throw new KOperationEngineException($logMessage);
		}
	}	
	
	private function buildPackageName($entry)
	{	
		$flavorAssetId = $this->data->flavorAssetId;
		$entryId = $this->job->entryId;
			
		if($entry->replacedEntryId)
		{
			$entryId = $entry->replacedEntryId;
			$filter = new KalturaAssetFilter();
			$filter->entryIdEqual = $entry->replacedEntryId;
			$filter->tagsLike = 'widevine'; 
			$flavorAssetList = $this->client->flavorAsset->listAction($filter);
			
			if($flavorAssetList->totalCount > 0)
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
		
		$this->packageName = $entryId.'_'.$flavorAssetId;
	}
	
	private function updateFlavorAsset($wvAssetId)
	{
		$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
		$updatedFlavorAsset->widevineAssetId = $wvAssetId;
		$wvDistributionStartDate = $this->data->flavorParamsOutput->widevineDistributionStartDate;
		$wvDistributionEndDate = $this->data->flavorParamsOutput->widevineDistributionEndDate;
		$updatedFlavorAsset->widevineDistributionStartDate = $wvDistributionStartDate;
		$updatedFlavorAsset->widevineDistributionEndDate = $wvDistributionEndDate;
		$this->client->flavorAsset->update($this->data->flavorAssetId, $updatedFlavorAsset);		
	}
}