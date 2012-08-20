<?php

/**
 * Base class that represents a row from the 'batch_job_log' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBatchJobLog extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        BatchJobLogPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the job_id field.
	 * @var        int
	 */
	protected $job_id;

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
	 * The value for the log_status field.
	 * @var        int
	 */
	protected $log_status;

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
	 * The value for the param_1 field.
	 * @var        int
	 */
	protected $param_1;

	/**
	 * The value for the param_2 field.
	 * @var        string
	 */
	protected $param_2;

	/**
	 * The value for the param_3 field.
	 * @var        string
	 */
	protected $param_3;

	/**
	 * The value for the param_4 field.
	 * @var        int
	 */
	protected $param_4;

	/**
	 * The value for the param_5 field.
	 * @var        string
	 */
	protected $param_5;

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
		$this->entry_id = '';
		$this->partner_id = 0;
		$this->subp_id = 0;
	}

	/**
	 * Initializes internal state of BaseBatchJobLog object.
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
	 * Get the [job_id] column value.
	 * 
	 * @return     int
	 */
	public function getJobId()
	{
		return $this->job_id;
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
	 * Get the [log_status] column value.
	 * 
	 * @return     int
	 */
	public function getLogStatus()
	{
		return $this->log_status;
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
	 * Get the [param_1] column value.
	 * 
	 * @return     int
	 */
	public function getParam1()
	{
		return $this->param_1;
	}

	/**
	 * Get the [param_2] column value.
	 * 
	 * @return     string
	 */
	public function getParam2()
	{
		return $this->param_2;
	}

	/**
	 * Get the [param_3] column value.
	 * 
	 * @return     string
	 */
	public function getParam3()
	{
		return $this->param_3;
	}

	/**
	 * Get the [param_4] column value.
	 * 
	 * @return     int
	 */
	public function getParam4()
	{
		return $this->param_4;
	}

	/**
	 * Get the [param_5] column value.
	 * 
	 * @return     string
	 */
	public function getParam5()
	{
		return $this->param_5;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ID]))
			$this->oldColumnsValues[BatchJobLogPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::JOB_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::JOB_ID] = $this->job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_id !== $v) {
			$this->job_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::JOB_ID;
		}

		return $this;
	} // setJobId()

	/**
	 * Set the value of [job_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setJobType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::JOB_TYPE]))
			$this->oldColumnsValues[BatchJobLogPeer::JOB_TYPE] = $this->job_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_type !== $v) {
			$this->job_type = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::JOB_TYPE;
		}

		return $this;
	} // setJobType()

	/**
	 * Set the value of [job_sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setJobSubType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::JOB_SUB_TYPE]))
			$this->oldColumnsValues[BatchJobLogPeer::JOB_SUB_TYPE] = $this->job_sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_sub_type !== $v) {
			$this->job_sub_type = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::JOB_SUB_TYPE;
		}

		return $this;
	} // setJobSubType()

	/**
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setData($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::DATA]))
			$this->oldColumnsValues[BatchJobLogPeer::DATA] = $this->data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->data !== $v) {
			$this->data = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::DATA;
		}

		return $this;
	} // setData()

	/**
	 * Set the value of [file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setFileSize($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::FILE_SIZE]))
			$this->oldColumnsValues[BatchJobLogPeer::FILE_SIZE] = $this->file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::FILE_SIZE;
		}

		return $this;
	} // setFileSize()

	/**
	 * Set the value of [duplication_key] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setDuplicationKey($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::DUPLICATION_KEY]))
			$this->oldColumnsValues[BatchJobLogPeer::DUPLICATION_KEY] = $this->duplication_key;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->duplication_key !== $v) {
			$this->duplication_key = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::DUPLICATION_KEY;
		}

		return $this;
	} // setDuplicationKey()

	/**
	 * Set the value of [log_status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setLogStatus($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::LOG_STATUS]))
			$this->oldColumnsValues[BatchJobLogPeer::LOG_STATUS] = $this->log_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->log_status !== $v) {
			$this->log_status = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::LOG_STATUS;
		}

		return $this;
	} // setLogStatus()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::STATUS]))
			$this->oldColumnsValues[BatchJobLogPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [abort] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setAbort($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ABORT]))
			$this->oldColumnsValues[BatchJobLogPeer::ABORT] = $this->abort;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->abort !== $v) {
			$this->abort = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ABORT;
		}

		return $this;
	} // setAbort()

	/**
	 * Set the value of [check_again_timeout] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setCheckAgainTimeout($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::CHECK_AGAIN_TIMEOUT]))
			$this->oldColumnsValues[BatchJobLogPeer::CHECK_AGAIN_TIMEOUT] = $this->check_again_timeout;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->check_again_timeout !== $v) {
			$this->check_again_timeout = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::CHECK_AGAIN_TIMEOUT;
		}

		return $this;
	} // setCheckAgainTimeout()

	/**
	 * Set the value of [progress] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setProgress($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PROGRESS]))
			$this->oldColumnsValues[BatchJobLogPeer::PROGRESS] = $this->progress;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->progress !== $v) {
			$this->progress = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PROGRESS;
		}

		return $this;
	} // setProgress()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setMessage($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::MESSAGE]))
			$this->oldColumnsValues[BatchJobLogPeer::MESSAGE] = $this->message;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->message !== $v) {
			$this->message = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::MESSAGE;
		}

		return $this;
	} // setMessage()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::DESCRIPTION]))
			$this->oldColumnsValues[BatchJobLogPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [updates_count] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setUpdatesCount($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::UPDATES_COUNT]))
			$this->oldColumnsValues[BatchJobLogPeer::UPDATES_COUNT] = $this->updates_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->updates_count !== $v) {
			$this->updates_count = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::UPDATES_COUNT;
		}

		return $this;
	} // setUpdatesCount()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
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
				$this->modifiedColumns[] = BatchJobLogPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setCreatedBy($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::CREATED_BY]))
			$this->oldColumnsValues[BatchJobLogPeer::CREATED_BY] = $this->created_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::CREATED_BY;
		}

		return $this;
	} // setCreatedBy()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
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
				$this->modifiedColumns[] = BatchJobLogPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [updated_by] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setUpdatedBy($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::UPDATED_BY]))
			$this->oldColumnsValues[BatchJobLogPeer::UPDATED_BY] = $this->updated_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->updated_by !== $v) {
			$this->updated_by = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::UPDATED_BY;
		}

		return $this;
	} // setUpdatedBy()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::DELETED_AT]))
			$this->oldColumnsValues[BatchJobLogPeer::DELETED_AT] = $this->deleted_at;

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
				$this->modifiedColumns[] = BatchJobLogPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [priority] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PRIORITY]))
			$this->oldColumnsValues[BatchJobLogPeer::PRIORITY] = $this->priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PRIORITY;
		}

		return $this;
	} // setPriority()

	/**
	 * Set the value of [work_group_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setWorkGroupId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::WORK_GROUP_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::WORK_GROUP_ID] = $this->work_group_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->work_group_id !== $v) {
			$this->work_group_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::WORK_GROUP_ID;
		}

		return $this;
	} // setWorkGroupId()

	/**
	 * Sets the value of [queue_time] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setQueueTime($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::QUEUE_TIME]))
			$this->oldColumnsValues[BatchJobLogPeer::QUEUE_TIME] = $this->queue_time;

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
				$this->modifiedColumns[] = BatchJobLogPeer::QUEUE_TIME;
			}
		} // if either are not null

		return $this;
	} // setQueueTime()

	/**
	 * Sets the value of [finish_time] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setFinishTime($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::FINISH_TIME]))
			$this->oldColumnsValues[BatchJobLogPeer::FINISH_TIME] = $this->finish_time;

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
				$this->modifiedColumns[] = BatchJobLogPeer::FINISH_TIME;
			}
		} // if either are not null

		return $this;
	} // setFinishTime()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ENTRY_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v || $this->isNew()) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARTNER_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [subp_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setSubpId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::SUBP_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::SUBP_ID] = $this->subp_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subp_id !== $v || $this->isNew()) {
			$this->subp_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::SUBP_ID;
		}

		return $this;
	} // setSubpId()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::WORKER_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::BATCH_INDEX]))
			$this->oldColumnsValues[BatchJobLogPeer::BATCH_INDEX] = $this->batch_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Set the value of [last_scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setLastSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::LAST_SCHEDULER_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::LAST_SCHEDULER_ID] = $this->last_scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->last_scheduler_id !== $v) {
			$this->last_scheduler_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::LAST_SCHEDULER_ID;
		}

		return $this;
	} // setLastSchedulerId()

	/**
	 * Set the value of [last_worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setLastWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::LAST_WORKER_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::LAST_WORKER_ID] = $this->last_worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->last_worker_id !== $v) {
			$this->last_worker_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::LAST_WORKER_ID;
		}

		return $this;
	} // setLastWorkerId()

	/**
	 * Set the value of [last_worker_remote] column.
	 * 
	 * @param      boolean $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setLastWorkerRemote($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::LAST_WORKER_REMOTE]))
			$this->oldColumnsValues[BatchJobLogPeer::LAST_WORKER_REMOTE] = $this->last_worker_remote;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->last_worker_remote !== $v) {
			$this->last_worker_remote = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::LAST_WORKER_REMOTE;
		}

		return $this;
	} // setLastWorkerRemote()

	/**
	 * Sets the value of [processor_expiration] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setProcessorExpiration($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PROCESSOR_EXPIRATION]))
			$this->oldColumnsValues[BatchJobLogPeer::PROCESSOR_EXPIRATION] = $this->processor_expiration;

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
				$this->modifiedColumns[] = BatchJobLogPeer::PROCESSOR_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setProcessorExpiration()

	/**
	 * Set the value of [execution_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setExecutionAttempts($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::EXECUTION_ATTEMPTS]))
			$this->oldColumnsValues[BatchJobLogPeer::EXECUTION_ATTEMPTS] = $this->execution_attempts;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_attempts !== $v) {
			$this->execution_attempts = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::EXECUTION_ATTEMPTS;
		}

		return $this;
	} // setExecutionAttempts()

	/**
	 * Set the value of [lock_version] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setLockVersion($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::LOCK_VERSION]))
			$this->oldColumnsValues[BatchJobLogPeer::LOCK_VERSION] = $this->lock_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lock_version !== $v) {
			$this->lock_version = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::LOCK_VERSION;
		}

		return $this;
	} // setLockVersion()

	/**
	 * Set the value of [twin_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setTwinJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::TWIN_JOB_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::TWIN_JOB_ID] = $this->twin_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->twin_job_id !== $v) {
			$this->twin_job_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::TWIN_JOB_ID;
		}

		return $this;
	} // setTwinJobId()

	/**
	 * Set the value of [bulk_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setBulkJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::BULK_JOB_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::BULK_JOB_ID] = $this->bulk_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->bulk_job_id !== $v) {
			$this->bulk_job_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::BULK_JOB_ID;
		}

		return $this;
	} // setBulkJobId()

	/**
	 * Set the value of [root_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setRootJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ROOT_JOB_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::ROOT_JOB_ID] = $this->root_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->root_job_id !== $v) {
			$this->root_job_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ROOT_JOB_ID;
		}

		return $this;
	} // setRootJobId()

	/**
	 * Set the value of [parent_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParentJobId($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARENT_JOB_ID]))
			$this->oldColumnsValues[BatchJobLogPeer::PARENT_JOB_ID] = $this->parent_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->parent_job_id !== $v) {
			$this->parent_job_id = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARENT_JOB_ID;
		}

		return $this;
	} // setParentJobId()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::DC]))
			$this->oldColumnsValues[BatchJobLogPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [err_type] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setErrType($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ERR_TYPE]))
			$this->oldColumnsValues[BatchJobLogPeer::ERR_TYPE] = $this->err_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->err_type !== $v) {
			$this->err_type = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ERR_TYPE;
		}

		return $this;
	} // setErrType()

	/**
	 * Set the value of [err_number] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setErrNumber($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ERR_NUMBER]))
			$this->oldColumnsValues[BatchJobLogPeer::ERR_NUMBER] = $this->err_number;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->err_number !== $v) {
			$this->err_number = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ERR_NUMBER;
		}

		return $this;
	} // setErrNumber()

	/**
	 * Set the value of [on_stress_divert_to] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setOnStressDivertTo($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::ON_STRESS_DIVERT_TO]))
			$this->oldColumnsValues[BatchJobLogPeer::ON_STRESS_DIVERT_TO] = $this->on_stress_divert_to;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->on_stress_divert_to !== $v) {
			$this->on_stress_divert_to = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::ON_STRESS_DIVERT_TO;
		}

		return $this;
	} // setOnStressDivertTo()

	/**
	 * Set the value of [param_1] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParam1($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARAM_1]))
			$this->oldColumnsValues[BatchJobLogPeer::PARAM_1] = $this->param_1;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->param_1 !== $v) {
			$this->param_1 = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARAM_1;
		}

		return $this;
	} // setParam1()

	/**
	 * Set the value of [param_2] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParam2($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARAM_2]))
			$this->oldColumnsValues[BatchJobLogPeer::PARAM_2] = $this->param_2;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->param_2 !== $v) {
			$this->param_2 = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARAM_2;
		}

		return $this;
	} // setParam2()

	/**
	 * Set the value of [param_3] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParam3($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARAM_3]))
			$this->oldColumnsValues[BatchJobLogPeer::PARAM_3] = $this->param_3;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->param_3 !== $v) {
			$this->param_3 = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARAM_3;
		}

		return $this;
	} // setParam3()

	/**
	 * Set the value of [param_4] column.
	 * 
	 * @param      int $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParam4($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARAM_4]))
			$this->oldColumnsValues[BatchJobLogPeer::PARAM_4] = $this->param_4;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->param_4 !== $v) {
			$this->param_4 = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARAM_4;
		}

		return $this;
	} // setParam4()

	/**
	 * Set the value of [param_5] column.
	 * 
	 * @param      string $v new value
	 * @return     BatchJobLog The current object (for fluent API support)
	 */
	public function setParam5($v)
	{
		if(!isset($this->oldColumnsValues[BatchJobLogPeer::PARAM_5]))
			$this->oldColumnsValues[BatchJobLogPeer::PARAM_5] = $this->param_5;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->param_5 !== $v) {
			$this->param_5 = $v;
			$this->modifiedColumns[] = BatchJobLogPeer::PARAM_5;
		}

		return $this;
	} // setParam5()

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
			$this->job_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->job_type = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->job_sub_type = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->data = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->file_size = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->duplication_key = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->log_status = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->status = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->abort = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->check_again_timeout = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->progress = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->message = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->description = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->updates_count = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->created_at = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->created_by = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->updated_at = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->updated_by = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->deleted_at = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->priority = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->work_group_id = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->queue_time = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->finish_time = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->entry_id = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->partner_id = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->subp_id = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->scheduler_id = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->worker_id = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
			$this->batch_index = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
			$this->last_scheduler_id = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->last_worker_id = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->last_worker_remote = ($row[$startcol + 32] !== null) ? (boolean) $row[$startcol + 32] : null;
			$this->processor_expiration = ($row[$startcol + 33] !== null) ? (string) $row[$startcol + 33] : null;
			$this->execution_attempts = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->lock_version = ($row[$startcol + 35] !== null) ? (int) $row[$startcol + 35] : null;
			$this->twin_job_id = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->bulk_job_id = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->root_job_id = ($row[$startcol + 38] !== null) ? (int) $row[$startcol + 38] : null;
			$this->parent_job_id = ($row[$startcol + 39] !== null) ? (int) $row[$startcol + 39] : null;
			$this->dc = ($row[$startcol + 40] !== null) ? (int) $row[$startcol + 40] : null;
			$this->err_type = ($row[$startcol + 41] !== null) ? (int) $row[$startcol + 41] : null;
			$this->err_number = ($row[$startcol + 42] !== null) ? (int) $row[$startcol + 42] : null;
			$this->on_stress_divert_to = ($row[$startcol + 43] !== null) ? (int) $row[$startcol + 43] : null;
			$this->param_1 = ($row[$startcol + 44] !== null) ? (int) $row[$startcol + 44] : null;
			$this->param_2 = ($row[$startcol + 45] !== null) ? (string) $row[$startcol + 45] : null;
			$this->param_3 = ($row[$startcol + 46] !== null) ? (string) $row[$startcol + 46] : null;
			$this->param_4 = ($row[$startcol + 47] !== null) ? (int) $row[$startcol + 47] : null;
			$this->param_5 = ($row[$startcol + 48] !== null) ? (string) $row[$startcol + 48] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 49; // 49 = BatchJobLogPeer::NUM_COLUMNS - BatchJobLogPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating BatchJobLog object", $e);
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
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		BatchJobLogPeer::setUseCriteriaFilter(false);
		$stmt = BatchJobLogPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		BatchJobLogPeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				BatchJobLogPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				BatchJobLogPeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = BatchJobLogPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = BatchJobLogPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = BatchJobLogPeer::doUpdate($this, $con);
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


			if (($retval = BatchJobLogPeer::doValidate($this, $columns)) !== true) {
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
		$pos = BatchJobLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getJobId();
				break;
			case 2:
				return $this->getJobType();
				break;
			case 3:
				return $this->getJobSubType();
				break;
			case 4:
				return $this->getData();
				break;
			case 5:
				return $this->getFileSize();
				break;
			case 6:
				return $this->getDuplicationKey();
				break;
			case 7:
				return $this->getLogStatus();
				break;
			case 8:
				return $this->getStatus();
				break;
			case 9:
				return $this->getAbort();
				break;
			case 10:
				return $this->getCheckAgainTimeout();
				break;
			case 11:
				return $this->getProgress();
				break;
			case 12:
				return $this->getMessage();
				break;
			case 13:
				return $this->getDescription();
				break;
			case 14:
				return $this->getUpdatesCount();
				break;
			case 15:
				return $this->getCreatedAt();
				break;
			case 16:
				return $this->getCreatedBy();
				break;
			case 17:
				return $this->getUpdatedAt();
				break;
			case 18:
				return $this->getUpdatedBy();
				break;
			case 19:
				return $this->getDeletedAt();
				break;
			case 20:
				return $this->getPriority();
				break;
			case 21:
				return $this->getWorkGroupId();
				break;
			case 22:
				return $this->getQueueTime();
				break;
			case 23:
				return $this->getFinishTime();
				break;
			case 24:
				return $this->getEntryId();
				break;
			case 25:
				return $this->getPartnerId();
				break;
			case 26:
				return $this->getSubpId();
				break;
			case 27:
				return $this->getSchedulerId();
				break;
			case 28:
				return $this->getWorkerId();
				break;
			case 29:
				return $this->getBatchIndex();
				break;
			case 30:
				return $this->getLastSchedulerId();
				break;
			case 31:
				return $this->getLastWorkerId();
				break;
			case 32:
				return $this->getLastWorkerRemote();
				break;
			case 33:
				return $this->getProcessorExpiration();
				break;
			case 34:
				return $this->getExecutionAttempts();
				break;
			case 35:
				return $this->getLockVersion();
				break;
			case 36:
				return $this->getTwinJobId();
				break;
			case 37:
				return $this->getBulkJobId();
				break;
			case 38:
				return $this->getRootJobId();
				break;
			case 39:
				return $this->getParentJobId();
				break;
			case 40:
				return $this->getDc();
				break;
			case 41:
				return $this->getErrType();
				break;
			case 42:
				return $this->getErrNumber();
				break;
			case 43:
				return $this->getOnStressDivertTo();
				break;
			case 44:
				return $this->getParam1();
				break;
			case 45:
				return $this->getParam2();
				break;
			case 46:
				return $this->getParam3();
				break;
			case 47:
				return $this->getParam4();
				break;
			case 48:
				return $this->getParam5();
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
		$keys = BatchJobLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getJobId(),
			$keys[2] => $this->getJobType(),
			$keys[3] => $this->getJobSubType(),
			$keys[4] => $this->getData(),
			$keys[5] => $this->getFileSize(),
			$keys[6] => $this->getDuplicationKey(),
			$keys[7] => $this->getLogStatus(),
			$keys[8] => $this->getStatus(),
			$keys[9] => $this->getAbort(),
			$keys[10] => $this->getCheckAgainTimeout(),
			$keys[11] => $this->getProgress(),
			$keys[12] => $this->getMessage(),
			$keys[13] => $this->getDescription(),
			$keys[14] => $this->getUpdatesCount(),
			$keys[15] => $this->getCreatedAt(),
			$keys[16] => $this->getCreatedBy(),
			$keys[17] => $this->getUpdatedAt(),
			$keys[18] => $this->getUpdatedBy(),
			$keys[19] => $this->getDeletedAt(),
			$keys[20] => $this->getPriority(),
			$keys[21] => $this->getWorkGroupId(),
			$keys[22] => $this->getQueueTime(),
			$keys[23] => $this->getFinishTime(),
			$keys[24] => $this->getEntryId(),
			$keys[25] => $this->getPartnerId(),
			$keys[26] => $this->getSubpId(),
			$keys[27] => $this->getSchedulerId(),
			$keys[28] => $this->getWorkerId(),
			$keys[29] => $this->getBatchIndex(),
			$keys[30] => $this->getLastSchedulerId(),
			$keys[31] => $this->getLastWorkerId(),
			$keys[32] => $this->getLastWorkerRemote(),
			$keys[33] => $this->getProcessorExpiration(),
			$keys[34] => $this->getExecutionAttempts(),
			$keys[35] => $this->getLockVersion(),
			$keys[36] => $this->getTwinJobId(),
			$keys[37] => $this->getBulkJobId(),
			$keys[38] => $this->getRootJobId(),
			$keys[39] => $this->getParentJobId(),
			$keys[40] => $this->getDc(),
			$keys[41] => $this->getErrType(),
			$keys[42] => $this->getErrNumber(),
			$keys[43] => $this->getOnStressDivertTo(),
			$keys[44] => $this->getParam1(),
			$keys[45] => $this->getParam2(),
			$keys[46] => $this->getParam3(),
			$keys[47] => $this->getParam4(),
			$keys[48] => $this->getParam5(),
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
		$pos = BatchJobLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setJobId($value);
				break;
			case 2:
				$this->setJobType($value);
				break;
			case 3:
				$this->setJobSubType($value);
				break;
			case 4:
				$this->setData($value);
				break;
			case 5:
				$this->setFileSize($value);
				break;
			case 6:
				$this->setDuplicationKey($value);
				break;
			case 7:
				$this->setLogStatus($value);
				break;
			case 8:
				$this->setStatus($value);
				break;
			case 9:
				$this->setAbort($value);
				break;
			case 10:
				$this->setCheckAgainTimeout($value);
				break;
			case 11:
				$this->setProgress($value);
				break;
			case 12:
				$this->setMessage($value);
				break;
			case 13:
				$this->setDescription($value);
				break;
			case 14:
				$this->setUpdatesCount($value);
				break;
			case 15:
				$this->setCreatedAt($value);
				break;
			case 16:
				$this->setCreatedBy($value);
				break;
			case 17:
				$this->setUpdatedAt($value);
				break;
			case 18:
				$this->setUpdatedBy($value);
				break;
			case 19:
				$this->setDeletedAt($value);
				break;
			case 20:
				$this->setPriority($value);
				break;
			case 21:
				$this->setWorkGroupId($value);
				break;
			case 22:
				$this->setQueueTime($value);
				break;
			case 23:
				$this->setFinishTime($value);
				break;
			case 24:
				$this->setEntryId($value);
				break;
			case 25:
				$this->setPartnerId($value);
				break;
			case 26:
				$this->setSubpId($value);
				break;
			case 27:
				$this->setSchedulerId($value);
				break;
			case 28:
				$this->setWorkerId($value);
				break;
			case 29:
				$this->setBatchIndex($value);
				break;
			case 30:
				$this->setLastSchedulerId($value);
				break;
			case 31:
				$this->setLastWorkerId($value);
				break;
			case 32:
				$this->setLastWorkerRemote($value);
				break;
			case 33:
				$this->setProcessorExpiration($value);
				break;
			case 34:
				$this->setExecutionAttempts($value);
				break;
			case 35:
				$this->setLockVersion($value);
				break;
			case 36:
				$this->setTwinJobId($value);
				break;
			case 37:
				$this->setBulkJobId($value);
				break;
			case 38:
				$this->setRootJobId($value);
				break;
			case 39:
				$this->setParentJobId($value);
				break;
			case 40:
				$this->setDc($value);
				break;
			case 41:
				$this->setErrType($value);
				break;
			case 42:
				$this->setErrNumber($value);
				break;
			case 43:
				$this->setOnStressDivertTo($value);
				break;
			case 44:
				$this->setParam1($value);
				break;
			case 45:
				$this->setParam2($value);
				break;
			case 46:
				$this->setParam3($value);
				break;
			case 47:
				$this->setParam4($value);
				break;
			case 48:
				$this->setParam5($value);
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
		$keys = BatchJobLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setJobId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJobType($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setJobSubType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setData($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFileSize($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDuplicationKey($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLogStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStatus($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setAbort($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCheckAgainTimeout($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setProgress($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setMessage($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDescription($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setUpdatesCount($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCreatedAt($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setCreatedBy($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setUpdatedAt($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setUpdatedBy($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setDeletedAt($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setPriority($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setWorkGroupId($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setQueueTime($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setFinishTime($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setEntryId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setPartnerId($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setSubpId($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setSchedulerId($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setWorkerId($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setBatchIndex($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setLastSchedulerId($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setLastWorkerId($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setLastWorkerRemote($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setProcessorExpiration($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setExecutionAttempts($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setLockVersion($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setTwinJobId($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setBulkJobId($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setRootJobId($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setParentJobId($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setDc($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setErrType($arr[$keys[41]]);
		if (array_key_exists($keys[42], $arr)) $this->setErrNumber($arr[$keys[42]]);
		if (array_key_exists($keys[43], $arr)) $this->setOnStressDivertTo($arr[$keys[43]]);
		if (array_key_exists($keys[44], $arr)) $this->setParam1($arr[$keys[44]]);
		if (array_key_exists($keys[45], $arr)) $this->setParam2($arr[$keys[45]]);
		if (array_key_exists($keys[46], $arr)) $this->setParam3($arr[$keys[46]]);
		if (array_key_exists($keys[47], $arr)) $this->setParam4($arr[$keys[47]]);
		if (array_key_exists($keys[48], $arr)) $this->setParam5($arr[$keys[48]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(BatchJobLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(BatchJobLogPeer::ID)) $criteria->add(BatchJobLogPeer::ID, $this->id);
		if ($this->isColumnModified(BatchJobLogPeer::JOB_ID)) $criteria->add(BatchJobLogPeer::JOB_ID, $this->job_id);
		if ($this->isColumnModified(BatchJobLogPeer::JOB_TYPE)) $criteria->add(BatchJobLogPeer::JOB_TYPE, $this->job_type);
		if ($this->isColumnModified(BatchJobLogPeer::JOB_SUB_TYPE)) $criteria->add(BatchJobLogPeer::JOB_SUB_TYPE, $this->job_sub_type);
		if ($this->isColumnModified(BatchJobLogPeer::DATA)) $criteria->add(BatchJobLogPeer::DATA, $this->data);
		if ($this->isColumnModified(BatchJobLogPeer::FILE_SIZE)) $criteria->add(BatchJobLogPeer::FILE_SIZE, $this->file_size);
		if ($this->isColumnModified(BatchJobLogPeer::DUPLICATION_KEY)) $criteria->add(BatchJobLogPeer::DUPLICATION_KEY, $this->duplication_key);
		if ($this->isColumnModified(BatchJobLogPeer::LOG_STATUS)) $criteria->add(BatchJobLogPeer::LOG_STATUS, $this->log_status);
		if ($this->isColumnModified(BatchJobLogPeer::STATUS)) $criteria->add(BatchJobLogPeer::STATUS, $this->status);
		if ($this->isColumnModified(BatchJobLogPeer::ABORT)) $criteria->add(BatchJobLogPeer::ABORT, $this->abort);
		if ($this->isColumnModified(BatchJobLogPeer::CHECK_AGAIN_TIMEOUT)) $criteria->add(BatchJobLogPeer::CHECK_AGAIN_TIMEOUT, $this->check_again_timeout);
		if ($this->isColumnModified(BatchJobLogPeer::PROGRESS)) $criteria->add(BatchJobLogPeer::PROGRESS, $this->progress);
		if ($this->isColumnModified(BatchJobLogPeer::MESSAGE)) $criteria->add(BatchJobLogPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(BatchJobLogPeer::DESCRIPTION)) $criteria->add(BatchJobLogPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(BatchJobLogPeer::UPDATES_COUNT)) $criteria->add(BatchJobLogPeer::UPDATES_COUNT, $this->updates_count);
		if ($this->isColumnModified(BatchJobLogPeer::CREATED_AT)) $criteria->add(BatchJobLogPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(BatchJobLogPeer::CREATED_BY)) $criteria->add(BatchJobLogPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(BatchJobLogPeer::UPDATED_AT)) $criteria->add(BatchJobLogPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(BatchJobLogPeer::UPDATED_BY)) $criteria->add(BatchJobLogPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(BatchJobLogPeer::DELETED_AT)) $criteria->add(BatchJobLogPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(BatchJobLogPeer::PRIORITY)) $criteria->add(BatchJobLogPeer::PRIORITY, $this->priority);
		if ($this->isColumnModified(BatchJobLogPeer::WORK_GROUP_ID)) $criteria->add(BatchJobLogPeer::WORK_GROUP_ID, $this->work_group_id);
		if ($this->isColumnModified(BatchJobLogPeer::QUEUE_TIME)) $criteria->add(BatchJobLogPeer::QUEUE_TIME, $this->queue_time);
		if ($this->isColumnModified(BatchJobLogPeer::FINISH_TIME)) $criteria->add(BatchJobLogPeer::FINISH_TIME, $this->finish_time);
		if ($this->isColumnModified(BatchJobLogPeer::ENTRY_ID)) $criteria->add(BatchJobLogPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(BatchJobLogPeer::PARTNER_ID)) $criteria->add(BatchJobLogPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(BatchJobLogPeer::SUBP_ID)) $criteria->add(BatchJobLogPeer::SUBP_ID, $this->subp_id);
		if ($this->isColumnModified(BatchJobLogPeer::SCHEDULER_ID)) $criteria->add(BatchJobLogPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(BatchJobLogPeer::WORKER_ID)) $criteria->add(BatchJobLogPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(BatchJobLogPeer::BATCH_INDEX)) $criteria->add(BatchJobLogPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(BatchJobLogPeer::LAST_SCHEDULER_ID)) $criteria->add(BatchJobLogPeer::LAST_SCHEDULER_ID, $this->last_scheduler_id);
		if ($this->isColumnModified(BatchJobLogPeer::LAST_WORKER_ID)) $criteria->add(BatchJobLogPeer::LAST_WORKER_ID, $this->last_worker_id);
		if ($this->isColumnModified(BatchJobLogPeer::LAST_WORKER_REMOTE)) $criteria->add(BatchJobLogPeer::LAST_WORKER_REMOTE, $this->last_worker_remote);
		if ($this->isColumnModified(BatchJobLogPeer::PROCESSOR_EXPIRATION)) $criteria->add(BatchJobLogPeer::PROCESSOR_EXPIRATION, $this->processor_expiration);
		if ($this->isColumnModified(BatchJobLogPeer::EXECUTION_ATTEMPTS)) $criteria->add(BatchJobLogPeer::EXECUTION_ATTEMPTS, $this->execution_attempts);
		if ($this->isColumnModified(BatchJobLogPeer::LOCK_VERSION)) $criteria->add(BatchJobLogPeer::LOCK_VERSION, $this->lock_version);
		if ($this->isColumnModified(BatchJobLogPeer::TWIN_JOB_ID)) $criteria->add(BatchJobLogPeer::TWIN_JOB_ID, $this->twin_job_id);
		if ($this->isColumnModified(BatchJobLogPeer::BULK_JOB_ID)) $criteria->add(BatchJobLogPeer::BULK_JOB_ID, $this->bulk_job_id);
		if ($this->isColumnModified(BatchJobLogPeer::ROOT_JOB_ID)) $criteria->add(BatchJobLogPeer::ROOT_JOB_ID, $this->root_job_id);
		if ($this->isColumnModified(BatchJobLogPeer::PARENT_JOB_ID)) $criteria->add(BatchJobLogPeer::PARENT_JOB_ID, $this->parent_job_id);
		if ($this->isColumnModified(BatchJobLogPeer::DC)) $criteria->add(BatchJobLogPeer::DC, $this->dc);
		if ($this->isColumnModified(BatchJobLogPeer::ERR_TYPE)) $criteria->add(BatchJobLogPeer::ERR_TYPE, $this->err_type);
		if ($this->isColumnModified(BatchJobLogPeer::ERR_NUMBER)) $criteria->add(BatchJobLogPeer::ERR_NUMBER, $this->err_number);
		if ($this->isColumnModified(BatchJobLogPeer::ON_STRESS_DIVERT_TO)) $criteria->add(BatchJobLogPeer::ON_STRESS_DIVERT_TO, $this->on_stress_divert_to);
		if ($this->isColumnModified(BatchJobLogPeer::PARAM_1)) $criteria->add(BatchJobLogPeer::PARAM_1, $this->param_1);
		if ($this->isColumnModified(BatchJobLogPeer::PARAM_2)) $criteria->add(BatchJobLogPeer::PARAM_2, $this->param_2);
		if ($this->isColumnModified(BatchJobLogPeer::PARAM_3)) $criteria->add(BatchJobLogPeer::PARAM_3, $this->param_3);
		if ($this->isColumnModified(BatchJobLogPeer::PARAM_4)) $criteria->add(BatchJobLogPeer::PARAM_4, $this->param_4);
		if ($this->isColumnModified(BatchJobLogPeer::PARAM_5)) $criteria->add(BatchJobLogPeer::PARAM_5, $this->param_5);

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
		$criteria = new Criteria(BatchJobLogPeer::DATABASE_NAME);

		$criteria->add(BatchJobLogPeer::ID, $this->id);
		
		if($this->alreadyInSave && count($this->modifiedColumns) == 2 && $this->isColumnModified(BatchJobLogPeer::UPDATED_AT))
		{
			$theModifiedColumn = null;
			foreach($this->modifiedColumns as $modifiedColumn)
				if($modifiedColumn != BatchJobLogPeer::UPDATED_AT)
					$theModifiedColumn = $modifiedColumn;
					
			$atomicColumns = BatchJobLogPeer::getAtomicColumns();
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
	 * @param      object $copyObj An object of BatchJobLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setJobId($this->job_id);

		$copyObj->setJobType($this->job_type);

		$copyObj->setJobSubType($this->job_sub_type);

		$copyObj->setData($this->data);

		$copyObj->setFileSize($this->file_size);

		$copyObj->setDuplicationKey($this->duplication_key);

		$copyObj->setLogStatus($this->log_status);

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

		$copyObj->setParam1($this->param_1);

		$copyObj->setParam2($this->param_2);

		$copyObj->setParam3($this->param_3);

		$copyObj->setParam4($this->param_4);

		$copyObj->setParam5($this->param_5);


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
	 * @return     BatchJobLog Clone of current object.
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
	 * @var     BatchJobLog Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      BatchJobLog $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(BatchJobLog $copiedFrom)
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
	 * @return     BatchJobLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new BatchJobLogPeer();
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

} // BaseBatchJobLog
