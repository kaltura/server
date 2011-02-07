<?php

/**
 * Base class that represents a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBulkUploadResult extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        BulkUploadResultPeer
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
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the bulk_upload_job_id field.
	 * @var        int
	 */
	protected $bulk_upload_job_id;

	/**
	 * The value for the line_index field.
	 * @var        int
	 */
	protected $line_index;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the entry_status field.
	 * @var        int
	 */
	protected $entry_status;

	/**
	 * The value for the row_data field.
	 * @var        string
	 */
	protected $row_data;

	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the url field.
	 * @var        string
	 */
	protected $url;

	/**
	 * The value for the content_type field.
	 * @var        string
	 */
	protected $content_type;

	/**
	 * The value for the conversion_profile_id field.
	 * @var        int
	 */
	protected $conversion_profile_id;

	/**
	 * The value for the access_control_profile_id field.
	 * @var        int
	 */
	protected $access_control_profile_id;

	/**
	 * The value for the category field.
	 * @var        string
	 */
	protected $category;

	/**
	 * The value for the schedule_start_date field.
	 * @var        string
	 */
	protected $schedule_start_date;

	/**
	 * The value for the schedule_end_date field.
	 * @var        string
	 */
	protected $schedule_end_date;

	/**
	 * The value for the thumbnail_url field.
	 * @var        string
	 */
	protected $thumbnail_url;

	/**
	 * The value for the thumbnail_saved field.
	 * @var        boolean
	 */
	protected $thumbnail_saved;

	/**
	 * The value for the partner_data field.
	 * @var        string
	 */
	protected $partner_data;

	/**
	 * The value for the error_description field.
	 * @var        string
	 */
	protected $error_description;

	/**
	 * The value for the plugins_data field.
	 * @var        string
	 */
	protected $plugins_data;

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
	 * Get the [bulk_upload_job_id] column value.
	 * 
	 * @return     int
	 */
	public function getBulkUploadJobId()
	{
		return $this->bulk_upload_job_id;
	}

	/**
	 * Get the [line_index] column value.
	 * 
	 * @return     int
	 */
	public function getLineIndex()
	{
		return $this->line_index;
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
	 * Get the [entry_id] column value.
	 * 
	 * @return     string
	 */
	public function getEntryId()
	{
		return $this->entry_id;
	}

	/**
	 * Get the [entry_status] column value.
	 * 
	 * @return     int
	 */
	public function getEntryStatus()
	{
		return $this->entry_status;
	}

	/**
	 * Get the [row_data] column value.
	 * 
	 * @return     string
	 */
	public function getRowData()
	{
		return $this->row_data;
	}

	/**
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{
		return $this->title;
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
	 * Get the [tags] column value.
	 * 
	 * @return     string
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Get the [url] column value.
	 * 
	 * @return     string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Get the [content_type] column value.
	 * 
	 * @return     string
	 */
	public function getContentType()
	{
		return $this->content_type;
	}

	/**
	 * Get the [conversion_profile_id] column value.
	 * 
	 * @return     int
	 */
	public function getConversionProfileId()
	{
		return $this->conversion_profile_id;
	}

	/**
	 * Get the [access_control_profile_id] column value.
	 * 
	 * @return     int
	 */
	public function getAccessControlProfileId()
	{
		return $this->access_control_profile_id;
	}

	/**
	 * Get the [category] column value.
	 * 
	 * @return     string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Get the [optionally formatted] temporal [schedule_start_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getScheduleStartDate($format = 'Y-m-d H:i:s')
	{
		if ($this->schedule_start_date === null) {
			return null;
		}


		if ($this->schedule_start_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->schedule_start_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->schedule_start_date, true), $x);
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
	 * Get the [optionally formatted] temporal [schedule_end_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getScheduleEndDate($format = 'Y-m-d H:i:s')
	{
		if ($this->schedule_end_date === null) {
			return null;
		}


		if ($this->schedule_end_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->schedule_end_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->schedule_end_date, true), $x);
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
	 * Get the [thumbnail_url] column value.
	 * 
	 * @return     string
	 */
	public function getThumbnailUrl()
	{
		return $this->thumbnail_url;
	}

	/**
	 * Get the [thumbnail_saved] column value.
	 * 
	 * @return     boolean
	 */
	public function getThumbnailSaved()
	{
		return $this->thumbnail_saved;
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
	 * Get the [error_description] column value.
	 * 
	 * @return     string
	 */
	public function getErrorDescription()
	{
		return $this->error_description;
	}

	/**
	 * Get the [plugins_data] column value.
	 * 
	 * @return     string
	 */
	public function getPluginsData()
	{
		return $this->plugins_data;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BulkUploadResult The current object (for fluent API support)
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
				$this->modifiedColumns[] = BulkUploadResultPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BulkUploadResult The current object (for fluent API support)
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
				$this->modifiedColumns[] = BulkUploadResultPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [bulk_upload_job_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setBulkUploadJobId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::BULK_UPLOAD_JOB_ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::BULK_UPLOAD_JOB_ID] = $this->bulk_upload_job_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->bulk_upload_job_id !== $v) {
			$this->bulk_upload_job_id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::BULK_UPLOAD_JOB_ID;
		}

		return $this;
	} // setBulkUploadJobId()

	/**
	 * Set the value of [line_index] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setLineIndex($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::LINE_INDEX]))
			$this->oldColumnsValues[BulkUploadResultPeer::LINE_INDEX] = $this->line_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->line_index !== $v) {
			$this->line_index = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::LINE_INDEX;
		}

		return $this;
	} // setLineIndex()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::PARTNER_ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ENTRY_ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [entry_status] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setEntryStatus($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ENTRY_STATUS]))
			$this->oldColumnsValues[BulkUploadResultPeer::ENTRY_STATUS] = $this->entry_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->entry_status !== $v) {
			$this->entry_status = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ENTRY_STATUS;
		}

		return $this;
	} // setEntryStatus()

	/**
	 * Set the value of [row_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setRowData($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ROW_DATA]))
			$this->oldColumnsValues[BulkUploadResultPeer::ROW_DATA] = $this->row_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->row_data !== $v) {
			$this->row_data = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ROW_DATA;
		}

		return $this;
	} // setRowData()

	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setTitle($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::TITLE]))
			$this->oldColumnsValues[BulkUploadResultPeer::TITLE] = $this->title;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::TITLE;
		}

		return $this;
	} // setTitle()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::DESCRIPTION]))
			$this->oldColumnsValues[BulkUploadResultPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::TAGS]))
			$this->oldColumnsValues[BulkUploadResultPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [url] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setUrl($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::URL]))
			$this->oldColumnsValues[BulkUploadResultPeer::URL] = $this->url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url !== $v) {
			$this->url = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::URL;
		}

		return $this;
	} // setUrl()

	/**
	 * Set the value of [content_type] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setContentType($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::CONTENT_TYPE]))
			$this->oldColumnsValues[BulkUploadResultPeer::CONTENT_TYPE] = $this->content_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->content_type !== $v) {
			$this->content_type = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::CONTENT_TYPE;
		}

		return $this;
	} // setContentType()

	/**
	 * Set the value of [conversion_profile_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setConversionProfileId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::CONVERSION_PROFILE_ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::CONVERSION_PROFILE_ID] = $this->conversion_profile_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->conversion_profile_id !== $v) {
			$this->conversion_profile_id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::CONVERSION_PROFILE_ID;
		}

		return $this;
	} // setConversionProfileId()

	/**
	 * Set the value of [access_control_profile_id] column.
	 * 
	 * @param      int $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setAccessControlProfileId($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID]))
			$this->oldColumnsValues[BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID] = $this->access_control_profile_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->access_control_profile_id !== $v) {
			$this->access_control_profile_id = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID;
		}

		return $this;
	} // setAccessControlProfileId()

	/**
	 * Set the value of [category] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setCategory($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::CATEGORY]))
			$this->oldColumnsValues[BulkUploadResultPeer::CATEGORY] = $this->category;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->category !== $v) {
			$this->category = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::CATEGORY;
		}

		return $this;
	} // setCategory()

	/**
	 * Sets the value of [schedule_start_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setScheduleStartDate($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::SCHEDULE_START_DATE]))
			$this->oldColumnsValues[BulkUploadResultPeer::SCHEDULE_START_DATE] = $this->schedule_start_date;

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

		if ( $this->schedule_start_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->schedule_start_date !== null && $tmpDt = new DateTime($this->schedule_start_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->schedule_start_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BulkUploadResultPeer::SCHEDULE_START_DATE;
			}
		} // if either are not null

		return $this;
	} // setScheduleStartDate()

	/**
	 * Sets the value of [schedule_end_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setScheduleEndDate($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::SCHEDULE_END_DATE]))
			$this->oldColumnsValues[BulkUploadResultPeer::SCHEDULE_END_DATE] = $this->schedule_end_date;

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

		if ( $this->schedule_end_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->schedule_end_date !== null && $tmpDt = new DateTime($this->schedule_end_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->schedule_end_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = BulkUploadResultPeer::SCHEDULE_END_DATE;
			}
		} // if either are not null

		return $this;
	} // setScheduleEndDate()

	/**
	 * Set the value of [thumbnail_url] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setThumbnailUrl($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::THUMBNAIL_URL]))
			$this->oldColumnsValues[BulkUploadResultPeer::THUMBNAIL_URL] = $this->thumbnail_url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->thumbnail_url !== $v) {
			$this->thumbnail_url = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::THUMBNAIL_URL;
		}

		return $this;
	} // setThumbnailUrl()

	/**
	 * Set the value of [thumbnail_saved] column.
	 * 
	 * @param      boolean $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setThumbnailSaved($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::THUMBNAIL_SAVED]))
			$this->oldColumnsValues[BulkUploadResultPeer::THUMBNAIL_SAVED] = $this->thumbnail_saved;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->thumbnail_saved !== $v) {
			$this->thumbnail_saved = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::THUMBNAIL_SAVED;
		}

		return $this;
	} // setThumbnailSaved()

	/**
	 * Set the value of [partner_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setPartnerData($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::PARTNER_DATA]))
			$this->oldColumnsValues[BulkUploadResultPeer::PARTNER_DATA] = $this->partner_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_data !== $v) {
			$this->partner_data = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::PARTNER_DATA;
		}

		return $this;
	} // setPartnerData()

	/**
	 * Set the value of [error_description] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setErrorDescription($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::ERROR_DESCRIPTION]))
			$this->oldColumnsValues[BulkUploadResultPeer::ERROR_DESCRIPTION] = $this->error_description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->error_description !== $v) {
			$this->error_description = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::ERROR_DESCRIPTION;
		}

		return $this;
	} // setErrorDescription()

	/**
	 * Set the value of [plugins_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setPluginsData($v)
	{
		if(!isset($this->oldColumnsValues[BulkUploadResultPeer::PLUGINS_DATA]))
			$this->oldColumnsValues[BulkUploadResultPeer::PLUGINS_DATA] = $this->plugins_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->plugins_data !== $v) {
			$this->plugins_data = $v;
			$this->modifiedColumns[] = BulkUploadResultPeer::PLUGINS_DATA;
		}

		return $this;
	} // setPluginsData()

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
			$this->updated_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->bulk_upload_job_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->line_index = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->partner_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->entry_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->entry_status = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->row_data = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->title = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->description = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->tags = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->url = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->content_type = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->conversion_profile_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->access_control_profile_id = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->category = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->schedule_start_date = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->schedule_end_date = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->thumbnail_url = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->thumbnail_saved = ($row[$startcol + 20] !== null) ? (boolean) $row[$startcol + 20] : null;
			$this->partner_data = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->error_description = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->plugins_data = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = BulkUploadResultPeer::NUM_COLUMNS - BulkUploadResultPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating BulkUploadResult object", $e);
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
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = BulkUploadResultPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				BulkUploadResultPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				BulkUploadResultPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = BulkUploadResultPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = BulkUploadResultPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += BulkUploadResultPeer::doUpdate($this, $con);
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
		BulkUploadResultPeer::setUseCriteriaFilter(false);
		$this->reload();
		BulkUploadResultPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = BulkUploadResultPeer::doValidate($this, $columns)) !== true) {
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
		$pos = BulkUploadResultPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getUpdatedAt();
				break;
			case 3:
				return $this->getBulkUploadJobId();
				break;
			case 4:
				return $this->getLineIndex();
				break;
			case 5:
				return $this->getPartnerId();
				break;
			case 6:
				return $this->getEntryId();
				break;
			case 7:
				return $this->getEntryStatus();
				break;
			case 8:
				return $this->getRowData();
				break;
			case 9:
				return $this->getTitle();
				break;
			case 10:
				return $this->getDescription();
				break;
			case 11:
				return $this->getTags();
				break;
			case 12:
				return $this->getUrl();
				break;
			case 13:
				return $this->getContentType();
				break;
			case 14:
				return $this->getConversionProfileId();
				break;
			case 15:
				return $this->getAccessControlProfileId();
				break;
			case 16:
				return $this->getCategory();
				break;
			case 17:
				return $this->getScheduleStartDate();
				break;
			case 18:
				return $this->getScheduleEndDate();
				break;
			case 19:
				return $this->getThumbnailUrl();
				break;
			case 20:
				return $this->getThumbnailSaved();
				break;
			case 21:
				return $this->getPartnerData();
				break;
			case 22:
				return $this->getErrorDescription();
				break;
			case 23:
				return $this->getPluginsData();
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
		$keys = BulkUploadResultPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getUpdatedAt(),
			$keys[3] => $this->getBulkUploadJobId(),
			$keys[4] => $this->getLineIndex(),
			$keys[5] => $this->getPartnerId(),
			$keys[6] => $this->getEntryId(),
			$keys[7] => $this->getEntryStatus(),
			$keys[8] => $this->getRowData(),
			$keys[9] => $this->getTitle(),
			$keys[10] => $this->getDescription(),
			$keys[11] => $this->getTags(),
			$keys[12] => $this->getUrl(),
			$keys[13] => $this->getContentType(),
			$keys[14] => $this->getConversionProfileId(),
			$keys[15] => $this->getAccessControlProfileId(),
			$keys[16] => $this->getCategory(),
			$keys[17] => $this->getScheduleStartDate(),
			$keys[18] => $this->getScheduleEndDate(),
			$keys[19] => $this->getThumbnailUrl(),
			$keys[20] => $this->getThumbnailSaved(),
			$keys[21] => $this->getPartnerData(),
			$keys[22] => $this->getErrorDescription(),
			$keys[23] => $this->getPluginsData(),
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
		$pos = BulkUploadResultPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setUpdatedAt($value);
				break;
			case 3:
				$this->setBulkUploadJobId($value);
				break;
			case 4:
				$this->setLineIndex($value);
				break;
			case 5:
				$this->setPartnerId($value);
				break;
			case 6:
				$this->setEntryId($value);
				break;
			case 7:
				$this->setEntryStatus($value);
				break;
			case 8:
				$this->setRowData($value);
				break;
			case 9:
				$this->setTitle($value);
				break;
			case 10:
				$this->setDescription($value);
				break;
			case 11:
				$this->setTags($value);
				break;
			case 12:
				$this->setUrl($value);
				break;
			case 13:
				$this->setContentType($value);
				break;
			case 14:
				$this->setConversionProfileId($value);
				break;
			case 15:
				$this->setAccessControlProfileId($value);
				break;
			case 16:
				$this->setCategory($value);
				break;
			case 17:
				$this->setScheduleStartDate($value);
				break;
			case 18:
				$this->setScheduleEndDate($value);
				break;
			case 19:
				$this->setThumbnailUrl($value);
				break;
			case 20:
				$this->setThumbnailSaved($value);
				break;
			case 21:
				$this->setPartnerData($value);
				break;
			case 22:
				$this->setErrorDescription($value);
				break;
			case 23:
				$this->setPluginsData($value);
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
		$keys = BulkUploadResultPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUpdatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setBulkUploadJobId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setLineIndex($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPartnerId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEntryId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setEntryStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setRowData($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTitle($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDescription($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setTags($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUrl($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setContentType($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setConversionProfileId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setAccessControlProfileId($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setCategory($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setScheduleStartDate($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setScheduleEndDate($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setThumbnailUrl($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setThumbnailSaved($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setPartnerData($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setErrorDescription($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setPluginsData($arr[$keys[23]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(BulkUploadResultPeer::DATABASE_NAME);

		if ($this->isColumnModified(BulkUploadResultPeer::ID)) $criteria->add(BulkUploadResultPeer::ID, $this->id);
		if ($this->isColumnModified(BulkUploadResultPeer::CREATED_AT)) $criteria->add(BulkUploadResultPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(BulkUploadResultPeer::UPDATED_AT)) $criteria->add(BulkUploadResultPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID)) $criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $this->bulk_upload_job_id);
		if ($this->isColumnModified(BulkUploadResultPeer::LINE_INDEX)) $criteria->add(BulkUploadResultPeer::LINE_INDEX, $this->line_index);
		if ($this->isColumnModified(BulkUploadResultPeer::PARTNER_ID)) $criteria->add(BulkUploadResultPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(BulkUploadResultPeer::ENTRY_ID)) $criteria->add(BulkUploadResultPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(BulkUploadResultPeer::ENTRY_STATUS)) $criteria->add(BulkUploadResultPeer::ENTRY_STATUS, $this->entry_status);
		if ($this->isColumnModified(BulkUploadResultPeer::ROW_DATA)) $criteria->add(BulkUploadResultPeer::ROW_DATA, $this->row_data);
		if ($this->isColumnModified(BulkUploadResultPeer::TITLE)) $criteria->add(BulkUploadResultPeer::TITLE, $this->title);
		if ($this->isColumnModified(BulkUploadResultPeer::DESCRIPTION)) $criteria->add(BulkUploadResultPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(BulkUploadResultPeer::TAGS)) $criteria->add(BulkUploadResultPeer::TAGS, $this->tags);
		if ($this->isColumnModified(BulkUploadResultPeer::URL)) $criteria->add(BulkUploadResultPeer::URL, $this->url);
		if ($this->isColumnModified(BulkUploadResultPeer::CONTENT_TYPE)) $criteria->add(BulkUploadResultPeer::CONTENT_TYPE, $this->content_type);
		if ($this->isColumnModified(BulkUploadResultPeer::CONVERSION_PROFILE_ID)) $criteria->add(BulkUploadResultPeer::CONVERSION_PROFILE_ID, $this->conversion_profile_id);
		if ($this->isColumnModified(BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID)) $criteria->add(BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID, $this->access_control_profile_id);
		if ($this->isColumnModified(BulkUploadResultPeer::CATEGORY)) $criteria->add(BulkUploadResultPeer::CATEGORY, $this->category);
		if ($this->isColumnModified(BulkUploadResultPeer::SCHEDULE_START_DATE)) $criteria->add(BulkUploadResultPeer::SCHEDULE_START_DATE, $this->schedule_start_date);
		if ($this->isColumnModified(BulkUploadResultPeer::SCHEDULE_END_DATE)) $criteria->add(BulkUploadResultPeer::SCHEDULE_END_DATE, $this->schedule_end_date);
		if ($this->isColumnModified(BulkUploadResultPeer::THUMBNAIL_URL)) $criteria->add(BulkUploadResultPeer::THUMBNAIL_URL, $this->thumbnail_url);
		if ($this->isColumnModified(BulkUploadResultPeer::THUMBNAIL_SAVED)) $criteria->add(BulkUploadResultPeer::THUMBNAIL_SAVED, $this->thumbnail_saved);
		if ($this->isColumnModified(BulkUploadResultPeer::PARTNER_DATA)) $criteria->add(BulkUploadResultPeer::PARTNER_DATA, $this->partner_data);
		if ($this->isColumnModified(BulkUploadResultPeer::ERROR_DESCRIPTION)) $criteria->add(BulkUploadResultPeer::ERROR_DESCRIPTION, $this->error_description);
		if ($this->isColumnModified(BulkUploadResultPeer::PLUGINS_DATA)) $criteria->add(BulkUploadResultPeer::PLUGINS_DATA, $this->plugins_data);

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
		$criteria = new Criteria(BulkUploadResultPeer::DATABASE_NAME);

		$criteria->add(BulkUploadResultPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of BulkUploadResult (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setBulkUploadJobId($this->bulk_upload_job_id);

		$copyObj->setLineIndex($this->line_index);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setEntryStatus($this->entry_status);

		$copyObj->setRowData($this->row_data);

		$copyObj->setTitle($this->title);

		$copyObj->setDescription($this->description);

		$copyObj->setTags($this->tags);

		$copyObj->setUrl($this->url);

		$copyObj->setContentType($this->content_type);

		$copyObj->setConversionProfileId($this->conversion_profile_id);

		$copyObj->setAccessControlProfileId($this->access_control_profile_id);

		$copyObj->setCategory($this->category);

		$copyObj->setScheduleStartDate($this->schedule_start_date);

		$copyObj->setScheduleEndDate($this->schedule_end_date);

		$copyObj->setThumbnailUrl($this->thumbnail_url);

		$copyObj->setThumbnailSaved($this->thumbnail_saved);

		$copyObj->setPartnerData($this->partner_data);

		$copyObj->setErrorDescription($this->error_description);

		$copyObj->setPluginsData($this->plugins_data);


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
	 * @return     BulkUploadResult Clone of current object.
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
	 * @var     BulkUploadResult Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      BulkUploadResult $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(BulkUploadResult $copiedFrom)
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
	 * @return     BulkUploadResultPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new BulkUploadResultPeer();
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

} // BaseBulkUploadResult
