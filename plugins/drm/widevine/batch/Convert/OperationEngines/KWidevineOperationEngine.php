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
		$this->packageName = $this->job->entryId.'_'.$this->data->flavorAssetId;
		
		KalturaLog::debug('start Widevine packaging: '.$this->packageName);
		
		$this->preparePackageFolders();
		$requestXml = $this->preparePackageNotifyRequestXml();
		$response = $this->sendPostRequest(WidevinePlugin::getWidevineConfigParam('package_notify_cgi'), $requestXml);
		$this->handleResponseError($response);
		
		return false;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doCloseOperation()
	 */
	protected function doCloseOperation()
	{
		$this->packageName = $this->job->entryId.'_'.$this->data->flavorAssetId;
		
		KalturaLog::debug('start Widevine package closer: '.$this->packageName);
		
		$requestXmlObj = new SimpleXMLElement('<PackageQuery/>');
		$requestXmlObj->addAttribute('name', $this->packageName);		
		$requestXml = $requestXmlObj->asXML();
		
		$response = $this->sendPostRequest(WidevinePlugin::getWidevineConfigParam('package_query_cgi'), $requestXml);		
		$this->message = "Package status: ".$response->getStatus();
		if($response->isSuccess())
		{
			$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
			$updatedFlavorAsset->widevineAssetId = $response->getAssetId();
			$this->client->flavorAsset->update($this->data->flavorAssetId, $updatedFlavorAsset);
			return true;
		}
		else 
		{
			$this->handleResponseError($response);
			return false;
		}		
	}
	
	private function preparePackageFolders()
	{
		$this->sourceFolder = $this->params->sourceRootPath . DIRECTORY_SEPARATOR . basename($this->data->destFileSyncLocalPath);
		
		KalturaLog::debug('Creating sources directory: '.$this->sourceFolder);
		mkdir($this->sourceFolder);
		
		$fileName = basename($this->data->actualSrcFileSyncLocalPath);		
		KalturaLog::debug('Creating symlink in the source folder: '.$fileName);
		symlink($this->data->actualSrcFileSyncLocalPath, $this->sourceFolder . DIRECTORY_SEPARATOR . $fileName);
		$this->packageFiles[] = $fileName;
		
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
			if(isset($params->policy) && $params->policy)
			{
				$requestInput->setPolicy($params->policy);
			}
		}
		
		$entry = $this->client->baseEntry->get($this->job->entryId);
		/* @var $entry KalturaBaseEntry */
		$requestInput->setLicenseStartDate($entry->startDate);
		$requestInput->setLicenseEndDate($entry->endDate);
		
		$requestXml = $requestInput->createPackageNotifyRequestXml();
			
		KalturaLog::debug('Package notify request: '.$requestXml);	
													  
		return $requestXml;
	}
	
	private function sendPostRequest($url, $requestXml)
	{
		if(!$url)
			throw new KOperationEngineException('CGI URL is not set');
			
		$full_url = $this->params->vodPackagerHost . $url;
		KalturaLog::debug('send Package Notify request, url: '.$full_url);		
		KalturaLog::debug('request params: '.$requestXml);
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $full_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		KalturaLog::debug('Package Notify response: '.$response);
		$responseObject = WidevinePackagerResponse::createWidevinePackagerResponse($response);
		
		return $responseObject;
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
}