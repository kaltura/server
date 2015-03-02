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

	protected $segments = array();

	public function addSegment( $liveStart, $vodSegmentDuration, $vodToLiveDeltaTime )
	{
		$this->segments[] = array(
				self::LIVE_START => $liveStart,
				self::VOD_SEGMENT_DURATION => $vodSegmentDuration,
				self::VOD_TO_LIVE_DELTA_TIME => $vodToLiveDeltaTime,
			);
	}

	/**
	 * Get the total VOD offset for a given time. Total = the sum of all VOD delta times throughout the segments.
	 * @return int|null The offset that should be subtracted from the given time, or
	 * 					null to indicate that this time signature is in the gap between the end
	 * 					of a VOD segment and the end of the live segment (marked with x's below).
	 *					<pre>
	 *					Live Start          VOD End    Live End
	 *					   ^                  ^           ^
	 *					   |                  |xxxxxxxxxxx|
	 *					   +------------------+-----------+
	 *					</pre>
	 */
	public function getTotalVodTimeOffset( $time )
	{
		$numSegments = count($this->segments);

		$totalVodOffset = 0; // Initially zero because there's no time drift in the first segment.
		$i = 0;

		$dbgPrevSegment = null;
		while ( $i < $numSegments )
		{
			$segment = $this->segments[$i];
			$liveStart = $segment[self::LIVE_START];
			$vodAdjustedEndTime = $this->getVodAdjustedEndTime($segment);
			if ( $time <= $vodAdjustedEndTime )
			{
				if ( $time >= $liveStart )
				{
					return $totalVodOffset;
				}
				else
				{
					KalturaLog::debug("Time [$time] <= $vodAdjustedEndTime but not >= $liveStart. Segment data: {$this->segmentAsString($dbgPrevSegment)}");
					return null;
				}
			}
			else
			{
				// Add up this segment's offset and move on to the next segment
				$totalVodOffset += $segment[self::VOD_TO_LIVE_DELTA_TIME];
				$dbgPrevSegment = $segment;
				$i++;
			}
		}

		// The time signature is greater than the total VOD duration
		KalturaLog::debug("Couldn't get offset for time [$time]. Segment data: {$this->segmentAsString($segment)}");
		return null;
	}

	/**
	 * Not the real VOD end time, rather current segment's (live start time) + (vod duration).
	 */
	protected function getVodAdjustedEndTime( $segment )
	{
		if ( $segment )
		{
			return $segment[self::LIVE_START] + $segment[self::VOD_SEGMENT_DURATION];
		}

		return -1;
	}

	protected function getLiveEndTime( $segment )
	{
		if ( $segment )
		{
			return $segment[self::LIVE_START] + $segment[self::VOD_SEGMENT_DURATION] + $segment[self::VOD_TO_LIVE_DELTA_TIME];
		}

		return -1;
	}

	public function segmentAsString( $segment )
	{
		if ( $segment )
		{
			return "Live start: {$segment[self::LIVE_START]}, Live end: {$this->getLiveEndTime($segment)}, VOD translated end: {$this->getVodAdjustedEndTime($segment)} (VOD segment duration: {$segment[self::VOD_SEGMENT_DURATION]}, VOD to live delta: {$segment[self::VOD_TO_LIVE_DELTA_TIME]})";
		}

		return "N/A";
	}
}
