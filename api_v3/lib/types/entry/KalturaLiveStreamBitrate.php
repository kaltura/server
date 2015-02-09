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
	

	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
		$this->bitrate = $source_object['bitrate'];
		$this->width = $source_object['width'];
		$this->height = $source_object['height'];
		if(isset($source_object['tags']))
			$this->tags = $source_object['tags'];
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
