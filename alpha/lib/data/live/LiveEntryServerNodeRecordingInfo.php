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
	 * @var int
	 */
	protected $recordingStatus;

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

	/**
	 * @return int
	 */
	public function getRecordingStatus()
	{
		return $this->recordingStatus;
	}

	/**
	 * @param int $recordingStatus
	 */
	public function setRecordingStatus($recordingStatus)
	{
		$this->recordingStatus = $recordingStatus;
	}

}