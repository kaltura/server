<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNodeRecordedProperties extends BaseObject
{

	/**
	 * @var int
	 */
	protected $duration;

	/**
	 * @var array
	 */
	protected $recordedEntriesDurations;

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
	 * @return array
	 */
	public function getRecordedEntriesDurations()
	{
		return $this->recordedEntriesDurations;
	}

	/**
	 * @param array $recordedEntriesDurations
	 */
	public function setRecordedEntriesDurations($recordedEntriesDurations)
	{
		$this->recordedEntriesDurations = $recordedEntriesDurations;
	}



}