<?php

/**
 * Base class that represents a row from the 'flavor_params_output' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseassetParamsOutput extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        assetParamsOutputPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the flavor_params_id field.
	 * @var        int
	 */
	protected $flavor_params_id;

	/**
	 * The value for the flavor_params_version field.
	 * @var        int
	 */
	protected $flavor_params_version;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the flavor_asset_id field.
	 * @var        string
	 */
	protected $flavor_asset_id;

	/**
	 * The value for the flavor_asset_version field.
	 * @var        string
	 */
	protected $flavor_asset_version;

	/**
	 * The value for the name field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $name;

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
	 * @var        int
	 */
	protected $audio_bitrate;

	/**
	 * The value for the audio_channels field.
	 * @var        int
	 */
	protected $audio_channels;

	/**
	 * The value for the audio_sample_rate field.
	 * @var        int
	 */
	protected $audio_sample_rate;

	/**
	 * The value for the audio_resolution field.
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
	 * The value for the command_lines field.
	 * @var        string
	 */
	protected $command_lines;

	/**
	 * The value for the file_ext field.
	 * @var        string
	 */
	protected $file_ext;

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
	 * @var        assetParams
	 */
	protected $aassetParams;

	/**
	 * @var        entry
	 */
	protected $aentry;

	/**
	 * @var        asset
	 */
	protected $aasset;

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
		$this->description = '';
		$this->is_default = 0;
		$this->video_bitrate = 0;
		$this->width = 0;
		$this->height = 0;
		$this->gop_size = 0;
		$this->two_pass = false;
		$this->type = 0;
	}

	/**
	 * Initializes internal state of BaseassetParamsOutput object.
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
	 * Get the [flavor_params_id] column value.
	 * 
	 * @return     int
	 */
	public function getFlavorParamsId()
	{
		return $this->flavor_params_id;
	}

	/**
	 * Get the [flavor_params_version] column value.
	 * 
	 * @return     int
	 */
	public function getFlavorParamsVersion()
	{
		return $this->flavor_params_version;
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
	 * Get the [entry_id] column value.
	 * 
	 * @return     string
	 */
	public function getEntryId()
	{
		return $this->entry_id;
	}

	/**
	 * Get the [flavor_asset_id] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorAssetId()
	{
		return $this->flavor_asset_id;
	}

	/**
	 * Get the [flavor_asset_version] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorAssetVersion()
	{
		return $this->flavor_asset_version;
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
	 * Get the [command_lines] column value.
	 * 
	 * @return     string
	 */
	public function getCommandLines()
	{
		return $this->command_lines;
	}

	/**
	 * Get the [file_ext] column value.
	 * 
	 * @return     string
	 */
	public function getFileExt()
	{
		return $this->file_ext;
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
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::ID]))
			$this->oldColumnsValues[assetParamsOutputPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [flavor_params_id] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFlavorParamsId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_PARAMS_ID]))
			$this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_PARAMS_ID] = $this->flavor_params_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flavor_params_id !== $v) {
			$this->flavor_params_id = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FLAVOR_PARAMS_ID;
		}

		if ($this->aassetParams !== null && $this->aassetParams->getId() !== $v) {
			$this->aassetParams = null;
		}

		return $this;
	} // setFlavorParamsId()

	/**
	 * Set the value of [flavor_params_version] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFlavorParamsVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_PARAMS_VERSION]))
			$this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_PARAMS_VERSION] = $this->flavor_params_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flavor_params_version !== $v) {
			$this->flavor_params_version = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FLAVOR_PARAMS_VERSION;
		}

		return $this;
	} // setFlavorParamsVersion()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::PARTNER_ID]))
			$this->oldColumnsValues[assetParamsOutputPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::ENTRY_ID]))
			$this->oldColumnsValues[assetParamsOutputPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::ENTRY_ID;
		}

		if ($this->aentry !== null && $this->aentry->getId() !== $v) {
			$this->aentry = null;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [flavor_asset_id] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFlavorAssetId($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_ASSET_ID]))
			$this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_ASSET_ID] = $this->flavor_asset_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_asset_id !== $v) {
			$this->flavor_asset_id = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FLAVOR_ASSET_ID;
		}

		if ($this->aasset !== null && $this->aasset->getId() !== $v) {
			$this->aasset = null;
		}

		return $this;
	} // setFlavorAssetId()

	/**
	 * Set the value of [flavor_asset_version] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFlavorAssetVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_ASSET_VERSION]))
			$this->oldColumnsValues[assetParamsOutputPeer::FLAVOR_ASSET_VERSION] = $this->flavor_asset_version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_asset_version !== $v) {
			$this->flavor_asset_version = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FLAVOR_ASSET_VERSION;
		}

		return $this;
	} // setFlavorAssetVersion()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::NAME]))
			$this->oldColumnsValues[assetParamsOutputPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::TAGS]))
			$this->oldColumnsValues[assetParamsOutputPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::DESCRIPTION]))
			$this->oldColumnsValues[assetParamsOutputPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v || $this->isNew()) {
			$this->description = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [ready_behavior] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setReadyBehavior($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::READY_BEHAVIOR]))
			$this->oldColumnsValues[assetParamsOutputPeer::READY_BEHAVIOR] = $this->ready_behavior;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ready_behavior !== $v) {
			$this->ready_behavior = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::READY_BEHAVIOR;
		}

		return $this;
	} // setReadyBehavior()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParamsOutput The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetParamsOutputPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParamsOutput The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetParamsOutputPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::DELETED_AT]))
			$this->oldColumnsValues[assetParamsOutputPeer::DELETED_AT] = $this->deleted_at;

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
				$this->modifiedColumns[] = assetParamsOutputPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [is_default] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setIsDefault($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::IS_DEFAULT]))
			$this->oldColumnsValues[assetParamsOutputPeer::IS_DEFAULT] = $this->is_default;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->is_default !== $v || $this->isNew()) {
			$this->is_default = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::IS_DEFAULT;
		}

		return $this;
	} // setIsDefault()

	/**
	 * Set the value of [format] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFormat($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FORMAT]))
			$this->oldColumnsValues[assetParamsOutputPeer::FORMAT] = $this->format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->format !== $v) {
			$this->format = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FORMAT;
		}

		return $this;
	} // setFormat()

	/**
	 * Set the value of [video_codec] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setVideoCodec($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::VIDEO_CODEC]))
			$this->oldColumnsValues[assetParamsOutputPeer::VIDEO_CODEC] = $this->video_codec;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->video_codec !== $v) {
			$this->video_codec = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::VIDEO_CODEC;
		}

		return $this;
	} // setVideoCodec()

	/**
	 * Set the value of [video_bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setVideoBitrate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::VIDEO_BITRATE]))
			$this->oldColumnsValues[assetParamsOutputPeer::VIDEO_BITRATE] = $this->video_bitrate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_bitrate !== $v || $this->isNew()) {
			$this->video_bitrate = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::VIDEO_BITRATE;
		}

		return $this;
	} // setVideoBitrate()

	/**
	 * Set the value of [audio_codec] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setAudioCodec($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::AUDIO_CODEC]))
			$this->oldColumnsValues[assetParamsOutputPeer::AUDIO_CODEC] = $this->audio_codec;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->audio_codec !== $v) {
			$this->audio_codec = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::AUDIO_CODEC;
		}

		return $this;
	} // setAudioCodec()

	/**
	 * Set the value of [audio_bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setAudioBitrate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::AUDIO_BITRATE]))
			$this->oldColumnsValues[assetParamsOutputPeer::AUDIO_BITRATE] = $this->audio_bitrate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_bitrate !== $v) {
			$this->audio_bitrate = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::AUDIO_BITRATE;
		}

		return $this;
	} // setAudioBitrate()

	/**
	 * Set the value of [audio_channels] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setAudioChannels($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::AUDIO_CHANNELS]))
			$this->oldColumnsValues[assetParamsOutputPeer::AUDIO_CHANNELS] = $this->audio_channels;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_channels !== $v) {
			$this->audio_channels = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::AUDIO_CHANNELS;
		}

		return $this;
	} // setAudioChannels()

	/**
	 * Set the value of [audio_sample_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setAudioSampleRate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::AUDIO_SAMPLE_RATE]))
			$this->oldColumnsValues[assetParamsOutputPeer::AUDIO_SAMPLE_RATE] = $this->audio_sample_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_sample_rate !== $v) {
			$this->audio_sample_rate = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::AUDIO_SAMPLE_RATE;
		}

		return $this;
	} // setAudioSampleRate()

	/**
	 * Set the value of [audio_resolution] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setAudioResolution($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::AUDIO_RESOLUTION]))
			$this->oldColumnsValues[assetParamsOutputPeer::AUDIO_RESOLUTION] = $this->audio_resolution;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_resolution !== $v) {
			$this->audio_resolution = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::AUDIO_RESOLUTION;
		}

		return $this;
	} // setAudioResolution()

	/**
	 * Set the value of [width] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setWidth($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::WIDTH]))
			$this->oldColumnsValues[assetParamsOutputPeer::WIDTH] = $this->width;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->width !== $v || $this->isNew()) {
			$this->width = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::WIDTH;
		}

		return $this;
	} // setWidth()

	/**
	 * Set the value of [height] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setHeight($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::HEIGHT]))
			$this->oldColumnsValues[assetParamsOutputPeer::HEIGHT] = $this->height;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->height !== $v || $this->isNew()) {
			$this->height = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::HEIGHT;
		}

		return $this;
	} // setHeight()

	/**
	 * Set the value of [frame_rate] column.
	 * 
	 * @param      double $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFrameRate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FRAME_RATE]))
			$this->oldColumnsValues[assetParamsOutputPeer::FRAME_RATE] = $this->frame_rate;

		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->frame_rate !== $v) {
			$this->frame_rate = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FRAME_RATE;
		}

		return $this;
	} // setFrameRate()

	/**
	 * Set the value of [gop_size] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setGopSize($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::GOP_SIZE]))
			$this->oldColumnsValues[assetParamsOutputPeer::GOP_SIZE] = $this->gop_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->gop_size !== $v || $this->isNew()) {
			$this->gop_size = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::GOP_SIZE;
		}

		return $this;
	} // setGopSize()

	/**
	 * Set the value of [two_pass] column.
	 * 
	 * @param      boolean $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setTwoPass($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::TWO_PASS]))
			$this->oldColumnsValues[assetParamsOutputPeer::TWO_PASS] = $this->two_pass;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->two_pass !== $v || $this->isNew()) {
			$this->two_pass = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::TWO_PASS;
		}

		return $this;
	} // setTwoPass()

	/**
	 * Set the value of [conversion_engines] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setConversionEngines($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::CONVERSION_ENGINES]))
			$this->oldColumnsValues[assetParamsOutputPeer::CONVERSION_ENGINES] = $this->conversion_engines;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conversion_engines !== $v) {
			$this->conversion_engines = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::CONVERSION_ENGINES;
		}

		return $this;
	} // setConversionEngines()

	/**
	 * Set the value of [conversion_engines_extra_params] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setConversionEnginesExtraParams($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS]))
			$this->oldColumnsValues[assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS] = $this->conversion_engines_extra_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conversion_engines_extra_params !== $v) {
			$this->conversion_engines_extra_params = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS;
		}

		return $this;
	} // setConversionEnginesExtraParams()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [command_lines] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setCommandLines($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::COMMAND_LINES]))
			$this->oldColumnsValues[assetParamsOutputPeer::COMMAND_LINES] = $this->command_lines;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->command_lines !== $v) {
			$this->command_lines = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::COMMAND_LINES;
		}

		return $this;
	} // setCommandLines()

	/**
	 * Set the value of [file_ext] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setFileExt($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::FILE_EXT]))
			$this->oldColumnsValues[assetParamsOutputPeer::FILE_EXT] = $this->file_ext;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_ext !== $v) {
			$this->file_ext = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::FILE_EXT;
		}

		return $this;
	} // setFileExt()

	/**
	 * Set the value of [deinterlice] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setDeinterlice($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::DEINTERLICE]))
			$this->oldColumnsValues[assetParamsOutputPeer::DEINTERLICE] = $this->deinterlice;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->deinterlice !== $v) {
			$this->deinterlice = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::DEINTERLICE;
		}

		return $this;
	} // setDeinterlice()

	/**
	 * Set the value of [rotate] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setRotate($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::ROTATE]))
			$this->oldColumnsValues[assetParamsOutputPeer::ROTATE] = $this->rotate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rotate !== $v) {
			$this->rotate = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::ROTATE;
		}

		return $this;
	} // setRotate()

	/**
	 * Set the value of [operators] column.
	 * 
	 * @param      string $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setOperators($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::OPERATORS]))
			$this->oldColumnsValues[assetParamsOutputPeer::OPERATORS] = $this->operators;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->operators !== $v) {
			$this->operators = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::OPERATORS;
		}

		return $this;
	} // setOperators()

	/**
	 * Set the value of [engine_version] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setEngineVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::ENGINE_VERSION]))
			$this->oldColumnsValues[assetParamsOutputPeer::ENGINE_VERSION] = $this->engine_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->engine_version !== $v) {
			$this->engine_version = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::ENGINE_VERSION;
		}

		return $this;
	} // setEngineVersion()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     assetParamsOutput The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[assetParamsOutputPeer::TYPE]))
			$this->oldColumnsValues[assetParamsOutputPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = assetParamsOutputPeer::TYPE;
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

			if ($this->description !== '') {
				return false;
			}

			if ($this->is_default !== 0) {
				return false;
			}

			if ($this->video_bitrate !== 0) {
				return false;
			}

			if ($this->width !== 0) {
				return false;
			}

			if ($this->height !== 0) {
				return false;
			}

			if ($this->gop_size !== 0) {
				return false;
			}

			if ($this->two_pass !== false) {
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
		// Nullify cached objects
		$this->m_custom_data = null;
		
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->flavor_params_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->flavor_params_version = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->partner_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->entry_id = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->flavor_asset_id = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->flavor_asset_version = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->name = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->tags = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->description = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->ready_behavior = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->deleted_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->is_default = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->format = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->video_codec = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->video_bitrate = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->audio_codec = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->audio_bitrate = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->audio_channels = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->audio_sample_rate = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->audio_resolution = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->width = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->height = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->frame_rate = ($row[$startcol + 25] !== null) ? (double) $row[$startcol + 25] : null;
			$this->gop_size = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->two_pass = ($row[$startcol + 27] !== null) ? (boolean) $row[$startcol + 27] : null;
			$this->conversion_engines = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->conversion_engines_extra_params = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->custom_data = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
			$this->command_lines = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
			$this->file_ext = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
			$this->deinterlice = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->rotate = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->operators = ($row[$startcol + 35] !== null) ? (string) $row[$startcol + 35] : null;
			$this->engine_version = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->type = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 38; // 38 = assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating assetParamsOutput object", $e);
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

		if ($this->aassetParams !== null && $this->flavor_params_id !== $this->aassetParams->getId()) {
			$this->aassetParams = null;
		}
		if ($this->aentry !== null && $this->entry_id !== $this->aentry->getId()) {
			$this->aentry = null;
		}
		if ($this->aasset !== null && $this->flavor_asset_id !== $this->aasset->getId()) {
			$this->aasset = null;
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
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		assetParamsOutputPeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		assetParamsOutputPeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		assetParamsOutputPeer::setUseCriteriaFilter(true);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aassetParams = null;
			$this->aentry = null;
			$this->aasset = null;
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
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				assetParamsOutputPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                if ($affectedRows || !$this->isColumnModified(assetParamsOutputPeer::CUSTOM_DATA)) //ask if custom_data wasn't modified to avoid retry with atomic column 
                	break;

                KalturaLog::debug("was unable to save! retrying for the $retries time");
                $criteria = $this->buildPkeyCriteria();
				$criteria->addSelectColumn(assetParamsOutputPeer::CUSTOM_DATA);
                $stmt = BasePeer::doSelect($criteria, $con);
                $cutsomDataArr = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $newCustomData = $cutsomDataArr[0];
                
                $this->custom_data_md5 = md5($newCustomData);

                $valuesToChangeTo = $this->m_custom_data->toArray();
				$this->m_custom_data = myCustomData::fromString($newCustomData); 

				//set custom data column values we wanted to change to
				$validUpdate = true;
				$atomicCustomDataFields = assetParamsOutputPeer::getAtomicCustomDataFields();
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
			assetParamsOutputPeer::addInstanceToPool($this);
			
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

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aassetParams !== null) {
				if ($this->aassetParams->isModified() || $this->aassetParams->isNew()) {
					$affectedRows += $this->aassetParams->save($con);
				}
				$this->setassetParams($this->aassetParams);
			}

			if ($this->aentry !== null) {
				if ($this->aentry->isModified() || $this->aentry->isNew()) {
					$affectedRows += $this->aentry->save($con);
				}
				$this->setentry($this->aentry);
			}

			if ($this->aasset !== null) {
				if ($this->aasset->isModified() || $this->aasset->isNew()) {
					$affectedRows += $this->aasset->save($con);
				}
				$this->setasset($this->aasset);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = assetParamsOutputPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = assetParamsOutputPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = assetParamsOutputPeer::doUpdate($this, $con);
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aassetParams !== null) {
				if (!$this->aassetParams->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aassetParams->getValidationFailures());
				}
			}

			if ($this->aentry !== null) {
				if (!$this->aentry->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aentry->getValidationFailures());
				}
			}

			if ($this->aasset !== null) {
				if (!$this->aasset->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aasset->getValidationFailures());
				}
			}


			if (($retval = assetParamsOutputPeer::doValidate($this, $columns)) !== true) {
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
		$pos = assetParamsOutputPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFlavorParamsId();
				break;
			case 2:
				return $this->getFlavorParamsVersion();
				break;
			case 3:
				return $this->getPartnerId();
				break;
			case 4:
				return $this->getEntryId();
				break;
			case 5:
				return $this->getFlavorAssetId();
				break;
			case 6:
				return $this->getFlavorAssetVersion();
				break;
			case 7:
				return $this->getName();
				break;
			case 8:
				return $this->getTags();
				break;
			case 9:
				return $this->getDescription();
				break;
			case 10:
				return $this->getReadyBehavior();
				break;
			case 11:
				return $this->getCreatedAt();
				break;
			case 12:
				return $this->getUpdatedAt();
				break;
			case 13:
				return $this->getDeletedAt();
				break;
			case 14:
				return $this->getIsDefault();
				break;
			case 15:
				return $this->getFormat();
				break;
			case 16:
				return $this->getVideoCodec();
				break;
			case 17:
				return $this->getVideoBitrate();
				break;
			case 18:
				return $this->getAudioCodec();
				break;
			case 19:
				return $this->getAudioBitrate();
				break;
			case 20:
				return $this->getAudioChannels();
				break;
			case 21:
				return $this->getAudioSampleRate();
				break;
			case 22:
				return $this->getAudioResolution();
				break;
			case 23:
				return $this->getWidth();
				break;
			case 24:
				return $this->getHeight();
				break;
			case 25:
				return $this->getFrameRate();
				break;
			case 26:
				return $this->getGopSize();
				break;
			case 27:
				return $this->getTwoPass();
				break;
			case 28:
				return $this->getConversionEngines();
				break;
			case 29:
				return $this->getConversionEnginesExtraParams();
				break;
			case 30:
				return $this->getCustomData();
				break;
			case 31:
				return $this->getCommandLines();
				break;
			case 32:
				return $this->getFileExt();
				break;
			case 33:
				return $this->getDeinterlice();
				break;
			case 34:
				return $this->getRotate();
				break;
			case 35:
				return $this->getOperators();
				break;
			case 36:
				return $this->getEngineVersion();
				break;
			case 37:
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
		$keys = assetParamsOutputPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getFlavorParamsId(),
			$keys[2] => $this->getFlavorParamsVersion(),
			$keys[3] => $this->getPartnerId(),
			$keys[4] => $this->getEntryId(),
			$keys[5] => $this->getFlavorAssetId(),
			$keys[6] => $this->getFlavorAssetVersion(),
			$keys[7] => $this->getName(),
			$keys[8] => $this->getTags(),
			$keys[9] => $this->getDescription(),
			$keys[10] => $this->getReadyBehavior(),
			$keys[11] => $this->getCreatedAt(),
			$keys[12] => $this->getUpdatedAt(),
			$keys[13] => $this->getDeletedAt(),
			$keys[14] => $this->getIsDefault(),
			$keys[15] => $this->getFormat(),
			$keys[16] => $this->getVideoCodec(),
			$keys[17] => $this->getVideoBitrate(),
			$keys[18] => $this->getAudioCodec(),
			$keys[19] => $this->getAudioBitrate(),
			$keys[20] => $this->getAudioChannels(),
			$keys[21] => $this->getAudioSampleRate(),
			$keys[22] => $this->getAudioResolution(),
			$keys[23] => $this->getWidth(),
			$keys[24] => $this->getHeight(),
			$keys[25] => $this->getFrameRate(),
			$keys[26] => $this->getGopSize(),
			$keys[27] => $this->getTwoPass(),
			$keys[28] => $this->getConversionEngines(),
			$keys[29] => $this->getConversionEnginesExtraParams(),
			$keys[30] => $this->getCustomData(),
			$keys[31] => $this->getCommandLines(),
			$keys[32] => $this->getFileExt(),
			$keys[33] => $this->getDeinterlice(),
			$keys[34] => $this->getRotate(),
			$keys[35] => $this->getOperators(),
			$keys[36] => $this->getEngineVersion(),
			$keys[37] => $this->getType(),
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
		$pos = assetParamsOutputPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFlavorParamsId($value);
				break;
			case 2:
				$this->setFlavorParamsVersion($value);
				break;
			case 3:
				$this->setPartnerId($value);
				break;
			case 4:
				$this->setEntryId($value);
				break;
			case 5:
				$this->setFlavorAssetId($value);
				break;
			case 6:
				$this->setFlavorAssetVersion($value);
				break;
			case 7:
				$this->setName($value);
				break;
			case 8:
				$this->setTags($value);
				break;
			case 9:
				$this->setDescription($value);
				break;
			case 10:
				$this->setReadyBehavior($value);
				break;
			case 11:
				$this->setCreatedAt($value);
				break;
			case 12:
				$this->setUpdatedAt($value);
				break;
			case 13:
				$this->setDeletedAt($value);
				break;
			case 14:
				$this->setIsDefault($value);
				break;
			case 15:
				$this->setFormat($value);
				break;
			case 16:
				$this->setVideoCodec($value);
				break;
			case 17:
				$this->setVideoBitrate($value);
				break;
			case 18:
				$this->setAudioCodec($value);
				break;
			case 19:
				$this->setAudioBitrate($value);
				break;
			case 20:
				$this->setAudioChannels($value);
				break;
			case 21:
				$this->setAudioSampleRate($value);
				break;
			case 22:
				$this->setAudioResolution($value);
				break;
			case 23:
				$this->setWidth($value);
				break;
			case 24:
				$this->setHeight($value);
				break;
			case 25:
				$this->setFrameRate($value);
				break;
			case 26:
				$this->setGopSize($value);
				break;
			case 27:
				$this->setTwoPass($value);
				break;
			case 28:
				$this->setConversionEngines($value);
				break;
			case 29:
				$this->setConversionEnginesExtraParams($value);
				break;
			case 30:
				$this->setCustomData($value);
				break;
			case 31:
				$this->setCommandLines($value);
				break;
			case 32:
				$this->setFileExt($value);
				break;
			case 33:
				$this->setDeinterlice($value);
				break;
			case 34:
				$this->setRotate($value);
				break;
			case 35:
				$this->setOperators($value);
				break;
			case 36:
				$this->setEngineVersion($value);
				break;
			case 37:
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
		$keys = assetParamsOutputPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFlavorParamsId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setFlavorParamsVersion($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPartnerId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEntryId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFlavorAssetId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFlavorAssetVersion($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setTags($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDescription($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setReadyBehavior($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDeletedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setIsDefault($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setFormat($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setVideoCodec($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setVideoBitrate($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setAudioCodec($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setAudioBitrate($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setAudioChannels($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setAudioSampleRate($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setAudioResolution($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setWidth($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setHeight($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setFrameRate($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setGopSize($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setTwoPass($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setConversionEngines($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setConversionEnginesExtraParams($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setCustomData($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setCommandLines($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setFileExt($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setDeinterlice($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setRotate($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setOperators($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setEngineVersion($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setType($arr[$keys[37]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(assetParamsOutputPeer::DATABASE_NAME);

		if ($this->isColumnModified(assetParamsOutputPeer::ID)) $criteria->add(assetParamsOutputPeer::ID, $this->id);
		if ($this->isColumnModified(assetParamsOutputPeer::FLAVOR_PARAMS_ID)) $criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_ID, $this->flavor_params_id);
		if ($this->isColumnModified(assetParamsOutputPeer::FLAVOR_PARAMS_VERSION)) $criteria->add(assetParamsOutputPeer::FLAVOR_PARAMS_VERSION, $this->flavor_params_version);
		if ($this->isColumnModified(assetParamsOutputPeer::PARTNER_ID)) $criteria->add(assetParamsOutputPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(assetParamsOutputPeer::ENTRY_ID)) $criteria->add(assetParamsOutputPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(assetParamsOutputPeer::FLAVOR_ASSET_ID)) $criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->flavor_asset_id);
		if ($this->isColumnModified(assetParamsOutputPeer::FLAVOR_ASSET_VERSION)) $criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_VERSION, $this->flavor_asset_version);
		if ($this->isColumnModified(assetParamsOutputPeer::NAME)) $criteria->add(assetParamsOutputPeer::NAME, $this->name);
		if ($this->isColumnModified(assetParamsOutputPeer::TAGS)) $criteria->add(assetParamsOutputPeer::TAGS, $this->tags);
		if ($this->isColumnModified(assetParamsOutputPeer::DESCRIPTION)) $criteria->add(assetParamsOutputPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(assetParamsOutputPeer::READY_BEHAVIOR)) $criteria->add(assetParamsOutputPeer::READY_BEHAVIOR, $this->ready_behavior);
		if ($this->isColumnModified(assetParamsOutputPeer::CREATED_AT)) $criteria->add(assetParamsOutputPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(assetParamsOutputPeer::UPDATED_AT)) $criteria->add(assetParamsOutputPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(assetParamsOutputPeer::DELETED_AT)) $criteria->add(assetParamsOutputPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(assetParamsOutputPeer::IS_DEFAULT)) $criteria->add(assetParamsOutputPeer::IS_DEFAULT, $this->is_default);
		if ($this->isColumnModified(assetParamsOutputPeer::FORMAT)) $criteria->add(assetParamsOutputPeer::FORMAT, $this->format);
		if ($this->isColumnModified(assetParamsOutputPeer::VIDEO_CODEC)) $criteria->add(assetParamsOutputPeer::VIDEO_CODEC, $this->video_codec);
		if ($this->isColumnModified(assetParamsOutputPeer::VIDEO_BITRATE)) $criteria->add(assetParamsOutputPeer::VIDEO_BITRATE, $this->video_bitrate);
		if ($this->isColumnModified(assetParamsOutputPeer::AUDIO_CODEC)) $criteria->add(assetParamsOutputPeer::AUDIO_CODEC, $this->audio_codec);
		if ($this->isColumnModified(assetParamsOutputPeer::AUDIO_BITRATE)) $criteria->add(assetParamsOutputPeer::AUDIO_BITRATE, $this->audio_bitrate);
		if ($this->isColumnModified(assetParamsOutputPeer::AUDIO_CHANNELS)) $criteria->add(assetParamsOutputPeer::AUDIO_CHANNELS, $this->audio_channels);
		if ($this->isColumnModified(assetParamsOutputPeer::AUDIO_SAMPLE_RATE)) $criteria->add(assetParamsOutputPeer::AUDIO_SAMPLE_RATE, $this->audio_sample_rate);
		if ($this->isColumnModified(assetParamsOutputPeer::AUDIO_RESOLUTION)) $criteria->add(assetParamsOutputPeer::AUDIO_RESOLUTION, $this->audio_resolution);
		if ($this->isColumnModified(assetParamsOutputPeer::WIDTH)) $criteria->add(assetParamsOutputPeer::WIDTH, $this->width);
		if ($this->isColumnModified(assetParamsOutputPeer::HEIGHT)) $criteria->add(assetParamsOutputPeer::HEIGHT, $this->height);
		if ($this->isColumnModified(assetParamsOutputPeer::FRAME_RATE)) $criteria->add(assetParamsOutputPeer::FRAME_RATE, $this->frame_rate);
		if ($this->isColumnModified(assetParamsOutputPeer::GOP_SIZE)) $criteria->add(assetParamsOutputPeer::GOP_SIZE, $this->gop_size);
		if ($this->isColumnModified(assetParamsOutputPeer::TWO_PASS)) $criteria->add(assetParamsOutputPeer::TWO_PASS, $this->two_pass);
		if ($this->isColumnModified(assetParamsOutputPeer::CONVERSION_ENGINES)) $criteria->add(assetParamsOutputPeer::CONVERSION_ENGINES, $this->conversion_engines);
		if ($this->isColumnModified(assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS)) $criteria->add(assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS, $this->conversion_engines_extra_params);
		if ($this->isColumnModified(assetParamsOutputPeer::CUSTOM_DATA)) $criteria->add(assetParamsOutputPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(assetParamsOutputPeer::COMMAND_LINES)) $criteria->add(assetParamsOutputPeer::COMMAND_LINES, $this->command_lines);
		if ($this->isColumnModified(assetParamsOutputPeer::FILE_EXT)) $criteria->add(assetParamsOutputPeer::FILE_EXT, $this->file_ext);
		if ($this->isColumnModified(assetParamsOutputPeer::DEINTERLICE)) $criteria->add(assetParamsOutputPeer::DEINTERLICE, $this->deinterlice);
		if ($this->isColumnModified(assetParamsOutputPeer::ROTATE)) $criteria->add(assetParamsOutputPeer::ROTATE, $this->rotate);
		if ($this->isColumnModified(assetParamsOutputPeer::OPERATORS)) $criteria->add(assetParamsOutputPeer::OPERATORS, $this->operators);
		if ($this->isColumnModified(assetParamsOutputPeer::ENGINE_VERSION)) $criteria->add(assetParamsOutputPeer::ENGINE_VERSION, $this->engine_version);
		if ($this->isColumnModified(assetParamsOutputPeer::TYPE)) $criteria->add(assetParamsOutputPeer::TYPE, $this->type);

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
		$criteria = new Criteria(assetParamsOutputPeer::DATABASE_NAME);

		$criteria->add(assetParamsOutputPeer::ID, $this->id);
		
		if($this->alreadyInSave)
		{
			if ($this->isColumnModified(assetParamsOutputPeer::CUSTOM_DATA))
			{
				if (!is_null($this->custom_data_md5))
					$criteria->add(assetParamsOutputPeer::CUSTOM_DATA, "MD5(cast(" . assetParamsOutputPeer::CUSTOM_DATA . " as char character set latin1)) = '$this->custom_data_md5'", Criteria::CUSTOM);
					//casting to latin char set to avoid mysql and php md5 difference
				else 
					$criteria->add(assetParamsOutputPeer::CUSTOM_DATA, NULL, Criteria::ISNULL);
			}
			
			if (count($this->modifiedColumns) == 2 && $this->isColumnModified(assetParamsOutputPeer::UPDATED_AT))
			{
				$theModifiedColumn = null;
				foreach($this->modifiedColumns as $modifiedColumn)
					if($modifiedColumn != assetParamsOutputPeer::UPDATED_AT)
						$theModifiedColumn = $modifiedColumn;
						
				$atomicColumns = assetParamsOutputPeer::getAtomicColumns();
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
	 * @param      object $copyObj An object of assetParamsOutput (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFlavorParamsId($this->flavor_params_id);

		$copyObj->setFlavorParamsVersion($this->flavor_params_version);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setFlavorAssetId($this->flavor_asset_id);

		$copyObj->setFlavorAssetVersion($this->flavor_asset_version);

		$copyObj->setName($this->name);

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

		$copyObj->setCommandLines($this->command_lines);

		$copyObj->setFileExt($this->file_ext);

		$copyObj->setDeinterlice($this->deinterlice);

		$copyObj->setRotate($this->rotate);

		$copyObj->setOperators($this->operators);

		$copyObj->setEngineVersion($this->engine_version);

		$copyObj->setType($this->type);


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
	 * @return     assetParamsOutput Clone of current object.
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
	 * @var     assetParamsOutput Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      assetParamsOutput $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(assetParamsOutput $copiedFrom)
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
	 * @return     assetParamsOutputPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new assetParamsOutputPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a assetParams object.
	 *
	 * @param      assetParams $v
	 * @return     assetParamsOutput The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setassetParams(assetParams $v = null)
	{
		if ($v === null) {
			$this->setFlavorParamsId(NULL);
		} else {
			$this->setFlavorParamsId($v->getId());
		}

		$this->aassetParams = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the assetParams object, it will not be re-added.
		if ($v !== null) {
			$v->addassetParamsOutput($this);
		}

		return $this;
	}


	/**
	 * Get the associated assetParams object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     assetParams The associated assetParams object.
	 * @throws     PropelException
	 */
	public function getassetParams(PropelPDO $con = null)
	{
		if ($this->aassetParams === null && ($this->flavor_params_id !== null)) {
			$this->aassetParams = assetParamsPeer::retrieveByPk($this->flavor_params_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aassetParams->addassetParamsOutputs($this);
			 */
		}
		return $this->aassetParams;
	}

	/**
	 * Declares an association between this object and a entry object.
	 *
	 * @param      entry $v
	 * @return     assetParamsOutput The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setentry(entry $v = null)
	{
		if ($v === null) {
			$this->setEntryId(NULL);
		} else {
			$this->setEntryId($v->getId());
		}

		$this->aentry = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the entry object, it will not be re-added.
		if ($v !== null) {
			$v->addassetParamsOutput($this);
		}

		return $this;
	}


	/**
	 * Get the associated entry object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     entry The associated entry object.
	 * @throws     PropelException
	 */
	public function getentry(PropelPDO $con = null)
	{
		if ($this->aentry === null && (($this->entry_id !== "" && $this->entry_id !== null))) {
			$this->aentry = entryPeer::retrieveByPk($this->entry_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aentry->addassetParamsOutputs($this);
			 */
		}
		return $this->aentry;
	}

	/**
	 * Declares an association between this object and a asset object.
	 *
	 * @param      asset $v
	 * @return     assetParamsOutput The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setasset(asset $v = null)
	{
		if ($v === null) {
			$this->setFlavorAssetId(NULL);
		} else {
			$this->setFlavorAssetId($v->getId());
		}

		$this->aasset = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the asset object, it will not be re-added.
		if ($v !== null) {
			$v->addassetParamsOutput($this);
		}

		return $this;
	}


	/**
	 * Get the associated asset object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     asset The associated asset object.
	 * @throws     PropelException
	 */
	public function getasset(PropelPDO $con = null)
	{
		if ($this->aasset === null && (($this->flavor_asset_id !== "" && $this->flavor_asset_id !== null))) {
			$c = new Criteria(assetPeer::DATABASE_NAME);
			$c->add(assetPeer::ID, $this->flavor_asset_id);
			$this->aasset = assetPeer::doSelectOne($c, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aasset->addassetParamsOutputs($this);
			 */
		}
		return $this->aasset;
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

			$this->aassetParams = null;
			$this->aentry = null;
			$this->aasset = null;
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
	
} // BaseassetParamsOutput
