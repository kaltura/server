<?php
/**
 * Base class to all operation attributes types
 *
 * @package Core
 * @subpackage model.data
 */
class kClipAttributes extends kOperationAttributes 
{
	const SYSTEM_DEFAULT_FLAVOR_PARAMS_ID = -1;
	
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
	
	/* (non-PHPdoc)
	 * @see kOperationAttributes::toArray()
	 */
	public function toArray()
	{
		return array(
			'ClipOffset' => $this->offset,
			'ClipDuration' => $this->duration,
		);
	}
	
	/* (non-PHPdoc)
	 * @see kOperationAttributes::getApiType()
	 */
	public function getApiType()
	{
		return 'KalturaClipAttributes';
	}

	/* (non-PHPdoc)
	 * @see kOperationAttributes::getAssetParamsId()
	 */
	public function getAssetParamsId()
	{
		return self::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID;
	}

	/* (non-PHPdoc)
	 * @see kOperationAttributes::getSourceType()
	 */
	public function getSourceType()
	{
		return EntrySourceType::CLIP;
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