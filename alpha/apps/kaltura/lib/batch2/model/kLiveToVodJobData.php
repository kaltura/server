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
	public function gettotalVodDuration()
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
	public function settotalVodDuration($totalVodDuration)
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


}
