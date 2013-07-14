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
	
	/**
	 * The host name 
	 * @var        string
	 */
	protected $hostName;
	
	/**
	 * Unique session ID
	 * @var        string
	 */
	protected $sessionId;
	
	public function __construct() {
		$this->timeStamp = date('Y-m-d H:i:s');
	}	
	
	/**
	 * Sets the value of [timestamp] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 */
	public function setTimeStamp($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->timeStamp !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->timeStamp !== null && $tmpDt = new DateTime($this->timeStamp)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->timeStamp = ($dt ? $dt->format('Y-m-d H:i:s') : null);
			}
		} // if either are not null

	} 
	
	public function getTimeStamp($format = 'Y-m-d H:i:s')
	{
		
		if ($this->timeStamp === null) {
			return null;
		}
	
		if ($this->timeStamp === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->timeStamp);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->timeStamp, true), $x);
			}
		}
	
		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
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
	
	/**
	 * @return the $hostName
	 */
	public function getHostName() {
		return $this->hostName;
	}
	
	/**
	 * @return the $sessionId
	 */
	public function getSessionId() {
		return $this->sessionId;
	}
	
	/**
	 * @param string $hostName
	 */
	public function setHostName($hostName) {
		$this->hostName = $hostName;
	}
	
	/**
	 * @param string $sessionId
	 */
	public function setSessionId($sessionId) {
		$this->sessionId = $sessionId;
	}
}
