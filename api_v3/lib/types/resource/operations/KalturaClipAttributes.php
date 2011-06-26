<?php
/**
 * Clip operation attributes
 * 
 * @package api
 * @subpackage objects
 */
class KalturaClipAttributes extends KalturaOperationAttributes
{
	/**
	 * Offset in milliseconds
	 * @var int
	 */
	public $offset;
	
	/**
	 * Duration in milliseconds
	 * @var int
	 */
	public $duration;

	public function toAttributesArray()
	{
		return array(
			'ClipOffset' => $this->offset,
			'ClipDuration' => $this->duration,
		);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kClipAttributes();
			
		$object_to_fill->setOffset($this->offset);
		$object_to_fill->setDuration($this->duration);
		
		return $object_to_fill;
	}
}