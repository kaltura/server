<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLiveToVodJobData extends kJobData
{
	/** vod Entry Id
	 * @var string
	 */
	private $vodEntryId;

	/** live Entry Id
	 * @var string
	 */
	private $liveEntryId;

	/** total VOD Duration
	 * @var float
	 */
	private $totalVODDuration;

	/** last Segment Duration
	 * @var float
	 */
	private $lastSegmentDuration;
	/** amf Array
	 * @var string
	 */
	private $amfArray;



	/**
	 * @return string vodEntryId
	 */
	public function getVodEntryId()
	{
		return $this->vodEntryId;
	}
	/**
	 * @return string liveEntryId
	 */
	public function getLiveEntryId()
	{
		return $this->liveEntryId;
	}
	/**
	 * @return float totalVODDuration
	 */
	public function getTotalVODDuration()
	{
		return $this->totalVODDuration;
	}
	/**
	 * @return float lastSegmentDuration
	 */
	public function getLastSegmentDuration()
	{
		return $this->lastSegmentDuration;
	}
	/**
	 * @return string amfArray
	 */
	public function getAmfArray()
	{
		return $this->amfArray;
	}


	/**
	 * @param string $VodEntryId
	 */
	public function setVodEntryId($VodEntryId)
	{
		$this->vodEntryId = $VodEntryId;
	}
	/**
	 * @param string $liveEntryId
	 */
	public function setLiveEntryId($liveEntryId)
	{
		$this->liveEntryId = $liveEntryId;
	}
	/**
	 * @param float $totalVODDuration
	 */
	public function setTotalVODDuration($totalVODDuration)
	{
		$this->totalVODDuration = $totalVODDuration;
	}
	/**
	 * @param float $lastSegmentDuration
	 */
	public function setLastSegmentDuration($lastSegmentDuration)
	{
		$this->lastSegmentDuration = $lastSegmentDuration;
	}
	/**
	 * @param string $amfArray
	 */
	public function setAmfArray($amfArray)
	{
		$this->amfArray = $amfArray;
	}


}
