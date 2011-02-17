<?php

/**
 * Base class that represents a row from the 'search_entry' table.
 *
 * 
 *
 * @package plugins.contentDistribution
 * @subpackage model.om
 */
abstract class BaseSearchEntry extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SearchEntryPeer
	 */
	protected static $peer;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the media_type field.
	 * @var        int
	 */
	protected $media_type;

	/**
	 * The value for the views field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the rank field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $rank;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the entry_status field.
	 * @var        int
	 */
	protected $entry_status;

	/**
	 * The value for the source_link field.
	 * @var        string
	 */
	protected $source_link;

	/**
	 * The value for the duration field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $duration;

	/**
	 * The value for the duration_type field.
	 * @var        string
	 */
	protected $duration_type;

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
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the display_in_search field.
	 * @var        int
	 */
	protected $display_in_search;

	/**
	 * The value for the group_id field.
	 * @var        string
	 */
	protected $group_id;

	/**
	 * The value for the plays field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $plays;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the media_date field.
	 * @var        string
	 */
	protected $media_date;

	/**
	 * The value for the admin_tags field.
	 * @var        string
	 */
	protected $admin_tags;

	/**
	 * The value for the moderation_status field.
	 * @var        int
	 */
	protected $moderation_status;

	/**
	 * The value for the moderation_count field.
	 * @var        int
	 */
	protected $moderation_count;

	/**
	 * The value for the modified_at field.
	 * @var        string
	 */
	protected $modified_at;

	/**
	 * The value for the access_control_id field.
	 * @var        int
	 */
	protected $access_control_id;

	/**
	 * The value for the categories field.
	 * @var        string
	 */
	protected $categories;

	/**
	 * The value for the start_date field.
	 * @var        string
	 */
	protected $start_date;

	/**
	 * The value for the end_date field.
	 * @var        string
	 */
	protected $end_date;

	/**
	 * The value for the flavor_params field.
	 * @var        string
	 */
	protected $flavor_params;

	/**
	 * The value for the available_from field.
	 * @var        string
	 */
	protected $available_from;

	/**
	 * The value for the plugin_data field.
	 * @var        string
	 */
	protected $plugin_data;

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
		$this->views = 0;
		$this->rank = 0;
		$this->duration = 0;
		$this->partner_id = 0;
		$this->plays = 0;
	}

	/**
	 * Initializes internal state of BaseSearchEntry object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
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
	 * Get the [kuser_id] column value.
	 * 
	 * @return     int
	 */
	public function getKuserId()
	{
		return $this->kuser_id;
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
	 * Get the [type] column value.
	 * 
	 * @return     int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [media_type] column value.
	 * 
	 * @return     int
	 */
	public function getMediaType()
	{
		return $this->media_type;
	}

	/**
	 * Get the [views] column value.
	 * 
	 * @return     int
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * Get the [rank] column value.
	 * 
	 * @return     int
	 */
	public function getRank()
	{
		return $this->rank;
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
	 * Get the [entry_status] column value.
	 * 
	 * @return     int
	 */
	public function getEntryStatus()
	{
		return $this->entry_status;
	}

	/**
	 * Get the [source_link] column value.
	 * 
	 * @return     string
	 */
	public function getSourceLink()
	{
		return $this->source_link;
	}

	/**
	 * Get the [duration] column value.
	 * 
	 * @return     int
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * Get the [duration_type] column value.
	 * 
	 * @return     string
	 */
	public function getDurationType()
	{
		return $this->duration_type;
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
	 * Get the [display_in_search] column value.
	 * 
	 * @return     int
	 */
	public function getDisplayInSearch()
	{
		return $this->display_in_search;
	}

	/**
	 * Get the [group_id] column value.
	 * 
	 * @return     string
	 */
	public function getGroupId()
	{
		return $this->group_id;
	}

	/**
	 * Get the [plays] column value.
	 * 
	 * @return     int
	 */
	public function getPlays()
	{
		return $this->plays;
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
	 * Get the [optionally formatted] temporal [media_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getMediaDate($format = 'Y-m-d H:i:s')
	{
		if ($this->media_date === null) {
			return null;
		}


		if ($this->media_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->media_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->media_date, true), $x);
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
	 * Get the [admin_tags] column value.
	 * 
	 * @return     string
	 */
	public function getAdminTags()
	{
		return $this->admin_tags;
	}

	/**
	 * Get the [moderation_status] column value.
	 * 
	 * @return     int
	 */
	public function getModerationStatus()
	{
		return $this->moderation_status;
	}

	/**
	 * Get the [moderation_count] column value.
	 * 
	 * @return     int
	 */
	public function getModerationCount()
	{
		return $this->moderation_count;
	}

	/**
	 * Get the [optionally formatted] temporal [modified_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getModifiedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->modified_at === null) {
			return null;
		}


		if ($this->modified_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->modified_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->modified_at, true), $x);
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
	 * Get the [access_control_id] column value.
	 * 
	 * @return     int
	 */
	public function getAccessControlId()
	{
		return $this->access_control_id;
	}

	/**
	 * Get the [categories] column value.
	 * 
	 * @return     string
	 */
	public function getCategories()
	{
		return $this->categories;
	}

	/**
	 * Get the [optionally formatted] temporal [start_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getStartDate($format = 'Y-m-d H:i:s')
	{
		if ($this->start_date === null) {
			return null;
		}


		if ($this->start_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->start_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_date, true), $x);
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
	 * Get the [optionally formatted] temporal [end_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getEndDate($format = 'Y-m-d H:i:s')
	{
		if ($this->end_date === null) {
			return null;
		}


		if ($this->end_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->end_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->end_date, true), $x);
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
	 * Get the [flavor_params] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorParams()
	{
		return $this->flavor_params;
	}

	/**
	 * Get the [optionally formatted] temporal [available_from] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getAvailableFrom($format = 'Y-m-d H:i:s')
	{
		if ($this->available_from === null) {
			return null;
		}


		if ($this->available_from === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->available_from);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->available_from, true), $x);
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
	 * Get the [plugin_data] column value.
	 * 
	 * @return     string
	 */
	public function getPluginData()
	{
		return $this->plugin_data;
	}

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::ENTRY_ID]))
			$this->oldColumnsValues[SearchEntryPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = SearchEntryPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::KUSER_ID]))
			$this->oldColumnsValues[SearchEntryPeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = SearchEntryPeer::KUSER_ID;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::NAME]))
			$this->oldColumnsValues[SearchEntryPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = SearchEntryPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::TYPE]))
			$this->oldColumnsValues[SearchEntryPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = SearchEntryPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [media_type] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setMediaType($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::MEDIA_TYPE]))
			$this->oldColumnsValues[SearchEntryPeer::MEDIA_TYPE] = $this->media_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->media_type !== $v) {
			$this->media_type = $v;
			$this->modifiedColumns[] = SearchEntryPeer::MEDIA_TYPE;
		}

		return $this;
	} // setMediaType()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::VIEWS]))
			$this->oldColumnsValues[SearchEntryPeer::VIEWS] = $this->views;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v || $this->isNew()) {
			$this->views = $v;
			$this->modifiedColumns[] = SearchEntryPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [rank] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setRank($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::RANK]))
			$this->oldColumnsValues[SearchEntryPeer::RANK] = $this->rank;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rank !== $v || $this->isNew()) {
			$this->rank = $v;
			$this->modifiedColumns[] = SearchEntryPeer::RANK;
		}

		return $this;
	} // setRank()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::TAGS]))
			$this->oldColumnsValues[SearchEntryPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = SearchEntryPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [entry_status] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setEntryStatus($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::ENTRY_STATUS]))
			$this->oldColumnsValues[SearchEntryPeer::ENTRY_STATUS] = $this->entry_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->entry_status !== $v) {
			$this->entry_status = $v;
			$this->modifiedColumns[] = SearchEntryPeer::ENTRY_STATUS;
		}

		return $this;
	} // setEntryStatus()

	/**
	 * Set the value of [source_link] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setSourceLink($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::SOURCE_LINK]))
			$this->oldColumnsValues[SearchEntryPeer::SOURCE_LINK] = $this->source_link;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->source_link !== $v) {
			$this->source_link = $v;
			$this->modifiedColumns[] = SearchEntryPeer::SOURCE_LINK;
		}

		return $this;
	} // setSourceLink()

	/**
	 * Set the value of [duration] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setDuration($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::DURATION]))
			$this->oldColumnsValues[SearchEntryPeer::DURATION] = $this->duration;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->duration !== $v || $this->isNew()) {
			$this->duration = $v;
			$this->modifiedColumns[] = SearchEntryPeer::DURATION;
		}

		return $this;
	} // setDuration()

	/**
	 * Set the value of [duration_type] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setDurationType($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::DURATION_TYPE]))
			$this->oldColumnsValues[SearchEntryPeer::DURATION_TYPE] = $this->duration_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->duration_type !== $v) {
			$this->duration_type = $v;
			$this->modifiedColumns[] = SearchEntryPeer::DURATION_TYPE;
		}

		return $this;
	} // setDurationType()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
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
				$this->modifiedColumns[] = SearchEntryPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
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
				$this->modifiedColumns[] = SearchEntryPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::PARTNER_ID]))
			$this->oldColumnsValues[SearchEntryPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = SearchEntryPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [display_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setDisplayInSearch($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::DISPLAY_IN_SEARCH]))
			$this->oldColumnsValues[SearchEntryPeer::DISPLAY_IN_SEARCH] = $this->display_in_search;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_in_search !== $v) {
			$this->display_in_search = $v;
			$this->modifiedColumns[] = SearchEntryPeer::DISPLAY_IN_SEARCH;
		}

		return $this;
	} // setDisplayInSearch()

	/**
	 * Set the value of [group_id] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setGroupId($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::GROUP_ID]))
			$this->oldColumnsValues[SearchEntryPeer::GROUP_ID] = $this->group_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->group_id !== $v) {
			$this->group_id = $v;
			$this->modifiedColumns[] = SearchEntryPeer::GROUP_ID;
		}

		return $this;
	} // setGroupId()

	/**
	 * Set the value of [plays] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setPlays($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::PLAYS]))
			$this->oldColumnsValues[SearchEntryPeer::PLAYS] = $this->plays;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plays !== $v || $this->isNew()) {
			$this->plays = $v;
			$this->modifiedColumns[] = SearchEntryPeer::PLAYS;
		}

		return $this;
	} // setPlays()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::DESCRIPTION]))
			$this->oldColumnsValues[SearchEntryPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = SearchEntryPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Sets the value of [media_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setMediaDate($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::MEDIA_DATE]))
			$this->oldColumnsValues[SearchEntryPeer::MEDIA_DATE] = $this->media_date;

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

		if ( $this->media_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->media_date !== null && $tmpDt = new DateTime($this->media_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->media_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = SearchEntryPeer::MEDIA_DATE;
			}
		} // if either are not null

		return $this;
	} // setMediaDate()

	/**
	 * Set the value of [admin_tags] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setAdminTags($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::ADMIN_TAGS]))
			$this->oldColumnsValues[SearchEntryPeer::ADMIN_TAGS] = $this->admin_tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_tags !== $v) {
			$this->admin_tags = $v;
			$this->modifiedColumns[] = SearchEntryPeer::ADMIN_TAGS;
		}

		return $this;
	} // setAdminTags()

	/**
	 * Set the value of [moderation_status] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setModerationStatus($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::MODERATION_STATUS]))
			$this->oldColumnsValues[SearchEntryPeer::MODERATION_STATUS] = $this->moderation_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->moderation_status !== $v) {
			$this->moderation_status = $v;
			$this->modifiedColumns[] = SearchEntryPeer::MODERATION_STATUS;
		}

		return $this;
	} // setModerationStatus()

	/**
	 * Set the value of [moderation_count] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setModerationCount($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::MODERATION_COUNT]))
			$this->oldColumnsValues[SearchEntryPeer::MODERATION_COUNT] = $this->moderation_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->moderation_count !== $v) {
			$this->moderation_count = $v;
			$this->modifiedColumns[] = SearchEntryPeer::MODERATION_COUNT;
		}

		return $this;
	} // setModerationCount()

	/**
	 * Sets the value of [modified_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setModifiedAt($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::MODIFIED_AT]))
			$this->oldColumnsValues[SearchEntryPeer::MODIFIED_AT] = $this->modified_at;

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

		if ( $this->modified_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->modified_at !== null && $tmpDt = new DateTime($this->modified_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->modified_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = SearchEntryPeer::MODIFIED_AT;
			}
		} // if either are not null

		return $this;
	} // setModifiedAt()

	/**
	 * Set the value of [access_control_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setAccessControlId($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::ACCESS_CONTROL_ID]))
			$this->oldColumnsValues[SearchEntryPeer::ACCESS_CONTROL_ID] = $this->access_control_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->access_control_id !== $v) {
			$this->access_control_id = $v;
			$this->modifiedColumns[] = SearchEntryPeer::ACCESS_CONTROL_ID;
		}

		return $this;
	} // setAccessControlId()

	/**
	 * Set the value of [categories] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setCategories($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::CATEGORIES]))
			$this->oldColumnsValues[SearchEntryPeer::CATEGORIES] = $this->categories;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->categories !== $v) {
			$this->categories = $v;
			$this->modifiedColumns[] = SearchEntryPeer::CATEGORIES;
		}

		return $this;
	} // setCategories()

	/**
	 * Sets the value of [start_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setStartDate($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::START_DATE]))
			$this->oldColumnsValues[SearchEntryPeer::START_DATE] = $this->start_date;

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

		if ( $this->start_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->start_date !== null && $tmpDt = new DateTime($this->start_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->start_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = SearchEntryPeer::START_DATE;
			}
		} // if either are not null

		return $this;
	} // setStartDate()

	/**
	 * Sets the value of [end_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setEndDate($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::END_DATE]))
			$this->oldColumnsValues[SearchEntryPeer::END_DATE] = $this->end_date;

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

		if ( $this->end_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->end_date !== null && $tmpDt = new DateTime($this->end_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->end_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = SearchEntryPeer::END_DATE;
			}
		} // if either are not null

		return $this;
	} // setEndDate()

	/**
	 * Set the value of [flavor_params] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setFlavorParams($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::FLAVOR_PARAMS]))
			$this->oldColumnsValues[SearchEntryPeer::FLAVOR_PARAMS] = $this->flavor_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_params !== $v) {
			$this->flavor_params = $v;
			$this->modifiedColumns[] = SearchEntryPeer::FLAVOR_PARAMS;
		}

		return $this;
	} // setFlavorParams()

	/**
	 * Sets the value of [available_from] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setAvailableFrom($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::AVAILABLE_FROM]))
			$this->oldColumnsValues[SearchEntryPeer::AVAILABLE_FROM] = $this->available_from;

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

		if ( $this->available_from !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->available_from !== null && $tmpDt = new DateTime($this->available_from)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->available_from = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = SearchEntryPeer::AVAILABLE_FROM;
			}
		} // if either are not null

		return $this;
	} // setAvailableFrom()

	/**
	 * Set the value of [plugin_data] column.
	 * 
	 * @param      string $v new value
	 * @return     SearchEntry The current object (for fluent API support)
	 */
	public function setPluginData($v)
	{
		if(!isset($this->oldColumnsValues[SearchEntryPeer::PLUGIN_DATA]))
			$this->oldColumnsValues[SearchEntryPeer::PLUGIN_DATA] = $this->plugin_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->plugin_data !== $v) {
			$this->plugin_data = $v;
			$this->modifiedColumns[] = SearchEntryPeer::PLUGIN_DATA;
		}

		return $this;
	} // setPluginData()

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
			if ($this->views !== 0) {
				return false;
			}

			if ($this->rank !== 0) {
				return false;
			}

			if ($this->duration !== 0) {
				return false;
			}

			if ($this->partner_id !== 0) {
				return false;
			}

			if ($this->plays !== 0) {
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

			$this->entry_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->kuser_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->type = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->media_type = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->views = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->rank = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->tags = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->entry_status = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->source_link = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->duration = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->duration_type = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->created_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->updated_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->partner_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->display_in_search = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->group_id = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->plays = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->description = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->media_date = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->admin_tags = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->moderation_status = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->moderation_count = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->modified_at = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->access_control_id = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->categories = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->start_date = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->end_date = ($row[$startcol + 27] !== null) ? (string) $row[$startcol + 27] : null;
			$this->flavor_params = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->available_from = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->plugin_data = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 31; // 31 = SearchEntryPeer::NUM_COLUMNS - SearchEntryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SearchEntry object", $e);
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
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = SearchEntryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				SearchEntryPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				SearchEntryPeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SearchEntryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += SearchEntryPeer::doUpdate($this, $con);
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
		SearchEntryPeer::setUseCriteriaFilter(false);
		$this->reload();
		SearchEntryPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = SearchEntryPeer::doValidate($this, $columns)) !== true) {
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
		$pos = SearchEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEntryId();
				break;
			case 1:
				return $this->getKuserId();
				break;
			case 2:
				return $this->getName();
				break;
			case 3:
				return $this->getType();
				break;
			case 4:
				return $this->getMediaType();
				break;
			case 5:
				return $this->getViews();
				break;
			case 6:
				return $this->getRank();
				break;
			case 7:
				return $this->getTags();
				break;
			case 8:
				return $this->getEntryStatus();
				break;
			case 9:
				return $this->getSourceLink();
				break;
			case 10:
				return $this->getDuration();
				break;
			case 11:
				return $this->getDurationType();
				break;
			case 12:
				return $this->getCreatedAt();
				break;
			case 13:
				return $this->getUpdatedAt();
				break;
			case 14:
				return $this->getPartnerId();
				break;
			case 15:
				return $this->getDisplayInSearch();
				break;
			case 16:
				return $this->getGroupId();
				break;
			case 17:
				return $this->getPlays();
				break;
			case 18:
				return $this->getDescription();
				break;
			case 19:
				return $this->getMediaDate();
				break;
			case 20:
				return $this->getAdminTags();
				break;
			case 21:
				return $this->getModerationStatus();
				break;
			case 22:
				return $this->getModerationCount();
				break;
			case 23:
				return $this->getModifiedAt();
				break;
			case 24:
				return $this->getAccessControlId();
				break;
			case 25:
				return $this->getCategories();
				break;
			case 26:
				return $this->getStartDate();
				break;
			case 27:
				return $this->getEndDate();
				break;
			case 28:
				return $this->getFlavorParams();
				break;
			case 29:
				return $this->getAvailableFrom();
				break;
			case 30:
				return $this->getPluginData();
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
		$keys = SearchEntryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getEntryId(),
			$keys[1] => $this->getKuserId(),
			$keys[2] => $this->getName(),
			$keys[3] => $this->getType(),
			$keys[4] => $this->getMediaType(),
			$keys[5] => $this->getViews(),
			$keys[6] => $this->getRank(),
			$keys[7] => $this->getTags(),
			$keys[8] => $this->getEntryStatus(),
			$keys[9] => $this->getSourceLink(),
			$keys[10] => $this->getDuration(),
			$keys[11] => $this->getDurationType(),
			$keys[12] => $this->getCreatedAt(),
			$keys[13] => $this->getUpdatedAt(),
			$keys[14] => $this->getPartnerId(),
			$keys[15] => $this->getDisplayInSearch(),
			$keys[16] => $this->getGroupId(),
			$keys[17] => $this->getPlays(),
			$keys[18] => $this->getDescription(),
			$keys[19] => $this->getMediaDate(),
			$keys[20] => $this->getAdminTags(),
			$keys[21] => $this->getModerationStatus(),
			$keys[22] => $this->getModerationCount(),
			$keys[23] => $this->getModifiedAt(),
			$keys[24] => $this->getAccessControlId(),
			$keys[25] => $this->getCategories(),
			$keys[26] => $this->getStartDate(),
			$keys[27] => $this->getEndDate(),
			$keys[28] => $this->getFlavorParams(),
			$keys[29] => $this->getAvailableFrom(),
			$keys[30] => $this->getPluginData(),
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
		$pos = SearchEntryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEntryId($value);
				break;
			case 1:
				$this->setKuserId($value);
				break;
			case 2:
				$this->setName($value);
				break;
			case 3:
				$this->setType($value);
				break;
			case 4:
				$this->setMediaType($value);
				break;
			case 5:
				$this->setViews($value);
				break;
			case 6:
				$this->setRank($value);
				break;
			case 7:
				$this->setTags($value);
				break;
			case 8:
				$this->setEntryStatus($value);
				break;
			case 9:
				$this->setSourceLink($value);
				break;
			case 10:
				$this->setDuration($value);
				break;
			case 11:
				$this->setDurationType($value);
				break;
			case 12:
				$this->setCreatedAt($value);
				break;
			case 13:
				$this->setUpdatedAt($value);
				break;
			case 14:
				$this->setPartnerId($value);
				break;
			case 15:
				$this->setDisplayInSearch($value);
				break;
			case 16:
				$this->setGroupId($value);
				break;
			case 17:
				$this->setPlays($value);
				break;
			case 18:
				$this->setDescription($value);
				break;
			case 19:
				$this->setMediaDate($value);
				break;
			case 20:
				$this->setAdminTags($value);
				break;
			case 21:
				$this->setModerationStatus($value);
				break;
			case 22:
				$this->setModerationCount($value);
				break;
			case 23:
				$this->setModifiedAt($value);
				break;
			case 24:
				$this->setAccessControlId($value);
				break;
			case 25:
				$this->setCategories($value);
				break;
			case 26:
				$this->setStartDate($value);
				break;
			case 27:
				$this->setEndDate($value);
				break;
			case 28:
				$this->setFlavorParams($value);
				break;
			case 29:
				$this->setAvailableFrom($value);
				break;
			case 30:
				$this->setPluginData($value);
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
		$keys = SearchEntryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setEntryId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setKuserId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setMediaType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setViews($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setRank($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTags($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setEntryStatus($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSourceLink($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDuration($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDurationType($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setCreatedAt($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setUpdatedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPartnerId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setDisplayInSearch($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setGroupId($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setPlays($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setDescription($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setMediaDate($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setAdminTags($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setModerationStatus($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setModerationCount($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setModifiedAt($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setAccessControlId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setCategories($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setStartDate($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setEndDate($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setFlavorParams($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setAvailableFrom($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setPluginData($arr[$keys[30]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SearchEntryPeer::DATABASE_NAME);

		if ($this->isColumnModified(SearchEntryPeer::ENTRY_ID)) $criteria->add(SearchEntryPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(SearchEntryPeer::KUSER_ID)) $criteria->add(SearchEntryPeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(SearchEntryPeer::NAME)) $criteria->add(SearchEntryPeer::NAME, $this->name);
		if ($this->isColumnModified(SearchEntryPeer::TYPE)) $criteria->add(SearchEntryPeer::TYPE, $this->type);
		if ($this->isColumnModified(SearchEntryPeer::MEDIA_TYPE)) $criteria->add(SearchEntryPeer::MEDIA_TYPE, $this->media_type);
		if ($this->isColumnModified(SearchEntryPeer::VIEWS)) $criteria->add(SearchEntryPeer::VIEWS, $this->views);
		if ($this->isColumnModified(SearchEntryPeer::RANK)) $criteria->add(SearchEntryPeer::RANK, $this->rank);
		if ($this->isColumnModified(SearchEntryPeer::TAGS)) $criteria->add(SearchEntryPeer::TAGS, $this->tags);
		if ($this->isColumnModified(SearchEntryPeer::ENTRY_STATUS)) $criteria->add(SearchEntryPeer::ENTRY_STATUS, $this->entry_status);
		if ($this->isColumnModified(SearchEntryPeer::SOURCE_LINK)) $criteria->add(SearchEntryPeer::SOURCE_LINK, $this->source_link);
		if ($this->isColumnModified(SearchEntryPeer::DURATION)) $criteria->add(SearchEntryPeer::DURATION, $this->duration);
		if ($this->isColumnModified(SearchEntryPeer::DURATION_TYPE)) $criteria->add(SearchEntryPeer::DURATION_TYPE, $this->duration_type);
		if ($this->isColumnModified(SearchEntryPeer::CREATED_AT)) $criteria->add(SearchEntryPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(SearchEntryPeer::UPDATED_AT)) $criteria->add(SearchEntryPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(SearchEntryPeer::PARTNER_ID)) $criteria->add(SearchEntryPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(SearchEntryPeer::DISPLAY_IN_SEARCH)) $criteria->add(SearchEntryPeer::DISPLAY_IN_SEARCH, $this->display_in_search);
		if ($this->isColumnModified(SearchEntryPeer::GROUP_ID)) $criteria->add(SearchEntryPeer::GROUP_ID, $this->group_id);
		if ($this->isColumnModified(SearchEntryPeer::PLAYS)) $criteria->add(SearchEntryPeer::PLAYS, $this->plays);
		if ($this->isColumnModified(SearchEntryPeer::DESCRIPTION)) $criteria->add(SearchEntryPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(SearchEntryPeer::MEDIA_DATE)) $criteria->add(SearchEntryPeer::MEDIA_DATE, $this->media_date);
		if ($this->isColumnModified(SearchEntryPeer::ADMIN_TAGS)) $criteria->add(SearchEntryPeer::ADMIN_TAGS, $this->admin_tags);
		if ($this->isColumnModified(SearchEntryPeer::MODERATION_STATUS)) $criteria->add(SearchEntryPeer::MODERATION_STATUS, $this->moderation_status);
		if ($this->isColumnModified(SearchEntryPeer::MODERATION_COUNT)) $criteria->add(SearchEntryPeer::MODERATION_COUNT, $this->moderation_count);
		if ($this->isColumnModified(SearchEntryPeer::MODIFIED_AT)) $criteria->add(SearchEntryPeer::MODIFIED_AT, $this->modified_at);
		if ($this->isColumnModified(SearchEntryPeer::ACCESS_CONTROL_ID)) $criteria->add(SearchEntryPeer::ACCESS_CONTROL_ID, $this->access_control_id);
		if ($this->isColumnModified(SearchEntryPeer::CATEGORIES)) $criteria->add(SearchEntryPeer::CATEGORIES, $this->categories);
		if ($this->isColumnModified(SearchEntryPeer::START_DATE)) $criteria->add(SearchEntryPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(SearchEntryPeer::END_DATE)) $criteria->add(SearchEntryPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(SearchEntryPeer::FLAVOR_PARAMS)) $criteria->add(SearchEntryPeer::FLAVOR_PARAMS, $this->flavor_params);
		if ($this->isColumnModified(SearchEntryPeer::AVAILABLE_FROM)) $criteria->add(SearchEntryPeer::AVAILABLE_FROM, $this->available_from);
		if ($this->isColumnModified(SearchEntryPeer::PLUGIN_DATA)) $criteria->add(SearchEntryPeer::PLUGIN_DATA, $this->plugin_data);

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
		$criteria = new Criteria(SearchEntryPeer::DATABASE_NAME);

		$criteria->add(SearchEntryPeer::ENTRY_ID, $this->entry_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getEntryId();
	}

	/**
	 * Generic method to set the primary key (entry_id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setEntryId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of SearchEntry (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setName($this->name);

		$copyObj->setType($this->type);

		$copyObj->setMediaType($this->media_type);

		$copyObj->setViews($this->views);

		$copyObj->setRank($this->rank);

		$copyObj->setTags($this->tags);

		$copyObj->setEntryStatus($this->entry_status);

		$copyObj->setSourceLink($this->source_link);

		$copyObj->setDuration($this->duration);

		$copyObj->setDurationType($this->duration_type);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDisplayInSearch($this->display_in_search);

		$copyObj->setGroupId($this->group_id);

		$copyObj->setPlays($this->plays);

		$copyObj->setDescription($this->description);

		$copyObj->setMediaDate($this->media_date);

		$copyObj->setAdminTags($this->admin_tags);

		$copyObj->setModerationStatus($this->moderation_status);

		$copyObj->setModerationCount($this->moderation_count);

		$copyObj->setModifiedAt($this->modified_at);

		$copyObj->setAccessControlId($this->access_control_id);

		$copyObj->setCategories($this->categories);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setFlavorParams($this->flavor_params);

		$copyObj->setAvailableFrom($this->available_from);

		$copyObj->setPluginData($this->plugin_data);


		$copyObj->setNew(true);

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
	 * @return     SearchEntry Clone of current object.
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
	 * @var     SearchEntry Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      SearchEntry $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(SearchEntry $copiedFrom)
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
	 * @return     SearchEntryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SearchEntryPeer();
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

} // BaseSearchEntry
