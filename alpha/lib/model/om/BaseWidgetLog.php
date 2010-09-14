<?php

/**
 * Base class that represents a row from the 'widget_log' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseWidgetLog extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        WidgetLogPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the kshow_id field.
	 * @var        string
	 */
	protected $kshow_id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the kmedia_type field.
	 * @var        int
	 */
	protected $kmedia_type;

	/**
	 * The value for the widget_type field.
	 * @var        string
	 */
	protected $widget_type;

	/**
	 * The value for the referer field.
	 * @var        string
	 */
	protected $referer;

	/**
	 * The value for the views field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the ip1 field.
	 * @var        int
	 */
	protected $ip1;

	/**
	 * The value for the ip1_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $ip1_count;

	/**
	 * The value for the ip2 field.
	 * @var        int
	 */
	protected $ip2;

	/**
	 * The value for the ip2_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $ip2_count;

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
	 * The value for the plays field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $plays;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->views = 0;
		$this->ip1_count = 0;
		$this->ip2_count = 0;
		$this->plays = 0;
		$this->partner_id = 0;
		$this->subp_id = 0;
	}

	/**
	 * Initializes internal state of BaseWidgetLog object.
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
	 * Get the [kshow_id] column value.
	 * 
	 * @return     string
	 */
	public function getKshowId()
	{
		return $this->kshow_id;
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
	 * Get the [kmedia_type] column value.
	 * 
	 * @return     int
	 */
	public function getKmediaType()
	{
		return $this->kmedia_type;
	}

	/**
	 * Get the [widget_type] column value.
	 * 
	 * @return     string
	 */
	public function getWidgetType()
	{
		return $this->widget_type;
	}

	/**
	 * Get the [referer] column value.
	 * 
	 * @return     string
	 */
	public function getReferer()
	{
		return $this->referer;
	}

	/**
	 * Get the [views] column value.
	 * 
	 * @return     int
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * Get the [ip1] column value.
	 * 
	 * @return     int
	 */
	public function getIp1()
	{
		return $this->ip1;
	}

	/**
	 * Get the [ip1_count] column value.
	 * 
	 * @return     int
	 */
	public function getIp1Count()
	{
		return $this->ip1_count;
	}

	/**
	 * Get the [ip2] column value.
	 * 
	 * @return     int
	 */
	public function getIp2()
	{
		return $this->ip2;
	}

	/**
	 * Get the [ip2_count] column value.
	 * 
	 * @return     int
	 */
	public function getIp2Count()
	{
		return $this->ip2_count;
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
	 * Get the [plays] column value.
	 * 
	 * @return     int
	 */
	public function getPlays()
	{
		return $this->plays;
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = WidgetLogPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [kshow_id] column.
	 * 
	 * @param      string $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setKshowId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->kshow_id !== $v) {
			$this->kshow_id = $v;
			$this->modifiedColumns[] = WidgetLogPeer::KSHOW_ID;
		}

		return $this;
	} // setKshowId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = WidgetLogPeer::ENTRY_ID;
		}

		if ($this->aentry !== null && $this->aentry->getId() !== $v) {
			$this->aentry = null;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [kmedia_type] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setKmediaType($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kmedia_type !== $v) {
			$this->kmedia_type = $v;
			$this->modifiedColumns[] = WidgetLogPeer::KMEDIA_TYPE;
		}

		return $this;
	} // setKmediaType()

	/**
	 * Set the value of [widget_type] column.
	 * 
	 * @param      string $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setWidgetType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->widget_type !== $v) {
			$this->widget_type = $v;
			$this->modifiedColumns[] = WidgetLogPeer::WIDGET_TYPE;
		}

		return $this;
	} // setWidgetType()

	/**
	 * Set the value of [referer] column.
	 * 
	 * @param      string $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setReferer($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->referer !== $v) {
			$this->referer = $v;
			$this->modifiedColumns[] = WidgetLogPeer::REFERER;
		}

		return $this;
	} // setReferer()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v || $this->isNew()) {
			$this->views = $v;
			$this->modifiedColumns[] = WidgetLogPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [ip1] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setIp1($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ip1 !== $v) {
			$this->ip1 = $v;
			$this->modifiedColumns[] = WidgetLogPeer::IP1;
		}

		return $this;
	} // setIp1()

	/**
	 * Set the value of [ip1_count] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setIp1Count($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ip1_count !== $v || $this->isNew()) {
			$this->ip1_count = $v;
			$this->modifiedColumns[] = WidgetLogPeer::IP1_COUNT;
		}

		return $this;
	} // setIp1Count()

	/**
	 * Set the value of [ip2] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setIp2($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ip2 !== $v) {
			$this->ip2 = $v;
			$this->modifiedColumns[] = WidgetLogPeer::IP2;
		}

		return $this;
	} // setIp2()

	/**
	 * Set the value of [ip2_count] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setIp2Count($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ip2_count !== $v || $this->isNew()) {
			$this->ip2_count = $v;
			$this->modifiedColumns[] = WidgetLogPeer::IP2_COUNT;
		}

		return $this;
	} // setIp2Count()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     WidgetLog The current object (for fluent API support)
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
				$this->modifiedColumns[] = WidgetLogPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     WidgetLog The current object (for fluent API support)
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
				$this->modifiedColumns[] = WidgetLogPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [plays] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setPlays($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plays !== $v || $this->isNew()) {
			$this->plays = $v;
			$this->modifiedColumns[] = WidgetLogPeer::PLAYS;
		}

		return $this;
	} // setPlays()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = WidgetLogPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [subp_id] column.
	 * 
	 * @param      int $v new value
	 * @return     WidgetLog The current object (for fluent API support)
	 */
	public function setSubpId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subp_id !== $v || $this->isNew()) {
			$this->subp_id = $v;
			$this->modifiedColumns[] = WidgetLogPeer::SUBP_ID;
		}

		return $this;
	} // setSubpId()

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
			if ($this->views !== 0) {
				return false;
			}

			if ($this->ip1_count !== 0) {
				return false;
			}

			if ($this->ip2_count !== 0) {
				return false;
			}

			if ($this->plays !== 0) {
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
			$this->kshow_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->entry_id = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->kmedia_type = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->widget_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->referer = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->views = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->ip1 = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->ip1_count = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->ip2 = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->ip2_count = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->plays = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->partner_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->subp_id = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 16; // 16 = WidgetLogPeer::NUM_COLUMNS - WidgetLogPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating WidgetLog object", $e);
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
			$con = Propel::getConnection(WidgetLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = WidgetLogPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(WidgetLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				WidgetLogPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(WidgetLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				WidgetLogPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = WidgetLogPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = WidgetLogPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += WidgetLogPeer::doUpdate($this, $con);
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
		WidgetLogPeer::setUseCriteriaFilter(false);
		$this->reload();
		WidgetLogPeer::setUseCriteriaFilter(true);
		
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aentry !== null) {
				if (!$this->aentry->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aentry->getValidationFailures());
				}
			}


			if (($retval = WidgetLogPeer::doValidate($this, $columns)) !== true) {
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
		$pos = WidgetLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getKshowId();
				break;
			case 2:
				return $this->getEntryId();
				break;
			case 3:
				return $this->getKmediaType();
				break;
			case 4:
				return $this->getWidgetType();
				break;
			case 5:
				return $this->getReferer();
				break;
			case 6:
				return $this->getViews();
				break;
			case 7:
				return $this->getIp1();
				break;
			case 8:
				return $this->getIp1Count();
				break;
			case 9:
				return $this->getIp2();
				break;
			case 10:
				return $this->getIp2Count();
				break;
			case 11:
				return $this->getCreatedAt();
				break;
			case 12:
				return $this->getUpdatedAt();
				break;
			case 13:
				return $this->getPlays();
				break;
			case 14:
				return $this->getPartnerId();
				break;
			case 15:
				return $this->getSubpId();
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
		$keys = WidgetLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getKshowId(),
			$keys[2] => $this->getEntryId(),
			$keys[3] => $this->getKmediaType(),
			$keys[4] => $this->getWidgetType(),
			$keys[5] => $this->getReferer(),
			$keys[6] => $this->getViews(),
			$keys[7] => $this->getIp1(),
			$keys[8] => $this->getIp1Count(),
			$keys[9] => $this->getIp2(),
			$keys[10] => $this->getIp2Count(),
			$keys[11] => $this->getCreatedAt(),
			$keys[12] => $this->getUpdatedAt(),
			$keys[13] => $this->getPlays(),
			$keys[14] => $this->getPartnerId(),
			$keys[15] => $this->getSubpId(),
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
		$pos = WidgetLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setKshowId($value);
				break;
			case 2:
				$this->setEntryId($value);
				break;
			case 3:
				$this->setKmediaType($value);
				break;
			case 4:
				$this->setWidgetType($value);
				break;
			case 5:
				$this->setReferer($value);
				break;
			case 6:
				$this->setViews($value);
				break;
			case 7:
				$this->setIp1($value);
				break;
			case 8:
				$this->setIp1Count($value);
				break;
			case 9:
				$this->setIp2($value);
				break;
			case 10:
				$this->setIp2Count($value);
				break;
			case 11:
				$this->setCreatedAt($value);
				break;
			case 12:
				$this->setUpdatedAt($value);
				break;
			case 13:
				$this->setPlays($value);
				break;
			case 14:
				$this->setPartnerId($value);
				break;
			case 15:
				$this->setSubpId($value);
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
		$keys = WidgetLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setKshowId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEntryId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setKmediaType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setWidgetType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setReferer($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setViews($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setIp1($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setIp1Count($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setIp2($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setIp2Count($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setPlays($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPartnerId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setSubpId($arr[$keys[15]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(WidgetLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(WidgetLogPeer::ID)) $criteria->add(WidgetLogPeer::ID, $this->id);
		if ($this->isColumnModified(WidgetLogPeer::KSHOW_ID)) $criteria->add(WidgetLogPeer::KSHOW_ID, $this->kshow_id);
		if ($this->isColumnModified(WidgetLogPeer::ENTRY_ID)) $criteria->add(WidgetLogPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(WidgetLogPeer::KMEDIA_TYPE)) $criteria->add(WidgetLogPeer::KMEDIA_TYPE, $this->kmedia_type);
		if ($this->isColumnModified(WidgetLogPeer::WIDGET_TYPE)) $criteria->add(WidgetLogPeer::WIDGET_TYPE, $this->widget_type);
		if ($this->isColumnModified(WidgetLogPeer::REFERER)) $criteria->add(WidgetLogPeer::REFERER, $this->referer);
		if ($this->isColumnModified(WidgetLogPeer::VIEWS)) $criteria->add(WidgetLogPeer::VIEWS, $this->views);
		if ($this->isColumnModified(WidgetLogPeer::IP1)) $criteria->add(WidgetLogPeer::IP1, $this->ip1);
		if ($this->isColumnModified(WidgetLogPeer::IP1_COUNT)) $criteria->add(WidgetLogPeer::IP1_COUNT, $this->ip1_count);
		if ($this->isColumnModified(WidgetLogPeer::IP2)) $criteria->add(WidgetLogPeer::IP2, $this->ip2);
		if ($this->isColumnModified(WidgetLogPeer::IP2_COUNT)) $criteria->add(WidgetLogPeer::IP2_COUNT, $this->ip2_count);
		if ($this->isColumnModified(WidgetLogPeer::CREATED_AT)) $criteria->add(WidgetLogPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(WidgetLogPeer::UPDATED_AT)) $criteria->add(WidgetLogPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(WidgetLogPeer::PLAYS)) $criteria->add(WidgetLogPeer::PLAYS, $this->plays);
		if ($this->isColumnModified(WidgetLogPeer::PARTNER_ID)) $criteria->add(WidgetLogPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(WidgetLogPeer::SUBP_ID)) $criteria->add(WidgetLogPeer::SUBP_ID, $this->subp_id);

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
		$criteria = new Criteria(WidgetLogPeer::DATABASE_NAME);

		$criteria->add(WidgetLogPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of WidgetLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setKshowId($this->kshow_id);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setKmediaType($this->kmedia_type);

		$copyObj->setWidgetType($this->widget_type);

		$copyObj->setReferer($this->referer);

		$copyObj->setViews($this->views);

		$copyObj->setIp1($this->ip1);

		$copyObj->setIp1Count($this->ip1_count);

		$copyObj->setIp2($this->ip2);

		$copyObj->setIp2Count($this->ip2_count);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPlays($this->plays);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setSubpId($this->subp_id);


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
	 * @return     WidgetLog Clone of current object.
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
	 * @var     WidgetLog Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      WidgetLog $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(WidgetLog $copiedFrom)
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
	 * @return     WidgetLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new WidgetLogPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a entry object.
	 *
	 * @param      entry $v
	 * @return     WidgetLog The current object (for fluent API support)
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
			$v->addWidgetLog($this);
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
			   $this->aentry->addWidgetLogs($this);
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

} // BaseWidgetLog
