<?php

/**
 * Base class that represents a row from the 'partner' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BasePartner extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PartnerPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the partner_name field.
	 * @var        string
	 */
	protected $partner_name;

	/**
	 * The value for the partner_alias field.
	 * @var        string
	 */
	protected $partner_alias;

	/**
	 * The value for the url1 field.
	 * @var        string
	 */
	protected $url1;

	/**
	 * The value for the url2 field.
	 * @var        string
	 */
	protected $url2;

	/**
	 * The value for the secret field.
	 * @var        string
	 */
	protected $secret;

	/**
	 * The value for the admin_secret field.
	 * @var        string
	 */
	protected $admin_secret;

	/**
	 * The value for the max_number_of_hits_per_day field.
	 * Note: this column has a database default value of: -1
	 * @var        int
	 */
	protected $max_number_of_hits_per_day;

	/**
	 * The value for the appear_in_search field.
	 * Note: this column has a database default value of: 2
	 * @var        int
	 */
	protected $appear_in_search;

	/**
	 * The value for the debug_level field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $debug_level;

	/**
	 * The value for the invalid_login_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $invalid_login_count;

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
	 * The value for the anonymous_kuser_id field.
	 * @var        int
	 */
	protected $anonymous_kuser_id;

	/**
	 * The value for the ks_max_expiry_in_seconds field.
	 * Note: this column has a database default value of: 86400
	 * @var        int
	 */
	protected $ks_max_expiry_in_seconds;

	/**
	 * The value for the create_user_on_demand field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $create_user_on_demand;

	/**
	 * The value for the prefix field.
	 * @var        string
	 */
	protected $prefix;

	/**
	 * The value for the admin_name field.
	 * @var        string
	 */
	protected $admin_name;

	/**
	 * The value for the admin_email field.
	 * @var        string
	 */
	protected $admin_email;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the commercial_use field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $commercial_use;

	/**
	 * The value for the moderate_content field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $moderate_content;

	/**
	 * The value for the notify field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $notify;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the service_config_id field.
	 * @var        string
	 */
	protected $service_config_id;

	/**
	 * The value for the status field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the content_categories field.
	 * @var        string
	 */
	protected $content_categories;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the phone field.
	 * @var        string
	 */
	protected $phone;

	/**
	 * The value for the describe_yourself field.
	 * @var        string
	 */
	protected $describe_yourself;

	/**
	 * The value for the adult_content field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $adult_content;

	/**
	 * The value for the partner_package field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $partner_package;

	/**
	 * The value for the usage_percent field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $usage_percent;

	/**
	 * The value for the storage_usage field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $storage_usage;

	/**
	 * The value for the eighty_percent_warning field.
	 * @var        int
	 */
	protected $eighty_percent_warning;

	/**
	 * The value for the usage_limit_warning field.
	 * @var        int
	 */
	protected $usage_limit_warning;

	/**
	 * The value for the monitor_usage field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $monitor_usage;

	/**
	 * The value for the priority_group_id field.
	 * @var        int
	 */
	protected $priority_group_id;

	/**
	 * The value for the partner_group_type field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $partner_group_type;

	/**
	 * The value for the partner_parent_id field.
	 * @var        int
	 */
	protected $partner_parent_id;

	/**
	 * The value for the kmc_version field.
	 * Note: this column has a database default value of: '1'
	 * @var        string
	 */
	protected $kmc_version;

	/**
	 * @var        kuser
	 */
	protected $akuser;

	/**
	 * @var        array adminKuser[] Collection to store aggregation of adminKuser objects.
	 */
	protected $colladminKusers;

	/**
	 * @var        Criteria The criteria used to select the current contents of colladminKusers.
	 */
	private $lastadminKuserCriteria = null;

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
		$this->max_number_of_hits_per_day = -1;
		$this->appear_in_search = 2;
		$this->debug_level = 0;
		$this->invalid_login_count = 0;
		$this->ks_max_expiry_in_seconds = 86400;
		$this->create_user_on_demand = 1;
		$this->commercial_use = 0;
		$this->moderate_content = 0;
		$this->notify = 0;
		$this->status = 1;
		$this->type = 1;
		$this->adult_content = 0;
		$this->partner_package = 1;
		$this->usage_percent = 0;
		$this->storage_usage = 0;
		$this->monitor_usage = 1;
		$this->partner_group_type = 1;
		$this->kmc_version = '1';
	}

	/**
	 * Initializes internal state of BasePartner object.
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
	 * Get the [partner_name] column value.
	 * 
	 * @return     string
	 */
	public function getPartnerName()
	{
		return $this->partner_name;
	}

	/**
	 * Get the [partner_alias] column value.
	 * 
	 * @return     string
	 */
	public function getPartnerAlias()
	{
		return $this->partner_alias;
	}

	/**
	 * Get the [url1] column value.
	 * 
	 * @return     string
	 */
	public function getUrl1()
	{
		return $this->url1;
	}

	/**
	 * Get the [url2] column value.
	 * 
	 * @return     string
	 */
	public function getUrl2()
	{
		return $this->url2;
	}

	/**
	 * Get the [secret] column value.
	 * 
	 * @return     string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * Get the [admin_secret] column value.
	 * 
	 * @return     string
	 */
	public function getAdminSecret()
	{
		return $this->admin_secret;
	}

	/**
	 * Get the [max_number_of_hits_per_day] column value.
	 * 
	 * @return     int
	 */
	public function getMaxNumberOfHitsPerDay()
	{
		return $this->max_number_of_hits_per_day;
	}

	/**
	 * Get the [appear_in_search] column value.
	 * 
	 * @return     int
	 */
	public function getAppearInSearch()
	{
		return $this->appear_in_search;
	}

	/**
	 * Get the [debug_level] column value.
	 * 
	 * @return     int
	 */
	public function getDebugLevel()
	{
		return $this->debug_level;
	}

	/**
	 * Get the [invalid_login_count] column value.
	 * 
	 * @return     int
	 */
	public function getInvalidLoginCount()
	{
		return $this->invalid_login_count;
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
	 * Get the [anonymous_kuser_id] column value.
	 * 
	 * @return     int
	 */
	public function getAnonymousKuserId()
	{
		return $this->anonymous_kuser_id;
	}

	/**
	 * Get the [ks_max_expiry_in_seconds] column value.
	 * 
	 * @return     int
	 */
	public function getKsMaxExpiryInSeconds()
	{
		return $this->ks_max_expiry_in_seconds;
	}

	/**
	 * Get the [create_user_on_demand] column value.
	 * 
	 * @return     int
	 */
	public function getCreateUserOnDemand()
	{
		return $this->create_user_on_demand;
	}

	/**
	 * Get the [prefix] column value.
	 * 
	 * @return     string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Get the [admin_name] column value.
	 * 
	 * @return     string
	 */
	public function getAdminName()
	{
		return $this->admin_name;
	}

	/**
	 * Get the [admin_email] column value.
	 * 
	 * @return     string
	 */
	public function getAdminEmail()
	{
		return $this->admin_email;
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
	 * Get the [commercial_use] column value.
	 * 
	 * @return     int
	 */
	public function getCommercialUse()
	{
		return $this->commercial_use;
	}

	/**
	 * Get the [moderate_content] column value.
	 * 
	 * @return     int
	 */
	public function getModerateContent()
	{
		return $this->moderate_content;
	}

	/**
	 * Get the [notify] column value.
	 * 
	 * @return     int
	 */
	public function getNotify()
	{
		return $this->notify;
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
	 * Get the [service_config_id] column value.
	 * 
	 * @return     string
	 */
	public function getServiceConfigId()
	{
		return $this->service_config_id;
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
	 * Get the [content_categories] column value.
	 * 
	 * @return     string
	 */
	public function getContentCategories()
	{
		return $this->content_categories;
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
	 * Get the [phone] column value.
	 * 
	 * @return     string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Get the [describe_yourself] column value.
	 * 
	 * @return     string
	 */
	public function getDescribeYourself()
	{
		return $this->describe_yourself;
	}

	/**
	 * Get the [adult_content] column value.
	 * 
	 * @return     int
	 */
	public function getAdultContent()
	{
		return $this->adult_content;
	}

	/**
	 * Get the [partner_package] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerPackage()
	{
		return $this->partner_package;
	}

	/**
	 * Get the [usage_percent] column value.
	 * 
	 * @return     int
	 */
	public function getUsagePercent()
	{
		return $this->usage_percent;
	}

	/**
	 * Get the [storage_usage] column value.
	 * 
	 * @return     int
	 */
	public function getStorageUsage()
	{
		return $this->storage_usage;
	}

	/**
	 * Get the [eighty_percent_warning] column value.
	 * 
	 * @return     int
	 */
	public function getEightyPercentWarning()
	{
		return $this->eighty_percent_warning;
	}

	/**
	 * Get the [usage_limit_warning] column value.
	 * 
	 * @return     int
	 */
	public function getUsageLimitWarning()
	{
		return $this->usage_limit_warning;
	}

	/**
	 * Get the [monitor_usage] column value.
	 * 
	 * @return     int
	 */
	public function getMonitorUsage()
	{
		return $this->monitor_usage;
	}

	/**
	 * Get the [priority_group_id] column value.
	 * 
	 * @return     int
	 */
	public function getPriorityGroupId()
	{
		return $this->priority_group_id;
	}

	/**
	 * Get the [partner_group_type] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerGroupType()
	{
		return $this->partner_group_type;
	}

	/**
	 * Get the [partner_parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerParentId()
	{
		return $this->partner_parent_id;
	}

	/**
	 * Get the [kmc_version] column value.
	 * 
	 * @return     string
	 */
	public function getKmcVersion()
	{
		return $this->kmc_version;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ID]))
			$this->oldColumnsValues[PartnerPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = PartnerPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPartnerName($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PARTNER_NAME]))
			$this->oldColumnsValues[PartnerPeer::PARTNER_NAME] = $this->partner_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_name !== $v) {
			$this->partner_name = $v;
			$this->modifiedColumns[] = PartnerPeer::PARTNER_NAME;
		}

		return $this;
	} // setPartnerName()

	/**
	 * Set the value of [partner_alias] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPartnerAlias($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PARTNER_ALIAS]))
			$this->oldColumnsValues[PartnerPeer::PARTNER_ALIAS] = $this->partner_alias;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_alias !== $v) {
			$this->partner_alias = $v;
			$this->modifiedColumns[] = PartnerPeer::PARTNER_ALIAS;
		}

		return $this;
	} // setPartnerAlias()

	/**
	 * Set the value of [url1] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setUrl1($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::URL1]))
			$this->oldColumnsValues[PartnerPeer::URL1] = $this->url1;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url1 !== $v) {
			$this->url1 = $v;
			$this->modifiedColumns[] = PartnerPeer::URL1;
		}

		return $this;
	} // setUrl1()

	/**
	 * Set the value of [url2] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setUrl2($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::URL2]))
			$this->oldColumnsValues[PartnerPeer::URL2] = $this->url2;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url2 !== $v) {
			$this->url2 = $v;
			$this->modifiedColumns[] = PartnerPeer::URL2;
		}

		return $this;
	} // setUrl2()

	/**
	 * Set the value of [secret] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setSecret($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::SECRET]))
			$this->oldColumnsValues[PartnerPeer::SECRET] = $this->secret;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->secret !== $v) {
			$this->secret = $v;
			$this->modifiedColumns[] = PartnerPeer::SECRET;
		}

		return $this;
	} // setSecret()

	/**
	 * Set the value of [admin_secret] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAdminSecret($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ADMIN_SECRET]))
			$this->oldColumnsValues[PartnerPeer::ADMIN_SECRET] = $this->admin_secret;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_secret !== $v) {
			$this->admin_secret = $v;
			$this->modifiedColumns[] = PartnerPeer::ADMIN_SECRET;
		}

		return $this;
	} // setAdminSecret()

	/**
	 * Set the value of [max_number_of_hits_per_day] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setMaxNumberOfHitsPerDay($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY]))
			$this->oldColumnsValues[PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY] = $this->max_number_of_hits_per_day;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->max_number_of_hits_per_day !== $v || $this->isNew()) {
			$this->max_number_of_hits_per_day = $v;
			$this->modifiedColumns[] = PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY;
		}

		return $this;
	} // setMaxNumberOfHitsPerDay()

	/**
	 * Set the value of [appear_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAppearInSearch($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::APPEAR_IN_SEARCH]))
			$this->oldColumnsValues[PartnerPeer::APPEAR_IN_SEARCH] = $this->appear_in_search;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->appear_in_search !== $v || $this->isNew()) {
			$this->appear_in_search = $v;
			$this->modifiedColumns[] = PartnerPeer::APPEAR_IN_SEARCH;
		}

		return $this;
	} // setAppearInSearch()

	/**
	 * Set the value of [debug_level] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setDebugLevel($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::DEBUG_LEVEL]))
			$this->oldColumnsValues[PartnerPeer::DEBUG_LEVEL] = $this->debug_level;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->debug_level !== $v || $this->isNew()) {
			$this->debug_level = $v;
			$this->modifiedColumns[] = PartnerPeer::DEBUG_LEVEL;
		}

		return $this;
	} // setDebugLevel()

	/**
	 * Set the value of [invalid_login_count] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setInvalidLoginCount($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::INVALID_LOGIN_COUNT]))
			$this->oldColumnsValues[PartnerPeer::INVALID_LOGIN_COUNT] = $this->invalid_login_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->invalid_login_count !== $v || $this->isNew()) {
			$this->invalid_login_count = $v;
			$this->modifiedColumns[] = PartnerPeer::INVALID_LOGIN_COUNT;
		}

		return $this;
	} // setInvalidLoginCount()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Partner The current object (for fluent API support)
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
				$this->modifiedColumns[] = PartnerPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Partner The current object (for fluent API support)
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
				$this->modifiedColumns[] = PartnerPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [anonymous_kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAnonymousKuserId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ANONYMOUS_KUSER_ID]))
			$this->oldColumnsValues[PartnerPeer::ANONYMOUS_KUSER_ID] = $this->anonymous_kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->anonymous_kuser_id !== $v) {
			$this->anonymous_kuser_id = $v;
			$this->modifiedColumns[] = PartnerPeer::ANONYMOUS_KUSER_ID;
		}

		if ($this->akuser !== null && $this->akuser->getId() !== $v) {
			$this->akuser = null;
		}

		return $this;
	} // setAnonymousKuserId()

	/**
	 * Set the value of [ks_max_expiry_in_seconds] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setKsMaxExpiryInSeconds($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS]))
			$this->oldColumnsValues[PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS] = $this->ks_max_expiry_in_seconds;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ks_max_expiry_in_seconds !== $v || $this->isNew()) {
			$this->ks_max_expiry_in_seconds = $v;
			$this->modifiedColumns[] = PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS;
		}

		return $this;
	} // setKsMaxExpiryInSeconds()

	/**
	 * Set the value of [create_user_on_demand] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setCreateUserOnDemand($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::CREATE_USER_ON_DEMAND]))
			$this->oldColumnsValues[PartnerPeer::CREATE_USER_ON_DEMAND] = $this->create_user_on_demand;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->create_user_on_demand !== $v || $this->isNew()) {
			$this->create_user_on_demand = $v;
			$this->modifiedColumns[] = PartnerPeer::CREATE_USER_ON_DEMAND;
		}

		return $this;
	} // setCreateUserOnDemand()

	/**
	 * Set the value of [prefix] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPrefix($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PREFIX]))
			$this->oldColumnsValues[PartnerPeer::PREFIX] = $this->prefix;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prefix !== $v) {
			$this->prefix = $v;
			$this->modifiedColumns[] = PartnerPeer::PREFIX;
		}

		return $this;
	} // setPrefix()

	/**
	 * Set the value of [admin_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAdminName($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ADMIN_NAME]))
			$this->oldColumnsValues[PartnerPeer::ADMIN_NAME] = $this->admin_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_name !== $v) {
			$this->admin_name = $v;
			$this->modifiedColumns[] = PartnerPeer::ADMIN_NAME;
		}

		return $this;
	} // setAdminName()

	/**
	 * Set the value of [admin_email] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAdminEmail($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ADMIN_EMAIL]))
			$this->oldColumnsValues[PartnerPeer::ADMIN_EMAIL] = $this->admin_email;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_email !== $v) {
			$this->admin_email = $v;
			$this->modifiedColumns[] = PartnerPeer::ADMIN_EMAIL;
		}

		return $this;
	} // setAdminEmail()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::DESCRIPTION]))
			$this->oldColumnsValues[PartnerPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = PartnerPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [commercial_use] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setCommercialUse($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::COMMERCIAL_USE]))
			$this->oldColumnsValues[PartnerPeer::COMMERCIAL_USE] = $this->commercial_use;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->commercial_use !== $v || $this->isNew()) {
			$this->commercial_use = $v;
			$this->modifiedColumns[] = PartnerPeer::COMMERCIAL_USE;
		}

		return $this;
	} // setCommercialUse()

	/**
	 * Set the value of [moderate_content] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setModerateContent($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::MODERATE_CONTENT]))
			$this->oldColumnsValues[PartnerPeer::MODERATE_CONTENT] = $this->moderate_content;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->moderate_content !== $v || $this->isNew()) {
			$this->moderate_content = $v;
			$this->modifiedColumns[] = PartnerPeer::MODERATE_CONTENT;
		}

		return $this;
	} // setModerateContent()

	/**
	 * Set the value of [notify] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setNotify($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::NOTIFY]))
			$this->oldColumnsValues[PartnerPeer::NOTIFY] = $this->notify;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->notify !== $v || $this->isNew()) {
			$this->notify = $v;
			$this->modifiedColumns[] = PartnerPeer::NOTIFY;
		}

		return $this;
	} // setNotify()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = PartnerPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [service_config_id] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setServiceConfigId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::SERVICE_CONFIG_ID]))
			$this->oldColumnsValues[PartnerPeer::SERVICE_CONFIG_ID] = $this->service_config_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->service_config_id !== $v) {
			$this->service_config_id = $v;
			$this->modifiedColumns[] = PartnerPeer::SERVICE_CONFIG_ID;
		}

		return $this;
	} // setServiceConfigId()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::STATUS]))
			$this->oldColumnsValues[PartnerPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v || $this->isNew()) {
			$this->status = $v;
			$this->modifiedColumns[] = PartnerPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [content_categories] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setContentCategories($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::CONTENT_CATEGORIES]))
			$this->oldColumnsValues[PartnerPeer::CONTENT_CATEGORIES] = $this->content_categories;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->content_categories !== $v) {
			$this->content_categories = $v;
			$this->modifiedColumns[] = PartnerPeer::CONTENT_CATEGORIES;
		}

		return $this;
	} // setContentCategories()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::TYPE]))
			$this->oldColumnsValues[PartnerPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = PartnerPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [phone] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPhone($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PHONE]))
			$this->oldColumnsValues[PartnerPeer::PHONE] = $this->phone;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->phone !== $v) {
			$this->phone = $v;
			$this->modifiedColumns[] = PartnerPeer::PHONE;
		}

		return $this;
	} // setPhone()

	/**
	 * Set the value of [describe_yourself] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setDescribeYourself($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::DESCRIBE_YOURSELF]))
			$this->oldColumnsValues[PartnerPeer::DESCRIBE_YOURSELF] = $this->describe_yourself;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->describe_yourself !== $v) {
			$this->describe_yourself = $v;
			$this->modifiedColumns[] = PartnerPeer::DESCRIBE_YOURSELF;
		}

		return $this;
	} // setDescribeYourself()

	/**
	 * Set the value of [adult_content] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setAdultContent($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::ADULT_CONTENT]))
			$this->oldColumnsValues[PartnerPeer::ADULT_CONTENT] = $this->adult_content;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->adult_content !== $v || $this->isNew()) {
			$this->adult_content = $v;
			$this->modifiedColumns[] = PartnerPeer::ADULT_CONTENT;
		}

		return $this;
	} // setAdultContent()

	/**
	 * Set the value of [partner_package] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPartnerPackage($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PARTNER_PACKAGE]))
			$this->oldColumnsValues[PartnerPeer::PARTNER_PACKAGE] = $this->partner_package;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_package !== $v || $this->isNew()) {
			$this->partner_package = $v;
			$this->modifiedColumns[] = PartnerPeer::PARTNER_PACKAGE;
		}

		return $this;
	} // setPartnerPackage()

	/**
	 * Set the value of [usage_percent] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setUsagePercent($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::USAGE_PERCENT]))
			$this->oldColumnsValues[PartnerPeer::USAGE_PERCENT] = $this->usage_percent;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->usage_percent !== $v || $this->isNew()) {
			$this->usage_percent = $v;
			$this->modifiedColumns[] = PartnerPeer::USAGE_PERCENT;
		}

		return $this;
	} // setUsagePercent()

	/**
	 * Set the value of [storage_usage] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setStorageUsage($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::STORAGE_USAGE]))
			$this->oldColumnsValues[PartnerPeer::STORAGE_USAGE] = $this->storage_usage;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->storage_usage !== $v || $this->isNew()) {
			$this->storage_usage = $v;
			$this->modifiedColumns[] = PartnerPeer::STORAGE_USAGE;
		}

		return $this;
	} // setStorageUsage()

	/**
	 * Set the value of [eighty_percent_warning] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setEightyPercentWarning($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::EIGHTY_PERCENT_WARNING]))
			$this->oldColumnsValues[PartnerPeer::EIGHTY_PERCENT_WARNING] = $this->eighty_percent_warning;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eighty_percent_warning !== $v) {
			$this->eighty_percent_warning = $v;
			$this->modifiedColumns[] = PartnerPeer::EIGHTY_PERCENT_WARNING;
		}

		return $this;
	} // setEightyPercentWarning()

	/**
	 * Set the value of [usage_limit_warning] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setUsageLimitWarning($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::USAGE_LIMIT_WARNING]))
			$this->oldColumnsValues[PartnerPeer::USAGE_LIMIT_WARNING] = $this->usage_limit_warning;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->usage_limit_warning !== $v) {
			$this->usage_limit_warning = $v;
			$this->modifiedColumns[] = PartnerPeer::USAGE_LIMIT_WARNING;
		}

		return $this;
	} // setUsageLimitWarning()

	/**
	 * Set the value of [monitor_usage] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setMonitorUsage($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::MONITOR_USAGE]))
			$this->oldColumnsValues[PartnerPeer::MONITOR_USAGE] = $this->monitor_usage;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->monitor_usage !== $v || $this->isNew()) {
			$this->monitor_usage = $v;
			$this->modifiedColumns[] = PartnerPeer::MONITOR_USAGE;
		}

		return $this;
	} // setMonitorUsage()

	/**
	 * Set the value of [priority_group_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPriorityGroupId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PRIORITY_GROUP_ID]))
			$this->oldColumnsValues[PartnerPeer::PRIORITY_GROUP_ID] = $this->priority_group_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority_group_id !== $v) {
			$this->priority_group_id = $v;
			$this->modifiedColumns[] = PartnerPeer::PRIORITY_GROUP_ID;
		}

		return $this;
	} // setPriorityGroupId()

	/**
	 * Set the value of [partner_group_type] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPartnerGroupType($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PARTNER_GROUP_TYPE]))
			$this->oldColumnsValues[PartnerPeer::PARTNER_GROUP_TYPE] = $this->partner_group_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_group_type !== $v || $this->isNew()) {
			$this->partner_group_type = $v;
			$this->modifiedColumns[] = PartnerPeer::PARTNER_GROUP_TYPE;
		}

		return $this;
	} // setPartnerGroupType()

	/**
	 * Set the value of [partner_parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setPartnerParentId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::PARTNER_PARENT_ID]))
			$this->oldColumnsValues[PartnerPeer::PARTNER_PARENT_ID] = $this->partner_parent_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_parent_id !== $v) {
			$this->partner_parent_id = $v;
			$this->modifiedColumns[] = PartnerPeer::PARTNER_PARENT_ID;
		}

		return $this;
	} // setPartnerParentId()

	/**
	 * Set the value of [kmc_version] column.
	 * 
	 * @param      string $v new value
	 * @return     Partner The current object (for fluent API support)
	 */
	public function setKmcVersion($v)
	{
		if(!isset($this->oldColumnsValues[PartnerPeer::KMC_VERSION]))
			$this->oldColumnsValues[PartnerPeer::KMC_VERSION] = $this->kmc_version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->kmc_version !== $v || $this->isNew()) {
			$this->kmc_version = $v;
			$this->modifiedColumns[] = PartnerPeer::KMC_VERSION;
		}

		return $this;
	} // setKmcVersion()

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
			if ($this->max_number_of_hits_per_day !== -1) {
				return false;
			}

			if ($this->appear_in_search !== 2) {
				return false;
			}

			if ($this->debug_level !== 0) {
				return false;
			}

			if ($this->invalid_login_count !== 0) {
				return false;
			}

			if ($this->ks_max_expiry_in_seconds !== 86400) {
				return false;
			}

			if ($this->create_user_on_demand !== 1) {
				return false;
			}

			if ($this->commercial_use !== 0) {
				return false;
			}

			if ($this->moderate_content !== 0) {
				return false;
			}

			if ($this->notify !== 0) {
				return false;
			}

			if ($this->status !== 1) {
				return false;
			}

			if ($this->type !== 1) {
				return false;
			}

			if ($this->adult_content !== 0) {
				return false;
			}

			if ($this->partner_package !== 1) {
				return false;
			}

			if ($this->usage_percent !== 0) {
				return false;
			}

			if ($this->storage_usage !== 0) {
				return false;
			}

			if ($this->monitor_usage !== 1) {
				return false;
			}

			if ($this->partner_group_type !== 1) {
				return false;
			}

			if ($this->kmc_version !== '1') {
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
			$this->partner_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->partner_alias = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->url1 = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->url2 = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->secret = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->admin_secret = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->max_number_of_hits_per_day = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->appear_in_search = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->debug_level = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->invalid_login_count = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->anonymous_kuser_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->ks_max_expiry_in_seconds = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->create_user_on_demand = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->prefix = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->admin_name = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->admin_email = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->description = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->commercial_use = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->moderate_content = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->notify = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->custom_data = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->service_config_id = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->status = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->content_categories = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->type = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->phone = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->describe_yourself = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->adult_content = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->partner_package = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->usage_percent = ($row[$startcol + 32] !== null) ? (int) $row[$startcol + 32] : null;
			$this->storage_usage = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->eighty_percent_warning = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->usage_limit_warning = ($row[$startcol + 35] !== null) ? (int) $row[$startcol + 35] : null;
			$this->monitor_usage = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->priority_group_id = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->partner_group_type = ($row[$startcol + 38] !== null) ? (int) $row[$startcol + 38] : null;
			$this->partner_parent_id = ($row[$startcol + 39] !== null) ? (int) $row[$startcol + 39] : null;
			$this->kmc_version = ($row[$startcol + 40] !== null) ? (string) $row[$startcol + 40] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 41; // 41 = PartnerPeer::NUM_COLUMNS - PartnerPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Partner object", $e);
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

		if ($this->akuser !== null && $this->anonymous_kuser_id !== $this->akuser->getId()) {
			$this->akuser = null;
		}
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
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = PartnerPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->akuser = null;
			$this->colladminKusers = null;
			$this->lastadminKuserCriteria = null;

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
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				PartnerPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				PartnerPeer::addInstanceToPool($this);
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

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->akuser !== null) {
				if ($this->akuser->isModified() || $this->akuser->isNew()) {
					$affectedRows += $this->akuser->save($con);
				}
				$this->setkuser($this->akuser);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = PartnerPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PartnerPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += PartnerPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->colladminKusers !== null) {
				foreach ($this->colladminKusers as $referrerFK) {
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
		PartnerPeer::setUseCriteriaFilter(false);
		$this->reload();
		PartnerPeer::setUseCriteriaFilter(true);
		
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->akuser !== null) {
				if (!$this->akuser->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->akuser->getValidationFailures());
				}
			}


			if (($retval = PartnerPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->colladminKusers !== null) {
					foreach ($this->colladminKusers as $referrerFK) {
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
		$pos = PartnerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPartnerName();
				break;
			case 2:
				return $this->getPartnerAlias();
				break;
			case 3:
				return $this->getUrl1();
				break;
			case 4:
				return $this->getUrl2();
				break;
			case 5:
				return $this->getSecret();
				break;
			case 6:
				return $this->getAdminSecret();
				break;
			case 7:
				return $this->getMaxNumberOfHitsPerDay();
				break;
			case 8:
				return $this->getAppearInSearch();
				break;
			case 9:
				return $this->getDebugLevel();
				break;
			case 10:
				return $this->getInvalidLoginCount();
				break;
			case 11:
				return $this->getCreatedAt();
				break;
			case 12:
				return $this->getUpdatedAt();
				break;
			case 13:
				return $this->getAnonymousKuserId();
				break;
			case 14:
				return $this->getKsMaxExpiryInSeconds();
				break;
			case 15:
				return $this->getCreateUserOnDemand();
				break;
			case 16:
				return $this->getPrefix();
				break;
			case 17:
				return $this->getAdminName();
				break;
			case 18:
				return $this->getAdminEmail();
				break;
			case 19:
				return $this->getDescription();
				break;
			case 20:
				return $this->getCommercialUse();
				break;
			case 21:
				return $this->getModerateContent();
				break;
			case 22:
				return $this->getNotify();
				break;
			case 23:
				return $this->getCustomData();
				break;
			case 24:
				return $this->getServiceConfigId();
				break;
			case 25:
				return $this->getStatus();
				break;
			case 26:
				return $this->getContentCategories();
				break;
			case 27:
				return $this->getType();
				break;
			case 28:
				return $this->getPhone();
				break;
			case 29:
				return $this->getDescribeYourself();
				break;
			case 30:
				return $this->getAdultContent();
				break;
			case 31:
				return $this->getPartnerPackage();
				break;
			case 32:
				return $this->getUsagePercent();
				break;
			case 33:
				return $this->getStorageUsage();
				break;
			case 34:
				return $this->getEightyPercentWarning();
				break;
			case 35:
				return $this->getUsageLimitWarning();
				break;
			case 36:
				return $this->getMonitorUsage();
				break;
			case 37:
				return $this->getPriorityGroupId();
				break;
			case 38:
				return $this->getPartnerGroupType();
				break;
			case 39:
				return $this->getPartnerParentId();
				break;
			case 40:
				return $this->getKmcVersion();
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
		$keys = PartnerPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerName(),
			$keys[2] => $this->getPartnerAlias(),
			$keys[3] => $this->getUrl1(),
			$keys[4] => $this->getUrl2(),
			$keys[5] => $this->getSecret(),
			$keys[6] => $this->getAdminSecret(),
			$keys[7] => $this->getMaxNumberOfHitsPerDay(),
			$keys[8] => $this->getAppearInSearch(),
			$keys[9] => $this->getDebugLevel(),
			$keys[10] => $this->getInvalidLoginCount(),
			$keys[11] => $this->getCreatedAt(),
			$keys[12] => $this->getUpdatedAt(),
			$keys[13] => $this->getAnonymousKuserId(),
			$keys[14] => $this->getKsMaxExpiryInSeconds(),
			$keys[15] => $this->getCreateUserOnDemand(),
			$keys[16] => $this->getPrefix(),
			$keys[17] => $this->getAdminName(),
			$keys[18] => $this->getAdminEmail(),
			$keys[19] => $this->getDescription(),
			$keys[20] => $this->getCommercialUse(),
			$keys[21] => $this->getModerateContent(),
			$keys[22] => $this->getNotify(),
			$keys[23] => $this->getCustomData(),
			$keys[24] => $this->getServiceConfigId(),
			$keys[25] => $this->getStatus(),
			$keys[26] => $this->getContentCategories(),
			$keys[27] => $this->getType(),
			$keys[28] => $this->getPhone(),
			$keys[29] => $this->getDescribeYourself(),
			$keys[30] => $this->getAdultContent(),
			$keys[31] => $this->getPartnerPackage(),
			$keys[32] => $this->getUsagePercent(),
			$keys[33] => $this->getStorageUsage(),
			$keys[34] => $this->getEightyPercentWarning(),
			$keys[35] => $this->getUsageLimitWarning(),
			$keys[36] => $this->getMonitorUsage(),
			$keys[37] => $this->getPriorityGroupId(),
			$keys[38] => $this->getPartnerGroupType(),
			$keys[39] => $this->getPartnerParentId(),
			$keys[40] => $this->getKmcVersion(),
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
		$pos = PartnerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPartnerName($value);
				break;
			case 2:
				$this->setPartnerAlias($value);
				break;
			case 3:
				$this->setUrl1($value);
				break;
			case 4:
				$this->setUrl2($value);
				break;
			case 5:
				$this->setSecret($value);
				break;
			case 6:
				$this->setAdminSecret($value);
				break;
			case 7:
				$this->setMaxNumberOfHitsPerDay($value);
				break;
			case 8:
				$this->setAppearInSearch($value);
				break;
			case 9:
				$this->setDebugLevel($value);
				break;
			case 10:
				$this->setInvalidLoginCount($value);
				break;
			case 11:
				$this->setCreatedAt($value);
				break;
			case 12:
				$this->setUpdatedAt($value);
				break;
			case 13:
				$this->setAnonymousKuserId($value);
				break;
			case 14:
				$this->setKsMaxExpiryInSeconds($value);
				break;
			case 15:
				$this->setCreateUserOnDemand($value);
				break;
			case 16:
				$this->setPrefix($value);
				break;
			case 17:
				$this->setAdminName($value);
				break;
			case 18:
				$this->setAdminEmail($value);
				break;
			case 19:
				$this->setDescription($value);
				break;
			case 20:
				$this->setCommercialUse($value);
				break;
			case 21:
				$this->setModerateContent($value);
				break;
			case 22:
				$this->setNotify($value);
				break;
			case 23:
				$this->setCustomData($value);
				break;
			case 24:
				$this->setServiceConfigId($value);
				break;
			case 25:
				$this->setStatus($value);
				break;
			case 26:
				$this->setContentCategories($value);
				break;
			case 27:
				$this->setType($value);
				break;
			case 28:
				$this->setPhone($value);
				break;
			case 29:
				$this->setDescribeYourself($value);
				break;
			case 30:
				$this->setAdultContent($value);
				break;
			case 31:
				$this->setPartnerPackage($value);
				break;
			case 32:
				$this->setUsagePercent($value);
				break;
			case 33:
				$this->setStorageUsage($value);
				break;
			case 34:
				$this->setEightyPercentWarning($value);
				break;
			case 35:
				$this->setUsageLimitWarning($value);
				break;
			case 36:
				$this->setMonitorUsage($value);
				break;
			case 37:
				$this->setPriorityGroupId($value);
				break;
			case 38:
				$this->setPartnerGroupType($value);
				break;
			case 39:
				$this->setPartnerParentId($value);
				break;
			case 40:
				$this->setKmcVersion($value);
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
		$keys = PartnerPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerAlias($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUrl1($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUrl2($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSecret($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAdminSecret($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setMaxNumberOfHitsPerDay($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAppearInSearch($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDebugLevel($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setInvalidLoginCount($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setAnonymousKuserId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setKsMaxExpiryInSeconds($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCreateUserOnDemand($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setPrefix($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setAdminName($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setAdminEmail($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setDescription($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setCommercialUse($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setModerateContent($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setNotify($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setCustomData($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setServiceConfigId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setStatus($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setContentCategories($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setType($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setPhone($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setDescribeYourself($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setAdultContent($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setPartnerPackage($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setUsagePercent($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setStorageUsage($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setEightyPercentWarning($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setUsageLimitWarning($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setMonitorUsage($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setPriorityGroupId($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setPartnerGroupType($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setPartnerParentId($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setKmcVersion($arr[$keys[40]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PartnerPeer::DATABASE_NAME);

		if ($this->isColumnModified(PartnerPeer::ID)) $criteria->add(PartnerPeer::ID, $this->id);
		if ($this->isColumnModified(PartnerPeer::PARTNER_NAME)) $criteria->add(PartnerPeer::PARTNER_NAME, $this->partner_name);
		if ($this->isColumnModified(PartnerPeer::PARTNER_ALIAS)) $criteria->add(PartnerPeer::PARTNER_ALIAS, $this->partner_alias);
		if ($this->isColumnModified(PartnerPeer::URL1)) $criteria->add(PartnerPeer::URL1, $this->url1);
		if ($this->isColumnModified(PartnerPeer::URL2)) $criteria->add(PartnerPeer::URL2, $this->url2);
		if ($this->isColumnModified(PartnerPeer::SECRET)) $criteria->add(PartnerPeer::SECRET, $this->secret);
		if ($this->isColumnModified(PartnerPeer::ADMIN_SECRET)) $criteria->add(PartnerPeer::ADMIN_SECRET, $this->admin_secret);
		if ($this->isColumnModified(PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY)) $criteria->add(PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY, $this->max_number_of_hits_per_day);
		if ($this->isColumnModified(PartnerPeer::APPEAR_IN_SEARCH)) $criteria->add(PartnerPeer::APPEAR_IN_SEARCH, $this->appear_in_search);
		if ($this->isColumnModified(PartnerPeer::DEBUG_LEVEL)) $criteria->add(PartnerPeer::DEBUG_LEVEL, $this->debug_level);
		if ($this->isColumnModified(PartnerPeer::INVALID_LOGIN_COUNT)) $criteria->add(PartnerPeer::INVALID_LOGIN_COUNT, $this->invalid_login_count);
		if ($this->isColumnModified(PartnerPeer::CREATED_AT)) $criteria->add(PartnerPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(PartnerPeer::UPDATED_AT)) $criteria->add(PartnerPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(PartnerPeer::ANONYMOUS_KUSER_ID)) $criteria->add(PartnerPeer::ANONYMOUS_KUSER_ID, $this->anonymous_kuser_id);
		if ($this->isColumnModified(PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS)) $criteria->add(PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS, $this->ks_max_expiry_in_seconds);
		if ($this->isColumnModified(PartnerPeer::CREATE_USER_ON_DEMAND)) $criteria->add(PartnerPeer::CREATE_USER_ON_DEMAND, $this->create_user_on_demand);
		if ($this->isColumnModified(PartnerPeer::PREFIX)) $criteria->add(PartnerPeer::PREFIX, $this->prefix);
		if ($this->isColumnModified(PartnerPeer::ADMIN_NAME)) $criteria->add(PartnerPeer::ADMIN_NAME, $this->admin_name);
		if ($this->isColumnModified(PartnerPeer::ADMIN_EMAIL)) $criteria->add(PartnerPeer::ADMIN_EMAIL, $this->admin_email);
		if ($this->isColumnModified(PartnerPeer::DESCRIPTION)) $criteria->add(PartnerPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(PartnerPeer::COMMERCIAL_USE)) $criteria->add(PartnerPeer::COMMERCIAL_USE, $this->commercial_use);
		if ($this->isColumnModified(PartnerPeer::MODERATE_CONTENT)) $criteria->add(PartnerPeer::MODERATE_CONTENT, $this->moderate_content);
		if ($this->isColumnModified(PartnerPeer::NOTIFY)) $criteria->add(PartnerPeer::NOTIFY, $this->notify);
		if ($this->isColumnModified(PartnerPeer::CUSTOM_DATA)) $criteria->add(PartnerPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(PartnerPeer::SERVICE_CONFIG_ID)) $criteria->add(PartnerPeer::SERVICE_CONFIG_ID, $this->service_config_id);
		if ($this->isColumnModified(PartnerPeer::STATUS)) $criteria->add(PartnerPeer::STATUS, $this->status);
		if ($this->isColumnModified(PartnerPeer::CONTENT_CATEGORIES)) $criteria->add(PartnerPeer::CONTENT_CATEGORIES, $this->content_categories);
		if ($this->isColumnModified(PartnerPeer::TYPE)) $criteria->add(PartnerPeer::TYPE, $this->type);
		if ($this->isColumnModified(PartnerPeer::PHONE)) $criteria->add(PartnerPeer::PHONE, $this->phone);
		if ($this->isColumnModified(PartnerPeer::DESCRIBE_YOURSELF)) $criteria->add(PartnerPeer::DESCRIBE_YOURSELF, $this->describe_yourself);
		if ($this->isColumnModified(PartnerPeer::ADULT_CONTENT)) $criteria->add(PartnerPeer::ADULT_CONTENT, $this->adult_content);
		if ($this->isColumnModified(PartnerPeer::PARTNER_PACKAGE)) $criteria->add(PartnerPeer::PARTNER_PACKAGE, $this->partner_package);
		if ($this->isColumnModified(PartnerPeer::USAGE_PERCENT)) $criteria->add(PartnerPeer::USAGE_PERCENT, $this->usage_percent);
		if ($this->isColumnModified(PartnerPeer::STORAGE_USAGE)) $criteria->add(PartnerPeer::STORAGE_USAGE, $this->storage_usage);
		if ($this->isColumnModified(PartnerPeer::EIGHTY_PERCENT_WARNING)) $criteria->add(PartnerPeer::EIGHTY_PERCENT_WARNING, $this->eighty_percent_warning);
		if ($this->isColumnModified(PartnerPeer::USAGE_LIMIT_WARNING)) $criteria->add(PartnerPeer::USAGE_LIMIT_WARNING, $this->usage_limit_warning);
		if ($this->isColumnModified(PartnerPeer::MONITOR_USAGE)) $criteria->add(PartnerPeer::MONITOR_USAGE, $this->monitor_usage);
		if ($this->isColumnModified(PartnerPeer::PRIORITY_GROUP_ID)) $criteria->add(PartnerPeer::PRIORITY_GROUP_ID, $this->priority_group_id);
		if ($this->isColumnModified(PartnerPeer::PARTNER_GROUP_TYPE)) $criteria->add(PartnerPeer::PARTNER_GROUP_TYPE, $this->partner_group_type);
		if ($this->isColumnModified(PartnerPeer::PARTNER_PARENT_ID)) $criteria->add(PartnerPeer::PARTNER_PARENT_ID, $this->partner_parent_id);
		if ($this->isColumnModified(PartnerPeer::KMC_VERSION)) $criteria->add(PartnerPeer::KMC_VERSION, $this->kmc_version);

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
		$criteria = new Criteria(PartnerPeer::DATABASE_NAME);

		$criteria->add(PartnerPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Partner (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerName($this->partner_name);

		$copyObj->setPartnerAlias($this->partner_alias);

		$copyObj->setUrl1($this->url1);

		$copyObj->setUrl2($this->url2);

		$copyObj->setSecret($this->secret);

		$copyObj->setAdminSecret($this->admin_secret);

		$copyObj->setMaxNumberOfHitsPerDay($this->max_number_of_hits_per_day);

		$copyObj->setAppearInSearch($this->appear_in_search);

		$copyObj->setDebugLevel($this->debug_level);

		$copyObj->setInvalidLoginCount($this->invalid_login_count);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setAnonymousKuserId($this->anonymous_kuser_id);

		$copyObj->setKsMaxExpiryInSeconds($this->ks_max_expiry_in_seconds);

		$copyObj->setCreateUserOnDemand($this->create_user_on_demand);

		$copyObj->setPrefix($this->prefix);

		$copyObj->setAdminName($this->admin_name);

		$copyObj->setAdminEmail($this->admin_email);

		$copyObj->setDescription($this->description);

		$copyObj->setCommercialUse($this->commercial_use);

		$copyObj->setModerateContent($this->moderate_content);

		$copyObj->setNotify($this->notify);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setServiceConfigId($this->service_config_id);

		$copyObj->setStatus($this->status);

		$copyObj->setContentCategories($this->content_categories);

		$copyObj->setType($this->type);

		$copyObj->setPhone($this->phone);

		$copyObj->setDescribeYourself($this->describe_yourself);

		$copyObj->setAdultContent($this->adult_content);

		$copyObj->setPartnerPackage($this->partner_package);

		$copyObj->setUsagePercent($this->usage_percent);

		$copyObj->setStorageUsage($this->storage_usage);

		$copyObj->setEightyPercentWarning($this->eighty_percent_warning);

		$copyObj->setUsageLimitWarning($this->usage_limit_warning);

		$copyObj->setMonitorUsage($this->monitor_usage);

		$copyObj->setPriorityGroupId($this->priority_group_id);

		$copyObj->setPartnerGroupType($this->partner_group_type);

		$copyObj->setPartnerParentId($this->partner_parent_id);

		$copyObj->setKmcVersion($this->kmc_version);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getadminKusers() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addadminKuser($relObj->copy($deepCopy));
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
	 * @return     Partner Clone of current object.
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
	 * @var     Partner Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      Partner $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(Partner $copiedFrom)
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
	 * @return     PartnerPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PartnerPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a kuser object.
	 *
	 * @param      kuser $v
	 * @return     Partner The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setkuser(kuser $v = null)
	{
		if ($v === null) {
			$this->setAnonymousKuserId(NULL);
		} else {
			$this->setAnonymousKuserId($v->getId());
		}

		$this->akuser = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the kuser object, it will not be re-added.
		if ($v !== null) {
			$v->addPartner($this);
		}

		return $this;
	}


	/**
	 * Get the associated kuser object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     kuser The associated kuser object.
	 * @throws     PropelException
	 */
	public function getkuser(PropelPDO $con = null)
	{
		if ($this->akuser === null && ($this->anonymous_kuser_id !== null)) {
			$this->akuser = kuserPeer::retrieveByPk($this->anonymous_kuser_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->akuser->addPartners($this);
			 */
		}
		return $this->akuser;
	}

	/**
	 * Clears out the colladminKusers collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addadminKusers()
	 */
	public function clearadminKusers()
	{
		$this->colladminKusers = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the colladminKusers collection (array).
	 *
	 * By default this just sets the colladminKusers collection to an empty array (like clearcolladminKusers());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initadminKusers()
	{
		$this->colladminKusers = array();
	}

	/**
	 * Gets an array of adminKuser objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Partner has previously been saved, it will retrieve
	 * related adminKusers from storage. If this Partner is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array adminKuser[]
	 * @throws     PropelException
	 */
	public function getadminKusers($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(PartnerPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->colladminKusers === null) {
			if ($this->isNew()) {
			   $this->colladminKusers = array();
			} else {

				$criteria->add(adminKuserPeer::PARTNER_ID, $this->id);

				adminKuserPeer::addSelectColumns($criteria);
				$this->colladminKusers = adminKuserPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(adminKuserPeer::PARTNER_ID, $this->id);

				adminKuserPeer::addSelectColumns($criteria);
				if (!isset($this->lastadminKuserCriteria) || !$this->lastadminKuserCriteria->equals($criteria)) {
					$this->colladminKusers = adminKuserPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastadminKuserCriteria = $criteria;
		return $this->colladminKusers;
	}

	/**
	 * Returns the number of related adminKuser objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related adminKuser objects.
	 * @throws     PropelException
	 */
	public function countadminKusers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(PartnerPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->colladminKusers === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(adminKuserPeer::PARTNER_ID, $this->id);

				$count = adminKuserPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(adminKuserPeer::PARTNER_ID, $this->id);

				if (!isset($this->lastadminKuserCriteria) || !$this->lastadminKuserCriteria->equals($criteria)) {
					$count = adminKuserPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->colladminKusers);
				}
			} else {
				$count = count($this->colladminKusers);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a adminKuser object to this object
	 * through the adminKuser foreign key attribute.
	 *
	 * @param      adminKuser $l adminKuser
	 * @return     void
	 * @throws     PropelException
	 */
	public function addadminKuser(adminKuser $l)
	{
		if ($this->colladminKusers === null) {
			$this->initadminKusers();
		}
		if (!in_array($l, $this->colladminKusers, true)) { // only add it if the **same** object is not already associated
			array_push($this->colladminKusers, $l);
			$l->setPartner($this);
		}
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
			if ($this->colladminKusers) {
				foreach ((array) $this->colladminKusers as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->colladminKusers = null;
			$this->akuser = null;
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
	
} // BasePartner
