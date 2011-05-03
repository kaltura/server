<?php

/**
 * Base class that represents a row from the 'file_sync' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseFileSync extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        FileSyncPeer
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
	 * The value for the object_type field.
	 * @var        int
	 */
	protected $object_type;

	/**
	 * The value for the object_id field.
	 * @var        string
	 */
	protected $object_id;

	/**
	 * The value for the version field.
	 * @var        string
	 */
	protected $version;

	/**
	 * The value for the object_sub_type field.
	 * @var        int
	 */
	protected $object_sub_type;

	/**
	 * The value for the dc field.
	 * @var        int
	 */
	protected $dc;

	/**
	 * The value for the original field.
	 * @var        int
	 */
	protected $original;

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
	 * The value for the ready_at field.
	 * @var        string
	 */
	protected $ready_at;

	/**
	 * The value for the sync_time field.
	 * @var        int
	 */
	protected $sync_time;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the file_type field.
	 * @var        int
	 */
	protected $file_type;

	/**
	 * The value for the linked_id field.
	 * @var        int
	 */
	protected $linked_id;

	/**
	 * The value for the link_count field.
	 * @var        int
	 */
	protected $link_count;

	/**
	 * The value for the file_root field.
	 * @var        string
	 */
	protected $file_root;

	/**
	 * The value for the file_path field.
	 * @var        string
	 */
	protected $file_path;

	/**
	 * The value for the file_size field.
	 * @var        string
	 */
	protected $file_size;

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
	 * Get the [object_type] column value.
	 * 
	 * @return     int
	 */
	public function getObjectType()
	{
		return $this->object_type;
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
	 * Get the [version] column value.
	 * 
	 * @return     string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Get the [object_sub_type] column value.
	 * 
	 * @return     int
	 */
	public function getObjectSubType()
	{
		return $this->object_sub_type;
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
	 * Get the [original] column value.
	 * 
	 * @return     int
	 */
	public function getOriginal()
	{
		return $this->original;
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
	 * Get the [optionally formatted] temporal [ready_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getReadyAt($format = 'Y-m-d H:i:s')
	{
		if ($this->ready_at === null) {
			return null;
		}


		if ($this->ready_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->ready_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ready_at, true), $x);
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
	 * Get the [sync_time] column value.
	 * 
	 * @return     int
	 */
	public function getSyncTime()
	{
		return $this->sync_time;
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
	 * Get the [file_type] column value.
	 * 
	 * @return     int
	 */
	public function getFileType()
	{
		return $this->file_type;
	}

	/**
	 * Get the [linked_id] column value.
	 * 
	 * @return     int
	 */
	public function getLinkedId()
	{
		return $this->linked_id;
	}

	/**
	 * Get the [link_count] column value.
	 * 
	 * @return     int
	 */
	public function getLinkCount()
	{
		return $this->link_count;
	}

	/**
	 * Get the [file_root] column value.
	 * 
	 * @return     string
	 */
	public function getFileRoot()
	{
		return $this->file_root;
	}

	/**
	 * Get the [file_path] column value.
	 * 
	 * @return     string
	 */
	public function getFilePath()
	{
		return $this->file_path;
	}

	/**
	 * Get the [file_size] column value.
	 * 
	 * @return     string
	 */
	public function getFileSize()
	{
		return $this->file_size;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::ID]))
			$this->oldColumnsValues[FileSyncPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = FileSyncPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::PARTNER_ID]))
			$this->oldColumnsValues[FileSyncPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = FileSyncPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[FileSyncPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = FileSyncPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::OBJECT_ID]))
			$this->oldColumnsValues[FileSyncPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = FileSyncPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      string $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::VERSION]))
			$this->oldColumnsValues[FileSyncPeer::VERSION] = $this->version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = FileSyncPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Set the value of [object_sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setObjectSubType($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::OBJECT_SUB_TYPE]))
			$this->oldColumnsValues[FileSyncPeer::OBJECT_SUB_TYPE] = $this->object_sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->object_sub_type !== $v) {
			$this->object_sub_type = $v;
			$this->modifiedColumns[] = FileSyncPeer::OBJECT_SUB_TYPE;
		}

		return $this;
	} // setObjectSubType()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::DC]))
			$this->oldColumnsValues[FileSyncPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = FileSyncPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [original] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setOriginal($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::ORIGINAL]))
			$this->oldColumnsValues[FileSyncPeer::ORIGINAL] = $this->original;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->original !== $v) {
			$this->original = $v;
			$this->modifiedColumns[] = FileSyncPeer::ORIGINAL;
		}

		return $this;
	} // setOriginal()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     FileSync The current object (for fluent API support)
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
				$this->modifiedColumns[] = FileSyncPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     FileSync The current object (for fluent API support)
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
				$this->modifiedColumns[] = FileSyncPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [ready_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setReadyAt($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::READY_AT]))
			$this->oldColumnsValues[FileSyncPeer::READY_AT] = $this->ready_at;

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

		if ( $this->ready_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->ready_at !== null && $tmpDt = new DateTime($this->ready_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->ready_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = FileSyncPeer::READY_AT;
			}
		} // if either are not null

		return $this;
	} // setReadyAt()

	/**
	 * Set the value of [sync_time] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setSyncTime($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::SYNC_TIME]))
			$this->oldColumnsValues[FileSyncPeer::SYNC_TIME] = $this->sync_time;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->sync_time !== $v) {
			$this->sync_time = $v;
			$this->modifiedColumns[] = FileSyncPeer::SYNC_TIME;
		}

		return $this;
	} // setSyncTime()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::STATUS]))
			$this->oldColumnsValues[FileSyncPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = FileSyncPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [file_type] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setFileType($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::FILE_TYPE]))
			$this->oldColumnsValues[FileSyncPeer::FILE_TYPE] = $this->file_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_type !== $v) {
			$this->file_type = $v;
			$this->modifiedColumns[] = FileSyncPeer::FILE_TYPE;
		}

		return $this;
	} // setFileType()

	/**
	 * Set the value of [linked_id] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setLinkedId($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::LINKED_ID]))
			$this->oldColumnsValues[FileSyncPeer::LINKED_ID] = $this->linked_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->linked_id !== $v) {
			$this->linked_id = $v;
			$this->modifiedColumns[] = FileSyncPeer::LINKED_ID;
		}

		return $this;
	} // setLinkedId()

	/**
	 * Set the value of [link_count] column.
	 * 
	 * @param      int $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setLinkCount($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::LINK_COUNT]))
			$this->oldColumnsValues[FileSyncPeer::LINK_COUNT] = $this->link_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->link_count !== $v) {
			$this->link_count = $v;
			$this->modifiedColumns[] = FileSyncPeer::LINK_COUNT;
		}

		return $this;
	} // setLinkCount()

	/**
	 * Set the value of [file_root] column.
	 * 
	 * @param      string $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setFileRoot($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::FILE_ROOT]))
			$this->oldColumnsValues[FileSyncPeer::FILE_ROOT] = $this->file_root;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_root !== $v) {
			$this->file_root = $v;
			$this->modifiedColumns[] = FileSyncPeer::FILE_ROOT;
		}

		return $this;
	} // setFileRoot()

	/**
	 * Set the value of [file_path] column.
	 * 
	 * @param      string $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setFilePath($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::FILE_PATH]))
			$this->oldColumnsValues[FileSyncPeer::FILE_PATH] = $this->file_path;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_path !== $v) {
			$this->file_path = $v;
			$this->modifiedColumns[] = FileSyncPeer::FILE_PATH;
		}

		return $this;
	} // setFilePath()

	/**
	 * Set the value of [file_size] column.
	 * 
	 * @param      string $v new value
	 * @return     FileSync The current object (for fluent API support)
	 */
	public function setFileSize($v)
	{
		if(!isset($this->oldColumnsValues[FileSyncPeer::FILE_SIZE]))
			$this->oldColumnsValues[FileSyncPeer::FILE_SIZE] = $this->file_size;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = FileSyncPeer::FILE_SIZE;
		}

		return $this;
	} // setFileSize()

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
			$this->object_type = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->object_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->version = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->object_sub_type = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->dc = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->original = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->ready_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->sync_time = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->status = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->file_type = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->linked_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->link_count = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->file_root = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->file_path = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->file_size = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 19; // 19 = FileSyncPeer::NUM_COLUMNS - FileSyncPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating FileSync object", $e);
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
			$con = Propel::getConnection(FileSyncPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = FileSyncPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(FileSyncPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				FileSyncPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(FileSyncPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				FileSyncPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = FileSyncPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = FileSyncPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += FileSyncPeer::doUpdate($this, $con);
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
		kQueryCache::invalidateQueryCache($this);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
		
	}

	/**
	 * Code to be run after updating the object in database
	 * @param PropelPDO $con
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		kQueryCache::invalidateQueryCache($this);
		
		if($this->isModified())
		{
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->tempModifiedColumns));
		}
			
		$this->tempModifiedColumns = array();
		
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


			if (($retval = FileSyncPeer::doValidate($this, $columns)) !== true) {
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
		$pos = FileSyncPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getObjectType();
				break;
			case 3:
				return $this->getObjectId();
				break;
			case 4:
				return $this->getVersion();
				break;
			case 5:
				return $this->getObjectSubType();
				break;
			case 6:
				return $this->getDc();
				break;
			case 7:
				return $this->getOriginal();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			case 9:
				return $this->getUpdatedAt();
				break;
			case 10:
				return $this->getReadyAt();
				break;
			case 11:
				return $this->getSyncTime();
				break;
			case 12:
				return $this->getStatus();
				break;
			case 13:
				return $this->getFileType();
				break;
			case 14:
				return $this->getLinkedId();
				break;
			case 15:
				return $this->getLinkCount();
				break;
			case 16:
				return $this->getFileRoot();
				break;
			case 17:
				return $this->getFilePath();
				break;
			case 18:
				return $this->getFileSize();
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
		$keys = FileSyncPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getObjectType(),
			$keys[3] => $this->getObjectId(),
			$keys[4] => $this->getVersion(),
			$keys[5] => $this->getObjectSubType(),
			$keys[6] => $this->getDc(),
			$keys[7] => $this->getOriginal(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getUpdatedAt(),
			$keys[10] => $this->getReadyAt(),
			$keys[11] => $this->getSyncTime(),
			$keys[12] => $this->getStatus(),
			$keys[13] => $this->getFileType(),
			$keys[14] => $this->getLinkedId(),
			$keys[15] => $this->getLinkCount(),
			$keys[16] => $this->getFileRoot(),
			$keys[17] => $this->getFilePath(),
			$keys[18] => $this->getFileSize(),
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
		$pos = FileSyncPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setObjectType($value);
				break;
			case 3:
				$this->setObjectId($value);
				break;
			case 4:
				$this->setVersion($value);
				break;
			case 5:
				$this->setObjectSubType($value);
				break;
			case 6:
				$this->setDc($value);
				break;
			case 7:
				$this->setOriginal($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
			case 9:
				$this->setUpdatedAt($value);
				break;
			case 10:
				$this->setReadyAt($value);
				break;
			case 11:
				$this->setSyncTime($value);
				break;
			case 12:
				$this->setStatus($value);
				break;
			case 13:
				$this->setFileType($value);
				break;
			case 14:
				$this->setLinkedId($value);
				break;
			case 15:
				$this->setLinkCount($value);
				break;
			case 16:
				$this->setFileRoot($value);
				break;
			case 17:
				$this->setFilePath($value);
				break;
			case 18:
				$this->setFileSize($value);
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
		$keys = FileSyncPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setObjectType($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjectId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setVersion($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setObjectSubType($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDc($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setOriginal($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setReadyAt($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setSyncTime($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setStatus($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setFileType($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setLinkedId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setLinkCount($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setFileRoot($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setFilePath($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setFileSize($arr[$keys[18]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);

		if ($this->isColumnModified(FileSyncPeer::ID)) $criteria->add(FileSyncPeer::ID, $this->id);
		if ($this->isColumnModified(FileSyncPeer::PARTNER_ID)) $criteria->add(FileSyncPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(FileSyncPeer::OBJECT_TYPE)) $criteria->add(FileSyncPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(FileSyncPeer::OBJECT_ID)) $criteria->add(FileSyncPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(FileSyncPeer::VERSION)) $criteria->add(FileSyncPeer::VERSION, $this->version);
		if ($this->isColumnModified(FileSyncPeer::OBJECT_SUB_TYPE)) $criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, $this->object_sub_type);
		if ($this->isColumnModified(FileSyncPeer::DC)) $criteria->add(FileSyncPeer::DC, $this->dc);
		if ($this->isColumnModified(FileSyncPeer::ORIGINAL)) $criteria->add(FileSyncPeer::ORIGINAL, $this->original);
		if ($this->isColumnModified(FileSyncPeer::CREATED_AT)) $criteria->add(FileSyncPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(FileSyncPeer::UPDATED_AT)) $criteria->add(FileSyncPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(FileSyncPeer::READY_AT)) $criteria->add(FileSyncPeer::READY_AT, $this->ready_at);
		if ($this->isColumnModified(FileSyncPeer::SYNC_TIME)) $criteria->add(FileSyncPeer::SYNC_TIME, $this->sync_time);
		if ($this->isColumnModified(FileSyncPeer::STATUS)) $criteria->add(FileSyncPeer::STATUS, $this->status);
		if ($this->isColumnModified(FileSyncPeer::FILE_TYPE)) $criteria->add(FileSyncPeer::FILE_TYPE, $this->file_type);
		if ($this->isColumnModified(FileSyncPeer::LINKED_ID)) $criteria->add(FileSyncPeer::LINKED_ID, $this->linked_id);
		if ($this->isColumnModified(FileSyncPeer::LINK_COUNT)) $criteria->add(FileSyncPeer::LINK_COUNT, $this->link_count);
		if ($this->isColumnModified(FileSyncPeer::FILE_ROOT)) $criteria->add(FileSyncPeer::FILE_ROOT, $this->file_root);
		if ($this->isColumnModified(FileSyncPeer::FILE_PATH)) $criteria->add(FileSyncPeer::FILE_PATH, $this->file_path);
		if ($this->isColumnModified(FileSyncPeer::FILE_SIZE)) $criteria->add(FileSyncPeer::FILE_SIZE, $this->file_size);

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
		$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);

		$criteria->add(FileSyncPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of FileSync (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setVersion($this->version);

		$copyObj->setObjectSubType($this->object_sub_type);

		$copyObj->setDc($this->dc);

		$copyObj->setOriginal($this->original);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setReadyAt($this->ready_at);

		$copyObj->setSyncTime($this->sync_time);

		$copyObj->setStatus($this->status);

		$copyObj->setFileType($this->file_type);

		$copyObj->setLinkedId($this->linked_id);

		$copyObj->setLinkCount($this->link_count);

		$copyObj->setFileRoot($this->file_root);

		$copyObj->setFilePath($this->file_path);

		$copyObj->setFileSize($this->file_size);


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
	 * @return     FileSync Clone of current object.
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
	 * @var     FileSync Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      FileSync $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(FileSync $copiedFrom)
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
	 * @return     FileSyncPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new FileSyncPeer();
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

} // BaseFileSync
