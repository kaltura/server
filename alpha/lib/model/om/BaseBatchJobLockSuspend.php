<?php

/**
 * Base class that represents a row from the 'batch_job_lock_suspend' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBatchJobLockSuspend extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        BatchJobLockSuspendPeer
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
	 * The value for the estimated_effort field.
	 * @var        string
	 */
	protected $estimated_effort;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the start_at field.
	 * @var        string
	 */
	protected $start_at;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

	/**
	 * The value for the urgency field.
	 * @var        int
	 */
	protected $urgency;

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
	 * The value for the scheduler_id field.
	 * @var        int
	 */
	protected $scheduler_id;

	/**
	 * The value for the worker_id field.
	 * @var        int
	 */
	protected $worker_id;

	/**
	 * The value for the batch_index field.
	 * @var        int
	 */
	protected $batch_index;

	/**
	 * The value for the expiration field.
	 * @var        string
	 */
	protected $expiration;

	/**
	 * The value for the execution_attempts field.
	 * @var        int
	 */
	protected $execution_attempts;

	/**
	 * The value for the version field.
	 * @var        int
	 */
	protected $version;

	/**
	 * The value for the dc field.
	 * @var        int
	 */
	protected $dc;

	/**
	 * The value for the batch_job_id field.
	 * @var        int
	 */
	protected $batch_job_id;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the batch_version field.
	 * @var        int
	 */
	protected $batch_version;

	/**
	 * @var        BatchJob
	 */
	protected $aBatchJob;

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
	 * Initializes internal state of BaseBatchJobLockSuspend object.
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
	 * Get the [estimated_effort] column value.
	 * 
	 * @return     string
	 */
	public function getEstimatedEffort()
	{
		return $this->estimated_effort;
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
	 * Get the [optionally formatted] temporal [start_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getStartAt($format = 'Y-m-d H:i:s')
	{
		if ($this->start_at === null) {
			return null;
		}


		if ($this->start_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->start_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_at, true), $x);
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
	 * Get the [priority] column value.
	 * 
	 * @return     int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Get the [urgency] column value.
	 * 
	 * @return     int
	 */
	public function getUrgency()
	{
		return $this->urgency;
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
	 * Get the [scheduler_id] column value.
	 * 
	 * @return     int
	 */
	public function getSchedulerId()
	{
		return $this->scheduler_id;
	}

	/**
	 * Get the [worker_id] column value.
	 * 
	 * @return     int
	 */
	public function getWorkerId()
	{
		return $this->worker_id;
	}

	/**
	 * Get the [batch_index] column value.
	 * 
	 * @return     int
	 */
	public function getBatchIndex()
	{
		return $this->batch_index;
	}

	/**
	 * Get the [optionally formatted] temporal [expiration] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getExpiration($format = 'Y-m-d H:i:s')
	{
		if ($this->expiration === null) {
			return null;
		}


		if ($this->expiration === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->expiration);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->expiration, true), $x);
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
	 * Get the [execution_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getExecutionAttempts()
	{
		return $this->execution_attempts;
	}

	/**
	 * Get the [version] column value.
	 * 
	 * @return     int
	 */
	public function getVersion()
	{
		return $this->version;
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
	 * Get the [batch_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getBatchJobId()
	{
		return $this->batch_job_id;
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
	 * Get the [batch_version] column value.
	 * 
	 * @return     int
	 */
	public function getBatchVersion()
	{
		return $this->batch_version;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [job_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setJobType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::JOB_TYPE]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::JOB_TYPE] = $this->job_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_type !== $v) {
			$this->job_type = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::JOB_TYPE;
		}

		return $this;
	} // setJobType()

	/**
	 * Set the value of [job_sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setJobSubType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::JOB_SUB_TYPE]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::JOB_SUB_TYPE] = $this->job_sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_sub_type !== $v) {
			$this->job_sub_type = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::JOB_SUB_TYPE;
		}

		return $this;
	} // setJobSubType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::OBJECT_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v || $this->isNew()) {
			$this->object_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [estimated_effort] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setEstimatedEffort($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::ESTIMATED_EFFORT]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::ESTIMATED_EFFORT] = $this->estimated_effort;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->estimated_effort !== $v) {
			$this->estimated_effort = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::ESTIMATED_EFFORT;
		}

		return $this;
	} // setEstimatedEffort()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::STATUS]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Sets the value of [start_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setStartAt($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::START_AT]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::START_AT] = $this->start_at;

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

		if ( $this->start_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->start_at !== null && $tmpDt = new DateTime($this->start_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->start_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobLockSuspendPeer::START_AT;
			}
		} // if either are not null

		return $this;
	} // setStartAt()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
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
				$this->modifiedColumns[] = BatchJobLockSuspendPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [priority] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::PRIORITY]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::PRIORITY] = $this->priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::PRIORITY;
		}

		return $this;
	} // setPriority()

	/**
	 * Set the value of [urgency] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setUrgency($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::URGENCY]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::URGENCY] = $this->urgency;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->urgency !== $v) {
			$this->urgency = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::URGENCY;
		}

		return $this;
	} // setUrgency()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::ENTRY_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v || $this->isNew()) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::PARTNER_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::WORKER_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_INDEX]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_INDEX] = $this->batch_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Sets the value of [expiration] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setExpiration($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::EXPIRATION]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::EXPIRATION] = $this->expiration;

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

		if ( $this->expiration !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->expiration !== null && $tmpDt = new DateTime($this->expiration)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->expiration = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobLockSuspendPeer::EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setExpiration()

	/**
	 * Set the value of [execution_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setExecutionAttempts($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::EXECUTION_ATTEMPTS]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::EXECUTION_ATTEMPTS] = $this->execution_attempts;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_attempts !== $v) {
			$this->execution_attempts = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::EXECUTION_ATTEMPTS;
		}

		return $this;
	} // setExecutionAttempts()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::VERSION]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::VERSION] = $this->version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::DC]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [batch_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setBatchJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_JOB_ID]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_JOB_ID] = $this->batch_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_job_id !== $v) {
			$this->batch_job_id = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::BATCH_JOB_ID;
		}

		if ($this->aBatchJob !== null && $this->aBatchJob->getId() !== $v) {
			$this->aBatchJob = null;
		}

		return $this;
	} // setBatchJobId()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [batch_version] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 */
	public function setBatchVersion($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_VERSION]))
			$this->oldColumnsValues[BatchJobLockSuspendPeer::BATCH_VERSION] = $this->batch_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_version !== $v) {
			$this->batch_version = $v;
			$this->modifiedColumns[] = BatchJobLockSuspendPeer::BATCH_VERSION;
		}

		return $this;
	} // setBatchVersion()

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
			$this->estimated_effort = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->status = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->start_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->priority = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->urgency = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->entry_id = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->partner_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->scheduler_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->worker_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->batch_index = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->expiration = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->execution_attempts = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->version = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->dc = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->batch_job_id = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->custom_data = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->batch_version = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = BatchJobLockSuspendPeer::NUM_COLUMNS - BatchJobLockSuspendPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating BatchJobLockSuspend object", $e);
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

		if ($this->aBatchJob !== null && $this->batch_job_id !== $this->aBatchJob->getId()) {
			$this->aBatchJob = null;
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
			$con = Propel::getConnection(BatchJobLockSuspendPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		BatchJobLockSuspendPeer::setUseCriteriaFilter(false);
		$stmt = BatchJobLockSuspendPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		BatchJobLockSuspendPeer::setUseCriteriaFilter(true);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aBatchJob = null;
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
			$con = Propel::getConnection(BatchJobLockSuspendPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				BatchJobLockSuspendPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(BatchJobLockSuspendPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				BatchJobLockSuspendPeer::addInstanceToPool($this);
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

			if ($this->aBatchJob !== null) {
				if ($this->aBatchJob->isModified() || $this->aBatchJob->isNew()) {
					$affectedRows += $this->aBatchJob->save($con);
				}
				$this->setBatchJob($this->aBatchJob);
			}


			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = BatchJobLockSuspendPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = BatchJobLockSuspendPeer::doUpdate($this, $con);
					if($affectedObjects)
						$this->objectSaved = true;
						
					$affectedRows += $affectedObjects;
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
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
    	
		return parent::preInsert($con);
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		kQueryCache::invalidateQueryCache($this);
		
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
	
		kQueryCache::invalidateQueryCache($this);
		
		parent::postUpdate($con);
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

			if ($this->aBatchJob !== null) {
				if (!$this->aBatchJob->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aBatchJob->getValidationFailures());
				}
			}


			if (($retval = BatchJobLockSuspendPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = BatchJobLockSuspendPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEstimatedEffort();
				break;
			case 6:
				return $this->getStatus();
				break;
			case 7:
				return $this->getStartAt();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			case 9:
				return $this->getPriority();
				break;
			case 10:
				return $this->getUrgency();
				break;
			case 11:
				return $this->getEntryId();
				break;
			case 12:
				return $this->getPartnerId();
				break;
			case 13:
				return $this->getSchedulerId();
				break;
			case 14:
				return $this->getWorkerId();
				break;
			case 15:
				return $this->getBatchIndex();
				break;
			case 16:
				return $this->getExpiration();
				break;
			case 17:
				return $this->getExecutionAttempts();
				break;
			case 18:
				return $this->getVersion();
				break;
			case 19:
				return $this->getDc();
				break;
			case 20:
				return $this->getBatchJobId();
				break;
			case 21:
				return $this->getCustomData();
				break;
			case 22:
				return $this->getBatchVersion();
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
		$keys = BatchJobLockSuspendPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getJobType(),
			$keys[2] => $this->getJobSubType(),
			$keys[3] => $this->getObjectId(),
			$keys[4] => $this->getObjectType(),
			$keys[5] => $this->getEstimatedEffort(),
			$keys[6] => $this->getStatus(),
			$keys[7] => $this->getStartAt(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getPriority(),
			$keys[10] => $this->getUrgency(),
			$keys[11] => $this->getEntryId(),
			$keys[12] => $this->getPartnerId(),
			$keys[13] => $this->getSchedulerId(),
			$keys[14] => $this->getWorkerId(),
			$keys[15] => $this->getBatchIndex(),
			$keys[16] => $this->getExpiration(),
			$keys[17] => $this->getExecutionAttempts(),
			$keys[18] => $this->getVersion(),
			$keys[19] => $this->getDc(),
			$keys[20] => $this->getBatchJobId(),
			$keys[21] => $this->getCustomData(),
			$keys[22] => $this->getBatchVersion(),
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
		$pos = BatchJobLockSuspendPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEstimatedEffort($value);
				break;
			case 6:
				$this->setStatus($value);
				break;
			case 7:
				$this->setStartAt($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
			case 9:
				$this->setPriority($value);
				break;
			case 10:
				$this->setUrgency($value);
				break;
			case 11:
				$this->setEntryId($value);
				break;
			case 12:
				$this->setPartnerId($value);
				break;
			case 13:
				$this->setSchedulerId($value);
				break;
			case 14:
				$this->setWorkerId($value);
				break;
			case 15:
				$this->setBatchIndex($value);
				break;
			case 16:
				$this->setExpiration($value);
				break;
			case 17:
				$this->setExecutionAttempts($value);
				break;
			case 18:
				$this->setVersion($value);
				break;
			case 19:
				$this->setDc($value);
				break;
			case 20:
				$this->setBatchJobId($value);
				break;
			case 21:
				$this->setCustomData($value);
				break;
			case 22:
				$this->setBatchVersion($value);
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
		$keys = BatchJobLockSuspendPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setJobType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJobSubType($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjectId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setObjectType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEstimatedEffort($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStartAt($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setPriority($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setUrgency($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setEntryId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPartnerId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSchedulerId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setWorkerId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setBatchIndex($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setExpiration($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setExecutionAttempts($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setVersion($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setDc($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setBatchJobId($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setCustomData($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setBatchVersion($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(BatchJobLockSuspendPeer::DATABASE_NAME);

		if ($this->isColumnModified(BatchJobLockSuspendPeer::ID)) $criteria->add(BatchJobLockSuspendPeer::ID, $this->id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::JOB_TYPE)) $criteria->add(BatchJobLockSuspendPeer::JOB_TYPE, $this->job_type);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::JOB_SUB_TYPE)) $criteria->add(BatchJobLockSuspendPeer::JOB_SUB_TYPE, $this->job_sub_type);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::OBJECT_ID)) $criteria->add(BatchJobLockSuspendPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::OBJECT_TYPE)) $criteria->add(BatchJobLockSuspendPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::ESTIMATED_EFFORT)) $criteria->add(BatchJobLockSuspendPeer::ESTIMATED_EFFORT, $this->estimated_effort);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::STATUS)) $criteria->add(BatchJobLockSuspendPeer::STATUS, $this->status);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::START_AT)) $criteria->add(BatchJobLockSuspendPeer::START_AT, $this->start_at);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::CREATED_AT)) $criteria->add(BatchJobLockSuspendPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::PRIORITY)) $criteria->add(BatchJobLockSuspendPeer::PRIORITY, $this->priority);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::URGENCY)) $criteria->add(BatchJobLockSuspendPeer::URGENCY, $this->urgency);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::ENTRY_ID)) $criteria->add(BatchJobLockSuspendPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::PARTNER_ID)) $criteria->add(BatchJobLockSuspendPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::SCHEDULER_ID)) $criteria->add(BatchJobLockSuspendPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::WORKER_ID)) $criteria->add(BatchJobLockSuspendPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::BATCH_INDEX)) $criteria->add(BatchJobLockSuspendPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::EXPIRATION)) $criteria->add(BatchJobLockSuspendPeer::EXPIRATION, $this->expiration);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::EXECUTION_ATTEMPTS)) $criteria->add(BatchJobLockSuspendPeer::EXECUTION_ATTEMPTS, $this->execution_attempts);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::VERSION)) $criteria->add(BatchJobLockSuspendPeer::VERSION, $this->version);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::DC)) $criteria->add(BatchJobLockSuspendPeer::DC, $this->dc);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::BATCH_JOB_ID)) $criteria->add(BatchJobLockSuspendPeer::BATCH_JOB_ID, $this->batch_job_id);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::CUSTOM_DATA)) $criteria->add(BatchJobLockSuspendPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(BatchJobLockSuspendPeer::BATCH_VERSION)) $criteria->add(BatchJobLockSuspendPeer::BATCH_VERSION, $this->batch_version);

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
		$criteria = new Criteria(BatchJobLockSuspendPeer::DATABASE_NAME);

		$criteria->add(BatchJobLockSuspendPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of BatchJobLockSuspend (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setId($this->id);

		$copyObj->setJobType($this->job_type);

		$copyObj->setJobSubType($this->job_sub_type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setEstimatedEffort($this->estimated_effort);

		$copyObj->setStatus($this->status);

		$copyObj->setStartAt($this->start_at);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setPriority($this->priority);

		$copyObj->setUrgency($this->urgency);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setBatchIndex($this->batch_index);

		$copyObj->setExpiration($this->expiration);

		$copyObj->setExecutionAttempts($this->execution_attempts);

		$copyObj->setVersion($this->version);

		$copyObj->setDc($this->dc);

		$copyObj->setBatchJobId($this->batch_job_id);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setBatchVersion($this->batch_version);


		$copyObj->setNew(true);

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
	 * @return     BatchJobLockSuspend Clone of current object.
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
	 * @var     BatchJobLockSuspend Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      BatchJobLockSuspend $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(BatchJobLockSuspend $copiedFrom)
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
	 * @return     BatchJobLockSuspendPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new BatchJobLockSuspendPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a BatchJob object.
	 *
	 * @param      BatchJob $v
	 * @return     BatchJobLockSuspend The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setBatchJob(BatchJob $v = null)
	{
		if ($v === null) {
			$this->setBatchJobId(NULL);
		} else {
			$this->setBatchJobId($v->getId());
		}

		$this->aBatchJob = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the BatchJob object, it will not be re-added.
		if ($v !== null) {
			$v->addBatchJobLockSuspend($this);
		}

		return $this;
	}


	/**
	 * Get the associated BatchJob object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     BatchJob The associated BatchJob object.
	 * @throws     PropelException
	 */
	public function getBatchJob(PropelPDO $con = null)
	{
		if ($this->aBatchJob === null && ($this->batch_job_id !== null)) {
			$this->aBatchJob = BatchJobPeer::retrieveByPk($this->batch_job_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aBatchJob->addBatchJobLockSuspends($this);
			 */
		}
		return $this->aBatchJob;
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
		} // if ($deep)

			$this->aBatchJob = null;
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
	
} // BaseBatchJobLockSuspend
