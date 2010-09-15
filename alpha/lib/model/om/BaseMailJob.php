<?php

/**
 * Base class that represents a row from the 'mail_job' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseMailJob extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MailJobPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the mail_type field.
	 * @var        int
	 */
	protected $mail_type;

	/**
	 * The value for the mail_priority field.
	 * @var        int
	 */
	protected $mail_priority;

	/**
	 * The value for the recipient_name field.
	 * @var        string
	 */
	protected $recipient_name;

	/**
	 * The value for the recipient_email field.
	 * @var        string
	 */
	protected $recipient_email;

	/**
	 * The value for the recipient_id field.
	 * @var        int
	 */
	protected $recipient_id;

	/**
	 * The value for the from_name field.
	 * @var        string
	 */
	protected $from_name;

	/**
	 * The value for the from_email field.
	 * @var        string
	 */
	protected $from_email;

	/**
	 * The value for the body_params field.
	 * @var        string
	 */
	protected $body_params;

	/**
	 * The value for the subject_params field.
	 * @var        string
	 */
	protected $subject_params;

	/**
	 * The value for the template_path field.
	 * @var        string
	 */
	protected $template_path;

	/**
	 * The value for the culture field.
	 * @var        int
	 */
	protected $culture;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the campaign_id field.
	 * @var        int
	 */
	protected $campaign_id;

	/**
	 * The value for the min_send_date field.
	 * @var        string
	 */
	protected $min_send_date;

	/**
	 * The value for the scheduler_id field.
	 * @var        int
	 */
	protected $scheduler_id;

	/**
	 * The value for the worker_id field.
	 * @var        int
	 */
	protected $worker_id;

	/**
	 * The value for the batch_index field.
	 * @var        int
	 */
	protected $batch_index;

	/**
	 * The value for the processor_expiration field.
	 * @var        string
	 */
	protected $processor_expiration;

	/**
	 * The value for the execution_attempts field.
	 * @var        int
	 */
	protected $execution_attempts;

	/**
	 * The value for the lock_version field.
	 * @var        int
	 */
	protected $lock_version;

	/**
	 * The value for the partner_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the dc field.
	 * @var        string
	 */
	protected $dc;

	/**
	 * @var        kuser
	 */
	protected $akuser;

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
		$this->partner_id = 0;
	}

	/**
	 * Initializes internal state of BaseMailJob object.
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
	 * Get the [mail_type] column value.
	 * 
	 * @return     int
	 */
	public function getMailType()
	{
		return $this->mail_type;
	}

	/**
	 * Get the [mail_priority] column value.
	 * 
	 * @return     int
	 */
	public function getMailPriority()
	{
		return $this->mail_priority;
	}

	/**
	 * Get the [recipient_name] column value.
	 * 
	 * @return     string
	 */
	public function getRecipientName()
	{
		return $this->recipient_name;
	}

	/**
	 * Get the [recipient_email] column value.
	 * 
	 * @return     string
	 */
	public function getRecipientEmail()
	{
		return $this->recipient_email;
	}

	/**
	 * Get the [recipient_id] column value.
	 * 
	 * @return     int
	 */
	public function getRecipientId()
	{
		return $this->recipient_id;
	}

	/**
	 * Get the [from_name] column value.
	 * 
	 * @return     string
	 */
	public function getFromName()
	{
		return $this->from_name;
	}

	/**
	 * Get the [from_email] column value.
	 * 
	 * @return     string
	 */
	public function getFromEmail()
	{
		return $this->from_email;
	}

	/**
	 * Get the [body_params] column value.
	 * 
	 * @return     string
	 */
	public function getBodyParams()
	{
		return $this->body_params;
	}

	/**
	 * Get the [subject_params] column value.
	 * 
	 * @return     string
	 */
	public function getSubjectParams()
	{
		return $this->subject_params;
	}

	/**
	 * Get the [template_path] column value.
	 * 
	 * @return     string
	 */
	public function getTemplatePath()
	{
		return $this->template_path;
	}

	/**
	 * Get the [culture] column value.
	 * 
	 * @return     int
	 */
	public function getCulture()
	{
		return $this->culture;
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
	 * Get the [campaign_id] column value.
	 * 
	 * @return     int
	 */
	public function getCampaignId()
	{
		return $this->campaign_id;
	}

	/**
	 * Get the [optionally formatted] temporal [min_send_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getMinSendDate($format = 'Y-m-d H:i:s')
	{
		if ($this->min_send_date === null) {
			return null;
		}


		if ($this->min_send_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->min_send_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->min_send_date, true), $x);
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
	 * Get the [scheduler_id] column value.
	 * 
	 * @return     int
	 */
	public function getSchedulerId()
	{
		return $this->scheduler_id;
	}

	/**
	 * Get the [worker_id] column value.
	 * 
	 * @return     int
	 */
	public function getWorkerId()
	{
		return $this->worker_id;
	}

	/**
	 * Get the [batch_index] column value.
	 * 
	 * @return     int
	 */
	public function getBatchIndex()
	{
		return $this->batch_index;
	}

	/**
	 * Get the [optionally formatted] temporal [processor_expiration] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getProcessorExpiration($format = 'Y-m-d H:i:s')
	{
		if ($this->processor_expiration === null) {
			return null;
		}


		if ($this->processor_expiration === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->processor_expiration);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->processor_expiration, true), $x);
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
	 * Get the [execution_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getExecutionAttempts()
	{
		return $this->execution_attempts;
	}

	/**
	 * Get the [lock_version] column value.
	 * 
	 * @return     int
	 */
	public function getLockVersion()
	{
		return $this->lock_version;
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
	 * Get the [dc] column value.
	 * 
	 * @return     string
	 */
	public function getDc()
	{
		return $this->dc;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::ID]))
			$this->oldColumnsValues[MailJobPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = MailJobPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [mail_type] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setMailType($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::MAIL_TYPE]))
			$this->oldColumnsValues[MailJobPeer::MAIL_TYPE] = $this->mail_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->mail_type !== $v) {
			$this->mail_type = $v;
			$this->modifiedColumns[] = MailJobPeer::MAIL_TYPE;
		}

		return $this;
	} // setMailType()

	/**
	 * Set the value of [mail_priority] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setMailPriority($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::MAIL_PRIORITY]))
			$this->oldColumnsValues[MailJobPeer::MAIL_PRIORITY] = $this->mail_priority;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->mail_priority !== $v) {
			$this->mail_priority = $v;
			$this->modifiedColumns[] = MailJobPeer::MAIL_PRIORITY;
		}

		return $this;
	} // setMailPriority()

	/**
	 * Set the value of [recipient_name] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setRecipientName($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::RECIPIENT_NAME]))
			$this->oldColumnsValues[MailJobPeer::RECIPIENT_NAME] = $this->recipient_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->recipient_name !== $v) {
			$this->recipient_name = $v;
			$this->modifiedColumns[] = MailJobPeer::RECIPIENT_NAME;
		}

		return $this;
	} // setRecipientName()

	/**
	 * Set the value of [recipient_email] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setRecipientEmail($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::RECIPIENT_EMAIL]))
			$this->oldColumnsValues[MailJobPeer::RECIPIENT_EMAIL] = $this->recipient_email;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->recipient_email !== $v) {
			$this->recipient_email = $v;
			$this->modifiedColumns[] = MailJobPeer::RECIPIENT_EMAIL;
		}

		return $this;
	} // setRecipientEmail()

	/**
	 * Set the value of [recipient_id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setRecipientId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::RECIPIENT_ID]))
			$this->oldColumnsValues[MailJobPeer::RECIPIENT_ID] = $this->recipient_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->recipient_id !== $v) {
			$this->recipient_id = $v;
			$this->modifiedColumns[] = MailJobPeer::RECIPIENT_ID;
		}

		if ($this->akuser !== null && $this->akuser->getId() !== $v) {
			$this->akuser = null;
		}

		return $this;
	} // setRecipientId()

	/**
	 * Set the value of [from_name] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setFromName($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::FROM_NAME]))
			$this->oldColumnsValues[MailJobPeer::FROM_NAME] = $this->from_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->from_name !== $v) {
			$this->from_name = $v;
			$this->modifiedColumns[] = MailJobPeer::FROM_NAME;
		}

		return $this;
	} // setFromName()

	/**
	 * Set the value of [from_email] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setFromEmail($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::FROM_EMAIL]))
			$this->oldColumnsValues[MailJobPeer::FROM_EMAIL] = $this->from_email;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->from_email !== $v) {
			$this->from_email = $v;
			$this->modifiedColumns[] = MailJobPeer::FROM_EMAIL;
		}

		return $this;
	} // setFromEmail()

	/**
	 * Set the value of [body_params] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setBodyParams($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::BODY_PARAMS]))
			$this->oldColumnsValues[MailJobPeer::BODY_PARAMS] = $this->body_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->body_params !== $v) {
			$this->body_params = $v;
			$this->modifiedColumns[] = MailJobPeer::BODY_PARAMS;
		}

		return $this;
	} // setBodyParams()

	/**
	 * Set the value of [subject_params] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setSubjectParams($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::SUBJECT_PARAMS]))
			$this->oldColumnsValues[MailJobPeer::SUBJECT_PARAMS] = $this->subject_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->subject_params !== $v) {
			$this->subject_params = $v;
			$this->modifiedColumns[] = MailJobPeer::SUBJECT_PARAMS;
		}

		return $this;
	} // setSubjectParams()

	/**
	 * Set the value of [template_path] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setTemplatePath($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::TEMPLATE_PATH]))
			$this->oldColumnsValues[MailJobPeer::TEMPLATE_PATH] = $this->template_path;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->template_path !== $v) {
			$this->template_path = $v;
			$this->modifiedColumns[] = MailJobPeer::TEMPLATE_PATH;
		}

		return $this;
	} // setTemplatePath()

	/**
	 * Set the value of [culture] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setCulture($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::CULTURE]))
			$this->oldColumnsValues[MailJobPeer::CULTURE] = $this->culture;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->culture !== $v) {
			$this->culture = $v;
			$this->modifiedColumns[] = MailJobPeer::CULTURE;
		}

		return $this;
	} // setCulture()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::STATUS]))
			$this->oldColumnsValues[MailJobPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = MailJobPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     MailJob The current object (for fluent API support)
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
				$this->modifiedColumns[] = MailJobPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [campaign_id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setCampaignId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::CAMPAIGN_ID]))
			$this->oldColumnsValues[MailJobPeer::CAMPAIGN_ID] = $this->campaign_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->campaign_id !== $v) {
			$this->campaign_id = $v;
			$this->modifiedColumns[] = MailJobPeer::CAMPAIGN_ID;
		}

		return $this;
	} // setCampaignId()

	/**
	 * Sets the value of [min_send_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setMinSendDate($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::MIN_SEND_DATE]))
			$this->oldColumnsValues[MailJobPeer::MIN_SEND_DATE] = $this->min_send_date;

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

		if ( $this->min_send_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->min_send_date !== null && $tmpDt = new DateTime($this->min_send_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->min_send_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = MailJobPeer::MIN_SEND_DATE;
			}
		} // if either are not null

		return $this;
	} // setMinSendDate()

	/**
	 * Set the value of [scheduler_id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setSchedulerId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::SCHEDULER_ID]))
			$this->oldColumnsValues[MailJobPeer::SCHEDULER_ID] = $this->scheduler_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->scheduler_id !== $v) {
			$this->scheduler_id = $v;
			$this->modifiedColumns[] = MailJobPeer::SCHEDULER_ID;
		}

		return $this;
	} // setSchedulerId()

	/**
	 * Set the value of [worker_id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setWorkerId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::WORKER_ID]))
			$this->oldColumnsValues[MailJobPeer::WORKER_ID] = $this->worker_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->worker_id !== $v) {
			$this->worker_id = $v;
			$this->modifiedColumns[] = MailJobPeer::WORKER_ID;
		}

		return $this;
	} // setWorkerId()

	/**
	 * Set the value of [batch_index] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setBatchIndex($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::BATCH_INDEX]))
			$this->oldColumnsValues[MailJobPeer::BATCH_INDEX] = $this->batch_index;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->batch_index !== $v) {
			$this->batch_index = $v;
			$this->modifiedColumns[] = MailJobPeer::BATCH_INDEX;
		}

		return $this;
	} // setBatchIndex()

	/**
	 * Sets the value of [processor_expiration] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setProcessorExpiration($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::PROCESSOR_EXPIRATION]))
			$this->oldColumnsValues[MailJobPeer::PROCESSOR_EXPIRATION] = $this->processor_expiration;

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

		if ( $this->processor_expiration !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->processor_expiration !== null && $tmpDt = new DateTime($this->processor_expiration)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->processor_expiration = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = MailJobPeer::PROCESSOR_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setProcessorExpiration()

	/**
	 * Set the value of [execution_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setExecutionAttempts($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::EXECUTION_ATTEMPTS]))
			$this->oldColumnsValues[MailJobPeer::EXECUTION_ATTEMPTS] = $this->execution_attempts;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->execution_attempts !== $v) {
			$this->execution_attempts = $v;
			$this->modifiedColumns[] = MailJobPeer::EXECUTION_ATTEMPTS;
		}

		return $this;
	} // setExecutionAttempts()

	/**
	 * Set the value of [lock_version] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setLockVersion($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::LOCK_VERSION]))
			$this->oldColumnsValues[MailJobPeer::LOCK_VERSION] = $this->lock_version;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lock_version !== $v) {
			$this->lock_version = $v;
			$this->modifiedColumns[] = MailJobPeer::LOCK_VERSION;
		}

		return $this;
	} // setLockVersion()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::PARTNER_ID]))
			$this->oldColumnsValues[MailJobPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = MailJobPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     MailJob The current object (for fluent API support)
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
				$this->modifiedColumns[] = MailJobPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      string $v new value
	 * @return     MailJob The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[MailJobPeer::DC]))
			$this->oldColumnsValues[MailJobPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = MailJobPeer::DC;
		}

		return $this;
	} // setDc()

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
			if ($this->partner_id !== 0) {
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
			$this->mail_type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->mail_priority = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->recipient_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->recipient_email = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->recipient_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->from_name = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->from_email = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->body_params = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->subject_params = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->template_path = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->culture = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->status = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->created_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->campaign_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->min_send_date = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->scheduler_id = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->worker_id = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->batch_index = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->processor_expiration = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->execution_attempts = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->lock_version = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->partner_id = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->updated_at = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->dc = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 25; // 25 = MailJobPeer::NUM_COLUMNS - MailJobPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating MailJob object", $e);
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

		if ($this->akuser !== null && $this->recipient_id !== $this->akuser->getId()) {
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
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = MailJobPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->akuser = null;
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
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				MailJobPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				MailJobPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = MailJobPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = MailJobPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += MailJobPeer::doUpdate($this, $con);
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
		MailJobPeer::setUseCriteriaFilter(false);
		$this->reload();
		MailJobPeer::setUseCriteriaFilter(true);
		
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
			$this->setUpdatedAt(time());
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->akuser !== null) {
				if (!$this->akuser->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->akuser->getValidationFailures());
				}
			}


			if (($retval = MailJobPeer::doValidate($this, $columns)) !== true) {
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
		$pos = MailJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getMailType();
				break;
			case 2:
				return $this->getMailPriority();
				break;
			case 3:
				return $this->getRecipientName();
				break;
			case 4:
				return $this->getRecipientEmail();
				break;
			case 5:
				return $this->getRecipientId();
				break;
			case 6:
				return $this->getFromName();
				break;
			case 7:
				return $this->getFromEmail();
				break;
			case 8:
				return $this->getBodyParams();
				break;
			case 9:
				return $this->getSubjectParams();
				break;
			case 10:
				return $this->getTemplatePath();
				break;
			case 11:
				return $this->getCulture();
				break;
			case 12:
				return $this->getStatus();
				break;
			case 13:
				return $this->getCreatedAt();
				break;
			case 14:
				return $this->getCampaignId();
				break;
			case 15:
				return $this->getMinSendDate();
				break;
			case 16:
				return $this->getSchedulerId();
				break;
			case 17:
				return $this->getWorkerId();
				break;
			case 18:
				return $this->getBatchIndex();
				break;
			case 19:
				return $this->getProcessorExpiration();
				break;
			case 20:
				return $this->getExecutionAttempts();
				break;
			case 21:
				return $this->getLockVersion();
				break;
			case 22:
				return $this->getPartnerId();
				break;
			case 23:
				return $this->getUpdatedAt();
				break;
			case 24:
				return $this->getDc();
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
		$keys = MailJobPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getMailType(),
			$keys[2] => $this->getMailPriority(),
			$keys[3] => $this->getRecipientName(),
			$keys[4] => $this->getRecipientEmail(),
			$keys[5] => $this->getRecipientId(),
			$keys[6] => $this->getFromName(),
			$keys[7] => $this->getFromEmail(),
			$keys[8] => $this->getBodyParams(),
			$keys[9] => $this->getSubjectParams(),
			$keys[10] => $this->getTemplatePath(),
			$keys[11] => $this->getCulture(),
			$keys[12] => $this->getStatus(),
			$keys[13] => $this->getCreatedAt(),
			$keys[14] => $this->getCampaignId(),
			$keys[15] => $this->getMinSendDate(),
			$keys[16] => $this->getSchedulerId(),
			$keys[17] => $this->getWorkerId(),
			$keys[18] => $this->getBatchIndex(),
			$keys[19] => $this->getProcessorExpiration(),
			$keys[20] => $this->getExecutionAttempts(),
			$keys[21] => $this->getLockVersion(),
			$keys[22] => $this->getPartnerId(),
			$keys[23] => $this->getUpdatedAt(),
			$keys[24] => $this->getDc(),
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
		$pos = MailJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setMailType($value);
				break;
			case 2:
				$this->setMailPriority($value);
				break;
			case 3:
				$this->setRecipientName($value);
				break;
			case 4:
				$this->setRecipientEmail($value);
				break;
			case 5:
				$this->setRecipientId($value);
				break;
			case 6:
				$this->setFromName($value);
				break;
			case 7:
				$this->setFromEmail($value);
				break;
			case 8:
				$this->setBodyParams($value);
				break;
			case 9:
				$this->setSubjectParams($value);
				break;
			case 10:
				$this->setTemplatePath($value);
				break;
			case 11:
				$this->setCulture($value);
				break;
			case 12:
				$this->setStatus($value);
				break;
			case 13:
				$this->setCreatedAt($value);
				break;
			case 14:
				$this->setCampaignId($value);
				break;
			case 15:
				$this->setMinSendDate($value);
				break;
			case 16:
				$this->setSchedulerId($value);
				break;
			case 17:
				$this->setWorkerId($value);
				break;
			case 18:
				$this->setBatchIndex($value);
				break;
			case 19:
				$this->setProcessorExpiration($value);
				break;
			case 20:
				$this->setExecutionAttempts($value);
				break;
			case 21:
				$this->setLockVersion($value);
				break;
			case 22:
				$this->setPartnerId($value);
				break;
			case 23:
				$this->setUpdatedAt($value);
				break;
			case 24:
				$this->setDc($value);
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
		$keys = MailJobPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setMailType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setMailPriority($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setRecipientName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setRecipientEmail($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setRecipientId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFromName($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFromEmail($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setBodyParams($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSubjectParams($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setTemplatePath($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCulture($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setStatus($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setCreatedAt($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCampaignId($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setMinSendDate($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setSchedulerId($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setWorkerId($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setBatchIndex($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setProcessorExpiration($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setExecutionAttempts($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setLockVersion($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setPartnerId($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setUpdatedAt($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setDc($arr[$keys[24]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MailJobPeer::DATABASE_NAME);

		if ($this->isColumnModified(MailJobPeer::ID)) $criteria->add(MailJobPeer::ID, $this->id);
		if ($this->isColumnModified(MailJobPeer::MAIL_TYPE)) $criteria->add(MailJobPeer::MAIL_TYPE, $this->mail_type);
		if ($this->isColumnModified(MailJobPeer::MAIL_PRIORITY)) $criteria->add(MailJobPeer::MAIL_PRIORITY, $this->mail_priority);
		if ($this->isColumnModified(MailJobPeer::RECIPIENT_NAME)) $criteria->add(MailJobPeer::RECIPIENT_NAME, $this->recipient_name);
		if ($this->isColumnModified(MailJobPeer::RECIPIENT_EMAIL)) $criteria->add(MailJobPeer::RECIPIENT_EMAIL, $this->recipient_email);
		if ($this->isColumnModified(MailJobPeer::RECIPIENT_ID)) $criteria->add(MailJobPeer::RECIPIENT_ID, $this->recipient_id);
		if ($this->isColumnModified(MailJobPeer::FROM_NAME)) $criteria->add(MailJobPeer::FROM_NAME, $this->from_name);
		if ($this->isColumnModified(MailJobPeer::FROM_EMAIL)) $criteria->add(MailJobPeer::FROM_EMAIL, $this->from_email);
		if ($this->isColumnModified(MailJobPeer::BODY_PARAMS)) $criteria->add(MailJobPeer::BODY_PARAMS, $this->body_params);
		if ($this->isColumnModified(MailJobPeer::SUBJECT_PARAMS)) $criteria->add(MailJobPeer::SUBJECT_PARAMS, $this->subject_params);
		if ($this->isColumnModified(MailJobPeer::TEMPLATE_PATH)) $criteria->add(MailJobPeer::TEMPLATE_PATH, $this->template_path);
		if ($this->isColumnModified(MailJobPeer::CULTURE)) $criteria->add(MailJobPeer::CULTURE, $this->culture);
		if ($this->isColumnModified(MailJobPeer::STATUS)) $criteria->add(MailJobPeer::STATUS, $this->status);
		if ($this->isColumnModified(MailJobPeer::CREATED_AT)) $criteria->add(MailJobPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(MailJobPeer::CAMPAIGN_ID)) $criteria->add(MailJobPeer::CAMPAIGN_ID, $this->campaign_id);
		if ($this->isColumnModified(MailJobPeer::MIN_SEND_DATE)) $criteria->add(MailJobPeer::MIN_SEND_DATE, $this->min_send_date);
		if ($this->isColumnModified(MailJobPeer::SCHEDULER_ID)) $criteria->add(MailJobPeer::SCHEDULER_ID, $this->scheduler_id);
		if ($this->isColumnModified(MailJobPeer::WORKER_ID)) $criteria->add(MailJobPeer::WORKER_ID, $this->worker_id);
		if ($this->isColumnModified(MailJobPeer::BATCH_INDEX)) $criteria->add(MailJobPeer::BATCH_INDEX, $this->batch_index);
		if ($this->isColumnModified(MailJobPeer::PROCESSOR_EXPIRATION)) $criteria->add(MailJobPeer::PROCESSOR_EXPIRATION, $this->processor_expiration);
		if ($this->isColumnModified(MailJobPeer::EXECUTION_ATTEMPTS)) $criteria->add(MailJobPeer::EXECUTION_ATTEMPTS, $this->execution_attempts);
		if ($this->isColumnModified(MailJobPeer::LOCK_VERSION)) $criteria->add(MailJobPeer::LOCK_VERSION, $this->lock_version);
		if ($this->isColumnModified(MailJobPeer::PARTNER_ID)) $criteria->add(MailJobPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(MailJobPeer::UPDATED_AT)) $criteria->add(MailJobPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(MailJobPeer::DC)) $criteria->add(MailJobPeer::DC, $this->dc);

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
		$criteria = new Criteria(MailJobPeer::DATABASE_NAME);

		$criteria->add(MailJobPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of MailJob (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setMailType($this->mail_type);

		$copyObj->setMailPriority($this->mail_priority);

		$copyObj->setRecipientName($this->recipient_name);

		$copyObj->setRecipientEmail($this->recipient_email);

		$copyObj->setRecipientId($this->recipient_id);

		$copyObj->setFromName($this->from_name);

		$copyObj->setFromEmail($this->from_email);

		$copyObj->setBodyParams($this->body_params);

		$copyObj->setSubjectParams($this->subject_params);

		$copyObj->setTemplatePath($this->template_path);

		$copyObj->setCulture($this->culture);

		$copyObj->setStatus($this->status);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setCampaignId($this->campaign_id);

		$copyObj->setMinSendDate($this->min_send_date);

		$copyObj->setSchedulerId($this->scheduler_id);

		$copyObj->setWorkerId($this->worker_id);

		$copyObj->setBatchIndex($this->batch_index);

		$copyObj->setProcessorExpiration($this->processor_expiration);

		$copyObj->setExecutionAttempts($this->execution_attempts);

		$copyObj->setLockVersion($this->lock_version);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setDc($this->dc);


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
	 * @return     MailJob Clone of current object.
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
	 * @var     MailJob Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      MailJob $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(MailJob $copiedFrom)
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
	 * @return     MailJobPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MailJobPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a kuser object.
	 *
	 * @param      kuser $v
	 * @return     MailJob The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setkuser(kuser $v = null)
	{
		if ($v === null) {
			$this->setRecipientId(NULL);
		} else {
			$this->setRecipientId($v->getId());
		}

		$this->akuser = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the kuser object, it will not be re-added.
		if ($v !== null) {
			$v->addMailJob($this);
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
		if ($this->akuser === null && ($this->recipient_id !== null)) {
			$this->akuser = kuserPeer::retrieveByPk($this->recipient_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->akuser->addMailJobs($this);
			 */
		}
		return $this->akuser;
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

			$this->akuser = null;
	}

} // BaseMailJob
