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
}