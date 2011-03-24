<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different entry in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync of the existing entry.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaEntryResource extends KalturaContentResource 
{
	/**
	 * ID of the source entry 
	 * @var string
	 */
	public $entryId;
	
	/**
	 * ID of the source flavor params, set to null to use the source flavor
	 * @var int
	 */
	public $flavorParamsId;
}