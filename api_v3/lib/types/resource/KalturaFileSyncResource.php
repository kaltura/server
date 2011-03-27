<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different file in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaFileSyncResource extends KalturaContentResource 
{
	/**
	 * The object type of the file sync object 
	 * @var int
	 */
	public $fileSyncObjectType;
	
	/**
	 * The object sub-type of the file sync object 
	 * @var int
	 */
	public $objectSubType;
	
	/**
	 * The object id of the file sync object 
	 * @var string
	 */
	public $objectId;
	
	/**
	 * The version of the file sync object 
	 * @var string
	 */
	public $version;
}