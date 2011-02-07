<?php

/**
 * Base class that represents a row from the 'conversion' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class Baseconversion extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        conversionPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the in_file_name field.
	 * @var        string
	 */
	protected $in_file_name;

	/**
	 * The value for the in_file_ext field.
	 * @var        string
	 */
	protected $in_file_ext;

	/**
	 * The value for the in_file_size field.
	 * @var        int
	 */
	protected $in_file_size;

	/**
	 * The value for the source field.
	 * @var        int
	 */
	protected $source;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the conversion_params field.
	 * @var        string
	 */
	protected $conversion_params;

	/**
	 * The value for the out_file_name field.
	 * @var        string
	 */
	protected $out_file_name;

	/**
	 * The value for the out_file_size field.
	 * @var        int
	 */
	protected $out_file_size;

	/**
	 * The value for the out_file_name_2 field.
	 * @var        string
	 */
	protected $out_file_name_2;

	/**
	 * The value for the out_file_size_2 field.
	 * @var        int
	 */
	protected $out_file_size_2;

	/**
	 * The value for the conversion_time field.
	 * @var        int
	 */
	protected $conversion_time;

	/**
	 * The value for the total_process_time field.
	 * @var        int
	 */
	protected $total_process_time;

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
	 * @var        entry
	 */
	protected $aentry;

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
	 * Get the [entry_id] column value.
	 * 
	 * @return     string
	 */
	public function getEntryId()
	{
		return $this->entry_id;
	}

	/**
	 * Get the [in_file_name] column value.
	 * 
	 * @return     string
	 */
	public function getInFileName()
	{
		return $this->in_file_name;
	}

	/**
	 * Get the [in_file_ext] column value.
	 * 
	 * @return     string
	 */
	public function getInFileExt()
	{
		return $this->in_file_ext;
	}

	/**
	 * Get the [in_file_size] column value.
	 * 
	 * @return     int
	 */
	public function getInFileSize()
	{
		return $this->in_file_size;
	}

	/**
	 * Get the [source] column value.
	 * 
	 * @return     int
	 */
	public function getSource()
	{
		return $this->source;
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
	 * Get the [conversion_params] column value.
	 * 
	 * @return     string
	 */
	public function getConversionParams()
	{
		return $this->conversion_params;
	}

	/**
	 * Get the [out_file_name] column value.
	 * 
	 * @return     string
	 */
	public function getOutFileName()
	{
		return $this->out_file_name;
	}

	/**
	 * Get the [out_file_size] column value.
	 * 
	 * @return     int
	 */
	public function getOutFileSize()
	{
		return $this->out_file_size;
	}

	/**
	 * Get the [out_file_name_2] column value.
	 * 
	 * @return     string
	 */
	public function getOutFileName2()
	{
		return $this->out_file_name_2;
	}

	/**
	 * Get the [out_file_size_2] column value.
	 * 
	 * @return     int
	 */
	public function getOutFileSize2()
	{
		return $this->out_file_size_2;
	}

	/**
	 * Get the [conversion_time] column value.
	 * 
	 * @return     int
	 */
	public function getConversionTime()
	{
		return $this->conversion_time;
	}

	/**
	 * Get the [total_process_time] column value.
	 * 
	 * @return     int
	 */
	public function getTotalProcessTime()
	{
		return $this->total_process_time;
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::ID]))
			$this->oldColumnsValues[conversionPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = conversionPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::ENTRY_ID]))
			$this->oldColumnsValues[conversionPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = conversionPeer::ENTRY_ID;
		}

		if ($this->aentry !== null && $this->aentry->getId() !== $v) {
			$this->aentry = null;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [in_file_name] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setInFileName($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::IN_FILE_NAME]))
			$this->oldColumnsValues[conversionPeer::IN_FILE_NAME] = $this->in_file_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->in_file_name !== $v) {
			$this->in_file_name = $v;
			$this->modifiedColumns[] = conversionPeer::IN_FILE_NAME;
		}

		return $this;
	} // setInFileName()

	/**
	 * Set the value of [in_file_ext] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setInFileExt($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::IN_FILE_EXT]))
			$this->oldColumnsValues[conversionPeer::IN_FILE_EXT] = $this->in_file_ext;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->in_file_ext !== $v) {
			$this->in_file_ext = $v;
			$this->modifiedColumns[] = conversionPeer::IN_FILE_EXT;
		}

		return $this;
	} // setInFileExt()

	/**
	 * Set the value of [in_file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setInFileSize($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::IN_FILE_SIZE]))
			$this->oldColumnsValues[conversionPeer::IN_FILE_SIZE] = $this->in_file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->in_file_size !== $v) {
			$this->in_file_size = $v;
			$this->modifiedColumns[] = conversionPeer::IN_FILE_SIZE;
		}

		return $this;
	} // setInFileSize()

	/**
	 * Set the value of [source] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setSource($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::SOURCE]))
			$this->oldColumnsValues[conversionPeer::SOURCE] = $this->source;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->source !== $v) {
			$this->source = $v;
			$this->modifiedColumns[] = conversionPeer::SOURCE;
		}

		return $this;
	} // setSource()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::STATUS]))
			$this->oldColumnsValues[conversionPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = conversionPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [conversion_params] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setConversionParams($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::CONVERSION_PARAMS]))
			$this->oldColumnsValues[conversionPeer::CONVERSION_PARAMS] = $this->conversion_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conversion_params !== $v) {
			$this->conversion_params = $v;
			$this->modifiedColumns[] = conversionPeer::CONVERSION_PARAMS;
		}

		return $this;
	} // setConversionParams()

	/**
	 * Set the value of [out_file_name] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setOutFileName($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::OUT_FILE_NAME]))
			$this->oldColumnsValues[conversionPeer::OUT_FILE_NAME] = $this->out_file_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->out_file_name !== $v) {
			$this->out_file_name = $v;
			$this->modifiedColumns[] = conversionPeer::OUT_FILE_NAME;
		}

		return $this;
	} // setOutFileName()

	/**
	 * Set the value of [out_file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setOutFileSize($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::OUT_FILE_SIZE]))
			$this->oldColumnsValues[conversionPeer::OUT_FILE_SIZE] = $this->out_file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->out_file_size !== $v) {
			$this->out_file_size = $v;
			$this->modifiedColumns[] = conversionPeer::OUT_FILE_SIZE;
		}

		return $this;
	} // setOutFileSize()

	/**
	 * Set the value of [out_file_name_2] column.
	 * 
	 * @param      string $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setOutFileName2($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::OUT_FILE_NAME_2]))
			$this->oldColumnsValues[conversionPeer::OUT_FILE_NAME_2] = $this->out_file_name_2;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->out_file_name_2 !== $v) {
			$this->out_file_name_2 = $v;
			$this->modifiedColumns[] = conversionPeer::OUT_FILE_NAME_2;
		}

		return $this;
	} // setOutFileName2()

	/**
	 * Set the value of [out_file_size_2] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setOutFileSize2($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::OUT_FILE_SIZE_2]))
			$this->oldColumnsValues[conversionPeer::OUT_FILE_SIZE_2] = $this->out_file_size_2;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->out_file_size_2 !== $v) {
			$this->out_file_size_2 = $v;
			$this->modifiedColumns[] = conversionPeer::OUT_FILE_SIZE_2;
		}

		return $this;
	} // setOutFileSize2()

	/**
	 * Set the value of [conversion_time] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setConversionTime($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::CONVERSION_TIME]))
			$this->oldColumnsValues[conversionPeer::CONVERSION_TIME] = $this->conversion_time;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->conversion_time !== $v) {
			$this->conversion_time = $v;
			$this->modifiedColumns[] = conversionPeer::CONVERSION_TIME;
		}

		return $this;
	} // setConversionTime()

	/**
	 * Set the value of [total_process_time] column.
	 * 
	 * @param      int $v new value
	 * @return     conversion The current object (for fluent API support)
	 */
	public function setTotalProcessTime($v)
	{
		if(!isset($this->oldColumnsValues[conversionPeer::TOTAL_PROCESS_TIME]))
			$this->oldColumnsValues[conversionPeer::TOTAL_PROCESS_TIME] = $this->total_process_time;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->total_process_time !== $v) {
			$this->total_process_time = $v;
			$this->modifiedColumns[] = conversionPeer::TOTAL_PROCESS_TIME;
		}

		return $this;
	} // setTotalProcessTime()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     conversion The current object (for fluent API support)
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
				$this->modifiedColumns[] = conversionPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     conversion The current object (for fluent API support)
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
				$this->modifiedColumns[] = conversionPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

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
			$this->entry_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->in_file_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->in_file_ext = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->in_file_size = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->source = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->status = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->conversion_params = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->out_file_name = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->out_file_size = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->out_file_name_2 = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->out_file_size_2 = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->conversion_time = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->total_process_time = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->created_at = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->updated_at = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = conversionPeer::NUM_COLUMNS - conversionPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating conversion object", $e);
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

		if ($this->aentry !== null && $this->entry_id !== $this->aentry->getId()) {
			$this->aentry = null;
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
			$con = Propel::getConnection(conversionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = conversionPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aentry = null;
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
			$con = Propel::getConnection(conversionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				conversionPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(conversionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				conversionPeer::addInstanceToPool($this);
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

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aentry !== null) {
				if ($this->aentry->isModified() || $this->aentry->isNew()) {
					$affectedRows += $this->aentry->save($con);
				}
				$this->setentry($this->aentry);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = conversionPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = conversionPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += conversionPeer::doUpdate($this, $con);
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
		conversionPeer::setUseCriteriaFilter(false);
		$this->reload();
		conversionPeer::setUseCriteriaFilter(true);
		
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aentry !== null) {
				if (!$this->aentry->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aentry->getValidationFailures());
				}
			}


			if (($retval = conversionPeer::doValidate($this, $columns)) !== true) {
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
		$pos = conversionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEntryId();
				break;
			case 2:
				return $this->getInFileName();
				break;
			case 3:
				return $this->getInFileExt();
				break;
			case 4:
				return $this->getInFileSize();
				break;
			case 5:
				return $this->getSource();
				break;
			case 6:
				return $this->getStatus();
				break;
			case 7:
				return $this->getConversionParams();
				break;
			case 8:
				return $this->getOutFileName();
				break;
			case 9:
				return $this->getOutFileSize();
				break;
			case 10:
				return $this->getOutFileName2();
				break;
			case 11:
				return $this->getOutFileSize2();
				break;
			case 12:
				return $this->getConversionTime();
				break;
			case 13:
				return $this->getTotalProcessTime();
				break;
			case 14:
				return $this->getCreatedAt();
				break;
			case 15:
				return $this->getUpdatedAt();
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
		$keys = conversionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getEntryId(),
			$keys[2] => $this->getInFileName(),
			$keys[3] => $this->getInFileExt(),
			$keys[4] => $this->getInFileSize(),
			$keys[5] => $this->getSource(),
			$keys[6] => $this->getStatus(),
			$keys[7] => $this->getConversionParams(),
			$keys[8] => $this->getOutFileName(),
			$keys[9] => $this->getOutFileSize(),
			$keys[10] => $this->getOutFileName2(),
			$keys[11] => $this->getOutFileSize2(),
			$keys[12] => $this->getConversionTime(),
			$keys[13] => $this->getTotalProcessTime(),
			$keys[14] => $this->getCreatedAt(),
			$keys[15] => $this->getUpdatedAt(),
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
		$pos = conversionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEntryId($value);
				break;
			case 2:
				$this->setInFileName($value);
				break;
			case 3:
				$this->setInFileExt($value);
				break;
			case 4:
				$this->setInFileSize($value);
				break;
			case 5:
				$this->setSource($value);
				break;
			case 6:
				$this->setStatus($value);
				break;
			case 7:
				$this->setConversionParams($value);
				break;
			case 8:
				$this->setOutFileName($value);
				break;
			case 9:
				$this->setOutFileSize($value);
				break;
			case 10:
				$this->setOutFileName2($value);
				break;
			case 11:
				$this->setOutFileSize2($value);
				break;
			case 12:
				$this->setConversionTime($value);
				break;
			case 13:
				$this->setTotalProcessTime($value);
				break;
			case 14:
				$this->setCreatedAt($value);
				break;
			case 15:
				$this->setUpdatedAt($value);
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
		$keys = conversionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setEntryId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setInFileName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setInFileExt($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setInFileSize($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSource($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setConversionParams($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setOutFileName($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setOutFileSize($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setOutFileName2($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setOutFileSize2($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setConversionTime($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setTotalProcessTime($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCreatedAt($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setUpdatedAt($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(conversionPeer::DATABASE_NAME);

		if ($this->isColumnModified(conversionPeer::ID)) $criteria->add(conversionPeer::ID, $this->id);
		if ($this->isColumnModified(conversionPeer::ENTRY_ID)) $criteria->add(conversionPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(conversionPeer::IN_FILE_NAME)) $criteria->add(conversionPeer::IN_FILE_NAME, $this->in_file_name);
		if ($this->isColumnModified(conversionPeer::IN_FILE_EXT)) $criteria->add(conversionPeer::IN_FILE_EXT, $this->in_file_ext);
		if ($this->isColumnModified(conversionPeer::IN_FILE_SIZE)) $criteria->add(conversionPeer::IN_FILE_SIZE, $this->in_file_size);
		if ($this->isColumnModified(conversionPeer::SOURCE)) $criteria->add(conversionPeer::SOURCE, $this->source);
		if ($this->isColumnModified(conversionPeer::STATUS)) $criteria->add(conversionPeer::STATUS, $this->status);
		if ($this->isColumnModified(conversionPeer::CONVERSION_PARAMS)) $criteria->add(conversionPeer::CONVERSION_PARAMS, $this->conversion_params);
		if ($this->isColumnModified(conversionPeer::OUT_FILE_NAME)) $criteria->add(conversionPeer::OUT_FILE_NAME, $this->out_file_name);
		if ($this->isColumnModified(conversionPeer::OUT_FILE_SIZE)) $criteria->add(conversionPeer::OUT_FILE_SIZE, $this->out_file_size);
		if ($this->isColumnModified(conversionPeer::OUT_FILE_NAME_2)) $criteria->add(conversionPeer::OUT_FILE_NAME_2, $this->out_file_name_2);
		if ($this->isColumnModified(conversionPeer::OUT_FILE_SIZE_2)) $criteria->add(conversionPeer::OUT_FILE_SIZE_2, $this->out_file_size_2);
		if ($this->isColumnModified(conversionPeer::CONVERSION_TIME)) $criteria->add(conversionPeer::CONVERSION_TIME, $this->conversion_time);
		if ($this->isColumnModified(conversionPeer::TOTAL_PROCESS_TIME)) $criteria->add(conversionPeer::TOTAL_PROCESS_TIME, $this->total_process_time);
		if ($this->isColumnModified(conversionPeer::CREATED_AT)) $criteria->add(conversionPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(conversionPeer::UPDATED_AT)) $criteria->add(conversionPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(conversionPeer::DATABASE_NAME);

		$criteria->add(conversionPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of conversion (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setInFileName($this->in_file_name);

		$copyObj->setInFileExt($this->in_file_ext);

		$copyObj->setInFileSize($this->in_file_size);

		$copyObj->setSource($this->source);

		$copyObj->setStatus($this->status);

		$copyObj->setConversionParams($this->conversion_params);

		$copyObj->setOutFileName($this->out_file_name);

		$copyObj->setOutFileSize($this->out_file_size);

		$copyObj->setOutFileName2($this->out_file_name_2);

		$copyObj->setOutFileSize2($this->out_file_size_2);

		$copyObj->setConversionTime($this->conversion_time);

		$copyObj->setTotalProcessTime($this->total_process_time);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);


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
	 * @return     conversion Clone of current object.
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
	 * @var     conversion Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      conversion $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(conversion $copiedFrom)
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
	 * @return     conversionPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new conversionPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a entry object.
	 *
	 * @param      entry $v
	 * @return     conversion The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setentry(entry $v = null)
	{
		if ($v === null) {
			$this->setEntryId(NULL);
		} else {
			$this->setEntryId($v->getId());
		}

		$this->aentry = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the entry object, it will not be re-added.
		if ($v !== null) {
			$v->addconversion($this);
		}

		return $this;
	}


	/**
	 * Get the associated entry object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     entry The associated entry object.
	 * @throws     PropelException
	 */
	public function getentry(PropelPDO $con = null)
	{
		if ($this->aentry === null && (($this->entry_id !== "" && $this->entry_id !== null))) {
			$this->aentry = entryPeer::retrieveByPk($this->entry_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aentry->addconversions($this);
			 */
		}
		return $this->aentry;
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

			$this->aentry = null;
	}

} // Baseconversion
