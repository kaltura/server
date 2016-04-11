<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage model.data
 */
class kBulkUploadICalJobData extends kBulkUploadJobData
{
	/**
	 * The type of the events that ill be created by this upload
	 * 
	 * @var int
	 */
	protected $eventsType;

	/**
	 * @return the eventsType
	 */
	public function getEventsType() {
		return $this->eventsType;
	}
	
	/**
	 * @param int $eventsType
	 */
	public function setEventsType($eventsType) {
		$this->eventsType = $eventsType;
	}
}
