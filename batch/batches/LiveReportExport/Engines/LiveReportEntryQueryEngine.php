<?php
 
/**
 * This class is the basic implemenation for quering Entry Total report.
 * If you need more complex behavior - please inherit this class
 */
class LiveReportEntryQueryEngine extends LiveReportEngine {
	
	protected $title;
	protected $printResult;
	protected $timeFrame;
	protected $fieldName;
	protected $defaultVal = array(0); // If didn't retrieve any value - use this as default.

	public function __construct($field, $timeFrame, $title = null, $printResult = true) {
		$this->fieldName = $field;
		$this->timeFrame = $timeFrame;
		$this->title = $title;
		$this->printResult = $printResult;
	}
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::IS_LIVE, LiveReportConstants::ENTRY_IDS));

		$reportType = KalturaLiveReportType::ENTRY_TOTAL;
		
		$filter = new KalturaLiveReportInputFilter();
		$filter->live =  $args[LiveReportConstants::IS_LIVE];
		$filter->toTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$filter->fromTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM] - $this->timeFrame;
		$filter->entryIds = $args[LiveReportConstants::ENTRY_IDS];

		$res = LiveReportQueryHelper::retrieveFromReport($reportType, $filter, null, "entryId", $this->fieldName);

		if($this->printResult) {
			if(empty($res))
				$res = $this->defaultVal;
			$msg = $this->title . LiveReportConstants::CELLS_SEPARATOR . implode(LiveReportConstants::CELLS_SEPARATOR, $res);
			fwrite($fp, $msg);
		}
		
		return $res;
	}
	
	public function getTitle() {
		return $this->title;
	}

	public function setDefaultValue($defaultValue) {
		$this->defaultVal = $defaultValue;
	}
}
