<?php

/**
 * Map duration and offset of a live and its associated VOD entries
 *
 * @package Core
 * @subpackage model
 *
 */
class kRecordedSegmentsInfo
{
	// The following string literals are short for the sake of a compact serialization
	const LIVE_START = 'ls'; // Live start time = Total live duration at the beginning of the segment
	const VOD_SEGMENT_DURATION = 'vsd';
	const VOD_TO_LIVE_DELTA_TIME = 'dt'; // The diff between live and VOD segments duration (liveStart + vodSegmentDuration + vodToLiveDeltaTime = liveEnd)
	const AMF_DATA = 'amf';

	protected $segments = array();

	public function addSegment( $liveStart, $vodSegmentDuration, $vodToLiveDeltaTime, $AMFs)
	{
		$this->segments[] = array(
				self::LIVE_START => $liveStart,
				self::VOD_SEGMENT_DURATION => $vodSegmentDuration,
				self::VOD_TO_LIVE_DELTA_TIME => $vodToLiveDeltaTime,
				self::AMF_DATA => $AMFs
			);
	}

	public function getOffsetForTimestamp($timestamp)
	{
		KalturaLog::debug("in kRecordedSegmentsInfo.getOffsetForTimestamp timestamp= " . $timestamp);
		$numSegments = count($this->segments);
		$totalVodOffset = 0;
		$i = 0;

		while ( $i < $numSegments ) {
			KalturaLog::debug("in kRecordedSegmentsInfo.getOffsetForTimestamp - " . $i . " of " . $numSegments);

			$segment = $this->segments[$i];
			$nextSegment = $i+1 < $numSegments ? $this->segments[$i+1] : null;

			$segmentStartTime = $this->getSegmentStartTime($segment);
			$nextSegmentStartTime = $nextSegment ? $this->getSegmentStartTime($nextSegment) : null;

			KalturaLog::debug("segmentStartTime: " . $segmentStartTime);
			KalturaLog::debug("nextSegmentStartTime: " . $nextSegmentStartTime);
			KalturaLog::debug("totalVodOffset: " . $totalVodOffset);

			// since the timestamp on the cue point is in seconds, and everything else is in milliseconds we can get
			// a cue point (that was created less than one second from the beginning of the stream) can get negative time
			// and we will need to "fix" its time
			if ($segmentStartTime > $timestamp){
				KalturaLog::debug("timestamp " . $timestamp . " passed to getOffsetForTimestamp was less than segmentStartTime " . $segmentStartTime);
				$timestamp = $segmentStartTime;
			}

			if ($timestamp >= $segmentStartTime && (is_null($nextSegmentStartTime) || $timestamp < $nextSegmentStartTime)) {
				$totalVodOffset += $this->getSegmentDurationTillTS($segment, $timestamp);
				KalturaLog::debug("kRecordedSegmentsInfo.getOffsetForTimestamp returning " . $totalVodOffset);
				return $totalVodOffset;
			} else {
				$totalVodOffset += $segment[self::VOD_SEGMENT_DURATION];
				KalturaLog::debug("adding " .  $segment[self::VOD_SEGMENT_DURATION] . " to totalVodOffset so now its " . $totalVodOffset);
			}
			$i++;
		}

		KalturaLog::err("Couldn't get offset for timestamp [$timestamp]");
		return null;
	}

	// assumption: $timestamp >= getSegmentStartTime($segment)
	private function getSegmentDurationTillTS($segment, $timestamp)
	{
		KalturaLog::debug("in getSegmentDurationTillTS with segment=" . print_r($segment, true) . " and timestamp=" . print_r($timestamp, true));

		$prevAMF = null;
		$nextAMF = null;
		$numAMFs = count($segment[self::AMF_DATA]);
		KalturaLog::debug("there are " . $numAMFs . " AMFs");
		for($i = 0; $i < $numAMFs; $i++){
			KalturaLog::debug("AMF # " . $i . " :timestamp= ". $segment[self::AMF_DATA][$i]->timestamp);
			if ($segment[self::AMF_DATA][$i]->timestamp >= $timestamp){
				KalturaLog::debug("AMF #" .$i. " is after the timestamp");
				$nextAMF = $segment[self::AMF_DATA][$i];
				KalturaLog::debug("setting nextAMF to " . print_r($nextAMF,true));
				if ($i > 0){
					$prevAMF = $segment[self::AMF_DATA][$i-1];
					KalturaLog::debug("setting prevAMF to " . print_r($prevAMF,true));
				}
				break;
			}
		}

		if (is_null($nextAMF)){
			$prevAMF = $segment[self::AMF_DATA][$numAMFs-1];
			KalturaLog::debug("nextAMF was null. setting prevAMF to" . print_r($prevAMF,true));
		}

		// At this point:
		// if $nextAMF and $prevAMF are not null - the time is between them
		// 		return $prevAMF.pts + timestamp - $prevAMF.timestamp
		// if $nextAMF is null - the time is after the last AMF
		// 		return $prevAMF.pts + timestamp - $prevAMF.timestamp
		// if $prevAMF is null - the time is before the first AMF.
		// 		return $nextAMF.pts - ($nextAMF.timestamp - timestamp)
		$ret = 0;
		KalturaLog::debug("timestamp= " . $timestamp);
		KalturaLog::debug("prevAMF->timestamp= ". $prevAMF->timestamp);
		KalturaLog::debug("prevAMF->pts= " . $prevAMF->pts);
		KalturaLog::debug("nextAMF->timestamp= ". $nextAMF->timestamp);
		KalturaLog::debug("nextAMF->pts= " . $nextAMF->pts);

		if (!is_null($prevAMF)){
			$ret = $prevAMF->pts + $timestamp - $prevAMF->timestamp;
		}
		else{
			$ret = $nextAMF->pts - ($nextAMF->timestamp - $timestamp);
		}

		KalturaLog::debug("getSegmentDurationTillTS returning " . $ret);
		return $ret;
	}

	// get the first AMF and reduce the AMF->pts from the AMF->timestamp to know the segment start time
	private function getSegmentStartTime($segment)
	{
		// use floor so we won't have a segment that starts after it's cue points
		return $segment[self::AMF_DATA][0]->timestamp - $segment[self::AMF_DATA][0]->pts;
	}

	// for debug prints
	public function printAMFsForAllSegments(){
		KalturaLog::debug("in printAMFsForAllSegments\n");
		$numSegments = count($this->segments);
		KalturaLog::debug("we have " . $numSegments . "segments\n");

		//while ( $i < $numSegments )
		for ($i = 0; $i < $numSegments; $i++)
		{
			KalturaLog::debug("segment #" . $i);

			$segment = $this->segments[$i];
			$liveStart = $segment[self::LIVE_START];
			$vsd = $segment[self::VOD_SEGMENT_DURATION];
			$dt = $segment[self::VOD_TO_LIVE_DELTA_TIME];
			$amf = $segment[self::AMF_DATA];

			$segmentStartTime = $this->getSegmentStartTime($segment);

			KalturaLog::debug('segmentStartTime: ' . $segmentStartTime);
			KalturaLog::debug('liveStart: ' . print_r($liveStart, true));
			KalturaLog::debug('vsd: ' . print_r($vsd, true));
			KalturaLog::debug('dt: ' . print_r($dt, true));
			KalturaLog::debug('amf: ' . print_r($amf, true));
		}
	}
}
