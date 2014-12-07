<?php

abstract class LiveReportExporter {
	
	const TIME_RANGE = "TIME_RANGE";
	
	protected $fileName = "fileName.csv";
	protected $params = array();
	protected $dateFormatter;
	
	public function __construct(KalturaLiveReportExportJobData $data, $reportNameFormat, $timeRange) {
		$this->params[LiveReportConstants::IS_LIVE] = false;
		$this->params[LiveReportConstants::TIME_REFERENCE_PARAM] = $data->timeReference;
		if($data->entryIds)
			$this->params[LiveReportConstants::ENTRY_IDS] = $data->entryIds;
		
		$this->dateFormatter = new LiveReportDateFormatter($data->timeZoneOffset);
		
		$fromTime = $this->dateFormatter->format($data->timeReference - $timeRange);
		$toTime = $this->dateFormatter->format($data->timeReference);
		
		$this->params[self::TIME_RANGE] = $fromTime . " - " . $toTime;
		
		$fileNamePrefix = "Export_" . substr(uniqid(),-5);
		$this->fileName = $data->outputPath . DIRECTORY_SEPARATOR . $fileNamePrefix . "_" . $reportNameFormat;
		$this->fileName = vsprintf($this->fileName, array($fromTime, $toTime));
		
		$data->outputPath =  $this->fileName;
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
		$fp = fopen ( $fileName, 'w' );
		if (! $fp)
			throw new KOperationEngineException ( "Failed to open report file : " . $fileName );
		
		KalturaLog::debug ( "Exporting report to $fileName" );
		$engines = $this->getEngines ();
		foreach ( $engines as $engine ) {
			$engine->run ( $fp, $this->params );
			fwrite ( $fp, PHP_EOL );
		}
		
		fclose ( $fp );
		return $fileName;
	}
}

abstract class LiveReportEntryExporter extends LiveReportExporter {
	
	protected $allEntriesEngines = array();
	protected $liveEntriesEngines = array();
	
	public function __construct(KalturaLiveReportExportJobData $data, $reportNameFormat, $timeRange) {
		if(!$data->entryIds)
			throw new KOperationEngineException("Missing mandatory argument entryIds");
		if(count(explode(",", $data->entryIds)) != 1)
			throw new KOperationEngineException("Too many entry ids :" . $data->entryIds);
		
		$reportNameFormat = str_replace("@ENTRY_ID@", $data->entryIds, $reportNameFormat);
		parent::__construct($data, $reportNameFormat, $timeRange);
		
		$this->allEntriesEngines = array(
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportEntryQueryEngine("plays", LiveReportConstants::SECONDS_36_HOURS, "Total Plays:"),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_36_HOURS, "Average Buffering Time per Minute (seconds):"),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_36_HOURS, "Average Bitrate (kbps):"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR));
	
		
		$this->liveEntriesEngines = array(
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
				new LiveReportEntryExactTimeEngine("audience", LiveReportConstants::SECONDS_60, "Total Plays:"),
				new LiveReportEntryQueryEngine("secondsViewed", LiveReportConstants::SECONDS_36_HOURS, "Seconds Viewed:"),
				new LiveReportEntryQueryEngine("bufferTime", LiveReportConstants::SECONDS_60, "Average Buffering Time per Minute (seconds):"),
				new LiveReportEntryQueryEngine("avgBitrate", LiveReportConstants::SECONDS_60, "Average Bitrate (kbps):"),
				new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR));
	}
}
