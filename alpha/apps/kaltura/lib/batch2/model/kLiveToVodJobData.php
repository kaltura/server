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
	private $totalVodDuration;

	/** last Segment Duration
	 * @var float
	 */
	private $lastSegmentDuration;
	
	/** amf Array
	 * @var string
	 */
	private $amfArray;
	
	/** vod Entry Id
	 * @var time
	 */
	private $lastCuePointSyncTime;
	
	/** last segment drift
	 * @var int
	 */
	private $lastSegmentDrift;

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
	 * @return float totalVodDuration
	 */
	public function getTotalVodDuration()
	{
		return $this->totalVodDuration;
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
	 * @return time $lastCuePointSyncTime
	 */
	public function getLastCuePointSyncTime()
	{
		return $this->lastCuePointSyncTime;
	}
	/**
	 * @return int $lastSegmentDrift
	 */
	public function getLastSegmentDrift()
	{
		return $this->lastSegmentDrift;
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
	 * @param float $totalVodDuration
	 */
	public function setTotalVodDuration($totalVodDuration)
	{
		$this->totalVodDuration = $totalVodDuration;
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
	/**
	 * @param time $lastCuePointSyncTime
	 */
	public function setLastCuePointSyncTime($lastCuePointSyncTime)
	{
		$this->lastCuePointSyncTime = $lastCuePointSyncTime;
	}
	/**
	 * @param time $lastSegmentDrift
	 */
	public function setLastSegmentDrift($lastSegmentDrift)
	{
		$this->lastSegmentDrift = $lastSegmentDrift;
	}
}
