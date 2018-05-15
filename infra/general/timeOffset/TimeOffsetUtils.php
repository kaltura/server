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
	 * @param int $assetStartTime
	 * @param int $assetEndTime
	 * @return bool
	 */
	public static function onTimeRange($elementStartTime, $elementEndTime, $assetStartTime, $assetEndTime)
	{
		//caption asset items which started before clip start time but ended after the clip started
		if(($elementEndTime >= $assetStartTime) && ($elementStartTime <= $assetStartTime))
			return true;

		//caption asset items which started during clip time range
		if (($elementStartTime >= $assetStartTime) && ($elementStartTime <= $assetEndTime))
			return true;

		return false;
	}


	/**
	 * @param int $elementStartTime
	 * @param int $assetStartTime
	 * @param int $globalOffSet
	 * @return int
	 */
	public static function getAdjustedStartTime($elementStartTime, $assetStartTime, $globalOffSet)
	{
		$adjustedStartTime = $elementStartTime - $assetStartTime + $globalOffSet;
		if ($adjustedStartTime < $globalOffSet)
			$adjustedStartTime = $globalOffSet;
		return $adjustedStartTime;
	}


	/**
	 * @param int $elementEndTime
	 * @param int $assetStartTime
	 * @param int $assetEndTime
	 * @param int $globalOffSet
	 * @return int
	 */
	public static function getAdjustedEndTime($elementEndTime,$assetStartTime, $assetEndTime, $globalOffSet)
	{
		$adjustedEndTime = $elementEndTime - $assetStartTime + $globalOffSet;
		$maxAllowedEndTime = $assetEndTime - $assetStartTime + $globalOffSet;
		if ($adjustedEndTime > $maxAllowedEndTime)
			$adjustedEndTime = $maxAllowedEndTime;
		return $adjustedEndTime;
	}

}