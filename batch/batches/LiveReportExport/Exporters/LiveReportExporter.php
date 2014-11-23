<?php

abstract class LiveReportExporter {
	
	protected $partnerId;
	protected $fileName = "fileName.csv";
	protected $params = array();
	
	public function __construct($partnerId, KalturaLiveReportExportJobData $data) {
		$this->partnerId = $partnerId;
		$this->params[LiveReportConstants::IS_LIVE] = false;
		$this->params[LiveReportConstants::TIME_REFERENCE_PARAM] = $data->timeReference;
		if($data->entryIds)
			$this->params[LiveReportConstants::ENTRY_IDS] = $data->entryIds;
	}
	
	/**
	 * Returns list of LiveReportEngine needed to create the export
	 */
	abstract protected function getEngines();
	
	/**
	 * Init function - Empty implementation. 
	 * Here should be anything that might throw an exception.
	 * @param KalturaLiveReportExportJobData $jobData
	 */
	public function init(KalturaLiveReportExportJobData $jobData) {
		// Do nothing
	}
	
	public function run() {
		
		$fileName = $this->fileName;
		$fp = fopen($fileName, 'w');
		if(!$fp)
			throw new KOperationEngineException("Failed to open report file : " . $fileName);
		
		KBatchBase::impersonate($this->partnerId);
		KalturaLog::debug("Exporting report to $fileName");
		$engines = $this->getEngines();
		foreach ($engines as $engine) {
			$engine->run($fp, $this->params);
		}
		KBatchBase::unimpersonate();
		
		fclose($fp);
	}
}

abstract class LiveReportEntryExporter extends LiveReportExporter {
	
	protected $allEntriesEngines = array();
	protected $liveEntriesEngines = array();
	
	public function __construct() {
			$this->allEntriesEngines = array(
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportEntryQueryEngine("plays", LiveReportConstants::SECONDS_36_HOURS, "Total Plays:"),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_36_HOURS, "Average Buffering Time per Minute (seconds):"),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_36_HOURS, "Average Bitrate (kbps):"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR));
	
		
		$this->liveEntriesEngines = array(
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportEntryQueryEngine("audience", LiveReportConstants::SECONDS_10, "Total Plays:"),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_60, "Average Buffering Time per Minute (seconds):"),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_10, "Average Bitrate (kbps):"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR));
	}
}
