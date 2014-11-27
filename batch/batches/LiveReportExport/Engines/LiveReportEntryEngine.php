<?php 


class LiveReportEntryEngine extends LiveReportEngine {
	
	protected $title;
	protected $fieldName;
	
	public function LiveReportEntryEngine($field, $title = null) {
		$this->fieldName = $field;
		$this->title = $title;
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
			$res[$object->id] = $object->$valueField;
		}
	
		return $res;
	}
	
	public function getTitle() {
		return $this->title;
	}	
}