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
}