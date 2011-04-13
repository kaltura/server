<?php

/**
 * 
 * Represents a bulk upload aborted exception caused by job beeing aborted
 * @author Roni
 *
 */
class KalturaBulkUploadAbortedException extends kException
{
	/**
	 * 
	 * Represents that a job was aborted
	 * @var int
	 */
	const JOB_ABORTED = 0;
	
	/**
	 * 
	 * The exception message
	 * @var string
	 */
	public $message = null;

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * 
	 * Creates a new KalturaBulkUploadException
	 * @param string $message - the exception message
	 * @param int $errorCode - the exception error code
	 */
	public function __construct($message, $code)
	{
		$this->message = $message;
		parent::__construct($code);
	}
}