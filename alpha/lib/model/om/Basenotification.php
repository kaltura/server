<?php

/**
 * Base class that represents a row from the 'notification' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class Basenotification extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        notificationPeer
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
	 * The value for the puser_id field.
	 * @var        string
	 */
	protected $puser_id;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the object_id field.
	 * @var        string
	 */
	protected $object_id;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the notification_data field.
	 * @var        string
	 */
	protected $notification_data;

	/**
	 * The value for the number_of_attempts field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $number_of_attempts;

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
	 * The value for the notification_result field.
	 * @var        string
	 */
	protected $notification_result;

	/**
	 * The value for the object_type field.
	 * @var        int
	 */
	protected $object_type;

	/**
	 * The value for the scheduler_id field.
	 * @var        int
	 */
	protected $scheduler_id;

	/**
	 * The value for the worker_id field.
	 * @var        int
	 */
	protected $worker_id;

	/**
	 * The value for the batch_index field.
	 * @var        int
	 */
	protected $batch_index;

	/**
	 * The value for the processor_expiration field.
	 * @var        string
	 */
	protected $processor_expiration;

	/**
	 * The value for the execution_attempts field.
	 * @var        int
	 */
	protected $execution_attempts;

	/**
	 * The value for the lock_version field.
	 * @var        int
	 */
	protected $lock_version;

	/**
	 * The value for the dc field.
	 * @var        string
	 */
	protected $dc;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->number_of_attempts = 0;
	}

	/**
	 * Initializes internal state of Basenotification object.
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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [puser_id] column value.
	 * 
	 * @return     string
	 */
	public function getPuserId()
	{
		return $this->puser_id;
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
	 * Get the [object_id] column value.
	 * 
	 * @return     string
	 */
	public function getObjectId()
	{
		return $this->object_id;
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
	 * Get the [notification_data] column value.
	 * 
	 * @return     string
	 */
	public function getNotificationData()
	{
		return $this->notification_data;
	}

	/**
	 * Get the [number_of_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getNumberOfAttempts()
	{
		return $this->number_of_attempts;
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
	 * Get the [notification_result] column value.
	 * 
	 * @return     string
	 */
	public function getNotificationResult()
	{
		return $this->notification_result;
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
	 * Get the [scheduler_id] column value.
	 * 
	 * @return     int
	 */
	public function getSchedulerId()
	{
		return $this->scheduler_id;
	}

	/**
	 * Get the [worker_id] column value.
	 * 
	 * @return     int
	 */
	public function getWorkerId()
	{
		return $this->worker_id;
	}

	/**
	 * Get the [batch_index] column value.
	 * 
	 * @return     int
	 */
	public function getBatchIndex()
	{
		return $this->batch_index;
	}

	/**
	 * Get the [optionally formatted] temporal [processor_expiration] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getProcessorExpiration($format = 'Y-m-d H:i:s')
	{
		if ($this->processor_expiration === null) {
			return null;
		}


		if ($this->processor_expiration === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->processor_expiration);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->processor_expiration, true), $x);
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
	 * Get the [execution_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getExecutionAttempts()
	{
		return $this->execution_attempts;
	}

	/**
	 * Get the [lock_version] column value.
	 * 
	 * @return     int
	 */
	public function getLockVersion()
	{
		return $this->lock_version;
	}

	/**
	 * Get the [dc] column value.
	 * 
	 * @return     string
	 */
	public function getDc()
	{
		return $this->dc;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::ID]))
			$this->oldColumnsValues[notificationPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = notificationPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::PARTNER_ID]))
			$this->oldColumnsValues[notificationPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = notificationPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [puser_id] column.
	 * 
	 * @param      string $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setPuserId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::PUSER_ID]))
			$this->oldColumnsValues[notificationPeer::PUSER_ID] = $this->puser_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->puser_id !== $v) {
			$this->puser_id = $v;
			$this->modifiedColumns[] = notificationPeer::PUSER_ID;
		}

		return $this;
	} // setPuserId()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::TYPE]))
			$this->oldColumnsValues[notificationPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = notificationPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::OBJECT_ID]))
			$this->oldColumnsValues[notificationPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = notificationPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::STATUS]))
			$this->oldColumnsValues[notificationPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = notificationPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [notification_data] column.
	 * 
	 * @param      string $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setNotificationData($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::NOTIFICATION_DATA]))
			$this->oldColumnsValues[notificationPeer::NOTIFICATION_DATA] = $this->notification_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->notification_data !== $v) {
			$this->notification_data = $v;
			$this->modifiedColumns[] = notificationPeer::NOTIFICATION_DATA;
		}

		return $this;
	} // setNotificationData()

	/**
	 * Set the value of [number_of_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setNumberOfAttempts($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::NUMBER_OF_ATTEMPTS]))
			$this->oldColumnsValues[notificationPeer::NUMBER_OF_ATTEMPTS] = $this->number_of_attempts;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->number_of_attempts !== $v || $this->isNew()) {
			$this->number_of_attempts = $v;
			$this->modifiedColumns[] = notificationPeer::NUMBER_OF_ATTEMPTS;
		}

		return $this;
	} // setNumberOfAttempts()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     notification The current object (for fluent API support)
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
				$this->modifiedColumns[] = notificationPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     notification The current object (for fluent API support)
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
				$this->modifiedColumns[] = notificationPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [notification_result] column.
	 * 
	 * @param      string $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setNotificationResult($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::NOTIFICATION_RESULT]))
			$this->oldColumnsValues[notificationPeer::NOTIFICATION_RESULT] = $this->notification_result;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->notification_result !== $v) {
			$this->notification_result = $v;
			$this->modifiedColumns[] = notificationPeer::NOTIFICATION_RESULT;
		}

		return $this;
	} // setNotificationResult()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[notificationPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = notificationPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[notificationPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = notificationPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::WORKER_ID]))
			$this->oldColumnsValues[notificationPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = notificationPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::BATCH_INDEX]))
			$this->oldColumnsValues[notificationPeer::BATCH_INDEX] = $this->batch_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = notificationPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Sets the value of [processor_expiration] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     notification The current object (for fluent API support)
	 */
	public function setProcessorExpiration($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::PROCESSOR_EXPIRATION]))
			$this->oldColumnsValues[notificationPeer::PROCESSOR_EXPIRATION] = $this->processor_expiration;

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

		if ( $this->processor_expiration !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->processor_expiration !== null && $tmpDt = new DateTime($this->processor_expiration)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->processor_expiration = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = notificationPeer::PROCESSOR_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setProcessorExpiration()

	/**
	 * Set the value of [execution_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setExecutionAttempts($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::EXECUTION_ATTEMPTS]))
			$this->oldColumnsValues[notificationPeer::EXECUTION_ATTEMPTS] = $this->execution_attempts;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_attempts !== $v) {
			$this->execution_attempts = $v;
			$this->modifiedColumns[] = notificationPeer::EXECUTION_ATTEMPTS;
		}

		return $this;
	} // setExecutionAttempts()

	/**
	 * Set the value of [lock_version] column.
	 * 
	 * @param      int $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setLockVersion($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::LOCK_VERSION]))
			$this->oldColumnsValues[notificationPeer::LOCK_VERSION] = $this->lock_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lock_version !== $v) {
			$this->lock_version = $v;
			$this->modifiedColumns[] = notificationPeer::LOCK_VERSION;
		}

		return $this;
	} // setLockVersion()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      string $v new value
	 * @return     notification The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[notificationPeer::DC]))
			$this->oldColumnsValues[notificationPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = notificationPeer::DC;
		}

		return $this;
	} // setDc()

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
			if ($this->number_of_attempts !== 0) {
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
			$this->partner_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->puser_id = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->type = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->object_id = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->status = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->notification_data = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->number_of_attempts = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->notification_result = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->object_type = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->scheduler_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->worker_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->batch_index = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->processor_expiration = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->execution_attempts = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->lock_version = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->dc = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 19; // 19 = notificationPeer::NUM_COLUMNS - notificationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating notification object", $e);
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
			$con = Propel::getConnection(notificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = notificationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(notificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				notificationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(notificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				notificationPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = notificationPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = notificationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += notificationPeer::doUpdate($this, $con);
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


			if (($retval = notificationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = notificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPuserId();
				break;
			case 3:
				return $this->getType();
				break;
			case 4:
				return $this->getObjectId();
				break;
			case 5:
				return $this->getStatus();
				break;
			case 6:
				return $this->getNotificationData();
				break;
			case 7:
				return $this->getNumberOfAttempts();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			case 9:
				return $this->getUpdatedAt();
				break;
			case 10:
				return $this->getNotificationResult();
				break;
			case 11:
				return $this->getObjectType();
				break;
			case 12:
				return $this->getSchedulerId();
				break;
			case 13:
				return $this->getWorkerId();
				break;
			case 14:
				return $this->getBatchIndex();
				break;
			case 15:
				return $this->getProcessorExpiration();
				break;
			case 16:
				return $this->getExecutionAttempts();
				break;
			case 17:
				return $this->getLockVersion();
				break;
			case 18:
				return $this->getDc();
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
		$keys = notificationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getPuserId(),
			$keys[3] => $this->getType(),
			$keys[4] => $this->getObjectId(),
			$keys[5] => $this->getStatus(),
			$keys[6] => $this->getNotificationData(),
			$keys[7] => $this->getNumberOfAttempts(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getUpdatedAt(),
			$keys[10] => $this->getNotificationResult(),
			$keys[11] => $this->getObjectType(),
			$keys[12] => $this->getSchedulerId(),
			$keys[13] => $this->getWorkerId(),
			$keys[14] => $this->getBatchIndex(),
			$keys[15] => $this->getProcessorExpiration(),
			$keys[16] => $this->getExecutionAttempts(),
			$keys[17] => $this->getLockVersion(),
			$keys[18] => $this->getDc(),
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
		$pos = notificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPuserId($value);
				break;
			case 3:
				$this->setType($value);
				break;
			case 4:
				$this->setObjectId($value);
				break;
			case 5:
				$this->setStatus($value);
				break;
			case 6:
				$this->setNotificationData($value);
				break;
			case 7:
				$this->setNumberOfAttempts($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
			case 9:
				$this->setUpdatedAt($value);
				break;
			case 10:
				$this->setNotificationResult($value);
				break;
			case 11:
				$this->setObjectType($value);
				break;
			case 12:
				$this->setSchedulerId($value);
				break;
			case 13:
				$this->setWorkerId($value);
				break;
			case 14:
				$this->setBatchIndex($value);
				break;
			case 15:
				$this->setProcessorExpiration($value);
				break;
			case 16:
				$this->setExecutionAttempts($value);
				break;
			case 17:
				$this->setLockVersion($value);
				break;
			case 18:
				$this->setDc($value);
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
		$keys = notificationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPuserId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setObjectId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setStatus($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setNotificationData($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setNumberOfAttempts($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setNotificationResult($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setObjectType($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setSchedulerId($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setWorkerId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setBatchIndex($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setProcessorExpiration($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setExecutionAttempts($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setLockVersion($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setDc($arr[$keys[18]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(notificationPeer::DATABASE_NAME);

		if ($this->isColumnModified(notificationPeer::ID)) $criteria->add(notificationPeer::ID, $this->id);
		if ($this->isColumnModified(notificationPeer::PARTNER_ID)) $criteria->add(notificationPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(notificationPeer::PUSER_ID)) $criteria->add(notificationPeer::PUSER_ID, $this->puser_id);
		if ($this->isColumnModified(notificationPeer::TYPE)) $criteria->add(notificationPeer::TYPE, $this->type);
		if ($this->isColumnModified(notificationPeer::OBJECT_ID)) $criteria->add(notificationPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(notificationPeer::STATUS)) $criteria->add(notificationPeer::STATUS, $this->status);
		if ($this->isColumnModified(notificationPeer::NOTIFICATION_DATA)) $criteria->add(notificationPeer::NOTIFICATION_DATA, $this->notification_data);
		if ($this->isColumnModified(notificationPeer::NUMBER_OF_ATTEMPTS)) $criteria->add(notificationPeer::NUMBER_OF_ATTEMPTS, $this->number_of_attempts);
		if ($this->isColumnModified(notificationPeer::CREATED_AT)) $criteria->add(notificationPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(notificationPeer::UPDATED_AT)) $criteria->add(notificationPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(notificationPeer::NOTIFICATION_RESULT)) $criteria->add(notificationPeer::NOTIFICATION_RESULT, $this->notification_result);
		if ($this->isColumnModified(notificationPeer::OBJECT_TYPE)) $criteria->add(notificationPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(notificationPeer::SCHEDULER_ID)) $criteria->add(notificationPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(notificationPeer::WORKER_ID)) $criteria->add(notificationPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(notificationPeer::BATCH_INDEX)) $criteria->add(notificationPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(notificationPeer::PROCESSOR_EXPIRATION)) $criteria->add(notificationPeer::PROCESSOR_EXPIRATION, $this->processor_expiration);
		if ($this->isColumnModified(notificationPeer::EXECUTION_ATTEMPTS)) $criteria->add(notificationPeer::EXECUTION_ATTEMPTS, $this->execution_attempts);
		if ($this->isColumnModified(notificationPeer::LOCK_VERSION)) $criteria->add(notificationPeer::LOCK_VERSION, $this->lock_version);
		if ($this->isColumnModified(notificationPeer::DC)) $criteria->add(notificationPeer::DC, $this->dc);

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
		$criteria = new Criteria(notificationPeer::DATABASE_NAME);

		$criteria->add(notificationPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of notification (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setPuserId($this->puser_id);

		$copyObj->setType($this->type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setStatus($this->status);

		$copyObj->setNotificationData($this->notification_data);

		$copyObj->setNumberOfAttempts($this->number_of_attempts);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setNotificationResult($this->notification_result);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setBatchIndex($this->batch_index);

		$copyObj->setProcessorExpiration($this->processor_expiration);

		$copyObj->setExecutionAttempts($this->execution_attempts);

		$copyObj->setLockVersion($this->lock_version);

		$copyObj->setDc($this->dc);


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
	 * @return     notification Clone of current object.
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
	 * @var     notification Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      notification $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(notification $copiedFrom)
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
	 * @return     notificationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new notificationPeer();
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

} // Basenotification
