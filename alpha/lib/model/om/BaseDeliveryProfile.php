<?php

/**
 * Base class that represents a row from the 'delivery_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseDeliveryProfile extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DeliveryProfilePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $type;

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
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the url field.
	 * @var        string
	 */
	protected $url;

	/**
	 * The value for the host_name field.
	 * @var        string
	 */
	protected $host_name;

	/**
	 * The value for the is_default field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $is_default;

	/**
	 * The value for the parent_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $parent_id;

	/**
	 * The value for the recognizer field.
	 * @var        string
	 */
	protected $recognizer;

	/**
	 * The value for the tokenizer field.
	 * @var        string
	 */
	protected $tokenizer;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the streamer_type field.
	 * @var        string
	 */
	protected $streamer_type;

	/**
	 * The value for the media_protocols field.
	 * @var        string
	 */
	protected $media_protocols;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->type = 0;
		$this->is_default = false;
		$this->parent_id = 0;
	}

	/**
	 * Initializes internal state of BaseDeliveryProfile object.
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
	 * Get the [type] column value.
	 * 
	 * @return     int
	 */
	public function getType()
	{
		return $this->type;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
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
	 * Get the [host_name] column value.
	 * 
	 * @return     string
	 */
	public function getHostName()
	{
		return $this->host_name;
	}

	/**
	 * Get the [is_default] column value.
	 * 
	 * @return     boolean
	 */
	public function getIsDefault()
	{
		return $this->is_default;
	}

	/**
	 * Get the [parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getParentId()
	{
		return $this->parent_id;
	}

	/**
	 * Get the [recognizer] column value.
	 * 
	 * @return     string
	 */
	public function getRecognizer()
	{
		return $this->recognizer;
	}

	/**
	 * Get the [tokenizer] column value.
	 * 
	 * @return     string
	 */
	public function getTokenizer()
	{
		return $this->tokenizer;
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
	 * Get the [streamer_type] column value.
	 * 
	 * @return     string
	 */
	public function getStreamerType()
	{
		return $this->streamer_type;
	}

	/**
	 * Get the [media_protocols] column value.
	 * 
	 * @return     string
	 */
	public function getMediaProtocols()
	{
		return $this->media_protocols;
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
	 * Get the [priority] column value.
	 * 
	 * @return     int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::ID]))
			$this->oldColumnsValues[DeliveryProfilePeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::TYPE]))
			$this->oldColumnsValues[DeliveryProfilePeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DeliveryProfile The current object (for fluent API support)
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
				$this->modifiedColumns[] = DeliveryProfilePeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     DeliveryProfile The current object (for fluent API support)
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
				$this->modifiedColumns[] = DeliveryProfilePeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::PARTNER_ID]))
			$this->oldColumnsValues[DeliveryProfilePeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::NAME]))
			$this->oldColumnsValues[DeliveryProfilePeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setSystemName($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::SYSTEM_NAME]))
			$this->oldColumnsValues[DeliveryProfilePeer::SYSTEM_NAME] = $this->system_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->system_name !== $v) {
			$this->system_name = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::SYSTEM_NAME;
		}

		return $this;
	} // setSystemName()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::DESCRIPTION]))
			$this->oldColumnsValues[DeliveryProfilePeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [url] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setUrl($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::URL]))
			$this->oldColumnsValues[DeliveryProfilePeer::URL] = $this->url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url !== $v) {
			$this->url = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::URL;
		}

		return $this;
	} // setUrl()

	/**
	 * Set the value of [host_name] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setHostName($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::HOST_NAME]))
			$this->oldColumnsValues[DeliveryProfilePeer::HOST_NAME] = $this->host_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->host_name !== $v) {
			$this->host_name = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::HOST_NAME;
		}

		return $this;
	} // setHostName()

	/**
	 * Set the value of [is_default] column.
	 * 
	 * @param      boolean $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setIsDefault($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::IS_DEFAULT]))
			$this->oldColumnsValues[DeliveryProfilePeer::IS_DEFAULT] = $this->is_default;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->is_default !== $v || $this->isNew()) {
			$this->is_default = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::IS_DEFAULT;
		}

		return $this;
	} // setIsDefault()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setParentId($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::PARENT_ID]))
			$this->oldColumnsValues[DeliveryProfilePeer::PARENT_ID] = $this->parent_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->parent_id !== $v || $this->isNew()) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::PARENT_ID;
		}

		return $this;
	} // setParentId()

	/**
	 * Set the value of [recognizer] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setRecognizer($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::RECOGNIZER]))
			$this->oldColumnsValues[DeliveryProfilePeer::RECOGNIZER] = $this->recognizer;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->recognizer !== $v) {
			$this->recognizer = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::RECOGNIZER;
		}

		return $this;
	} // setRecognizer()

	/**
	 * Set the value of [tokenizer] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setTokenizer($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::TOKENIZER]))
			$this->oldColumnsValues[DeliveryProfilePeer::TOKENIZER] = $this->tokenizer;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tokenizer !== $v) {
			$this->tokenizer = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::TOKENIZER;
		}

		return $this;
	} // setTokenizer()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::STATUS]))
			$this->oldColumnsValues[DeliveryProfilePeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [streamer_type] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setStreamerType($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::STREAMER_TYPE]))
			$this->oldColumnsValues[DeliveryProfilePeer::STREAMER_TYPE] = $this->streamer_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->streamer_type !== $v) {
			$this->streamer_type = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::STREAMER_TYPE;
		}

		return $this;
	} // setStreamerType()

	/**
	 * Set the value of [media_protocols] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setMediaProtocols($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::MEDIA_PROTOCOLS]))
			$this->oldColumnsValues[DeliveryProfilePeer::MEDIA_PROTOCOLS] = $this->media_protocols;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->media_protocols !== $v) {
			$this->media_protocols = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::MEDIA_PROTOCOLS;
		}

		return $this;
	} // setMediaProtocols()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [priority] column.
	 * 
	 * @param      int $v new value
	 * @return     DeliveryProfile The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if(!isset($this->oldColumnsValues[DeliveryProfilePeer::PRIORITY]))
			$this->oldColumnsValues[DeliveryProfilePeer::PRIORITY] = $this->priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = DeliveryProfilePeer::PRIORITY;
		}

		return $this;
	} // setPriority()

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
			if ($this->type !== 0) {
				return false;
			}

			if ($this->is_default !== false) {
				return false;
			}

			if ($this->parent_id !== 0) {
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
		$this->last_hydrate_time = time();

		// Nullify cached objects
		$this->m_custom_data = null;
		
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->created_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->updated_at = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->partner_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->system_name = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->description = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->url = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->host_name = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->is_default = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
			$this->parent_id = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->recognizer = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->tokenizer = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->status = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->streamer_type = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->media_protocols = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->custom_data = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->priority = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 19; // 19 = DeliveryProfilePeer::NUM_COLUMNS - DeliveryProfilePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DeliveryProfile object", $e);
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
			$con = Propel::getConnection(DeliveryProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		DeliveryProfilePeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		DeliveryProfilePeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		DeliveryProfilePeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(DeliveryProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				DeliveryProfilePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(DeliveryProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                if ($affectedRows || !$this->isColumnModified(DeliveryProfilePeer::CUSTOM_DATA)) //ask if custom_data wasn't modified to avoid retry with atomic column 
                	break;

                KalturaLog::debug("was unable to save! retrying for the $retries time");
                $criteria = $this->buildPkeyCriteria();
				$criteria->addSelectColumn(DeliveryProfilePeer::CUSTOM_DATA);
                $stmt = BasePeer::doSelect($criteria, $con);
                $cutsomDataArr = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $newCustomData = $cutsomDataArr[0];

                $this->custom_data_md5 = is_null($newCustomData) ? null : md5($newCustomData);

                $valuesToChangeTo = $this->m_custom_data->toArray();
				$this->m_custom_data = myCustomData::fromString($newCustomData); 

				//set custom data column values we wanted to change to
				$validUpdate = true;
				$atomicCustomDataFields = DeliveryProfilePeer::getAtomicCustomDataFields();
			 	foreach ($this->oldCustomDataValues as $namespace => $namespaceValues){
                	foreach($namespaceValues as $name => $oldValue)
					{
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
		
						if (is_null($newValue)) {
							$this->removeFromCustomData($name, $namespace);
						}
						else {
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
			DeliveryProfilePeer::addInstanceToPool($this);
			
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
				$this->modifiedColumns[] = DeliveryProfilePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = DeliveryProfilePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = DeliveryProfilePeer::doUpdate($this, $con);
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


			if (($retval = DeliveryProfilePeer::doValidate($this, $columns)) !== true) {
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
		$pos = DeliveryProfilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getType();
				break;
			case 2:
				return $this->getCreatedAt();
				break;
			case 3:
				return $this->getUpdatedAt();
				break;
			case 4:
				return $this->getPartnerId();
				break;
			case 5:
				return $this->getName();
				break;
			case 6:
				return $this->getSystemName();
				break;
			case 7:
				return $this->getDescription();
				break;
			case 8:
				return $this->getUrl();
				break;
			case 9:
				return $this->getHostName();
				break;
			case 10:
				return $this->getIsDefault();
				break;
			case 11:
				return $this->getParentId();
				break;
			case 12:
				return $this->getRecognizer();
				break;
			case 13:
				return $this->getTokenizer();
				break;
			case 14:
				return $this->getStatus();
				break;
			case 15:
				return $this->getStreamerType();
				break;
			case 16:
				return $this->getMediaProtocols();
				break;
			case 17:
				return $this->getCustomData();
				break;
			case 18:
				return $this->getPriority();
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
		$keys = DeliveryProfilePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getType(),
			$keys[2] => $this->getCreatedAt(),
			$keys[3] => $this->getUpdatedAt(),
			$keys[4] => $this->getPartnerId(),
			$keys[5] => $this->getName(),
			$keys[6] => $this->getSystemName(),
			$keys[7] => $this->getDescription(),
			$keys[8] => $this->getUrl(),
			$keys[9] => $this->getHostName(),
			$keys[10] => $this->getIsDefault(),
			$keys[11] => $this->getParentId(),
			$keys[12] => $this->getRecognizer(),
			$keys[13] => $this->getTokenizer(),
			$keys[14] => $this->getStatus(),
			$keys[15] => $this->getStreamerType(),
			$keys[16] => $this->getMediaProtocols(),
			$keys[17] => $this->getCustomData(),
			$keys[18] => $this->getPriority(),
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
		$pos = DeliveryProfilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setType($value);
				break;
			case 2:
				$this->setCreatedAt($value);
				break;
			case 3:
				$this->setUpdatedAt($value);
				break;
			case 4:
				$this->setPartnerId($value);
				break;
			case 5:
				$this->setName($value);
				break;
			case 6:
				$this->setSystemName($value);
				break;
			case 7:
				$this->setDescription($value);
				break;
			case 8:
				$this->setUrl($value);
				break;
			case 9:
				$this->setHostName($value);
				break;
			case 10:
				$this->setIsDefault($value);
				break;
			case 11:
				$this->setParentId($value);
				break;
			case 12:
				$this->setRecognizer($value);
				break;
			case 13:
				$this->setTokenizer($value);
				break;
			case 14:
				$this->setStatus($value);
				break;
			case 15:
				$this->setStreamerType($value);
				break;
			case 16:
				$this->setMediaProtocols($value);
				break;
			case 17:
				$this->setCustomData($value);
				break;
			case 18:
				$this->setPriority($value);
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
		$keys = DeliveryProfilePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUpdatedAt($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPartnerId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSystemName($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUrl($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setHostName($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setIsDefault($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setParentId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setRecognizer($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setTokenizer($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStatus($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setStreamerType($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setMediaProtocols($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setCustomData($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setPriority($arr[$keys[18]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DeliveryProfilePeer::DATABASE_NAME);

		if ($this->isColumnModified(DeliveryProfilePeer::ID)) $criteria->add(DeliveryProfilePeer::ID, $this->id);
		if ($this->isColumnModified(DeliveryProfilePeer::TYPE)) $criteria->add(DeliveryProfilePeer::TYPE, $this->type);
		if ($this->isColumnModified(DeliveryProfilePeer::CREATED_AT)) $criteria->add(DeliveryProfilePeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(DeliveryProfilePeer::UPDATED_AT)) $criteria->add(DeliveryProfilePeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(DeliveryProfilePeer::PARTNER_ID)) $criteria->add(DeliveryProfilePeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(DeliveryProfilePeer::NAME)) $criteria->add(DeliveryProfilePeer::NAME, $this->name);
		if ($this->isColumnModified(DeliveryProfilePeer::SYSTEM_NAME)) $criteria->add(DeliveryProfilePeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(DeliveryProfilePeer::DESCRIPTION)) $criteria->add(DeliveryProfilePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(DeliveryProfilePeer::URL)) $criteria->add(DeliveryProfilePeer::URL, $this->url);
		if ($this->isColumnModified(DeliveryProfilePeer::HOST_NAME)) $criteria->add(DeliveryProfilePeer::HOST_NAME, $this->host_name);
		if ($this->isColumnModified(DeliveryProfilePeer::IS_DEFAULT)) $criteria->add(DeliveryProfilePeer::IS_DEFAULT, $this->is_default);
		if ($this->isColumnModified(DeliveryProfilePeer::PARENT_ID)) $criteria->add(DeliveryProfilePeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(DeliveryProfilePeer::RECOGNIZER)) $criteria->add(DeliveryProfilePeer::RECOGNIZER, $this->recognizer);
		if ($this->isColumnModified(DeliveryProfilePeer::TOKENIZER)) $criteria->add(DeliveryProfilePeer::TOKENIZER, $this->tokenizer);
		if ($this->isColumnModified(DeliveryProfilePeer::STATUS)) $criteria->add(DeliveryProfilePeer::STATUS, $this->status);
		if ($this->isColumnModified(DeliveryProfilePeer::STREAMER_TYPE)) $criteria->add(DeliveryProfilePeer::STREAMER_TYPE, $this->streamer_type);
		if ($this->isColumnModified(DeliveryProfilePeer::MEDIA_PROTOCOLS)) $criteria->add(DeliveryProfilePeer::MEDIA_PROTOCOLS, $this->media_protocols);
		if ($this->isColumnModified(DeliveryProfilePeer::CUSTOM_DATA)) $criteria->add(DeliveryProfilePeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(DeliveryProfilePeer::PRIORITY)) $criteria->add(DeliveryProfilePeer::PRIORITY, $this->priority);

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
		$criteria = new Criteria(DeliveryProfilePeer::DATABASE_NAME);

		$criteria->add(DeliveryProfilePeer::ID, $this->id);
		
		if($this->alreadyInSave)
		{
			if ($this->isColumnModified(DeliveryProfilePeer::CUSTOM_DATA))
			{
				if (!is_null($this->custom_data_md5))
					$criteria->add(DeliveryProfilePeer::CUSTOM_DATA, "MD5(cast(" . DeliveryProfilePeer::CUSTOM_DATA . " as char character set latin1)) = '$this->custom_data_md5'", Criteria::CUSTOM);
					//casting to latin char set to avoid mysql and php md5 difference
				else 
					$criteria->add(DeliveryProfilePeer::CUSTOM_DATA, NULL, Criteria::ISNULL);
			}
			
			if (count($this->modifiedColumns) == 2 && $this->isColumnModified(DeliveryProfilePeer::UPDATED_AT))
			{
				$theModifiedColumn = null;
				foreach($this->modifiedColumns as $modifiedColumn)
					if($modifiedColumn != DeliveryProfilePeer::UPDATED_AT)
						$theModifiedColumn = $modifiedColumn;
						
				$atomicColumns = DeliveryProfilePeer::getAtomicColumns();
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
	 * @param      object $copyObj An object of DeliveryProfile (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setType($this->type);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setName($this->name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setDescription($this->description);

		$copyObj->setUrl($this->url);

		$copyObj->setHostName($this->host_name);

		$copyObj->setIsDefault($this->is_default);

		$copyObj->setParentId($this->parent_id);

		$copyObj->setRecognizer($this->recognizer);

		$copyObj->setTokenizer($this->tokenizer);

		$copyObj->setStatus($this->status);

		$copyObj->setStreamerType($this->streamer_type);

		$copyObj->setMediaProtocols($this->media_protocols);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setPriority($this->priority);


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
	 * @return     DeliveryProfile Clone of current object.
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
	 * @var     DeliveryProfile Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      DeliveryProfile $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(DeliveryProfile $copiedFrom)
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
	 * @return     DeliveryProfilePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DeliveryProfilePeer();
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
	
	protected $last_hydrate_time;

	public function getLastHydrateTime()
	{
		return $this->last_hydrate_time;
	}

} // BaseDeliveryProfile
