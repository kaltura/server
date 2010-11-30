<?php

/**
 * Base class that represents a row from the 'flavor_asset' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class Baseasset extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        assetPeer
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
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

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
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the flavor_params_id field.
	 * @var        int
	 */
	protected $flavor_params_id;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the version field.
	 * @var        string
	 */
	protected $version;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

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
	 * The value for the bitrate field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $bitrate;

	/**
	 * The value for the frame_rate field.
	 * Note: this column has a database default value of: 0
	 * @var        double
	 */
	protected $frame_rate;

	/**
	 * The value for the size field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $size;

	/**
	 * The value for the is_original field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $is_original;

	/**
	 * The value for the file_ext field.
	 * @var        string
	 */
	protected $file_ext;

	/**
	 * The value for the container_format field.
	 * @var        string
	 */
	protected $container_format;

	/**
	 * The value for the video_codec_id field.
	 * @var        string
	 */
	protected $video_codec_id;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * @var        entry
	 */
	protected $aentry;

	/**
	 * @var        assetParams
	 */
	protected $aassetParams;

	/**
	 * @var        array mediaInfo[] Collection to store aggregation of mediaInfo objects.
	 */
	protected $collmediaInfos;

	/**
	 * @var        Criteria The criteria used to select the current contents of collmediaInfos.
	 */
	private $lastmediaInfoCriteria = null;

	/**
	 * @var        array assetParamsOutput[] Collection to store aggregation of assetParamsOutput objects.
	 */
	protected $collassetParamsOutputs;

	/**
	 * @var        Criteria The criteria used to select the current contents of collassetParamsOutputs.
	 */
	private $lastassetParamsOutputCriteria = null;

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
		$this->width = 0;
		$this->height = 0;
		$this->bitrate = 0;
		$this->frame_rate = 0;
		$this->size = 0;
		$this->is_original = false;
		$this->type = 0;
	}

	/**
	 * Initializes internal state of Baseasset object.
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
	 * Get the [tags] column value.
	 * 
	 * @return     string
	 */
	public function getTags()
	{
		return $this->tags;
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
	 * Get the [entry_id] column value.
	 * 
	 * @return     string
	 */
	public function getEntryId()
	{
		return $this->entry_id;
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
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the [version] column value.
	 * 
	 * @return     string
	 */
	public function getVersion()
	{
		return $this->version;
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
	 * Get the [bitrate] column value.
	 * 
	 * @return     int
	 */
	public function getBitrate()
	{
		return $this->bitrate;
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
	 * Get the [size] column value.
	 * 
	 * @return     int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Get the [is_original] column value.
	 * 
	 * @return     boolean
	 */
	public function getIsOriginal()
	{
		return $this->is_original;
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
	 * Get the [container_format] column value.
	 * 
	 * @return     string
	 */
	public function getContainerFormat()
	{
		return $this->container_format;
	}

	/**
	 * Get the [video_codec_id] column value.
	 * 
	 * @return     string
	 */
	public function getVideoCodecId()
	{
		return $this->video_codec_id;
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
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::ID]))
			$this->oldColumnsValues[assetPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = assetPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [int_id] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setIntId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::INT_ID]))
			$this->oldColumnsValues[assetPeer::INT_ID] = $this->int_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->int_id !== $v) {
			$this->int_id = $v;
			$this->modifiedColumns[] = assetPeer::INT_ID;
		}

		return $this;
	} // setIntId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::PARTNER_ID]))
			$this->oldColumnsValues[assetPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = assetPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::TAGS]))
			$this->oldColumnsValues[assetPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = assetPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     asset The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     asset The current object (for fluent API support)
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
				$this->modifiedColumns[] = assetPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     asset The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::DELETED_AT]))
			$this->oldColumnsValues[assetPeer::DELETED_AT] = $this->deleted_at;

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
				$this->modifiedColumns[] = assetPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::ENTRY_ID]))
			$this->oldColumnsValues[assetPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = assetPeer::ENTRY_ID;
		}

		if ($this->aentry !== null && $this->aentry->getId() !== $v) {
			$this->aentry = null;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [flavor_params_id] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setFlavorParamsId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::FLAVOR_PARAMS_ID]))
			$this->oldColumnsValues[assetPeer::FLAVOR_PARAMS_ID] = $this->flavor_params_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flavor_params_id !== $v) {
			$this->flavor_params_id = $v;
			$this->modifiedColumns[] = assetPeer::FLAVOR_PARAMS_ID;
		}

		if ($this->aassetParams !== null && $this->aassetParams->getId() !== $v) {
			$this->aassetParams = null;
		}

		return $this;
	} // setFlavorParamsId()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::STATUS]))
			$this->oldColumnsValues[assetPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = assetPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::VERSION]))
			$this->oldColumnsValues[assetPeer::VERSION] = $this->version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = assetPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::DESCRIPTION]))
			$this->oldColumnsValues[assetPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = assetPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [width] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setWidth($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::WIDTH]))
			$this->oldColumnsValues[assetPeer::WIDTH] = $this->width;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->width !== $v || $this->isNew()) {
			$this->width = $v;
			$this->modifiedColumns[] = assetPeer::WIDTH;
		}

		return $this;
	} // setWidth()

	/**
	 * Set the value of [height] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setHeight($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::HEIGHT]))
			$this->oldColumnsValues[assetPeer::HEIGHT] = $this->height;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->height !== $v || $this->isNew()) {
			$this->height = $v;
			$this->modifiedColumns[] = assetPeer::HEIGHT;
		}

		return $this;
	} // setHeight()

	/**
	 * Set the value of [bitrate] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setBitrate($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::BITRATE]))
			$this->oldColumnsValues[assetPeer::BITRATE] = $this->bitrate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->bitrate !== $v || $this->isNew()) {
			$this->bitrate = $v;
			$this->modifiedColumns[] = assetPeer::BITRATE;
		}

		return $this;
	} // setBitrate()

	/**
	 * Set the value of [frame_rate] column.
	 * 
	 * @param      double $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setFrameRate($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::FRAME_RATE]))
			$this->oldColumnsValues[assetPeer::FRAME_RATE] = $this->frame_rate;

		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->frame_rate !== $v || $this->isNew()) {
			$this->frame_rate = $v;
			$this->modifiedColumns[] = assetPeer::FRAME_RATE;
		}

		return $this;
	} // setFrameRate()

	/**
	 * Set the value of [size] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setSize($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::SIZE]))
			$this->oldColumnsValues[assetPeer::SIZE] = $this->size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->size !== $v || $this->isNew()) {
			$this->size = $v;
			$this->modifiedColumns[] = assetPeer::SIZE;
		}

		return $this;
	} // setSize()

	/**
	 * Set the value of [is_original] column.
	 * 
	 * @param      boolean $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setIsOriginal($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::IS_ORIGINAL]))
			$this->oldColumnsValues[assetPeer::IS_ORIGINAL] = $this->is_original;

		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->is_original !== $v || $this->isNew()) {
			$this->is_original = $v;
			$this->modifiedColumns[] = assetPeer::IS_ORIGINAL;
		}

		return $this;
	} // setIsOriginal()

	/**
	 * Set the value of [file_ext] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setFileExt($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::FILE_EXT]))
			$this->oldColumnsValues[assetPeer::FILE_EXT] = $this->file_ext;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->file_ext !== $v) {
			$this->file_ext = $v;
			$this->modifiedColumns[] = assetPeer::FILE_EXT;
		}

		return $this;
	} // setFileExt()

	/**
	 * Set the value of [container_format] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setContainerFormat($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::CONTAINER_FORMAT]))
			$this->oldColumnsValues[assetPeer::CONTAINER_FORMAT] = $this->container_format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->container_format !== $v) {
			$this->container_format = $v;
			$this->modifiedColumns[] = assetPeer::CONTAINER_FORMAT;
		}

		return $this;
	} // setContainerFormat()

	/**
	 * Set the value of [video_codec_id] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setVideoCodecId($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::VIDEO_CODEC_ID]))
			$this->oldColumnsValues[assetPeer::VIDEO_CODEC_ID] = $this->video_codec_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->video_codec_id !== $v) {
			$this->video_codec_id = $v;
			$this->modifiedColumns[] = assetPeer::VIDEO_CODEC_ID;
		}

		return $this;
	} // setVideoCodecId()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[assetPeer::TYPE]))
			$this->oldColumnsValues[assetPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = assetPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = assetPeer::CUSTOM_DATA;
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
			if ($this->width !== 0) {
				return false;
			}

			if ($this->height !== 0) {
				return false;
			}

			if ($this->bitrate !== 0) {
				return false;
			}

			if ($this->frame_rate !== 0) {
				return false;
			}

			if ($this->size !== 0) {
				return false;
			}

			if ($this->is_original !== false) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->int_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->partner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->tags = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->created_at = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->updated_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->deleted_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->entry_id = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->flavor_params_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->status = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->version = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->description = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->width = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->height = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->bitrate = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->frame_rate = ($row[$startcol + 15] !== null) ? (double) $row[$startcol + 15] : null;
			$this->size = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->is_original = ($row[$startcol + 17] !== null) ? (boolean) $row[$startcol + 17] : null;
			$this->file_ext = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->container_format = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->video_codec_id = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->type = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->custom_data = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 23; // 23 = assetPeer::NUM_COLUMNS - assetPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating asset object", $e);
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

		if ($this->aentry !== null && $this->entry_id !== $this->aentry->getId()) {
			$this->aentry = null;
		}
		if ($this->aassetParams !== null && $this->flavor_params_id !== $this->aassetParams->getId()) {
			$this->aassetParams = null;
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
			$con = Propel::getConnection(assetPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = assetPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aentry = null;
			$this->aassetParams = null;
			$this->collmediaInfos = null;
			$this->lastmediaInfoCriteria = null;

			$this->collassetParamsOutputs = null;
			$this->lastassetParamsOutputCriteria = null;

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
			$con = Propel::getConnection(assetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				assetPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(assetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				assetPeer::addInstanceToPool($this);
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

			if ($this->aentry !== null) {
				if ($this->aentry->isModified() || $this->aentry->isNew()) {
					$affectedRows += $this->aentry->save($con);
				}
				$this->setentry($this->aentry);
			}

			if ($this->aassetParams !== null) {
				if ($this->aassetParams->isModified() || $this->aassetParams->isNew()) {
					$affectedRows += $this->aassetParams->save($con);
				}
				$this->setassetParams($this->aassetParams);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = assetPeer::INT_ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = assetPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setIntId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += assetPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collmediaInfos !== null) {
				foreach ($this->collmediaInfos as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collassetParamsOutputs !== null) {
				foreach ($this->collassetParamsOutputs as $referrerFK) {
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
		assetPeer::setUseCriteriaFilter(false);
		$this->reload();
		assetPeer::setUseCriteriaFilter(true);
		
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

			if ($this->aentry !== null) {
				if (!$this->aentry->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aentry->getValidationFailures());
				}
			}

			if ($this->aassetParams !== null) {
				if (!$this->aassetParams->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aassetParams->getValidationFailures());
				}
			}


			if (($retval = assetPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collmediaInfos !== null) {
					foreach ($this->collmediaInfos as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collassetParamsOutputs !== null) {
					foreach ($this->collassetParamsOutputs as $referrerFK) {
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
		$pos = assetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getTags();
				break;
			case 4:
				return $this->getCreatedAt();
				break;
			case 5:
				return $this->getUpdatedAt();
				break;
			case 6:
				return $this->getDeletedAt();
				break;
			case 7:
				return $this->getEntryId();
				break;
			case 8:
				return $this->getFlavorParamsId();
				break;
			case 9:
				return $this->getStatus();
				break;
			case 10:
				return $this->getVersion();
				break;
			case 11:
				return $this->getDescription();
				break;
			case 12:
				return $this->getWidth();
				break;
			case 13:
				return $this->getHeight();
				break;
			case 14:
				return $this->getBitrate();
				break;
			case 15:
				return $this->getFrameRate();
				break;
			case 16:
				return $this->getSize();
				break;
			case 17:
				return $this->getIsOriginal();
				break;
			case 18:
				return $this->getFileExt();
				break;
			case 19:
				return $this->getContainerFormat();
				break;
			case 20:
				return $this->getVideoCodecId();
				break;
			case 21:
				return $this->getType();
				break;
			case 22:
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
		$keys = assetPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getIntId(),
			$keys[2] => $this->getPartnerId(),
			$keys[3] => $this->getTags(),
			$keys[4] => $this->getCreatedAt(),
			$keys[5] => $this->getUpdatedAt(),
			$keys[6] => $this->getDeletedAt(),
			$keys[7] => $this->getEntryId(),
			$keys[8] => $this->getFlavorParamsId(),
			$keys[9] => $this->getStatus(),
			$keys[10] => $this->getVersion(),
			$keys[11] => $this->getDescription(),
			$keys[12] => $this->getWidth(),
			$keys[13] => $this->getHeight(),
			$keys[14] => $this->getBitrate(),
			$keys[15] => $this->getFrameRate(),
			$keys[16] => $this->getSize(),
			$keys[17] => $this->getIsOriginal(),
			$keys[18] => $this->getFileExt(),
			$keys[19] => $this->getContainerFormat(),
			$keys[20] => $this->getVideoCodecId(),
			$keys[21] => $this->getType(),
			$keys[22] => $this->getCustomData(),
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
		$pos = assetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setTags($value);
				break;
			case 4:
				$this->setCreatedAt($value);
				break;
			case 5:
				$this->setUpdatedAt($value);
				break;
			case 6:
				$this->setDeletedAt($value);
				break;
			case 7:
				$this->setEntryId($value);
				break;
			case 8:
				$this->setFlavorParamsId($value);
				break;
			case 9:
				$this->setStatus($value);
				break;
			case 10:
				$this->setVersion($value);
				break;
			case 11:
				$this->setDescription($value);
				break;
			case 12:
				$this->setWidth($value);
				break;
			case 13:
				$this->setHeight($value);
				break;
			case 14:
				$this->setBitrate($value);
				break;
			case 15:
				$this->setFrameRate($value);
				break;
			case 16:
				$this->setSize($value);
				break;
			case 17:
				$this->setIsOriginal($value);
				break;
			case 18:
				$this->setFileExt($value);
				break;
			case 19:
				$this->setContainerFormat($value);
				break;
			case 20:
				$this->setVideoCodecId($value);
				break;
			case 21:
				$this->setType($value);
				break;
			case 22:
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
		$keys = assetPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIntId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTags($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCreatedAt($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setUpdatedAt($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDeletedAt($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setEntryId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setFlavorParamsId($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setStatus($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setVersion($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDescription($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setWidth($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setHeight($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setBitrate($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setFrameRate($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setSize($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setIsOriginal($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setFileExt($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setContainerFormat($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setVideoCodecId($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setType($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setCustomData($arr[$keys[22]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(assetPeer::DATABASE_NAME);

		if ($this->isColumnModified(assetPeer::ID)) $criteria->add(assetPeer::ID, $this->id);
		if ($this->isColumnModified(assetPeer::INT_ID)) $criteria->add(assetPeer::INT_ID, $this->int_id);
		if ($this->isColumnModified(assetPeer::PARTNER_ID)) $criteria->add(assetPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(assetPeer::TAGS)) $criteria->add(assetPeer::TAGS, $this->tags);
		if ($this->isColumnModified(assetPeer::CREATED_AT)) $criteria->add(assetPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(assetPeer::UPDATED_AT)) $criteria->add(assetPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(assetPeer::DELETED_AT)) $criteria->add(assetPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(assetPeer::ENTRY_ID)) $criteria->add(assetPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(assetPeer::FLAVOR_PARAMS_ID)) $criteria->add(assetPeer::FLAVOR_PARAMS_ID, $this->flavor_params_id);
		if ($this->isColumnModified(assetPeer::STATUS)) $criteria->add(assetPeer::STATUS, $this->status);
		if ($this->isColumnModified(assetPeer::VERSION)) $criteria->add(assetPeer::VERSION, $this->version);
		if ($this->isColumnModified(assetPeer::DESCRIPTION)) $criteria->add(assetPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(assetPeer::WIDTH)) $criteria->add(assetPeer::WIDTH, $this->width);
		if ($this->isColumnModified(assetPeer::HEIGHT)) $criteria->add(assetPeer::HEIGHT, $this->height);
		if ($this->isColumnModified(assetPeer::BITRATE)) $criteria->add(assetPeer::BITRATE, $this->bitrate);
		if ($this->isColumnModified(assetPeer::FRAME_RATE)) $criteria->add(assetPeer::FRAME_RATE, $this->frame_rate);
		if ($this->isColumnModified(assetPeer::SIZE)) $criteria->add(assetPeer::SIZE, $this->size);
		if ($this->isColumnModified(assetPeer::IS_ORIGINAL)) $criteria->add(assetPeer::IS_ORIGINAL, $this->is_original);
		if ($this->isColumnModified(assetPeer::FILE_EXT)) $criteria->add(assetPeer::FILE_EXT, $this->file_ext);
		if ($this->isColumnModified(assetPeer::CONTAINER_FORMAT)) $criteria->add(assetPeer::CONTAINER_FORMAT, $this->container_format);
		if ($this->isColumnModified(assetPeer::VIDEO_CODEC_ID)) $criteria->add(assetPeer::VIDEO_CODEC_ID, $this->video_codec_id);
		if ($this->isColumnModified(assetPeer::TYPE)) $criteria->add(assetPeer::TYPE, $this->type);
		if ($this->isColumnModified(assetPeer::CUSTOM_DATA)) $criteria->add(assetPeer::CUSTOM_DATA, $this->custom_data);

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
		$criteria = new Criteria(assetPeer::DATABASE_NAME);

		$criteria->add(assetPeer::INT_ID, $this->int_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIntId();
	}

	/**
	 * Generic method to set the primary key (int_id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIntId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of asset (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setId($this->id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setTags($this->tags);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setDeletedAt($this->deleted_at);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setFlavorParamsId($this->flavor_params_id);

		$copyObj->setStatus($this->status);

		$copyObj->setVersion($this->version);

		$copyObj->setDescription($this->description);

		$copyObj->setWidth($this->width);

		$copyObj->setHeight($this->height);

		$copyObj->setBitrate($this->bitrate);

		$copyObj->setFrameRate($this->frame_rate);

		$copyObj->setSize($this->size);

		$copyObj->setIsOriginal($this->is_original);

		$copyObj->setFileExt($this->file_ext);

		$copyObj->setContainerFormat($this->container_format);

		$copyObj->setVideoCodecId($this->video_codec_id);

		$copyObj->setType($this->type);

		$copyObj->setCustomData($this->custom_data);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getmediaInfos() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addmediaInfo($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getassetParamsOutputs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addassetParamsOutput($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


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
	 * @return     asset Clone of current object.
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
	 * @var     asset Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      asset $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(asset $copiedFrom)
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
	 * @return     assetPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new assetPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a entry object.
	 *
	 * @param      entry $v
	 * @return     asset The current object (for fluent API support)
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
			$v->addasset($this);
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
			   $this->aentry->addassets($this);
			 */
		}
		return $this->aentry;
	}

	/**
	 * Declares an association between this object and a assetParams object.
	 *
	 * @param      assetParams $v
	 * @return     asset The current object (for fluent API support)
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
			$v->addasset($this);
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
			   $this->aassetParams->addassets($this);
			 */
		}
		return $this->aassetParams;
	}

	/**
	 * Clears out the collmediaInfos collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addmediaInfos()
	 */
	public function clearmediaInfos()
	{
		$this->collmediaInfos = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collmediaInfos collection (array).
	 *
	 * By default this just sets the collmediaInfos collection to an empty array (like clearcollmediaInfos());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initmediaInfos()
	{
		$this->collmediaInfos = array();
	}

	/**
	 * Gets an array of mediaInfo objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this asset has previously been saved, it will retrieve
	 * related mediaInfos from storage. If this asset is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array mediaInfo[]
	 * @throws     PropelException
	 */
	public function getmediaInfos($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmediaInfos === null) {
			if ($this->isNew()) {
			   $this->collmediaInfos = array();
			} else {

				$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $this->id);

				mediaInfoPeer::addSelectColumns($criteria);
				$this->collmediaInfos = mediaInfoPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $this->id);

				mediaInfoPeer::addSelectColumns($criteria);
				if (!isset($this->lastmediaInfoCriteria) || !$this->lastmediaInfoCriteria->equals($criteria)) {
					$this->collmediaInfos = mediaInfoPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastmediaInfoCriteria = $criteria;
		return $this->collmediaInfos;
	}

	/**
	 * Returns the number of related mediaInfo objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related mediaInfo objects.
	 * @throws     PropelException
	 */
	public function countmediaInfos(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collmediaInfos === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $this->id);

				$count = mediaInfoPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $this->id);

				if (!isset($this->lastmediaInfoCriteria) || !$this->lastmediaInfoCriteria->equals($criteria)) {
					$count = mediaInfoPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collmediaInfos);
				}
			} else {
				$count = count($this->collmediaInfos);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a mediaInfo object to this object
	 * through the mediaInfo foreign key attribute.
	 *
	 * @param      mediaInfo $l mediaInfo
	 * @return     void
	 * @throws     PropelException
	 */
	public function addmediaInfo(mediaInfo $l)
	{
		if ($this->collmediaInfos === null) {
			$this->initmediaInfos();
		}
		if (!in_array($l, $this->collmediaInfos, true)) { // only add it if the **same** object is not already associated
			array_push($this->collmediaInfos, $l);
			$l->setasset($this);
		}
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
	 * Otherwise if this asset has previously been saved, it will retrieve
	 * related assetParamsOutputs from storage. If this asset is new, it will return
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
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
			   $this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				assetParamsOutputPeer::addSelectColumns($criteria);
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

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
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
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

				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				$count = assetParamsOutputPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

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
			$l->setasset($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this asset is new, it will return
	 * an empty collection; or if this asset has previously
	 * been saved, it will retrieve related assetParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in asset.
	 */
	public function getassetParamsOutputsJoinassetParams($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinassetParams($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

			if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinassetParams($criteria, $con, $join_behavior);
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;

		return $this->collassetParamsOutputs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this asset is new, it will return
	 * an empty collection; or if this asset has previously
	 * been saved, it will retrieve related assetParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in asset.
	 */
	public function getassetParamsOutputsJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collassetParamsOutputs = array();
			} else {

				$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

			if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
				$this->collassetParamsOutputs = assetParamsOutputPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;

		return $this->collassetParamsOutputs;
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
			if ($this->collmediaInfos) {
				foreach ((array) $this->collmediaInfos as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collassetParamsOutputs) {
				foreach ((array) $this->collassetParamsOutputs as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collmediaInfos = null;
		$this->collassetParamsOutputs = null;
			$this->aentry = null;
			$this->aassetParams = null;
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
	
} // Baseasset
