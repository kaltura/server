<?php

/**
 * Base class that represents a row from the 'entry_distribution' table.
 *
 * 
 *
 * @package plugins.contentDistribution
 * @subpackage model.om
 */
abstract class BaseEntryDistribution extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EntryDistributionPeer
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
	 * The value for the submitted_at field.
	 * @var        string
	 */
	protected $submitted_at;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the distribution_profile_id field.
	 * @var        int
	 */
	protected $distribution_profile_id;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the dirty_status field.
	 * @var        int
	 */
	protected $dirty_status;

	/**
	 * The value for the thumb_asset_ids field.
	 * @var        string
	 */
	protected $thumb_asset_ids;

	/**
	 * The value for the flavor_asset_ids field.
	 * @var        string
	 */
	protected $flavor_asset_ids;

	/**
	 * The value for the sunrise field.
	 * @var        string
	 */
	protected $sunrise;

	/**
	 * The value for the sunset field.
	 * @var        string
	 */
	protected $sunset;

	/**
	 * The value for the remote_id field.
	 * @var        string
	 */
	protected $remote_id;

	/**
	 * The value for the plays field.
	 * @var        int
	 */
	protected $plays;

	/**
	 * The value for the views field.
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the validation_errors field.
	 * @var        string
	 */
	protected $validation_errors;

	/**
	 * The value for the error_type field.
	 * @var        int
	 */
	protected $error_type;

	/**
	 * The value for the error_number field.
	 * @var        int
	 */
	protected $error_number;

	/**
	 * The value for the error_description field.
	 * @var        string
	 */
	protected $error_description;

	/**
	 * The value for the last_report field.
	 * @var        string
	 */
	protected $last_report;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

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
	 * Get the [optionally formatted] temporal [submitted_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getSubmittedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->submitted_at === null) {
			return null;
		}


		if ($this->submitted_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->submitted_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->submitted_at, true), $x);
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
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [distribution_profile_id] column value.
	 * 
	 * @return     int
	 */
	public function getDistributionProfileId()
	{
		return $this->distribution_profile_id;
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
	 * Get the [dirty_status] column value.
	 * 
	 * @return     int
	 */
	public function getDirtyStatus()
	{
		return $this->dirty_status;
	}

	/**
	 * Get the [thumb_asset_ids] column value.
	 * 
	 * @return     string
	 */
	public function getThumbAssetIds()
	{
		return $this->thumb_asset_ids;
	}

	/**
	 * Get the [flavor_asset_ids] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorAssetIds()
	{
		return $this->flavor_asset_ids;
	}

	/**
	 * Get the [optionally formatted] temporal [sunrise] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getSunrise($format = 'Y-m-d H:i:s')
	{
		if ($this->sunrise === null) {
			return null;
		}


		if ($this->sunrise === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->sunrise);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->sunrise, true), $x);
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
	 * Get the [optionally formatted] temporal [sunset] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getSunset($format = 'Y-m-d H:i:s')
	{
		if ($this->sunset === null) {
			return null;
		}


		if ($this->sunset === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->sunset);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->sunset, true), $x);
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
	 * Get the [remote_id] column value.
	 * 
	 * @return     string
	 */
	public function getRemoteId()
	{
		return $this->remote_id;
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
	 * Get the [views] column value.
	 * 
	 * @return     int
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * Get the [validation_errors] column value.
	 * 
	 * @return     string
	 */
	public function getValidationErrors()
	{
		return $this->validation_errors;
	}

	/**
	 * Get the [error_type] column value.
	 * 
	 * @return     int
	 */
	public function getErrorType()
	{
		return $this->error_type;
	}

	/**
	 * Get the [error_number] column value.
	 * 
	 * @return     int
	 */
	public function getErrorNumber()
	{
		return $this->error_number;
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
	 * Get the [optionally formatted] temporal [last_report] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getLastReport($format = 'Y-m-d H:i:s')
	{
		if ($this->last_report === null) {
			return null;
		}


		if ($this->last_report === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->last_report);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_report, true), $x);
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
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::ID]))
			$this->oldColumnsValues[EntryDistributionPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
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
				$this->modifiedColumns[] = EntryDistributionPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
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
				$this->modifiedColumns[] = EntryDistributionPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [submitted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setSubmittedAt($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::SUBMITTED_AT]))
			$this->oldColumnsValues[EntryDistributionPeer::SUBMITTED_AT] = $this->submitted_at;

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

		if ( $this->submitted_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->submitted_at !== null && $tmpDt = new DateTime($this->submitted_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->submitted_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = EntryDistributionPeer::SUBMITTED_AT;
			}
		} // if either are not null

		return $this;
	} // setSubmittedAt()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::ENTRY_ID]))
			$this->oldColumnsValues[EntryDistributionPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::PARTNER_ID]))
			$this->oldColumnsValues[EntryDistributionPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [distribution_profile_id] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setDistributionProfileId($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::DISTRIBUTION_PROFILE_ID]))
			$this->oldColumnsValues[EntryDistributionPeer::DISTRIBUTION_PROFILE_ID] = $this->distribution_profile_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->distribution_profile_id !== $v) {
			$this->distribution_profile_id = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::DISTRIBUTION_PROFILE_ID;
		}

		return $this;
	} // setDistributionProfileId()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::STATUS]))
			$this->oldColumnsValues[EntryDistributionPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [dirty_status] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setDirtyStatus($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::DIRTY_STATUS]))
			$this->oldColumnsValues[EntryDistributionPeer::DIRTY_STATUS] = $this->dirty_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dirty_status !== $v) {
			$this->dirty_status = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::DIRTY_STATUS;
		}

		return $this;
	} // setDirtyStatus()

	/**
	 * Set the value of [thumb_asset_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setThumbAssetIds($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::THUMB_ASSET_IDS]))
			$this->oldColumnsValues[EntryDistributionPeer::THUMB_ASSET_IDS] = $this->thumb_asset_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->thumb_asset_ids !== $v) {
			$this->thumb_asset_ids = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::THUMB_ASSET_IDS;
		}

		return $this;
	} // setThumbAssetIds()

	/**
	 * Set the value of [flavor_asset_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setFlavorAssetIds($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::FLAVOR_ASSET_IDS]))
			$this->oldColumnsValues[EntryDistributionPeer::FLAVOR_ASSET_IDS] = $this->flavor_asset_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_asset_ids !== $v) {
			$this->flavor_asset_ids = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::FLAVOR_ASSET_IDS;
		}

		return $this;
	} // setFlavorAssetIds()

	/**
	 * Sets the value of [sunrise] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setSunrise($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::SUNRISE]))
			$this->oldColumnsValues[EntryDistributionPeer::SUNRISE] = $this->sunrise;

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

		if ( $this->sunrise !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->sunrise !== null && $tmpDt = new DateTime($this->sunrise)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->sunrise = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = EntryDistributionPeer::SUNRISE;
			}
		} // if either are not null

		return $this;
	} // setSunrise()

	/**
	 * Sets the value of [sunset] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setSunset($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::SUNSET]))
			$this->oldColumnsValues[EntryDistributionPeer::SUNSET] = $this->sunset;

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

		if ( $this->sunset !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->sunset !== null && $tmpDt = new DateTime($this->sunset)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->sunset = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = EntryDistributionPeer::SUNSET;
			}
		} // if either are not null

		return $this;
	} // setSunset()

	/**
	 * Set the value of [remote_id] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setRemoteId($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::REMOTE_ID]))
			$this->oldColumnsValues[EntryDistributionPeer::REMOTE_ID] = $this->remote_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->remote_id !== $v) {
			$this->remote_id = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::REMOTE_ID;
		}

		return $this;
	} // setRemoteId()

	/**
	 * Set the value of [plays] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setPlays($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::PLAYS]))
			$this->oldColumnsValues[EntryDistributionPeer::PLAYS] = $this->plays;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plays !== $v) {
			$this->plays = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::PLAYS;
		}

		return $this;
	} // setPlays()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::VIEWS]))
			$this->oldColumnsValues[EntryDistributionPeer::VIEWS] = $this->views;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v) {
			$this->views = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [validation_errors] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setValidationErrors($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::VALIDATION_ERRORS]))
			$this->oldColumnsValues[EntryDistributionPeer::VALIDATION_ERRORS] = $this->validation_errors;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->validation_errors !== $v) {
			$this->validation_errors = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::VALIDATION_ERRORS;
		}

		return $this;
	} // setValidationErrors()

	/**
	 * Set the value of [error_type] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setErrorType($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::ERROR_TYPE]))
			$this->oldColumnsValues[EntryDistributionPeer::ERROR_TYPE] = $this->error_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->error_type !== $v) {
			$this->error_type = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::ERROR_TYPE;
		}

		return $this;
	} // setErrorType()

	/**
	 * Set the value of [error_number] column.
	 * 
	 * @param      int $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setErrorNumber($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::ERROR_NUMBER]))
			$this->oldColumnsValues[EntryDistributionPeer::ERROR_NUMBER] = $this->error_number;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->error_number !== $v) {
			$this->error_number = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::ERROR_NUMBER;
		}

		return $this;
	} // setErrorNumber()

	/**
	 * Set the value of [error_description] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setErrorDescription($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::ERROR_DESCRIPTION]))
			$this->oldColumnsValues[EntryDistributionPeer::ERROR_DESCRIPTION] = $this->error_description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->error_description !== $v) {
			$this->error_description = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::ERROR_DESCRIPTION;
		}

		return $this;
	} // setErrorDescription()

	/**
	 * Sets the value of [last_report] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setLastReport($v)
	{
		if(!isset($this->oldColumnsValues[EntryDistributionPeer::LAST_REPORT]))
			$this->oldColumnsValues[EntryDistributionPeer::LAST_REPORT] = $this->last_report;

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

		if ( $this->last_report !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->last_report !== null && $tmpDt = new DateTime($this->last_report)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->last_report = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = EntryDistributionPeer::LAST_REPORT;
			}
		} // if either are not null

		return $this;
	} // setLastReport()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     EntryDistribution The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = EntryDistributionPeer::CUSTOM_DATA;
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
			$this->submitted_at = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->entry_id = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->partner_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->distribution_profile_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->status = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->dirty_status = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->thumb_asset_ids = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->flavor_asset_ids = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->sunrise = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->sunset = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->remote_id = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->plays = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->views = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->validation_errors = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->error_type = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->error_number = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->error_description = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->last_report = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->custom_data = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 22; // 22 = EntryDistributionPeer::NUM_COLUMNS - EntryDistributionPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EntryDistribution object", $e);
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
			$con = Propel::getConnection(EntryDistributionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = EntryDistributionPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(EntryDistributionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				EntryDistributionPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EntryDistributionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				EntryDistributionPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = EntryDistributionPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = EntryDistributionPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EntryDistributionPeer::doUpdate($this, $con);
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


			if (($retval = EntryDistributionPeer::doValidate($this, $columns)) !== true) {
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
		$pos = EntryDistributionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getSubmittedAt();
				break;
			case 4:
				return $this->getEntryId();
				break;
			case 5:
				return $this->getPartnerId();
				break;
			case 6:
				return $this->getDistributionProfileId();
				break;
			case 7:
				return $this->getStatus();
				break;
			case 8:
				return $this->getDirtyStatus();
				break;
			case 9:
				return $this->getThumbAssetIds();
				break;
			case 10:
				return $this->getFlavorAssetIds();
				break;
			case 11:
				return $this->getSunrise();
				break;
			case 12:
				return $this->getSunset();
				break;
			case 13:
				return $this->getRemoteId();
				break;
			case 14:
				return $this->getPlays();
				break;
			case 15:
				return $this->getViews();
				break;
			case 16:
				return $this->getValidationErrors();
				break;
			case 17:
				return $this->getErrorType();
				break;
			case 18:
				return $this->getErrorNumber();
				break;
			case 19:
				return $this->getErrorDescription();
				break;
			case 20:
				return $this->getLastReport();
				break;
			case 21:
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
		$keys = EntryDistributionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getUpdatedAt(),
			$keys[3] => $this->getSubmittedAt(),
			$keys[4] => $this->getEntryId(),
			$keys[5] => $this->getPartnerId(),
			$keys[6] => $this->getDistributionProfileId(),
			$keys[7] => $this->getStatus(),
			$keys[8] => $this->getDirtyStatus(),
			$keys[9] => $this->getThumbAssetIds(),
			$keys[10] => $this->getFlavorAssetIds(),
			$keys[11] => $this->getSunrise(),
			$keys[12] => $this->getSunset(),
			$keys[13] => $this->getRemoteId(),
			$keys[14] => $this->getPlays(),
			$keys[15] => $this->getViews(),
			$keys[16] => $this->getValidationErrors(),
			$keys[17] => $this->getErrorType(),
			$keys[18] => $this->getErrorNumber(),
			$keys[19] => $this->getErrorDescription(),
			$keys[20] => $this->getLastReport(),
			$keys[21] => $this->getCustomData(),
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
		$pos = EntryDistributionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setSubmittedAt($value);
				break;
			case 4:
				$this->setEntryId($value);
				break;
			case 5:
				$this->setPartnerId($value);
				break;
			case 6:
				$this->setDistributionProfileId($value);
				break;
			case 7:
				$this->setStatus($value);
				break;
			case 8:
				$this->setDirtyStatus($value);
				break;
			case 9:
				$this->setThumbAssetIds($value);
				break;
			case 10:
				$this->setFlavorAssetIds($value);
				break;
			case 11:
				$this->setSunrise($value);
				break;
			case 12:
				$this->setSunset($value);
				break;
			case 13:
				$this->setRemoteId($value);
				break;
			case 14:
				$this->setPlays($value);
				break;
			case 15:
				$this->setViews($value);
				break;
			case 16:
				$this->setValidationErrors($value);
				break;
			case 17:
				$this->setErrorType($value);
				break;
			case 18:
				$this->setErrorNumber($value);
				break;
			case 19:
				$this->setErrorDescription($value);
				break;
			case 20:
				$this->setLastReport($value);
				break;
			case 21:
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
		$keys = EntryDistributionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUpdatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSubmittedAt($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEntryId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPartnerId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDistributionProfileId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDirtyStatus($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setThumbAssetIds($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setFlavorAssetIds($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setSunrise($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setSunset($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRemoteId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setPlays($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setViews($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setValidationErrors($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setErrorType($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setErrorNumber($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setErrorDescription($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setLastReport($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setCustomData($arr[$keys[21]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EntryDistributionPeer::DATABASE_NAME);

		if ($this->isColumnModified(EntryDistributionPeer::ID)) $criteria->add(EntryDistributionPeer::ID, $this->id);
		if ($this->isColumnModified(EntryDistributionPeer::CREATED_AT)) $criteria->add(EntryDistributionPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(EntryDistributionPeer::UPDATED_AT)) $criteria->add(EntryDistributionPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(EntryDistributionPeer::SUBMITTED_AT)) $criteria->add(EntryDistributionPeer::SUBMITTED_AT, $this->submitted_at);
		if ($this->isColumnModified(EntryDistributionPeer::ENTRY_ID)) $criteria->add(EntryDistributionPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(EntryDistributionPeer::PARTNER_ID)) $criteria->add(EntryDistributionPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID)) $criteria->add(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID, $this->distribution_profile_id);
		if ($this->isColumnModified(EntryDistributionPeer::STATUS)) $criteria->add(EntryDistributionPeer::STATUS, $this->status);
		if ($this->isColumnModified(EntryDistributionPeer::DIRTY_STATUS)) $criteria->add(EntryDistributionPeer::DIRTY_STATUS, $this->dirty_status);
		if ($this->isColumnModified(EntryDistributionPeer::THUMB_ASSET_IDS)) $criteria->add(EntryDistributionPeer::THUMB_ASSET_IDS, $this->thumb_asset_ids);
		if ($this->isColumnModified(EntryDistributionPeer::FLAVOR_ASSET_IDS)) $criteria->add(EntryDistributionPeer::FLAVOR_ASSET_IDS, $this->flavor_asset_ids);
		if ($this->isColumnModified(EntryDistributionPeer::SUNRISE)) $criteria->add(EntryDistributionPeer::SUNRISE, $this->sunrise);
		if ($this->isColumnModified(EntryDistributionPeer::SUNSET)) $criteria->add(EntryDistributionPeer::SUNSET, $this->sunset);
		if ($this->isColumnModified(EntryDistributionPeer::REMOTE_ID)) $criteria->add(EntryDistributionPeer::REMOTE_ID, $this->remote_id);
		if ($this->isColumnModified(EntryDistributionPeer::PLAYS)) $criteria->add(EntryDistributionPeer::PLAYS, $this->plays);
		if ($this->isColumnModified(EntryDistributionPeer::VIEWS)) $criteria->add(EntryDistributionPeer::VIEWS, $this->views);
		if ($this->isColumnModified(EntryDistributionPeer::VALIDATION_ERRORS)) $criteria->add(EntryDistributionPeer::VALIDATION_ERRORS, $this->validation_errors);
		if ($this->isColumnModified(EntryDistributionPeer::ERROR_TYPE)) $criteria->add(EntryDistributionPeer::ERROR_TYPE, $this->error_type);
		if ($this->isColumnModified(EntryDistributionPeer::ERROR_NUMBER)) $criteria->add(EntryDistributionPeer::ERROR_NUMBER, $this->error_number);
		if ($this->isColumnModified(EntryDistributionPeer::ERROR_DESCRIPTION)) $criteria->add(EntryDistributionPeer::ERROR_DESCRIPTION, $this->error_description);
		if ($this->isColumnModified(EntryDistributionPeer::LAST_REPORT)) $criteria->add(EntryDistributionPeer::LAST_REPORT, $this->last_report);
		if ($this->isColumnModified(EntryDistributionPeer::CUSTOM_DATA)) $criteria->add(EntryDistributionPeer::CUSTOM_DATA, $this->custom_data);

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
		$criteria = new Criteria(EntryDistributionPeer::DATABASE_NAME);

		$criteria->add(EntryDistributionPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of EntryDistribution (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setSubmittedAt($this->submitted_at);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDistributionProfileId($this->distribution_profile_id);

		$copyObj->setStatus($this->status);

		$copyObj->setDirtyStatus($this->dirty_status);

		$copyObj->setThumbAssetIds($this->thumb_asset_ids);

		$copyObj->setFlavorAssetIds($this->flavor_asset_ids);

		$copyObj->setSunrise($this->sunrise);

		$copyObj->setSunset($this->sunset);

		$copyObj->setRemoteId($this->remote_id);

		$copyObj->setPlays($this->plays);

		$copyObj->setViews($this->views);

		$copyObj->setValidationErrors($this->validation_errors);

		$copyObj->setErrorType($this->error_type);

		$copyObj->setErrorNumber($this->error_number);

		$copyObj->setErrorDescription($this->error_description);

		$copyObj->setLastReport($this->last_report);

		$copyObj->setCustomData($this->custom_data);


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
	 * @return     EntryDistribution Clone of current object.
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
	 * @var     EntryDistribution Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      EntryDistribution $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(EntryDistribution $copiedFrom)
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
	 * @return     EntryDistributionPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EntryDistributionPeer();
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
	
} // BaseEntryDistribution
