<?php
/**
 * @package plugins.playReady
 * @subpackage model
 */

class PlayReadyContentKey
{
	protected $keyId;
	protected $contentKey;
	
	/**
	 * @return the $keyId
	 */
	public function getKeyId() 
	{
		return $this->keyId;
	}

	/**
	 * @return the $contentKey
	 */
	public function getContentKey() 
	{
		return $this->contentKey;
	}

	/**
	 * @param field_type $keyId
	 */
	public function setKeyId($keyId) 
	{
		$this->keyId = $keyId;
	}

	/**
	 * @param field_type $contentKey
	 */
	public function setContentKey($contentKey) 
	{
		$this->contentKey = $contentKey;
	}	
}