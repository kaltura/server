<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLiveReportExportJobData extends kJobData
{
	public $entryIds;
	
	public $timeReference;
	
	public $outputPath;
	
	public $recipientEmail;
	

	/**
	 * @return the $recipientEmail
	 */
	public function getRecipientEmail() {
		return $this->recipientEmail;
	}

	/**
	 * @param field_type $recipientEmail
	 */
	public function setRecipientEmail($recipientEmail) {
		$this->recipientEmail = $recipientEmail;
	}
	
	/**
	 * @return the $timeReference
	 */
	public function getTimeReference() {
		return $this->timeReference;
	}

	/**
	 * @param field_type $timeReference
	 */
	public function setTimeReference($timeReference) {
		$this->timeReference = $timeReference;
	}
	
	/**
	 * @return the $entryIds
	 */
	public function getEntryIds() {
		return $this->entryIds;
	}

	/**
	 * @param field_type $entryIds
	 */
	public function setEntryIds($entryIds) {
		$this->entryIds = $entryIds;
	}

}
