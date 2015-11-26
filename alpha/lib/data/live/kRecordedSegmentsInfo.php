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
	// 50bytes per AMF * 50 AMFs = 2500 = 2.5k
	// We can get up to 15 AMFs per segment (limit in KAMFMediaInfoParser to max 1 AMF per minute) * 4 segments
	const MAX_AMF_TO_HOLD = 60;
	const MAX_TIME_TO_SAVE_AMF_IN_SECONDS = 3600; //one hour

	protected $AMFs = array();
	protected $lastSegmentEndPTS = 0;

	public function addSegment( $vodSegmentDuration, $AMFs)
	{
		KalturaLog::debug('in addSegment. vodSegmentDuration is ' . $vodSegmentDuration . ' lastSegmentEndPTS was ' . $this->lastSegmentEndPTS);

		for($i=0; $i < count($AMFs); ++$i){
			$amf = $AMFs[$i];
			$amf->pts += $this->lastSegmentEndPTS;
			array_push($this->AMFs, $amf);
		}
		$this->lastSegmentEndPTS += $vodSegmentDuration;

		// now delete old AMFs if needed
		// first delete by date, and after that, if needed, delete last AMFs
		$tsThreshold = $this->AMFs[count($this->AMFs)-1]->ts - self::MAX_TIME_TO_SAVE_AMF_IN_SECONDS * 1000;
		while (count($this->AMFs) > 1 && $this->AMFs[0]->ts < $tsThreshold){
			KalturaLog::debug('removing AMF with TS= ' . $this->AMFs[0]->ts . ' which is below ' . $tsThreshold);
			array_shift($this->AMFs);
		}

		while (count($this->AMFs) > self::MAX_AMF_TO_HOLD){
			KalturaLog::debug('removing AMF since there are over ' . self::MAX_AMF_TO_HOLD . ' AMFs in the array');
			array_shift($this->AMFs);
		}

		KalturaLog::debug('After AMF cleanup array is:' . print_r($this->AMFs, true));
	}

	public function getLastAMFTS(){
		$AMFCount = count($this->AMFs);
		if ($AMFCount == 0){
			return 0;
		}
		return $this->AMFs[$AMFCount-1]->ts;
	}

	public function getOffsetForTimestamp($timestamp){

		KalturaLog::debug('getOffsetForTimestamp ' . $timestamp);

		$minDistanceIndex = $this->getClosestAMFIndex($timestamp);

		$ret = 0;
		if (is_null($minDistanceIndex)){
			KalturaLog::debug('minDistanceIndex is null - returning 0');
		}
		else if ($this->AMFs[$minDistanceIndex]->ts > $timestamp){
			KalturaLog::debug('timestamp is before index #' . $minDistanceIndex);
			$ret = $this->AMFs[$minDistanceIndex]->pts - ($this->AMFs[$minDistanceIndex]->ts - $timestamp);
		}
		else{
			KalturaLog::debug('timestamp is after index #' . $minDistanceIndex);
			$ret = $this->AMFs[$minDistanceIndex]->pts + ($timestamp - $this->AMFs[$minDistanceIndex]->ts);
		}

		KalturaLog::debug('AMFs array is:' . print_r($this->AMFs, true) . 'getOffsetForTimestamp returning ' . $ret);
		return $ret;

	}

	protected function getClosestAMFIndex($timestamp){
		$len = count($this->AMFs);
		$ret = null;

		if ($len == 1){
			$ret = 0;
		}
		else if ($timestamp >= $this->AMFs[$len-1]->ts){
			$ret = $len-1;
		}
		else if ($timestamp <= $this->AMFs[0]->ts){
			$ret = 0;
		}
		else if ($len > 1) {
			$lo = 0;
			$hi = $len - 1;

			while ($hi - $lo > 1) {
				$mid = round(($lo + $hi) / 2);
				if ($this->AMFs[$mid]->ts <= $timestamp) {
					$lo = $mid;
				} else {
					$hi = $mid;
				}
			}

			if (abs($this->AMFs[$hi]->ts - $timestamp) > abs($this->AMFs[$lo]->ts - $timestamp)) {
				return $lo;
			} else {
				return $hi;
			}
		}

		KalturaLog::debug('getClosestAMFIndex returning ' . $ret);
		return $ret;
	}
}