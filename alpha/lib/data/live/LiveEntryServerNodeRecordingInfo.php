<?php

/**
 * 
 *
 * @package Core
 * @subpackage model
 *
 */
class LiveEntryServerNodeRecordingInfo
{
	/**
	 * @var string
	 */
	protected $recordedEntryId;

	/**
	 * @var int
	 */
	protected $duration;

	/**
	 * @return string
	 */
	public function getRecordedEntryId()
	{
		return $this->recordedEntryId;
	}

	/**
	 * @param string $recordedEntryId
	 */
	public function setRecordedEntryId($recordedEntryId)
	{
		$this->recordedEntryId = $recordedEntryId;
	}

	/**
	 * @return int
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param int $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}

}