<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready. The bulk upload id will be saved on the entry.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaBulkResource extends KalturaUrlResource 
{
	/**
	 * ID of the bulk upload job to be associated with the entry 
	 * @var int
	 */
	public $bulkUploadId;

	private static $map_between_objects = array
	(
		'bulkUploadId',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('bulkUploadId');
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kBulkResource();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}