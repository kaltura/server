<?php

abstract class LiveReportExporter {
	
	protected $fileDir;
	protected $fileName = "fileName.csv";
	protected $params = array();
	
	public function __construct(KalturaLiveReportExportJobData $data) {
		$this->params[LiveReportConstants::TIME_REFERENCE_PARAM] = $jobData->timeReference;
		if($jobData->entryIds)
			$this->params[LiveReportConstants::ENTRY_IDS] = $jobData->entryIds;
		
		$this->fileDir = $data->outputPath;
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
		
		$fileName = $this->getDirectory . DIRECTORY_SEPARATOR . $this->fileName;
		$fp = fopen($fileName, 'w');
		
		$engines = $this->getEngines();
		foreach ($engines as $engine) {
			$engine->run($fp, $this->params);
		}
		
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
