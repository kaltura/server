<?php

/**
 * This class is an extention of LiveReportLivePartnerEngine that uses exact time.
 */
class LiveReportLivePartnerExactTimeEngine extends LiveReportLivePartnerEngine {
	
	protected $timeReferenceFix;
	
	public function __construct($field, $timeFrame, $title = null) {
		parent::__construct($field, $timeFrame, $title, false);
		$this->timeFrame = 0;
		$this->timeReferenceFix = $timeFrame;
	}

	public function run($fp, array $args = array()) {

		$curTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime - $this->timeReferenceFix;

		$res = parent::run($fp, $args);

		$finalRes = 0;
		foreach($res as $vals) {
			foreach($vals as $val) {
				$finalRes += $val;
			}
		}
		fwrite($fp, $this->title . LiveReportConstants::CELLS_SEPARATOR . $finalRes);

		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime;
		return $res;
	}
}
