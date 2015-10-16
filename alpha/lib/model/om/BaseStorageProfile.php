<?php

/**
 * Base class that represents a row from the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseStorageProfile extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        StorageProfilePeer
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
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

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
	 * The value for the desciption field.
	 * @var        string
	 */
	protected $desciption;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the protocol field.
	 * @var        int
	 */
	protected $protocol;

	/**
	 * The value for the storage_url field.
	 * @var        string
	 */
	protected $storage_url;

	/**
	 * The value for the storage_base_dir field.
	 * @var        string
	 */
	protected $storage_base_dir;

	/**
	 * The value for the storage_username field.
	 * @var        string
	 */
	protected $storage_username;

	/**
	 * The value for the storage_password field.
	 * @var        string
	 */
	protected $storage_password;

	/**
	 * The value for the storage_ftp_passive_mode field.
	 * @var        int
	 */
	protected $storage_ftp_passive_mode;

	/**
	 * The value for the min_file_size field.
	 * @var        int
	 */
	protected $min_file_size;

	/**
	 * The value for the max_file_size field.
	 * @var        int
	 */
	protected $max_file_size;

	/**
	 * The value for the flavor_params_ids field.
	 * @var        string
	 */
	protected $flavor_params_ids;

	/**
	 * The value for the max_concurrent_connections field.
	 * @var        int
	 */
	protected $max_concurrent_connections;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the path_manager_class field.
	 * @var        string
	 */
	protected $path_manager_class;

	/**
	 * The value for the delivery_priority field.
	 * @var        int
	 */
	protected $delivery_priority;

	/**
	 * The value for the delivery_status field.
	 * @var        int
	 */
	protected $delivery_status;

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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
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
	 * Get the [desciption] column value.
	 * 
	 * @return     string
	 */
	public function getDesciption()
	{
		return $this->desciption;
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
	 * Get the [protocol] column value.
	 * 
	 * @return     int
	 */
	public function getProtocol()
	{
		return $this->protocol;
	}

	/**
	 * Get the [storage_url] column value.
	 * 
	 * @return     string
	 */
	public function getStorageUrl()
	{
		return $this->storage_url;
	}

	/**
	 * Get the [storage_base_dir] column value.
	 * 
	 * @return     string
	 */
	public function getStorageBaseDir()
	{
		return $this->storage_base_dir;
	}

	/**
	 * Get the [storage_username] column value.
	 * 
	 * @return     string
	 */
	public function getStorageUsername()
	{
		return $this->storage_username;
	}

	/**
	 * Get the [storage_password] column value.
	 * 
	 * @return     string
	 */
	public function getStoragePassword()
	{
		return $this->storage_password;
	}

	/**
	 * Get the [storage_ftp_passive_mode] column value.
	 * 
	 * @return     int
	 */
	public function getStorageFtpPassiveMode()
	{
		return $this->storage_ftp_passive_mode;
	}

	/**
	 * Get the [min_file_size] column value.
	 * 
	 * @return     int
	 */
	public function getMinFileSize()
	{
		return $this->min_file_size;
	}

	/**
	 * Get the [max_file_size] column value.
	 * 
	 * @return     int
	 */
	public function getMaxFileSize()
	{
		return $this->max_file_size;
	}

	/**
	 * Get the [flavor_params_ids] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorParamsIds()
	{
		return $this->flavor_params_ids;
	}

	/**
	 * Get the [max_concurrent_connections] column value.
	 * 
	 * @return     int
	 */
	public function getMaxConcurrentConnections()
	{
		return $this->max_concurrent_connections;
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
	 * Get the [path_manager_class] column value.
	 * 
	 * @return     string
	 */
	public function getPathManagerClass()
	{
		return $this->path_manager_class;
	}

	/**
	 * Get the [delivery_priority] column value.
	 * 
	 * @return     int
	 */
	public function getDeliveryPriority()
	{
		return $this->delivery_priority;
	}

	/**
	 * Get the [delivery_status] column value.
	 * 
	 * @return     int
	 */
	public function getDeliveryStatus()
	{
		return $this->delivery_status;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::ID]))
			$this->oldColumnsValues[StorageProfilePeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = StorageProfilePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     StorageProfile The current object (for fluent API support)
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
				$this->modifiedColumns[] = StorageProfilePeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     StorageProfile The current object (for fluent API support)
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
				$this->modifiedColumns[] = StorageProfilePeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::PARTNER_ID]))
			$this->oldColumnsValues[StorageProfilePeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = StorageProfilePeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::NAME]))
			$this->oldColumnsValues[StorageProfilePeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = StorageProfilePeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setSystemName($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::SYSTEM_NAME]))
			$this->oldColumnsValues[StorageProfilePeer::SYSTEM_NAME] = $this->system_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->system_name !== $v) {
			$this->system_name = $v;
			$this->modifiedColumns[] = StorageProfilePeer::SYSTEM_NAME;
		}

		return $this;
	} // setSystemName()

	/**
	 * Set the value of [desciption] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setDesciption($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::DESCIPTION]))
			$this->oldColumnsValues[StorageProfilePeer::DESCIPTION] = $this->desciption;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->desciption !== $v) {
			$this->desciption = $v;
			$this->modifiedColumns[] = StorageProfilePeer::DESCIPTION;
		}

		return $this;
	} // setDesciption()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STATUS]))
			$this->oldColumnsValues[StorageProfilePeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [protocol] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setProtocol($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::PROTOCOL]))
			$this->oldColumnsValues[StorageProfilePeer::PROTOCOL] = $this->protocol;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->protocol !== $v) {
			$this->protocol = $v;
			$this->modifiedColumns[] = StorageProfilePeer::PROTOCOL;
		}

		return $this;
	} // setProtocol()

	/**
	 * Set the value of [storage_url] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStorageUrl($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STORAGE_URL]))
			$this->oldColumnsValues[StorageProfilePeer::STORAGE_URL] = $this->storage_url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->storage_url !== $v) {
			$this->storage_url = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STORAGE_URL;
		}

		return $this;
	} // setStorageUrl()

	/**
	 * Set the value of [storage_base_dir] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStorageBaseDir($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STORAGE_BASE_DIR]))
			$this->oldColumnsValues[StorageProfilePeer::STORAGE_BASE_DIR] = $this->storage_base_dir;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->storage_base_dir !== $v) {
			$this->storage_base_dir = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STORAGE_BASE_DIR;
		}

		return $this;
	} // setStorageBaseDir()

	/**
	 * Set the value of [storage_username] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStorageUsername($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STORAGE_USERNAME]))
			$this->oldColumnsValues[StorageProfilePeer::STORAGE_USERNAME] = $this->storage_username;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->storage_username !== $v) {
			$this->storage_username = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STORAGE_USERNAME;
		}

		return $this;
	} // setStorageUsername()

	/**
	 * Set the value of [storage_password] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStoragePassword($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STORAGE_PASSWORD]))
			$this->oldColumnsValues[StorageProfilePeer::STORAGE_PASSWORD] = $this->storage_password;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->storage_password !== $v) {
			$this->storage_password = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STORAGE_PASSWORD;
		}

		return $this;
	} // setStoragePassword()

	/**
	 * Set the value of [storage_ftp_passive_mode] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setStorageFtpPassiveMode($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE]))
			$this->oldColumnsValues[StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE] = $this->storage_ftp_passive_mode;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->storage_ftp_passive_mode !== $v) {
			$this->storage_ftp_passive_mode = $v;
			$this->modifiedColumns[] = StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE;
		}

		return $this;
	} // setStorageFtpPassiveMode()

	/**
	 * Set the value of [min_file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setMinFileSize($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::MIN_FILE_SIZE]))
			$this->oldColumnsValues[StorageProfilePeer::MIN_FILE_SIZE] = $this->min_file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->min_file_size !== $v) {
			$this->min_file_size = $v;
			$this->modifiedColumns[] = StorageProfilePeer::MIN_FILE_SIZE;
		}

		return $this;
	} // setMinFileSize()

	/**
	 * Set the value of [max_file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setMaxFileSize($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::MAX_FILE_SIZE]))
			$this->oldColumnsValues[StorageProfilePeer::MAX_FILE_SIZE] = $this->max_file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->max_file_size !== $v) {
			$this->max_file_size = $v;
			$this->modifiedColumns[] = StorageProfilePeer::MAX_FILE_SIZE;
		}

		return $this;
	} // setMaxFileSize()

	/**
	 * Set the value of [flavor_params_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setFlavorParamsIds($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::FLAVOR_PARAMS_IDS]))
			$this->oldColumnsValues[StorageProfilePeer::FLAVOR_PARAMS_IDS] = $this->flavor_params_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_params_ids !== $v) {
			$this->flavor_params_ids = $v;
			$this->modifiedColumns[] = StorageProfilePeer::FLAVOR_PARAMS_IDS;
		}

		return $this;
	} // setFlavorParamsIds()

	/**
	 * Set the value of [max_concurrent_connections] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setMaxConcurrentConnections($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS]))
			$this->oldColumnsValues[StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS] = $this->max_concurrent_connections;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->max_concurrent_connections !== $v) {
			$this->max_concurrent_connections = $v;
			$this->modifiedColumns[] = StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS;
		}

		return $this;
	} // setMaxConcurrentConnections()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = StorageProfilePeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [path_manager_class] column.
	 * 
	 * @param      string $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setPathManagerClass($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::PATH_MANAGER_CLASS]))
			$this->oldColumnsValues[StorageProfilePeer::PATH_MANAGER_CLASS] = $this->path_manager_class;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->path_manager_class !== $v) {
			$this->path_manager_class = $v;
			$this->modifiedColumns[] = StorageProfilePeer::PATH_MANAGER_CLASS;
		}

		return $this;
	} // setPathManagerClass()

	/**
	 * Set the value of [delivery_priority] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setDeliveryPriority($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::DELIVERY_PRIORITY]))
			$this->oldColumnsValues[StorageProfilePeer::DELIVERY_PRIORITY] = $this->delivery_priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->delivery_priority !== $v) {
			$this->delivery_priority = $v;
			$this->modifiedColumns[] = StorageProfilePeer::DELIVERY_PRIORITY;
		}

		return $this;
	} // setDeliveryPriority()

	/**
	 * Set the value of [delivery_status] column.
	 * 
	 * @param      int $v new value
	 * @return     StorageProfile The current object (for fluent API support)
	 */
	public function setDeliveryStatus($v)
	{
		if(!isset($this->oldColumnsValues[StorageProfilePeer::DELIVERY_STATUS]))
			$this->oldColumnsValues[StorageProfilePeer::DELIVERY_STATUS] = $this->delivery_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->delivery_status !== $v) {
			$this->delivery_status = $v;
			$this->modifiedColumns[] = StorageProfilePeer::DELIVERY_STATUS;
		}

		return $this;
	} // setDeliveryStatus()

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
		// Nullify cached objects
		$this->m_custom_data = null;
		
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->created_at = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->updated_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->partner_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->system_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->desciption = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->status = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->protocol = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->storage_url = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->storage_base_dir = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->storage_username = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->storage_password = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->storage_ftp_passive_mode = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->min_file_size = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->max_file_size = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->flavor_params_ids = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->max_concurrent_connections = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->custom_data = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->path_manager_class = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->delivery_priority = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->delivery_status = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 22; // 22 = StorageProfilePeer::NUM_COLUMNS - StorageProfilePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating StorageProfile object", $e);
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
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		StorageProfilePeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		StorageProfilePeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		StorageProfilePeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				StorageProfilePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                if ($affectedRows || !$this->isColumnModified(StorageProfilePeer::CUSTOM_DATA)) //ask if custom_data wasn't modified to avoid retry with atomic column 
                	break;

                KalturaLog::debug("was unable to save! retrying for the $retries time");
                $criteria = $this->buildPkeyCriteria();
				$criteria->addSelectColumn(StorageProfilePeer::CUSTOM_DATA);
                $stmt = BasePeer::doSelect($criteria, $con);
                $cutsomDataArr = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $newCustomData = $cutsomDataArr[0];
                
                $this->custom_data_md5 = md5($newCustomData);

                $valuesToChangeTo = $this->m_custom_data->toArray();
				$this->m_custom_data = myCustomData::fromString($newCustomData); 

				//set custom data column values we wanted to change to
				$validUpdate = true;
				$atomicCustomDataFields = StorageProfilePeer::getAtomicCustomDataFields();
			 	foreach ($this->oldCustomDataValues as $namespace => $namespaceValues){
                	foreach($namespaceValues as $name => $oldValue)
					{
						$newValue = null;
						if ($namespace)
						{
							if (isset ($valuesToChangeTo[$namespace][$name]))
								$newValue = $valuesToChangeTo[$namespace][$name];
						}
						else
						{ 
							$newValue = $valuesToChangeTo[$name];
						}
					 
						if (!is_null($newValue)) {
							$atomicField = false;
							if($namespace) {
								$atomicField = array_key_exists($namespace, $atomicCustomDataFields) && in_array($name, $atomicCustomDataFields[$namespace]);
							} else {
								$atomicField = in_array($name, $atomicCustomDataFields);
							}
							if($atomicField) {
								$dbValue = $this->m_custom_data->get($name, $namespace);
								if($oldValue != $dbValue) {
									$validUpdate = false;
									break;
								}
							}
							$this->putInCustomData($name, $newValue, $namespace);
						}
					}
                   }
                   
				if(!$validUpdate) 
					break;
					                   
				$this->setCustomData($this->m_custom_data->toString());
			}

			if ($isInsert) {
				$this->postInsert($con);
			} else {
				$this->postUpdate($con);
			}
			$this->postSave($con);
			StorageProfilePeer::addInstanceToPool($this);
			
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
				$this->modifiedColumns[] = StorageProfilePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = StorageProfilePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = StorageProfilePeer::doUpdate($this, $con);
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
			$modifiedColumns = $this->tempModifiedColumns;
			$modifiedColumns[kObjectChangedEvent::CUSTOM_DATA_OLD_VALUES] = $this->oldCustomDataValues;
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $modifiedColumns));
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


			if (($retval = StorageProfilePeer::doValidate($this, $columns)) !== true) {
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
		$pos = StorageProfilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPartnerId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getSystemName();
				break;
			case 6:
				return $this->getDesciption();
				break;
			case 7:
				return $this->getStatus();
				break;
			case 8:
				return $this->getProtocol();
				break;
			case 9:
				return $this->getStorageUrl();
				break;
			case 10:
				return $this->getStorageBaseDir();
				break;
			case 11:
				return $this->getStorageUsername();
				break;
			case 12:
				return $this->getStoragePassword();
				break;
			case 13:
				return $this->getStorageFtpPassiveMode();
				break;
			case 14:
				return $this->getMinFileSize();
				break;
			case 15:
				return $this->getMaxFileSize();
				break;
			case 16:
				return $this->getFlavorParamsIds();
				break;
			case 17:
				return $this->getMaxConcurrentConnections();
				break;
			case 18:
				return $this->getCustomData();
				break;
			case 19:
				return $this->getPathManagerClass();
				break;
			case 20:
				return $this->getDeliveryPriority();
				break;
			case 21:
				return $this->getDeliveryStatus();
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
		$keys = StorageProfilePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getUpdatedAt(),
			$keys[3] => $this->getPartnerId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getSystemName(),
			$keys[6] => $this->getDesciption(),
			$keys[7] => $this->getStatus(),
			$keys[8] => $this->getProtocol(),
			$keys[9] => $this->getStorageUrl(),
			$keys[10] => $this->getStorageBaseDir(),
			$keys[11] => $this->getStorageUsername(),
			$keys[12] => $this->getStoragePassword(),
			$keys[13] => $this->getStorageFtpPassiveMode(),
			$keys[14] => $this->getMinFileSize(),
			$keys[15] => $this->getMaxFileSize(),
			$keys[16] => $this->getFlavorParamsIds(),
			$keys[17] => $this->getMaxConcurrentConnections(),
			$keys[18] => $this->getCustomData(),
			$keys[19] => $this->getPathManagerClass(),
			$keys[20] => $this->getDeliveryPriority(),
			$keys[21] => $this->getDeliveryStatus(),
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
		$pos = StorageProfilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPartnerId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setSystemName($value);
				break;
			case 6:
				$this->setDesciption($value);
				break;
			case 7:
				$this->setStatus($value);
				break;
			case 8:
				$this->setProtocol($value);
				break;
			case 9:
				$this->setStorageUrl($value);
				break;
			case 10:
				$this->setStorageBaseDir($value);
				break;
			case 11:
				$this->setStorageUsername($value);
				break;
			case 12:
				$this->setStoragePassword($value);
				break;
			case 13:
				$this->setStorageFtpPassiveMode($value);
				break;
			case 14:
				$this->setMinFileSize($value);
				break;
			case 15:
				$this->setMaxFileSize($value);
				break;
			case 16:
				$this->setFlavorParamsIds($value);
				break;
			case 17:
				$this->setMaxConcurrentConnections($value);
				break;
			case 18:
				$this->setCustomData($value);
				break;
			case 19:
				$this->setPathManagerClass($value);
				break;
			case 20:
				$this->setDeliveryPriority($value);
				break;
			case 21:
				$this->setDeliveryStatus($value);
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
		$keys = StorageProfilePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUpdatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPartnerId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSystemName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDesciption($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setProtocol($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setStorageUrl($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setStorageBaseDir($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setStorageUsername($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setStoragePassword($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setStorageFtpPassiveMode($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setMinFileSize($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setMaxFileSize($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setFlavorParamsIds($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setMaxConcurrentConnections($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setCustomData($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setPathManagerClass($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setDeliveryPriority($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setDeliveryStatus($arr[$keys[21]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);

		if ($this->isColumnModified(StorageProfilePeer::ID)) $criteria->add(StorageProfilePeer::ID, $this->id);
		if ($this->isColumnModified(StorageProfilePeer::CREATED_AT)) $criteria->add(StorageProfilePeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(StorageProfilePeer::UPDATED_AT)) $criteria->add(StorageProfilePeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(StorageProfilePeer::PARTNER_ID)) $criteria->add(StorageProfilePeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(StorageProfilePeer::NAME)) $criteria->add(StorageProfilePeer::NAME, $this->name);
		if ($this->isColumnModified(StorageProfilePeer::SYSTEM_NAME)) $criteria->add(StorageProfilePeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(StorageProfilePeer::DESCIPTION)) $criteria->add(StorageProfilePeer::DESCIPTION, $this->desciption);
		if ($this->isColumnModified(StorageProfilePeer::STATUS)) $criteria->add(StorageProfilePeer::STATUS, $this->status);
		if ($this->isColumnModified(StorageProfilePeer::PROTOCOL)) $criteria->add(StorageProfilePeer::PROTOCOL, $this->protocol);
		if ($this->isColumnModified(StorageProfilePeer::STORAGE_URL)) $criteria->add(StorageProfilePeer::STORAGE_URL, $this->storage_url);
		if ($this->isColumnModified(StorageProfilePeer::STORAGE_BASE_DIR)) $criteria->add(StorageProfilePeer::STORAGE_BASE_DIR, $this->storage_base_dir);
		if ($this->isColumnModified(StorageProfilePeer::STORAGE_USERNAME)) $criteria->add(StorageProfilePeer::STORAGE_USERNAME, $this->storage_username);
		if ($this->isColumnModified(StorageProfilePeer::STORAGE_PASSWORD)) $criteria->add(StorageProfilePeer::STORAGE_PASSWORD, $this->storage_password);
		if ($this->isColumnModified(StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE)) $criteria->add(StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE, $this->storage_ftp_passive_mode);
		if ($this->isColumnModified(StorageProfilePeer::MIN_FILE_SIZE)) $criteria->add(StorageProfilePeer::MIN_FILE_SIZE, $this->min_file_size);
		if ($this->isColumnModified(StorageProfilePeer::MAX_FILE_SIZE)) $criteria->add(StorageProfilePeer::MAX_FILE_SIZE, $this->max_file_size);
		if ($this->isColumnModified(StorageProfilePeer::FLAVOR_PARAMS_IDS)) $criteria->add(StorageProfilePeer::FLAVOR_PARAMS_IDS, $this->flavor_params_ids);
		if ($this->isColumnModified(StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS)) $criteria->add(StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS, $this->max_concurrent_connections);
		if ($this->isColumnModified(StorageProfilePeer::CUSTOM_DATA)) $criteria->add(StorageProfilePeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(StorageProfilePeer::PATH_MANAGER_CLASS)) $criteria->add(StorageProfilePeer::PATH_MANAGER_CLASS, $this->path_manager_class);
		if ($this->isColumnModified(StorageProfilePeer::DELIVERY_PRIORITY)) $criteria->add(StorageProfilePeer::DELIVERY_PRIORITY, $this->delivery_priority);
		if ($this->isColumnModified(StorageProfilePeer::DELIVERY_STATUS)) $criteria->add(StorageProfilePeer::DELIVERY_STATUS, $this->delivery_status);

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
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);

		$criteria->add(StorageProfilePeer::ID, $this->id);
		
		if($this->alreadyInSave)
		{
			if ($this->isColumnModified(StorageProfilePeer::CUSTOM_DATA))
			{
				if (!is_null($this->custom_data_md5))
					$criteria->add(StorageProfilePeer::CUSTOM_DATA, "MD5(cast(" . StorageProfilePeer::CUSTOM_DATA . " as char character set latin1)) = '$this->custom_data_md5'", Criteria::CUSTOM);
					//casting to latin char set to avoid mysql and php md5 difference
				else 
					$criteria->add(StorageProfilePeer::CUSTOM_DATA, NULL, Criteria::ISNULL);
			}
			
			if (count($this->modifiedColumns) == 2 && $this->isColumnModified(StorageProfilePeer::UPDATED_AT))
			{
				$theModifiedColumn = null;
				foreach($this->modifiedColumns as $modifiedColumn)
					if($modifiedColumn != StorageProfilePeer::UPDATED_AT)
						$theModifiedColumn = $modifiedColumn;
						
				$atomicColumns = StorageProfilePeer::getAtomicColumns();
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
	 * @param      object $copyObj An object of StorageProfile (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setName($this->name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setDesciption($this->desciption);

		$copyObj->setStatus($this->status);

		$copyObj->setProtocol($this->protocol);

		$copyObj->setStorageUrl($this->storage_url);

		$copyObj->setStorageBaseDir($this->storage_base_dir);

		$copyObj->setStorageUsername($this->storage_username);

		$copyObj->setStoragePassword($this->storage_password);

		$copyObj->setStorageFtpPassiveMode($this->storage_ftp_passive_mode);

		$copyObj->setMinFileSize($this->min_file_size);

		$copyObj->setMaxFileSize($this->max_file_size);

		$copyObj->setFlavorParamsIds($this->flavor_params_ids);

		$copyObj->setMaxConcurrentConnections($this->max_concurrent_connections);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setPathManagerClass($this->path_manager_class);

		$copyObj->setDeliveryPriority($this->delivery_priority);

		$copyObj->setDeliveryStatus($this->delivery_status);


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
	 * @return     StorageProfile Clone of current object.
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
	 * @var     StorageProfile Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      StorageProfile $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(StorageProfile $copiedFrom)
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
	 * @return     StorageProfilePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new StorageProfilePeer();
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
		
		$currentNamespace = '';
		if($namespace)
			$currentNamespace = $namespace;
			
		if(!isset($this->oldCustomDataValues[$currentNamespace]))
			$this->oldCustomDataValues[$currentNamespace] = array();
		if(!isset($this->oldCustomDataValues[$currentNamespace][$name]))
			$this->oldCustomDataValues[$currentNamespace][$name] = $customData->get($name, $namespace);
		
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
	
} // BaseStorageProfile
