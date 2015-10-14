<?php

/**
 * Base class that represents a row from the 'drop_folder_file' table.
 *
 * 
 *
 * @package plugins.dropFolder
 * @subpackage model.om
 */
abstract class BaseDropFolderFile extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DropFolderFilePeer
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
	 * The value for the drop_folder_id field.
	 * @var        int
	 */
	protected $drop_folder_id;

	/**
	 * The value for the file_name field.
	 * @var        string
	 */
	protected $file_name;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the file_size field.
	 * @var        int
	 */
	protected $file_size;

	/**
	 * The value for the file_size_last_set_at field.
	 * @var        string
	 */
	protected $file_size_last_set_at;

	/**
	 * The value for the error_code field.
	 * @var        int
	 */
	protected $error_code;

	/**
	 * The value for the error_description field.
	 * @var        string
	 */
	protected $error_description;

	/**
	 * The value for the parsed_slug field.
	 * @var        string
	 */
	protected $parsed_slug;

	/**
	 * The value for the parsed_flavor field.
	 * @var        string
	 */
	protected $parsed_flavor;

	/**
	 * The value for the lead_drop_folder_file_id field.
	 * @var        int
	 */
	protected $lead_drop_folder_file_id;

	/**
	 * The value for the deleted_drop_folder_file_id field.
	 * @var        int
	 */
	protected $deleted_drop_folder_file_id;

	/**
	 * The value for the md5_file_name field.
	 * @var        string
	 */
	protected $md5_file_name;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

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
	 * The value for the upload_start_detected_at field.
	 * @var        string
	 */
	protected $upload_start_detected_at;

	/**
	 * The value for the upload_end_detected_at field.
	 * @var        string
	 */
	protected $upload_end_detected_at;

	/**
	 * The value for the import_started_at field.
	 * @var        string
	 */
	protected $import_started_at;

	/**
	 * The value for the import_ended_at field.
	 * @var        string
	 */
	protected $import_ended_at;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [drop_folder_id] column value.
	 * 
	 * @return     int
	 */
	public function getDropFolderId()
	{
		return $this->drop_folder_id;
	}

	/**
	 * Get the [file_name] column value.
	 * 
	 * @return     string
	 */
	public function getFileName()
	{
		return $this->file_name;
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
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
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
	 * Get the [optionally formatted] temporal [file_size_last_set_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFileSizeLastSetAt($format = 'Y-m-d H:i:s')
	{
		if ($this->file_size_last_set_at === null) {
			return null;
		}


		if ($this->file_size_last_set_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->file_size_last_set_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->file_size_last_set_at, true), $x);
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
	 * Get the [error_code] column value.
	 * 
	 * @return     int
	 */
	public function getErrorCode()
	{
		return $this->error_code;
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
	 * Get the [parsed_slug] column value.
	 * 
	 * @return     string
	 */
	public function getParsedSlug()
	{
		return $this->parsed_slug;
	}

	/**
	 * Get the [parsed_flavor] column value.
	 * 
	 * @return     string
	 */
	public function getParsedFlavor()
	{
		return $this->parsed_flavor;
	}

	/**
	 * Get the [lead_drop_folder_file_id] column value.
	 * 
	 * @return     int
	 */
	public function getLeadDropFolderFileId()
	{
		return $this->lead_drop_folder_file_id;
	}

	/**
	 * Get the [deleted_drop_folder_file_id] column value.
	 * 
	 * @return     int
	 */
	public function getDeletedDropFolderFileId()
	{
		return $this->deleted_drop_folder_file_id;
	}

	/**
	 * Get the [md5_file_name] column value.
	 * 
	 * @return     string
	 */
	public function getMd5FileName()
	{
		return $this->md5_file_name;
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
	 * Get the [optionally formatted] temporal [upload_start_detected_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUploadStartDetectedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->upload_start_detected_at === null) {
			return null;
		}


		if ($this->upload_start_detected_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->upload_start_detected_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->upload_start_detected_at, true), $x);
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
	 * Get the [optionally formatted] temporal [upload_end_detected_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUploadEndDetectedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->upload_end_detected_at === null) {
			return null;
		}


		if ($this->upload_end_detected_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->upload_end_detected_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->upload_end_detected_at, true), $x);
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
	 * Get the [optionally formatted] temporal [import_started_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getImportStartedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->import_started_at === null) {
			return null;
		}


		if ($this->import_started_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->import_started_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->import_started_at, true), $x);
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
	 * Get the [optionally formatted] temporal [import_ended_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getImportEndedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->import_ended_at === null) {
			return null;
		}


		if ($this->import_ended_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->import_ended_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->import_ended_at, true), $x);
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::ID]))
			$this->oldColumnsValues[DropFolderFilePeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::PARTNER_ID]))
			$this->oldColumnsValues[DropFolderFilePeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [drop_folder_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setDropFolderId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::DROP_FOLDER_ID]))
			$this->oldColumnsValues[DropFolderFilePeer::DROP_FOLDER_ID] = $this->drop_folder_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->drop_folder_id !== $v) {
			$this->drop_folder_id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::DROP_FOLDER_ID;
		}

		return $this;
	} // setDropFolderId()

	/**
	 * Set the value of [file_name] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setFileName($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::FILE_NAME]))
			$this->oldColumnsValues[DropFolderFilePeer::FILE_NAME] = $this->file_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_name !== $v) {
			$this->file_name = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::FILE_NAME;
		}

		return $this;
	} // setFileName()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::TYPE]))
			$this->oldColumnsValues[DropFolderFilePeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::STATUS]))
			$this->oldColumnsValues[DropFolderFilePeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setFileSize($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::FILE_SIZE]))
			$this->oldColumnsValues[DropFolderFilePeer::FILE_SIZE] = $this->file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::FILE_SIZE;
		}

		return $this;
	} // setFileSize()

	/**
	 * Sets the value of [file_size_last_set_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setFileSizeLastSetAt($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::FILE_SIZE_LAST_SET_AT]))
			$this->oldColumnsValues[DropFolderFilePeer::FILE_SIZE_LAST_SET_AT] = $this->file_size_last_set_at;

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

		if ( $this->file_size_last_set_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->file_size_last_set_at !== null && $tmpDt = new DateTime($this->file_size_last_set_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->file_size_last_set_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = DropFolderFilePeer::FILE_SIZE_LAST_SET_AT;
			}
		} // if either are not null

		return $this;
	} // setFileSizeLastSetAt()

	/**
	 * Set the value of [error_code] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setErrorCode($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::ERROR_CODE]))
			$this->oldColumnsValues[DropFolderFilePeer::ERROR_CODE] = $this->error_code;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->error_code !== $v) {
			$this->error_code = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::ERROR_CODE;
		}

		return $this;
	} // setErrorCode()

	/**
	 * Set the value of [error_description] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setErrorDescription($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::ERROR_DESCRIPTION]))
			$this->oldColumnsValues[DropFolderFilePeer::ERROR_DESCRIPTION] = $this->error_description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->error_description !== $v) {
			$this->error_description = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::ERROR_DESCRIPTION;
		}

		return $this;
	} // setErrorDescription()

	/**
	 * Set the value of [parsed_slug] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setParsedSlug($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::PARSED_SLUG]))
			$this->oldColumnsValues[DropFolderFilePeer::PARSED_SLUG] = $this->parsed_slug;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->parsed_slug !== $v) {
			$this->parsed_slug = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::PARSED_SLUG;
		}

		return $this;
	} // setParsedSlug()

	/**
	 * Set the value of [parsed_flavor] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setParsedFlavor($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::PARSED_FLAVOR]))
			$this->oldColumnsValues[DropFolderFilePeer::PARSED_FLAVOR] = $this->parsed_flavor;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->parsed_flavor !== $v) {
			$this->parsed_flavor = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::PARSED_FLAVOR;
		}

		return $this;
	} // setParsedFlavor()

	/**
	 * Set the value of [lead_drop_folder_file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setLeadDropFolderFileId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID]))
			$this->oldColumnsValues[DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID] = $this->lead_drop_folder_file_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lead_drop_folder_file_id !== $v) {
			$this->lead_drop_folder_file_id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID;
		}

		return $this;
	} // setLeadDropFolderFileId()

	/**
	 * Set the value of [deleted_drop_folder_file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setDeletedDropFolderFileId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID]))
			$this->oldColumnsValues[DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID] = $this->deleted_drop_folder_file_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->deleted_drop_folder_file_id !== $v) {
			$this->deleted_drop_folder_file_id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID;
		}

		return $this;
	} // setDeletedDropFolderFileId()

	/**
	 * Set the value of [md5_file_name] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setMd5FileName($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::MD5_FILE_NAME]))
			$this->oldColumnsValues[DropFolderFilePeer::MD5_FILE_NAME] = $this->md5_file_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->md5_file_name !== $v) {
			$this->md5_file_name = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::MD5_FILE_NAME;
		}

		return $this;
	} // setMd5FileName()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::ENTRY_ID]))
			$this->oldColumnsValues[DropFolderFilePeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
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
				$this->modifiedColumns[] = DropFolderFilePeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
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
				$this->modifiedColumns[] = DropFolderFilePeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [upload_start_detected_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setUploadStartDetectedAt($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::UPLOAD_START_DETECTED_AT]))
			$this->oldColumnsValues[DropFolderFilePeer::UPLOAD_START_DETECTED_AT] = $this->upload_start_detected_at;

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

		if ( $this->upload_start_detected_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->upload_start_detected_at !== null && $tmpDt = new DateTime($this->upload_start_detected_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->upload_start_detected_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = DropFolderFilePeer::UPLOAD_START_DETECTED_AT;
			}
		} // if either are not null

		return $this;
	} // setUploadStartDetectedAt()

	/**
	 * Sets the value of [upload_end_detected_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setUploadEndDetectedAt($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::UPLOAD_END_DETECTED_AT]))
			$this->oldColumnsValues[DropFolderFilePeer::UPLOAD_END_DETECTED_AT] = $this->upload_end_detected_at;

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

		if ( $this->upload_end_detected_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->upload_end_detected_at !== null && $tmpDt = new DateTime($this->upload_end_detected_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->upload_end_detected_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = DropFolderFilePeer::UPLOAD_END_DETECTED_AT;
			}
		} // if either are not null

		return $this;
	} // setUploadEndDetectedAt()

	/**
	 * Sets the value of [import_started_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setImportStartedAt($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::IMPORT_STARTED_AT]))
			$this->oldColumnsValues[DropFolderFilePeer::IMPORT_STARTED_AT] = $this->import_started_at;

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

		if ( $this->import_started_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->import_started_at !== null && $tmpDt = new DateTime($this->import_started_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->import_started_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = DropFolderFilePeer::IMPORT_STARTED_AT;
			}
		} // if either are not null

		return $this;
	} // setImportStartedAt()

	/**
	 * Sets the value of [import_ended_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setImportEndedAt($v)
	{
		if(!isset($this->oldColumnsValues[DropFolderFilePeer::IMPORT_ENDED_AT]))
			$this->oldColumnsValues[DropFolderFilePeer::IMPORT_ENDED_AT] = $this->import_ended_at;

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

		if ( $this->import_ended_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->import_ended_at !== null && $tmpDt = new DateTime($this->import_ended_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->import_ended_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = DropFolderFilePeer::IMPORT_ENDED_AT;
			}
		} // if either are not null

		return $this;
	} // setImportEndedAt()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     DropFolderFile The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = DropFolderFilePeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

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
			$this->drop_folder_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->file_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->type = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->status = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->file_size = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->file_size_last_set_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->error_code = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->error_description = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->parsed_slug = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->parsed_flavor = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->lead_drop_folder_file_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->deleted_drop_folder_file_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->md5_file_name = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->entry_id = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->created_at = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->updated_at = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->upload_start_detected_at = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->upload_end_detected_at = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->import_started_at = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->import_ended_at = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->custom_data = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = DropFolderFilePeer::NUM_COLUMNS - DropFolderFilePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DropFolderFile object", $e);
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
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		DropFolderFilePeer::setUseCriteriaFilter(false);
		$stmt = DropFolderFilePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		DropFolderFilePeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				DropFolderFilePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
			
			if (!$ret || !$this->isModified()) {
				$con->commit();
				return 0;
			}
			
			for ($retries = 1; $retries < KalturaPDO::SAVE_MAX_RETRIES; $retries++)
			{
               $affectedRows = $this->doSave($con);
                if ($affectedRows || !$this->isColumnModified(DropFolderFilePeer::CUSTOM_DATA)) //ask if custom_data wasn't modified to avoid retry with atomic column 
                	break;

                KalturaLog::info("was unable to save! retrying for the $retries time");
                $criteria = $this->buildPkeyCriteria();
				$criteria->addSelectColumn(DropFolderFilePeer::CUSTOM_DATA);
                $stmt = DropFolderFilePeer::doSelectStmt($criteria, $con);
                $cutsomDataArr = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $newCustomData = $cutsomDataArr[0];
                
                $this->custom_data_md5 = md5($newCustomData);

                $valuesToChangeTo = $this->m_custom_data->toArray();
				$this->m_custom_data = myCustomData::fromString($newCustomData); 

				//set custom data column values we wanted to change to
			 	foreach ($this->oldCustomDataValues as $namespace => $namespaceValues){
                	foreach($namespaceValues as $name => $oldValue)
					{
						if ($namespace)
						{
							$newValue = $valuesToChangeTo[$namespace][$name];
						}
						else
						{ 
							$newValue = $valuesToChangeTo[$name];
						}
					 
						$this->putInCustomData($name, $newValue, $namespace);
					}
                   }
                   
				$this->setCustomData($this->m_custom_data->toString());
			}

			if ($isInsert) {
				$this->postInsert($con);
			} else {
				$this->postUpdate($con);
			}
			$this->postSave($con);
			DropFolderFilePeer::addInstanceToPool($this);
			
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
				$this->modifiedColumns[] = DropFolderFilePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DropFolderFilePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = DropFolderFilePeer::doUpdate($this, $con);
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


			if (($retval = DropFolderFilePeer::doValidate($this, $columns)) !== true) {
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
		$pos = DropFolderFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDropFolderId();
				break;
			case 3:
				return $this->getFileName();
				break;
			case 4:
				return $this->getType();
				break;
			case 5:
				return $this->getStatus();
				break;
			case 6:
				return $this->getFileSize();
				break;
			case 7:
				return $this->getFileSizeLastSetAt();
				break;
			case 8:
				return $this->getErrorCode();
				break;
			case 9:
				return $this->getErrorDescription();
				break;
			case 10:
				return $this->getParsedSlug();
				break;
			case 11:
				return $this->getParsedFlavor();
				break;
			case 12:
				return $this->getLeadDropFolderFileId();
				break;
			case 13:
				return $this->getDeletedDropFolderFileId();
				break;
			case 14:
				return $this->getMd5FileName();
				break;
			case 15:
				return $this->getEntryId();
				break;
			case 16:
				return $this->getCreatedAt();
				break;
			case 17:
				return $this->getUpdatedAt();
				break;
			case 18:
				return $this->getUploadStartDetectedAt();
				break;
			case 19:
				return $this->getUploadEndDetectedAt();
				break;
			case 20:
				return $this->getImportStartedAt();
				break;
			case 21:
				return $this->getImportEndedAt();
				break;
			case 22:
				return $this->getCustomData();
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
		$keys = DropFolderFilePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getDropFolderId(),
			$keys[3] => $this->getFileName(),
			$keys[4] => $this->getType(),
			$keys[5] => $this->getStatus(),
			$keys[6] => $this->getFileSize(),
			$keys[7] => $this->getFileSizeLastSetAt(),
			$keys[8] => $this->getErrorCode(),
			$keys[9] => $this->getErrorDescription(),
			$keys[10] => $this->getParsedSlug(),
			$keys[11] => $this->getParsedFlavor(),
			$keys[12] => $this->getLeadDropFolderFileId(),
			$keys[13] => $this->getDeletedDropFolderFileId(),
			$keys[14] => $this->getMd5FileName(),
			$keys[15] => $this->getEntryId(),
			$keys[16] => $this->getCreatedAt(),
			$keys[17] => $this->getUpdatedAt(),
			$keys[18] => $this->getUploadStartDetectedAt(),
			$keys[19] => $this->getUploadEndDetectedAt(),
			$keys[20] => $this->getImportStartedAt(),
			$keys[21] => $this->getImportEndedAt(),
			$keys[22] => $this->getCustomData(),
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
		$pos = DropFolderFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDropFolderId($value);
				break;
			case 3:
				$this->setFileName($value);
				break;
			case 4:
				$this->setType($value);
				break;
			case 5:
				$this->setStatus($value);
				break;
			case 6:
				$this->setFileSize($value);
				break;
			case 7:
				$this->setFileSizeLastSetAt($value);
				break;
			case 8:
				$this->setErrorCode($value);
				break;
			case 9:
				$this->setErrorDescription($value);
				break;
			case 10:
				$this->setParsedSlug($value);
				break;
			case 11:
				$this->setParsedFlavor($value);
				break;
			case 12:
				$this->setLeadDropFolderFileId($value);
				break;
			case 13:
				$this->setDeletedDropFolderFileId($value);
				break;
			case 14:
				$this->setMd5FileName($value);
				break;
			case 15:
				$this->setEntryId($value);
				break;
			case 16:
				$this->setCreatedAt($value);
				break;
			case 17:
				$this->setUpdatedAt($value);
				break;
			case 18:
				$this->setUploadStartDetectedAt($value);
				break;
			case 19:
				$this->setUploadEndDetectedAt($value);
				break;
			case 20:
				$this->setImportStartedAt($value);
				break;
			case 21:
				$this->setImportEndedAt($value);
				break;
			case 22:
				$this->setCustomData($value);
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
		$keys = DropFolderFilePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDropFolderId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setFileName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setStatus($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFileSize($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFileSizeLastSetAt($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setErrorCode($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setErrorDescription($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setParsedSlug($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setParsedFlavor($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setLeadDropFolderFileId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDeletedDropFolderFileId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setMd5FileName($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setEntryId($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setCreatedAt($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setUpdatedAt($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setUploadStartDetectedAt($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setUploadEndDetectedAt($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setImportStartedAt($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setImportEndedAt($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setCustomData($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DropFolderFilePeer::DATABASE_NAME);

		if ($this->isColumnModified(DropFolderFilePeer::ID)) $criteria->add(DropFolderFilePeer::ID, $this->id);
		if ($this->isColumnModified(DropFolderFilePeer::PARTNER_ID)) $criteria->add(DropFolderFilePeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(DropFolderFilePeer::DROP_FOLDER_ID)) $criteria->add(DropFolderFilePeer::DROP_FOLDER_ID, $this->drop_folder_id);
		if ($this->isColumnModified(DropFolderFilePeer::FILE_NAME)) $criteria->add(DropFolderFilePeer::FILE_NAME, $this->file_name);
		if ($this->isColumnModified(DropFolderFilePeer::TYPE)) $criteria->add(DropFolderFilePeer::TYPE, $this->type);
		if ($this->isColumnModified(DropFolderFilePeer::STATUS)) $criteria->add(DropFolderFilePeer::STATUS, $this->status);
		if ($this->isColumnModified(DropFolderFilePeer::FILE_SIZE)) $criteria->add(DropFolderFilePeer::FILE_SIZE, $this->file_size);
		if ($this->isColumnModified(DropFolderFilePeer::FILE_SIZE_LAST_SET_AT)) $criteria->add(DropFolderFilePeer::FILE_SIZE_LAST_SET_AT, $this->file_size_last_set_at);
		if ($this->isColumnModified(DropFolderFilePeer::ERROR_CODE)) $criteria->add(DropFolderFilePeer::ERROR_CODE, $this->error_code);
		if ($this->isColumnModified(DropFolderFilePeer::ERROR_DESCRIPTION)) $criteria->add(DropFolderFilePeer::ERROR_DESCRIPTION, $this->error_description);
		if ($this->isColumnModified(DropFolderFilePeer::PARSED_SLUG)) $criteria->add(DropFolderFilePeer::PARSED_SLUG, $this->parsed_slug);
		if ($this->isColumnModified(DropFolderFilePeer::PARSED_FLAVOR)) $criteria->add(DropFolderFilePeer::PARSED_FLAVOR, $this->parsed_flavor);
		if ($this->isColumnModified(DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID)) $criteria->add(DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID, $this->lead_drop_folder_file_id);
		if ($this->isColumnModified(DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID)) $criteria->add(DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID, $this->deleted_drop_folder_file_id);
		if ($this->isColumnModified(DropFolderFilePeer::MD5_FILE_NAME)) $criteria->add(DropFolderFilePeer::MD5_FILE_NAME, $this->md5_file_name);
		if ($this->isColumnModified(DropFolderFilePeer::ENTRY_ID)) $criteria->add(DropFolderFilePeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(DropFolderFilePeer::CREATED_AT)) $criteria->add(DropFolderFilePeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(DropFolderFilePeer::UPDATED_AT)) $criteria->add(DropFolderFilePeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(DropFolderFilePeer::UPLOAD_START_DETECTED_AT)) $criteria->add(DropFolderFilePeer::UPLOAD_START_DETECTED_AT, $this->upload_start_detected_at);
		if ($this->isColumnModified(DropFolderFilePeer::UPLOAD_END_DETECTED_AT)) $criteria->add(DropFolderFilePeer::UPLOAD_END_DETECTED_AT, $this->upload_end_detected_at);
		if ($this->isColumnModified(DropFolderFilePeer::IMPORT_STARTED_AT)) $criteria->add(DropFolderFilePeer::IMPORT_STARTED_AT, $this->import_started_at);
		if ($this->isColumnModified(DropFolderFilePeer::IMPORT_ENDED_AT)) $criteria->add(DropFolderFilePeer::IMPORT_ENDED_AT, $this->import_ended_at);
		if ($this->isColumnModified(DropFolderFilePeer::CUSTOM_DATA)) $criteria->add(DropFolderFilePeer::CUSTOM_DATA, $this->custom_data);

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
		$criteria = new Criteria(DropFolderFilePeer::DATABASE_NAME);

		$criteria->add(DropFolderFilePeer::ID, $this->id);
		
		if($this->alreadyInSave)
		{
			if ($this->isColumnModified(DropFolderFilePeer::CUSTOM_DATA))
			{
				if (!is_null($this->custom_data_md5))
					$criteria->add(DropFolderFilePeer::CUSTOM_DATA, "MD5(cast(" . DropFolderFilePeer::CUSTOM_DATA . " as char character set latin1)) = '$this->custom_data_md5'", Criteria::CUSTOM);
					//casting to latin char set to avoid mysql and php md5 difference
				else 
					$criteria->add(DropFolderFilePeer::CUSTOM_DATA, NULL, Criteria::ISNULL);
			}
			
			if (count($this->modifiedColumns) == 2 && $this->isColumnModified(DropFolderFilePeer::UPDATED_AT))
			{
				$theModifiedColumn = null;
				foreach($this->modifiedColumns as $modifiedColumn)
					if($modifiedColumn != DropFolderFilePeer::UPDATED_AT)
						$theModifiedColumn = $modifiedColumn;
						
				$atomicColumns = DropFolderFilePeer::getAtomicColumns();
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
	 * @param      object $copyObj An object of DropFolderFile (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDropFolderId($this->drop_folder_id);

		$copyObj->setFileName($this->file_name);

		$copyObj->setType($this->type);

		$copyObj->setStatus($this->status);

		$copyObj->setFileSize($this->file_size);

		$copyObj->setFileSizeLastSetAt($this->file_size_last_set_at);

		$copyObj->setErrorCode($this->error_code);

		$copyObj->setErrorDescription($this->error_description);

		$copyObj->setParsedSlug($this->parsed_slug);

		$copyObj->setParsedFlavor($this->parsed_flavor);

		$copyObj->setLeadDropFolderFileId($this->lead_drop_folder_file_id);

		$copyObj->setDeletedDropFolderFileId($this->deleted_drop_folder_file_id);

		$copyObj->setMd5FileName($this->md5_file_name);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setUploadStartDetectedAt($this->upload_start_detected_at);

		$copyObj->setUploadEndDetectedAt($this->upload_end_detected_at);

		$copyObj->setImportStartedAt($this->import_started_at);

		$copyObj->setImportEndedAt($this->import_ended_at);

		$copyObj->setCustomData($this->custom_data);


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
	 * @return     DropFolderFile Clone of current object.
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
	 * @var     DropFolderFile Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      DropFolderFile $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(DropFolderFile $copiedFrom)
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
	 * @return     DropFolderFilePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DropFolderFilePeer();
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
	 * The md5 value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data_md5;

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
		$customData = $this->getCustomDataObj();
		
		$currentNamespace = '';
		if($namespace)
			$currentNamespace = $namespace;
			
		if(!isset($this->oldCustomDataValues[$currentNamespace]))
			$this->oldCustomDataValues[$currentNamespace] = array();
		if(!isset($this->oldCustomDataValues[$currentNamespace][$name]))
			$this->oldCustomDataValues[$currentNamespace][$name] = $customData->get($name, $namespace);
		
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
			$this->custom_data_md5 = is_null($this->custom_data) ? null : md5($this->custom_data);
			$this->setCustomData( $this->m_custom_data->toString() );
		}
	}
	
	/* ---------------------- CustomData functions ------------------------- */
	
} // BaseDropFolderFile