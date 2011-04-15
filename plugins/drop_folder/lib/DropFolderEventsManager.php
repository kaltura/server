<?php

class DropFolderEventsManager implements kObjectChangedEventConsumer
{
	
	/**
	 * On DropFolderFile object status change to DropFolderFileStatus::PENDING :
	 * 	1. Handle all files in status DropFolderFileStatus::WAITING of the same drop folder.
	 *  2. Handle the changed drop folder file.
	 *  
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof DropFolderFile)
		{
			if (in_array(DropFolderFilePeer::STATUS , $modifiedColumns))
			{
				if ($object->getStatus() == DropFolderFileStatus::PENDING)
				{
					// handle all files in status WAITING
					$waitingFiles = DropFolderFilePeer::retrieveByDropFolderIdAndStatus($object->getDropFolderId(), DropFolderFileStatus::WAITING);
					foreach ($waitingFiles as $waitingFile)
					{
						self::handleFile($waitingFile);
					}
					
					// handle the PENDING file
					self::handleFile($object);
				}				
			}
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * Check if the given $dropFolderFile matches any file name pattern for its drop folder, and execute the relevant handler if it does
	 * @param DropFolderFile $dropFolderFile
	 */
	private static function handleFile(DropFolderFile $dropFolderFile)
	{
		// get file patterns configured for the drop folder
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
		$filePatterns = $dropFolder->getFileNamePatterns();
		$filePatterns = array_map('trim', explode(',', $filePatterns));
		
		// get current file name
		$fileName = $dropFolderFile->getFileName();
		
		// search for a match
		$matchFound = false;
		foreach ($filePatterns as $pattern)
		{
			if (!is_null($pattern) && ($pattern != '')) {
				$pattern = '/^'.$pattern.'$/';
				if (@preg_match($pattern, $fileName)) {
					$matchFound = true;	
				}
			}
		}
		
		// if match found -> handle file by the file handelr configured for its drop folder
		if ($matchFound)
		{
			$fileHandler = DropFolderFileHandler::getHandler($dropFolder->getFileHandlerType(), $dropFolder->getFileHandlerConfig());
			$fileHandler->handleFile($dropFolderFile->getId());
		}
	}
		
}
