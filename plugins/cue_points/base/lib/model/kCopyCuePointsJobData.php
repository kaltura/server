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

	/**
	 * the sources start time and duration
	 * @var array
	 */
	private $clipsDescriptionArray;

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
	 * @return array
	 */
	public function getClipsDescriptionArray()
	{
		return $this->clipsDescriptionArray;
	}

	/**
	 * @param array $clipsDescriptionArray
	 */
	public function setClipsDescriptionArray($clipsDescriptionArray)
	{
		$this->clipsDescriptionArray = $clipsDescriptionArray;
	}


}