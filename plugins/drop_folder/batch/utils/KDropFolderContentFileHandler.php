<?php
/**
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */

class KDropFolderContentFileHandler extends KDropFolderFileHandler
{
	const REFERENCE_ID_WILDCARD = 'referenceId';
	const FLAVOR_NAME_WILDCARD  = 'flavorName';
	const DEFAULT_SLUG_REGEX = '/(?P<referenceId>.+)[.]\w{3,}/';
	
	
	public function handleFileAdded($fileName, $fileSize, $lastModificationTime)
	{
    try
	    {
    	    $newDropFolderFile = new KalturaDropFolderFile();
    		$newDropFolderFile->dropFolderId = $this->folder->id;
    		$newDropFolderFile->fileName = $fileName;
    		$newDropFolderFile->fileSize = $fileSize;
    		$newDropFolderFile->lastModificationTime = $lastModificationTime; 
    		
    		$parsedSlug = null;
    		$parsedFlavor = null;
    		$isMatch = $this->parseRegex($fileName, $parsedSlug, $parsedFlavor);
 			if($isMatch)
 			{
 				$newDropFolderFile->parsedSlug = $parsedSlug;
 				$newDropFolderFile->parsedFlavor = $parsedFlavor;				
  			}    		
 			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
 
 			if(!$isMatch)
 			{
 				$dropFolderFile = $this->handleFileError($dropFolderFile->id, 
 										KalturaDropFolderFileStatus::ERROR_HANDLING, KalturaDropFolderFileErrorCode::SLUG_REGEX_NO_MATCH, 
 										'File name does not match defined slug regex ['.$this->config->slugRegex.']');
 			} 	
 			return $dropFolderFile;		
		}
		catch (Exception $e) 
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$fileName.'] - '.$e->getMessage());
			return null;
		}	
	}
		
	/**
	 * Parse file name according to defined slugRegex and set the extracted parsedSlug and parsedFlavor.
	 * The following expressions are currently recognized and used:
	 * 	- (?P<referenceId>\w+) - will be used as the drop folder file's parsed slug.
	 *  - (?P<flavorName>\w+)  - will be used as the drop folder file's parsed flavor. 
	 * 
	 * @return bool true if file name matches the slugRegex or false otherwise
	 */
	private function parseRegex($fileName, &$parsedSlug, &$parsedFlavor)
	{
		$matches = null;
		$slugRegex = (is_null($this->config->slugRegex) || empty($this->config->slugRegex)) ? self::DEFAULT_SLUG_REGEX : $this->config->slugRegex;
		$matchFound = @preg_match($slugRegex, $fileName, $matches);
		
		if ($matchFound) 
		{
			$parsedSlug   = isset($matches[self::REFERENCE_ID_WILDCARD]) ? $matches[self::REFERENCE_ID_WILDCARD] : null;
			$parsedFlavor = isset($matches[self::FLAVOR_NAME_WILDCARD])  ? $matches[self::FLAVOR_NAME_WILDCARD]  : null;
				
			KalturaLog::debug('Parsed slug ['.$parsedSlug.'], Parsed flavor ['.$parsedFlavor.']');
		}
		
		return $matchFound;
	}
}