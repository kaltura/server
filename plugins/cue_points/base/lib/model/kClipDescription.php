<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kClipDescription
{
	/**
	 * The Source
	 * @var string
	 */
	private $sourceEntryId;

	/**
	 *
	 * @var int
	 */
	private $startTime;

	/**
	 *
	 * @var int
	 */
	private $duration;


	/**
	 * @param string $sourceEntryId
	 */
	public function setSourceEntryId($sourceEntryId)
	{
		$this->sourceEntryId = $sourceEntryId;
	}

	/**
	 * @return string
	 */
	public function getSourceEntryId()
	{
		return $this->sourceEntryId;
	}


	/**
	 * @param int $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}

	/**
	 * @return int $startTime
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}


	/**
	 * @param int $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}

	/**
	 * @return int $duration
	 */
	public function getDuration()
	{
		return $this->duration;
	}
}