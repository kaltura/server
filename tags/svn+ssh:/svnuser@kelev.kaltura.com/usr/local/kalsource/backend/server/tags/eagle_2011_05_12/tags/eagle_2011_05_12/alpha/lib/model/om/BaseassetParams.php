<?php

/**
 * Base class that represents a row from the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseassetParams extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        assetParamsPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the version field.
	 * @var        int
	 */
	protected $version;

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
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the description field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the ready_behavior field.
	 * @var        int
	 */
	protected $ready_behavior;

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
	 * The value for the is_default field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $is_default;

	/**
	 * The value for the format field.
	 * @var        string
	 */
	protected $format;

	/**
	 * The value for the video_codec field.
	 * @var        string
	 */
	protected $video_codec;

	/**
	 * The value for the video_bitrate field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $video_bitrate;

	/**
	 * The value for the audio_codec field.
	 * @var        string
	 */
	protected $audio_codec;

	/**
	 * The value for the audio_bitrate field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $audio_bitrate;

	/**
	 * The value for the audio_channels field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $audio_channels;

	/**
	 * The value for the audio_sample_rate field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $audio_sample_rate;

	/**
	 * The value for the audio_resolution field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $audio_resolution;

	/**
	 * The value for the width field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $width;

	/**
	 * The value for the height field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $height;

	/**
	 * The value for the frame_rate field.
	 * Note: this column has a database default value of: 0
	 * @var        double
	 */
	protected $frame_rate;

	/**
	 * The value for the gop_size field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $gop_size;

	/**
	 * The value for the two_pass field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $two_pass;

	/**
	 * The value for the conversion_engines field.
	 * @var        string
	 */
	protected $conversion_engines;

	/**
	 * The value for the conversion_engines_extra_params field.
	 * @var        string
	 */
	protected $conversion_engines_extra_params;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the view_order field.
	 * @var        int
	 */
	protected $view_order;

	/**
	 * The value for the creation_mode field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $creation_mode;

	/**
	 * The value for the deinterlice field.
	 * @var        int
	 */
	protected $deinterlice;

	/**
	 * The value for the rotate field.
	 * @var        int
	 */
	protected $rotate;

	/**
	 * The value for the operators field.
	 * @var        string
	 */
	protected $operators;

	/**
	 * The value for the engine_version field.
	 * @var        int
	 */
	protected $engine_version;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $type;

	/**
	 * @var        array assetParamsOutput[] Collection to store aggregation of assetParamsOutput objects.
	 */
	protected $collassetParamsOutputs;

	/**
	 * @var        Criteria The criteria used to select the current contents of collassetParamsOutputs.
	 */
	private $lastassetParamsOutputCriteria = null;

	/**
	 * @var        array asset[] Collection to store aggregation of asset objects.
	 */
	protected $collassets;

	/**
	 * @var        Criteria The criteria used to select the current contents of collassets.
	 */
	private $lastassetCriteria = null;

	/**
	 * @var        array flavorParamsConversionProfile[] Collection to store aggregation of flavorParamsConversionProfile objects.
	 */
	protected $collflavorParamsConversionProfiles;

	/**
	 * @var        Criteria The criteria used to select the current contents of collflavorParamsConversionProfiles.
	 */
	private $lastflavorParamsConversionProfileCriteria = null;

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
		$this->is_default = 0;
		$this->video_bitrate = 0;
		$this->audio_bitrate = 0;
		$this->audio_channels = 0;
		$this->audio_sample_rate = 0;
		$this->audio_resolution = 0;
		$this->width = 0;
		$this->height = 0;
		$this->frame_rate = 0;
		$this->gop_size = 0;
		$this->two_pass = false;
		$this->creation_mode = 1;
		$this->type = 0;
	}

	/**
	 * Initializes internal state of BaseassetParams object.
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
	 * Get the [version] column value.
	 * 
	 * @return     int
	 */
	public function getVersion()
	{
		return $this->version;
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
	 * Get the [tags] column value.
	 * 
	 * @return     string
	 */
	public function getTags()
	{
		return $this->tags;
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
	 * Get the [ready_behavior] column value.
	 * 
	 * @return     int
	 */
	public function getReadyBehavior()
	{
		return $this->ready_behavior;
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
	 * Get the [is_default] column value.
	 * 
	 * @return     int
	 */
	public function getIsDefault()
	{
		return $this->is_default;
	}

	/**
	 * Get the [format] column value.
	 * 
	 * @return     string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Get the [video_codec] column value.
	 * 
	 * @return     string
	 */
	public function getVideoCodec()
	{
		return $this->video_codec;
	}

	/**
	 * Get the [video_bitrate] column value.
	 * 
	 * @return     int
	 */
	public function getVideoBitrate()
	{
		return $this->video_bitrate;
	}

	/**
	 * Get the [audio_codec] column value.
	 * 
	 * @return     string
	 */
	public function getAudioCodec()
	{
		return $this->audio_codec;
	}

	/**
	 * Get the [audio_bitrate] column value.
	 * 
	 * @return     int
	 */
	public function getAudioBitrate()
	{
		return $this->audio_bitrate;
	}

	/**
	 * Get the [audio_channels] column value.
	 * 
	 * @return     int
	 */
	public function getAudioChannels()
	{
		return $this->audio_channels;
	}

	/**
	 * Get the [audio_sample_rate] column value.
	 * 
	 * @return     int
	 */
	public function getAudioSampleRate()
	{
		return $this->audio_sample_rate;
	}

	/**
	 * Get the [audio_resolution] column value.
	 * 
	 * @return     int
	 */
	public function getAudioResolution()
	{
		return $this->audio_resolution;
	}

	/**
	 * Get the [width] column value.
	 * 
	 * @return     int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get the [height] column value.
	 * 
	 * @return     int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get the [frame_rate] column value.
	 * 
	 * @return     double
	 */
	public function getFrameRate()
	{
		return $this->frame_rate;
	}

	/**
	 * Get the [gop_size] column value.
	 * 
	 * @return     int
	 */
	public function getGopSize()
	{
		return $this->gop_size;
	}

	/**
	 * Get the [two_pass] column value.
	 * 
	 * @return     boolean
	 */
	public function getTwoPass()
	{
		return $this->two_pass;
	}

	/**
	 * Get the [conversion_engines] column value.
	 * 
	 * @return     string
	 */
	public function getConversionEngines()
	{
		return $this->conversion_engines;
	}

	/**
	 * Get the [conversion_engines_extra_params] column value.
	 * 
	 * @return     string
	 */
	public function getConversionEnginesExtraParams()
	{
		return $this->conversion_engines_extra_params;
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
	 * Get the [view_order] column value.
	 * 
	 * @return     int
	 */
	public function getViewOrder()
	{
		return $this->view_order;
	}

	/**
	 * Get the [creation_mode] column value.
	 * 
	 * @return     int
	 */
	public function getCreationMode()
	{
		return $this->creation_mode;
	}

	/**
	 * Get the [deinterlice] column value.
	 * 
	 * @return     int
	 */
	public function getDeinterlice()
	{
		return $this->deinterlice;
	}

	/**
	 * Get the [rotate] column value.
	 * 
	 * @return     int
	 */
	public function getRotate()
	{
		return $this->rotate;
	}

	/**
	 * Get the [operators] column value.
	 * 
	 * @return     string
	 */
	public function getOperators()
	{
		return $this->operators;
	}

	/**
	 * Get the [engine_version] column value.
	 * 
	 * @return     int
	 */
	public function getEngineVersion()
	{
		return $this->engine_version;
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::ID]))
			$this->oldColumnsValues[assetParamsPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = assetParamsPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::VERSION]))
			$this->oldColumnsValues[assetParamsPeer::VERSION] = $this->version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = assetParamsPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::PARTNER_ID]))
			$this->oldColumnsValues[assetParamsPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = assetParamsPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::NAME]))
			$this->oldColumnsValues[assetParamsPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = assetParamsPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [system_name] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setSystemName($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::SYSTEM_NAME]))
			$this->oldColumnsValues[assetParamsPeer::SYSTEM_NAME] = $this->system_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->system_name !== $v || $this->isNew()) {
			$this->system_name = $v;
			$this->modifiedColumns[] = assetParamsPeer::SYSTEM_NAME;
		}

		return $this;
	} // setSystemName()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::TAGS]))
			$this->oldColumnsValues[assetParamsPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = assetParamsPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::DESCRIPTION]))
			$this->oldColumnsValues[assetParamsPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v || $this->isNew()) {
			$this->description = $v;
			$this->modifiedColumns[] = assetParamsPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [ready_behavior] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setReadyBehavior($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::READY_BEHAVIOR]))
			$this->oldColumnsValues[assetParamsPeer::READY_BEHAVIOR] = $this->ready_behavior;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ready_behavior !== $v) {
			$this->ready_behavior = $v;
			$this->modifiedColumns[] = assetParamsPeer::READY_BEHAVIOR;
		}

		return $this;
	} // setReadyBehavior()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParams The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetParamsPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParams The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetParamsPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::DELETED_AT]))
			$this->oldColumnsValues[assetParamsPeer::DELETED_AT] = $this->deleted_at;

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
				$this->modifiedColumns[] = assetParamsPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [is_default] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setIsDefault($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::IS_DEFAULT]))
			$this->oldColumnsValues[assetParamsPeer::IS_DEFAULT] = $this->is_default;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->is_default !== $v || $this->isNew()) {
			$this->is_default = $v;
			$this->modifiedColumns[] = assetParamsPeer::IS_DEFAULT;
		}

		return $this;
	} // setIsDefault()

	/**
	 * Set the value of [format] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setFormat($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::FORMAT]))
			$this->oldColumnsValues[assetParamsPeer::FORMAT] = $this->format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->format !== $v) {
			$this->format = $v;
			$this->modifiedColumns[] = assetParamsPeer::FORMAT;
		}

		return $this;
	} // setFormat()

	/**
	 * Set the value of [video_codec] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setVideoCodec($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::VIDEO_CODEC]))
			$this->oldColumnsValues[assetParamsPeer::VIDEO_CODEC] = $this->video_codec;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->video_codec !== $v) {
			$this->video_codec = $v;
			$this->modifiedColumns[] = assetParamsPeer::VIDEO_CODEC;
		}

		return $this;
	} // setVideoCodec()

	/**
	 * Set the value of [video_bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setVideoBitrate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::VIDEO_BITRATE]))
			$this->oldColumnsValues[assetParamsPeer::VIDEO_BITRATE] = $this->video_bitrate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_bitrate !== $v || $this->isNew()) {
			$this->video_bitrate = $v;
			$this->modifiedColumns[] = assetParamsPeer::VIDEO_BITRATE;
		}

		return $this;
	} // setVideoBitrate()

	/**
	 * Set the value of [audio_codec] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setAudioCodec($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::AUDIO_CODEC]))
			$this->oldColumnsValues[assetParamsPeer::AUDIO_CODEC] = $this->audio_codec;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->audio_codec !== $v) {
			$this->audio_codec = $v;
			$this->modifiedColumns[] = assetParamsPeer::AUDIO_CODEC;
		}

		return $this;
	} // setAudioCodec()

	/**
	 * Set the value of [audio_bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setAudioBitrate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::AUDIO_BITRATE]))
			$this->oldColumnsValues[assetParamsPeer::AUDIO_BITRATE] = $this->audio_bitrate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_bitrate !== $v || $this->isNew()) {
			$this->audio_bitrate = $v;
			$this->modifiedColumns[] = assetParamsPeer::AUDIO_BITRATE;
		}

		return $this;
	} // setAudioBitrate()

	/**
	 * Set the value of [audio_channels] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setAudioChannels($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::AUDIO_CHANNELS]))
			$this->oldColumnsValues[assetParamsPeer::AUDIO_CHANNELS] = $this->audio_channels;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_channels !== $v || $this->isNew()) {
			$this->audio_channels = $v;
			$this->modifiedColumns[] = assetParamsPeer::AUDIO_CHANNELS;
		}

		return $this;
	} // setAudioChannels()

	/**
	 * Set the value of [audio_sample_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setAudioSampleRate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::AUDIO_SAMPLE_RATE]))
			$this->oldColumnsValues[assetParamsPeer::AUDIO_SAMPLE_RATE] = $this->audio_sample_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_sample_rate !== $v || $this->isNew()) {
			$this->audio_sample_rate = $v;
			$this->modifiedColumns[] = assetParamsPeer::AUDIO_SAMPLE_RATE;
		}

		return $this;
	} // setAudioSampleRate()

	/**
	 * Set the value of [audio_resolution] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setAudioResolution($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::AUDIO_RESOLUTION]))
			$this->oldColumnsValues[assetParamsPeer::AUDIO_RESOLUTION] = $this->audio_resolution;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_resolution !== $v || $this->isNew()) {
			$this->audio_resolution = $v;
			$this->modifiedColumns[] = assetParamsPeer::AUDIO_RESOLUTION;
		}

		return $this;
	} // setAudioResolution()

	/**
	 * Set the value of [width] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setWidth($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::WIDTH]))
			$this->oldColumnsValues[assetParamsPeer::WIDTH] = $this->width;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->width !== $v || $this->isNew()) {
			$this->width = $v;
			$this->modifiedColumns[] = assetParamsPeer::WIDTH;
		}

		return $this;
	} // setWidth()

	/**
	 * Set the value of [height] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setHeight($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::HEIGHT]))
			$this->oldColumnsValues[assetParamsPeer::HEIGHT] = $this->height;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->height !== $v || $this->isNew()) {
			$this->height = $v;
			$this->modifiedColumns[] = assetParamsPeer::HEIGHT;
		}

		return $this;
	} // setHeight()

	/**
	 * Set the value of [frame_rate] column.
	 * 
	 * @param      double $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setFrameRate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::FRAME_RATE]))
			$this->oldColumnsValues[assetParamsPeer::FRAME_RATE] = $this->frame_rate;

		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->frame_rate !== $v || $this->isNew()) {
			$this->frame_rate = $v;
			$this->modifiedColumns[] = assetParamsPeer::FRAME_RATE;
		}

		return $this;
	} // setFrameRate()

	/**
	 * Set the value of [gop_size] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setGopSize($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::GOP_SIZE]))
			$this->oldColumnsValues[assetParamsPeer::GOP_SIZE] = $this->gop_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->gop_size !== $v || $this->isNew()) {
			$this->gop_size = $v;
			$this->modifiedColumns[] = assetParamsPeer::GOP_SIZE;
		}

		return $this;
	} // setGopSize()

	/**
	 * Set the value of [two_pass] column.
	 * 
	 * @param      boolean $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setTwoPass($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::TWO_PASS]))
			$this->oldColumnsValues[assetParamsPeer::TWO_PASS] = $this->two_pass;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->two_pass !== $v || $this->isNew()) {
			$this->two_pass = $v;
			$this->modifiedColumns[] = assetParamsPeer::TWO_PASS;
		}

		return $this;
	} // setTwoPass()

	/**
	 * Set the value of [conversion_engines] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setConversionEngines($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::CONVERSION_ENGINES]))
			$this->oldColumnsValues[assetParamsPeer::CONVERSION_ENGINES] = $this->conversion_engines;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conversion_engines !== $v) {
			$this->conversion_engines = $v;
			$this->modifiedColumns[] = assetParamsPeer::CONVERSION_ENGINES;
		}

		return $this;
	} // setConversionEngines()

	/**
	 * Set the value of [conversion_engines_extra_params] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setConversionEnginesExtraParams($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS]))
			$this->oldColumnsValues[assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS] = $this->conversion_engines_extra_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conversion_engines_extra_params !== $v) {
			$this->conversion_engines_extra_params = $v;
			$this->modifiedColumns[] = assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS;
		}

		return $this;
	} // setConversionEnginesExtraParams()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = assetParamsPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [view_order] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setViewOrder($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::VIEW_ORDER]))
			$this->oldColumnsValues[assetParamsPeer::VIEW_ORDER] = $this->view_order;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->view_order !== $v) {
			$this->view_order = $v;
			$this->modifiedColumns[] = assetParamsPeer::VIEW_ORDER;
		}

		return $this;
	} // setViewOrder()

	/**
	 * Set the value of [creation_mode] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setCreationMode($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::CREATION_MODE]))
			$this->oldColumnsValues[assetParamsPeer::CREATION_MODE] = $this->creation_mode;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->creation_mode !== $v || $this->isNew()) {
			$this->creation_mode = $v;
			$this->modifiedColumns[] = assetParamsPeer::CREATION_MODE;
		}

		return $this;
	} // setCreationMode()

	/**
	 * Set the value of [deinterlice] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setDeinterlice($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::DEINTERLICE]))
			$this->oldColumnsValues[assetParamsPeer::DEINTERLICE] = $this->deinterlice;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->deinterlice !== $v) {
			$this->deinterlice = $v;
			$this->modifiedColumns[] = assetParamsPeer::DEINTERLICE;
		}

		return $this;
	} // setDeinterlice()

	/**
	 * Set the value of [rotate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setRotate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::ROTATE]))
			$this->oldColumnsValues[assetParamsPeer::ROTATE] = $this->rotate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rotate !== $v) {
			$this->rotate = $v;
			$this->modifiedColumns[] = assetParamsPeer::ROTATE;
		}

		return $this;
	} // setRotate()

	/**
	 * Set the value of [operators] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setOperators($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::OPERATORS]))
			$this->oldColumnsValues[assetParamsPeer::OPERATORS] = $this->operators;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->operators !== $v) {
			$this->operators = $v;
			$this->modifiedColumns[] = assetParamsPeer::OPERATORS;
		}

		return $this;
	} // setOperators()

	/**
	 * Set the value of [engine_version] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setEngineVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::ENGINE_VERSION]))
			$this->oldColumnsValues[assetParamsPeer::ENGINE_VERSION] = $this->engine_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->engine_version !== $v) {
			$this->engine_version = $v;
			$this->modifiedColumns[] = assetParamsPeer::ENGINE_VERSION;
		}

		return $this;
	} // setEngineVersion()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParams The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsPeer::TYPE]))
			$this->oldColumnsValues[assetParamsPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = assetParamsPeer::TYPE;
		}

		return $this;
	} // setType()

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

			if ($this->is_default !== 0) {
				return false;
			}

			if ($this->video_bitrate !== 0) {
				return false;
			}

			if ($this->audio_bitrate !== 0) {
				return false;
			}

			if ($this->audio_channels !== 0) {
				return false;
			}

			if ($this->audio_sample_rate !== 0) {
				return false;
			}

			if ($this->audio_resolution !== 0) {
				return false;
			}

			if ($this->width !== 0) {
				return false;
			}

			if ($this->height !== 0) {
				return false;
			}

			if ($this->frame_rate !== 0) {
				return false;
			}

			if ($this->gop_size !== 0) {
				return false;
			}

			if ($this->two_pass !== false) {
				return false;
			}

			if ($this->creation_mode !== 1) {
				return false;
			}

			if ($this->type !== 0) {
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
			$this->version = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->partner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->system_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->tags = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->description = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->ready_behavior = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->deleted_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->is_default = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->format = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->video_codec = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->video_bitrate = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->audio_codec = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->audio_bitrate = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->audio_channels = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->audio_sample_rate = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->audio_resolution = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->width = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->height = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->frame_rate = ($row[$startcol + 22] !== null) ? (double) $row[$startcol + 22] : null;
			$this->gop_size = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->two_pass = ($row[$startcol + 24] !== null) ? (boolean) $row[$startcol + 24] : null;
			$this->conversion_engines = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->conversion_engines_extra_params = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->custom_data = ($row[$startcol + 27] !== null) ? (string) $row[$startcol + 27] : null;
			$this->view_order = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
			$this->creation_mode = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
			$this->deinterlice = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->rotate = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->operators = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
			$this->engine_version = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->type = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 35; // 35 = assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating assetParams object", $e);
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
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = assetParamsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collassetParamsOutputs = null;
			$this->lastassetParamsOutputCriteria = null;

			$this->collassets = null;
			$this->lastassetCriteria = null;

			$this->collflavorParamsConversionProfiles = null;
			$this->lastflavorParamsConversionProfileCriteria = null;

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
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				assetParamsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				assetParamsPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = assetParamsPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = assetParamsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += assetParamsPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collassetParamsOutputs !== null) {
				foreach ($this->collassetParamsOutputs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collassets !== null) {
				foreach ($this->collassets as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collflavorParamsConversionProfiles !== null) {
				foreach ($this->collflavorParamsConversionProfiles as $referrerFK) {
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


			if (($retval = assetParamsPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collassetParamsOutputs !== null) {
					foreach ($this->collassetParamsOutputs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collassets !== null) {
					foreach ($this->collassets as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collflavorParamsConversionProfiles !== null) {
					foreach ($this->collflavorParamsConversionProfiles as $referrerFK) {
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
		$pos = assetParamsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getVersion();
				break;
			case 2:
				return $this->getPartnerId();
				break;
			case 3:
				return $this->getName();
				break;
			case 4:
				return $this->getSystemName();
				break;
			case 5:
				return $this->getTags();
				break;
			case 6:
				return $this->getDescription();
				break;
			case 7:
				return $this->getReadyBehavior();
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
				return $this->getIsDefault();
				break;
			case 12:
				return $this->getFormat();
				break;
			case 13:
				return $this->getVideoCodec();
				break;
			case 14:
				return $this->getVideoBitrate();
				break;
			case 15:
				return $this->getAudioCodec();
				break;
			case 16:
				return $this->getAudioBitrate();
				break;
			case 17:
				return $this->getAudioChannels();
				break;
			case 18:
				return $this->getAudioSampleRate();
				break;
			case 19:
				return $this->getAudioResolution();
				break;
			case 20:
				return $this->getWidth();
				break;
			case 21:
				return $this->getHeight();
				break;
			case 22:
				return $this->getFrameRate();
				break;
			case 23:
				return $this->getGopSize();
				break;
			case 24:
				return $this->getTwoPass();
				break;
			case 25:
				return $this->getConversionEngines();
				break;
			case 26:
				return $this->getConversionEnginesExtraParams();
				break;
			case 27:
				return $this->getCustomData();
				break;
			case 28:
				return $this->getViewOrder();
				break;
			case 29:
				return $this->getCreationMode();
				break;
			case 30:
				return $this->getDeinterlice();
				break;
			case 31:
				return $this->getRotate();
				break;
			case 32:
				return $this->getOperators();
				break;
			case 33:
				return $this->getEngineVersion();
				break;
			case 34:
				return $this->getType();
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
		$keys = assetParamsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getVersion(),
			$keys[2] => $this->getPartnerId(),
			$keys[3] => $this->getName(),
			$keys[4] => $this->getSystemName(),
			$keys[5] => $this->getTags(),
			$keys[6] => $this->getDescription(),
			$keys[7] => $this->getReadyBehavior(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getUpdatedAt(),
			$keys[10] => $this->getDeletedAt(),
			$keys[11] => $this->getIsDefault(),
			$keys[12] => $this->getFormat(),
			$keys[13] => $this->getVideoCodec(),
			$keys[14] => $this->getVideoBitrate(),
			$keys[15] => $this->getAudioCodec(),
			$keys[16] => $this->getAudioBitrate(),
			$keys[17] => $this->getAudioChannels(),
			$keys[18] => $this->getAudioSampleRate(),
			$keys[19] => $this->getAudioResolution(),
			$keys[20] => $this->getWidth(),
			$keys[21] => $this->getHeight(),
			$keys[22] => $this->getFrameRate(),
			$keys[23] => $this->getGopSize(),
			$keys[24] => $this->getTwoPass(),
			$keys[25] => $this->getConversionEngines(),
			$keys[26] => $this->getConversionEnginesExtraParams(),
			$keys[27] => $this->getCustomData(),
			$keys[28] => $this->getViewOrder(),
			$keys[29] => $this->getCreationMode(),
			$keys[30] => $this->getDeinterlice(),
			$keys[31] => $this->getRotate(),
			$keys[32] => $this->getOperators(),
			$keys[33] => $this->getEngineVersion(),
			$keys[34] => $this->getType(),
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
		$pos = assetParamsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setVersion($value);
				break;
			case 2:
				$this->setPartnerId($value);
				break;
			case 3:
				$this->setName($value);
				break;
			case 4:
				$this->setSystemName($value);
				break;
			case 5:
				$this->setTags($value);
				break;
			case 6:
				$this->setDescription($value);
				break;
			case 7:
				$this->setReadyBehavior($value);
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
				$this->setIsDefault($value);
				break;
			case 12:
				$this->setFormat($value);
				break;
			case 13:
				$this->setVideoCodec($value);
				break;
			case 14:
				$this->setVideoBitrate($value);
				break;
			case 15:
				$this->setAudioCodec($value);
				break;
			case 16:
				$this->setAudioBitrate($value);
				break;
			case 17:
				$this->setAudioChannels($value);
				break;
			case 18:
				$this->setAudioSampleRate($value);
				break;
			case 19:
				$this->setAudioResolution($value);
				break;
			case 20:
				$this->setWidth($value);
				break;
			case 21:
				$this->setHeight($value);
				break;
			case 22:
				$this->setFrameRate($value);
				break;
			case 23:
				$this->setGopSize($value);
				break;
			case 24:
				$this->setTwoPass($value);
				break;
			case 25:
				$this->setConversionEngines($value);
				break;
			case 26:
				$this->setConversionEnginesExtraParams($value);
				break;
			case 27:
				$this->setCustomData($value);
				break;
			case 28:
				$this->setViewOrder($value);
				break;
			case 29:
				$this->setCreationMode($value);
				break;
			case 30:
				$this->setDeinterlice($value);
				break;
			case 31:
				$this->setRotate($value);
				break;
			case 32:
				$this->setOperators($value);
				break;
			case 33:
				$this->setEngineVersion($value);
				break;
			case 34:
				$this->setType($value);
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
		$keys = assetParamsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setVersion($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSystemName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTags($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDescription($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setReadyBehavior($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDeletedAt($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setIsDefault($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setFormat($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setVideoCodec($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setVideoBitrate($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setAudioCodec($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setAudioBitrate($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setAudioChannels($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setAudioSampleRate($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setAudioResolution($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setWidth($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setHeight($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setFrameRate($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setGopSize($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setTwoPass($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setConversionEngines($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setConversionEnginesExtraParams($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setCustomData($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setViewOrder($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setCreationMode($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setDeinterlice($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setRotate($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setOperators($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setEngineVersion($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setType($arr[$keys[34]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);

		if ($this->isColumnModified(assetParamsPeer::ID)) $criteria->add(assetParamsPeer::ID, $this->id);
		if ($this->isColumnModified(assetParamsPeer::VERSION)) $criteria->add(assetParamsPeer::VERSION, $this->version);
		if ($this->isColumnModified(assetParamsPeer::PARTNER_ID)) $criteria->add(assetParamsPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(assetParamsPeer::NAME)) $criteria->add(assetParamsPeer::NAME, $this->name);
		if ($this->isColumnModified(assetParamsPeer::SYSTEM_NAME)) $criteria->add(assetParamsPeer::SYSTEM_NAME, $this->system_name);
		if ($this->isColumnModified(assetParamsPeer::TAGS)) $criteria->add(assetParamsPeer::TAGS, $this->tags);
		if ($this->isColumnModified(assetParamsPeer::DESCRIPTION)) $criteria->add(assetParamsPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(assetParamsPeer::READY_BEHAVIOR)) $criteria->add(assetParamsPeer::READY_BEHAVIOR, $this->ready_behavior);
		if ($this->isColumnModified(assetParamsPeer::CREATED_AT)) $criteria->add(assetParamsPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(assetParamsPeer::UPDATED_AT)) $criteria->add(assetParamsPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(assetParamsPeer::DELETED_AT)) $criteria->add(assetParamsPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(assetParamsPeer::IS_DEFAULT)) $criteria->add(assetParamsPeer::IS_DEFAULT, $this->is_default);
		if ($this->isColumnModified(assetParamsPeer::FORMAT)) $criteria->add(assetParamsPeer::FORMAT, $this->format);
		if ($this->isColumnModified(assetParamsPeer::VIDEO_CODEC)) $criteria->add(assetParamsPeer::VIDEO_CODEC, $this->video_codec);
		if ($this->isColumnModified(assetParamsPeer::VIDEO_BITRATE)) $criteria->add(assetParamsPeer::VIDEO_BITRATE, $this->video_bitrate);
		if ($this->isColumnModified(assetParamsPeer::AUDIO_CODEC)) $criteria->add(assetParamsPeer::AUDIO_CODEC, $this->audio_codec);
		if ($this->isColumnModified(assetParamsPeer::AUDIO_BITRATE)) $criteria->add(assetParamsPeer::AUDIO_BITRATE, $this->audio_bitrate);
		if ($this->isColumnModified(assetParamsPeer::AUDIO_CHANNELS)) $criteria->add(assetParamsPeer::AUDIO_CHANNELS, $this->audio_channels);
		if ($this->isColumnModified(assetParamsPeer::AUDIO_SAMPLE_RATE)) $criteria->add(assetParamsPeer::AUDIO_SAMPLE_RATE, $this->audio_sample_rate);
		if ($this->isColumnModified(assetParamsPeer::AUDIO_RESOLUTION)) $criteria->add(assetParamsPeer::AUDIO_RESOLUTION, $this->audio_resolution);
		if ($this->isColumnModified(assetParamsPeer::WIDTH)) $criteria->add(assetParamsPeer::WIDTH, $this->width);
		if ($this->isColumnModified(assetParamsPeer::HEIGHT)) $criteria->add(assetParamsPeer::HEIGHT, $this->height);
		if ($this->isColumnModified(assetParamsPeer::FRAME_RATE)) $criteria->add(assetParamsPeer::FRAME_RATE, $this->frame_rate);
		if ($this->isColumnModified(assetParamsPeer::GOP_SIZE)) $criteria->add(assetParamsPeer::GOP_SIZE, $this->gop_size);
		if ($this->isColumnModified(assetParamsPeer::TWO_PASS)) $criteria->add(assetParamsPeer::TWO_PASS, $this->two_pass);
		if ($this->isColumnModified(assetParamsPeer::CONVERSION_ENGINES)) $criteria->add(assetParamsPeer::CONVERSION_ENGINES, $this->conversion_engines);
		if ($this->isColumnModified(assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS)) $criteria->add(assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS, $this->conversion_engines_extra_params);
		if ($this->isColumnModified(assetParamsPeer::CUSTOM_DATA)) $criteria->add(assetParamsPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(assetParamsPeer::VIEW_ORDER)) $criteria->add(assetParamsPeer::VIEW_ORDER, $this->view_order);
		if ($this->isColumnModified(assetParamsPeer::CREATION_MODE)) $criteria->add(assetParamsPeer::CREATION_MODE, $this->creation_mode);
		if ($this->isColumnModified(assetParamsPeer::DEINTERLICE)) $criteria->add(assetParamsPeer::DEINTERLICE, $this->deinterlice);
		if ($this->isColumnModified(assetParamsPeer::ROTATE)) $criteria->add(assetParamsPeer::ROTATE, $this->rotate);
		if ($this->isColumnModified(assetParamsPeer::OPERATORS)) $criteria->add(assetParamsPeer::OPERATORS, $this->operators);
		if ($this->isColumnModified(assetParamsPeer::ENGINE_VERSION)) $criteria->add(assetParamsPeer::ENGINE_VERSION, $this->engine_version);
		if ($this->isColumnModified(assetParamsPeer::TYPE)) $criteria->add(assetParamsPeer::TYPE, $this->type);

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
		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);

		$criteria->add(assetParamsPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of assetParams (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setVersion($this->version);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setName($this->name);

		$copyObj->setSystemName($this->system_name);

		$copyObj->setTags($this->tags);

		$copyObj->setDescription($this->description);

		$copyObj->setReadyBehavior($this->ready_behavior);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setDeletedAt($this->deleted_at);

		$copyObj->setIsDefault($this->is_default);

		$copyObj->setFormat($this->format);

		$copyObj->setVideoCodec($this->video_codec);

		$copyObj->setVideoBitrate($this->video_bitrate);

		$copyObj->setAudioCodec($this->audio_codec);

		$copyObj->setAudioBitrate($this->audio_bitrate);

		$copyObj->setAudioChannels($this->audio_channels);

		$copyObj->setAudioSampleRate($this->audio_sample_rate);

		$copyObj->setAudioResolution($this->audio_resolution);

		$copyObj->setWidth($this->width);

		$copyObj->setHeight($this->height);

		$copyObj->setFrameRate($this->frame_rate);

		$copyObj->setGopSize($this->gop_size);

		$copyObj->setTwoPass($this->two_pass);

		$copyObj->setConversionEngines($this->conversion_engines);

		$copyObj->setConversionEnginesExtraParams($this->conversion_engines_extra_params);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setViewOrder($this->view_order);

		$copyObj->setCreationMode($this->creation_mode);

		$copyObj->setDeinterlice($this->deinterlice);

		$copyObj->setRotate($this->rotate);

		$copyObj->setOperators($this->operators);

		$copyObj->setEngineVersion($this->engine_version);

		$copyObj->setType($this->type);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getassetParamsOutputs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addassetParamsOutput($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getassets() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addasset($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getflavorParamsConversionProfiles() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addflavorParamsConversionProfile($relObj->copy($deepCopy));
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
	 * @return     assetParams Clone of current object.
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
	 * @var     assetParams Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      assetParams $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(assetParams $copiedFrom)
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
	 * @return     assetParamsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new assetParamsPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collassetParamsOutputs collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addassetParamsOutputs()
	 */
	public function clearassetParamsOutputs()
	{
		$this->collassetParamsOutputs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collassetParamsOutputs collection (array).
	 *
	 * By default this just sets the collassetParamsOutputs collection to an empty array (like clearcollassetParamsOutputs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initassetParamsOutputs()
	{
		$this->collassetParamsOutputs = array();
	}

	/**
	 * Gets an array of assetParamsOutput objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this assetParams has previously been saved, it will retrieve
	 * related assetParamsOutputs from storage. If this assetParams is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array assetParamsOutput[]
	 * @throws     PropelException
	 */
	public function getassetParamsOutputs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
			   $this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				assetParamsOutputPeer::addSelectColumns($criteria);
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				assetParamsOutputPeer::addSelectColumns($criteria);
				if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
					$this->collassetParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;
		return $this->collassetParamsOutputs;
	}

	/**
	 * Returns the number of related assetParamsOutput objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related assetParamsOutput objects.
	 * @throws     PropelException
	 */
	public function countassetParamsOutputs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				$count = assetParamsOutputPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
					$count = assetParamsOutputPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collassetParamsOutputs);
				}
			} else {
				$count = count($this->collassetParamsOutputs);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a assetParamsOutput object to this object
	 * through the assetParamsOutput foreign key attribute.
	 *
	 * @param      assetParamsOutput $l assetParamsOutput
	 * @return     void
	 * @throws     PropelException
	 */
	public function addassetParamsOutput(assetParamsOutput $l)
	{
		if ($this->collassetParamsOutputs === null) {
			$this->initassetParamsOutputs();
		}
		if (!in_array($l, $this->collassetParamsOutputs, true)) { // only add it if the **same** object is not already associated
			array_push($this->collassetParamsOutputs, $l);
			$l->setassetParams($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this assetParams is new, it will return
	 * an empty collection; or if this assetParams has previously
	 * been saved, it will retrieve related assetParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in assetParams.
	 */
	public function getassetParamsOutputsJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

			if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;

		return $this->collassetParamsOutputs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this assetParams is new, it will return
	 * an empty collection; or if this assetParams has previously
	 * been saved, it will retrieve related assetParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in assetParams.
	 */
	public function getassetParamsOutputsJoinasset($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinasset($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->id);

			if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinasset($criteria, $con, $join_behavior);
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;

		return $this->collassetParamsOutputs;
	}

	/**
	 * Clears out the collassets collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addassets()
	 */
	public function clearassets()
	{
		$this->collassets = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collassets collection (array).
	 *
	 * By default this just sets the collassets collection to an empty array (like clearcollassets());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initassets()
	{
		$this->collassets = array();
	}

	/**
	 * Gets an array of asset objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this assetParams has previously been saved, it will retrieve
	 * related assets from storage. If this assetParams is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array asset[]
	 * @throws     PropelException
	 */
	public function getassets($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassets === null) {
			if ($this->isNew()) {
			   $this->collassets = array();
			} else {

				$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

				assetPeer::addSelectColumns($criteria);
				$this->collassets = assetPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

				assetPeer::addSelectColumns($criteria);
				if (!isset($this->lastassetCriteria) || !$this->lastassetCriteria->equals($criteria)) {
					$this->collassets = assetPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastassetCriteria = $criteria;
		return $this->collassets;
	}

	/**
	 * Returns the number of related asset objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related asset objects.
	 * @throws     PropelException
	 */
	public function countassets(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collassets === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

				$count = assetPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

				if (!isset($this->lastassetCriteria) || !$this->lastassetCriteria->equals($criteria)) {
					$count = assetPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collassets);
				}
			} else {
				$count = count($this->collassets);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a asset object to this object
	 * through the asset foreign key attribute.
	 *
	 * @param      asset $l asset
	 * @return     void
	 * @throws     PropelException
	 */
	public function addasset(asset $l)
	{
		if ($this->collassets === null) {
			$this->initassets();
		}
		if (!in_array($l, $this->collassets, true)) { // only add it if the **same** object is not already associated
			array_push($this->collassets, $l);
			$l->setassetParams($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this assetParams is new, it will return
	 * an empty collection; or if this assetParams has previously
	 * been saved, it will retrieve related assets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in assetParams.
	 */
	public function getassetsJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassets === null) {
			if ($this->isNew()) {
				$this->collassets = array();
			} else {

				$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

				$this->collassets = assetPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->id);

			if (!isset($this->lastassetCriteria) || !$this->lastassetCriteria->equals($criteria)) {
				$this->collassets = assetPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastassetCriteria = $criteria;

		return $this->collassets;
	}

	/**
	 * Clears out the collflavorParamsConversionProfiles collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addflavorParamsConversionProfiles()
	 */
	public function clearflavorParamsConversionProfiles()
	{
		$this->collflavorParamsConversionProfiles = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collflavorParamsConversionProfiles collection (array).
	 *
	 * By default this just sets the collflavorParamsConversionProfiles collection to an empty array (like clearcollflavorParamsConversionProfiles());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initflavorParamsConversionProfiles()
	{
		$this->collflavorParamsConversionProfiles = array();
	}

	/**
	 * Gets an array of flavorParamsConversionProfile objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this assetParams has previously been saved, it will retrieve
	 * related flavorParamsConversionProfiles from storage. If this assetParams is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flavorParamsConversionProfile[]
	 * @throws     PropelException
	 */
	public function getflavorParamsConversionProfiles($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorParamsConversionProfiles === null) {
			if ($this->isNew()) {
			   $this->collflavorParamsConversionProfiles = array();
			} else {

				$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

				flavorParamsConversionProfilePeer::addSelectColumns($criteria);
				$this->collflavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

				flavorParamsConversionProfilePeer::addSelectColumns($criteria);
				if (!isset($this->lastflavorParamsConversionProfileCriteria) || !$this->lastflavorParamsConversionProfileCriteria->equals($criteria)) {
					$this->collflavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastflavorParamsConversionProfileCriteria = $criteria;
		return $this->collflavorParamsConversionProfiles;
	}

	/**
	 * Returns the number of related flavorParamsConversionProfile objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related flavorParamsConversionProfile objects.
	 * @throws     PropelException
	 */
	public function countflavorParamsConversionProfiles(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collflavorParamsConversionProfiles === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

				$count = flavorParamsConversionProfilePeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

				if (!isset($this->lastflavorParamsConversionProfileCriteria) || !$this->lastflavorParamsConversionProfileCriteria->equals($criteria)) {
					$count = flavorParamsConversionProfilePeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collflavorParamsConversionProfiles);
				}
			} else {
				$count = count($this->collflavorParamsConversionProfiles);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a flavorParamsConversionProfile object to this object
	 * through the flavorParamsConversionProfile foreign key attribute.
	 *
	 * @param      flavorParamsConversionProfile $l flavorParamsConversionProfile
	 * @return     void
	 * @throws     PropelException
	 */
	public function addflavorParamsConversionProfile(flavorParamsConversionProfile $l)
	{
		if ($this->collflavorParamsConversionProfiles === null) {
			$this->initflavorParamsConversionProfiles();
		}
		if (!in_array($l, $this->collflavorParamsConversionProfiles, true)) { // only add it if the **same** object is not already associated
			array_push($this->collflavorParamsConversionProfiles, $l);
			$l->setassetParams($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this assetParams is new, it will return
	 * an empty collection; or if this assetParams has previously
	 * been saved, it will retrieve related flavorParamsConversionProfiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in assetParams.
	 */
	public function getflavorParamsConversionProfilesJoinconversionProfile2($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorParamsConversionProfiles === null) {
			if ($this->isNew()) {
				$this->collflavorParamsConversionProfiles = array();
			} else {

				$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

				$this->collflavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $this->id);

			if (!isset($this->lastflavorParamsConversionProfileCriteria) || !$this->lastflavorParamsConversionProfileCriteria->equals($criteria)) {
				$this->collflavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		}
		$this->lastflavorParamsConversionProfileCriteria = $criteria;

		return $this->collflavorParamsConversionProfiles;
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
			if ($this->collassetParamsOutputs) {
				foreach ((array) $this->collassetParamsOutputs as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collassets) {
				foreach ((array) $this->collassets as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collflavorParamsConversionProfiles) {
				foreach ((array) $this->collflavorParamsConversionProfiles as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collassetParamsOutputs = null;
		$this->collassets = null;
		$this->collflavorParamsConversionProfiles = null;
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
	
} // BaseassetParams
