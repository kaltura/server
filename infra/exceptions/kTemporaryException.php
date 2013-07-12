<?php
/**
 * @package infra
 * @subpackage Exceptions
 * 
 * this calss is an exception calss represents a minor exception.
 * the concrete usage of this calss is to pass a batch_job retry request to KAsyncConvert.
 */
class kTemporaryException extends kException
{
	protected $resetJobExecutionAttempts = false;
	
	public function __construct($message, $code = 0)
	{
		parent::__construct($code, $message);
	}
	
	/**
	 * @return the $resetJobExecutionAttempts
	 */
	public function getResetJobExecutionAttempts() {
		return $this->resetJobExecutionAttempts;
	}

	/**
	 * @param bool $resetJobExecutionAttempts
	 */
	public function setResetJobExecutionAttempts($resetJobExecutionAttempts) {
		$this->resetJobExecutionAttempts = $resetJobExecutionAttempts;
	}
	
}