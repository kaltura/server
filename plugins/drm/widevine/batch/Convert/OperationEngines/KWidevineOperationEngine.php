<?php

class KWidevineOperationEngine extends KOperationEngine
{
	const PACKAGE_NOTIFY_URL = "/widevine/voddealer/cgi-bin/packagenotify.cgi";
	const PACKAGE_QUERY_URL = "/widevine/voddealer/cgi-bin/packagequery.cgi";
	
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
	
	private $PACKAGE_ERROR_STATUSES = array('error' => 'error', 'importFailed' => 'importFailed', 'processingFailed' => 'processingFailed', 'exportFailed' => 'exportFailed', 'unknown' => 'unknown', 'packageDeleteFailed' => 'packageDeleteFailed');
	
	
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
		$responseXml = $this->sendPostRequest(self::PACKAGE_NOTIFY_URL, $requestXml);
		KalturaLog::debug("response xml ".$responseXml);
		$response = XmlHelper::parsePackagerResponse($responseXml);
		$this->handleResponseError($response);
		
		return false;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doCloseOperation()
	 */
	protected function doCloseOperation()
	{
		$requestXml = $this->preparePackageQueryRequestXml();
		$responseXml = $this->sendPostRequest(self::PACKAGE_QUERY_URL, $requestXml);
		$response = XmlHelper::parsePackagerResponse($responseXml);
		return $this->handleResponseStatus($response);
	}
	
	private function preparePackageFolders()
	{
		$this->sourceFolder = $this->params->sourceRootPath . DIRECTORY_SEPARATOR . basename($this->data->destFileSyncLocalPath);
		KalturaLog::debug('source folder path: '.$this->sourceFolder);
		mkdir($this->sourceFolder);
		
		$fileName = basename($this->data->actualSrcFileSyncLocalPath);	//this should be the converted flavor file sync	
		symlink($this->data->actualSrcFileSyncLocalPath, $this->sourceFolder . DIRECTORY_SEPARATOR . $fileName);
		$this->packageFiles[] = $fileName;
		
		$this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath . self::PACKAGE_FILE_EXT;
		
		KalturaLog::debug('source files folder: '.$this->sourceFolder);
		KalturaLog::debug('source file: '.$fileName);
		KalturaLog::debug('target file: '.$this->data->destFileSyncLocalPath);
	}
	
	private function preparePackageNotifyRequestXml()
	{
		$files = array();		
		$outputFileName = basename($this->data->destFileSyncLocalPath);
		$targetFolder = dirname($this->data->destFileSyncLocalPath);
		
		KalturaLog::debug("package name: ".$this->packageName." source folder:".$this->sourceFolder." target folder:".$targetFolder." output file: ".$outputFileName);
		$requestInput = new PackageNotifyRequest($this->packageName, $this->sourceFolder, $targetFolder, $outputFileName, $this->packageFiles);
		
		$params = explode(',', $this->operator->params);
		if(isset($params->policy) && $params->policy)
		{
			$requestInput->setPolicy($params->policy);
		}
		
		$requestXml = XmlHelper::constructPackageNotifyRequestXml($requestInput);
																  
		return $requestXml->asXML();
	}
	
	private function sendPostRequest($url, $requestXml)
	{
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
		
		return $response;
	}
	
	private function handleResponseError(PackagerResponse $response)
	{
		KalturaLog::debug('response status: '. $response->getStatus());
		if(array_key_exists($response->getStatus(), $this->PACKAGE_ERROR_STATUSES))
		{
			$logMessage = 'Package Notify request failed, package name: '.$response->getName().' error: '.$response->getErrorText();
			KalturaLog::err($logMessage);
			//$this->addToLogFile($logMessage);
			throw new KOperationEngineException($logMessage);
		}
	}
	
	private function preparePackageQueryRequestXml()
	{
		$this->packageName = $this->job->entryId.'_'.$this->data->flavorAssetId;
		
		$requestXml = XmlHelper::constructPackageQueryRequestXml($this->packageName);
		return $requestXml->asXML();
	}
	
	private function handleResponseStatus(PackagerResponse $response)
	{
		$this->handleResponseError($response);
		if($response->getStatus() == 'successful')
		{	
			return true;	
		}
		else 
		{
			$this->message = "Package status: ".$response->getStatus();
			return false;
		}		
	}
}