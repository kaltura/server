<?php

/**
 * Base class that represents a row from the 'batch_job_sep' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBatchJob extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        BatchJobPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the job_type field.
	 * @var        int
	 */
	protected $job_type;

	/**
	 * The value for the job_sub_type field.
	 * @var        int
	 */
	protected $job_sub_type;

	/**
	 * The value for the object_id field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $object_id;

	/**
	 * The value for the object_type field.
	 * @var        int
	 */
	protected $object_type;

	/**
	 * The value for the data field.
	 * @var        string
	 */
	protected $data;

	/**
	 * The value for the history field.
	 * @var        string
	 */
	protected $history;

	/**
	 * The value for the lock_info field.
	 * @var        string
	 */
	protected $lock_info;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the execution_status field.
	 * @var        int
	 */
	protected $execution_status;

	/**
	 * The value for the message field.
	 * @var        string
	 */
	protected $message;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

	/**
	 * The value for the queue_time field.
	 * @var        string
	 */
	protected $queue_time;

	/**
	 * The value for the finish_time field.
	 * @var        string
	 */
	protected $finish_time;

	/**
	 * The value for the entry_id field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the partner_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the bulk_job_id field.
	 * @var        int
	 */
	protected $bulk_job_id;

	/**
	 * The value for the root_job_id field.
	 * @var        int
	 */
	protected $root_job_id;

	/**
	 * The value for the parent_job_id field.
	 * @var        int
	 */
	protected $parent_job_id;

	/**
	 * The value for the last_scheduler_id field.
	 * @var        int
	 */
	protected $last_scheduler_id;

	/**
	 * The value for the last_worker_id field.
	 * @var        int
	 */
	protected $last_worker_id;

	/**
	 * The value for the dc field.
	 * @var        int
	 */
	protected $dc;

	/**
	 * The value for the err_type field.
	 * @var        int
	 */
	protected $err_type;

	/**
	 * The value for the err_number field.
	 * @var        int
	 */
	protected $err_number;

	/**
	 * The value for the batch_job_lock_id field.
	 * @var        int
	 */
	protected $batch_job_lock_id;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * @var        BatchJobLock
	 */
	protected $aBatchJobLock;

	/**
	 * @var        array BatchJobLock[] Collection to store aggregation of BatchJobLock objects.
	 */
	protected $collBatchJobLocks;

	/**
	 * @var        Criteria The criteria used to select the current contents of collBatchJobLocks.
	 */
	private $lastBatchJobLockCriteria = null;

	/**
	 * @var        array BatchJobLockSuspend[] Collection to store aggregation of BatchJobLockSuspend objects.
	 */
	protected $collBatchJobLockSuspends;

	/**
	 * @var        Criteria The criteria used to select the current contents of collBatchJobLockSuspends.
	 */
	private $lastBatchJobLockSuspendCriteria = null;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to indicate if save action actually affected the db.
	 * @var        boolean
	 */
	protected $objectSaved = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Store columns old values before the changes
	 * @var        array
	 */
	protected $oldColumnsValues = array();
	
	/**
	 * @return array
	 */
	public function getColumnsOldValues()
	{
		return $this->oldColumnsValues;
	}
	
	/**
	 * @return mixed field value or null
	 */
	public function getColumnsOldValue($name)
	{
		if(isset($this->oldColumnsValues[$name]))
			return $this->oldColumnsValues[$name];
			
		return null;
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->object_id = '';
		$this->entry_id = '';
		$this->partner_id = 0;
	}

	/**
	 * Initializes internal state of BaseBatchJob object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [job_type] column value.
	 * 
	 * @return     int
	 */
	public function getJobType()
	{
		return $this->job_type;
	}

	/**
	 * Get the [job_sub_type] column value.
	 * 
	 * @return     int
	 */
	public function getJobSubType()
	{
		return $this->job_sub_type;
	}

	/**
	 * Get the [object_id] column value.
	 * 
	 * @return     string
	 */
	public function getObjectId()
	{
		return $this->object_id;
	}

	/**
	 * Get the [object_type] column value.
	 * 
	 * @return     int
	 */
	public function getObjectType()
	{
		return $this->object_type;
	}

	/**
	 * Get the [data] column value.
	 * 
	 * @return     string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the [history] column value.
	 * 
	 * @return     string
	 */
	public function getHistory()
	{
		return $this->history;
	}

	/**
	 * Get the [lock_info] column value.
	 * 
	 * @return     string
	 */
	public function getLockInfo()
	{
		return $this->lock_info;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the [execution_status] column value.
	 * 
	 * @return     int
	 */
	public function getExecutionStatus()
	{
		return $this->execution_status;
	}

	/**
	 * Get the [message] column value.
	 * 
	 * @return     string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
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
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
	 * Get the [priority] column value.
	 * 
	 * @return     int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Get the [optionally formatted] temporal [queue_time] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getQueueTime($format = 'Y-m-d H:i:s')
	{
		if ($this->queue_time === null) {
			return null;
		}


		if ($this->queue_time === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->queue_time);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->queue_time, true), $x);
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
	 * Get the [optionally formatted] temporal [finish_time] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFinishTime($format = 'Y-m-d H:i:s')
	{
		if ($this->finish_time === null) {
			return null;
		}


		if ($this->finish_time === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->finish_time);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->finish_time, true), $x);
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
	 * Get the [entry_id] column value.
	 * 
	 * @return     string
	 */
	public function getEntryId()
	{
		return $this->entry_id;
	}

	/**
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [bulk_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getBulkJobId()
	{
		return $this->bulk_job_id;
	}

	/**
	 * Get the [root_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getRootJobId()
	{
		return $this->root_job_id;
	}

	/**
	 * Get the [parent_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getParentJobId()
	{
		return $this->parent_job_id;
	}

	/**
	 * Get the [last_scheduler_id] column value.
	 * 
	 * @return     int
	 */
	public function getLastSchedulerId()
	{
		return $this->last_scheduler_id;
	}

	/**
	 * Get the [last_worker_id] column value.
	 * 
	 * @return     int
	 */
	public function getLastWorkerId()
	{
		return $this->last_worker_id;
	}

	/**
	 * Get the [dc] column value.
	 * 
	 * @return     int
	 */
	public function getDc()
	{
		return $this->dc;
	}

	/**
	 * Get the [err_type] column value.
	 * 
	 * @return     int
	 */
	public function getErrType()
	{
		return $this->err_type;
	}

	/**
	 * Get the [err_number] column value.
	 * 
	 * @return     int
	 */
	public function getErrNumber()
	{
		return $this->err_number;
	}

	/**
	 * Get the [batch_job_lock_id] column value.
	 * 
	 * @return     int
	 */
	public function getBatchJobLockId()
	{
		return $this->batch_job_lock_id;
	}

	/**
	 * Get the [custom_data] column value.
	 * 
	 * @return     string
	 */
	public function getCustomData()
	{
		return $this->custom_data;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::ID]))
			$this->oldColumnsValues[BatchJobPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = BatchJobPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [job_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setJobType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::JOB_TYPE]))
			$this->oldColumnsValues[BatchJobPeer::JOB_TYPE] = $this->job_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_type !== $v) {
			$this->job_type = $v;
			$this->modifiedColumns[] = BatchJobPeer::JOB_TYPE;
		}

		return $this;
	} // setJobType()

	/**
	 * Set the value of [job_sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setJobSubType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::JOB_SUB_TYPE]))
			$this->oldColumnsValues[BatchJobPeer::JOB_SUB_TYPE] = $this->job_sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_sub_type !== $v) {
			$this->job_sub_type = $v;
			$this->modifiedColumns[] = BatchJobPeer::JOB_SUB_TYPE;
		}

		return $this;
	} // setJobSubType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::OBJECT_ID]))
			$this->oldColumnsValues[BatchJobPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v || $this->isNew()) {
			$this->object_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[BatchJobPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = BatchJobPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setData($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::DATA]))
			$this->oldColumnsValues[BatchJobPeer::DATA] = $this->data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->data !== $v) {
			$this->data = $v;
			$this->modifiedColumns[] = BatchJobPeer::DATA;
		}

		return $this;
	} // setData()

	/**
	 * Set the value of [history] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setHistory($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::HISTORY]))
			$this->oldColumnsValues[BatchJobPeer::HISTORY] = $this->history;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->history !== $v) {
			$this->history = $v;
			$this->modifiedColumns[] = BatchJobPeer::HISTORY;
		}

		return $this;
	} // setHistory()

	/**
	 * Set the value of [lock_info] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLockInfo($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::LOCK_INFO]))
			$this->oldColumnsValues[BatchJobPeer::LOCK_INFO] = $this->lock_info;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->lock_info !== $v) {
			$this->lock_info = $v;
			$this->modifiedColumns[] = BatchJobPeer::LOCK_INFO;
		}

		return $this;
	} // setLockInfo()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::STATUS]))
			$this->oldColumnsValues[BatchJobPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = BatchJobPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [execution_status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setExecutionStatus($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::EXECUTION_STATUS]))
			$this->oldColumnsValues[BatchJobPeer::EXECUTION_STATUS] = $this->execution_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_status !== $v) {
			$this->execution_status = $v;
			$this->modifiedColumns[] = BatchJobPeer::EXECUTION_STATUS;
		}

		return $this;
	} // setExecutionStatus()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setMessage($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::MESSAGE]))
			$this->oldColumnsValues[BatchJobPeer::MESSAGE] = $this->message;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->message !== $v) {
			$this->message = $v;
			$this->modifiedColumns[] = BatchJobPeer::MESSAGE;
		}

		return $this;
	} // setMessage()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::DESCRIPTION]))
			$this->oldColumnsValues[BatchJobPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = BatchJobPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
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

		if ( $this->created_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->created_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
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

		if ( $this->updated_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->updated_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [priority] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::PRIORITY]))
			$this->oldColumnsValues[BatchJobPeer::PRIORITY] = $this->priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = BatchJobPeer::PRIORITY;
		}

		return $this;
	} // setPriority()

	/**
	 * Sets the value of [queue_time] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setQueueTime($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::QUEUE_TIME]))
			$this->oldColumnsValues[BatchJobPeer::QUEUE_TIME] = $this->queue_time;

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

		if ( $this->queue_time !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->queue_time !== null && $tmpDt = new DateTime($this->queue_time)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->queue_time = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::QUEUE_TIME;
			}
		} // if either are not null

		return $this;
	} // setQueueTime()

	/**
	 * Sets the value of [finish_time] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setFinishTime($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::FINISH_TIME]))
			$this->oldColumnsValues[BatchJobPeer::FINISH_TIME] = $this->finish_time;

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

		if ( $this->finish_time !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->finish_time !== null && $tmpDt = new DateTime($this->finish_time)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->finish_time = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::FINISH_TIME;
			}
		} // if either are not null

		return $this;
	} // setFinishTime()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::ENTRY_ID]))
			$this->oldColumnsValues[BatchJobPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v || $this->isNew()) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::PARTNER_ID]))
			$this->oldColumnsValues[BatchJobPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [bulk_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setBulkJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::BULK_JOB_ID]))
			$this->oldColumnsValues[BatchJobPeer::BULK_JOB_ID] = $this->bulk_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->bulk_job_id !== $v) {
			$this->bulk_job_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::BULK_JOB_ID;
		}

		return $this;
	} // setBulkJobId()

	/**
	 * Set the value of [root_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setRootJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::ROOT_JOB_ID]))
			$this->oldColumnsValues[BatchJobPeer::ROOT_JOB_ID] = $this->root_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->root_job_id !== $v) {
			$this->root_job_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::ROOT_JOB_ID;
		}

		return $this;
	} // setRootJobId()

	/**
	 * Set the value of [parent_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setParentJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::PARENT_JOB_ID]))
			$this->oldColumnsValues[BatchJobPeer::PARENT_JOB_ID] = $this->parent_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->parent_job_id !== $v) {
			$this->parent_job_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::PARENT_JOB_ID;
		}

		return $this;
	} // setParentJobId()

	/**
	 * Set the value of [last_scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLastSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::LAST_SCHEDULER_ID]))
			$this->oldColumnsValues[BatchJobPeer::LAST_SCHEDULER_ID] = $this->last_scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->last_scheduler_id !== $v) {
			$this->last_scheduler_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::LAST_SCHEDULER_ID;
		}

		return $this;
	} // setLastSchedulerId()

	/**
	 * Set the value of [last_worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLastWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::LAST_WORKER_ID]))
			$this->oldColumnsValues[BatchJobPeer::LAST_WORKER_ID] = $this->last_worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->last_worker_id !== $v) {
			$this->last_worker_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::LAST_WORKER_ID;
		}

		return $this;
	} // setLastWorkerId()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::DC]))
			$this->oldColumnsValues[BatchJobPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = BatchJobPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [err_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setErrType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::ERR_TYPE]))
			$this->oldColumnsValues[BatchJobPeer::ERR_TYPE] = $this->err_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->err_type !== $v) {
			$this->err_type = $v;
			$this->modifiedColumns[] = BatchJobPeer::ERR_TYPE;
		}

		return $this;
	} // setErrType()

	/**
	 * Set the value of [err_number] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setErrNumber($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::ERR_NUMBER]))
			$this->oldColumnsValues[BatchJobPeer::ERR_NUMBER] = $this->err_number;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->err_number !== $v) {
			$this->err_number = $v;
			$this->modifiedColumns[] = BatchJobPeer::ERR_NUMBER;
		}

		return $this;
	} // setErrNumber()

	/**
	 * Set the value of [batch_job_lock_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setBatchJobLockId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobPeer::BATCH_JOB_LOCK_ID]))
			$this->oldColumnsValues[BatchJobPeer::BATCH_JOB_LOCK_ID] = $this->batch_job_lock_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_job_lock_id !== $v) {
			$this->batch_job_lock_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::BATCH_JOB_LOCK_ID;
		}

		if ($this->aBatchJobLock !== null && $this->aBatchJobLock->getId() !== $v) {
			$this->aBatchJobLock = null;
		}

		return $this;
	} // setBatchJobLockId()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = BatchJobPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->object_id !== '') {
				return false;
			}

			if ($this->entry_id !== '') {
				return false;
			}

			if ($this->partner_id !== 0) {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->job_type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->job_sub_type = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->object_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->object_type = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->data = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->history = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->lock_info = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->status = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->execution_status = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->message = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->description = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->created_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->updated_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->priority = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->queue_time = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->finish_time = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->entry_id = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->partner_id = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->bulk_job_id = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->root_job_id = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->parent_job_id = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->last_scheduler_id = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->last_worker_id = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->dc = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->err_type = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->err_number = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->batch_job_lock_id = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->custom_data = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 29; // 29 = BatchJobPeer::NUM_COLUMNS - BatchJobPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating BatchJob object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aBatchJobLock !== null && $this->batch_job_lock_id !== $this->aBatchJobLock->getId()) {
			$this->aBatchJobLock = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		BatchJobPeer::setUseCriteriaFilter(false);
		$stmt = BatchJobPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		BatchJobPeer::setUseCriteriaFilter(true);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aBatchJobLock = null;
			$this->collBatchJobLocks = null;
			$this->lastBatchJobLockCriteria = null;

			$this->collBatchJobLockSuspends = null;
			$this->lastBatchJobLockSuspendCriteria = null;

		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				BatchJobPeer::doDelete($this, $con);
				$this->postDelete($con);
				$this->setDeleted(true);
				$con->commit();
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				BatchJobPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}
	
	public function wasObjectSaved()
	{
		return $this->objectSaved;
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aBatchJobLock !== null) {
				if ($this->aBatchJobLock->isModified() || $this->aBatchJobLock->isNew()) {
					$affectedRows += $this->aBatchJobLock->save($con);
				}
				$this->setBatchJobLock($this->aBatchJobLock);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = BatchJobPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = BatchJobPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = BatchJobPeer::doUpdate($this, $con);
					if($affectedObjects)
						$this->objectSaved = true;
						
					$affectedRows += $affectedObjects;
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collBatchJobLocks !== null) {
				foreach ($this->collBatchJobLocks as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collBatchJobLockSuspends !== null) {
				foreach ($this->collBatchJobLockSuspends as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Override in order to use the query cache.
	 * Cache invalidation keys are used to determine when cached queries are valid.
	 * Before returning a query result from the cache, the time of the cached query
	 * is compared to the time saved in the invalidation key.
	 * A cached query will only be used if it's newer than the matching invalidation key.
	 *  
	 * @return     array Array of keys that will should be updated when this object is modified.
	 */
	public function getCacheInvalidationKeys()
	{
		return array();
	}
		
	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO $con
	 * @return bloolean
	 */
	public function preSave(PropelPDO $con = null)
	{
		$this->setCustomDataObj();
    	
		return parent::preSave($con);
	}

	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null) 
	{
		kEventsManager::raiseEvent(new kObjectSavedEvent($this));
		$this->oldColumnsValues = array();
		$this->oldCustomDataValues = array();
    	 
		parent::postSave($con);
	}
	
	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
    	$this->setCreatedAt(time());
    	
		$this->setUpdatedAt(time());
		return parent::preInsert($con);
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		kQueryCache::invalidateQueryCache($this);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
		
		parent::postInsert($con);
	}

	/**
	 * Code to be run after updating the object in database
	 * @param PropelPDO $con
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
		{
			return;
		}
	
		if($this->isModified())
		{
			kQueryCache::invalidateQueryCache($this);
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->tempModifiedColumns));
		}
			
		$this->tempModifiedColumns = array();
		
		parent::postUpdate($con);
	}
	/**
	 * Saves the modified columns temporarily while saving
	 * @var array
	 */
	private $tempModifiedColumns = array();
	
	/**
	 * Returns whether the object has been modified.
	 *
	 * @return     boolean True if the object has been modified.
	 */
	public function isModified()
	{
		if(!empty($this->tempModifiedColumns))
			return true;
			
		return !empty($this->modifiedColumns);
	}

	/**
	 * Has specified column been modified?
	 *
	 * @param      string $col
	 * @return     boolean True if $col has been modified.
	 */
	public function isColumnModified($col)
	{
		if(in_array($col, $this->tempModifiedColumns))
			return true;
			
		return in_array($col, $this->modifiedColumns);
	}

	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
		{
			return true;
		}	
		
		
		if($this->isModified())
			$this->setUpdatedAt(time());
		
		$this->tempModifiedColumns = $this->modifiedColumns;
		return parent::preUpdate($con);
	}
	
	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aBatchJobLock !== null) {
				if (!$this->aBatchJobLock->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aBatchJobLock->getValidationFailures());
				}
			}


			if (($retval = BatchJobPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collBatchJobLocks !== null) {
					foreach ($this->collBatchJobLocks as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collBatchJobLockSuspends !== null) {
					foreach ($this->collBatchJobLockSuspends as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = BatchJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getJobType();
				break;
			case 2:
				return $this->getJobSubType();
				break;
			case 3:
				return $this->getObjectId();
				break;
			case 4:
				return $this->getObjectType();
				break;
			case 5:
				return $this->getData();
				break;
			case 6:
				return $this->getHistory();
				break;
			case 7:
				return $this->getLockInfo();
				break;
			case 8:
				return $this->getStatus();
				break;
			case 9:
				return $this->getExecutionStatus();
				break;
			case 10:
				return $this->getMessage();
				break;
			case 11:
				return $this->getDescription();
				break;
			case 12:
				return $this->getCreatedAt();
				break;
			case 13:
				return $this->getUpdatedAt();
				break;
			case 14:
				return $this->getPriority();
				break;
			case 15:
				return $this->getQueueTime();
				break;
			case 16:
				return $this->getFinishTime();
				break;
			case 17:
				return $this->getEntryId();
				break;
			case 18:
				return $this->getPartnerId();
				break;
			case 19:
				return $this->getBulkJobId();
				break;
			case 20:
				return $this->getRootJobId();
				break;
			case 21:
				return $this->getParentJobId();
				break;
			case 22:
				return $this->getLastSchedulerId();
				break;
			case 23:
				return $this->getLastWorkerId();
				break;
			case 24:
				return $this->getDc();
				break;
			case 25:
				return $this->getErrType();
				break;
			case 26:
				return $this->getErrNumber();
				break;
			case 27:
				return $this->getBatchJobLockId();
				break;
			case 28:
				return $this->getCustomData();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                        BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
	 * @param      boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns.  Defaults to TRUE.
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = BatchJobPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getJobType(),
			$keys[2] => $this->getJobSubType(),
			$keys[3] => $this->getObjectId(),
			$keys[4] => $this->getObjectType(),
			$keys[5] => $this->getData(),
			$keys[6] => $this->getHistory(),
			$keys[7] => $this->getLockInfo(),
			$keys[8] => $this->getStatus(),
			$keys[9] => $this->getExecutionStatus(),
			$keys[10] => $this->getMessage(),
			$keys[11] => $this->getDescription(),
			$keys[12] => $this->getCreatedAt(),
			$keys[13] => $this->getUpdatedAt(),
			$keys[14] => $this->getPriority(),
			$keys[15] => $this->getQueueTime(),
			$keys[16] => $this->getFinishTime(),
			$keys[17] => $this->getEntryId(),
			$keys[18] => $this->getPartnerId(),
			$keys[19] => $this->getBulkJobId(),
			$keys[20] => $this->getRootJobId(),
			$keys[21] => $this->getParentJobId(),
			$keys[22] => $this->getLastSchedulerId(),
			$keys[23] => $this->getLastWorkerId(),
			$keys[24] => $this->getDc(),
			$keys[25] => $this->getErrType(),
			$keys[26] => $this->getErrNumber(),
			$keys[27] => $this->getBatchJobLockId(),
			$keys[28] => $this->getCustomData(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = BatchJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setJobType($value);
				break;
			case 2:
				$this->setJobSubType($value);
				break;
			case 3:
				$this->setObjectId($value);
				break;
			case 4:
				$this->setObjectType($value);
				break;
			case 5:
				$this->setData($value);
				break;
			case 6:
				$this->setHistory($value);
				break;
			case 7:
				$this->setLockInfo($value);
				break;
			case 8:
				$this->setStatus($value);
				break;
			case 9:
				$this->setExecutionStatus($value);
				break;
			case 10:
				$this->setMessage($value);
				break;
			case 11:
				$this->setDescription($value);
				break;
			case 12:
				$this->setCreatedAt($value);
				break;
			case 13:
				$this->setUpdatedAt($value);
				break;
			case 14:
				$this->setPriority($value);
				break;
			case 15:
				$this->setQueueTime($value);
				break;
			case 16:
				$this->setFinishTime($value);
				break;
			case 17:
				$this->setEntryId($value);
				break;
			case 18:
				$this->setPartnerId($value);
				break;
			case 19:
				$this->setBulkJobId($value);
				break;
			case 20:
				$this->setRootJobId($value);
				break;
			case 21:
				$this->setParentJobId($value);
				break;
			case 22:
				$this->setLastSchedulerId($value);
				break;
			case 23:
				$this->setLastWorkerId($value);
				break;
			case 24:
				$this->setDc($value);
				break;
			case 25:
				$this->setErrType($value);
				break;
			case 26:
				$this->setErrNumber($value);
				break;
			case 27:
				$this->setBatchJobLockId($value);
				break;
			case 28:
				$this->setCustomData($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = BatchJobPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setJobType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJobSubType($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjectId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setObjectType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setData($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setHistory($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLockInfo($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStatus($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setExecutionStatus($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setMessage($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDescription($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setCreatedAt($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setUpdatedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPriority($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setQueueTime($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setFinishTime($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setEntryId($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setPartnerId($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setBulkJobId($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setRootJobId($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setParentJobId($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setLastSchedulerId($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setLastWorkerId($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setDc($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setErrType($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setErrNumber($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setBatchJobLockId($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setCustomData($arr[$keys[28]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);

		if ($this->isColumnModified(BatchJobPeer::ID)) $criteria->add(BatchJobPeer::ID, $this->id);
		if ($this->isColumnModified(BatchJobPeer::JOB_TYPE)) $criteria->add(BatchJobPeer::JOB_TYPE, $this->job_type);
		if ($this->isColumnModified(BatchJobPeer::JOB_SUB_TYPE)) $criteria->add(BatchJobPeer::JOB_SUB_TYPE, $this->job_sub_type);
		if ($this->isColumnModified(BatchJobPeer::OBJECT_ID)) $criteria->add(BatchJobPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(BatchJobPeer::OBJECT_TYPE)) $criteria->add(BatchJobPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(BatchJobPeer::DATA)) $criteria->add(BatchJobPeer::DATA, $this->data);
		if ($this->isColumnModified(BatchJobPeer::HISTORY)) $criteria->add(BatchJobPeer::HISTORY, $this->history);
		if ($this->isColumnModified(BatchJobPeer::LOCK_INFO)) $criteria->add(BatchJobPeer::LOCK_INFO, $this->lock_info);
		if ($this->isColumnModified(BatchJobPeer::STATUS)) $criteria->add(BatchJobPeer::STATUS, $this->status);
		if ($this->isColumnModified(BatchJobPeer::EXECUTION_STATUS)) $criteria->add(BatchJobPeer::EXECUTION_STATUS, $this->execution_status);
		if ($this->isColumnModified(BatchJobPeer::MESSAGE)) $criteria->add(BatchJobPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(BatchJobPeer::DESCRIPTION)) $criteria->add(BatchJobPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(BatchJobPeer::CREATED_AT)) $criteria->add(BatchJobPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(BatchJobPeer::UPDATED_AT)) $criteria->add(BatchJobPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(BatchJobPeer::PRIORITY)) $criteria->add(BatchJobPeer::PRIORITY, $this->priority);
		if ($this->isColumnModified(BatchJobPeer::QUEUE_TIME)) $criteria->add(BatchJobPeer::QUEUE_TIME, $this->queue_time);
		if ($this->isColumnModified(BatchJobPeer::FINISH_TIME)) $criteria->add(BatchJobPeer::FINISH_TIME, $this->finish_time);
		if ($this->isColumnModified(BatchJobPeer::ENTRY_ID)) $criteria->add(BatchJobPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(BatchJobPeer::PARTNER_ID)) $criteria->add(BatchJobPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(BatchJobPeer::BULK_JOB_ID)) $criteria->add(BatchJobPeer::BULK_JOB_ID, $this->bulk_job_id);
		if ($this->isColumnModified(BatchJobPeer::ROOT_JOB_ID)) $criteria->add(BatchJobPeer::ROOT_JOB_ID, $this->root_job_id);
		if ($this->isColumnModified(BatchJobPeer::PARENT_JOB_ID)) $criteria->add(BatchJobPeer::PARENT_JOB_ID, $this->parent_job_id);
		if ($this->isColumnModified(BatchJobPeer::LAST_SCHEDULER_ID)) $criteria->add(BatchJobPeer::LAST_SCHEDULER_ID, $this->last_scheduler_id);
		if ($this->isColumnModified(BatchJobPeer::LAST_WORKER_ID)) $criteria->add(BatchJobPeer::LAST_WORKER_ID, $this->last_worker_id);
		if ($this->isColumnModified(BatchJobPeer::DC)) $criteria->add(BatchJobPeer::DC, $this->dc);
		if ($this->isColumnModified(BatchJobPeer::ERR_TYPE)) $criteria->add(BatchJobPeer::ERR_TYPE, $this->err_type);
		if ($this->isColumnModified(BatchJobPeer::ERR_NUMBER)) $criteria->add(BatchJobPeer::ERR_NUMBER, $this->err_number);
		if ($this->isColumnModified(BatchJobPeer::BATCH_JOB_LOCK_ID)) $criteria->add(BatchJobPeer::BATCH_JOB_LOCK_ID, $this->batch_job_lock_id);
		if ($this->isColumnModified(BatchJobPeer::CUSTOM_DATA)) $criteria->add(BatchJobPeer::CUSTOM_DATA, $this->custom_data);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);

		$criteria->add(BatchJobPeer::ID, $this->id);
		
		if($this->alreadyInSave && count($this->modifiedColumns) == 2 && $this->isColumnModified(BatchJobPeer::UPDATED_AT))
		{
			$theModifiedColumn = null;
			foreach($this->modifiedColumns as $modifiedColumn)
				if($modifiedColumn != BatchJobPeer::UPDATED_AT)
					$theModifiedColumn = $modifiedColumn;
					
			$atomicColumns = BatchJobPeer::getAtomicColumns();
			if(in_array($theModifiedColumn, $atomicColumns))
				$criteria->add($theModifiedColumn, $this->getByName($theModifiedColumn, BasePeer::TYPE_COLNAME), Criteria::NOT_EQUAL);
		}

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of BatchJob (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setJobType($this->job_type);

		$copyObj->setJobSubType($this->job_sub_type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setData($this->data);

		$copyObj->setHistory($this->history);

		$copyObj->setLockInfo($this->lock_info);

		$copyObj->setStatus($this->status);

		$copyObj->setExecutionStatus($this->execution_status);

		$copyObj->setMessage($this->message);

		$copyObj->setDescription($this->description);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPriority($this->priority);

		$copyObj->setQueueTime($this->queue_time);

		$copyObj->setFinishTime($this->finish_time);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setBulkJobId($this->bulk_job_id);

		$copyObj->setRootJobId($this->root_job_id);

		$copyObj->setParentJobId($this->parent_job_id);

		$copyObj->setLastSchedulerId($this->last_scheduler_id);

		$copyObj->setLastWorkerId($this->last_worker_id);

		$copyObj->setDc($this->dc);

		$copyObj->setErrType($this->err_type);

		$copyObj->setErrNumber($this->err_number);

		$copyObj->setBatchJobLockId($this->batch_job_lock_id);

		$copyObj->setCustomData($this->custom_data);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getBatchJobLocks() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addBatchJobLock($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getBatchJobLockSuspends() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addBatchJobLockSuspend($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     BatchJob Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		$copyObj->setCopiedFrom($this);
		return $copyObj;
	}
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @var     BatchJob Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      BatchJob $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(BatchJob $copiedFrom)
	{
		$this->copiedFrom = $copiedFrom;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     BatchJobPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new BatchJobPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a BatchJobLock object.
	 *
	 * @param      BatchJobLock $v
	 * @return     BatchJob The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setBatchJobLock(BatchJobLock $v = null)
	{
		if ($v === null) {
			$this->setBatchJobLockId(NULL);
		} else {
			$this->setBatchJobLockId($v->getId());
		}

		$this->aBatchJobLock = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the BatchJobLock object, it will not be re-added.
		if ($v !== null) {
			$v->addBatchJob($this);
		}

		return $this;
	}


	/**
	 * Get the associated BatchJobLock object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     BatchJobLock The associated BatchJobLock object.
	 * @throws     PropelException
	 */
	public function getBatchJobLock(PropelPDO $con = null)
	{
		if ($this->aBatchJobLock === null && ($this->batch_job_lock_id !== null)) {
			$this->aBatchJobLock = BatchJobLockPeer::retrieveByPk($this->batch_job_lock_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aBatchJobLock->addBatchJobs($this);
			 */
		}
		return $this->aBatchJobLock;
	}

	/**
	 * Clears out the collBatchJobLocks collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addBatchJobLocks()
	 */
	public function clearBatchJobLocks()
	{
		$this->collBatchJobLocks = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collBatchJobLocks collection (array).
	 *
	 * By default this just sets the collBatchJobLocks collection to an empty array (like clearcollBatchJobLocks());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initBatchJobLocks()
	{
		$this->collBatchJobLocks = array();
	}

	/**
	 * Gets an array of BatchJobLock objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this BatchJob has previously been saved, it will retrieve
	 * related BatchJobLocks from storage. If this BatchJob is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array BatchJobLock[]
	 * @throws     PropelException
	 */
	public function getBatchJobLocks($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collBatchJobLocks === null) {
			if ($this->isNew()) {
			   $this->collBatchJobLocks = array();
			} else {

				$criteria->add(BatchJobLockPeer::BATCH_JOB_ID, $this->id);

				BatchJobLockPeer::addSelectColumns($criteria);
				$this->collBatchJobLocks = BatchJobLockPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(BatchJobLockPeer::BATCH_JOB_ID, $this->id);

				BatchJobLockPeer::addSelectColumns($criteria);
				if (!isset($this->lastBatchJobLockCriteria) || !$this->lastBatchJobLockCriteria->equals($criteria)) {
					$this->collBatchJobLocks = BatchJobLockPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastBatchJobLockCriteria = $criteria;
		return $this->collBatchJobLocks;
	}

	/**
	 * Returns the number of related BatchJobLock objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related BatchJobLock objects.
	 * @throws     PropelException
	 */
	public function countBatchJobLocks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collBatchJobLocks === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(BatchJobLockPeer::BATCH_JOB_ID, $this->id);

				$count = BatchJobLockPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(BatchJobLockPeer::BATCH_JOB_ID, $this->id);

				if (!isset($this->lastBatchJobLockCriteria) || !$this->lastBatchJobLockCriteria->equals($criteria)) {
					$count = BatchJobLockPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collBatchJobLocks);
				}
			} else {
				$count = count($this->collBatchJobLocks);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a BatchJobLock object to this object
	 * through the BatchJobLock foreign key attribute.
	 *
	 * @param      BatchJobLock $l BatchJobLock
	 * @return     void
	 * @throws     PropelException
	 */
	public function addBatchJobLock(BatchJobLock $l)
	{
		if ($this->collBatchJobLocks === null) {
			$this->initBatchJobLocks();
		}
		if (!in_array($l, $this->collBatchJobLocks, true)) { // only add it if the **same** object is not already associated
			array_push($this->collBatchJobLocks, $l);
			$l->setBatchJob($this);
		}
	}

	/**
	 * Clears out the collBatchJobLockSuspends collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addBatchJobLockSuspends()
	 */
	public function clearBatchJobLockSuspends()
	{
		$this->collBatchJobLockSuspends = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collBatchJobLockSuspends collection (array).
	 *
	 * By default this just sets the collBatchJobLockSuspends collection to an empty array (like clearcollBatchJobLockSuspends());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initBatchJobLockSuspends()
	{
		$this->collBatchJobLockSuspends = array();
	}

	/**
	 * Gets an array of BatchJobLockSuspend objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this BatchJob has previously been saved, it will retrieve
	 * related BatchJobLockSuspends from storage. If this BatchJob is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array BatchJobLockSuspend[]
	 * @throws     PropelException
	 */
	public function getBatchJobLockSuspends($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collBatchJobLockSuspends === null) {
			if ($this->isNew()) {
			   $this->collBatchJobLockSuspends = array();
			} else {

				$criteria->add(BatchJobLockSuspendPeer::BATCH_JOB_ID, $this->id);

				BatchJobLockSuspendPeer::addSelectColumns($criteria);
				$this->collBatchJobLockSuspends = BatchJobLockSuspendPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(BatchJobLockSuspendPeer::BATCH_JOB_ID, $this->id);

				BatchJobLockSuspendPeer::addSelectColumns($criteria);
				if (!isset($this->lastBatchJobLockSuspendCriteria) || !$this->lastBatchJobLockSuspendCriteria->equals($criteria)) {
					$this->collBatchJobLockSuspends = BatchJobLockSuspendPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastBatchJobLockSuspendCriteria = $criteria;
		return $this->collBatchJobLockSuspends;
	}

	/**
	 * Returns the number of related BatchJobLockSuspend objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related BatchJobLockSuspend objects.
	 * @throws     PropelException
	 */
	public function countBatchJobLockSuspends(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collBatchJobLockSuspends === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(BatchJobLockSuspendPeer::BATCH_JOB_ID, $this->id);

				$count = BatchJobLockSuspendPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(BatchJobLockSuspendPeer::BATCH_JOB_ID, $this->id);

				if (!isset($this->lastBatchJobLockSuspendCriteria) || !$this->lastBatchJobLockSuspendCriteria->equals($criteria)) {
					$count = BatchJobLockSuspendPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collBatchJobLockSuspends);
				}
			} else {
				$count = count($this->collBatchJobLockSuspends);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a BatchJobLockSuspend object to this object
	 * through the BatchJobLockSuspend foreign key attribute.
	 *
	 * @param      BatchJobLockSuspend $l BatchJobLockSuspend
	 * @return     void
	 * @throws     PropelException
	 */
	public function addBatchJobLockSuspend(BatchJobLockSuspend $l)
	{
		if ($this->collBatchJobLockSuspends === null) {
			$this->initBatchJobLockSuspends();
		}
		if (!in_array($l, $this->collBatchJobLockSuspends, true)) { // only add it if the **same** object is not already associated
			array_push($this->collBatchJobLockSuspends, $l);
			$l->setBatchJob($this);
		}
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
			if ($this->collBatchJobLocks) {
				foreach ((array) $this->collBatchJobLocks as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collBatchJobLockSuspends) {
				foreach ((array) $this->collBatchJobLockSuspends as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collBatchJobLocks = null;
		$this->collBatchJobLockSuspends = null;
			$this->aBatchJobLock = null;
	}

	/* ---------------------- CustomData functions ------------------------- */

	/**
	 * @var myCustomData
	 */
	protected $m_custom_data = null;

	/**
	 * Store custom data old values before the changes
	 * @var        array
	 */
	protected $oldCustomDataValues = array();
	
	/**
	 * @return array
	 */
	public function getCustomDataOldValues()
	{
		return $this->oldCustomDataValues;
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param string $namespace
	 * @return string
	 */
	public function putInCustomData ( $name , $value , $namespace = null )
	{
		$customData = $this->getCustomDataObj( );
		
		$currentNamespace = '';
		if($namespace)
			$currentNamespace = $namespace;
			
		if(!isset($this->oldCustomDataValues[$currentNamespace]))
			$this->oldCustomDataValues[$currentNamespace] = array();
		if(!isset($this->oldCustomDataValues[$currentNamespace][$name]))
			$this->oldCustomDataValues[$currentNamespace][$name] = $customData->get($name, $namespace);
		
		$customData->put ( $name , $value , $namespace );
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 * @param string $defaultValue
	 * @return string
	 */
	public function getFromCustomData ( $name , $namespace = null , $defaultValue = null )
	{
		$customData = $this->getCustomDataObj( );
		$res = $customData->get ( $name , $namespace );
		if ( $res === null ) return $defaultValue;
		return $res;
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 */
	public function removeFromCustomData ( $name , $namespace = null)
	{

		$customData = $this->getCustomDataObj( );
		return $customData->remove ( $name , $namespace );
	}

	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function incInCustomData ( $name , $delta = 1, $namespace = null)
	{
		$customData = $this->getCustomDataObj( );
		return $customData->inc ( $name , $delta , $namespace  );
	}

	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function decInCustomData ( $name , $delta = 1, $namespace = null)
	{
		$customData = $this->getCustomDataObj(  );
		return $customData->dec ( $name , $delta , $namespace );
	}

	/**
	 * @return myCustomData
	 */
	public function getCustomDataObj( )
	{
		if ( ! $this->m_custom_data )
		{
			$this->m_custom_data = myCustomData::fromString ( $this->getCustomData() );
		}
		return $this->m_custom_data;
	}
	
	/**
	 * Must be called before saving the object
	 */
	public function setCustomDataObj()
	{
		if ( $this->m_custom_data != null )
		{
			$this->setCustomData( $this->m_custom_data->toString() );
		}
	}
	
	/* ---------------------- CustomData functions ------------------------- */
	
} // BaseBatchJob
