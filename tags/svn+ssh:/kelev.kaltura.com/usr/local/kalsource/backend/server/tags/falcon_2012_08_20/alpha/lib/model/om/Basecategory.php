<?php

/**
 * Base class that represents a row from the 'category' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class Basecategory extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        categoryPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the parent_id field.
	 * @var        int
	 */
	protected $parent_id;

	/**
	 * The value for the depth field.
	 * @var        int
	 */
	protected $depth;

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
	 * The value for the full_name field.
	 * @var        string
	 */
	protected $full_name;

	/**
	 * The value for the full_ids field.
	 * @var        string
	 */
	protected $full_ids;

	/**
	 * The value for the entries_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $entries_count;

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
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the direct_entries_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $direct_entries_count;

	/**
	 * The value for the direct_sub_categories_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $direct_sub_categories_count;

	/**
	 * The value for the members_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $members_count;

	/**
	 * The value for the pending_members_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $pending_members_count;

	/**
	 * The value for the pending_entries_count field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $pending_entries_count;

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
	 * The value for the display_in_search field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $display_in_search;

	/**
	 * The value for the privacy field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $privacy;

	/**
	 * The value for the inheritance_type field.
	 * Note: this column has a database default value of: 2
	 * @var        int
	 */
	protected $inheritance_type;

	/**
	 * The value for the user_join_policy field.
	 * Note: this column has a database default value of: 3
	 * @var        int
	 */
	protected $user_join_policy;

	/**
	 * The value for the default_permission_level field.
	 * Note: this column has a database default value of: 3
	 * @var        int
	 */
	protected $default_permission_level;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the puser_id field.
	 * @var        string
	 */
	protected $puser_id;

	/**
	 * The value for the reference_id field.
	 * @var        string
	 */
	protected $reference_id;

	/**
	 * The value for the contribution_policy field.
	 * Note: this column has a database default value of: 2
	 * @var        int
	 */
	protected $contribution_policy;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the privacy_context field.
	 * @var        string
	 */
	protected $privacy_context;

	/**
	 * The value for the privacy_contexts field.
	 * @var        string
	 */
	protected $privacy_contexts;

	/**
	 * The value for the inherited_parent_id field.
	 * @var        int
	 */
	protected $inherited_parent_id;

	/**
	 * The value for the moderation field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $moderation;

	/**
	 * @var        array categoryKuser[] Collection to store aggregation of categoryKuser objects.
	 */
	protected $collcategoryKusers;

	/**
	 * @var        Criteria The criteria used to select the current contents of collcategoryKusers.
	 */
	private $lastcategoryKuserCriteria = null;

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
		$this->name = '';
		$this->entries_count = 0;
		$this->direct_entries_count = 0;
		$this->direct_sub_categories_count = 0;
		$this->members_count = 0;
		$this->pending_members_count = 0;
		$this->pending_entries_count = 0;
		$this->display_in_search = 1;
		$this->privacy = 1;
		$this->inheritance_type = 2;
		$this->user_join_policy = 3;
		$this->default_permission_level = 3;
		$this->contribution_policy = 2;
		$this->moderation = false;
	}

	/**
	 * Initializes internal state of Basecategory object.
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
	 * Get the [parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getParentId()
	{
		return $this->parent_id;
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
	 * Get the [full_name] column value.
	 * 
	 * @return     string
	 */
	public function getFullName()
	{
		return $this->full_name;
	}

	/**
	 * Get the [full_ids] column value.
	 * 
	 * @return     string
	 */
	public function getFullIds()
	{
		return $this->full_ids;
	}

	/**
	 * Get the [entries_count] column value.
	 * 
	 * @return     int
	 */
	public function getEntriesCount()
	{
		return $this->entries_count;
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
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the [direct_entries_count] column value.
	 * 
	 * @return     int
	 */
	public function getDirectEntriesCount()
	{
		return $this->direct_entries_count;
	}

	/**
	 * Get the [direct_sub_categories_count] column value.
	 * 
	 * @return     int
	 */
	public function getDirectSubCategoriesCount()
	{
		return $this->direct_sub_categories_count;
	}

	/**
	 * Get the [members_count] column value.
	 * 
	 * @return     int
	 */
	public function getMembersCount()
	{
		return $this->members_count;
	}

	/**
	 * Get the [pending_members_count] column value.
	 * 
	 * @return     int
	 */
	public function getPendingMembersCount()
	{
		return $this->pending_members_count;
	}

	/**
	 * Get the [pending_entries_count] column value.
	 * 
	 * @return     int
	 */
	public function getPendingEntriesCount()
	{
		return $this->pending_entries_count;
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
	 * Get the [display_in_search] column value.
	 * 
	 * @return     int
	 */
	public function getDisplayInSearch()
	{
		return $this->display_in_search;
	}

	/**
	 * Get the [privacy] column value.
	 * 
	 * @return     int
	 */
	public function getPrivacy()
	{
		return $this->privacy;
	}

	/**
	 * Get the [inheritance_type] column value.
	 * 
	 * @return     int
	 */
	public function getInheritanceType()
	{
		return $this->inheritance_type;
	}

	/**
	 * Get the [user_join_policy] column value.
	 * 
	 * @return     int
	 */
	public function getUserJoinPolicy()
	{
		return $this->user_join_policy;
	}

	/**
	 * Get the [default_permission_level] column value.
	 * 
	 * @return     int
	 */
	public function getDefaultPermissionLevel()
	{
		return $this->default_permission_level;
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
	 * Get the [puser_id] column value.
	 * 
	 * @return     string
	 */
	public function getPuserId()
	{
		return $this->puser_id;
	}

	/**
	 * Get the [reference_id] column value.
	 * 
	 * @return     string
	 */
	public function getReferenceId()
	{
		return $this->reference_id;
	}

	/**
	 * Get the [contribution_policy] column value.
	 * 
	 * @return     int
	 */
	public function getContributionPolicy()
	{
		return $this->contribution_policy;
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
	 * Get the [privacy_context] column value.
	 * 
	 * @return     string
	 */
	public function getPrivacyContext()
	{
		return $this->privacy_context;
	}

	/**
	 * Get the [privacy_contexts] column value.
	 * 
	 * @return     string
	 */
	public function getPrivacyContexts()
	{
		return $this->privacy_contexts;
	}

	/**
	 * Get the [inherited_parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getInheritedParentId()
	{
		return $this->inherited_parent_id;
	}

	/**
	 * Get the [moderation] column value.
	 * 
	 * @return     boolean
	 */
	public function getModeration()
	{
		return $this->moderation;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::ID]))
			$this->oldColumnsValues[categoryPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = categoryPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setParentId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PARENT_ID]))
			$this->oldColumnsValues[categoryPeer::PARENT_ID] = $this->parent_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->parent_id !== $v) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = categoryPeer::PARENT_ID;
		}

		return $this;
	} // setParentId()

	/**
	 * Set the value of [depth] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDepth($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DEPTH]))
			$this->oldColumnsValues[categoryPeer::DEPTH] = $this->depth;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->depth !== $v) {
			$this->depth = $v;
			$this->modifiedColumns[] = categoryPeer::DEPTH;
		}

		return $this;
	} // setDepth()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PARTNER_ID]))
			$this->oldColumnsValues[categoryPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = categoryPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::NAME]))
			$this->oldColumnsValues[categoryPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = categoryPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [full_name] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setFullName($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::FULL_NAME]))
			$this->oldColumnsValues[categoryPeer::FULL_NAME] = $this->full_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->full_name !== $v) {
			$this->full_name = $v;
			$this->modifiedColumns[] = categoryPeer::FULL_NAME;
		}

		return $this;
	} // setFullName()

	/**
	 * Set the value of [full_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setFullIds($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::FULL_IDS]))
			$this->oldColumnsValues[categoryPeer::FULL_IDS] = $this->full_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->full_ids !== $v) {
			$this->full_ids = $v;
			$this->modifiedColumns[] = categoryPeer::FULL_IDS;
		}

		return $this;
	} // setFullIds()

	/**
	 * Set the value of [entries_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setEntriesCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::ENTRIES_COUNT]))
			$this->oldColumnsValues[categoryPeer::ENTRIES_COUNT] = $this->entries_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->entries_count !== $v || $this->isNew()) {
			$this->entries_count = $v;
			$this->modifiedColumns[] = categoryPeer::ENTRIES_COUNT;
		}

		return $this;
	} // setEntriesCount()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     category The current object (for fluent API support)
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
				$this->modifiedColumns[] = categoryPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     category The current object (for fluent API support)
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
				$this->modifiedColumns[] = categoryPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     category The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DELETED_AT]))
			$this->oldColumnsValues[categoryPeer::DELETED_AT] = $this->deleted_at;

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
				$this->modifiedColumns[] = categoryPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::STATUS]))
			$this->oldColumnsValues[categoryPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = categoryPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [direct_entries_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDirectEntriesCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DIRECT_ENTRIES_COUNT]))
			$this->oldColumnsValues[categoryPeer::DIRECT_ENTRIES_COUNT] = $this->direct_entries_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->direct_entries_count !== $v || $this->isNew()) {
			$this->direct_entries_count = $v;
			$this->modifiedColumns[] = categoryPeer::DIRECT_ENTRIES_COUNT;
		}

		return $this;
	} // setDirectEntriesCount()

	/**
	 * Set the value of [direct_sub_categories_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDirectSubCategoriesCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DIRECT_SUB_CATEGORIES_COUNT]))
			$this->oldColumnsValues[categoryPeer::DIRECT_SUB_CATEGORIES_COUNT] = $this->direct_sub_categories_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->direct_sub_categories_count !== $v || $this->isNew()) {
			$this->direct_sub_categories_count = $v;
			$this->modifiedColumns[] = categoryPeer::DIRECT_SUB_CATEGORIES_COUNT;
		}

		return $this;
	} // setDirectSubCategoriesCount()

	/**
	 * Set the value of [members_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setMembersCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::MEMBERS_COUNT]))
			$this->oldColumnsValues[categoryPeer::MEMBERS_COUNT] = $this->members_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->members_count !== $v || $this->isNew()) {
			$this->members_count = $v;
			$this->modifiedColumns[] = categoryPeer::MEMBERS_COUNT;
		}

		return $this;
	} // setMembersCount()

	/**
	 * Set the value of [pending_members_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPendingMembersCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PENDING_MEMBERS_COUNT]))
			$this->oldColumnsValues[categoryPeer::PENDING_MEMBERS_COUNT] = $this->pending_members_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->pending_members_count !== $v || $this->isNew()) {
			$this->pending_members_count = $v;
			$this->modifiedColumns[] = categoryPeer::PENDING_MEMBERS_COUNT;
		}

		return $this;
	} // setPendingMembersCount()

	/**
	 * Set the value of [pending_entries_count] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPendingEntriesCount($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PENDING_ENTRIES_COUNT]))
			$this->oldColumnsValues[categoryPeer::PENDING_ENTRIES_COUNT] = $this->pending_entries_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->pending_entries_count !== $v || $this->isNew()) {
			$this->pending_entries_count = $v;
			$this->modifiedColumns[] = categoryPeer::PENDING_ENTRIES_COUNT;
		}

		return $this;
	} // setPendingEntriesCount()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DESCRIPTION]))
			$this->oldColumnsValues[categoryPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = categoryPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::TAGS]))
			$this->oldColumnsValues[categoryPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = categoryPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [display_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDisplayInSearch($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DISPLAY_IN_SEARCH]))
			$this->oldColumnsValues[categoryPeer::DISPLAY_IN_SEARCH] = $this->display_in_search;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_in_search !== $v || $this->isNew()) {
			$this->display_in_search = $v;
			$this->modifiedColumns[] = categoryPeer::DISPLAY_IN_SEARCH;
		}

		return $this;
	} // setDisplayInSearch()

	/**
	 * Set the value of [privacy] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPrivacy($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PRIVACY]))
			$this->oldColumnsValues[categoryPeer::PRIVACY] = $this->privacy;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->privacy !== $v || $this->isNew()) {
			$this->privacy = $v;
			$this->modifiedColumns[] = categoryPeer::PRIVACY;
		}

		return $this;
	} // setPrivacy()

	/**
	 * Set the value of [inheritance_type] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setInheritanceType($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::INHERITANCE_TYPE]))
			$this->oldColumnsValues[categoryPeer::INHERITANCE_TYPE] = $this->inheritance_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->inheritance_type !== $v || $this->isNew()) {
			$this->inheritance_type = $v;
			$this->modifiedColumns[] = categoryPeer::INHERITANCE_TYPE;
		}

		return $this;
	} // setInheritanceType()

	/**
	 * Set the value of [user_join_policy] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setUserJoinPolicy($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::USER_JOIN_POLICY]))
			$this->oldColumnsValues[categoryPeer::USER_JOIN_POLICY] = $this->user_join_policy;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->user_join_policy !== $v || $this->isNew()) {
			$this->user_join_policy = $v;
			$this->modifiedColumns[] = categoryPeer::USER_JOIN_POLICY;
		}

		return $this;
	} // setUserJoinPolicy()

	/**
	 * Set the value of [default_permission_level] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setDefaultPermissionLevel($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::DEFAULT_PERMISSION_LEVEL]))
			$this->oldColumnsValues[categoryPeer::DEFAULT_PERMISSION_LEVEL] = $this->default_permission_level;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->default_permission_level !== $v || $this->isNew()) {
			$this->default_permission_level = $v;
			$this->modifiedColumns[] = categoryPeer::DEFAULT_PERMISSION_LEVEL;
		}

		return $this;
	} // setDefaultPermissionLevel()

	/**
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::KUSER_ID]))
			$this->oldColumnsValues[categoryPeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = categoryPeer::KUSER_ID;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [puser_id] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPuserId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PUSER_ID]))
			$this->oldColumnsValues[categoryPeer::PUSER_ID] = $this->puser_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->puser_id !== $v) {
			$this->puser_id = $v;
			$this->modifiedColumns[] = categoryPeer::PUSER_ID;
		}

		return $this;
	} // setPuserId()

	/**
	 * Set the value of [reference_id] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setReferenceId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::REFERENCE_ID]))
			$this->oldColumnsValues[categoryPeer::REFERENCE_ID] = $this->reference_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->reference_id !== $v) {
			$this->reference_id = $v;
			$this->modifiedColumns[] = categoryPeer::REFERENCE_ID;
		}

		return $this;
	} // setReferenceId()

	/**
	 * Set the value of [contribution_policy] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setContributionPolicy($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::CONTRIBUTION_POLICY]))
			$this->oldColumnsValues[categoryPeer::CONTRIBUTION_POLICY] = $this->contribution_policy;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->contribution_policy !== $v || $this->isNew()) {
			$this->contribution_policy = $v;
			$this->modifiedColumns[] = categoryPeer::CONTRIBUTION_POLICY;
		}

		return $this;
	} // setContributionPolicy()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = categoryPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [privacy_context] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPrivacyContext($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PRIVACY_CONTEXT]))
			$this->oldColumnsValues[categoryPeer::PRIVACY_CONTEXT] = $this->privacy_context;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->privacy_context !== $v) {
			$this->privacy_context = $v;
			$this->modifiedColumns[] = categoryPeer::PRIVACY_CONTEXT;
		}

		return $this;
	} // setPrivacyContext()

	/**
	 * Set the value of [privacy_contexts] column.
	 * 
	 * @param      string $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setPrivacyContexts($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::PRIVACY_CONTEXTS]))
			$this->oldColumnsValues[categoryPeer::PRIVACY_CONTEXTS] = $this->privacy_contexts;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->privacy_contexts !== $v) {
			$this->privacy_contexts = $v;
			$this->modifiedColumns[] = categoryPeer::PRIVACY_CONTEXTS;
		}

		return $this;
	} // setPrivacyContexts()

	/**
	 * Set the value of [inherited_parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setInheritedParentId($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::INHERITED_PARENT_ID]))
			$this->oldColumnsValues[categoryPeer::INHERITED_PARENT_ID] = $this->inherited_parent_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->inherited_parent_id !== $v) {
			$this->inherited_parent_id = $v;
			$this->modifiedColumns[] = categoryPeer::INHERITED_PARENT_ID;
		}

		return $this;
	} // setInheritedParentId()

	/**
	 * Set the value of [moderation] column.
	 * 
	 * @param      boolean $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setModeration($v)
	{
		if(!isset($this->oldColumnsValues[categoryPeer::MODERATION]))
			$this->oldColumnsValues[categoryPeer::MODERATION] = $this->moderation;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->moderation !== $v || $this->isNew()) {
			$this->moderation = $v;
			$this->modifiedColumns[] = categoryPeer::MODERATION;
		}

		return $this;
	} // setModeration()

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

			if ($this->entries_count !== 0) {
				return false;
			}

			if ($this->direct_entries_count !== 0) {
				return false;
			}

			if ($this->direct_sub_categories_count !== 0) {
				return false;
			}

			if ($this->members_count !== 0) {
				return false;
			}

			if ($this->pending_members_count !== 0) {
				return false;
			}

			if ($this->pending_entries_count !== 0) {
				return false;
			}

			if ($this->display_in_search !== 1) {
				return false;
			}

			if ($this->privacy !== 1) {
				return false;
			}

			if ($this->inheritance_type !== 2) {
				return false;
			}

			if ($this->user_join_policy !== 3) {
				return false;
			}

			if ($this->default_permission_level !== 3) {
				return false;
			}

			if ($this->contribution_policy !== 2) {
				return false;
			}

			if ($this->moderation !== false) {
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
			$this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->depth = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->partner_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->full_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->full_ids = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->entries_count = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->deleted_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->status = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->direct_entries_count = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->direct_sub_categories_count = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->members_count = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->pending_members_count = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->pending_entries_count = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->description = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->tags = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->display_in_search = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->privacy = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->inheritance_type = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->user_join_policy = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->default_permission_level = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->kuser_id = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->puser_id = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->reference_id = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->contribution_policy = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->custom_data = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->privacy_context = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->privacy_contexts = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
			$this->inherited_parent_id = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->moderation = ($row[$startcol + 32] !== null) ? (boolean) $row[$startcol + 32] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 33; // 33 = categoryPeer::NUM_COLUMNS - categoryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating category object", $e);
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
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		categoryPeer::setUseCriteriaFilter(false);
		$stmt = categoryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		categoryPeer::setUseCriteriaFilter(true);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collcategoryKusers = null;
			$this->lastcategoryKuserCriteria = null;

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
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				categoryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				categoryPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = categoryPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = categoryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = categoryPeer::doUpdate($this, $con);
					if($affectedObjects)
						$this->objectSaved = true;
						
					$affectedRows += $affectedObjects;
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collcategoryKusers !== null) {
				foreach ($this->collcategoryKusers as $referrerFK) {
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
	 * Code to be run after deleting the object from database
	 * @param PropelPDO $con
	 */
	public function postDelete(PropelPDO $con = null)
	{
		kEventsManager::raiseEvent(new kObjectErasedEvent($this));
		
		parent::postDelete($con);
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


			if (($retval = categoryPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collcategoryKusers !== null) {
					foreach ($this->collcategoryKusers as $referrerFK) {
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
		$pos = categoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getParentId();
				break;
			case 2:
				return $this->getDepth();
				break;
			case 3:
				return $this->getPartnerId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getFullName();
				break;
			case 6:
				return $this->getFullIds();
				break;
			case 7:
				return $this->getEntriesCount();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			case 9:
				return $this->getUpdatedAt();
				break;
			case 10:
				return $this->getDeletedAt();
				break;
			case 11:
				return $this->getStatus();
				break;
			case 12:
				return $this->getDirectEntriesCount();
				break;
			case 13:
				return $this->getDirectSubCategoriesCount();
				break;
			case 14:
				return $this->getMembersCount();
				break;
			case 15:
				return $this->getPendingMembersCount();
				break;
			case 16:
				return $this->getPendingEntriesCount();
				break;
			case 17:
				return $this->getDescription();
				break;
			case 18:
				return $this->getTags();
				break;
			case 19:
				return $this->getDisplayInSearch();
				break;
			case 20:
				return $this->getPrivacy();
				break;
			case 21:
				return $this->getInheritanceType();
				break;
			case 22:
				return $this->getUserJoinPolicy();
				break;
			case 23:
				return $this->getDefaultPermissionLevel();
				break;
			case 24:
				return $this->getKuserId();
				break;
			case 25:
				return $this->getPuserId();
				break;
			case 26:
				return $this->getReferenceId();
				break;
			case 27:
				return $this->getContributionPolicy();
				break;
			case 28:
				return $this->getCustomData();
				break;
			case 29:
				return $this->getPrivacyContext();
				break;
			case 30:
				return $this->getPrivacyContexts();
				break;
			case 31:
				return $this->getInheritedParentId();
				break;
			case 32:
				return $this->getModeration();
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
		$keys = categoryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getParentId(),
			$keys[2] => $this->getDepth(),
			$keys[3] => $this->getPartnerId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getFullName(),
			$keys[6] => $this->getFullIds(),
			$keys[7] => $this->getEntriesCount(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getUpdatedAt(),
			$keys[10] => $this->getDeletedAt(),
			$keys[11] => $this->getStatus(),
			$keys[12] => $this->getDirectEntriesCount(),
			$keys[13] => $this->getDirectSubCategoriesCount(),
			$keys[14] => $this->getMembersCount(),
			$keys[15] => $this->getPendingMembersCount(),
			$keys[16] => $this->getPendingEntriesCount(),
			$keys[17] => $this->getDescription(),
			$keys[18] => $this->getTags(),
			$keys[19] => $this->getDisplayInSearch(),
			$keys[20] => $this->getPrivacy(),
			$keys[21] => $this->getInheritanceType(),
			$keys[22] => $this->getUserJoinPolicy(),
			$keys[23] => $this->getDefaultPermissionLevel(),
			$keys[24] => $this->getKuserId(),
			$keys[25] => $this->getPuserId(),
			$keys[26] => $this->getReferenceId(),
			$keys[27] => $this->getContributionPolicy(),
			$keys[28] => $this->getCustomData(),
			$keys[29] => $this->getPrivacyContext(),
			$keys[30] => $this->getPrivacyContexts(),
			$keys[31] => $this->getInheritedParentId(),
			$keys[32] => $this->getModeration(),
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
		$pos = categoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setParentId($value);
				break;
			case 2:
				$this->setDepth($value);
				break;
			case 3:
				$this->setPartnerId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setFullName($value);
				break;
			case 6:
				$this->setFullIds($value);
				break;
			case 7:
				$this->setEntriesCount($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
			case 9:
				$this->setUpdatedAt($value);
				break;
			case 10:
				$this->setDeletedAt($value);
				break;
			case 11:
				$this->setStatus($value);
				break;
			case 12:
				$this->setDirectEntriesCount($value);
				break;
			case 13:
				$this->setDirectSubCategoriesCount($value);
				break;
			case 14:
				$this->setMembersCount($value);
				break;
			case 15:
				$this->setPendingMembersCount($value);
				break;
			case 16:
				$this->setPendingEntriesCount($value);
				break;
			case 17:
				$this->setDescription($value);
				break;
			case 18:
				$this->setTags($value);
				break;
			case 19:
				$this->setDisplayInSearch($value);
				break;
			case 20:
				$this->setPrivacy($value);
				break;
			case 21:
				$this->setInheritanceType($value);
				break;
			case 22:
				$this->setUserJoinPolicy($value);
				break;
			case 23:
				$this->setDefaultPermissionLevel($value);
				break;
			case 24:
				$this->setKuserId($value);
				break;
			case 25:
				$this->setPuserId($value);
				break;
			case 26:
				$this->setReferenceId($value);
				break;
			case 27:
				$this->setContributionPolicy($value);
				break;
			case 28:
				$this->setCustomData($value);
				break;
			case 29:
				$this->setPrivacyContext($value);
				break;
			case 30:
				$this->setPrivacyContexts($value);
				break;
			case 31:
				$this->setInheritedParentId($value);
				break;
			case 32:
				$this->setModeration($value);
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
		$keys = categoryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDepth($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPartnerId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFullName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFullIds($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setEntriesCount($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDeletedAt($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setStatus($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDirectEntriesCount($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDirectSubCategoriesCount($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setMembersCount($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setPendingMembersCount($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setPendingEntriesCount($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDescription($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setTags($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setDisplayInSearch($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setPrivacy($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setInheritanceType($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setUserJoinPolicy($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setDefaultPermissionLevel($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setKuserId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setPuserId($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setReferenceId($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setContributionPolicy($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setCustomData($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setPrivacyContext($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setPrivacyContexts($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setInheritedParentId($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setModeration($arr[$keys[32]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(categoryPeer::DATABASE_NAME);

		if ($this->isColumnModified(categoryPeer::ID)) $criteria->add(categoryPeer::ID, $this->id);
		if ($this->isColumnModified(categoryPeer::PARENT_ID)) $criteria->add(categoryPeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(categoryPeer::DEPTH)) $criteria->add(categoryPeer::DEPTH, $this->depth);
		if ($this->isColumnModified(categoryPeer::PARTNER_ID)) $criteria->add(categoryPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(categoryPeer::NAME)) $criteria->add(categoryPeer::NAME, $this->name);
		if ($this->isColumnModified(categoryPeer::FULL_NAME)) $criteria->add(categoryPeer::FULL_NAME, $this->full_name);
		if ($this->isColumnModified(categoryPeer::FULL_IDS)) $criteria->add(categoryPeer::FULL_IDS, $this->full_ids);
		if ($this->isColumnModified(categoryPeer::ENTRIES_COUNT)) $criteria->add(categoryPeer::ENTRIES_COUNT, $this->entries_count);
		if ($this->isColumnModified(categoryPeer::CREATED_AT)) $criteria->add(categoryPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(categoryPeer::UPDATED_AT)) $criteria->add(categoryPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(categoryPeer::DELETED_AT)) $criteria->add(categoryPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(categoryPeer::STATUS)) $criteria->add(categoryPeer::STATUS, $this->status);
		if ($this->isColumnModified(categoryPeer::DIRECT_ENTRIES_COUNT)) $criteria->add(categoryPeer::DIRECT_ENTRIES_COUNT, $this->direct_entries_count);
		if ($this->isColumnModified(categoryPeer::DIRECT_SUB_CATEGORIES_COUNT)) $criteria->add(categoryPeer::DIRECT_SUB_CATEGORIES_COUNT, $this->direct_sub_categories_count);
		if ($this->isColumnModified(categoryPeer::MEMBERS_COUNT)) $criteria->add(categoryPeer::MEMBERS_COUNT, $this->members_count);
		if ($this->isColumnModified(categoryPeer::PENDING_MEMBERS_COUNT)) $criteria->add(categoryPeer::PENDING_MEMBERS_COUNT, $this->pending_members_count);
		if ($this->isColumnModified(categoryPeer::PENDING_ENTRIES_COUNT)) $criteria->add(categoryPeer::PENDING_ENTRIES_COUNT, $this->pending_entries_count);
		if ($this->isColumnModified(categoryPeer::DESCRIPTION)) $criteria->add(categoryPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(categoryPeer::TAGS)) $criteria->add(categoryPeer::TAGS, $this->tags);
		if ($this->isColumnModified(categoryPeer::DISPLAY_IN_SEARCH)) $criteria->add(categoryPeer::DISPLAY_IN_SEARCH, $this->display_in_search);
		if ($this->isColumnModified(categoryPeer::PRIVACY)) $criteria->add(categoryPeer::PRIVACY, $this->privacy);
		if ($this->isColumnModified(categoryPeer::INHERITANCE_TYPE)) $criteria->add(categoryPeer::INHERITANCE_TYPE, $this->inheritance_type);
		if ($this->isColumnModified(categoryPeer::USER_JOIN_POLICY)) $criteria->add(categoryPeer::USER_JOIN_POLICY, $this->user_join_policy);
		if ($this->isColumnModified(categoryPeer::DEFAULT_PERMISSION_LEVEL)) $criteria->add(categoryPeer::DEFAULT_PERMISSION_LEVEL, $this->default_permission_level);
		if ($this->isColumnModified(categoryPeer::KUSER_ID)) $criteria->add(categoryPeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(categoryPeer::PUSER_ID)) $criteria->add(categoryPeer::PUSER_ID, $this->puser_id);
		if ($this->isColumnModified(categoryPeer::REFERENCE_ID)) $criteria->add(categoryPeer::REFERENCE_ID, $this->reference_id);
		if ($this->isColumnModified(categoryPeer::CONTRIBUTION_POLICY)) $criteria->add(categoryPeer::CONTRIBUTION_POLICY, $this->contribution_policy);
		if ($this->isColumnModified(categoryPeer::CUSTOM_DATA)) $criteria->add(categoryPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(categoryPeer::PRIVACY_CONTEXT)) $criteria->add(categoryPeer::PRIVACY_CONTEXT, $this->privacy_context);
		if ($this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS)) $criteria->add(categoryPeer::PRIVACY_CONTEXTS, $this->privacy_contexts);
		if ($this->isColumnModified(categoryPeer::INHERITED_PARENT_ID)) $criteria->add(categoryPeer::INHERITED_PARENT_ID, $this->inherited_parent_id);
		if ($this->isColumnModified(categoryPeer::MODERATION)) $criteria->add(categoryPeer::MODERATION, $this->moderation);

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
		$criteria = new Criteria(categoryPeer::DATABASE_NAME);

		$criteria->add(categoryPeer::ID, $this->id);
		
		if($this->alreadyInSave && count($this->modifiedColumns) == 2 && $this->isColumnModified(categoryPeer::UPDATED_AT))
		{
			$theModifiedColumn = null;
			foreach($this->modifiedColumns as $modifiedColumn)
				if($modifiedColumn != categoryPeer::UPDATED_AT)
					$theModifiedColumn = $modifiedColumn;
					
			$atomicColumns = categoryPeer::getAtomicColumns();
			if(in_array($theModifiedColumn, $atomicColumns))
				$criteria->add($theModifiedColumn, $this->getByName($theModifiedColumn, BasePeer::TYPE_COLNAME), Criteria::NOT_EQUAL);
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
	 * @param      object $copyObj An object of category (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setParentId($this->parent_id);

		$copyObj->setDepth($this->depth);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setName($this->name);

		$copyObj->setFullName($this->full_name);

		$copyObj->setFullIds($this->full_ids);

		$copyObj->setEntriesCount($this->entries_count);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setDeletedAt($this->deleted_at);

		$copyObj->setStatus($this->status);

		$copyObj->setDirectEntriesCount($this->direct_entries_count);

		$copyObj->setDirectSubCategoriesCount($this->direct_sub_categories_count);

		$copyObj->setMembersCount($this->members_count);

		$copyObj->setPendingMembersCount($this->pending_members_count);

		$copyObj->setPendingEntriesCount($this->pending_entries_count);

		$copyObj->setDescription($this->description);

		$copyObj->setTags($this->tags);

		$copyObj->setDisplayInSearch($this->display_in_search);

		$copyObj->setPrivacy($this->privacy);

		$copyObj->setInheritanceType($this->inheritance_type);

		$copyObj->setUserJoinPolicy($this->user_join_policy);

		$copyObj->setDefaultPermissionLevel($this->default_permission_level);

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setPuserId($this->puser_id);

		$copyObj->setReferenceId($this->reference_id);

		$copyObj->setContributionPolicy($this->contribution_policy);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setPrivacyContext($this->privacy_context);

		$copyObj->setPrivacyContexts($this->privacy_contexts);

		$copyObj->setInheritedParentId($this->inherited_parent_id);

		$copyObj->setModeration($this->moderation);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getcategoryKusers() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addcategoryKuser($relObj->copy($deepCopy));
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
	 * @return     category Clone of current object.
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
	 * @var     category Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      category $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(category $copiedFrom)
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
	 * @return     categoryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new categoryPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collcategoryKusers collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addcategoryKusers()
	 */
	public function clearcategoryKusers()
	{
		$this->collcategoryKusers = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collcategoryKusers collection (array).
	 *
	 * By default this just sets the collcategoryKusers collection to an empty array (like clearcollcategoryKusers());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initcategoryKusers()
	{
		$this->collcategoryKusers = array();
	}

	/**
	 * Gets an array of categoryKuser objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this category has previously been saved, it will retrieve
	 * related categoryKusers from storage. If this category is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array categoryKuser[]
	 * @throws     PropelException
	 */
	public function getcategoryKusers($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(categoryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collcategoryKusers === null) {
			if ($this->isNew()) {
			   $this->collcategoryKusers = array();
			} else {

				$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

				categoryKuserPeer::addSelectColumns($criteria);
				$this->collcategoryKusers = categoryKuserPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

				categoryKuserPeer::addSelectColumns($criteria);
				if (!isset($this->lastcategoryKuserCriteria) || !$this->lastcategoryKuserCriteria->equals($criteria)) {
					$this->collcategoryKusers = categoryKuserPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastcategoryKuserCriteria = $criteria;
		return $this->collcategoryKusers;
	}

	/**
	 * Returns the number of related categoryKuser objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related categoryKuser objects.
	 * @throws     PropelException
	 */
	public function countcategoryKusers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(categoryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collcategoryKusers === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

				$count = categoryKuserPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

				if (!isset($this->lastcategoryKuserCriteria) || !$this->lastcategoryKuserCriteria->equals($criteria)) {
					$count = categoryKuserPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collcategoryKusers);
				}
			} else {
				$count = count($this->collcategoryKusers);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a categoryKuser object to this object
	 * through the categoryKuser foreign key attribute.
	 *
	 * @param      categoryKuser $l categoryKuser
	 * @return     void
	 * @throws     PropelException
	 */
	public function addcategoryKuser(categoryKuser $l)
	{
		if ($this->collcategoryKusers === null) {
			$this->initcategoryKusers();
		}
		if (!in_array($l, $this->collcategoryKusers, true)) { // only add it if the **same** object is not already associated
			array_push($this->collcategoryKusers, $l);
			$l->setcategory($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this category is new, it will return
	 * an empty collection; or if this category has previously
	 * been saved, it will retrieve related categoryKusers from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in category.
	 */
	public function getcategoryKusersJoinkuser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(categoryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collcategoryKusers === null) {
			if ($this->isNew()) {
				$this->collcategoryKusers = array();
			} else {

				$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

				$this->collcategoryKusers = categoryKuserPeer::doSelectJoinkuser($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(categoryKuserPeer::CATEGORY_ID, $this->id);

			if (!isset($this->lastcategoryKuserCriteria) || !$this->lastcategoryKuserCriteria->equals($criteria)) {
				$this->collcategoryKusers = categoryKuserPeer::doSelectJoinkuser($criteria, $con, $join_behavior);
			}
		}
		$this->lastcategoryKuserCriteria = $criteria;

		return $this->collcategoryKusers;
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
			if ($this->collcategoryKusers) {
				foreach ((array) $this->collcategoryKusers as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collcategoryKusers = null;
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
	
} // Basecategory
