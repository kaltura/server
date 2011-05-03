<?php

/**
 * Base class that represents a row from the 'media_info' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasemediaInfo extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        mediaInfoPeer
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
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the flavor_asset_id field.
	 * @var        string
	 */
	protected $flavor_asset_id;

	/**
	 * The value for the file_size field.
	 * @var        int
	 */
	protected $file_size;

	/**
	 * The value for the container_format field.
	 * @var        string
	 */
	protected $container_format;

	/**
	 * The value for the container_id field.
	 * @var        string
	 */
	protected $container_id;

	/**
	 * The value for the container_profile field.
	 * @var        string
	 */
	protected $container_profile;

	/**
	 * The value for the container_duration field.
	 * @var        int
	 */
	protected $container_duration;

	/**
	 * The value for the container_bit_rate field.
	 * @var        int
	 */
	protected $container_bit_rate;

	/**
	 * The value for the video_format field.
	 * @var        string
	 */
	protected $video_format;

	/**
	 * The value for the video_codec_id field.
	 * @var        string
	 */
	protected $video_codec_id;

	/**
	 * The value for the video_duration field.
	 * @var        int
	 */
	protected $video_duration;

	/**
	 * The value for the video_bit_rate field.
	 * @var        int
	 */
	protected $video_bit_rate;

	/**
	 * The value for the video_bit_rate_mode field.
	 * @var        int
	 */
	protected $video_bit_rate_mode;

	/**
	 * The value for the video_width field.
	 * @var        int
	 */
	protected $video_width;

	/**
	 * The value for the video_height field.
	 * @var        int
	 */
	protected $video_height;

	/**
	 * The value for the video_frame_rate field.
	 * @var        double
	 */
	protected $video_frame_rate;

	/**
	 * The value for the video_dar field.
	 * @var        double
	 */
	protected $video_dar;

	/**
	 * The value for the video_rotation field.
	 * @var        int
	 */
	protected $video_rotation;

	/**
	 * The value for the audio_format field.
	 * @var        string
	 */
	protected $audio_format;

	/**
	 * The value for the audio_codec_id field.
	 * @var        string
	 */
	protected $audio_codec_id;

	/**
	 * The value for the audio_duration field.
	 * @var        int
	 */
	protected $audio_duration;

	/**
	 * The value for the audio_bit_rate field.
	 * @var        int
	 */
	protected $audio_bit_rate;

	/**
	 * The value for the audio_bit_rate_mode field.
	 * @var        int
	 */
	protected $audio_bit_rate_mode;

	/**
	 * The value for the audio_channels field.
	 * @var        int
	 */
	protected $audio_channels;

	/**
	 * The value for the audio_sampling_rate field.
	 * @var        int
	 */
	protected $audio_sampling_rate;

	/**
	 * The value for the audio_resolution field.
	 * @var        int
	 */
	protected $audio_resolution;

	/**
	 * The value for the writing_lib field.
	 * @var        string
	 */
	protected $writing_lib;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the raw_data field.
	 * @var        string
	 */
	protected $raw_data;

	/**
	 * The value for the multi_stream_info field.
	 * @var        string
	 */
	protected $multi_stream_info;

	/**
	 * The value for the flavor_asset_version field.
	 * @var        string
	 */
	protected $flavor_asset_version;

	/**
	 * The value for the scan_type field.
	 * @var        int
	 */
	protected $scan_type;

	/**
	 * The value for the multi_stream field.
	 * @var        string
	 */
	protected $multi_stream;

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
	 * Get the [flavor_asset_id] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorAssetId()
	{
		return $this->flavor_asset_id;
	}

	/**
	 * Get the [file_size] column value.
	 * 
	 * @return     int
	 */
	public function getFileSize()
	{
		return $this->file_size;
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
	 * Get the [container_id] column value.
	 * 
	 * @return     string
	 */
	public function getContainerId()
	{
		return $this->container_id;
	}

	/**
	 * Get the [container_profile] column value.
	 * 
	 * @return     string
	 */
	public function getContainerProfile()
	{
		return $this->container_profile;
	}

	/**
	 * Get the [container_duration] column value.
	 * 
	 * @return     int
	 */
	public function getContainerDuration()
	{
		return $this->container_duration;
	}

	/**
	 * Get the [container_bit_rate] column value.
	 * 
	 * @return     int
	 */
	public function getContainerBitRate()
	{
		return $this->container_bit_rate;
	}

	/**
	 * Get the [video_format] column value.
	 * 
	 * @return     string
	 */
	public function getVideoFormat()
	{
		return $this->video_format;
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
	 * Get the [video_duration] column value.
	 * 
	 * @return     int
	 */
	public function getVideoDuration()
	{
		return $this->video_duration;
	}

	/**
	 * Get the [video_bit_rate] column value.
	 * 
	 * @return     int
	 */
	public function getVideoBitRate()
	{
		return $this->video_bit_rate;
	}

	/**
	 * Get the [video_bit_rate_mode] column value.
	 * 
	 * @return     int
	 */
	public function getVideoBitRateMode()
	{
		return $this->video_bit_rate_mode;
	}

	/**
	 * Get the [video_width] column value.
	 * 
	 * @return     int
	 */
	public function getVideoWidth()
	{
		return $this->video_width;
	}

	/**
	 * Get the [video_height] column value.
	 * 
	 * @return     int
	 */
	public function getVideoHeight()
	{
		return $this->video_height;
	}

	/**
	 * Get the [video_frame_rate] column value.
	 * 
	 * @return     double
	 */
	public function getVideoFrameRate()
	{
		return $this->video_frame_rate;
	}

	/**
	 * Get the [video_dar] column value.
	 * 
	 * @return     double
	 */
	public function getVideoDar()
	{
		return $this->video_dar;
	}

	/**
	 * Get the [video_rotation] column value.
	 * 
	 * @return     int
	 */
	public function getVideoRotation()
	{
		return $this->video_rotation;
	}

	/**
	 * Get the [audio_format] column value.
	 * 
	 * @return     string
	 */
	public function getAudioFormat()
	{
		return $this->audio_format;
	}

	/**
	 * Get the [audio_codec_id] column value.
	 * 
	 * @return     string
	 */
	public function getAudioCodecId()
	{
		return $this->audio_codec_id;
	}

	/**
	 * Get the [audio_duration] column value.
	 * 
	 * @return     int
	 */
	public function getAudioDuration()
	{
		return $this->audio_duration;
	}

	/**
	 * Get the [audio_bit_rate] column value.
	 * 
	 * @return     int
	 */
	public function getAudioBitRate()
	{
		return $this->audio_bit_rate;
	}

	/**
	 * Get the [audio_bit_rate_mode] column value.
	 * 
	 * @return     int
	 */
	public function getAudioBitRateMode()
	{
		return $this->audio_bit_rate_mode;
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
	 * Get the [audio_sampling_rate] column value.
	 * 
	 * @return     int
	 */
	public function getAudioSamplingRate()
	{
		return $this->audio_sampling_rate;
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
	 * Get the [writing_lib] column value.
	 * 
	 * @return     string
	 */
	public function getWritingLib()
	{
		return $this->writing_lib;
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
	 * Get the [raw_data] column value.
	 * 
	 * @return     string
	 */
	public function getRawData()
	{
		return $this->raw_data;
	}

	/**
	 * Get the [multi_stream_info] column value.
	 * 
	 * @return     string
	 */
	public function getMultiStreamInfo()
	{
		return $this->multi_stream_info;
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
	 * Get the [scan_type] column value.
	 * 
	 * @return     int
	 */
	public function getScanType()
	{
		return $this->scan_type;
	}

	/**
	 * Get the [multi_stream] column value.
	 * 
	 * @return     string
	 */
	public function getMultiStream()
	{
		return $this->multi_stream;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::ID]))
			$this->oldColumnsValues[mediaInfoPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = mediaInfoPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     mediaInfo The current object (for fluent API support)
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
				$this->modifiedColumns[] = mediaInfoPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     mediaInfo The current object (for fluent API support)
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
				$this->modifiedColumns[] = mediaInfoPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [flavor_asset_id] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setFlavorAssetId($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::FLAVOR_ASSET_ID]))
			$this->oldColumnsValues[mediaInfoPeer::FLAVOR_ASSET_ID] = $this->flavor_asset_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_asset_id !== $v) {
			$this->flavor_asset_id = $v;
			$this->modifiedColumns[] = mediaInfoPeer::FLAVOR_ASSET_ID;
		}

		if ($this->aasset !== null && $this->aasset->getId() !== $v) {
			$this->aasset = null;
		}

		return $this;
	} // setFlavorAssetId()

	/**
	 * Set the value of [file_size] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setFileSize($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::FILE_SIZE]))
			$this->oldColumnsValues[mediaInfoPeer::FILE_SIZE] = $this->file_size;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_size !== $v) {
			$this->file_size = $v;
			$this->modifiedColumns[] = mediaInfoPeer::FILE_SIZE;
		}

		return $this;
	} // setFileSize()

	/**
	 * Set the value of [container_format] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setContainerFormat($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::CONTAINER_FORMAT]))
			$this->oldColumnsValues[mediaInfoPeer::CONTAINER_FORMAT] = $this->container_format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->container_format !== $v) {
			$this->container_format = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CONTAINER_FORMAT;
		}

		return $this;
	} // setContainerFormat()

	/**
	 * Set the value of [container_id] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setContainerId($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::CONTAINER_ID]))
			$this->oldColumnsValues[mediaInfoPeer::CONTAINER_ID] = $this->container_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->container_id !== $v) {
			$this->container_id = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CONTAINER_ID;
		}

		return $this;
	} // setContainerId()

	/**
	 * Set the value of [container_profile] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setContainerProfile($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::CONTAINER_PROFILE]))
			$this->oldColumnsValues[mediaInfoPeer::CONTAINER_PROFILE] = $this->container_profile;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->container_profile !== $v) {
			$this->container_profile = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CONTAINER_PROFILE;
		}

		return $this;
	} // setContainerProfile()

	/**
	 * Set the value of [container_duration] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setContainerDuration($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::CONTAINER_DURATION]))
			$this->oldColumnsValues[mediaInfoPeer::CONTAINER_DURATION] = $this->container_duration;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->container_duration !== $v) {
			$this->container_duration = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CONTAINER_DURATION;
		}

		return $this;
	} // setContainerDuration()

	/**
	 * Set the value of [container_bit_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setContainerBitRate($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::CONTAINER_BIT_RATE]))
			$this->oldColumnsValues[mediaInfoPeer::CONTAINER_BIT_RATE] = $this->container_bit_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->container_bit_rate !== $v) {
			$this->container_bit_rate = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CONTAINER_BIT_RATE;
		}

		return $this;
	} // setContainerBitRate()

	/**
	 * Set the value of [video_format] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoFormat($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_FORMAT]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_FORMAT] = $this->video_format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->video_format !== $v) {
			$this->video_format = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_FORMAT;
		}

		return $this;
	} // setVideoFormat()

	/**
	 * Set the value of [video_codec_id] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoCodecId($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_CODEC_ID]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_CODEC_ID] = $this->video_codec_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->video_codec_id !== $v) {
			$this->video_codec_id = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_CODEC_ID;
		}

		return $this;
	} // setVideoCodecId()

	/**
	 * Set the value of [video_duration] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoDuration($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_DURATION]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_DURATION] = $this->video_duration;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_duration !== $v) {
			$this->video_duration = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_DURATION;
		}

		return $this;
	} // setVideoDuration()

	/**
	 * Set the value of [video_bit_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoBitRate($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_BIT_RATE]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_BIT_RATE] = $this->video_bit_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_bit_rate !== $v) {
			$this->video_bit_rate = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_BIT_RATE;
		}

		return $this;
	} // setVideoBitRate()

	/**
	 * Set the value of [video_bit_rate_mode] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoBitRateMode($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_BIT_RATE_MODE]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_BIT_RATE_MODE] = $this->video_bit_rate_mode;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_bit_rate_mode !== $v) {
			$this->video_bit_rate_mode = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_BIT_RATE_MODE;
		}

		return $this;
	} // setVideoBitRateMode()

	/**
	 * Set the value of [video_width] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoWidth($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_WIDTH]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_WIDTH] = $this->video_width;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_width !== $v) {
			$this->video_width = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_WIDTH;
		}

		return $this;
	} // setVideoWidth()

	/**
	 * Set the value of [video_height] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoHeight($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_HEIGHT]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_HEIGHT] = $this->video_height;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_height !== $v) {
			$this->video_height = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_HEIGHT;
		}

		return $this;
	} // setVideoHeight()

	/**
	 * Set the value of [video_frame_rate] column.
	 * 
	 * @param      double $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoFrameRate($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_FRAME_RATE]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_FRAME_RATE] = $this->video_frame_rate;

		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->video_frame_rate !== $v) {
			$this->video_frame_rate = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_FRAME_RATE;
		}

		return $this;
	} // setVideoFrameRate()

	/**
	 * Set the value of [video_dar] column.
	 * 
	 * @param      double $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoDar($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_DAR]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_DAR] = $this->video_dar;

		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->video_dar !== $v) {
			$this->video_dar = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_DAR;
		}

		return $this;
	} // setVideoDar()

	/**
	 * Set the value of [video_rotation] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setVideoRotation($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::VIDEO_ROTATION]))
			$this->oldColumnsValues[mediaInfoPeer::VIDEO_ROTATION] = $this->video_rotation;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->video_rotation !== $v) {
			$this->video_rotation = $v;
			$this->modifiedColumns[] = mediaInfoPeer::VIDEO_ROTATION;
		}

		return $this;
	} // setVideoRotation()

	/**
	 * Set the value of [audio_format] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioFormat($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_FORMAT]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_FORMAT] = $this->audio_format;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->audio_format !== $v) {
			$this->audio_format = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_FORMAT;
		}

		return $this;
	} // setAudioFormat()

	/**
	 * Set the value of [audio_codec_id] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioCodecId($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_CODEC_ID]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_CODEC_ID] = $this->audio_codec_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->audio_codec_id !== $v) {
			$this->audio_codec_id = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_CODEC_ID;
		}

		return $this;
	} // setAudioCodecId()

	/**
	 * Set the value of [audio_duration] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioDuration($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_DURATION]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_DURATION] = $this->audio_duration;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_duration !== $v) {
			$this->audio_duration = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_DURATION;
		}

		return $this;
	} // setAudioDuration()

	/**
	 * Set the value of [audio_bit_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioBitRate($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_BIT_RATE]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_BIT_RATE] = $this->audio_bit_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_bit_rate !== $v) {
			$this->audio_bit_rate = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_BIT_RATE;
		}

		return $this;
	} // setAudioBitRate()

	/**
	 * Set the value of [audio_bit_rate_mode] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioBitRateMode($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_BIT_RATE_MODE]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_BIT_RATE_MODE] = $this->audio_bit_rate_mode;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_bit_rate_mode !== $v) {
			$this->audio_bit_rate_mode = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_BIT_RATE_MODE;
		}

		return $this;
	} // setAudioBitRateMode()

	/**
	 * Set the value of [audio_channels] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioChannels($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_CHANNELS]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_CHANNELS] = $this->audio_channels;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_channels !== $v) {
			$this->audio_channels = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_CHANNELS;
		}

		return $this;
	} // setAudioChannels()

	/**
	 * Set the value of [audio_sampling_rate] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioSamplingRate($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_SAMPLING_RATE]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_SAMPLING_RATE] = $this->audio_sampling_rate;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_sampling_rate !== $v) {
			$this->audio_sampling_rate = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_SAMPLING_RATE;
		}

		return $this;
	} // setAudioSamplingRate()

	/**
	 * Set the value of [audio_resolution] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setAudioResolution($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::AUDIO_RESOLUTION]))
			$this->oldColumnsValues[mediaInfoPeer::AUDIO_RESOLUTION] = $this->audio_resolution;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->audio_resolution !== $v) {
			$this->audio_resolution = $v;
			$this->modifiedColumns[] = mediaInfoPeer::AUDIO_RESOLUTION;
		}

		return $this;
	} // setAudioResolution()

	/**
	 * Set the value of [writing_lib] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setWritingLib($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::WRITING_LIB]))
			$this->oldColumnsValues[mediaInfoPeer::WRITING_LIB] = $this->writing_lib;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->writing_lib !== $v) {
			$this->writing_lib = $v;
			$this->modifiedColumns[] = mediaInfoPeer::WRITING_LIB;
		}

		return $this;
	} // setWritingLib()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = mediaInfoPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [raw_data] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setRawData($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::RAW_DATA]))
			$this->oldColumnsValues[mediaInfoPeer::RAW_DATA] = $this->raw_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->raw_data !== $v) {
			$this->raw_data = $v;
			$this->modifiedColumns[] = mediaInfoPeer::RAW_DATA;
		}

		return $this;
	} // setRawData()

	/**
	 * Set the value of [multi_stream_info] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setMultiStreamInfo($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::MULTI_STREAM_INFO]))
			$this->oldColumnsValues[mediaInfoPeer::MULTI_STREAM_INFO] = $this->multi_stream_info;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->multi_stream_info !== $v) {
			$this->multi_stream_info = $v;
			$this->modifiedColumns[] = mediaInfoPeer::MULTI_STREAM_INFO;
		}

		return $this;
	} // setMultiStreamInfo()

	/**
	 * Set the value of [flavor_asset_version] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setFlavorAssetVersion($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::FLAVOR_ASSET_VERSION]))
			$this->oldColumnsValues[mediaInfoPeer::FLAVOR_ASSET_VERSION] = $this->flavor_asset_version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_asset_version !== $v) {
			$this->flavor_asset_version = $v;
			$this->modifiedColumns[] = mediaInfoPeer::FLAVOR_ASSET_VERSION;
		}

		return $this;
	} // setFlavorAssetVersion()

	/**
	 * Set the value of [scan_type] column.
	 * 
	 * @param      int $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setScanType($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::SCAN_TYPE]))
			$this->oldColumnsValues[mediaInfoPeer::SCAN_TYPE] = $this->scan_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scan_type !== $v) {
			$this->scan_type = $v;
			$this->modifiedColumns[] = mediaInfoPeer::SCAN_TYPE;
		}

		return $this;
	} // setScanType()

	/**
	 * Set the value of [multi_stream] column.
	 * 
	 * @param      string $v new value
	 * @return     mediaInfo The current object (for fluent API support)
	 */
	public function setMultiStream($v)
	{
		if(!isset($this->oldColumnsValues[mediaInfoPeer::MULTI_STREAM]))
			$this->oldColumnsValues[mediaInfoPeer::MULTI_STREAM] = $this->multi_stream;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->multi_stream !== $v) {
			$this->multi_stream = $v;
			$this->modifiedColumns[] = mediaInfoPeer::MULTI_STREAM;
		}

		return $this;
	} // setMultiStream()

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
			$this->updated_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->flavor_asset_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->file_size = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->container_format = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->container_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->container_profile = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->container_duration = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->container_bit_rate = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->video_format = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->video_codec_id = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->video_duration = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->video_bit_rate = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->video_bit_rate_mode = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->video_width = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->video_height = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->video_frame_rate = ($row[$startcol + 17] !== null) ? (double) $row[$startcol + 17] : null;
			$this->video_dar = ($row[$startcol + 18] !== null) ? (double) $row[$startcol + 18] : null;
			$this->video_rotation = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->audio_format = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->audio_codec_id = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->audio_duration = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->audio_bit_rate = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->audio_bit_rate_mode = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->audio_channels = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->audio_sampling_rate = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->audio_resolution = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->writing_lib = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->custom_data = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->raw_data = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
			$this->multi_stream_info = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
			$this->flavor_asset_version = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
			$this->scan_type = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->multi_stream = ($row[$startcol + 34] !== null) ? (string) $row[$startcol + 34] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 35; // 35 = mediaInfoPeer::NUM_COLUMNS - mediaInfoPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating mediaInfo object", $e);
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
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = mediaInfoPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

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
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				mediaInfoPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				mediaInfoPeer::addInstanceToPool($this);
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

			if ($this->aasset !== null) {
				if ($this->aasset->isModified() || $this->aasset->isNew()) {
					$affectedRows += $this->aasset->save($con);
				}
				$this->setasset($this->aasset);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = mediaInfoPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = mediaInfoPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += mediaInfoPeer::doUpdate($this, $con);
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aasset !== null) {
				if (!$this->aasset->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aasset->getValidationFailures());
				}
			}


			if (($retval = mediaInfoPeer::doValidate($this, $columns)) !== true) {
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
		$pos = mediaInfoPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getUpdatedAt();
				break;
			case 3:
				return $this->getFlavorAssetId();
				break;
			case 4:
				return $this->getFileSize();
				break;
			case 5:
				return $this->getContainerFormat();
				break;
			case 6:
				return $this->getContainerId();
				break;
			case 7:
				return $this->getContainerProfile();
				break;
			case 8:
				return $this->getContainerDuration();
				break;
			case 9:
				return $this->getContainerBitRate();
				break;
			case 10:
				return $this->getVideoFormat();
				break;
			case 11:
				return $this->getVideoCodecId();
				break;
			case 12:
				return $this->getVideoDuration();
				break;
			case 13:
				return $this->getVideoBitRate();
				break;
			case 14:
				return $this->getVideoBitRateMode();
				break;
			case 15:
				return $this->getVideoWidth();
				break;
			case 16:
				return $this->getVideoHeight();
				break;
			case 17:
				return $this->getVideoFrameRate();
				break;
			case 18:
				return $this->getVideoDar();
				break;
			case 19:
				return $this->getVideoRotation();
				break;
			case 20:
				return $this->getAudioFormat();
				break;
			case 21:
				return $this->getAudioCodecId();
				break;
			case 22:
				return $this->getAudioDuration();
				break;
			case 23:
				return $this->getAudioBitRate();
				break;
			case 24:
				return $this->getAudioBitRateMode();
				break;
			case 25:
				return $this->getAudioChannels();
				break;
			case 26:
				return $this->getAudioSamplingRate();
				break;
			case 27:
				return $this->getAudioResolution();
				break;
			case 28:
				return $this->getWritingLib();
				break;
			case 29:
				return $this->getCustomData();
				break;
			case 30:
				return $this->getRawData();
				break;
			case 31:
				return $this->getMultiStreamInfo();
				break;
			case 32:
				return $this->getFlavorAssetVersion();
				break;
			case 33:
				return $this->getScanType();
				break;
			case 34:
				return $this->getMultiStream();
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
		$keys = mediaInfoPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getUpdatedAt(),
			$keys[3] => $this->getFlavorAssetId(),
			$keys[4] => $this->getFileSize(),
			$keys[5] => $this->getContainerFormat(),
			$keys[6] => $this->getContainerId(),
			$keys[7] => $this->getContainerProfile(),
			$keys[8] => $this->getContainerDuration(),
			$keys[9] => $this->getContainerBitRate(),
			$keys[10] => $this->getVideoFormat(),
			$keys[11] => $this->getVideoCodecId(),
			$keys[12] => $this->getVideoDuration(),
			$keys[13] => $this->getVideoBitRate(),
			$keys[14] => $this->getVideoBitRateMode(),
			$keys[15] => $this->getVideoWidth(),
			$keys[16] => $this->getVideoHeight(),
			$keys[17] => $this->getVideoFrameRate(),
			$keys[18] => $this->getVideoDar(),
			$keys[19] => $this->getVideoRotation(),
			$keys[20] => $this->getAudioFormat(),
			$keys[21] => $this->getAudioCodecId(),
			$keys[22] => $this->getAudioDuration(),
			$keys[23] => $this->getAudioBitRate(),
			$keys[24] => $this->getAudioBitRateMode(),
			$keys[25] => $this->getAudioChannels(),
			$keys[26] => $this->getAudioSamplingRate(),
			$keys[27] => $this->getAudioResolution(),
			$keys[28] => $this->getWritingLib(),
			$keys[29] => $this->getCustomData(),
			$keys[30] => $this->getRawData(),
			$keys[31] => $this->getMultiStreamInfo(),
			$keys[32] => $this->getFlavorAssetVersion(),
			$keys[33] => $this->getScanType(),
			$keys[34] => $this->getMultiStream(),
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
		$pos = mediaInfoPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCreatedAt($value);
				break;
			case 2:
				$this->setUpdatedAt($value);
				break;
			case 3:
				$this->setFlavorAssetId($value);
				break;
			case 4:
				$this->setFileSize($value);
				break;
			case 5:
				$this->setContainerFormat($value);
				break;
			case 6:
				$this->setContainerId($value);
				break;
			case 7:
				$this->setContainerProfile($value);
				break;
			case 8:
				$this->setContainerDuration($value);
				break;
			case 9:
				$this->setContainerBitRate($value);
				break;
			case 10:
				$this->setVideoFormat($value);
				break;
			case 11:
				$this->setVideoCodecId($value);
				break;
			case 12:
				$this->setVideoDuration($value);
				break;
			case 13:
				$this->setVideoBitRate($value);
				break;
			case 14:
				$this->setVideoBitRateMode($value);
				break;
			case 15:
				$this->setVideoWidth($value);
				break;
			case 16:
				$this->setVideoHeight($value);
				break;
			case 17:
				$this->setVideoFrameRate($value);
				break;
			case 18:
				$this->setVideoDar($value);
				break;
			case 19:
				$this->setVideoRotation($value);
				break;
			case 20:
				$this->setAudioFormat($value);
				break;
			case 21:
				$this->setAudioCodecId($value);
				break;
			case 22:
				$this->setAudioDuration($value);
				break;
			case 23:
				$this->setAudioBitRate($value);
				break;
			case 24:
				$this->setAudioBitRateMode($value);
				break;
			case 25:
				$this->setAudioChannels($value);
				break;
			case 26:
				$this->setAudioSamplingRate($value);
				break;
			case 27:
				$this->setAudioResolution($value);
				break;
			case 28:
				$this->setWritingLib($value);
				break;
			case 29:
				$this->setCustomData($value);
				break;
			case 30:
				$this->setRawData($value);
				break;
			case 31:
				$this->setMultiStreamInfo($value);
				break;
			case 32:
				$this->setFlavorAssetVersion($value);
				break;
			case 33:
				$this->setScanType($value);
				break;
			case 34:
				$this->setMultiStream($value);
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
		$keys = mediaInfoPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUpdatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setFlavorAssetId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFileSize($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setContainerFormat($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setContainerId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setContainerProfile($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setContainerDuration($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setContainerBitRate($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setVideoFormat($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setVideoCodecId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setVideoDuration($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setVideoBitRate($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setVideoBitRateMode($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setVideoWidth($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setVideoHeight($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setVideoFrameRate($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setVideoDar($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setVideoRotation($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setAudioFormat($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setAudioCodecId($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setAudioDuration($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setAudioBitRate($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setAudioBitRateMode($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setAudioChannels($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setAudioSamplingRate($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setAudioResolution($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setWritingLib($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setCustomData($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setRawData($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setMultiStreamInfo($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setFlavorAssetVersion($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setScanType($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setMultiStream($arr[$keys[34]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(mediaInfoPeer::DATABASE_NAME);

		if ($this->isColumnModified(mediaInfoPeer::ID)) $criteria->add(mediaInfoPeer::ID, $this->id);
		if ($this->isColumnModified(mediaInfoPeer::CREATED_AT)) $criteria->add(mediaInfoPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(mediaInfoPeer::UPDATED_AT)) $criteria->add(mediaInfoPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(mediaInfoPeer::FLAVOR_ASSET_ID)) $criteria->add(mediaInfoPeer::FLAVOR_ASSET_ID, $this->flavor_asset_id);
		if ($this->isColumnModified(mediaInfoPeer::FILE_SIZE)) $criteria->add(mediaInfoPeer::FILE_SIZE, $this->file_size);
		if ($this->isColumnModified(mediaInfoPeer::CONTAINER_FORMAT)) $criteria->add(mediaInfoPeer::CONTAINER_FORMAT, $this->container_format);
		if ($this->isColumnModified(mediaInfoPeer::CONTAINER_ID)) $criteria->add(mediaInfoPeer::CONTAINER_ID, $this->container_id);
		if ($this->isColumnModified(mediaInfoPeer::CONTAINER_PROFILE)) $criteria->add(mediaInfoPeer::CONTAINER_PROFILE, $this->container_profile);
		if ($this->isColumnModified(mediaInfoPeer::CONTAINER_DURATION)) $criteria->add(mediaInfoPeer::CONTAINER_DURATION, $this->container_duration);
		if ($this->isColumnModified(mediaInfoPeer::CONTAINER_BIT_RATE)) $criteria->add(mediaInfoPeer::CONTAINER_BIT_RATE, $this->container_bit_rate);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_FORMAT)) $criteria->add(mediaInfoPeer::VIDEO_FORMAT, $this->video_format);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_CODEC_ID)) $criteria->add(mediaInfoPeer::VIDEO_CODEC_ID, $this->video_codec_id);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_DURATION)) $criteria->add(mediaInfoPeer::VIDEO_DURATION, $this->video_duration);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_BIT_RATE)) $criteria->add(mediaInfoPeer::VIDEO_BIT_RATE, $this->video_bit_rate);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_BIT_RATE_MODE)) $criteria->add(mediaInfoPeer::VIDEO_BIT_RATE_MODE, $this->video_bit_rate_mode);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_WIDTH)) $criteria->add(mediaInfoPeer::VIDEO_WIDTH, $this->video_width);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_HEIGHT)) $criteria->add(mediaInfoPeer::VIDEO_HEIGHT, $this->video_height);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_FRAME_RATE)) $criteria->add(mediaInfoPeer::VIDEO_FRAME_RATE, $this->video_frame_rate);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_DAR)) $criteria->add(mediaInfoPeer::VIDEO_DAR, $this->video_dar);
		if ($this->isColumnModified(mediaInfoPeer::VIDEO_ROTATION)) $criteria->add(mediaInfoPeer::VIDEO_ROTATION, $this->video_rotation);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_FORMAT)) $criteria->add(mediaInfoPeer::AUDIO_FORMAT, $this->audio_format);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_CODEC_ID)) $criteria->add(mediaInfoPeer::AUDIO_CODEC_ID, $this->audio_codec_id);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_DURATION)) $criteria->add(mediaInfoPeer::AUDIO_DURATION, $this->audio_duration);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_BIT_RATE)) $criteria->add(mediaInfoPeer::AUDIO_BIT_RATE, $this->audio_bit_rate);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_BIT_RATE_MODE)) $criteria->add(mediaInfoPeer::AUDIO_BIT_RATE_MODE, $this->audio_bit_rate_mode);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_CHANNELS)) $criteria->add(mediaInfoPeer::AUDIO_CHANNELS, $this->audio_channels);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_SAMPLING_RATE)) $criteria->add(mediaInfoPeer::AUDIO_SAMPLING_RATE, $this->audio_sampling_rate);
		if ($this->isColumnModified(mediaInfoPeer::AUDIO_RESOLUTION)) $criteria->add(mediaInfoPeer::AUDIO_RESOLUTION, $this->audio_resolution);
		if ($this->isColumnModified(mediaInfoPeer::WRITING_LIB)) $criteria->add(mediaInfoPeer::WRITING_LIB, $this->writing_lib);
		if ($this->isColumnModified(mediaInfoPeer::CUSTOM_DATA)) $criteria->add(mediaInfoPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(mediaInfoPeer::RAW_DATA)) $criteria->add(mediaInfoPeer::RAW_DATA, $this->raw_data);
		if ($this->isColumnModified(mediaInfoPeer::MULTI_STREAM_INFO)) $criteria->add(mediaInfoPeer::MULTI_STREAM_INFO, $this->multi_stream_info);
		if ($this->isColumnModified(mediaInfoPeer::FLAVOR_ASSET_VERSION)) $criteria->add(mediaInfoPeer::FLAVOR_ASSET_VERSION, $this->flavor_asset_version);
		if ($this->isColumnModified(mediaInfoPeer::SCAN_TYPE)) $criteria->add(mediaInfoPeer::SCAN_TYPE, $this->scan_type);
		if ($this->isColumnModified(mediaInfoPeer::MULTI_STREAM)) $criteria->add(mediaInfoPeer::MULTI_STREAM, $this->multi_stream);

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
		$criteria = new Criteria(mediaInfoPeer::DATABASE_NAME);

		$criteria->add(mediaInfoPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of mediaInfo (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setFlavorAssetId($this->flavor_asset_id);

		$copyObj->setFileSize($this->file_size);

		$copyObj->setContainerFormat($this->container_format);

		$copyObj->setContainerId($this->container_id);

		$copyObj->setContainerProfile($this->container_profile);

		$copyObj->setContainerDuration($this->container_duration);

		$copyObj->setContainerBitRate($this->container_bit_rate);

		$copyObj->setVideoFormat($this->video_format);

		$copyObj->setVideoCodecId($this->video_codec_id);

		$copyObj->setVideoDuration($this->video_duration);

		$copyObj->setVideoBitRate($this->video_bit_rate);

		$copyObj->setVideoBitRateMode($this->video_bit_rate_mode);

		$copyObj->setVideoWidth($this->video_width);

		$copyObj->setVideoHeight($this->video_height);

		$copyObj->setVideoFrameRate($this->video_frame_rate);

		$copyObj->setVideoDar($this->video_dar);

		$copyObj->setVideoRotation($this->video_rotation);

		$copyObj->setAudioFormat($this->audio_format);

		$copyObj->setAudioCodecId($this->audio_codec_id);

		$copyObj->setAudioDuration($this->audio_duration);

		$copyObj->setAudioBitRate($this->audio_bit_rate);

		$copyObj->setAudioBitRateMode($this->audio_bit_rate_mode);

		$copyObj->setAudioChannels($this->audio_channels);

		$copyObj->setAudioSamplingRate($this->audio_sampling_rate);

		$copyObj->setAudioResolution($this->audio_resolution);

		$copyObj->setWritingLib($this->writing_lib);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setRawData($this->raw_data);

		$copyObj->setMultiStreamInfo($this->multi_stream_info);

		$copyObj->setFlavorAssetVersion($this->flavor_asset_version);

		$copyObj->setScanType($this->scan_type);

		$copyObj->setMultiStream($this->multi_stream);


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
	 * @return     mediaInfo Clone of current object.
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
	 * @var     mediaInfo Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      mediaInfo $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(mediaInfo $copiedFrom)
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
	 * @return     mediaInfoPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new mediaInfoPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a asset object.
	 *
	 * @param      asset $v
	 * @return     mediaInfo The current object (for fluent API support)
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
			$v->addmediaInfo($this);
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
			   $this->aasset->addmediaInfos($this);
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

			$this->aasset = null;
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
	
} // BasemediaInfo
