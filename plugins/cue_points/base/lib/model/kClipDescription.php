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
	 *
	 * @var int
	 */
	private $offsetInDestination;


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

	/**
	 * @param int $offsetInDestination
	 */
	public function setOffsetInDestination($offsetInDestination)
	{
		$this->offsetInDestination = $offsetInDestination;
	}

	/**
	 * @return int $offsetInDestination
	 */
	public function getOffsetInDestination()
	{
		return $this->offsetInDestination;
	}
}