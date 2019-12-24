<?php

/**
 * Live stream recording entry configuration object 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveEntryRecordingOptions
{
	/**
	 * @var boolean
	 */
	protected $shouldCopyEntitlement;
	
	/**
	 * @var boolean
	 */
	protected $shouldCopyScheduling;
	
	/**
	 * @var boolean
	 */
	protected $shouldCopyThumbnail;

	/**
	 * @var boolean
	 */
	protected $shouldMakeHidden = false;

	/**
	 * @return the $shouldCopyThumbnail
	 */
	public function getShouldCopyThumbnail() {
		return $this->shouldCopyThumbnail;
	}

	/**
	 * @param boolean $shouldCopyThumbnail
	 */
	public function setShouldCopyThumbnail($shouldCopyThumbnail) {
		$this->shouldCopyThumbnail = $shouldCopyThumbnail;
	}

	/**
	 * @return boolean $shouldCopyScheduling
	 */
	public function getShouldCopyScheduling() 
	{
		return $this->shouldCopyScheduling;
	}

	/**
	 * @param boolean $shouldCopyScheduling
	 */
	public function setShouldCopyScheduling($shouldCopyScheduling) 
	{
		$this->shouldCopyScheduling = $shouldCopyScheduling;
	}

	/**
	 * @param boolean $shouldCopyEntitlement
	 */
	public function setShouldCopyEntitlement($shouldCopyEntitlement)
	{
		$this->shouldCopyEntitlement = $shouldCopyEntitlement;
	}
	
	/**
	 * @return boolean
	 */
	public function getShouldCopyEntitlement()
	{
		return $this->shouldCopyEntitlement;
	}

	/**
	 * @param boolean $shouldMakeHidden
	 */
	public function setShouldMakeHidden($shouldMakeHidden)
	{
		$this->shouldMakeHidden = $shouldMakeHidden;
	}

	/**
	 * @return boolean
	 */
	public function getShouldMakeHidden()
	{
		return $this->shouldMakeHidden;
	}

}