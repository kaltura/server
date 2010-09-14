<?php

/**
 * Base class that represents a row from the 'syndication_feed' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BasesyndicationFeed extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        syndicationFeedPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        string
	 */
	protected $id;

	/**
	 * The value for the int_id field.
	 * @var        int
	 */
	protected $int_id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the playlist_id field.
	 * @var        string
	 */
	protected $playlist_id;

	/**
	 * The value for the name field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the landing_page field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $landing_page;

	/**
	 * The value for the flavor_param_id field.
	 * @var        int
	 */
	protected $flavor_param_id;

	/**
	 * The value for the player_uiconf_id field.
	 * @var        int
	 */
	protected $player_uiconf_id;

	/**
	 * The value for the allow_embed field.
	 * Note: this column has a database default value of: true
	 * @var        boolean
	 */
	protected $allow_embed;

	/**
	 * The value for the adult_content field.
	 * @var        string
	 */
	protected $adult_content;

	/**
	 * The value for the transcode_existing_content field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $transcode_existing_content;

	/**
	 * The value for the add_to_default_conversion_profile field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $add_to_default_conversion_profile;

	/**
	 * The value for the categories field.
	 * @var        string
	 */
	protected $categories;

	/**
	 * The value for the feed_description field.
	 * @var        string
	 */
	protected $feed_description;

	/**
	 * The value for the language field.
	 * @var        string
	 */
	protected $language;

	/**
	 * The value for the feed_landing_page field.
	 * @var        string
	 */
	protected $feed_landing_page;

	/**
	 * The value for the owner_name field.
	 * @var        string
	 */
	protected $owner_name;

	/**
	 * The value for the owner_email field.
	 * @var        string
	 */
	protected $owner_email;

	/**
	 * The value for the feed_image_url field.
	 * @var        string
	 */
	protected $feed_image_url;

	/**
	 * The value for the feed_author field.
	 * @var        string
	 */
	protected $feed_author;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->name = '';
		$this->landing_page = '';
		$this->allow_embed = true;
		$this->transcode_existing_content = false;
		$this->add_to_default_conversion_profile = false;
	}

	/**
	 * Initializes internal state of BasesyndicationFeed object.
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
	 * @return     string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [int_id] column value.
	 * 
	 * @return     int
	 */
	public function getIntId()
	{
		return $this->int_id;
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
	 * Get the [playlist_id] column value.
	 * 
	 * @return     string
	 */
	public function getPlaylistId()
	{
		return $this->playlist_id;
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
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
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
	 * Get the [landing_page] column value.
	 * 
	 * @return     string
	 */
	public function getLandingPage()
	{
		return $this->landing_page;
	}

	/**
	 * Get the [flavor_param_id] column value.
	 * 
	 * @return     int
	 */
	public function getFlavorParamId()
	{
		return $this->flavor_param_id;
	}

	/**
	 * Get the [player_uiconf_id] column value.
	 * 
	 * @return     int
	 */
	public function getPlayerUiconfId()
	{
		return $this->player_uiconf_id;
	}

	/**
	 * Get the [allow_embed] column value.
	 * 
	 * @return     boolean
	 */
	public function getAllowEmbed()
	{
		return $this->allow_embed;
	}

	/**
	 * Get the [adult_content] column value.
	 * 
	 * @return     string
	 */
	public function getAdultContent()
	{
		return $this->adult_content;
	}

	/**
	 * Get the [transcode_existing_content] column value.
	 * 
	 * @return     boolean
	 */
	public function getTranscodeExistingContent()
	{
		return $this->transcode_existing_content;
	}

	/**
	 * Get the [add_to_default_conversion_profile] column value.
	 * 
	 * @return     boolean
	 */
	public function getAddToDefaultConversionProfile()
	{
		return $this->add_to_default_conversion_profile;
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
	 * Get the [feed_description] column value.
	 * 
	 * @return     string
	 */
	public function getFeedDescription()
	{
		return $this->feed_description;
	}

	/**
	 * Get the [language] column value.
	 * 
	 * @return     string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Get the [feed_landing_page] column value.
	 * 
	 * @return     string
	 */
	public function getFeedLandingPage()
	{
		return $this->feed_landing_page;
	}

	/**
	 * Get the [owner_name] column value.
	 * 
	 * @return     string
	 */
	public function getOwnerName()
	{
		return $this->owner_name;
	}

	/**
	 * Get the [owner_email] column value.
	 * 
	 * @return     string
	 */
	public function getOwnerEmail()
	{
		return $this->owner_email;
	}

	/**
	 * Get the [feed_image_url] column value.
	 * 
	 * @return     string
	 */
	public function getFeedImageUrl()
	{
		return $this->feed_image_url;
	}

	/**
	 * Get the [feed_author] column value.
	 * 
	 * @return     string
	 */
	public function getFeedAuthor()
	{
		return $this->feed_author;
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
	 * Set the value of [id] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [int_id] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setIntId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->int_id !== $v) {
			$this->int_id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::INT_ID;
		}

		return $this;
	} // setIntId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [playlist_id] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setPlaylistId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->playlist_id !== $v) {
			$this->playlist_id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::PLAYLIST_ID;
		}

		return $this;
	} // setPlaylistId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [landing_page] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setLandingPage($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->landing_page !== $v || $this->isNew()) {
			$this->landing_page = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::LANDING_PAGE;
		}

		return $this;
	} // setLandingPage()

	/**
	 * Set the value of [flavor_param_id] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setFlavorParamId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flavor_param_id !== $v) {
			$this->flavor_param_id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::FLAVOR_PARAM_ID;
		}

		return $this;
	} // setFlavorParamId()

	/**
	 * Set the value of [player_uiconf_id] column.
	 * 
	 * @param      int $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setPlayerUiconfId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->player_uiconf_id !== $v) {
			$this->player_uiconf_id = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::PLAYER_UICONF_ID;
		}

		return $this;
	} // setPlayerUiconfId()

	/**
	 * Set the value of [allow_embed] column.
	 * 
	 * @param      boolean $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setAllowEmbed($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->allow_embed !== $v || $this->isNew()) {
			$this->allow_embed = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::ALLOW_EMBED;
		}

		return $this;
	} // setAllowEmbed()

	/**
	 * Set the value of [adult_content] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setAdultContent($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adult_content !== $v) {
			$this->adult_content = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::ADULT_CONTENT;
		}

		return $this;
	} // setAdultContent()

	/**
	 * Set the value of [transcode_existing_content] column.
	 * 
	 * @param      boolean $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setTranscodeExistingContent($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->transcode_existing_content !== $v || $this->isNew()) {
			$this->transcode_existing_content = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::TRANSCODE_EXISTING_CONTENT;
		}

		return $this;
	} // setTranscodeExistingContent()

	/**
	 * Set the value of [add_to_default_conversion_profile] column.
	 * 
	 * @param      boolean $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setAddToDefaultConversionProfile($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->add_to_default_conversion_profile !== $v || $this->isNew()) {
			$this->add_to_default_conversion_profile = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::ADD_TO_DEFAULT_CONVERSION_PROFILE;
		}

		return $this;
	} // setAddToDefaultConversionProfile()

	/**
	 * Set the value of [categories] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setCategories($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->categories !== $v) {
			$this->categories = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::CATEGORIES;
		}

		return $this;
	} // setCategories()

	/**
	 * Set the value of [feed_description] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setFeedDescription($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->feed_description !== $v) {
			$this->feed_description = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::FEED_DESCRIPTION;
		}

		return $this;
	} // setFeedDescription()

	/**
	 * Set the value of [language] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setLanguage($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->language !== $v) {
			$this->language = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::LANGUAGE;
		}

		return $this;
	} // setLanguage()

	/**
	 * Set the value of [feed_landing_page] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setFeedLandingPage($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->feed_landing_page !== $v) {
			$this->feed_landing_page = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::FEED_LANDING_PAGE;
		}

		return $this;
	} // setFeedLandingPage()

	/**
	 * Set the value of [owner_name] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setOwnerName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->owner_name !== $v) {
			$this->owner_name = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::OWNER_NAME;
		}

		return $this;
	} // setOwnerName()

	/**
	 * Set the value of [owner_email] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setOwnerEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->owner_email !== $v) {
			$this->owner_email = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::OWNER_EMAIL;
		}

		return $this;
	} // setOwnerEmail()

	/**
	 * Set the value of [feed_image_url] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setFeedImageUrl($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->feed_image_url !== $v) {
			$this->feed_image_url = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::FEED_IMAGE_URL;
		}

		return $this;
	} // setFeedImageUrl()

	/**
	 * Set the value of [feed_author] column.
	 * 
	 * @param      string $v new value
	 * @return     syndicationFeed The current object (for fluent API support)
	 */
	public function setFeedAuthor($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->feed_author !== $v) {
			$this->feed_author = $v;
			$this->modifiedColumns[] = syndicationFeedPeer::FEED_AUTHOR;
		}

		return $this;
	} // setFeedAuthor()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     syndicationFeed The current object (for fluent API support)
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
				$this->modifiedColumns[] = syndicationFeedPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

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

			if ($this->landing_page !== '') {
				return false;
			}

			if ($this->allow_embed !== true) {
				return false;
			}

			if ($this->transcode_existing_content !== false) {
				return false;
			}

			if ($this->add_to_default_conversion_profile !== false) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->int_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->partner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->playlist_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->status = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->type = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->landing_page = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->flavor_param_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->player_uiconf_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->allow_embed = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
			$this->adult_content = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->transcode_existing_content = ($row[$startcol + 12] !== null) ? (boolean) $row[$startcol + 12] : null;
			$this->add_to_default_conversion_profile = ($row[$startcol + 13] !== null) ? (boolean) $row[$startcol + 13] : null;
			$this->categories = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->feed_description = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->language = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->feed_landing_page = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->owner_name = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->owner_email = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->feed_image_url = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->feed_author = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->created_at = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = syndicationFeedPeer::NUM_COLUMNS - syndicationFeedPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating syndicationFeed object", $e);
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
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = syndicationFeedPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				syndicationFeedPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				syndicationFeedPeer::addInstanceToPool($this);
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
					$pk = syndicationFeedPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += syndicationFeedPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

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
		syndicationFeedPeer::setUseCriteriaFilter(false);
		$this->reload();
		syndicationFeedPeer::setUseCriteriaFilter(true);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
	}

	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isModified())
		{
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->modifiedColumns));
		}
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


			if (($retval = syndicationFeedPeer::doValidate($this, $columns)) !== true) {
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
		$pos = syndicationFeedPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIntId();
				break;
			case 2:
				return $this->getPartnerId();
				break;
			case 3:
				return $this->getPlaylistId();
				break;
			case 4:
				return $this->getName();
				break;
			case 5:
				return $this->getStatus();
				break;
			case 6:
				return $this->getType();
				break;
			case 7:
				return $this->getLandingPage();
				break;
			case 8:
				return $this->getFlavorParamId();
				break;
			case 9:
				return $this->getPlayerUiconfId();
				break;
			case 10:
				return $this->getAllowEmbed();
				break;
			case 11:
				return $this->getAdultContent();
				break;
			case 12:
				return $this->getTranscodeExistingContent();
				break;
			case 13:
				return $this->getAddToDefaultConversionProfile();
				break;
			case 14:
				return $this->getCategories();
				break;
			case 15:
				return $this->getFeedDescription();
				break;
			case 16:
				return $this->getLanguage();
				break;
			case 17:
				return $this->getFeedLandingPage();
				break;
			case 18:
				return $this->getOwnerName();
				break;
			case 19:
				return $this->getOwnerEmail();
				break;
			case 20:
				return $this->getFeedImageUrl();
				break;
			case 21:
				return $this->getFeedAuthor();
				break;
			case 22:
				return $this->getCreatedAt();
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
		$keys = syndicationFeedPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getIntId(),
			$keys[2] => $this->getPartnerId(),
			$keys[3] => $this->getPlaylistId(),
			$keys[4] => $this->getName(),
			$keys[5] => $this->getStatus(),
			$keys[6] => $this->getType(),
			$keys[7] => $this->getLandingPage(),
			$keys[8] => $this->getFlavorParamId(),
			$keys[9] => $this->getPlayerUiconfId(),
			$keys[10] => $this->getAllowEmbed(),
			$keys[11] => $this->getAdultContent(),
			$keys[12] => $this->getTranscodeExistingContent(),
			$keys[13] => $this->getAddToDefaultConversionProfile(),
			$keys[14] => $this->getCategories(),
			$keys[15] => $this->getFeedDescription(),
			$keys[16] => $this->getLanguage(),
			$keys[17] => $this->getFeedLandingPage(),
			$keys[18] => $this->getOwnerName(),
			$keys[19] => $this->getOwnerEmail(),
			$keys[20] => $this->getFeedImageUrl(),
			$keys[21] => $this->getFeedAuthor(),
			$keys[22] => $this->getCreatedAt(),
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
		$pos = syndicationFeedPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIntId($value);
				break;
			case 2:
				$this->setPartnerId($value);
				break;
			case 3:
				$this->setPlaylistId($value);
				break;
			case 4:
				$this->setName($value);
				break;
			case 5:
				$this->setStatus($value);
				break;
			case 6:
				$this->setType($value);
				break;
			case 7:
				$this->setLandingPage($value);
				break;
			case 8:
				$this->setFlavorParamId($value);
				break;
			case 9:
				$this->setPlayerUiconfId($value);
				break;
			case 10:
				$this->setAllowEmbed($value);
				break;
			case 11:
				$this->setAdultContent($value);
				break;
			case 12:
				$this->setTranscodeExistingContent($value);
				break;
			case 13:
				$this->setAddToDefaultConversionProfile($value);
				break;
			case 14:
				$this->setCategories($value);
				break;
			case 15:
				$this->setFeedDescription($value);
				break;
			case 16:
				$this->setLanguage($value);
				break;
			case 17:
				$this->setFeedLandingPage($value);
				break;
			case 18:
				$this->setOwnerName($value);
				break;
			case 19:
				$this->setOwnerEmail($value);
				break;
			case 20:
				$this->setFeedImageUrl($value);
				break;
			case 21:
				$this->setFeedAuthor($value);
				break;
			case 22:
				$this->setCreatedAt($value);
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
		$keys = syndicationFeedPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIntId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPlaylistId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setStatus($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setType($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLandingPage($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setFlavorParamId($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setPlayerUiconfId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setAllowEmbed($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setAdultContent($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setTranscodeExistingContent($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setAddToDefaultConversionProfile($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCategories($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setFeedDescription($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setLanguage($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setFeedLandingPage($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setOwnerName($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setOwnerEmail($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setFeedImageUrl($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setFeedAuthor($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setCreatedAt($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(syndicationFeedPeer::DATABASE_NAME);

		if ($this->isColumnModified(syndicationFeedPeer::ID)) $criteria->add(syndicationFeedPeer::ID, $this->id);
		if ($this->isColumnModified(syndicationFeedPeer::INT_ID)) $criteria->add(syndicationFeedPeer::INT_ID, $this->int_id);
		if ($this->isColumnModified(syndicationFeedPeer::PARTNER_ID)) $criteria->add(syndicationFeedPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(syndicationFeedPeer::PLAYLIST_ID)) $criteria->add(syndicationFeedPeer::PLAYLIST_ID, $this->playlist_id);
		if ($this->isColumnModified(syndicationFeedPeer::NAME)) $criteria->add(syndicationFeedPeer::NAME, $this->name);
		if ($this->isColumnModified(syndicationFeedPeer::STATUS)) $criteria->add(syndicationFeedPeer::STATUS, $this->status);
		if ($this->isColumnModified(syndicationFeedPeer::TYPE)) $criteria->add(syndicationFeedPeer::TYPE, $this->type);
		if ($this->isColumnModified(syndicationFeedPeer::LANDING_PAGE)) $criteria->add(syndicationFeedPeer::LANDING_PAGE, $this->landing_page);
		if ($this->isColumnModified(syndicationFeedPeer::FLAVOR_PARAM_ID)) $criteria->add(syndicationFeedPeer::FLAVOR_PARAM_ID, $this->flavor_param_id);
		if ($this->isColumnModified(syndicationFeedPeer::PLAYER_UICONF_ID)) $criteria->add(syndicationFeedPeer::PLAYER_UICONF_ID, $this->player_uiconf_id);
		if ($this->isColumnModified(syndicationFeedPeer::ALLOW_EMBED)) $criteria->add(syndicationFeedPeer::ALLOW_EMBED, $this->allow_embed);
		if ($this->isColumnModified(syndicationFeedPeer::ADULT_CONTENT)) $criteria->add(syndicationFeedPeer::ADULT_CONTENT, $this->adult_content);
		if ($this->isColumnModified(syndicationFeedPeer::TRANSCODE_EXISTING_CONTENT)) $criteria->add(syndicationFeedPeer::TRANSCODE_EXISTING_CONTENT, $this->transcode_existing_content);
		if ($this->isColumnModified(syndicationFeedPeer::ADD_TO_DEFAULT_CONVERSION_PROFILE)) $criteria->add(syndicationFeedPeer::ADD_TO_DEFAULT_CONVERSION_PROFILE, $this->add_to_default_conversion_profile);
		if ($this->isColumnModified(syndicationFeedPeer::CATEGORIES)) $criteria->add(syndicationFeedPeer::CATEGORIES, $this->categories);
		if ($this->isColumnModified(syndicationFeedPeer::FEED_DESCRIPTION)) $criteria->add(syndicationFeedPeer::FEED_DESCRIPTION, $this->feed_description);
		if ($this->isColumnModified(syndicationFeedPeer::LANGUAGE)) $criteria->add(syndicationFeedPeer::LANGUAGE, $this->language);
		if ($this->isColumnModified(syndicationFeedPeer::FEED_LANDING_PAGE)) $criteria->add(syndicationFeedPeer::FEED_LANDING_PAGE, $this->feed_landing_page);
		if ($this->isColumnModified(syndicationFeedPeer::OWNER_NAME)) $criteria->add(syndicationFeedPeer::OWNER_NAME, $this->owner_name);
		if ($this->isColumnModified(syndicationFeedPeer::OWNER_EMAIL)) $criteria->add(syndicationFeedPeer::OWNER_EMAIL, $this->owner_email);
		if ($this->isColumnModified(syndicationFeedPeer::FEED_IMAGE_URL)) $criteria->add(syndicationFeedPeer::FEED_IMAGE_URL, $this->feed_image_url);
		if ($this->isColumnModified(syndicationFeedPeer::FEED_AUTHOR)) $criteria->add(syndicationFeedPeer::FEED_AUTHOR, $this->feed_author);
		if ($this->isColumnModified(syndicationFeedPeer::CREATED_AT)) $criteria->add(syndicationFeedPeer::CREATED_AT, $this->created_at);

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
		$criteria = new Criteria(syndicationFeedPeer::DATABASE_NAME);

		$criteria->add(syndicationFeedPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      string $key Primary key.
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
	 * @param      object $copyObj An object of syndicationFeed (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setId($this->id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setPlaylistId($this->playlist_id);

		$copyObj->setName($this->name);

		$copyObj->setStatus($this->status);

		$copyObj->setType($this->type);

		$copyObj->setLandingPage($this->landing_page);

		$copyObj->setFlavorParamId($this->flavor_param_id);

		$copyObj->setPlayerUiconfId($this->player_uiconf_id);

		$copyObj->setAllowEmbed($this->allow_embed);

		$copyObj->setAdultContent($this->adult_content);

		$copyObj->setTranscodeExistingContent($this->transcode_existing_content);

		$copyObj->setAddToDefaultConversionProfile($this->add_to_default_conversion_profile);

		$copyObj->setCategories($this->categories);

		$copyObj->setFeedDescription($this->feed_description);

		$copyObj->setLanguage($this->language);

		$copyObj->setFeedLandingPage($this->feed_landing_page);

		$copyObj->setOwnerName($this->owner_name);

		$copyObj->setOwnerEmail($this->owner_email);

		$copyObj->setFeedImageUrl($this->feed_image_url);

		$copyObj->setFeedAuthor($this->feed_author);

		$copyObj->setCreatedAt($this->created_at);


		$copyObj->setNew(true);

		$copyObj->setIntId(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     syndicationFeed Clone of current object.
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
	 * @var     syndicationFeed Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      syndicationFeed $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(syndicationFeed $copiedFrom)
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
	 * @return     syndicationFeedPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new syndicationFeedPeer();
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

} // BasesyndicationFeed
