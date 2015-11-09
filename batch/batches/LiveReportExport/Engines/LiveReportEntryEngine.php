<?php 


class LiveReportEntryEngine extends LiveReportEngine {
	
	protected $title;
	protected $fieldName;
	protected $formatter;
	protected $printResult;
	
	public function __construct($field, $title = null, LiveReportFormatter $formatter = null, $printResult = false) {
		$this->fieldName = $field;
		$this->title = $title;
		$this->formatter = $formatter;
		$this->printResult = $printResult;
	}
	
	public function run($fp, array $args = array()) {
		$this->checkParams($args, array(LiveReportConstants::ENTRY_IDS));
	
		$filter = new KalturaLiveStreamEntryFilter();
		$filter->idIn = $args[LiveReportConstants::ENTRY_IDS];
		
		/** @var KalturaLiveStreamListResponse */
		$response = KBatchBase::$kClient->liveStream->listAction($filter, null);
		
		$valueField = $this->fieldName;
		$res = array();
		foreach($response->objects as $object) {
			if($this->formatter)
				$res[$object->id] = $this->formatter->format($object->$valueField);
			else 
				$res[$object->id] = $object->$valueField;
		}
	
		if($this->printResult) {
			$msg = $this->title . LiveReportConstants::CELLS_SEPARATOR . implode(LiveReportConstants::CELLS_SEPARATOR, $res);
			fwrite($fp, $msg);
		}
		
		return $res;
	}
	
	public function getTitle() {
		return $this->title;
	}	
}
