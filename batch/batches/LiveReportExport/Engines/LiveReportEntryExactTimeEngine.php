<?php
 
/**
 * This class is an extention of LiveReportEntryQueryEngine that uses exact time.
 */
class LiveReportEntryExactTimeEngine extends LiveReportEntryQueryEngine {
	
	protected $timeReferenceFix;
	protected $shouldPrintResult;

	public function __construct($field, $timeFrame, $title = null, $printResult = true) {
		parent::__construct($field, $timeFrame, $title, false);
		$this->timeFrame = 0;
		$this->timeReferenceFix = $timeFrame;
		$this->shouldPrintResult = $printResult;
	}
	
	public function run($fp, array $args = array()) {
		
		$curTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime - $this->timeReferenceFix;
		
		$res = parent::run($fp, $args);

		$total = 0;
		$entryRes = array();
		foreach($res as $entryId=>$entryVals) {
			$entryVal = 0;
			foreach($entryVals as $val) {
				$total += $val;
				$entryVal += $val;
			}
			$entryRes[$entryId] = $entryVal;
		}
		if($this->shouldPrintResult) { // If we don't print here, the original request was to print outside the engine
			fwrite($fp, $this->getTitle() . LiveReportConstants::CELLS_SEPARATOR . $total);
		}
		else {
			$this->printResult = true;
		}

		$args[LiveReportConstants::TIME_REFERENCE_PARAM] = $curTime;
		return $entryRes;
	}
}
