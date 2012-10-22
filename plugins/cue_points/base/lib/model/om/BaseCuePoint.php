<?php

/**
 * Base class that represents a row from the 'cue_point' table.
 *
 * 
 *
 * @package plugins.cuePoint
 * @subpackage model.om
 */
abstract class BaseCuePoint extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CuePointPeer
	 */
	protected static $peer;

	/**
	 * The value for the int_id field.
	 * @var        int
	 */
	protected $int_id;

	/**
	 * The value for the id field.
	 * @var        string
	 */
	protected $id;

	/**
	 * The value for the parent_id field.
	 * @var        string
	 */
	protected $parent_id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

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
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the system_name field.
	 * @var        string
	 */
	protected $system_name;

	/**
	 * The value for the text field.
	 * @var        string
	 */
	protected $text;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the start_time field.
	 * @var        int
	 */
	protected $start_time;

	/**
	 * The value for the end_time field.
	 * @var        int
	 */
	protected $end_time;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the sub_type field.
	 * @var        int
	 */
	protected $sub_type;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the partner_data field.
	 * @var        string
	 */
	protected $partner_data;

	/**
	 * The value for the partner_sort_value field.
	 * @var        int
	 */
	protected $partner_sort_value;

	/**
	 * The value for the thumb_offset field.
	 * @var        int
	 */
	protected $thumb_offset;

	/**
	 * The value for the depth field.
	 * @var        int
	 */
	protected $depth;

	/**
	 * The value for the children_count field.
	 * @var        int
	 */
	protected $children_count;

	/**
	 * The value for the direct_children_count field.
	 * @var        int
	 */
	protected $direct_children_count;

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
	 * Get the [int_id] column value.
	 * 
	 * @return     int
	 */
	public function getIntId()
	{
		return $this->int_id;
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [parent_id] column value.
	 * 
	 * @return     string
	 */
	public function getParentId()
	{
		return $this->parent_id;
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the [system_name] column value.
	 * 
	 * @return     string
	 */
	public function getSystemName()
	{
		return $this->system_name;
	}

	/**
	 * Get the [text] column value.
	 * 
	 * @return     string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * Get the [tags] column value.
	 * 
	 * @return     string
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Get the [start_time] column value.
	 * 
	 * @return     int
	 */
	public function getStartTime()
	{
		return $this->start_time;
	}

	/**
	 * Get the [end_time] column value.
	 * 
	 * @return     int
	 */
	public function getEndTime()
	{
		return $this->end_time;
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
	 * Get the [type] column value.
	 * 
	 * @return     int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [sub_type] column value.
	 * 
	 * @return     int
	 */
	public function getSubType()
	{
		return $this->sub_type;
	}

	/**
	 * Get the [kuser_id] column value.
	 * 
	 * @return     int
	 */
	public function getKuserId()
	{
		return $this->kuser_id;
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
	 * Get the [partner_data] column value.
	 * 
	 * @return     string
	 */
	public function getPartnerData()
	{
		return $this->partner_data;
	}

	/**
	 * Get the [partner_sort_value] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerSortValue()
	{
		return $this->partner_sort_value;
	}

	/**
	 * Get the [thumb_offset] column value.
	 * 
	 * @return     int
	 */
	public function getThumbOffset()
	{
		return $this->thumb_offset;
	}

	/**
	 * Get the [depth] column value.
	 * 
	 * @return     int
	 */
	public function getDepth()
	{
		return $this->depth;
	}

	/**
	 * Get the [children_count] column value.
	 * 
	 * @return     int
	 */
	public function getChildrenCount()
	{
		return $this->children_count;
	}

	/**
	 * Get the [direct_children_count] column value.
	 * 
	 * @return     int
	 */
	public function getDirectChildrenCount()
	{
		return $this->direct_children_count;
	}

	/**
	 * Set the value of [int_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setIntId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::INT_ID]))
			$this->oldColumnsValues[CuePointPeer::INT_ID] = $this->int_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->int_id !== $v) {
			$this->int_id = $v;
			$this->modifiedColumns[] = CuePointPeer::INT_ID;
		}

		return $this;
	} // setIntId()

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::ID]))
			$this->oldColumnsValues[CuePointPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CuePointPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setParentId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::PARENT_ID]))
			$this->oldColumnsValues[CuePointPeer::PARENT_ID] = $this->parent_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->parent_id !== $v) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = CuePointPeer::PARENT_ID;
		}

		return $this;
	} // setParentId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::ENTRY_ID]))
			$this->oldColumnsValues[CuePointPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = CuePointPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::PARTNER_ID]))
			$this->oldColumnsValues[CuePointPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = CuePointPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CuePoint The current object (for fluent API support)
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
				$this->modifiedColumns[] = CuePointPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CuePoint The current object (for fluent API support)
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
				$this->modifiedColumns[] = CuePointPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::NAME]))
			$this->oldColumnsValues[CuePointPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = CuePointPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setSystemName($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::SYSTEM_NAME]))
			$this->oldColumnsValues[CuePointPeer::SYSTEM_NAME] = $this->system_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->system_name !== $v) {
			$this->system_name = $v;
			$this->modifiedColumns[] = CuePointPeer::SYSTEM_NAME;
		}

		return $this;
	} // setSystemName()

	/**
	 * Set the value of [text] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setText($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::TEXT]))
			$this->oldColumnsValues[CuePointPeer::TEXT] = $this->text;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->text !== $v) {
			$this->text = $v;
			$this->modifiedColumns[] = CuePointPeer::TEXT;
		}

		return $this;
	} // setText()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::TAGS]))
			$this->oldColumnsValues[CuePointPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = CuePointPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [start_time] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setStartTime($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::START_TIME]))
			$this->oldColumnsValues[CuePointPeer::START_TIME] = $this->start_time;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->start_time !== $v) {
			$this->start_time = $v;
			$this->modifiedColumns[] = CuePointPeer::START_TIME;
		}

		return $this;
	} // setStartTime()

	/**
	 * Set the value of [end_time] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setEndTime($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::END_TIME]))
			$this->oldColumnsValues[CuePointPeer::END_TIME] = $this->end_time;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->end_time !== $v) {
			$this->end_time = $v;
			$this->modifiedColumns[] = CuePointPeer::END_TIME;
		}

		return $this;
	} // setEndTime()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::STATUS]))
			$this->oldColumnsValues[CuePointPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = CuePointPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::TYPE]))
			$this->oldColumnsValues[CuePointPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = CuePointPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setSubType($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::SUB_TYPE]))
			$this->oldColumnsValues[CuePointPeer::SUB_TYPE] = $this->sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->sub_type !== $v) {
			$this->sub_type = $v;
			$this->modifiedColumns[] = CuePointPeer::SUB_TYPE;
		}

		return $this;
	} // setSubType()

	/**
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::KUSER_ID]))
			$this->oldColumnsValues[CuePointPeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = CuePointPeer::KUSER_ID;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = CuePointPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [partner_data] column.
	 * 
	 * @param      string $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setPartnerData($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::PARTNER_DATA]))
			$this->oldColumnsValues[CuePointPeer::PARTNER_DATA] = $this->partner_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_data !== $v) {
			$this->partner_data = $v;
			$this->modifiedColumns[] = CuePointPeer::PARTNER_DATA;
		}

		return $this;
	} // setPartnerData()

	/**
	 * Set the value of [partner_sort_value] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setPartnerSortValue($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::PARTNER_SORT_VALUE]))
			$this->oldColumnsValues[CuePointPeer::PARTNER_SORT_VALUE] = $this->partner_sort_value;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_sort_value !== $v) {
			$this->partner_sort_value = $v;
			$this->modifiedColumns[] = CuePointPeer::PARTNER_SORT_VALUE;
		}

		return $this;
	} // setPartnerSortValue()

	/**
	 * Set the value of [thumb_offset] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setThumbOffset($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::THUMB_OFFSET]))
			$this->oldColumnsValues[CuePointPeer::THUMB_OFFSET] = $this->thumb_offset;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->thumb_offset !== $v) {
			$this->thumb_offset = $v;
			$this->modifiedColumns[] = CuePointPeer::THUMB_OFFSET;
		}

		return $this;
	} // setThumbOffset()

	/**
	 * Set the value of [depth] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setDepth($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::DEPTH]))
			$this->oldColumnsValues[CuePointPeer::DEPTH] = $this->depth;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->depth !== $v) {
			$this->depth = $v;
			$this->modifiedColumns[] = CuePointPeer::DEPTH;
		}

		return $this;
	} // setDepth()

	/**
	 * Set the value of [children_count] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setChildrenCount($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::CHILDREN_COUNT]))
			$this->oldColumnsValues[CuePointPeer::CHILDREN_COUNT] = $this->children_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->children_count !== $v) {
			$this->children_count = $v;
			$this->modifiedColumns[] = CuePointPeer::CHILDREN_COUNT;
		}

		return $this;
	} // setChildrenCount()

	/**
	 * Set the value of [direct_children_count] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setDirectChildrenCount($v)
	{
		if(!isset($this->oldColumnsValues[CuePointPeer::DIRECT_CHILDREN_COUNT]))
			$this->oldColumnsValues[CuePointPeer::DIRECT_CHILDREN_COUNT] = $this->direct_children_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->direct_children_count !== $v) {
			$this->direct_children_count = $v;
			$this->modifiedColumns[] = CuePointPeer::DIRECT_CHILDREN_COUNT;
		}

		return $this;
	} // setDirectChildrenCount()

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

			$this->int_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->parent_id = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->entry_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->partner_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->created_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->updated_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->name = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->system_name = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->text = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->tags = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->start_time = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->end_time = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->status = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->type = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->sub_type = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->kuser_id = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->custom_data = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->partner_data = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->partner_sort_value = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->thumb_offset = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->depth = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->children_count = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->direct_children_count = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = CuePointPeer::NUM_COLUMNS - CuePointPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CuePoint object", $e);
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
			$con = Propel::getConnection(CuePointPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		CuePointPeer::setUseCriteriaFilter(false);
		$stmt = CuePointPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		CuePointPeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(CuePointPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CuePointPeer::doDelete($this, $con);
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
	 * Since this table was configured to reload rows on insert, the object will
	 * be reloaded from the database if an INSERT operation is performed (unless
	 * the $skipReload parameter is TRUE).
	 *
	 * @param      PropelPDO $con
	 * @param      boolean $skipReload Whether to skip the reload for this object from database.
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null, $skipReload = false)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CuePointPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				$affectedRows = $this->doSave($con, $skipReload);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				CuePointPeer::addInstanceToPool($this);
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
	 * @param      boolean $skipReload Whether to skip the reload for this object from database.
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con, $skipReload = false)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			$reloadObject = false;


			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CuePointPeer::doInsert($this, $con);
					if (!$skipReload) {
						$reloadObject = true;
					}
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = CuePointPeer::doUpdate($this, $con);
					if($affectedObjects)
						$this->objectSaved = true;
						
					$affectedRows += $affectedObjects;
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

			if ($reloadObject) {
				$this->reload($con);
			}

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


			if (($retval = CuePointPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CuePointPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIntId();
				break;
			case 1:
				return $this->getId();
				break;
			case 2:
				return $this->getParentId();
				break;
			case 3:
				return $this->getEntryId();
				break;
			case 4:
				return $this->getPartnerId();
				break;
			case 5:
				return $this->getCreatedAt();
				break;
			case 6:
				return $this->getUpdatedAt();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getSystemName();
				break;
			case 9:
				return $this->getText();
				break;
			case 10:
				return $this->getTags();
				break;
			case 11:
				return $this->getStartTime();
				break;
			case 12:
				return $this->getEndTime();
				break;
			case 13:
				return $this->getStatus();
				break;
			case 14:
				return $this->getType();
				break;
			case 15:
				return $this->getSubType();
				break;
			case 16:
				return $this->getKuserId();
				break;
			case 17:
				return $this->getCustomData();
				break;
			case 18:
				return $this->getPartnerData();
				break;
			case 19:
				return $this->getPartnerSortValue();
				break;
			case 20:
				return $this->getThumbOffset();
				break;
			case 21:
				return $this->getDepth();
				break;
			case 22:
				return $this->getChildrenCount();
				break;
			case 23:
				return $this->getDirectChildrenCount();
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
		$keys = CuePointPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIntId(),
			$keys[1] => $this->getId(),
			$keys[2] => $this->getParentId(),
			$keys[3] => $this->getEntryId(),
			$keys[4] => $this->getPartnerId(),
			$keys[5] => $this->getCreatedAt(),
			$keys[6] => $this->getUpdatedAt(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getSystemName(),
			$keys[9] => $this->getText(),
			$keys[10] => $this->getTags(),
			$keys[11] => $this->getStartTime(),
			$keys[12] => $this->getEndTime(),
			$keys[13] => $this->getStatus(),
			$keys[14] => $this->getType(),
			$keys[15] => $this->getSubType(),
			$keys[16] => $this->getKuserId(),
			$keys[17] => $this->getCustomData(),
			$keys[18] => $this->getPartnerData(),
			$keys[19] => $this->getPartnerSortValue(),
			$keys[20] => $this->getThumbOffset(),
			$keys[21] => $this->getDepth(),
			$keys[22] => $this->getChildrenCount(),
			$keys[23] => $this->getDirectChildrenCount(),
		);
		return $result;
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CuePointPeer::DATABASE_NAME);

		if ($this->isColumnModified(CuePointPeer::INT_ID)) $criteria->add(CuePointPeer::INT_ID, $this->int_id);
		if ($this->isColumnModified(CuePointPeer::ID)) $criteria->add(CuePointPeer::ID, $this->id);
		if ($this->isColumnModified(CuePointPeer::PARENT_ID)) $criteria->add(CuePointPeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(CuePointPeer::ENTRY_ID)) $criteria->add(CuePointPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(CuePointPeer::PARTNER_ID)) $criteria->add(CuePointPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(CuePointPeer::CREATED_AT)) $criteria->add(CuePointPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(CuePointPeer::UPDATED_AT)) $criteria->add(CuePointPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(CuePointPeer::NAME)) $criteria->add(CuePointPeer::NAME, $this->name);
		if ($this->isColumnModified(CuePointPeer::SYSTEM_NAME)) $criteria->add(CuePointPeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(CuePointPeer::TEXT)) $criteria->add(CuePointPeer::TEXT, $this->text);
		if ($this->isColumnModified(CuePointPeer::TAGS)) $criteria->add(CuePointPeer::TAGS, $this->tags);
		if ($this->isColumnModified(CuePointPeer::START_TIME)) $criteria->add(CuePointPeer::START_TIME, $this->start_time);
		if ($this->isColumnModified(CuePointPeer::END_TIME)) $criteria->add(CuePointPeer::END_TIME, $this->end_time);
		if ($this->isColumnModified(CuePointPeer::STATUS)) $criteria->add(CuePointPeer::STATUS, $this->status);
		if ($this->isColumnModified(CuePointPeer::TYPE)) $criteria->add(CuePointPeer::TYPE, $this->type);
		if ($this->isColumnModified(CuePointPeer::SUB_TYPE)) $criteria->add(CuePointPeer::SUB_TYPE, $this->sub_type);
		if ($this->isColumnModified(CuePointPeer::KUSER_ID)) $criteria->add(CuePointPeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(CuePointPeer::CUSTOM_DATA)) $criteria->add(CuePointPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(CuePointPeer::PARTNER_DATA)) $criteria->add(CuePointPeer::PARTNER_DATA, $this->partner_data);
		if ($this->isColumnModified(CuePointPeer::PARTNER_SORT_VALUE)) $criteria->add(CuePointPeer::PARTNER_SORT_VALUE, $this->partner_sort_value);
		if ($this->isColumnModified(CuePointPeer::THUMB_OFFSET)) $criteria->add(CuePointPeer::THUMB_OFFSET, $this->thumb_offset);
		if ($this->isColumnModified(CuePointPeer::DEPTH)) $criteria->add(CuePointPeer::DEPTH, $this->depth);
		if ($this->isColumnModified(CuePointPeer::CHILDREN_COUNT)) $criteria->add(CuePointPeer::CHILDREN_COUNT, $this->children_count);
		if ($this->isColumnModified(CuePointPeer::DIRECT_CHILDREN_COUNT)) $criteria->add(CuePointPeer::DIRECT_CHILDREN_COUNT, $this->direct_children_count);

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
		$criteria = new Criteria(CuePointPeer::DATABASE_NAME);

		$criteria->add(CuePointPeer::ID, $this->id);
		
		if($this->alreadyInSave && count($this->modifiedColumns) == 2 && $this->isColumnModified(CuePointPeer::UPDATED_AT))
		{
			$theModifiedColumn = null;
			foreach($this->modifiedColumns as $modifiedColumn)
				if($modifiedColumn != CuePointPeer::UPDATED_AT)
					$theModifiedColumn = $modifiedColumn;
					
			$atomicColumns = CuePointPeer::getAtomicColumns();
			if(in_array($theModifiedColumn, $atomicColumns))
				$criteria->add($theModifiedColumn, $this->getByName($theModifiedColumn, BasePeer::TYPE_COLNAME), Criteria::NOT_EQUAL);
		}

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      string $key Primary key.
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
	 * @param      object $copyObj An object of CuePoint (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setId($this->id);

		$copyObj->setParentId($this->parent_id);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setName($this->name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setText($this->text);

		$copyObj->setTags($this->tags);

		$copyObj->setStartTime($this->start_time);

		$copyObj->setEndTime($this->end_time);

		$copyObj->setStatus($this->status);

		$copyObj->setType($this->type);

		$copyObj->setSubType($this->sub_type);

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setPartnerData($this->partner_data);

		$copyObj->setPartnerSortValue($this->partner_sort_value);

		$copyObj->setThumbOffset($this->thumb_offset);

		$copyObj->setDepth($this->depth);

		$copyObj->setChildrenCount($this->children_count);

		$copyObj->setDirectChildrenCount($this->direct_children_count);


		$copyObj->setNew(true);

		$copyObj->setIntId(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     CuePoint Clone of current object.
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
	 * @var     CuePoint Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      CuePoint $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(CuePoint $copiedFrom)
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
	 * @return     CuePointPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CuePointPeer();
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
	
} // BaseCuePoint
