<?php

/**
 * Base class that represents a row from the 'conversion_params' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseConversionParams extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ConversionParamsPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the enabled field.
	 * @var        int
	 */
	protected $enabled;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the profile_type field.
	 * @var        string
	 */
	protected $profile_type;

	/**
	 * The value for the profile_type_index field.
	 * @var        int
	 */
	protected $profile_type_index;

	/**
	 * The value for the width field.
	 * @var        int
	 */
	protected $width;

	/**
	 * The value for the height field.
	 * @var        int
	 */
	protected $height;

	/**
	 * The value for the aspect_ratio field.
	 * @var        string
	 */
	protected $aspect_ratio;

	/**
	 * The value for the gop_size field.
	 * @var        int
	 */
	protected $gop_size;

	/**
	 * The value for the bitrate field.
	 * @var        int
	 */
	protected $bitrate;

	/**
	 * The value for the qscale field.
	 * @var        int
	 */
	protected $qscale;

	/**
	 * The value for the file_suffix field.
	 * @var        string
	 */
	protected $file_suffix;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [enabled] column value.
	 * 
	 * @return     int
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the [profile_type] column value.
	 * 
	 * @return     string
	 */
	public function getProfileType()
	{
		return $this->profile_type;
	}

	/**
	 * Get the [profile_type_index] column value.
	 * 
	 * @return     int
	 */
	public function getProfileTypeIndex()
	{
		return $this->profile_type_index;
	}

	/**
	 * Get the [width] column value.
	 * 
	 * @return     int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get the [height] column value.
	 * 
	 * @return     int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get the [aspect_ratio] column value.
	 * 
	 * @return     string
	 */
	public function getAspectRatio()
	{
		return $this->aspect_ratio;
	}

	/**
	 * Get the [gop_size] column value.
	 * 
	 * @return     int
	 */
	public function getGopSize()
	{
		return $this->gop_size;
	}

	/**
	 * Get the [bitrate] column value.
	 * 
	 * @return     int
	 */
	public function getBitrate()
	{
		return $this->bitrate;
	}

	/**
	 * Get the [qscale] column value.
	 * 
	 * @return     int
	 */
	public function getQscale()
	{
		return $this->qscale;
	}

	/**
	 * Get the [file_suffix] column value.
	 * 
	 * @return     string
	 */
	public function getFileSuffix()
	{
		return $this->file_suffix;
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
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::ID]))
			$this->oldColumnsValues[ConversionParamsPeer::ID] = $this->getId();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::PARTNER_ID]))
			$this->oldColumnsValues[ConversionParamsPeer::PARTNER_ID] = $this->getPartnerId();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [enabled] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setEnabled($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::ENABLED]))
			$this->oldColumnsValues[ConversionParamsPeer::ENABLED] = $this->getEnabled();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->enabled !== $v) {
			$this->enabled = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::ENABLED;
		}

		return $this;
	} // setEnabled()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::NAME]))
			$this->oldColumnsValues[ConversionParamsPeer::NAME] = $this->getName();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [profile_type] column.
	 * 
	 * @param      string $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setProfileType($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::PROFILE_TYPE]))
			$this->oldColumnsValues[ConversionParamsPeer::PROFILE_TYPE] = $this->getProfileType();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->profile_type !== $v) {
			$this->profile_type = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::PROFILE_TYPE;
		}

		return $this;
	} // setProfileType()

	/**
	 * Set the value of [profile_type_index] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setProfileTypeIndex($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::PROFILE_TYPE_INDEX]))
			$this->oldColumnsValues[ConversionParamsPeer::PROFILE_TYPE_INDEX] = $this->getProfileTypeIndex();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->profile_type_index !== $v) {
			$this->profile_type_index = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::PROFILE_TYPE_INDEX;
		}

		return $this;
	} // setProfileTypeIndex()

	/**
	 * Set the value of [width] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setWidth($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::WIDTH]))
			$this->oldColumnsValues[ConversionParamsPeer::WIDTH] = $this->getWidth();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->width !== $v) {
			$this->width = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::WIDTH;
		}

		return $this;
	} // setWidth()

	/**
	 * Set the value of [height] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setHeight($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::HEIGHT]))
			$this->oldColumnsValues[ConversionParamsPeer::HEIGHT] = $this->getHeight();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->height !== $v) {
			$this->height = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::HEIGHT;
		}

		return $this;
	} // setHeight()

	/**
	 * Set the value of [aspect_ratio] column.
	 * 
	 * @param      string $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setAspectRatio($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::ASPECT_RATIO]))
			$this->oldColumnsValues[ConversionParamsPeer::ASPECT_RATIO] = $this->getAspectRatio();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->aspect_ratio !== $v) {
			$this->aspect_ratio = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::ASPECT_RATIO;
		}

		return $this;
	} // setAspectRatio()

	/**
	 * Set the value of [gop_size] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setGopSize($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::GOP_SIZE]))
			$this->oldColumnsValues[ConversionParamsPeer::GOP_SIZE] = $this->getGopSize();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->gop_size !== $v) {
			$this->gop_size = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::GOP_SIZE;
		}

		return $this;
	} // setGopSize()

	/**
	 * Set the value of [bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setBitrate($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::BITRATE]))
			$this->oldColumnsValues[ConversionParamsPeer::BITRATE] = $this->getBitrate();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->bitrate !== $v) {
			$this->bitrate = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::BITRATE;
		}

		return $this;
	} // setBitrate()

	/**
	 * Set the value of [qscale] column.
	 * 
	 * @param      int $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setQscale($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::QSCALE]))
			$this->oldColumnsValues[ConversionParamsPeer::QSCALE] = $this->getQscale();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->qscale !== $v) {
			$this->qscale = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::QSCALE;
		}

		return $this;
	} // setQscale()

	/**
	 * Set the value of [file_suffix] column.
	 * 
	 * @param      string $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setFileSuffix($v)
	{
		if(!isset($this->oldColumnsValues[ConversionParamsPeer::FILE_SUFFIX]))
			$this->oldColumnsValues[ConversionParamsPeer::FILE_SUFFIX] = $this->getFileSuffix();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_suffix !== $v) {
			$this->file_suffix = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::FILE_SUFFIX;
		}

		return $this;
	} // setFileSuffix()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     ConversionParams The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = ConversionParamsPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     ConversionParams The current object (for fluent API support)
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
				$this->modifiedColumns[] = ConversionParamsPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     ConversionParams The current object (for fluent API support)
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
				$this->modifiedColumns[] = ConversionParamsPeer::UPDATED_AT;
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
			$this->partner_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->enabled = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->profile_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->profile_type_index = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->width = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->height = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->aspect_ratio = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->gop_size = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->bitrate = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->qscale = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->file_suffix = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->custom_data = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->created_at = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->updated_at = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = ConversionParamsPeer::NUM_COLUMNS - ConversionParamsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ConversionParams object", $e);
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
			$con = Propel::getConnection(ConversionParamsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ConversionParamsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(ConversionParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				ConversionParamsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ConversionParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ConversionParamsPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = ConversionParamsPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ConversionParamsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ConversionParamsPeer::doUpdate($this, $con);
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
		$this->setCustomDataObj();
    	
		return parent::preSave($con);
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
		ConversionParamsPeer::setUseCriteriaFilter(false);
		$this->reload();
		ConversionParamsPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = ConversionParamsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ConversionParamsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPartnerId();
				break;
			case 2:
				return $this->getEnabled();
				break;
			case 3:
				return $this->getName();
				break;
			case 4:
				return $this->getProfileType();
				break;
			case 5:
				return $this->getProfileTypeIndex();
				break;
			case 6:
				return $this->getWidth();
				break;
			case 7:
				return $this->getHeight();
				break;
			case 8:
				return $this->getAspectRatio();
				break;
			case 9:
				return $this->getGopSize();
				break;
			case 10:
				return $this->getBitrate();
				break;
			case 11:
				return $this->getQscale();
				break;
			case 12:
				return $this->getFileSuffix();
				break;
			case 13:
				return $this->getCustomData();
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
		$keys = ConversionParamsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getEnabled(),
			$keys[3] => $this->getName(),
			$keys[4] => $this->getProfileType(),
			$keys[5] => $this->getProfileTypeIndex(),
			$keys[6] => $this->getWidth(),
			$keys[7] => $this->getHeight(),
			$keys[8] => $this->getAspectRatio(),
			$keys[9] => $this->getGopSize(),
			$keys[10] => $this->getBitrate(),
			$keys[11] => $this->getQscale(),
			$keys[12] => $this->getFileSuffix(),
			$keys[13] => $this->getCustomData(),
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
		$pos = ConversionParamsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPartnerId($value);
				break;
			case 2:
				$this->setEnabled($value);
				break;
			case 3:
				$this->setName($value);
				break;
			case 4:
				$this->setProfileType($value);
				break;
			case 5:
				$this->setProfileTypeIndex($value);
				break;
			case 6:
				$this->setWidth($value);
				break;
			case 7:
				$this->setHeight($value);
				break;
			case 8:
				$this->setAspectRatio($value);
				break;
			case 9:
				$this->setGopSize($value);
				break;
			case 10:
				$this->setBitrate($value);
				break;
			case 11:
				$this->setQscale($value);
				break;
			case 12:
				$this->setFileSuffix($value);
				break;
			case 13:
				$this->setCustomData($value);
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
		$keys = ConversionParamsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEnabled($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setProfileType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setProfileTypeIndex($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setWidth($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setHeight($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAspectRatio($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setGopSize($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setBitrate($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setQscale($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setFileSuffix($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setCustomData($arr[$keys[13]]);
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
		$criteria = new Criteria(ConversionParamsPeer::DATABASE_NAME);

		if ($this->isColumnModified(ConversionParamsPeer::ID)) $criteria->add(ConversionParamsPeer::ID, $this->id);
		if ($this->isColumnModified(ConversionParamsPeer::PARTNER_ID)) $criteria->add(ConversionParamsPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(ConversionParamsPeer::ENABLED)) $criteria->add(ConversionParamsPeer::ENABLED, $this->enabled);
		if ($this->isColumnModified(ConversionParamsPeer::NAME)) $criteria->add(ConversionParamsPeer::NAME, $this->name);
		if ($this->isColumnModified(ConversionParamsPeer::PROFILE_TYPE)) $criteria->add(ConversionParamsPeer::PROFILE_TYPE, $this->profile_type);
		if ($this->isColumnModified(ConversionParamsPeer::PROFILE_TYPE_INDEX)) $criteria->add(ConversionParamsPeer::PROFILE_TYPE_INDEX, $this->profile_type_index);
		if ($this->isColumnModified(ConversionParamsPeer::WIDTH)) $criteria->add(ConversionParamsPeer::WIDTH, $this->width);
		if ($this->isColumnModified(ConversionParamsPeer::HEIGHT)) $criteria->add(ConversionParamsPeer::HEIGHT, $this->height);
		if ($this->isColumnModified(ConversionParamsPeer::ASPECT_RATIO)) $criteria->add(ConversionParamsPeer::ASPECT_RATIO, $this->aspect_ratio);
		if ($this->isColumnModified(ConversionParamsPeer::GOP_SIZE)) $criteria->add(ConversionParamsPeer::GOP_SIZE, $this->gop_size);
		if ($this->isColumnModified(ConversionParamsPeer::BITRATE)) $criteria->add(ConversionParamsPeer::BITRATE, $this->bitrate);
		if ($this->isColumnModified(ConversionParamsPeer::QSCALE)) $criteria->add(ConversionParamsPeer::QSCALE, $this->qscale);
		if ($this->isColumnModified(ConversionParamsPeer::FILE_SUFFIX)) $criteria->add(ConversionParamsPeer::FILE_SUFFIX, $this->file_suffix);
		if ($this->isColumnModified(ConversionParamsPeer::CUSTOM_DATA)) $criteria->add(ConversionParamsPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(ConversionParamsPeer::CREATED_AT)) $criteria->add(ConversionParamsPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(ConversionParamsPeer::UPDATED_AT)) $criteria->add(ConversionParamsPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(ConversionParamsPeer::DATABASE_NAME);

		$criteria->add(ConversionParamsPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of ConversionParams (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setEnabled($this->enabled);

		$copyObj->setName($this->name);

		$copyObj->setProfileType($this->profile_type);

		$copyObj->setProfileTypeIndex($this->profile_type_index);

		$copyObj->setWidth($this->width);

		$copyObj->setHeight($this->height);

		$copyObj->setAspectRatio($this->aspect_ratio);

		$copyObj->setGopSize($this->gop_size);

		$copyObj->setBitrate($this->bitrate);

		$copyObj->setQscale($this->qscale);

		$copyObj->setFileSuffix($this->file_suffix);

		$copyObj->setCustomData($this->custom_data);

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
	 * @return     ConversionParams Clone of current object.
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
	 * @var     ConversionParams Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      ConversionParams $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(ConversionParams $copiedFrom)
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
	 * @return     ConversionParamsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ConversionParamsPeer();
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
	
} // BaseConversionParams
