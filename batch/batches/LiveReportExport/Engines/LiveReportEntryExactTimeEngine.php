<?php
 
/**
 * This class is an extention of LiveReportEntryQueryEngine that uses exact time.
 */
class LiveReportEntryExactTimeEngine extends LiveReportEntryQueryEngine {
	
	protected $timeReferenceFix;
	
	public function LiveReportEntryQueryEngine($field, $timeFrame, $title = null, $printResult = true) {
		parent::__construct($field, $timeFrame, $title, $printResult);
		$this->timeFrame = 0;
		$this->timeReferenceFix = $timeFrame;
	}
	
	public function run($fp, array $args = array()) {
		
		$curTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime - $this->timeReferenceFix;
		
		$res = parent::run($fp, $args);
		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime;
		return $res;
	}
}
