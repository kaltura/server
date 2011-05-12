<?php

/**
 * Base class that represents a row from the 'access_control' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseaccessControl extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        accessControlPeer
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
	 * The value for the name field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the system_name field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $system_name;

	/**
	 * The value for the description field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $description;

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
	 * The value for the deleted_at field.
	 * @var        string
	 */
	protected $deleted_at;

	/**
	 * The value for the site_restrict_type field.
	 * @var        int
	 */
	protected $site_restrict_type;

	/**
	 * The value for the site_restrict_list field.
	 * @var        string
	 */
	protected $site_restrict_list;

	/**
	 * The value for the country_restrict_type field.
	 * @var        int
	 */
	protected $country_restrict_type;

	/**
	 * The value for the country_restrict_list field.
	 * @var        string
	 */
	protected $country_restrict_list;

	/**
	 * The value for the ks_restrict_privilege field.
	 * @var        string
	 */
	protected $ks_restrict_privilege;

	/**
	 * The value for the prv_restrict_privilege field.
	 * @var        string
	 */
	protected $prv_restrict_privilege;

	/**
	 * The value for the prv_restrict_length field.
	 * @var        int
	 */
	protected $prv_restrict_length;

	/**
	 * The value for the kdir_restrict_type field.
	 * @var        int
	 */
	protected $kdir_restrict_type;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * @var        array entry[] Collection to store aggregation of entry objects.
	 */
	protected $collentrys;

	/**
	 * @var        Criteria The criteria used to select the current contents of collentrys.
	 */
	private $lastentryCriteria = null;

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
		$this->name = '';
		$this->system_name = '';
		$this->description = '';
	}

	/**
	 * Initializes internal state of BaseaccessControl object.
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
	 * Get the [optionally formatted] temporal [deleted_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->deleted_at === null) {
			return null;
		}


		if ($this->deleted_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->deleted_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->deleted_at, true), $x);
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
	 * Get the [site_restrict_type] column value.
	 * 
	 * @return     int
	 */
	public function getSiteRestrictType()
	{
		return $this->site_restrict_type;
	}

	/**
	 * Get the [site_restrict_list] column value.
	 * 
	 * @return     string
	 */
	public function getSiteRestrictList()
	{
		return $this->site_restrict_list;
	}

	/**
	 * Get the [country_restrict_type] column value.
	 * 
	 * @return     int
	 */
	public function getCountryRestrictType()
	{
		return $this->country_restrict_type;
	}

	/**
	 * Get the [country_restrict_list] column value.
	 * 
	 * @return     string
	 */
	public function getCountryRestrictList()
	{
		return $this->country_restrict_list;
	}

	/**
	 * Get the [ks_restrict_privilege] column value.
	 * 
	 * @return     string
	 */
	public function getKsRestrictPrivilege()
	{
		return $this->ks_restrict_privilege;
	}

	/**
	 * Get the [prv_restrict_privilege] column value.
	 * 
	 * @return     string
	 */
	public function getPrvRestrictPrivilege()
	{
		return $this->prv_restrict_privilege;
	}

	/**
	 * Get the [prv_restrict_length] column value.
	 * 
	 * @return     int
	 */
	public function getPrvRestrictLength()
	{
		return $this->prv_restrict_length;
	}

	/**
	 * Get the [kdir_restrict_type] column value.
	 * 
	 * @return     int
	 */
	public function getKdirRestrictType()
	{
		return $this->kdir_restrict_type;
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
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::ID]))
			$this->oldColumnsValues[accessControlPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = accessControlPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::PARTNER_ID]))
			$this->oldColumnsValues[accessControlPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = accessControlPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::NAME]))
			$this->oldColumnsValues[accessControlPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = accessControlPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setSystemName($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::SYSTEM_NAME]))
			$this->oldColumnsValues[accessControlPeer::SYSTEM_NAME] = $this->system_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->system_name !== $v || $this->isNew()) {
			$this->system_name = $v;
			$this->modifiedColumns[] = accessControlPeer::SYSTEM_NAME;
		}

		return $this;
	} // setSystemName()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::DESCRIPTION]))
			$this->oldColumnsValues[accessControlPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v || $this->isNew()) {
			$this->description = $v;
			$this->modifiedColumns[] = accessControlPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     accessControl The current object (for fluent API support)
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
				$this->modifiedColumns[] = accessControlPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     accessControl The current object (for fluent API support)
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
				$this->modifiedColumns[] = accessControlPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::DELETED_AT]))
			$this->oldColumnsValues[accessControlPeer::DELETED_AT] = $this->deleted_at;

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

		if ( $this->deleted_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->deleted_at !== null && $tmpDt = new DateTime($this->deleted_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->deleted_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = accessControlPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [site_restrict_type] column.
	 * 
	 * @param      int $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setSiteRestrictType($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::SITE_RESTRICT_TYPE]))
			$this->oldColumnsValues[accessControlPeer::SITE_RESTRICT_TYPE] = $this->site_restrict_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->site_restrict_type !== $v) {
			$this->site_restrict_type = $v;
			$this->modifiedColumns[] = accessControlPeer::SITE_RESTRICT_TYPE;
		}

		return $this;
	} // setSiteRestrictType()

	/**
	 * Set the value of [site_restrict_list] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setSiteRestrictList($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::SITE_RESTRICT_LIST]))
			$this->oldColumnsValues[accessControlPeer::SITE_RESTRICT_LIST] = $this->site_restrict_list;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->site_restrict_list !== $v) {
			$this->site_restrict_list = $v;
			$this->modifiedColumns[] = accessControlPeer::SITE_RESTRICT_LIST;
		}

		return $this;
	} // setSiteRestrictList()

	/**
	 * Set the value of [country_restrict_type] column.
	 * 
	 * @param      int $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setCountryRestrictType($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::COUNTRY_RESTRICT_TYPE]))
			$this->oldColumnsValues[accessControlPeer::COUNTRY_RESTRICT_TYPE] = $this->country_restrict_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->country_restrict_type !== $v) {
			$this->country_restrict_type = $v;
			$this->modifiedColumns[] = accessControlPeer::COUNTRY_RESTRICT_TYPE;
		}

		return $this;
	} // setCountryRestrictType()

	/**
	 * Set the value of [country_restrict_list] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setCountryRestrictList($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::COUNTRY_RESTRICT_LIST]))
			$this->oldColumnsValues[accessControlPeer::COUNTRY_RESTRICT_LIST] = $this->country_restrict_list;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->country_restrict_list !== $v) {
			$this->country_restrict_list = $v;
			$this->modifiedColumns[] = accessControlPeer::COUNTRY_RESTRICT_LIST;
		}

		return $this;
	} // setCountryRestrictList()

	/**
	 * Set the value of [ks_restrict_privilege] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setKsRestrictPrivilege($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::KS_RESTRICT_PRIVILEGE]))
			$this->oldColumnsValues[accessControlPeer::KS_RESTRICT_PRIVILEGE] = $this->ks_restrict_privilege;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ks_restrict_privilege !== $v) {
			$this->ks_restrict_privilege = $v;
			$this->modifiedColumns[] = accessControlPeer::KS_RESTRICT_PRIVILEGE;
		}

		return $this;
	} // setKsRestrictPrivilege()

	/**
	 * Set the value of [prv_restrict_privilege] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setPrvRestrictPrivilege($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::PRV_RESTRICT_PRIVILEGE]))
			$this->oldColumnsValues[accessControlPeer::PRV_RESTRICT_PRIVILEGE] = $this->prv_restrict_privilege;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prv_restrict_privilege !== $v) {
			$this->prv_restrict_privilege = $v;
			$this->modifiedColumns[] = accessControlPeer::PRV_RESTRICT_PRIVILEGE;
		}

		return $this;
	} // setPrvRestrictPrivilege()

	/**
	 * Set the value of [prv_restrict_length] column.
	 * 
	 * @param      int $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setPrvRestrictLength($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::PRV_RESTRICT_LENGTH]))
			$this->oldColumnsValues[accessControlPeer::PRV_RESTRICT_LENGTH] = $this->prv_restrict_length;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->prv_restrict_length !== $v) {
			$this->prv_restrict_length = $v;
			$this->modifiedColumns[] = accessControlPeer::PRV_RESTRICT_LENGTH;
		}

		return $this;
	} // setPrvRestrictLength()

	/**
	 * Set the value of [kdir_restrict_type] column.
	 * 
	 * @param      int $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setKdirRestrictType($v)
	{
		if(!isset($this->oldColumnsValues[accessControlPeer::KDIR_RESTRICT_TYPE]))
			$this->oldColumnsValues[accessControlPeer::KDIR_RESTRICT_TYPE] = $this->kdir_restrict_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kdir_restrict_type !== $v) {
			$this->kdir_restrict_type = $v;
			$this->modifiedColumns[] = accessControlPeer::KDIR_RESTRICT_TYPE;
		}

		return $this;
	} // setKdirRestrictType()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     accessControl The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = accessControlPeer::CUSTOM_DATA;
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
			if ($this->name !== '') {
				return false;
			}

			if ($this->system_name !== '') {
				return false;
			}

			if ($this->description !== '') {
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
			$this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->system_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->created_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->updated_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->deleted_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->site_restrict_type = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->site_restrict_list = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->country_restrict_type = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->country_restrict_list = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->ks_restrict_privilege = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->prv_restrict_privilege = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->prv_restrict_length = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->kdir_restrict_type = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->custom_data = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 17; // 17 = accessControlPeer::NUM_COLUMNS - accessControlPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating accessControl object", $e);
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
			$con = Propel::getConnection(accessControlPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = accessControlPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collentrys = null;
			$this->lastentryCriteria = null;

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
			$con = Propel::getConnection(accessControlPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				accessControlPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(accessControlPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				accessControlPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = accessControlPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = accessControlPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += accessControlPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collentrys !== null) {
				foreach ($this->collentrys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
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


			if (($retval = accessControlPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collentrys !== null) {
					foreach ($this->collentrys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = accessControlPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getName();
				break;
			case 3:
				return $this->getSystemName();
				break;
			case 4:
				return $this->getDescription();
				break;
			case 5:
				return $this->getCreatedAt();
				break;
			case 6:
				return $this->getUpdatedAt();
				break;
			case 7:
				return $this->getDeletedAt();
				break;
			case 8:
				return $this->getSiteRestrictType();
				break;
			case 9:
				return $this->getSiteRestrictList();
				break;
			case 10:
				return $this->getCountryRestrictType();
				break;
			case 11:
				return $this->getCountryRestrictList();
				break;
			case 12:
				return $this->getKsRestrictPrivilege();
				break;
			case 13:
				return $this->getPrvRestrictPrivilege();
				break;
			case 14:
				return $this->getPrvRestrictLength();
				break;
			case 15:
				return $this->getKdirRestrictType();
				break;
			case 16:
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
		$keys = accessControlPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getName(),
			$keys[3] => $this->getSystemName(),
			$keys[4] => $this->getDescription(),
			$keys[5] => $this->getCreatedAt(),
			$keys[6] => $this->getUpdatedAt(),
			$keys[7] => $this->getDeletedAt(),
			$keys[8] => $this->getSiteRestrictType(),
			$keys[9] => $this->getSiteRestrictList(),
			$keys[10] => $this->getCountryRestrictType(),
			$keys[11] => $this->getCountryRestrictList(),
			$keys[12] => $this->getKsRestrictPrivilege(),
			$keys[13] => $this->getPrvRestrictPrivilege(),
			$keys[14] => $this->getPrvRestrictLength(),
			$keys[15] => $this->getKdirRestrictType(),
			$keys[16] => $this->getCustomData(),
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
		$pos = accessControlPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setName($value);
				break;
			case 3:
				$this->setSystemName($value);
				break;
			case 4:
				$this->setDescription($value);
				break;
			case 5:
				$this->setCreatedAt($value);
				break;
			case 6:
				$this->setUpdatedAt($value);
				break;
			case 7:
				$this->setDeletedAt($value);
				break;
			case 8:
				$this->setSiteRestrictType($value);
				break;
			case 9:
				$this->setSiteRestrictList($value);
				break;
			case 10:
				$this->setCountryRestrictType($value);
				break;
			case 11:
				$this->setCountryRestrictList($value);
				break;
			case 12:
				$this->setKsRestrictPrivilege($value);
				break;
			case 13:
				$this->setPrvRestrictPrivilege($value);
				break;
			case 14:
				$this->setPrvRestrictLength($value);
				break;
			case 15:
				$this->setKdirRestrictType($value);
				break;
			case 16:
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
		$keys = accessControlPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSystemName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCreatedAt($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDeletedAt($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSiteRestrictType($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSiteRestrictList($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCountryRestrictType($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCountryRestrictList($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setKsRestrictPrivilege($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setPrvRestrictPrivilege($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPrvRestrictLength($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setKdirRestrictType($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setCustomData($arr[$keys[16]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(accessControlPeer::DATABASE_NAME);

		if ($this->isColumnModified(accessControlPeer::ID)) $criteria->add(accessControlPeer::ID, $this->id);
		if ($this->isColumnModified(accessControlPeer::PARTNER_ID)) $criteria->add(accessControlPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(accessControlPeer::NAME)) $criteria->add(accessControlPeer::NAME, $this->name);
		if ($this->isColumnModified(accessControlPeer::SYSTEM_NAME)) $criteria->add(accessControlPeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(accessControlPeer::DESCRIPTION)) $criteria->add(accessControlPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(accessControlPeer::CREATED_AT)) $criteria->add(accessControlPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(accessControlPeer::UPDATED_AT)) $criteria->add(accessControlPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(accessControlPeer::DELETED_AT)) $criteria->add(accessControlPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(accessControlPeer::SITE_RESTRICT_TYPE)) $criteria->add(accessControlPeer::SITE_RESTRICT_TYPE, $this->site_restrict_type);
		if ($this->isColumnModified(accessControlPeer::SITE_RESTRICT_LIST)) $criteria->add(accessControlPeer::SITE_RESTRICT_LIST, $this->site_restrict_list);
		if ($this->isColumnModified(accessControlPeer::COUNTRY_RESTRICT_TYPE)) $criteria->add(accessControlPeer::COUNTRY_RESTRICT_TYPE, $this->country_restrict_type);
		if ($this->isColumnModified(accessControlPeer::COUNTRY_RESTRICT_LIST)) $criteria->add(accessControlPeer::COUNTRY_RESTRICT_LIST, $this->country_restrict_list);
		if ($this->isColumnModified(accessControlPeer::KS_RESTRICT_PRIVILEGE)) $criteria->add(accessControlPeer::KS_RESTRICT_PRIVILEGE, $this->ks_restrict_privilege);
		if ($this->isColumnModified(accessControlPeer::PRV_RESTRICT_PRIVILEGE)) $criteria->add(accessControlPeer::PRV_RESTRICT_PRIVILEGE, $this->prv_restrict_privilege);
		if ($this->isColumnModified(accessControlPeer::PRV_RESTRICT_LENGTH)) $criteria->add(accessControlPeer::PRV_RESTRICT_LENGTH, $this->prv_restrict_length);
		if ($this->isColumnModified(accessControlPeer::KDIR_RESTRICT_TYPE)) $criteria->add(accessControlPeer::KDIR_RESTRICT_TYPE, $this->kdir_restrict_type);
		if ($this->isColumnModified(accessControlPeer::CUSTOM_DATA)) $criteria->add(accessControlPeer::CUSTOM_DATA, $this->custom_data);

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
		$criteria = new Criteria(accessControlPeer::DATABASE_NAME);

		$criteria->add(accessControlPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of accessControl (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setName($this->name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setDescription($this->description);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setDeletedAt($this->deleted_at);

		$copyObj->setSiteRestrictType($this->site_restrict_type);

		$copyObj->setSiteRestrictList($this->site_restrict_list);

		$copyObj->setCountryRestrictType($this->country_restrict_type);

		$copyObj->setCountryRestrictList($this->country_restrict_list);

		$copyObj->setKsRestrictPrivilege($this->ks_restrict_privilege);

		$copyObj->setPrvRestrictPrivilege($this->prv_restrict_privilege);

		$copyObj->setPrvRestrictLength($this->prv_restrict_length);

		$copyObj->setKdirRestrictType($this->kdir_restrict_type);

		$copyObj->setCustomData($this->custom_data);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getentrys() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addentry($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


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
	 * @return     accessControl Clone of current object.
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
	 * @var     accessControl Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      accessControl $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(accessControl $copiedFrom)
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
	 * @return     accessControlPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new accessControlPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collentrys collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addentrys()
	 */
	public function clearentrys()
	{
		$this->collentrys = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collentrys collection (array).
	 *
	 * By default this just sets the collentrys collection to an empty array (like clearcollentrys());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initentrys()
	{
		$this->collentrys = array();
	}

	/**
	 * Gets an array of entry objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this accessControl has previously been saved, it will retrieve
	 * related entrys from storage. If this accessControl is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array entry[]
	 * @throws     PropelException
	 */
	public function getentrys($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(accessControlPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
			   $this->collentrys = array();
			} else {

				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				entryPeer::addSelectColumns($criteria);
				$this->collentrys = entryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				entryPeer::addSelectColumns($criteria);
				if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
					$this->collentrys = entryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastentryCriteria = $criteria;
		return $this->collentrys;
	}

	/**
	 * Returns the number of related entry objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related entry objects.
	 * @throws     PropelException
	 */
	public function countentrys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(accessControlPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				$count = entryPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
					$count = entryPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collentrys);
				}
			} else {
				$count = count($this->collentrys);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a entry object to this object
	 * through the entry foreign key attribute.
	 *
	 * @param      entry $l entry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addentry(entry $l)
	{
		if ($this->collentrys === null) {
			$this->initentrys();
		}
		if (!in_array($l, $this->collentrys, true)) { // only add it if the **same** object is not already associated
			array_push($this->collentrys, $l);
			$l->setaccessControl($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this accessControl is new, it will return
	 * an empty collection; or if this accessControl has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in accessControl.
	 */
	public function getentrysJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(accessControlPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this accessControl is new, it will return
	 * an empty collection; or if this accessControl has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in accessControl.
	 */
	public function getentrysJoinkuser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(accessControlPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinkuser($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinkuser($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this accessControl is new, it will return
	 * an empty collection; or if this accessControl has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in accessControl.
	 */
	public function getentrysJoinconversionProfile2($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(accessControlPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
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
			if ($this->collentrys) {
				foreach ((array) $this->collentrys as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collentrys = null;
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
	
} // BaseaccessControl
