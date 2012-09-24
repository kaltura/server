<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBatchHistoryData 
{
	/**
	 * The value for the scheduler_id field.
	 * @var        int
	 */
	protected $schedulerId;
	
	/**
	 * The value for the worker_id field.
	 * @var        int
	 */
	protected $workerId;
	
	/**
	 * The value for the batch_index field.
	 * @var        int
	 */
	protected $batchIndex;
	
	/**
	 * The value for the update date
	 * @var        int
	 */
	protected $timeStamp;
	
	/**
	 * The value for the message field.
	 * @var        string
	 */
	protected $message;
	
	/**
	 * The value for the err_type field.
	 * @var        int
	 */
	protected $errType;
	
	/**
	 * The value for the err_number field.
	 * @var        int
	 */
	protected $errNumber;
	
	public function __construct() {
		$this->timeStamp = time();
	}	
	
	/**
	 * @return the $timeStamp
	 */
	public function getTimeStamp() {
		return date('Y-m-d H:i:s', $this->timeStamp);
	}

	/**
	 * @param string $timeStamp
	 */
	public function setTimeStamp($v) {
		$this->timeStamp = $v;
	}

	/**
	 * @return the $schedulerId
	 */
	public function getSchedulerId() {
		return $this->schedulerId;
	}

	/**
	 * @return the $workerId
	 */
	public function getWorkerId() {
		return $this->workerId;
	}

	/**
	 * @return the $batchIndex
	 */
	public function getBatchIndex() {
		return $this->batchIndex;
	}

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return the $errType
	 */
	public function getErrType() {
		return $this->errType;
	}

	/**
	 * @return the $errNumber
	 */
	public function getErrNumber() {
		return $this->errNumber;
	}

	/**
	 * @param number $schedulerId
	 */
	public function setSchedulerId($schedulerId) {
		$this->schedulerId = $schedulerId;
	}

	/**
	 * @param number $workerId
	 */
	public function setWorkerId($workerId) {
		$this->workerId = $workerId;
	}

	/**
	 * @param number $batchIndex
	 */
	public function setBatchIndex($batchIndex) {
		$this->batchIndex = $batchIndex;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @param number $errType
	 */
	public function setErrType($errType) {
		$this->errType = $errType;
	}

	/**
	 * @param number $errNumber
	 */
	public function setErrNumber($errNumber) {
		$this->errNumber = $errNumber;
	}
}
