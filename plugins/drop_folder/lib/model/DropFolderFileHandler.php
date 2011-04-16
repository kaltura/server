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
	}

	
	/**
	 * Should handle the drop folder file with the given id
	 * At the end of execution, the DropFolderFile object's STATUS may be one of the following:
	 * 1. HANDLED - success
	 * 2. WAITING - waiting for another file
	 * 3. ERROR_HANDLING - an error happened
	 * 4. NO_MATCH - no error occured, but the file cannot be handled since it does not match any entry
	 */
	public abstract function handle();	
		// must be implemented by extending classes
	
		/**
	 * @return DropFolderFileHandlerType
	 */
	public abstract function getType();
		// must be implemented by extending classes
		
	
	protected function updateDropFolderFile()
	{
		return $this->kClient->dropFolderFile->update($this->dropFolderFile->id, $this->dropFolderFile);
	}
		
}