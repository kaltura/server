<?php
/**
 * @package infra
 * @subpackage general
 */

/**
 * Asset - the adjusted video times
 * element - the element(caption \ cue point etc... )
 *
 */
class TimeOffsetUtils
{

	/**
	 * @param int $elementStartTime
	 * @param int $elementEndTime
	 * @param int $timeLineStart
	 * @param int $timeLineEnd
	 * @return bool
	 */
	public static function onTimeRange($elementStartTime, $elementEndTime, $timeLineStart, $timeLineEnd)
	{
		if ($elementEndTime < $timeLineStart || $elementStartTime > $timeLineEnd)
			return false;
		return true;
	}


	/**
	 * @param int $elementStartTime
	 * @param int $timeLineStart
	 * @param int $timeLineEnd
	 * @return int
	 */
	public static function getAdjustedStartTime($elementStartTime, $timeLineStart, $timeLineEnd)
	{
		$adjustedStartTime = $elementStartTime - $timeLineStart + $timeLineEnd;
		if ($adjustedStartTime < $timeLineEnd)
			$adjustedStartTime = $timeLineEnd;
		return $adjustedStartTime;
	}


	/**
	 * @param int $elementEndTime
	 * @param int $timeLineStart
	 * @param int $timeLineEnd
	 * @param int $globalOffSet
	 * @return int
	 */
	public static function getAdjustedEndTime($elementEndTime, $timeLineStart, $timeLineEnd, $globalOffSet)
	{
		$adjustedEndTime = $elementEndTime - $timeLineStart + $globalOffSet;
		$maxAllowedEndTime = $timeLineEnd - $timeLineStart + $globalOffSet;
		if ($adjustedEndTime > $maxAllowedEndTime)
			$adjustedEndTime = $maxAllowedEndTime;
		return $adjustedEndTime;
	}

}