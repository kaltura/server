<?php

/**
 * Base class that represents a row from the 'batch_job' table.
 *
 * 
 *
 * @package    lib.model.om
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
	 * The value for the data field.
	 * @var        string
	 */
	protected $data;

	/**
	 * The value for the file_size field.
	 * @var        int
	 */
	protected $file_size;

	/**
	 * The value for the duplication_key field.
	 * @var        string
	 */
	protected $duplication_key;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the abort field.
	 * @var        int
	 */
	protected $abort;

	/**
	 * The value for the check_again_timeout field.
	 * @var        int
	 */
	protected $check_again_timeout;

	/**
	 * The value for the progress field.
	 * @var        int
	 */
	protected $progress;

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
	 * The value for the updates_count field.
	 * @var        int
	 */
	protected $updates_count;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the created_by field.
	 * @var        string
	 */
	protected $created_by;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the updated_by field.
	 * @var        string
	 */
	protected $updated_by;

	/**
	 * The value for the deleted_at field.
	 * @var        string
	 */
	protected $deleted_at;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

	/**
	 * The value for the work_group_id field.
	 * @var        int
	 */
	protected $work_group_id;

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
	 * The value for the subp_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $subp_id;

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
	 * The value for the last_worker_remote field.
	 * @var        boolean
	 */
	protected $last_worker_remote;

	/**
	 * The value for the processor_expiration field.
	 * @var        string
	 */
	protected $processor_expiration;

	/**
	 * The value for the execution_attempts field.
	 * @var        int
	 */
	protected $execution_attempts;

	/**
	 * The value for the lock_version field.
	 * @var        int
	 */
	protected $lock_version;

	/**
	 * The value for the twin_job_id field.
	 * @var        int
	 */
	protected $twin_job_id;

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
	 * The value for the on_stress_divert_to field.
	 * @var        int
	 */
	protected $on_stress_divert_to;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->entry_id = '';
		$this->partner_id = 0;
		$this->subp_id = 0;
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
	 * Get the [data] column value.
	 * 
	 * @return     string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the [file_size] column value.
	 * 
	 * @return     int
	 */
	public function getFileSize()
	{
		return $this->file_size;
	}

	/**
	 * Get the [duplication_key] column value.
	 * 
	 * @return     string
	 */
	public function getDuplicationKey()
	{
		return $this->duplication_key;
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
	 * Get the [abort] column value.
	 * 
	 * @return     int
	 */
	public function getAbort()
	{
		return $this->abort;
	}

	/**
	 * Get the [check_again_timeout] column value.
	 * 
	 * @return     int
	 */
	public function getCheckAgainTimeout()
	{
		return $this->check_again_timeout;
	}

	/**
	 * Get the [progress] column value.
	 * 
	 * @return     int
	 */
	public function getProgress()
	{
		return $this->progress;
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
	 * Get the [updates_count] column value.
	 * 
	 * @return     int
	 */
	public function getUpdatesCount()
	{
		return $this->updates_count;
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
	 * Get the [created_by] column value.
	 * 
	 * @return     string
	 */
	public function getCreatedBy()
	{
		return $this->created_by;
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
	 * Get the [updated_by] column value.
	 * 
	 * @return     string
	 */
	public function getUpdatedBy()
	{
		return $this->updated_by;
	}

	/**
	 * Get the [optionally formatted] temporal [deleted_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->deleted_at === null) {
			return null;
		}


		if ($this->deleted_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->deleted_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->deleted_at, true), $x);
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
	 * Get the [work_group_id] column value.
	 * 
	 * @return     int
	 */
	public function getWorkGroupId()
	{
		return $this->work_group_id;
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
	 * Get the [subp_id] column value.
	 * 
	 * @return     int
	 */
	public function getSubpId()
	{
		return $this->subp_id;
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
	 * Get the [last_worker_remote] column value.
	 * 
	 * @return     boolean
	 */
	public function getLastWorkerRemote()
	{
		return $this->last_worker_remote;
	}

	/**
	 * Get the [optionally formatted] temporal [processor_expiration] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getProcessorExpiration($format = 'Y-m-d H:i:s')
	{
		if ($this->processor_expiration === null) {
			return null;
		}


		if ($this->processor_expiration === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->processor_expiration);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->processor_expiration, true), $x);
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
	 * Get the [lock_version] column value.
	 * 
	 * @return     int
	 */
	public function getLockVersion()
	{
		return $this->lock_version;
	}

	/**
	 * Get the [twin_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getTwinJobId()
	{
		return $this->twin_job_id;
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
	 * Get the [on_stress_divert_to] column value.
	 * 
	 * @return     int
	 */
	public function getOnStressDivertTo()
	{
		return $this->on_stress_divert_to;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setId($v)
	{
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
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setData($v)
	{
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
	 * Set the value of [file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setFileSize($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = BatchJobPeer::FILE_SIZE;
		}

		return $this;
	} // setFileSize()

	/**
	 * Set the value of [duplication_key] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setDuplicationKey($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->duplication_key !== $v) {
			$this->duplication_key = $v;
			$this->modifiedColumns[] = BatchJobPeer::DUPLICATION_KEY;
		}

		return $this;
	} // setDuplicationKey()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
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
	 * Set the value of [abort] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setAbort($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->abort !== $v) {
			$this->abort = $v;
			$this->modifiedColumns[] = BatchJobPeer::ABORT;
		}

		return $this;
	} // setAbort()

	/**
	 * Set the value of [check_again_timeout] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setCheckAgainTimeout($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->check_again_timeout !== $v) {
			$this->check_again_timeout = $v;
			$this->modifiedColumns[] = BatchJobPeer::CHECK_AGAIN_TIMEOUT;
		}

		return $this;
	} // setCheckAgainTimeout()

	/**
	 * Set the value of [progress] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setProgress($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->progress !== $v) {
			$this->progress = $v;
			$this->modifiedColumns[] = BatchJobPeer::PROGRESS;
		}

		return $this;
	} // setProgress()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setMessage($v)
	{
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
	 * Set the value of [updates_count] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setUpdatesCount($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->updates_count !== $v) {
			$this->updates_count = $v;
			$this->modifiedColumns[] = BatchJobPeer::UPDATES_COUNT;
		}

		return $this;
	} // setUpdatesCount()

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
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setCreatedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = BatchJobPeer::CREATED_BY;
		}

		return $this;
	} // setCreatedBy()

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
	 * Set the value of [updated_by] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setUpdatedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->updated_by !== $v) {
			$this->updated_by = $v;
			$this->modifiedColumns[] = BatchJobPeer::UPDATED_BY;
		}

		return $this;
	} // setUpdatedBy()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
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

		if ( $this->deleted_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->deleted_at !== null && $tmpDt = new DateTime($this->deleted_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->deleted_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [priority] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
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
	 * Set the value of [work_group_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setWorkGroupId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->work_group_id !== $v) {
			$this->work_group_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::WORK_GROUP_ID;
		}

		return $this;
	} // setWorkGroupId()

	/**
	 * Sets the value of [queue_time] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setQueueTime($v)
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
	 * Set the value of [subp_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setSubpId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subp_id !== $v || $this->isNew()) {
			$this->subp_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::SUBP_ID;
		}

		return $this;
	} // setSubpId()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = BatchJobPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Set the value of [last_scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLastSchedulerId($v)
	{
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
	 * Set the value of [last_worker_remote] column.
	 * 
	 * @param      boolean $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLastWorkerRemote($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->last_worker_remote !== $v) {
			$this->last_worker_remote = $v;
			$this->modifiedColumns[] = BatchJobPeer::LAST_WORKER_REMOTE;
		}

		return $this;
	} // setLastWorkerRemote()

	/**
	 * Sets the value of [processor_expiration] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setProcessorExpiration($v)
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

		if ( $this->processor_expiration !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->processor_expiration !== null && $tmpDt = new DateTime($this->processor_expiration)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->processor_expiration = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BatchJobPeer::PROCESSOR_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setProcessorExpiration()

	/**
	 * Set the value of [execution_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setExecutionAttempts($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_attempts !== $v) {
			$this->execution_attempts = $v;
			$this->modifiedColumns[] = BatchJobPeer::EXECUTION_ATTEMPTS;
		}

		return $this;
	} // setExecutionAttempts()

	/**
	 * Set the value of [lock_version] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setLockVersion($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lock_version !== $v) {
			$this->lock_version = $v;
			$this->modifiedColumns[] = BatchJobPeer::LOCK_VERSION;
		}

		return $this;
	} // setLockVersion()

	/**
	 * Set the value of [twin_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setTwinJobId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->twin_job_id !== $v) {
			$this->twin_job_id = $v;
			$this->modifiedColumns[] = BatchJobPeer::TWIN_JOB_ID;
		}

		return $this;
	} // setTwinJobId()

	/**
	 * Set the value of [bulk_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setBulkJobId($v)
	{
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
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setDc($v)
	{
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
	 * Set the value of [on_stress_divert_to] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJob The current object (for fluent API support)
	 */
	public function setOnStressDivertTo($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->on_stress_divert_to !== $v) {
			$this->on_stress_divert_to = $v;
			$this->modifiedColumns[] = BatchJobPeer::ON_STRESS_DIVERT_TO;
		}

		return $this;
	} // setOnStressDivertTo()

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
			if ($this->entry_id !== '') {
				return false;
			}

			if ($this->partner_id !== 0) {
				return false;
			}

			if ($this->subp_id !== 0) {
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
			$this->data = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->file_size = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->duplication_key = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->status = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->abort = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->check_again_timeout = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->progress = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->message = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->description = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->updates_count = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->created_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->created_by = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->updated_at = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->updated_by = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->deleted_at = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->priority = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->work_group_id = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->queue_time = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->finish_time = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->entry_id = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->partner_id = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->subp_id = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->scheduler_id = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->worker_id = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->batch_index = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->last_scheduler_id = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
			$this->last_worker_id = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
			$this->last_worker_remote = ($row[$startcol + 30] !== null) ? (boolean) $row[$startcol + 30] : null;
			$this->processor_expiration = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
			$this->execution_attempts = ($row[$startcol + 32] !== null) ? (int) $row[$startcol + 32] : null;
			$this->lock_version = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->twin_job_id = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->bulk_job_id = ($row[$startcol + 35] !== null) ? (int) $row[$startcol + 35] : null;
			$this->root_job_id = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->parent_job_id = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->dc = ($row[$startcol + 38] !== null) ? (int) $row[$startcol + 38] : null;
			$this->err_type = ($row[$startcol + 39] !== null) ? (int) $row[$startcol + 39] : null;
			$this->err_number = ($row[$startcol + 40] !== null) ? (int) $row[$startcol + 40] : null;
			$this->on_stress_divert_to = ($row[$startcol + 41] !== null) ? (int) $row[$startcol + 41] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 42; // 42 = BatchJobPeer::NUM_COLUMNS - BatchJobPeer::NUM_LAZY_LOAD_COLUMNS).

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

		$stmt = BatchJobPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = BatchJobPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = BatchJobPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += BatchJobPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
    	$this->setCreatedAt(time());
    	
		$this->setUpdatedAt(time());
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		BatchJobPeer::setUseCriteriaFilter(false);
		$this->reload();
		BatchJobPeer::setUseCriteriaFilter(true);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
	}

	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isModified())
		{
			$this->setUpdatedAt(time());
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->modifiedColumns));
		}
		return true;
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


			if (($retval = BatchJobPeer::doValidate($this, $columns)) !== true) {
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
				return $this->getData();
				break;
			case 4:
				return $this->getFileSize();
				break;
			case 5:
				return $this->getDuplicationKey();
				break;
			case 6:
				return $this->getStatus();
				break;
			case 7:
				return $this->getAbort();
				break;
			case 8:
				return $this->getCheckAgainTimeout();
				break;
			case 9:
				return $this->getProgress();
				break;
			case 10:
				return $this->getMessage();
				break;
			case 11:
				return $this->getDescription();
				break;
			case 12:
				return $this->getUpdatesCount();
				break;
			case 13:
				return $this->getCreatedAt();
				break;
			case 14:
				return $this->getCreatedBy();
				break;
			case 15:
				return $this->getUpdatedAt();
				break;
			case 16:
				return $this->getUpdatedBy();
				break;
			case 17:
				return $this->getDeletedAt();
				break;
			case 18:
				return $this->getPriority();
				break;
			case 19:
				return $this->getWorkGroupId();
				break;
			case 20:
				return $this->getQueueTime();
				break;
			case 21:
				return $this->getFinishTime();
				break;
			case 22:
				return $this->getEntryId();
				break;
			case 23:
				return $this->getPartnerId();
				break;
			case 24:
				return $this->getSubpId();
				break;
			case 25:
				return $this->getSchedulerId();
				break;
			case 26:
				return $this->getWorkerId();
				break;
			case 27:
				return $this->getBatchIndex();
				break;
			case 28:
				return $this->getLastSchedulerId();
				break;
			case 29:
				return $this->getLastWorkerId();
				break;
			case 30:
				return $this->getLastWorkerRemote();
				break;
			case 31:
				return $this->getProcessorExpiration();
				break;
			case 32:
				return $this->getExecutionAttempts();
				break;
			case 33:
				return $this->getLockVersion();
				break;
			case 34:
				return $this->getTwinJobId();
				break;
			case 35:
				return $this->getBulkJobId();
				break;
			case 36:
				return $this->getRootJobId();
				break;
			case 37:
				return $this->getParentJobId();
				break;
			case 38:
				return $this->getDc();
				break;
			case 39:
				return $this->getErrType();
				break;
			case 40:
				return $this->getErrNumber();
				break;
			case 41:
				return $this->getOnStressDivertTo();
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
			$keys[3] => $this->getData(),
			$keys[4] => $this->getFileSize(),
			$keys[5] => $this->getDuplicationKey(),
			$keys[6] => $this->getStatus(),
			$keys[7] => $this->getAbort(),
			$keys[8] => $this->getCheckAgainTimeout(),
			$keys[9] => $this->getProgress(),
			$keys[10] => $this->getMessage(),
			$keys[11] => $this->getDescription(),
			$keys[12] => $this->getUpdatesCount(),
			$keys[13] => $this->getCreatedAt(),
			$keys[14] => $this->getCreatedBy(),
			$keys[15] => $this->getUpdatedAt(),
			$keys[16] => $this->getUpdatedBy(),
			$keys[17] => $this->getDeletedAt(),
			$keys[18] => $this->getPriority(),
			$keys[19] => $this->getWorkGroupId(),
			$keys[20] => $this->getQueueTime(),
			$keys[21] => $this->getFinishTime(),
			$keys[22] => $this->getEntryId(),
			$keys[23] => $this->getPartnerId(),
			$keys[24] => $this->getSubpId(),
			$keys[25] => $this->getSchedulerId(),
			$keys[26] => $this->getWorkerId(),
			$keys[27] => $this->getBatchIndex(),
			$keys[28] => $this->getLastSchedulerId(),
			$keys[29] => $this->getLastWorkerId(),
			$keys[30] => $this->getLastWorkerRemote(),
			$keys[31] => $this->getProcessorExpiration(),
			$keys[32] => $this->getExecutionAttempts(),
			$keys[33] => $this->getLockVersion(),
			$keys[34] => $this->getTwinJobId(),
			$keys[35] => $this->getBulkJobId(),
			$keys[36] => $this->getRootJobId(),
			$keys[37] => $this->getParentJobId(),
			$keys[38] => $this->getDc(),
			$keys[39] => $this->getErrType(),
			$keys[40] => $this->getErrNumber(),
			$keys[41] => $this->getOnStressDivertTo(),
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
				$this->setData($value);
				break;
			case 4:
				$this->setFileSize($value);
				break;
			case 5:
				$this->setDuplicationKey($value);
				break;
			case 6:
				$this->setStatus($value);
				break;
			case 7:
				$this->setAbort($value);
				break;
			case 8:
				$this->setCheckAgainTimeout($value);
				break;
			case 9:
				$this->setProgress($value);
				break;
			case 10:
				$this->setMessage($value);
				break;
			case 11:
				$this->setDescription($value);
				break;
			case 12:
				$this->setUpdatesCount($value);
				break;
			case 13:
				$this->setCreatedAt($value);
				break;
			case 14:
				$this->setCreatedBy($value);
				break;
			case 15:
				$this->setUpdatedAt($value);
				break;
			case 16:
				$this->setUpdatedBy($value);
				break;
			case 17:
				$this->setDeletedAt($value);
				break;
			case 18:
				$this->setPriority($value);
				break;
			case 19:
				$this->setWorkGroupId($value);
				break;
			case 20:
				$this->setQueueTime($value);
				break;
			case 21:
				$this->setFinishTime($value);
				break;
			case 22:
				$this->setEntryId($value);
				break;
			case 23:
				$this->setPartnerId($value);
				break;
			case 24:
				$this->setSubpId($value);
				break;
			case 25:
				$this->setSchedulerId($value);
				break;
			case 26:
				$this->setWorkerId($value);
				break;
			case 27:
				$this->setBatchIndex($value);
				break;
			case 28:
				$this->setLastSchedulerId($value);
				break;
			case 29:
				$this->setLastWorkerId($value);
				break;
			case 30:
				$this->setLastWorkerRemote($value);
				break;
			case 31:
				$this->setProcessorExpiration($value);
				break;
			case 32:
				$this->setExecutionAttempts($value);
				break;
			case 33:
				$this->setLockVersion($value);
				break;
			case 34:
				$this->setTwinJobId($value);
				break;
			case 35:
				$this->setBulkJobId($value);
				break;
			case 36:
				$this->setRootJobId($value);
				break;
			case 37:
				$this->setParentJobId($value);
				break;
			case 38:
				$this->setDc($value);
				break;
			case 39:
				$this->setErrType($value);
				break;
			case 40:
				$this->setErrNumber($value);
				break;
			case 41:
				$this->setOnStressDivertTo($value);
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
		if (array_key_exists($keys[3], $arr)) $this->setData($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFileSize($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDuplicationKey($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setAbort($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCheckAgainTimeout($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setProgress($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setMessage($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDescription($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUpdatesCount($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setCreatedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCreatedBy($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setUpdatedAt($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setUpdatedBy($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDeletedAt($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setPriority($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setWorkGroupId($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setQueueTime($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setFinishTime($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setEntryId($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setPartnerId($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setSubpId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setSchedulerId($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setWorkerId($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setBatchIndex($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setLastSchedulerId($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setLastWorkerId($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setLastWorkerRemote($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setProcessorExpiration($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setExecutionAttempts($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setLockVersion($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setTwinJobId($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setBulkJobId($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setRootJobId($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setParentJobId($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setDc($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setErrType($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setErrNumber($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setOnStressDivertTo($arr[$keys[41]]);
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
		if ($this->isColumnModified(BatchJobPeer::DATA)) $criteria->add(BatchJobPeer::DATA, $this->data);
		if ($this->isColumnModified(BatchJobPeer::FILE_SIZE)) $criteria->add(BatchJobPeer::FILE_SIZE, $this->file_size);
		if ($this->isColumnModified(BatchJobPeer::DUPLICATION_KEY)) $criteria->add(BatchJobPeer::DUPLICATION_KEY, $this->duplication_key);
		if ($this->isColumnModified(BatchJobPeer::STATUS)) $criteria->add(BatchJobPeer::STATUS, $this->status);
		if ($this->isColumnModified(BatchJobPeer::ABORT)) $criteria->add(BatchJobPeer::ABORT, $this->abort);
		if ($this->isColumnModified(BatchJobPeer::CHECK_AGAIN_TIMEOUT)) $criteria->add(BatchJobPeer::CHECK_AGAIN_TIMEOUT, $this->check_again_timeout);
		if ($this->isColumnModified(BatchJobPeer::PROGRESS)) $criteria->add(BatchJobPeer::PROGRESS, $this->progress);
		if ($this->isColumnModified(BatchJobPeer::MESSAGE)) $criteria->add(BatchJobPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(BatchJobPeer::DESCRIPTION)) $criteria->add(BatchJobPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(BatchJobPeer::UPDATES_COUNT)) $criteria->add(BatchJobPeer::UPDATES_COUNT, $this->updates_count);
		if ($this->isColumnModified(BatchJobPeer::CREATED_AT)) $criteria->add(BatchJobPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(BatchJobPeer::CREATED_BY)) $criteria->add(BatchJobPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(BatchJobPeer::UPDATED_AT)) $criteria->add(BatchJobPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(BatchJobPeer::UPDATED_BY)) $criteria->add(BatchJobPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(BatchJobPeer::DELETED_AT)) $criteria->add(BatchJobPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(BatchJobPeer::PRIORITY)) $criteria->add(BatchJobPeer::PRIORITY, $this->priority);
		if ($this->isColumnModified(BatchJobPeer::WORK_GROUP_ID)) $criteria->add(BatchJobPeer::WORK_GROUP_ID, $this->work_group_id);
		if ($this->isColumnModified(BatchJobPeer::QUEUE_TIME)) $criteria->add(BatchJobPeer::QUEUE_TIME, $this->queue_time);
		if ($this->isColumnModified(BatchJobPeer::FINISH_TIME)) $criteria->add(BatchJobPeer::FINISH_TIME, $this->finish_time);
		if ($this->isColumnModified(BatchJobPeer::ENTRY_ID)) $criteria->add(BatchJobPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(BatchJobPeer::PARTNER_ID)) $criteria->add(BatchJobPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(BatchJobPeer::SUBP_ID)) $criteria->add(BatchJobPeer::SUBP_ID, $this->subp_id);
		if ($this->isColumnModified(BatchJobPeer::SCHEDULER_ID)) $criteria->add(BatchJobPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(BatchJobPeer::WORKER_ID)) $criteria->add(BatchJobPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(BatchJobPeer::BATCH_INDEX)) $criteria->add(BatchJobPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(BatchJobPeer::LAST_SCHEDULER_ID)) $criteria->add(BatchJobPeer::LAST_SCHEDULER_ID, $this->last_scheduler_id);
		if ($this->isColumnModified(BatchJobPeer::LAST_WORKER_ID)) $criteria->add(BatchJobPeer::LAST_WORKER_ID, $this->last_worker_id);
		if ($this->isColumnModified(BatchJobPeer::LAST_WORKER_REMOTE)) $criteria->add(BatchJobPeer::LAST_WORKER_REMOTE, $this->last_worker_remote);
		if ($this->isColumnModified(BatchJobPeer::PROCESSOR_EXPIRATION)) $criteria->add(BatchJobPeer::PROCESSOR_EXPIRATION, $this->processor_expiration);
		if ($this->isColumnModified(BatchJobPeer::EXECUTION_ATTEMPTS)) $criteria->add(BatchJobPeer::EXECUTION_ATTEMPTS, $this->execution_attempts);
		if ($this->isColumnModified(BatchJobPeer::LOCK_VERSION)) $criteria->add(BatchJobPeer::LOCK_VERSION, $this->lock_version);
		if ($this->isColumnModified(BatchJobPeer::TWIN_JOB_ID)) $criteria->add(BatchJobPeer::TWIN_JOB_ID, $this->twin_job_id);
		if ($this->isColumnModified(BatchJobPeer::BULK_JOB_ID)) $criteria->add(BatchJobPeer::BULK_JOB_ID, $this->bulk_job_id);
		if ($this->isColumnModified(BatchJobPeer::ROOT_JOB_ID)) $criteria->add(BatchJobPeer::ROOT_JOB_ID, $this->root_job_id);
		if ($this->isColumnModified(BatchJobPeer::PARENT_JOB_ID)) $criteria->add(BatchJobPeer::PARENT_JOB_ID, $this->parent_job_id);
		if ($this->isColumnModified(BatchJobPeer::DC)) $criteria->add(BatchJobPeer::DC, $this->dc);
		if ($this->isColumnModified(BatchJobPeer::ERR_TYPE)) $criteria->add(BatchJobPeer::ERR_TYPE, $this->err_type);
		if ($this->isColumnModified(BatchJobPeer::ERR_NUMBER)) $criteria->add(BatchJobPeer::ERR_NUMBER, $this->err_number);
		if ($this->isColumnModified(BatchJobPeer::ON_STRESS_DIVERT_TO)) $criteria->add(BatchJobPeer::ON_STRESS_DIVERT_TO, $this->on_stress_divert_to);

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

		$copyObj->setData($this->data);

		$copyObj->setFileSize($this->file_size);

		$copyObj->setDuplicationKey($this->duplication_key);

		$copyObj->setStatus($this->status);

		$copyObj->setAbort($this->abort);

		$copyObj->setCheckAgainTimeout($this->check_again_timeout);

		$copyObj->setProgress($this->progress);

		$copyObj->setMessage($this->message);

		$copyObj->setDescription($this->description);

		$copyObj->setUpdatesCount($this->updates_count);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setUpdatedBy($this->updated_by);

		$copyObj->setDeletedAt($this->deleted_at);

		$copyObj->setPriority($this->priority);

		$copyObj->setWorkGroupId($this->work_group_id);

		$copyObj->setQueueTime($this->queue_time);

		$copyObj->setFinishTime($this->finish_time);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setSubpId($this->subp_id);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setBatchIndex($this->batch_index);

		$copyObj->setLastSchedulerId($this->last_scheduler_id);

		$copyObj->setLastWorkerId($this->last_worker_id);

		$copyObj->setLastWorkerRemote($this->last_worker_remote);

		$copyObj->setProcessorExpiration($this->processor_expiration);

		$copyObj->setExecutionAttempts($this->execution_attempts);

		$copyObj->setLockVersion($this->lock_version);

		$copyObj->setTwinJobId($this->twin_job_id);

		$copyObj->setBulkJobId($this->bulk_job_id);

		$copyObj->setRootJobId($this->root_job_id);

		$copyObj->setParentJobId($this->parent_job_id);

		$copyObj->setDc($this->dc);

		$copyObj->setErrType($this->err_type);

		$copyObj->setErrNumber($this->err_number);

		$copyObj->setOnStressDivertTo($this->on_stress_divert_to);


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

	}

} // BaseBatchJob
