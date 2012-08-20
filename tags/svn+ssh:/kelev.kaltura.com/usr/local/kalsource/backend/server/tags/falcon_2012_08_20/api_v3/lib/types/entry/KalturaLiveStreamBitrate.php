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
	

	public function fromObject ( $source_object )
	{
		$this->bitrate = $source_object['bitrate'];
		$this->width = $source_object['width'];
		$this->height = $source_object['height'];
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
		
		return $object_to_fill;	
	}
}
?>