<?php
/**
 * Used to ingest media that streamed to the system and represented by token that returned from media server such as FMS or red5.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaWebcamTokenResource extends KalturaContentResource 
{
	/**
	 * Token that returned from media server such as FMS or red5. 
	 * @var string
	 */
	public $token;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('token');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
			
	    $content = myContentStorage::getFSContentRootPath();
	    $entryFullPath = "{$content}/content/webcam/{$this->token}.flv";
	    
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$entryFixedFullPath = $entryFullPath . '.fixed.flv';
 		KalturaLog::debug("Fix webcam full path from [$entryFullPath] to [$entryFixedFullPath]");
		myFlvStaticHandler::fixRed5WebcamFlv($entryFullPath, $entryFixedFullPath);
				
		$entryNewFullPath = $entryFullPath . '.clipped.flv';
 		KalturaLog::debug("Clip webcam full path from [$entryFixedFullPath] to [$entryNewFullPath]");
		myFlvStaticHandler::clipToNewFile($entryFixedFullPath, $entryNewFullPath, 0, 0);
		$entryFullPath = $entryNewFullPath ;
				
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$object_to_fill->setLocalFilePath($entryFullPath);
		return $object_to_fill;
	}
}