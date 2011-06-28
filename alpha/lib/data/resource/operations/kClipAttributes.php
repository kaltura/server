<?php
/**
 * Base class to all operation attributes types
 *
 * @package Core
 * @subpackage model.data
 */
class kClipAttributes extends kOperationAttributes 
{
	/**
	 * Offset in milliseconds
	 * @var int
	 */
	private $offset;
	
	/**
	 * Duration in milliseconds
	 * @var int
	 */
	private $duration;
	
	public function toArray()
	{
		return array(
			'ClipOffset' => $this->offset,
			'ClipDuration' => $this->duration,
		);
	}
	
	public function getApiType()
	{
		return 'KalturaClipAttributes';
	}

	/**
	 * @return the $offset
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * @return the $duration
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * @param int $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
}