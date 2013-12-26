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
	
	/**
	 * Force Import Job 
	 * @var bool
	 */
	public $forceAsyncDownload;
	
	private static $map_between_objects = array('url', 'forceAsyncDownload');
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('url');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kUrlResource();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}