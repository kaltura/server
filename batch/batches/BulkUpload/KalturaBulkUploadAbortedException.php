<?php
/**
 * Represents a bulk upload aborted exception caused by job beeing aborted
 * @author Roni
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class KalturaBulkUploadAbortedException extends kException
{
	/**
	 * Represents that a job was aborted
	 * @var int
	 */
	const JOB_ABORTED = 0;
	
	/**
	 * @param string $message - the exception message
	 * @param int $errorCode - the exception error code
	 */
	public function __construct($message, $code)
	{
		$this->message = $message;
		parent::__construct($code);
	}
}
