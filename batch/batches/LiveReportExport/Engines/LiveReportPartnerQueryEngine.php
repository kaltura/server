<?php

/**
 * This class is the basic implemenation for quering Partner Total report.
 * If you need more complex behavior - please inherit this class
 */
class LiveReportPartnerEngine extends LiveReportEngine {
	
	protected $title;
	protected $timeFrame;
	protected $fieldName;
	
	public function LiveReportPartnerEngine($field, $timeFrame, $title) {
		$this->fieldName = $field;
		$this->timeFrame = $timeFrame;
		$this->title = $title;
	}
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::TIME_REFERENCE_PARAM, LiveReportConstants::IS_LIVE));
		
		$reportType = KalturaLiveReportType::PARTNER_TOTAL;
		$filter = new KalturaLiveReportInputFilter();
		$filter->live = $args[LiveReportConstants::IS_LIVE];
		$filter->toTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
		$filter->fromTime = $args[LiveReportConstants::TIME_REFERENCE_PARAM] - $this->timeFrame;
		if(isset($args[LiveReportConstants::ENTRY_IDS])) 
			$filter->entryIds = $args[LiveReportConstants::ENTRY_IDS];
		
		$res = LiveReportQueryHelper::retrieveFromReport($reportType, $filter, null, null, $this->fieldName);
		fwrite($fp, $this->title . LiveReportConstants::CELLS_SEPARATOR . implode(LiveReportConstants::CELLS_SEPARATOR, $res));
	}

}

/**
 * This class does the same as  LiveReportPartnerEngine, it just validates it has the entry ID parameters as these are must in live partner requests.
 */
class LiveReportLivePartnerEngine extends LiveReportPartnerEngine {
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::ENTRY_IDS));
		parent::run($fp, $args);
	}
}

