<?php
/**
 * sed to ingest media that uploaded as posted file in this http request, the file data represents the $_FILE
 * 
 * @package api
 * @subpackage objects
 */
class KalturaUploadedFileResource extends KalturaContentResource 
{
	/**
	 * Represents the $_FILE 
	 * @var file
	 */
	public $fileData;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('fileData');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
			
		$ext = pathinfo($this->fileData['name'], PATHINFO_EXTENSION);
		
		$uploadPath = $this->fileData['tmp_name'];
		$tempPath = myContentStorage::getFSUploadsPath() . '/' . uniqid(time()) . '.' . $ext;
		$moved = kFile::moveFile($uploadPath, $tempPath, true);
		if(!$moved)
			throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
			
		$object_to_fill->setLocalFilePath($tempPath);
		return $object_to_fill;
	}
}