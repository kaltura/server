<?php
/**
 * Used to ingest media file that is already accessible on the shared disc.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLocalFileResource extends KalturaContentResource 
{
	/**
	 * Full path to the local file 
	 * @var string
	 */
	public $localFilePath;
}