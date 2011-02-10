<?php

/**
 * Base class that represents a row from the 'audit_trail' table.
 *
 * 
 *
 * @package plugins.audit
 * @subpackage model.om
 */
abstract class BaseAuditTrail extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AuditTrailPeer
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
	 * The value for the parsed_at field.
	 * @var        string
	 */
	protected $parsed_at;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the object_type field.
	 * @var        string
	 */
	protected $object_type;

	/**
	 * The value for the object_id field.
	 * @var        string
	 */
	protected $object_id;

	/**
	 * The value for the related_object_id field.
	 * @var        string
	 */
	protected $related_object_id;

	/**
	 * The value for the related_object_type field.
	 * @var        string
	 */
	protected $related_object_type;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the master_partner_id field.
	 * @var        int
	 */
	protected $master_partner_id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the request_id field.
	 * @var        string
	 */
	protected $request_id;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the action field.
	 * @var        string
	 */
	protected $action;

	/**
	 * The value for the data field.
	 * @var        string
	 */
	protected $data;

	/**
	 * The value for the ks field.
	 * @var        string
	 */
	protected $ks;

	/**
	 * The value for the context field.
	 * @var        int
	 */
	protected $context;

	/**
	 * The value for the entry_point field.
	 * @var        string
	 */
	protected $entry_point;

	/**
	 * The value for the server_name field.
	 * @var        string
	 */
	protected $server_name;

	/**
	 * The value for the ip_address field.
	 * @var        string
	 */
	protected $ip_address;

	/**
	 * The value for the user_agent field.
	 * @var        string
	 */
	protected $user_agent;

	/**
	 * The value for the client_tag field.
	 * @var        string
	 */
	protected $client_tag;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the error_description field.
	 * @var        string
	 */
	protected $error_description;

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
	 * Get the [optionally formatted] temporal [parsed_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getParsedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->parsed_at === null) {
			return null;
		}


		if ($this->parsed_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->parsed_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->parsed_at, true), $x);
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
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the [object_type] column value.
	 * 
	 * @return     string
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
	 * Get the [related_object_id] column value.
	 * 
	 * @return     string
	 */
	public function getRelatedObjectId()
	{
		return $this->related_object_id;
	}

	/**
	 * Get the [related_object_type] column value.
	 * 
	 * @return     string
	 */
	public function getRelatedObjectType()
	{
		return $this->related_object_type;
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
	 * Get the [master_partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getMasterPartnerId()
	{
		return $this->master_partner_id;
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
	 * Get the [request_id] column value.
	 * 
	 * @return     string
	 */
	public function getRequestId()
	{
		return $this->request_id;
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
	 * Get the [action] column value.
	 * 
	 * @return     string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Get the [data] column value.
	 * 
	 * @return     string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the [ks] column value.
	 * 
	 * @return     string
	 */
	public function getKs()
	{
		return $this->ks;
	}

	/**
	 * Get the [context] column value.
	 * 
	 * @return     int
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Get the [entry_point] column value.
	 * 
	 * @return     string
	 */
	public function getEntryPoint()
	{
		return $this->entry_point;
	}

	/**
	 * Get the [server_name] column value.
	 * 
	 * @return     string
	 */
	public function getServerName()
	{
		return $this->server_name;
	}

	/**
	 * Get the [ip_address] column value.
	 * 
	 * @return     string
	 */
	public function getIpAddress()
	{
		return $this->ip_address;
	}

	/**
	 * Get the [user_agent] column value.
	 * 
	 * @return     string
	 */
	public function getUserAgent()
	{
		return $this->user_agent;
	}

	/**
	 * Get the [client_tag] column value.
	 * 
	 * @return     string
	 */
	public function getClientTag()
	{
		return $this->client_tag;
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
	 * Get the [error_description] column value.
	 * 
	 * @return     string
	 */
	public function getErrorDescription()
	{
		return $this->error_description;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::ID]))
			$this->oldColumnsValues[AuditTrailPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AuditTrail The current object (for fluent API support)
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
				$this->modifiedColumns[] = AuditTrailPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [parsed_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setParsedAt($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::PARSED_AT]))
			$this->oldColumnsValues[AuditTrailPeer::PARSED_AT] = $this->parsed_at;

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

		if ( $this->parsed_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->parsed_at !== null && $tmpDt = new DateTime($this->parsed_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->parsed_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AuditTrailPeer::PARSED_AT;
			}
		} // if either are not null

		return $this;
	} // setParsedAt()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::STATUS]))
			$this->oldColumnsValues[AuditTrailPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = AuditTrailPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[AuditTrailPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = AuditTrailPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::OBJECT_ID]))
			$this->oldColumnsValues[AuditTrailPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [related_object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setRelatedObjectId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::RELATED_OBJECT_ID]))
			$this->oldColumnsValues[AuditTrailPeer::RELATED_OBJECT_ID] = $this->related_object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->related_object_id !== $v) {
			$this->related_object_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::RELATED_OBJECT_ID;
		}

		return $this;
	} // setRelatedObjectId()

	/**
	 * Set the value of [related_object_type] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setRelatedObjectType($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::RELATED_OBJECT_TYPE]))
			$this->oldColumnsValues[AuditTrailPeer::RELATED_OBJECT_TYPE] = $this->related_object_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->related_object_type !== $v) {
			$this->related_object_type = $v;
			$this->modifiedColumns[] = AuditTrailPeer::RELATED_OBJECT_TYPE;
		}

		return $this;
	} // setRelatedObjectType()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::ENTRY_ID]))
			$this->oldColumnsValues[AuditTrailPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [master_partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setMasterPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::MASTER_PARTNER_ID]))
			$this->oldColumnsValues[AuditTrailPeer::MASTER_PARTNER_ID] = $this->master_partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->master_partner_id !== $v) {
			$this->master_partner_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::MASTER_PARTNER_ID;
		}

		return $this;
	} // setMasterPartnerId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::PARTNER_ID]))
			$this->oldColumnsValues[AuditTrailPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [request_id] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setRequestId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::REQUEST_ID]))
			$this->oldColumnsValues[AuditTrailPeer::REQUEST_ID] = $this->request_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->request_id !== $v) {
			$this->request_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::REQUEST_ID;
		}

		return $this;
	} // setRequestId()

	/**
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::KUSER_ID]))
			$this->oldColumnsValues[AuditTrailPeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = AuditTrailPeer::KUSER_ID;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [action] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setAction($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::ACTION]))
			$this->oldColumnsValues[AuditTrailPeer::ACTION] = $this->action;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->action !== $v) {
			$this->action = $v;
			$this->modifiedColumns[] = AuditTrailPeer::ACTION;
		}

		return $this;
	} // setAction()

	/**
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setData($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::DATA]))
			$this->oldColumnsValues[AuditTrailPeer::DATA] = $this->data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->data !== $v) {
			$this->data = $v;
			$this->modifiedColumns[] = AuditTrailPeer::DATA;
		}

		return $this;
	} // setData()

	/**
	 * Set the value of [ks] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setKs($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::KS]))
			$this->oldColumnsValues[AuditTrailPeer::KS] = $this->ks;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ks !== $v) {
			$this->ks = $v;
			$this->modifiedColumns[] = AuditTrailPeer::KS;
		}

		return $this;
	} // setKs()

	/**
	 * Set the value of [context] column.
	 * 
	 * @param      int $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setContext($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::CONTEXT]))
			$this->oldColumnsValues[AuditTrailPeer::CONTEXT] = $this->context;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->context !== $v) {
			$this->context = $v;
			$this->modifiedColumns[] = AuditTrailPeer::CONTEXT;
		}

		return $this;
	} // setContext()

	/**
	 * Set the value of [entry_point] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setEntryPoint($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::ENTRY_POINT]))
			$this->oldColumnsValues[AuditTrailPeer::ENTRY_POINT] = $this->entry_point;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_point !== $v) {
			$this->entry_point = $v;
			$this->modifiedColumns[] = AuditTrailPeer::ENTRY_POINT;
		}

		return $this;
	} // setEntryPoint()

	/**
	 * Set the value of [server_name] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setServerName($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::SERVER_NAME]))
			$this->oldColumnsValues[AuditTrailPeer::SERVER_NAME] = $this->server_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->server_name !== $v) {
			$this->server_name = $v;
			$this->modifiedColumns[] = AuditTrailPeer::SERVER_NAME;
		}

		return $this;
	} // setServerName()

	/**
	 * Set the value of [ip_address] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setIpAddress($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::IP_ADDRESS]))
			$this->oldColumnsValues[AuditTrailPeer::IP_ADDRESS] = $this->ip_address;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ip_address !== $v) {
			$this->ip_address = $v;
			$this->modifiedColumns[] = AuditTrailPeer::IP_ADDRESS;
		}

		return $this;
	} // setIpAddress()

	/**
	 * Set the value of [user_agent] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setUserAgent($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::USER_AGENT]))
			$this->oldColumnsValues[AuditTrailPeer::USER_AGENT] = $this->user_agent;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->user_agent !== $v) {
			$this->user_agent = $v;
			$this->modifiedColumns[] = AuditTrailPeer::USER_AGENT;
		}

		return $this;
	} // setUserAgent()

	/**
	 * Set the value of [client_tag] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setClientTag($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::CLIENT_TAG]))
			$this->oldColumnsValues[AuditTrailPeer::CLIENT_TAG] = $this->client_tag;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->client_tag !== $v) {
			$this->client_tag = $v;
			$this->modifiedColumns[] = AuditTrailPeer::CLIENT_TAG;
		}

		return $this;
	} // setClientTag()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::DESCRIPTION]))
			$this->oldColumnsValues[AuditTrailPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = AuditTrailPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [error_description] column.
	 * 
	 * @param      string $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setErrorDescription($v)
	{
		if(!isset($this->oldColumnsValues[AuditTrailPeer::ERROR_DESCRIPTION]))
			$this->oldColumnsValues[AuditTrailPeer::ERROR_DESCRIPTION] = $this->error_description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->error_description !== $v) {
			$this->error_description = $v;
			$this->modifiedColumns[] = AuditTrailPeer::ERROR_DESCRIPTION;
		}

		return $this;
	} // setErrorDescription()

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
			$this->parsed_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->status = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->object_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->object_id = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->related_object_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->related_object_type = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->entry_id = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->master_partner_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->partner_id = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->request_id = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->kuser_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->action = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->data = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->ks = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->context = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->entry_point = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->server_name = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->ip_address = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->user_agent = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->client_tag = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->description = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->error_description = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = AuditTrailPeer::NUM_COLUMNS - AuditTrailPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AuditTrail object", $e);
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
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AuditTrailPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AuditTrailPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AuditTrailPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = AuditTrailPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AuditTrailPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AuditTrailPeer::doUpdate($this, $con);
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
    	
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		AuditTrailPeer::setUseCriteriaFilter(false);
		$this->reload();
		AuditTrailPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = AuditTrailPeer::doValidate($this, $columns)) !== true) {
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
		$pos = AuditTrailPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getParsedAt();
				break;
			case 3:
				return $this->getStatus();
				break;
			case 4:
				return $this->getObjectType();
				break;
			case 5:
				return $this->getObjectId();
				break;
			case 6:
				return $this->getRelatedObjectId();
				break;
			case 7:
				return $this->getRelatedObjectType();
				break;
			case 8:
				return $this->getEntryId();
				break;
			case 9:
				return $this->getMasterPartnerId();
				break;
			case 10:
				return $this->getPartnerId();
				break;
			case 11:
				return $this->getRequestId();
				break;
			case 12:
				return $this->getKuserId();
				break;
			case 13:
				return $this->getAction();
				break;
			case 14:
				return $this->getData();
				break;
			case 15:
				return $this->getKs();
				break;
			case 16:
				return $this->getContext();
				break;
			case 17:
				return $this->getEntryPoint();
				break;
			case 18:
				return $this->getServerName();
				break;
			case 19:
				return $this->getIpAddress();
				break;
			case 20:
				return $this->getUserAgent();
				break;
			case 21:
				return $this->getClientTag();
				break;
			case 22:
				return $this->getDescription();
				break;
			case 23:
				return $this->getErrorDescription();
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
		$keys = AuditTrailPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getParsedAt(),
			$keys[3] => $this->getStatus(),
			$keys[4] => $this->getObjectType(),
			$keys[5] => $this->getObjectId(),
			$keys[6] => $this->getRelatedObjectId(),
			$keys[7] => $this->getRelatedObjectType(),
			$keys[8] => $this->getEntryId(),
			$keys[9] => $this->getMasterPartnerId(),
			$keys[10] => $this->getPartnerId(),
			$keys[11] => $this->getRequestId(),
			$keys[12] => $this->getKuserId(),
			$keys[13] => $this->getAction(),
			$keys[14] => $this->getData(),
			$keys[15] => $this->getKs(),
			$keys[16] => $this->getContext(),
			$keys[17] => $this->getEntryPoint(),
			$keys[18] => $this->getServerName(),
			$keys[19] => $this->getIpAddress(),
			$keys[20] => $this->getUserAgent(),
			$keys[21] => $this->getClientTag(),
			$keys[22] => $this->getDescription(),
			$keys[23] => $this->getErrorDescription(),
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
		$criteria = new Criteria(AuditTrailPeer::DATABASE_NAME);

		if ($this->isColumnModified(AuditTrailPeer::ID)) $criteria->add(AuditTrailPeer::ID, $this->id);
		if ($this->isColumnModified(AuditTrailPeer::CREATED_AT)) $criteria->add(AuditTrailPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AuditTrailPeer::PARSED_AT)) $criteria->add(AuditTrailPeer::PARSED_AT, $this->parsed_at);
		if ($this->isColumnModified(AuditTrailPeer::STATUS)) $criteria->add(AuditTrailPeer::STATUS, $this->status);
		if ($this->isColumnModified(AuditTrailPeer::OBJECT_TYPE)) $criteria->add(AuditTrailPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(AuditTrailPeer::OBJECT_ID)) $criteria->add(AuditTrailPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(AuditTrailPeer::RELATED_OBJECT_ID)) $criteria->add(AuditTrailPeer::RELATED_OBJECT_ID, $this->related_object_id);
		if ($this->isColumnModified(AuditTrailPeer::RELATED_OBJECT_TYPE)) $criteria->add(AuditTrailPeer::RELATED_OBJECT_TYPE, $this->related_object_type);
		if ($this->isColumnModified(AuditTrailPeer::ENTRY_ID)) $criteria->add(AuditTrailPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(AuditTrailPeer::MASTER_PARTNER_ID)) $criteria->add(AuditTrailPeer::MASTER_PARTNER_ID, $this->master_partner_id);
		if ($this->isColumnModified(AuditTrailPeer::PARTNER_ID)) $criteria->add(AuditTrailPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(AuditTrailPeer::REQUEST_ID)) $criteria->add(AuditTrailPeer::REQUEST_ID, $this->request_id);
		if ($this->isColumnModified(AuditTrailPeer::KUSER_ID)) $criteria->add(AuditTrailPeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(AuditTrailPeer::ACTION)) $criteria->add(AuditTrailPeer::ACTION, $this->action);
		if ($this->isColumnModified(AuditTrailPeer::DATA)) $criteria->add(AuditTrailPeer::DATA, $this->data);
		if ($this->isColumnModified(AuditTrailPeer::KS)) $criteria->add(AuditTrailPeer::KS, $this->ks);
		if ($this->isColumnModified(AuditTrailPeer::CONTEXT)) $criteria->add(AuditTrailPeer::CONTEXT, $this->context);
		if ($this->isColumnModified(AuditTrailPeer::ENTRY_POINT)) $criteria->add(AuditTrailPeer::ENTRY_POINT, $this->entry_point);
		if ($this->isColumnModified(AuditTrailPeer::SERVER_NAME)) $criteria->add(AuditTrailPeer::SERVER_NAME, $this->server_name);
		if ($this->isColumnModified(AuditTrailPeer::IP_ADDRESS)) $criteria->add(AuditTrailPeer::IP_ADDRESS, $this->ip_address);
		if ($this->isColumnModified(AuditTrailPeer::USER_AGENT)) $criteria->add(AuditTrailPeer::USER_AGENT, $this->user_agent);
		if ($this->isColumnModified(AuditTrailPeer::CLIENT_TAG)) $criteria->add(AuditTrailPeer::CLIENT_TAG, $this->client_tag);
		if ($this->isColumnModified(AuditTrailPeer::DESCRIPTION)) $criteria->add(AuditTrailPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(AuditTrailPeer::ERROR_DESCRIPTION)) $criteria->add(AuditTrailPeer::ERROR_DESCRIPTION, $this->error_description);

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
		$criteria = new Criteria(AuditTrailPeer::DATABASE_NAME);

		$criteria->add(AuditTrailPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AuditTrail (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setParsedAt($this->parsed_at);

		$copyObj->setStatus($this->status);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setRelatedObjectId($this->related_object_id);

		$copyObj->setRelatedObjectType($this->related_object_type);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setMasterPartnerId($this->master_partner_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setRequestId($this->request_id);

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setAction($this->action);

		$copyObj->setData($this->data);

		$copyObj->setKs($this->ks);

		$copyObj->setContext($this->context);

		$copyObj->setEntryPoint($this->entry_point);

		$copyObj->setServerName($this->server_name);

		$copyObj->setIpAddress($this->ip_address);

		$copyObj->setUserAgent($this->user_agent);

		$copyObj->setClientTag($this->client_tag);

		$copyObj->setDescription($this->description);

		$copyObj->setErrorDescription($this->error_description);


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
	 * @return     AuditTrail Clone of current object.
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
	 * @var     AuditTrail Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      AuditTrail $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(AuditTrail $copiedFrom)
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
	 * @return     AuditTrailPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AuditTrailPeer();
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

} // BaseAuditTrail
