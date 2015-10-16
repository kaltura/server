<?php

/**
 * Base class that represents a row from the 'control_panel_command' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseControlPanelCommand extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ControlPanelCommandPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

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
	 * The value for the created_by_id field.
	 * @var        int
	 */
	protected $created_by_id;

	/**
	 * The value for the scheduler_id field.
	 * @var        int
	 */
	protected $scheduler_id;

	/**
	 * The value for the scheduler_configured_id field.
	 * @var        int
	 */
	protected $scheduler_configured_id;

	/**
	 * The value for the worker_id field.
	 * @var        int
	 */
	protected $worker_id;

	/**
	 * The value for the worker_configured_id field.
	 * @var        int
	 */
	protected $worker_configured_id;

	/**
	 * The value for the worker_name field.
	 * @var        string
	 */
	protected $worker_name;

	/**
	 * The value for the batch_index field.
	 * @var        int
	 */
	protected $batch_index;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the target_type field.
	 * @var        int
	 */
	protected $target_type;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the cause field.
	 * @var        string
	 */
	protected $cause;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the error_description field.
	 * @var        string
	 */
	protected $error_description;

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
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
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
	 * Get the [created_by_id] column value.
	 * 
	 * @return     int
	 */
	public function getCreatedById()
	{
		return $this->created_by_id;
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
	 * Get the [scheduler_configured_id] column value.
	 * 
	 * @return     int
	 */
	public function getSchedulerConfiguredId()
	{
		return $this->scheduler_configured_id;
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
	 * Get the [worker_configured_id] column value.
	 * 
	 * @return     int
	 */
	public function getWorkerConfiguredId()
	{
		return $this->worker_configured_id;
	}

	/**
	 * Get the [worker_name] column value.
	 * 
	 * @return     string
	 */
	public function getWorkerName()
	{
		return $this->worker_name;
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
	 * Get the [type] column value.
	 * 
	 * @return     int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [target_type] column value.
	 * 
	 * @return     int
	 */
	public function getTargetType()
	{
		return $this->target_type;
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
	 * Get the [cause] column value.
	 * 
	 * @return     string
	 */
	public function getCause()
	{
		return $this->cause;
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
	 * Get the [error_description] column value.
	 * 
	 * @return     string
	 */
	public function getErrorDescription()
	{
		return $this->error_description;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     ControlPanelCommand The current object (for fluent API support)
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
				$this->modifiedColumns[] = ControlPanelCommandPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setCreatedBy($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::CREATED_BY]))
			$this->oldColumnsValues[ControlPanelCommandPeer::CREATED_BY] = $this->created_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::CREATED_BY;
		}

		return $this;
	} // setCreatedBy()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     ControlPanelCommand The current object (for fluent API support)
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
				$this->modifiedColumns[] = ControlPanelCommandPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [updated_by] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setUpdatedBy($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::UPDATED_BY]))
			$this->oldColumnsValues[ControlPanelCommandPeer::UPDATED_BY] = $this->updated_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->updated_by !== $v) {
			$this->updated_by = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::UPDATED_BY;
		}

		return $this;
	} // setUpdatedBy()

	/**
	 * Set the value of [created_by_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setCreatedById($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::CREATED_BY_ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::CREATED_BY_ID] = $this->created_by_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->created_by_id !== $v) {
			$this->created_by_id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::CREATED_BY_ID;
		}

		return $this;
	} // setCreatedById()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [scheduler_configured_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setSchedulerConfiguredId($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID] = $this->scheduler_configured_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_configured_id !== $v) {
			$this->scheduler_configured_id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID;
		}

		return $this;
	} // setSchedulerConfiguredId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::WORKER_ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [worker_configured_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setWorkerConfiguredId($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::WORKER_CONFIGURED_ID]))
			$this->oldColumnsValues[ControlPanelCommandPeer::WORKER_CONFIGURED_ID] = $this->worker_configured_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_configured_id !== $v) {
			$this->worker_configured_id = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::WORKER_CONFIGURED_ID;
		}

		return $this;
	} // setWorkerConfiguredId()

	/**
	 * Set the value of [worker_name] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setWorkerName($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::WORKER_NAME]))
			$this->oldColumnsValues[ControlPanelCommandPeer::WORKER_NAME] = $this->worker_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->worker_name !== $v) {
			$this->worker_name = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::WORKER_NAME;
		}

		return $this;
	} // setWorkerName()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::BATCH_INDEX]))
			$this->oldColumnsValues[ControlPanelCommandPeer::BATCH_INDEX] = $this->batch_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::TYPE]))
			$this->oldColumnsValues[ControlPanelCommandPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [target_type] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setTargetType($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::TARGET_TYPE]))
			$this->oldColumnsValues[ControlPanelCommandPeer::TARGET_TYPE] = $this->target_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->target_type !== $v) {
			$this->target_type = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::TARGET_TYPE;
		}

		return $this;
	} // setTargetType()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::STATUS]))
			$this->oldColumnsValues[ControlPanelCommandPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [cause] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setCause($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::CAUSE]))
			$this->oldColumnsValues[ControlPanelCommandPeer::CAUSE] = $this->cause;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cause !== $v) {
			$this->cause = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::CAUSE;
		}

		return $this;
	} // setCause()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::DESCRIPTION]))
			$this->oldColumnsValues[ControlPanelCommandPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [error_description] column.
	 * 
	 * @param      string $v new value
	 * @return     ControlPanelCommand The current object (for fluent API support)
	 */
	public function setErrorDescription($v)
	{
		if(!isset($this->oldColumnsValues[ControlPanelCommandPeer::ERROR_DESCRIPTION]))
			$this->oldColumnsValues[ControlPanelCommandPeer::ERROR_DESCRIPTION] = $this->error_description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->error_description !== $v) {
			$this->error_description = $v;
			$this->modifiedColumns[] = ControlPanelCommandPeer::ERROR_DESCRIPTION;
		}

		return $this;
	} // setErrorDescription()

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
			$this->created_at = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->created_by = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->updated_at = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->updated_by = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->created_by_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->scheduler_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->scheduler_configured_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->worker_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->worker_configured_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->worker_name = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->batch_index = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->type = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->target_type = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->status = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->cause = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->description = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->error_description = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 18; // 18 = ControlPanelCommandPeer::NUM_COLUMNS - ControlPanelCommandPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ControlPanelCommand object", $e);
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
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		ControlPanelCommandPeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		ControlPanelCommandPeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		ControlPanelCommandPeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				ControlPanelCommandPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ControlPanelCommandPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = ControlPanelCommandPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ControlPanelCommandPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = ControlPanelCommandPeer::doUpdate($this, $con);
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
	 * @return boolean
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


			if (($retval = ControlPanelCommandPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ControlPanelCommandPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCreatedAt();
				break;
			case 2:
				return $this->getCreatedBy();
				break;
			case 3:
				return $this->getUpdatedAt();
				break;
			case 4:
				return $this->getUpdatedBy();
				break;
			case 5:
				return $this->getCreatedById();
				break;
			case 6:
				return $this->getSchedulerId();
				break;
			case 7:
				return $this->getSchedulerConfiguredId();
				break;
			case 8:
				return $this->getWorkerId();
				break;
			case 9:
				return $this->getWorkerConfiguredId();
				break;
			case 10:
				return $this->getWorkerName();
				break;
			case 11:
				return $this->getBatchIndex();
				break;
			case 12:
				return $this->getType();
				break;
			case 13:
				return $this->getTargetType();
				break;
			case 14:
				return $this->getStatus();
				break;
			case 15:
				return $this->getCause();
				break;
			case 16:
				return $this->getDescription();
				break;
			case 17:
				return $this->getErrorDescription();
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
		$keys = ControlPanelCommandPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getCreatedBy(),
			$keys[3] => $this->getUpdatedAt(),
			$keys[4] => $this->getUpdatedBy(),
			$keys[5] => $this->getCreatedById(),
			$keys[6] => $this->getSchedulerId(),
			$keys[7] => $this->getSchedulerConfiguredId(),
			$keys[8] => $this->getWorkerId(),
			$keys[9] => $this->getWorkerConfiguredId(),
			$keys[10] => $this->getWorkerName(),
			$keys[11] => $this->getBatchIndex(),
			$keys[12] => $this->getType(),
			$keys[13] => $this->getTargetType(),
			$keys[14] => $this->getStatus(),
			$keys[15] => $this->getCause(),
			$keys[16] => $this->getDescription(),
			$keys[17] => $this->getErrorDescription(),
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
		$pos = ControlPanelCommandPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCreatedAt($value);
				break;
			case 2:
				$this->setCreatedBy($value);
				break;
			case 3:
				$this->setUpdatedAt($value);
				break;
			case 4:
				$this->setUpdatedBy($value);
				break;
			case 5:
				$this->setCreatedById($value);
				break;
			case 6:
				$this->setSchedulerId($value);
				break;
			case 7:
				$this->setSchedulerConfiguredId($value);
				break;
			case 8:
				$this->setWorkerId($value);
				break;
			case 9:
				$this->setWorkerConfiguredId($value);
				break;
			case 10:
				$this->setWorkerName($value);
				break;
			case 11:
				$this->setBatchIndex($value);
				break;
			case 12:
				$this->setType($value);
				break;
			case 13:
				$this->setTargetType($value);
				break;
			case 14:
				$this->setStatus($value);
				break;
			case 15:
				$this->setCause($value);
				break;
			case 16:
				$this->setDescription($value);
				break;
			case 17:
				$this->setErrorDescription($value);
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
		$keys = ControlPanelCommandPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedBy($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUpdatedAt($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUpdatedBy($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCreatedById($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSchedulerId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSchedulerConfiguredId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setWorkerId($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setWorkerConfiguredId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setWorkerName($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setBatchIndex($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setType($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setTargetType($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStatus($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCause($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setDescription($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setErrorDescription($arr[$keys[17]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ControlPanelCommandPeer::DATABASE_NAME);

		if ($this->isColumnModified(ControlPanelCommandPeer::ID)) $criteria->add(ControlPanelCommandPeer::ID, $this->id);
		if ($this->isColumnModified(ControlPanelCommandPeer::CREATED_AT)) $criteria->add(ControlPanelCommandPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(ControlPanelCommandPeer::CREATED_BY)) $criteria->add(ControlPanelCommandPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(ControlPanelCommandPeer::UPDATED_AT)) $criteria->add(ControlPanelCommandPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(ControlPanelCommandPeer::UPDATED_BY)) $criteria->add(ControlPanelCommandPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(ControlPanelCommandPeer::CREATED_BY_ID)) $criteria->add(ControlPanelCommandPeer::CREATED_BY_ID, $this->created_by_id);
		if ($this->isColumnModified(ControlPanelCommandPeer::SCHEDULER_ID)) $criteria->add(ControlPanelCommandPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID)) $criteria->add(ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID, $this->scheduler_configured_id);
		if ($this->isColumnModified(ControlPanelCommandPeer::WORKER_ID)) $criteria->add(ControlPanelCommandPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(ControlPanelCommandPeer::WORKER_CONFIGURED_ID)) $criteria->add(ControlPanelCommandPeer::WORKER_CONFIGURED_ID, $this->worker_configured_id);
		if ($this->isColumnModified(ControlPanelCommandPeer::WORKER_NAME)) $criteria->add(ControlPanelCommandPeer::WORKER_NAME, $this->worker_name);
		if ($this->isColumnModified(ControlPanelCommandPeer::BATCH_INDEX)) $criteria->add(ControlPanelCommandPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(ControlPanelCommandPeer::TYPE)) $criteria->add(ControlPanelCommandPeer::TYPE, $this->type);
		if ($this->isColumnModified(ControlPanelCommandPeer::TARGET_TYPE)) $criteria->add(ControlPanelCommandPeer::TARGET_TYPE, $this->target_type);
		if ($this->isColumnModified(ControlPanelCommandPeer::STATUS)) $criteria->add(ControlPanelCommandPeer::STATUS, $this->status);
		if ($this->isColumnModified(ControlPanelCommandPeer::CAUSE)) $criteria->add(ControlPanelCommandPeer::CAUSE, $this->cause);
		if ($this->isColumnModified(ControlPanelCommandPeer::DESCRIPTION)) $criteria->add(ControlPanelCommandPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ControlPanelCommandPeer::ERROR_DESCRIPTION)) $criteria->add(ControlPanelCommandPeer::ERROR_DESCRIPTION, $this->error_description);

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
		$criteria = new Criteria(ControlPanelCommandPeer::DATABASE_NAME);

		$criteria->add(ControlPanelCommandPeer::ID, $this->id);
		
		if($this->alreadyInSave)
		{
			if (count($this->modifiedColumns) == 2 && $this->isColumnModified(ControlPanelCommandPeer::UPDATED_AT))
			{
				$theModifiedColumn = null;
				foreach($this->modifiedColumns as $modifiedColumn)
					if($modifiedColumn != ControlPanelCommandPeer::UPDATED_AT)
						$theModifiedColumn = $modifiedColumn;
						
				$atomicColumns = ControlPanelCommandPeer::getAtomicColumns();
				if(in_array($theModifiedColumn, $atomicColumns))
					$criteria->add($theModifiedColumn, $this->getByName($theModifiedColumn, BasePeer::TYPE_COLNAME), Criteria::NOT_EQUAL);
			}
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
	 * @param      object $copyObj An object of ControlPanelCommand (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setUpdatedBy($this->updated_by);

		$copyObj->setCreatedById($this->created_by_id);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setSchedulerConfiguredId($this->scheduler_configured_id);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setWorkerConfiguredId($this->worker_configured_id);

		$copyObj->setWorkerName($this->worker_name);

		$copyObj->setBatchIndex($this->batch_index);

		$copyObj->setType($this->type);

		$copyObj->setTargetType($this->target_type);

		$copyObj->setStatus($this->status);

		$copyObj->setCause($this->cause);

		$copyObj->setDescription($this->description);

		$copyObj->setErrorDescription($this->error_description);


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
	 * @return     ControlPanelCommand Clone of current object.
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
	 * @var     ControlPanelCommand Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      ControlPanelCommand $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(ControlPanelCommand $copiedFrom)
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
	 * @return     ControlPanelCommandPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ControlPanelCommandPeer();
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

} // BaseControlPanelCommand
