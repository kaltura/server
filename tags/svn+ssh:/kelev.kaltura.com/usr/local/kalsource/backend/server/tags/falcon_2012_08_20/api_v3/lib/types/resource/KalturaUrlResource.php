<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaUrlResource extends KalturaContentResource 
{
	/**
	 * Remote URL, FTP, HTTP or HTTPS 
	 * @var string
	 */
	public $url;

	private static $map_between_objects = array
	(
		'url',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('url');
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kUrlResource();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}