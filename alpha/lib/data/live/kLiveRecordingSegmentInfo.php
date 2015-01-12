<?php

/**
 * Map duration and offset of a live and its associated VOD entries
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveRecordingSegmentInfo
{
	/**
	 * Live stream relative start time in msec.
	 * @var int
	 */
	protected $liveStreamStartTime = 0;

	/**
	 * VOD entry relative end time in msec.
	 * @var int
	 */
	protected $vodEntryEndTime = 0;

	/**
	 * The diff between VOD end time and live stream end time in msec
	 * @var int
	 */
	protected $vodToLiveDeltaTime = 0;

	public function setLiveStreamStartTime( $liveStreamStartTime ) { $this->liveStreamStartTime = $liveStreamStartTime; }
	public function getLiveStreamStartTime() { return $this->liveStreamStartTime; }
	
	public function setVodEntryEndTime( $vodEntryEndTime ) { $this->vodEntryEndTime = $vodEntryEndTime; }
	public function getVodEntryEndTime() { return $this->vodEntryEndTime; }
	
	public function setVodToLiveDeltaTime( $vodToLiveDeltaTime ) { $this->vodToLiveDeltaTime = $vodToLiveDeltaTime; }
	public function getVodToLiveDeltaTime() { return $this->vodToLiveDeltaTime; }
}