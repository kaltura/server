<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamBitrate extends KalturaObject
{
	/**
	 * @var int
	 */
	public $bitrate;
	
	/**
	 * @var int
	 */
	public $width;
	
	/**
	 * @var int
	 */
	public $height;
	
	/**
	 * @var string
	 */
	public $tags;

	private static $map_between_objects = array
	(
		'bitrate',
		'width',
		'height',
		'tags',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		// enables extension with default empty object
		if(!is_array($object_to_fill))
		{
			if(!is_null($object_to_fill))
				return $object_to_fill;
				
			$object_to_fill = array();
		}
			
		$object_to_fill['bitrate'] = $this->bitrate;
		$object_to_fill['width'] = $this->width;
		$object_to_fill['height'] = $this->height;
		if($this->tags)
			$object_to_fill['tags'] = $this->tags;
		
		return $object_to_fill;	
	}
}
