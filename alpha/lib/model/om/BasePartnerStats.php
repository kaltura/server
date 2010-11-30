<?php

/**
 * Base class that represents a row from the 'partner_stats' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BasePartnerStats extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PartnerStatsPeer
	 */
	protected static $peer;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the views field.
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the plays field.
	 * @var        int
	 */
	protected $plays;

	/**
	 * The value for the videos field.
	 * @var        int
	 */
	protected $videos;

	/**
	 * The value for the audios field.
	 * @var        int
	 */
	protected $audios;

	/**
	 * The value for the images field.
	 * @var        int
	 */
	protected $images;

	/**
	 * The value for the entries field.
	 * @var        int
	 */
	protected $entries;

	/**
	 * The value for the users_1 field.
	 * @var        int
	 */
	protected $users_1;

	/**
	 * The value for the users_2 field.
	 * @var        int
	 */
	protected $users_2;

	/**
	 * The value for the rc_1 field.
	 * @var        int
	 */
	protected $rc_1;

	/**
	 * The value for the rc_2 field.
	 * @var        int
	 */
	protected $rc_2;

	/**
	 * The value for the kshows_1 field.
	 * @var        int
	 */
	protected $kshows_1;

	/**
	 * The value for the kshows_2 field.
	 * @var        int
	 */
	protected $kshows_2;

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
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the widgets field.
	 * @var        int
	 */
	protected $widgets;

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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
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
	 * Get the [plays] column value.
	 * 
	 * @return     int
	 */
	public function getPlays()
	{
		return $this->plays;
	}

	/**
	 * Get the [videos] column value.
	 * 
	 * @return     int
	 */
	public function getVideos()
	{
		return $this->videos;
	}

	/**
	 * Get the [audios] column value.
	 * 
	 * @return     int
	 */
	public function getAudios()
	{
		return $this->audios;
	}

	/**
	 * Get the [images] column value.
	 * 
	 * @return     int
	 */
	public function getImages()
	{
		return $this->images;
	}

	/**
	 * Get the [entries] column value.
	 * 
	 * @return     int
	 */
	public function getEntries()
	{
		return $this->entries;
	}

	/**
	 * Get the [users_1] column value.
	 * 
	 * @return     int
	 */
	public function getUsers1()
	{
		return $this->users_1;
	}

	/**
	 * Get the [users_2] column value.
	 * 
	 * @return     int
	 */
	public function getUsers2()
	{
		return $this->users_2;
	}

	/**
	 * Get the [rc_1] column value.
	 * 
	 * @return     int
	 */
	public function getRc1()
	{
		return $this->rc_1;
	}

	/**
	 * Get the [rc_2] column value.
	 * 
	 * @return     int
	 */
	public function getRc2()
	{
		return $this->rc_2;
	}

	/**
	 * Get the [kshows_1] column value.
	 * 
	 * @return     int
	 */
	public function getKshows1()
	{
		return $this->kshows_1;
	}

	/**
	 * Get the [kshows_2] column value.
	 * 
	 * @return     int
	 */
	public function getKshows2()
	{
		return $this->kshows_2;
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
	 * Get the [custom_data] column value.
	 * 
	 * @return     string
	 */
	public function getCustomData()
	{
		return $this->custom_data;
	}

	/**
	 * Get the [widgets] column value.
	 * 
	 * @return     int
	 */
	public function getWidgets()
	{
		return $this->widgets;
	}

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::PARTNER_ID]))
			$this->oldColumnsValues[PartnerStatsPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::VIEWS]))
			$this->oldColumnsValues[PartnerStatsPeer::VIEWS] = $this->views;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v) {
			$this->views = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [plays] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setPlays($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::PLAYS]))
			$this->oldColumnsValues[PartnerStatsPeer::PLAYS] = $this->plays;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plays !== $v) {
			$this->plays = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::PLAYS;
		}

		return $this;
	} // setPlays()

	/**
	 * Set the value of [videos] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setVideos($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::VIDEOS]))
			$this->oldColumnsValues[PartnerStatsPeer::VIDEOS] = $this->videos;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->videos !== $v) {
			$this->videos = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::VIDEOS;
		}

		return $this;
	} // setVideos()

	/**
	 * Set the value of [audios] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setAudios($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::AUDIOS]))
			$this->oldColumnsValues[PartnerStatsPeer::AUDIOS] = $this->audios;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audios !== $v) {
			$this->audios = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::AUDIOS;
		}

		return $this;
	} // setAudios()

	/**
	 * Set the value of [images] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setImages($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::IMAGES]))
			$this->oldColumnsValues[PartnerStatsPeer::IMAGES] = $this->images;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->images !== $v) {
			$this->images = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::IMAGES;
		}

		return $this;
	} // setImages()

	/**
	 * Set the value of [entries] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setEntries($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::ENTRIES]))
			$this->oldColumnsValues[PartnerStatsPeer::ENTRIES] = $this->entries;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->entries !== $v) {
			$this->entries = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::ENTRIES;
		}

		return $this;
	} // setEntries()

	/**
	 * Set the value of [users_1] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setUsers1($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::USERS_1]))
			$this->oldColumnsValues[PartnerStatsPeer::USERS_1] = $this->users_1;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->users_1 !== $v) {
			$this->users_1 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::USERS_1;
		}

		return $this;
	} // setUsers1()

	/**
	 * Set the value of [users_2] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setUsers2($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::USERS_2]))
			$this->oldColumnsValues[PartnerStatsPeer::USERS_2] = $this->users_2;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->users_2 !== $v) {
			$this->users_2 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::USERS_2;
		}

		return $this;
	} // setUsers2()

	/**
	 * Set the value of [rc_1] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setRc1($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::RC_1]))
			$this->oldColumnsValues[PartnerStatsPeer::RC_1] = $this->rc_1;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rc_1 !== $v) {
			$this->rc_1 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::RC_1;
		}

		return $this;
	} // setRc1()

	/**
	 * Set the value of [rc_2] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setRc2($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::RC_2]))
			$this->oldColumnsValues[PartnerStatsPeer::RC_2] = $this->rc_2;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rc_2 !== $v) {
			$this->rc_2 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::RC_2;
		}

		return $this;
	} // setRc2()

	/**
	 * Set the value of [kshows_1] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setKshows1($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::KSHOWS_1]))
			$this->oldColumnsValues[PartnerStatsPeer::KSHOWS_1] = $this->kshows_1;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kshows_1 !== $v) {
			$this->kshows_1 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::KSHOWS_1;
		}

		return $this;
	} // setKshows1()

	/**
	 * Set the value of [kshows_2] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setKshows2($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::KSHOWS_2]))
			$this->oldColumnsValues[PartnerStatsPeer::KSHOWS_2] = $this->kshows_2;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kshows_2 !== $v) {
			$this->kshows_2 = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::KSHOWS_2;
		}

		return $this;
	} // setKshows2()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PartnerStats The current object (for fluent API support)
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
				$this->modifiedColumns[] = PartnerStatsPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PartnerStats The current object (for fluent API support)
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
				$this->modifiedColumns[] = PartnerStatsPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [widgets] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerStats The current object (for fluent API support)
	 */
	public function setWidgets($v)
	{
		if(!isset($this->oldColumnsValues[PartnerStatsPeer::WIDGETS]))
			$this->oldColumnsValues[PartnerStatsPeer::WIDGETS] = $this->widgets;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->widgets !== $v) {
			$this->widgets = $v;
			$this->modifiedColumns[] = PartnerStatsPeer::WIDGETS;
		}

		return $this;
	} // setWidgets()

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

			$this->partner_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->views = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->plays = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->videos = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->audios = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->images = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->entries = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->users_1 = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->users_2 = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->rc_1 = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->rc_2 = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->kshows_1 = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->kshows_2 = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->created_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->updated_at = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->custom_data = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->widgets = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 17; // 17 = PartnerStatsPeer::NUM_COLUMNS - PartnerStatsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PartnerStats object", $e);
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
			$con = Propel::getConnection(PartnerStatsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = PartnerStatsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(PartnerStatsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				PartnerStatsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PartnerStatsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				PartnerStatsPeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PartnerStatsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += PartnerStatsPeer::doUpdate($this, $con);
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
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null) 
	{
		$this->oldColumnsValues = array();
		$this->oldCustomDataValues = array();
    	 
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
		PartnerStatsPeer::setUseCriteriaFilter(false);
		$this->reload();
		PartnerStatsPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = PartnerStatsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = PartnerStatsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPartnerId();
				break;
			case 1:
				return $this->getViews();
				break;
			case 2:
				return $this->getPlays();
				break;
			case 3:
				return $this->getVideos();
				break;
			case 4:
				return $this->getAudios();
				break;
			case 5:
				return $this->getImages();
				break;
			case 6:
				return $this->getEntries();
				break;
			case 7:
				return $this->getUsers1();
				break;
			case 8:
				return $this->getUsers2();
				break;
			case 9:
				return $this->getRc1();
				break;
			case 10:
				return $this->getRc2();
				break;
			case 11:
				return $this->getKshows1();
				break;
			case 12:
				return $this->getKshows2();
				break;
			case 13:
				return $this->getCreatedAt();
				break;
			case 14:
				return $this->getUpdatedAt();
				break;
			case 15:
				return $this->getCustomData();
				break;
			case 16:
				return $this->getWidgets();
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
		$keys = PartnerStatsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getPartnerId(),
			$keys[1] => $this->getViews(),
			$keys[2] => $this->getPlays(),
			$keys[3] => $this->getVideos(),
			$keys[4] => $this->getAudios(),
			$keys[5] => $this->getImages(),
			$keys[6] => $this->getEntries(),
			$keys[7] => $this->getUsers1(),
			$keys[8] => $this->getUsers2(),
			$keys[9] => $this->getRc1(),
			$keys[10] => $this->getRc2(),
			$keys[11] => $this->getKshows1(),
			$keys[12] => $this->getKshows2(),
			$keys[13] => $this->getCreatedAt(),
			$keys[14] => $this->getUpdatedAt(),
			$keys[15] => $this->getCustomData(),
			$keys[16] => $this->getWidgets(),
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
		$pos = PartnerStatsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPartnerId($value);
				break;
			case 1:
				$this->setViews($value);
				break;
			case 2:
				$this->setPlays($value);
				break;
			case 3:
				$this->setVideos($value);
				break;
			case 4:
				$this->setAudios($value);
				break;
			case 5:
				$this->setImages($value);
				break;
			case 6:
				$this->setEntries($value);
				break;
			case 7:
				$this->setUsers1($value);
				break;
			case 8:
				$this->setUsers2($value);
				break;
			case 9:
				$this->setRc1($value);
				break;
			case 10:
				$this->setRc2($value);
				break;
			case 11:
				$this->setKshows1($value);
				break;
			case 12:
				$this->setKshows2($value);
				break;
			case 13:
				$this->setCreatedAt($value);
				break;
			case 14:
				$this->setUpdatedAt($value);
				break;
			case 15:
				$this->setCustomData($value);
				break;
			case 16:
				$this->setWidgets($value);
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
		$keys = PartnerStatsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setPartnerId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setViews($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPlays($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setVideos($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAudios($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setImages($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEntries($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setUsers1($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUsers2($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setRc1($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setRc2($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setKshows1($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setKshows2($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setCreatedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setUpdatedAt($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCustomData($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setWidgets($arr[$keys[16]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PartnerStatsPeer::DATABASE_NAME);

		if ($this->isColumnModified(PartnerStatsPeer::PARTNER_ID)) $criteria->add(PartnerStatsPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(PartnerStatsPeer::VIEWS)) $criteria->add(PartnerStatsPeer::VIEWS, $this->views);
		if ($this->isColumnModified(PartnerStatsPeer::PLAYS)) $criteria->add(PartnerStatsPeer::PLAYS, $this->plays);
		if ($this->isColumnModified(PartnerStatsPeer::VIDEOS)) $criteria->add(PartnerStatsPeer::VIDEOS, $this->videos);
		if ($this->isColumnModified(PartnerStatsPeer::AUDIOS)) $criteria->add(PartnerStatsPeer::AUDIOS, $this->audios);
		if ($this->isColumnModified(PartnerStatsPeer::IMAGES)) $criteria->add(PartnerStatsPeer::IMAGES, $this->images);
		if ($this->isColumnModified(PartnerStatsPeer::ENTRIES)) $criteria->add(PartnerStatsPeer::ENTRIES, $this->entries);
		if ($this->isColumnModified(PartnerStatsPeer::USERS_1)) $criteria->add(PartnerStatsPeer::USERS_1, $this->users_1);
		if ($this->isColumnModified(PartnerStatsPeer::USERS_2)) $criteria->add(PartnerStatsPeer::USERS_2, $this->users_2);
		if ($this->isColumnModified(PartnerStatsPeer::RC_1)) $criteria->add(PartnerStatsPeer::RC_1, $this->rc_1);
		if ($this->isColumnModified(PartnerStatsPeer::RC_2)) $criteria->add(PartnerStatsPeer::RC_2, $this->rc_2);
		if ($this->isColumnModified(PartnerStatsPeer::KSHOWS_1)) $criteria->add(PartnerStatsPeer::KSHOWS_1, $this->kshows_1);
		if ($this->isColumnModified(PartnerStatsPeer::KSHOWS_2)) $criteria->add(PartnerStatsPeer::KSHOWS_2, $this->kshows_2);
		if ($this->isColumnModified(PartnerStatsPeer::CREATED_AT)) $criteria->add(PartnerStatsPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(PartnerStatsPeer::UPDATED_AT)) $criteria->add(PartnerStatsPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(PartnerStatsPeer::CUSTOM_DATA)) $criteria->add(PartnerStatsPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(PartnerStatsPeer::WIDGETS)) $criteria->add(PartnerStatsPeer::WIDGETS, $this->widgets);

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
		$criteria = new Criteria(PartnerStatsPeer::DATABASE_NAME);

		$criteria->add(PartnerStatsPeer::PARTNER_ID, $this->partner_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getPartnerId();
	}

	/**
	 * Generic method to set the primary key (partner_id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setPartnerId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of PartnerStats (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setViews($this->views);

		$copyObj->setPlays($this->plays);

		$copyObj->setVideos($this->videos);

		$copyObj->setAudios($this->audios);

		$copyObj->setImages($this->images);

		$copyObj->setEntries($this->entries);

		$copyObj->setUsers1($this->users_1);

		$copyObj->setUsers2($this->users_2);

		$copyObj->setRc1($this->rc_1);

		$copyObj->setRc2($this->rc_2);

		$copyObj->setKshows1($this->kshows_1);

		$copyObj->setKshows2($this->kshows_2);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setWidgets($this->widgets);


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
	 * @return     PartnerStats Clone of current object.
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
	 * @var     PartnerStats Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      PartnerStats $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(PartnerStats $copiedFrom)
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
	 * @return     PartnerStatsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PartnerStatsPeer();
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
	
} // BasePartnerStats
