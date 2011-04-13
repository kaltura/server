<?php

abstract class DropFolderFileHandler
{
	
	/**
	 * Return a new instance of a class extending DropFolderFileHandler, according to give $type
	 * @param DropFolderFileHandlerType $type
	 */
	public static function getHandler($type, DropFolderFileHandlerConfig $config = null)
	{
		$handler = KalturaPluginManager::loadObject('DropFolderFileHandler', $type);
		if ($config) {
			$handler->setConfig($config);
		}
		return $config;
	}
	
	private function __construct();
		// instantiation should be done using getHandler
		
	protected abstract function setConfig(DropFolderFileHandlerConfig $config);
		// must be implemented by extending classes
	
	/**
	 * @return DropFolderFileHandlerType
	 */
	public abstract function getType();
		// must be implemented by extending classes
	
	/**
	 * Should handle the drop folder file with the given id
	 * At the end of execution, the DropFolderFile object's STATUS may be one of the following:
	 * 1. HANDLED - success
	 * 2. WAITING - waiting for another file
	 * 3. ERROR_HANDLING - an error happened
	 * 4. NO_MATCH - no error occured, but the file cannot be handled since it does not match any entry
	 * 
	 * @param int $dropFolderFileId id of the DropFolderFile object
	 */
	public abstract function handleFile($dropFolderFileId);	
		// must be implemented by extending classes
	
}