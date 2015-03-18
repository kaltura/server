<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLiveReportExportJobData extends kJobData
{
	public $entryIds;
	
	public $timeReference;
	
	public $timeZoneOffset;
	
	public $outputPath;
	
	public $recipientEmail;

	public $applicationUrlTemplate;
	
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
	
	/**
	 * @return the $outputPath
	 */
	public function getOutputPath() {
		return $this->outputPath;
	}

	/**
	 * @param field_type $outputPath
	 */
	public function setOutputPath($outputPath) {
		$this->outputPath = $outputPath;
	}
	
	/**
	 * @return the $timeZoneOffset
	 */
	public function getTimeZoneOffset() {
		return $this->timeZoneOffset;
	}

	/**
	 * @param field_type $timeZoneOffset
	 */
	public function setTimeZoneOffset($timeZoneOffset) {
		$this->timeZoneOffset = $timeZoneOffset;
	}

	/**
	 * @return the $applicationUrlTemplate
	 */
	public function getApplicationUrlTemplate() {
		return $this->applicationUrlTemplate;
	}

	/**
	 * @param string $applicationUrlTemplate
	 */
	public function setApplicationUrlTemplate($applicationUrlTemplate) {
		$this->applicationUrlTemplate = $applicationUrlTemplate;
	}
	
}
