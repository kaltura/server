<?php
/**
 * Used to ingest media that is already dropped to drop folder and have drop folder file id.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaDropFolderFileResource extends KalturaContentResource 
{
	/**
	 * ID of the drop folder file object 
	 * @var int
	 */
	public $dropFolderFileId;
}