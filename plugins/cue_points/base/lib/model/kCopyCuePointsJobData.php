<?php
/**
 * @package plugins.cue_points
 * @subpackage model.data
 */
class kCopyCuePointsJobData extends kJobData
{
	/** source entry Id
	 * @var string
	 */
	private $destinationEntryId;

	/** source entry Id
	 * @var string
	 */
	private $sourceEntryId;

	/**
	 * @return string
	 */
	public function getDestinationEntryId()
	{
		return $this->destinationEntryId;
	}

	/**
	 * @param string $destinationEntryId
	 */
	public function setDestinationEntryId($destinationEntryId)
	{
		$this->destinationEntryId = $destinationEntryId;
	}

	/**
	 * @return string
	 */
	public function getSourceEntryId()
	{
		return $this->sourceEntryId;
	}

	/**
	 * @param string $sourceEntryId
	 */
	public function setSourceEntryId($sourceEntryId)
	{
		$this->sourceEntryId = $sourceEntryId;
	}




}