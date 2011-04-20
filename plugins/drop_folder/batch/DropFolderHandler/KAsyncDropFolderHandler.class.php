<?php
require_once("bootstrap.php");

/**
 * Handles files in drop folders
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KAsyncDropFolderHandler extends KBatchBase
{

	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_HANDLER;
	}
	
	protected function init()
	{ /* non-relevant abstract function */ }
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }
	
	
	
	public function run()
	{
		KalturaLog::info("Drop folder handler batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		// get drop folder tags to work on from configuration
		$folderTags = $this->taskConfig->params->tags;
		$currentDc  = $this->taskConfig->params->dc;
		
		if (strlen($folderTags) == 0) {
			KalturaLog::err('Tags configuration is empty - cannot continue');
			return;
		}
		
		if (strlen($currentDc) == 0) {
			KalturaLog::err('DC configuration is empty - cannot continue');
			return;
		}
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		
		if ($folderTags != '*') {
			$filter->tagsMultiLikeOr = $folderTags;
		}
			
		$filter->dcEqual = $currentDc;
		$filter->statusEqual = KalturaDropFolderStatus::ENABLED;
		
		try {
			$dropFolders = $this->kClient->dropFolder->listAction($filter);
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot get drop folder list - '.$e->getMessage());
			return;
		}
		
		$dropFolders = $dropFolders->objects;
		KalturaLog::log('['.count($dropFolders).'] folders to handle');
		
		foreach ($dropFolders as $folder)
		{
			$this->handleFolder($folder);
		}
	}
		
	/**
	 * Main logic function.
	 * @param KalturaDropFolder $folder
	 */
	private function handleFolder(KalturaDropFolder $folder)
	{
		KalturaLog::debug('Handling folder ['.$folder->id.']');
		
		try {
			$dropFolderFiles = $this->getPendingWaitingFiles($folder->id);
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot get list of files for drop folder id ['.$folder->id.'] - '.$e->getMessage());
			return false;
		}
		
		foreach ($dropFolderFiles as $file)
		{
			$fileHandled = $this->handleFile($folder, $file);
			
			if ($fileHandled) {
				// break loop and go to next folder, because current folder files' status might have changed
				return true;
			}
		}
		
		return false; // no file was handled
	}
	
	/**
	 * Handle the given file if it matches the defined file name pattern by executing the right file handler
	 * @param KalturaDropFolder $dropFolder
	 * @param KalturaDropFolderFile $dropFolderFile
	 */
	private function handleFile(KalturaDropFolder $dropFolder, KalturaDropFolderFile $dropFolderFile)
	{
		KalturaLog::debug('Handling file id ['.$dropFolderFile->id.'] name ['.$dropFolderFile->fileName.']');
		
		// get defined file name patterns
		$filePatterns = $dropFolder->fileNamePatterns;
		$filePatterns = array_map('trim', explode(',', $filePatterns));
		
		// get current file name
		$fileName = $dropFolderFile->fileName;
		
		// search for a match
		$matchFound = false;
		foreach ($filePatterns as $pattern)
		{
			if (!is_null($pattern) && ($pattern != '')) {
				if (fnmatch($pattern, $fileName)) {
					$matchFound = true;
					break;
				}
			}
		}
		
		// if file name doesn't match pattern - quit
		if (!$matchFound) {
			KalturaLog::debug("File name [$fileName] does not match any of the defined patterns");
			return false;
		}
		
		//  handle file by the file handelr configured for its drop folder
		$fileHandler = DropFolderFileHandler::getHandler($dropFolder->fileHandlerType);
		$fileHandler->setConfig($this->kClient, $dropFolderFile, $dropFolder);
		$fileHandled = $fileHandler->handle();
		if ($fileHandled) {
			KalturaLog::debug('File handled succesfully');
			return true;
		}
		else {
			KalturaLog::err('File was not handled!');
			return false;
		}
	}
	
	
	/**
	 * @param int $dropFolderId
	 * @return array of KalturaDropFolderFile
	 */
	private function getPendingWaitingFiles($dropFolderId)
	{
		$dropFolderFileFilter = new KalturaDropFolderFileFilter();
		$dropFolderFileFilter->dropFolderIdEqual = $dropFolderId;
		$dropFolderFileFilter->statusIn = KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING;
		$dropFolderFiles = $this->kClient->dropFolderFile->listAction($dropFolderFileFilter);
		$dropFolderFiles = $dropFolderFiles->objects;
		return $dropFolderFiles;
	}
		
}
