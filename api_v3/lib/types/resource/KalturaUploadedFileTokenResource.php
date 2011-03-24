<?php
/**
 * Used to ingest media that uploaded to the system and represented by token that returned from upload.upload action or uploadToken.add action.
 * 
 * @package api
 * @subpackage objects
 * @see api/services/UploadService#uploadAction()
 * @see api/services/UploadTokenService#addAction()
 */
class KalturaUploadedFileTokenResource extends KalturaContentResource 
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action. 
	 * @var string
	 */
	public $token;
}