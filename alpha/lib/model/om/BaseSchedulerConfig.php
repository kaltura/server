<?php

/**
 * Base class that represents a row from the 'scheduler_config' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseSchedulerConfig extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SchedulerConfigPeer
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
	 * The value for the command_id field.
	 * @var        int
	 */
	protected $command_id;

	/**
	 * The value for the command_status field.
	 * @var        int
	 */
	protected $command_status;

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
	 * The value for the scheduler_name field.
	 * @var        string
	 */
	protected $scheduler_name;

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
	 * The value for the variable field.
	 * @var        string
	 */
	protected $variable;

	/**
	 * The value for the variable_part field.
	 * @var        string
	 */
	protected $variable_part;

	/**
	 * The value for the value field.
	 * @var        string
	 */
	protected $value;

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
	 * Get the [command_id] column value.
	 * 
	 * @return     int
	 */
	public function getCommandId()
	{
		return $this->command_id;
	}

	/**
	 * Get the [command_status] column value.
	 * 
	 * @return     int
	 */
	public function getCommandStatus()
	{
		return $this->command_status;
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
	 * Get the [scheduler_name] column value.
	 * 
	 * @return     string
	 */
	public function getSchedulerName()
	{
		return $this->scheduler_name;
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
	 * Get the [variable] column value.
	 * 
	 * @return     string
	 */
	public function getVariable()
	{
		return $this->variable;
	}

	/**
	 * Get the [variable_part] column value.
	 * 
	 * @return     string
	 */
	public function getVariablePart()
	{
		return $this->variable_part;
	}

	/**
	 * Get the [value] column value.
	 * 
	 * @return     string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SchedulerConfig The current object (for fluent API support)
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
				$this->modifiedColumns[] = SchedulerConfigPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setCreatedBy($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::CREATED_BY]))
			$this->oldColumnsValues[SchedulerConfigPeer::CREATED_BY] = $this->created_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->created_by !== $v) {
			$this->created_by = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::CREATED_BY;
		}

		return $this;
	} // setCreatedBy()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SchedulerConfig The current object (for fluent API support)
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
				$this->modifiedColumns[] = SchedulerConfigPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [updated_by] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setUpdatedBy($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::UPDATED_BY]))
			$this->oldColumnsValues[SchedulerConfigPeer::UPDATED_BY] = $this->updated_by;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->updated_by !== $v) {
			$this->updated_by = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::UPDATED_BY;
		}

		return $this;
	} // setUpdatedBy()

	/**
	 * Set the value of [command_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setCommandId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::COMMAND_ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::COMMAND_ID] = $this->command_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->command_id !== $v) {
			$this->command_id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::COMMAND_ID;
		}

		return $this;
	} // setCommandId()

	/**
	 * Set the value of [command_status] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setCommandStatus($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::COMMAND_STATUS]))
			$this->oldColumnsValues[SchedulerConfigPeer::COMMAND_STATUS] = $this->command_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->command_status !== $v) {
			$this->command_status = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::COMMAND_STATUS;
		}

		return $this;
	} // setCommandStatus()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [scheduler_configured_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setSchedulerConfiguredId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID] = $this->scheduler_configured_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_configured_id !== $v) {
			$this->scheduler_configured_id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID;
		}

		return $this;
	} // setSchedulerConfiguredId()

	/**
	 * Set the value of [scheduler_name] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setSchedulerName($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_NAME]))
			$this->oldColumnsValues[SchedulerConfigPeer::SCHEDULER_NAME] = $this->scheduler_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->scheduler_name !== $v) {
			$this->scheduler_name = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::SCHEDULER_NAME;
		}

		return $this;
	} // setSchedulerName()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::WORKER_ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [worker_configured_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setWorkerConfiguredId($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::WORKER_CONFIGURED_ID]))
			$this->oldColumnsValues[SchedulerConfigPeer::WORKER_CONFIGURED_ID] = $this->worker_configured_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_configured_id !== $v) {
			$this->worker_configured_id = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::WORKER_CONFIGURED_ID;
		}

		return $this;
	} // setWorkerConfiguredId()

	/**
	 * Set the value of [worker_name] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setWorkerName($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::WORKER_NAME]))
			$this->oldColumnsValues[SchedulerConfigPeer::WORKER_NAME] = $this->worker_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->worker_name !== $v) {
			$this->worker_name = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::WORKER_NAME;
		}

		return $this;
	} // setWorkerName()

	/**
	 * Set the value of [variable] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setVariable($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::VARIABLE]))
			$this->oldColumnsValues[SchedulerConfigPeer::VARIABLE] = $this->variable;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->variable !== $v) {
			$this->variable = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::VARIABLE;
		}

		return $this;
	} // setVariable()

	/**
	 * Set the value of [variable_part] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setVariablePart($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::VARIABLE_PART]))
			$this->oldColumnsValues[SchedulerConfigPeer::VARIABLE_PART] = $this->variable_part;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->variable_part !== $v) {
			$this->variable_part = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::VARIABLE_PART;
		}

		return $this;
	} // setVariablePart()

	/**
	 * Set the value of [value] column.
	 * 
	 * @param      string $v new value
	 * @return     SchedulerConfig The current object (for fluent API support)
	 */
	public function setValue($v)
	{
		if(!isset($this->oldColumnsValues[SchedulerConfigPeer::VALUE]))
			$this->oldColumnsValues[SchedulerConfigPeer::VALUE] = $this->value;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->value !== $v) {
			$this->value = $v;
			$this->modifiedColumns[] = SchedulerConfigPeer::VALUE;
		}

		return $this;
	} // setValue()

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
			$this->command_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->command_status = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->scheduler_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->scheduler_configured_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->scheduler_name = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->worker_id = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->worker_configured_id = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->worker_name = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->variable = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->variable_part = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->value = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = SchedulerConfigPeer::NUM_COLUMNS - SchedulerConfigPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SchedulerConfig object", $e);
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
			$con = Propel::getConnection(SchedulerConfigPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = SchedulerConfigPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(SchedulerConfigPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				SchedulerConfigPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SchedulerConfigPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				SchedulerConfigPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = SchedulerConfigPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SchedulerConfigPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += SchedulerConfigPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

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
		$this->oldColumnsValues = array(); 
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
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		SchedulerConfigPeer::setUseCriteriaFilter(false);
		$this->reload();
		SchedulerConfigPeer::setUseCriteriaFilter(true);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
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
		if($this->isModified())
			$this->setUpdatedAt(time());
		
		$this->tempModifiedColumns = $this->modifiedColumns;
		return true;
	}

	/**
	 * Code to be run after updating the object in database
	 * @param PropelPDO $con
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if($this->isModified())
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->tempModifiedColumns));
			
		$this->tempModifiedColumns = array();
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


			if (($retval = SchedulerConfigPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SchedulerConfigPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCommandId();
				break;
			case 6:
				return $this->getCommandStatus();
				break;
			case 7:
				return $this->getSchedulerId();
				break;
			case 8:
				return $this->getSchedulerConfiguredId();
				break;
			case 9:
				return $this->getSchedulerName();
				break;
			case 10:
				return $this->getWorkerId();
				break;
			case 11:
				return $this->getWorkerConfiguredId();
				break;
			case 12:
				return $this->getWorkerName();
				break;
			case 13:
				return $this->getVariable();
				break;
			case 14:
				return $this->getVariablePart();
				break;
			case 15:
				return $this->getValue();
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
		$keys = SchedulerConfigPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getCreatedBy(),
			$keys[3] => $this->getUpdatedAt(),
			$keys[4] => $this->getUpdatedBy(),
			$keys[5] => $this->getCommandId(),
			$keys[6] => $this->getCommandStatus(),
			$keys[7] => $this->getSchedulerId(),
			$keys[8] => $this->getSchedulerConfiguredId(),
			$keys[9] => $this->getSchedulerName(),
			$keys[10] => $this->getWorkerId(),
			$keys[11] => $this->getWorkerConfiguredId(),
			$keys[12] => $this->getWorkerName(),
			$keys[13] => $this->getVariable(),
			$keys[14] => $this->getVariablePart(),
			$keys[15] => $this->getValue(),
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
		$pos = SchedulerConfigPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCommandId($value);
				break;
			case 6:
				$this->setCommandStatus($value);
				break;
			case 7:
				$this->setSchedulerId($value);
				break;
			case 8:
				$this->setSchedulerConfiguredId($value);
				break;
			case 9:
				$this->setSchedulerName($value);
				break;
			case 10:
				$this->setWorkerId($value);
				break;
			case 11:
				$this->setWorkerConfiguredId($value);
				break;
			case 12:
				$this->setWorkerName($value);
				break;
			case 13:
				$this->setVariable($value);
				break;
			case 14:
				$this->setVariablePart($value);
				break;
			case 15:
				$this->setValue($value);
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
		$keys = SchedulerConfigPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedBy($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUpdatedAt($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUpdatedBy($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCommandId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setCommandStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSchedulerId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSchedulerConfiguredId($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSchedulerName($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setWorkerId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setWorkerConfiguredId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setWorkerName($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setVariable($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setVariablePart($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setValue($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SchedulerConfigPeer::DATABASE_NAME);

		if ($this->isColumnModified(SchedulerConfigPeer::ID)) $criteria->add(SchedulerConfigPeer::ID, $this->id);
		if ($this->isColumnModified(SchedulerConfigPeer::CREATED_AT)) $criteria->add(SchedulerConfigPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(SchedulerConfigPeer::CREATED_BY)) $criteria->add(SchedulerConfigPeer::CREATED_BY, $this->created_by);
		if ($this->isColumnModified(SchedulerConfigPeer::UPDATED_AT)) $criteria->add(SchedulerConfigPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(SchedulerConfigPeer::UPDATED_BY)) $criteria->add(SchedulerConfigPeer::UPDATED_BY, $this->updated_by);
		if ($this->isColumnModified(SchedulerConfigPeer::COMMAND_ID)) $criteria->add(SchedulerConfigPeer::COMMAND_ID, $this->command_id);
		if ($this->isColumnModified(SchedulerConfigPeer::COMMAND_STATUS)) $criteria->add(SchedulerConfigPeer::COMMAND_STATUS, $this->command_status);
		if ($this->isColumnModified(SchedulerConfigPeer::SCHEDULER_ID)) $criteria->add(SchedulerConfigPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID)) $criteria->add(SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID, $this->scheduler_configured_id);
		if ($this->isColumnModified(SchedulerConfigPeer::SCHEDULER_NAME)) $criteria->add(SchedulerConfigPeer::SCHEDULER_NAME, $this->scheduler_name);
		if ($this->isColumnModified(SchedulerConfigPeer::WORKER_ID)) $criteria->add(SchedulerConfigPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(SchedulerConfigPeer::WORKER_CONFIGURED_ID)) $criteria->add(SchedulerConfigPeer::WORKER_CONFIGURED_ID, $this->worker_configured_id);
		if ($this->isColumnModified(SchedulerConfigPeer::WORKER_NAME)) $criteria->add(SchedulerConfigPeer::WORKER_NAME, $this->worker_name);
		if ($this->isColumnModified(SchedulerConfigPeer::VARIABLE)) $criteria->add(SchedulerConfigPeer::VARIABLE, $this->variable);
		if ($this->isColumnModified(SchedulerConfigPeer::VARIABLE_PART)) $criteria->add(SchedulerConfigPeer::VARIABLE_PART, $this->variable_part);
		if ($this->isColumnModified(SchedulerConfigPeer::VALUE)) $criteria->add(SchedulerConfigPeer::VALUE, $this->value);

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
		$criteria = new Criteria(SchedulerConfigPeer::DATABASE_NAME);

		$criteria->add(SchedulerConfigPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SchedulerConfig (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setCreatedBy($this->created_by);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setUpdatedBy($this->updated_by);

		$copyObj->setCommandId($this->command_id);

		$copyObj->setCommandStatus($this->command_status);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setSchedulerConfiguredId($this->scheduler_configured_id);

		$copyObj->setSchedulerName($this->scheduler_name);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setWorkerConfiguredId($this->worker_configured_id);

		$copyObj->setWorkerName($this->worker_name);

		$copyObj->setVariable($this->variable);

		$copyObj->setVariablePart($this->variable_part);

		$copyObj->setValue($this->value);


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
	 * @return     SchedulerConfig Clone of current object.
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
	 * @var     SchedulerConfig Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      SchedulerConfig $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(SchedulerConfig $copiedFrom)
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
	 * @return     SchedulerConfigPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SchedulerConfigPeer();
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

} // BaseSchedulerConfig
