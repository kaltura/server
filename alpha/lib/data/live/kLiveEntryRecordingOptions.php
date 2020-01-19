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
     * @var boolean
     */
	protected $shouldAutoArchive = false;

    /**
     * @var string
     */
	protected $nonDeletedCuePointsTags;

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

	/**
	 * @param boolean $shouldAutoArchive
	 */
	public function setShouldAutoArchive($shouldAutoArchive)
	{
		$this->shouldAutoArchive = $shouldAutoArchive;
	}

	/**
	 * @return boolean
	 */
	public function getShouldAutoArchive()
	{
		return $this->shouldAutoArchive;
	}

	/**
	 * @param string $nonDeletedCuePointsTags
	 */
	public function setNonDeletedCuePointsTags($nonDeletedCuePointsTags)
	{
		$this->nonDeletedCuePointsTags = $nonDeletedCuePointsTags;
	}

	/**
	 * @return string
	 */
	public function getNonDeletedCuePointsTags()
	{
		return $this->nonDeletedCuePointsTags;
	}
}