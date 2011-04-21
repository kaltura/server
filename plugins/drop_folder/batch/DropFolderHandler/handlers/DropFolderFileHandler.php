<?php

abstract class DropFolderFileHandler
{
	/**
	 * @var KalturaClient
	 */
	protected $kClient;
	
	/**
	 * @var KalturaDropFolderFileHandlerConfig
	 */
	protected $config;
	
	/**
	 * @var KalturaDropFolder
	 */
	protected $dropFolder;
	
	/**
	 * @var KalturaDropFolderFile
	 */
	protected $dropFolderFile;
	
	/**
	 * @var int
	 */
	private $batchPartnerId;
	
	
	
	/**
	 * Return a new instance of a class extending DropFolderFileHandler, according to give $type
	 * @param KalturaDropFolderFileHandlerType $type
	 * @return DropFolderFileHandler
	 */
	public static function getHandler($type)
	{
		switch ($type)
		{
			case KalturaDropFolderFileHandlerType::CONTENT:
				return new DropFolderContentFileHandler();		
				
			default:
				return KalturaPluginManager::loadObject('DropFolderFileHandler', $type);
		}
	}
	

	public function setConfig(KalturaClient $client, KalturaDropFolderFile $dropFolderFile, KalturaDropFolder $dropFolder)
	{
		$this->kClient = $client;
		$this->dropFolder = $dropFolder;
		$this->dropFolderFile = $dropFolderFile;
		$this->config = $dropFolder->fileHandlerConfig;
		$this->batchPartnerId = $this->kClient->getConfig()->partnerId;
	}

	
	/**
	 * Should handle the drop folder file with the given id
	 * At the end of execution, the DropFolderFile object's STATUS may be one of the following:
	 * 1. HANDLED - success
	 * 2. WAITING - waiting for another file
	 * 3. ERROR_HANDLING - an error happened
	 * 4. NO_MATCH - no error occured, but the file cannot be handled since it does not match any entry
	 * 
	 * @return true if file was handled or false otherwise
	 */
	public abstract function handle();	
		// must be implemented by extending classes
	
	
	/**
	 * @return DropFolderFileHandlerType
	 */
	public abstract function getType();
		// must be implemented by extending classes
		
	
	/**
	 * Update the associated drop folder file object with its current state
	 * @return KalturaDropFolderFile
	 */
	protected function updateDropFolderFile()
	{
		$updateFile = new KalturaDropFolderFile();
		$updateFile->status = $this->dropFolderFile->status;
		$updateFile->fileSize = $this->dropFolderFile->fileSize;
		$updateFile->parsedSlug = $this->dropFolderFile->parsedSlug;
		$updateFile->parsedFlavor = $this->dropFolderFile->parsedFlavor;
		$updateFile->errorDescription = $this->dropFolderFile->errorDescription;		
		
		return $this->kClient->dropFolderFile->update($this->dropFolderFile->id, $updateFile);
	}
	
	
	/**
	 * @param string $parsedFlavor
	 * @return KalturaFlavorParams the flavor matching the given $systemName
	 */
	protected function getFlavorBySystemName($systemName)
	{
		$flavorFilter = new KalturaFlavorParamsFilter();
		$flavorFilter->systemNameEqual = $systemName;
		$flavorList = $this->kClient->flavorParams->listAction($flavorFilter);
		
		if (is_array($flavorList->objects) && isset($flavorList->objects[0]) ) {
			return $flavorList->objects[0];
		}
		else {
			return null;
		}			
	}
		
	
	/**
	 * @param string $referenceId
	 * @return KalturaFlavorParams the entry matching the given $referenceId
	 */
	protected function getEntryByReferenceId($referenceId)
	{
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->referenceIdEqual = $referenceId;
		$entryPager = new KalturaFilterPager();
		$entryPager->pageSize = 1;
		$entryPager->pageIndex = 1;
		$entryList = $this->kClient->baseEntry->listAction($entryFilter, $entryPager);
		
		if (is_array($entryList->objects) && isset($entryList->objects[0]) ) {
			return $matchedEntryList->objects[0];
		}
		else {
			return null;
		}
	}
	
	/**
	 * @return KalturaConversionProfile
	 */
	protected function getIngestionProfile()
	{
		if (!is_null($this->dropFolder->ingestionProfileId)) {
			$result = $this->kClient->conversionProfile->get($this->dropFolder->ingestionProfileId);
		}
		else {
			$this->impersonate($this->dropFolderFile->partnerId);
			$result = $this->kClient->conversionProfile->getDefault();
			$this->unimpersonate();
		}
		return $result;
	}
		
	
	protected function impersonate($partnerId)
	{
		$config = $this->kClient->getConfig();
		$config->partnerId = $partnerId;
		$this->kClient->setConfig($config);
	}
	
	protected function unimpersonate()
	{
		$config = $this->kClient->getConfig();
		$config->partnerId = $this->batchPartnerId;
		$this->kClient->setConfig($config);
	}
}