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
	const VOD_SEGMENT_DURATION = 'vsd';
	const AMF_DATA = 'amf';
	const PTS_TIMESTAMP_EPSILON = 100; // when checking for continuety of AMFs, allow up to 100ms of diff
	const ALLOW_OLD_CUE_POINT_TOLERANCE = 2000; // 1 sec for NTP offset and one for the miss-match of time resolution between AMF data and server data

	protected $segments = array();

	public function addSegment( $vodSegmentDuration, $AMFs)
	{
		$this->segments[] = array(
				self::VOD_SEGMENT_DURATION => $vodSegmentDuration,
				self::AMF_DATA => $AMFs
			);
	}

	// Given a timestamp, find it's offset from the beginning of the stream
	// go over each segment and if:
	// $timestamp is after the beginning of the next segment - add the duration of this segment
	// $timestamp is after the end of this segment, but before the beginning of the next one - add the duration of this segment and return
	// $timestamp is in this segment - call getSegmentDurationTillTS, add its return value and return
	// interesting scenarios:
	// - a full segment is missing - return the offset of the end of the previous segment
	// - a chunk is missing in the middle of a segment - return the time of the prev AMF
	// - a chunk is missing in the beginning of a segment - the segment is shorter - return the end of the previous segment
	// - a chunk is missing in the end of a segment - the segment is shorter - return the end of this segment
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
			$segmentEndTime = $this->getSegmentEndTime($segment);
			$nextSegmentStartTime = $nextSegment ? $this->getSegmentStartTime($nextSegment) : null;

			KalturaLog::debug("segmentStartTime: " . $segmentStartTime .
				" segmentEndTime: " . $segmentEndTime .
				" nextSegmentStartTime: " . $nextSegmentStartTime .
				" totalVodOffset: " . $totalVodOffset);

			// since the timestamp on the cue point is in seconds, and everything else is in milliseconds we can get
			// a cue point (that was created less than one second from the beginning of the stream) can get negative time
			// and we will need to "fix" its time
			if ($segmentStartTime > $timestamp){

				if ($segmentStartTime > $timestamp + self::ALLOW_OLD_CUE_POINT_TOLERANCE) {
					KalturaLog::warning("not copying coue point with time " . $timestamp . " segment start is " . $segmentStartTime);
					return null;
				}

				KalturaLog::debug("timestamp " . $timestamp . " passed to getOffsetForTimestamp was less than segmentStartTime " . $segmentStartTime);
				$timestamp = $segmentStartTime;
			}

			// cue point is in this segment
			if ($timestamp >= $segmentStartTime && (is_null($nextSegmentStartTime) || $timestamp < $nextSegmentStartTime)){

				if ($timestamp <= $segmentEndTime) {
					$totalVodOffset += $this->getSegmentDurationTillTS($segment, $timestamp);
				}
				// cue point is after this segment, but not in the next one(or there is no segment after this one) - set it to the end of this segment
				else{
					$totalVodOffset += $segment[self::VOD_SEGMENT_DURATION];
					KalturaLog::debug("required timestamp " . $timestamp . "is between segments. setting its time to the end of this segment");
				}


				KalturaLog::debug("kRecordedSegmentsInfo.getOffsetForTimestamp returning " . $totalVodOffset);
				return $totalVodOffset;
			}
			// cue point is in next segment
			else {
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

		// $timestamp is after the last AMF
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
		KalturaLog::debug("timestamp= " . $timestamp .
			" prevAMF->timestamp= ". $prevAMF->timestamp .
			" prevAMF->pts= " . $prevAMF->pts .
			" nextAMF->timestamp= ". $nextAMF->timestamp .
			" nextAMF->pts= " . $nextAMF->pts);

		// we are between AMFs - check that the difference between the timestamps and the difference between PTSs are the same
		if (!is_null($prevAMF) && !is_null($nextAMF) && !$this->isAMFContinues($prevAMF, $nextAMF)){
			$ret = $prevAMF->pts;
			KalturaLog::warning("AMFs were not Continues - might have missed a chunk in the middle of a segment. AMF1.ts=" . $prevAMF->timestamp . " AMF2.ts=" . $nextAMF->timestamp);
		}
		else if (!is_null($prevAMF)){
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

	private function getSegmentEndTime($segment)
	{
		return $this->getSegmentStartTime($segment) + $segment[self::VOD_SEGMENT_DURATION];
	}

	public function isAMFContinues($prevAMF, $nextAMF){
		$ptsDiff = $nextAMF->pts - $prevAMF->pts;
		$timestampDiff = $nextAMF->timestamp - $prevAMF->timestamp;

		return ($timestampDiff <= $ptsDiff + self::PTS_TIMESTAMP_EPSILON);
	}

	// for debug prints - uncomment to use.
	/*
	public function printAMFsForAllSegments(){
		KalturaLog::debug("in printAMFsForAllSegments\n");
		$numSegments = count($this->segments);
		KalturaLog::debug("we have " . $numSegments . "segments\n");

		//while ( $i < $numSegments )
		for ($i = 0; $i < $numSegments; $i++)
		{
			KalturaLog::debug("segment #" . $i);

			$segment = $this->segments[$i];
			$vsd = $segment[self::VOD_SEGMENT_DURATION];
			$amf = $segment[self::AMF_DATA];

			$segmentStartTime = $this->getSegmentStartTime($segment);

			KalturaLog::debug('segmentStartTime: ' . $segmentStartTime);
			KalturaLog::debug('vsd: ' . print_r($vsd, true));
			KalturaLog::debug('amf: ' . print_r($amf, true));
		}
	}*/
}
