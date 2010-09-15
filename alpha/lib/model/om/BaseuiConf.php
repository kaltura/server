<?php

/**
 * Base class that represents a row from the 'ui_conf' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseuiConf extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        uiConfPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the obj_type field.
	 * @var        int
	 */
	protected $obj_type;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the subp_id field.
	 * @var        int
	 */
	protected $subp_id;

	/**
	 * The value for the conf_file_path field.
	 * @var        string
	 */
	protected $conf_file_path;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the width field.
	 * @var        string
	 */
	protected $width;

	/**
	 * The value for the height field.
	 * @var        string
	 */
	protected $height;

	/**
	 * The value for the html_params field.
	 * @var        string
	 */
	protected $html_params;

	/**
	 * The value for the swf_url field.
	 * @var        string
	 */
	protected $swf_url;

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
	 * The value for the conf_vars field.
	 * @var        string
	 */
	protected $conf_vars;

	/**
	 * The value for the use_cdn field.
	 * @var        int
	 */
	protected $use_cdn;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the display_in_search field.
	 * @var        int
	 */
	protected $display_in_search;

	/**
	 * The value for the creation_mode field.
	 * @var        int
	 */
	protected $creation_mode;

	/**
	 * The value for the version field.
	 * @var        string
	 */
	protected $version;

	/**
	 * @var        array widget[] Collection to store aggregation of widget objects.
	 */
	protected $collwidgets;

	/**
	 * @var        Criteria The criteria used to select the current contents of collwidgets.
	 */
	private $lastwidgetCriteria = null;

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
	 * Get the [obj_type] column value.
	 * 
	 * @return     int
	 */
	public function getObjType()
	{
		return $this->obj_type;
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
	 * Get the [subp_id] column value.
	 * 
	 * @return     int
	 */
	public function getSubpId()
	{
		return $this->subp_id;
	}

	/**
	 * Get the [conf_file_path] column value.
	 * 
	 * @return     string
	 */
	public function getConfFilePath()
	{
		return $this->conf_file_path;
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
	 * Get the [width] column value.
	 * 
	 * @return     string
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get the [height] column value.
	 * 
	 * @return     string
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get the [html_params] column value.
	 * 
	 * @return     string
	 */
	public function getHtmlParams()
	{
		return $this->html_params;
	}

	/**
	 * Get the [swf_url] column value.
	 * 
	 * @return     string
	 */
	public function getSwfUrl()
	{
		return $this->swf_url;
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
	 * Get the [conf_vars] column value.
	 * 
	 * @return     string
	 */
	public function getConfVars()
	{
		return $this->conf_vars;
	}

	/**
	 * Get the [use_cdn] column value.
	 * 
	 * @return     int
	 */
	public function getUseCdn()
	{
		return $this->use_cdn;
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
	 * Get the [custom_data] column value.
	 * 
	 * @return     string
	 */
	public function getCustomData()
	{
		return $this->custom_data;
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
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
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
	 * Get the [creation_mode] column value.
	 * 
	 * @return     int
	 */
	public function getCreationMode()
	{
		return $this->creation_mode;
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::ID]))
			$this->oldColumnsValues[uiConfPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = uiConfPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [obj_type] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setObjType($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::OBJ_TYPE]))
			$this->oldColumnsValues[uiConfPeer::OBJ_TYPE] = $this->obj_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->obj_type !== $v) {
			$this->obj_type = $v;
			$this->modifiedColumns[] = uiConfPeer::OBJ_TYPE;
		}

		return $this;
	} // setObjType()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::PARTNER_ID]))
			$this->oldColumnsValues[uiConfPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = uiConfPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [subp_id] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setSubpId($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::SUBP_ID]))
			$this->oldColumnsValues[uiConfPeer::SUBP_ID] = $this->subp_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subp_id !== $v) {
			$this->subp_id = $v;
			$this->modifiedColumns[] = uiConfPeer::SUBP_ID;
		}

		return $this;
	} // setSubpId()

	/**
	 * Set the value of [conf_file_path] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setConfFilePath($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::CONF_FILE_PATH]))
			$this->oldColumnsValues[uiConfPeer::CONF_FILE_PATH] = $this->conf_file_path;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conf_file_path !== $v) {
			$this->conf_file_path = $v;
			$this->modifiedColumns[] = uiConfPeer::CONF_FILE_PATH;
		}

		return $this;
	} // setConfFilePath()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::NAME]))
			$this->oldColumnsValues[uiConfPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = uiConfPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [width] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setWidth($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::WIDTH]))
			$this->oldColumnsValues[uiConfPeer::WIDTH] = $this->width;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->width !== $v) {
			$this->width = $v;
			$this->modifiedColumns[] = uiConfPeer::WIDTH;
		}

		return $this;
	} // setWidth()

	/**
	 * Set the value of [height] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setHeight($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::HEIGHT]))
			$this->oldColumnsValues[uiConfPeer::HEIGHT] = $this->height;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->height !== $v) {
			$this->height = $v;
			$this->modifiedColumns[] = uiConfPeer::HEIGHT;
		}

		return $this;
	} // setHeight()

	/**
	 * Set the value of [html_params] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setHtmlParams($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::HTML_PARAMS]))
			$this->oldColumnsValues[uiConfPeer::HTML_PARAMS] = $this->html_params;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->html_params !== $v) {
			$this->html_params = $v;
			$this->modifiedColumns[] = uiConfPeer::HTML_PARAMS;
		}

		return $this;
	} // setHtmlParams()

	/**
	 * Set the value of [swf_url] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setSwfUrl($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::SWF_URL]))
			$this->oldColumnsValues[uiConfPeer::SWF_URL] = $this->swf_url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->swf_url !== $v) {
			$this->swf_url = $v;
			$this->modifiedColumns[] = uiConfPeer::SWF_URL;
		}

		return $this;
	} // setSwfUrl()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     uiConf The current object (for fluent API support)
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
				$this->modifiedColumns[] = uiConfPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     uiConf The current object (for fluent API support)
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
				$this->modifiedColumns[] = uiConfPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [conf_vars] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setConfVars($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::CONF_VARS]))
			$this->oldColumnsValues[uiConfPeer::CONF_VARS] = $this->conf_vars;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->conf_vars !== $v) {
			$this->conf_vars = $v;
			$this->modifiedColumns[] = uiConfPeer::CONF_VARS;
		}

		return $this;
	} // setConfVars()

	/**
	 * Set the value of [use_cdn] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setUseCdn($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::USE_CDN]))
			$this->oldColumnsValues[uiConfPeer::USE_CDN] = $this->use_cdn;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->use_cdn !== $v) {
			$this->use_cdn = $v;
			$this->modifiedColumns[] = uiConfPeer::USE_CDN;
		}

		return $this;
	} // setUseCdn()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::TAGS]))
			$this->oldColumnsValues[uiConfPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = uiConfPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = uiConfPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::STATUS]))
			$this->oldColumnsValues[uiConfPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = uiConfPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::DESCRIPTION]))
			$this->oldColumnsValues[uiConfPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = uiConfPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [display_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setDisplayInSearch($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::DISPLAY_IN_SEARCH]))
			$this->oldColumnsValues[uiConfPeer::DISPLAY_IN_SEARCH] = $this->display_in_search;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_in_search !== $v) {
			$this->display_in_search = $v;
			$this->modifiedColumns[] = uiConfPeer::DISPLAY_IN_SEARCH;
		}

		return $this;
	} // setDisplayInSearch()

	/**
	 * Set the value of [creation_mode] column.
	 * 
	 * @param      int $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setCreationMode($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::CREATION_MODE]))
			$this->oldColumnsValues[uiConfPeer::CREATION_MODE] = $this->creation_mode;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->creation_mode !== $v) {
			$this->creation_mode = $v;
			$this->modifiedColumns[] = uiConfPeer::CREATION_MODE;
		}

		return $this;
	} // setCreationMode()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      string $v new value
	 * @return     uiConf The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if(!isset($this->oldColumnsValues[uiConfPeer::VERSION]))
			$this->oldColumnsValues[uiConfPeer::VERSION] = $this->version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = uiConfPeer::VERSION;
		}

		return $this;
	} // setVersion()

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
			$this->obj_type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->partner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->subp_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->conf_file_path = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->width = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->height = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->html_params = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->swf_url = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->created_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->updated_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->conf_vars = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->use_cdn = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->tags = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->custom_data = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->status = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->description = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->display_in_search = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->creation_mode = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->version = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 21; // 21 = uiConfPeer::NUM_COLUMNS - uiConfPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating uiConf object", $e);
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
			$con = Propel::getConnection(uiConfPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = uiConfPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collwidgets = null;
			$this->lastwidgetCriteria = null;

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
			$con = Propel::getConnection(uiConfPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				uiConfPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(uiConfPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				uiConfPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = uiConfPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = uiConfPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += uiConfPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collwidgets !== null) {
				foreach ($this->collwidgets as $referrerFK) {
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
		uiConfPeer::setUseCriteriaFilter(false);
		$this->reload();
		uiConfPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = uiConfPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collwidgets !== null) {
					foreach ($this->collwidgets as $referrerFK) {
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
		$pos = uiConfPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getObjType();
				break;
			case 2:
				return $this->getPartnerId();
				break;
			case 3:
				return $this->getSubpId();
				break;
			case 4:
				return $this->getConfFilePath();
				break;
			case 5:
				return $this->getName();
				break;
			case 6:
				return $this->getWidth();
				break;
			case 7:
				return $this->getHeight();
				break;
			case 8:
				return $this->getHtmlParams();
				break;
			case 9:
				return $this->getSwfUrl();
				break;
			case 10:
				return $this->getCreatedAt();
				break;
			case 11:
				return $this->getUpdatedAt();
				break;
			case 12:
				return $this->getConfVars();
				break;
			case 13:
				return $this->getUseCdn();
				break;
			case 14:
				return $this->getTags();
				break;
			case 15:
				return $this->getCustomData();
				break;
			case 16:
				return $this->getStatus();
				break;
			case 17:
				return $this->getDescription();
				break;
			case 18:
				return $this->getDisplayInSearch();
				break;
			case 19:
				return $this->getCreationMode();
				break;
			case 20:
				return $this->getVersion();
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
		$keys = uiConfPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getObjType(),
			$keys[2] => $this->getPartnerId(),
			$keys[3] => $this->getSubpId(),
			$keys[4] => $this->getConfFilePath(),
			$keys[5] => $this->getName(),
			$keys[6] => $this->getWidth(),
			$keys[7] => $this->getHeight(),
			$keys[8] => $this->getHtmlParams(),
			$keys[9] => $this->getSwfUrl(),
			$keys[10] => $this->getCreatedAt(),
			$keys[11] => $this->getUpdatedAt(),
			$keys[12] => $this->getConfVars(),
			$keys[13] => $this->getUseCdn(),
			$keys[14] => $this->getTags(),
			$keys[15] => $this->getCustomData(),
			$keys[16] => $this->getStatus(),
			$keys[17] => $this->getDescription(),
			$keys[18] => $this->getDisplayInSearch(),
			$keys[19] => $this->getCreationMode(),
			$keys[20] => $this->getVersion(),
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
		$pos = uiConfPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setObjType($value);
				break;
			case 2:
				$this->setPartnerId($value);
				break;
			case 3:
				$this->setSubpId($value);
				break;
			case 4:
				$this->setConfFilePath($value);
				break;
			case 5:
				$this->setName($value);
				break;
			case 6:
				$this->setWidth($value);
				break;
			case 7:
				$this->setHeight($value);
				break;
			case 8:
				$this->setHtmlParams($value);
				break;
			case 9:
				$this->setSwfUrl($value);
				break;
			case 10:
				$this->setCreatedAt($value);
				break;
			case 11:
				$this->setUpdatedAt($value);
				break;
			case 12:
				$this->setConfVars($value);
				break;
			case 13:
				$this->setUseCdn($value);
				break;
			case 14:
				$this->setTags($value);
				break;
			case 15:
				$this->setCustomData($value);
				break;
			case 16:
				$this->setStatus($value);
				break;
			case 17:
				$this->setDescription($value);
				break;
			case 18:
				$this->setDisplayInSearch($value);
				break;
			case 19:
				$this->setCreationMode($value);
				break;
			case 20:
				$this->setVersion($value);
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
		$keys = uiConfPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setObjType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSubpId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setConfFilePath($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setWidth($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setHeight($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setHtmlParams($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setSwfUrl($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCreatedAt($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setUpdatedAt($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setConfVars($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setUseCdn($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setTags($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setCustomData($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setStatus($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDescription($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setDisplayInSearch($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setCreationMode($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setVersion($arr[$keys[20]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(uiConfPeer::DATABASE_NAME);

		if ($this->isColumnModified(uiConfPeer::ID)) $criteria->add(uiConfPeer::ID, $this->id);
		if ($this->isColumnModified(uiConfPeer::OBJ_TYPE)) $criteria->add(uiConfPeer::OBJ_TYPE, $this->obj_type);
		if ($this->isColumnModified(uiConfPeer::PARTNER_ID)) $criteria->add(uiConfPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(uiConfPeer::SUBP_ID)) $criteria->add(uiConfPeer::SUBP_ID, $this->subp_id);
		if ($this->isColumnModified(uiConfPeer::CONF_FILE_PATH)) $criteria->add(uiConfPeer::CONF_FILE_PATH, $this->conf_file_path);
		if ($this->isColumnModified(uiConfPeer::NAME)) $criteria->add(uiConfPeer::NAME, $this->name);
		if ($this->isColumnModified(uiConfPeer::WIDTH)) $criteria->add(uiConfPeer::WIDTH, $this->width);
		if ($this->isColumnModified(uiConfPeer::HEIGHT)) $criteria->add(uiConfPeer::HEIGHT, $this->height);
		if ($this->isColumnModified(uiConfPeer::HTML_PARAMS)) $criteria->add(uiConfPeer::HTML_PARAMS, $this->html_params);
		if ($this->isColumnModified(uiConfPeer::SWF_URL)) $criteria->add(uiConfPeer::SWF_URL, $this->swf_url);
		if ($this->isColumnModified(uiConfPeer::CREATED_AT)) $criteria->add(uiConfPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(uiConfPeer::UPDATED_AT)) $criteria->add(uiConfPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(uiConfPeer::CONF_VARS)) $criteria->add(uiConfPeer::CONF_VARS, $this->conf_vars);
		if ($this->isColumnModified(uiConfPeer::USE_CDN)) $criteria->add(uiConfPeer::USE_CDN, $this->use_cdn);
		if ($this->isColumnModified(uiConfPeer::TAGS)) $criteria->add(uiConfPeer::TAGS, $this->tags);
		if ($this->isColumnModified(uiConfPeer::CUSTOM_DATA)) $criteria->add(uiConfPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(uiConfPeer::STATUS)) $criteria->add(uiConfPeer::STATUS, $this->status);
		if ($this->isColumnModified(uiConfPeer::DESCRIPTION)) $criteria->add(uiConfPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(uiConfPeer::DISPLAY_IN_SEARCH)) $criteria->add(uiConfPeer::DISPLAY_IN_SEARCH, $this->display_in_search);
		if ($this->isColumnModified(uiConfPeer::CREATION_MODE)) $criteria->add(uiConfPeer::CREATION_MODE, $this->creation_mode);
		if ($this->isColumnModified(uiConfPeer::VERSION)) $criteria->add(uiConfPeer::VERSION, $this->version);

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
		$criteria = new Criteria(uiConfPeer::DATABASE_NAME);

		$criteria->add(uiConfPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of uiConf (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setObjType($this->obj_type);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setSubpId($this->subp_id);

		$copyObj->setConfFilePath($this->conf_file_path);

		$copyObj->setName($this->name);

		$copyObj->setWidth($this->width);

		$copyObj->setHeight($this->height);

		$copyObj->setHtmlParams($this->html_params);

		$copyObj->setSwfUrl($this->swf_url);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setConfVars($this->conf_vars);

		$copyObj->setUseCdn($this->use_cdn);

		$copyObj->setTags($this->tags);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setStatus($this->status);

		$copyObj->setDescription($this->description);

		$copyObj->setDisplayInSearch($this->display_in_search);

		$copyObj->setCreationMode($this->creation_mode);

		$copyObj->setVersion($this->version);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getwidgets() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addwidget($relObj->copy($deepCopy));
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
	 * @return     uiConf Clone of current object.
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
	 * @var     uiConf Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      uiConf $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(uiConf $copiedFrom)
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
	 * @return     uiConfPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new uiConfPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collwidgets collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addwidgets()
	 */
	public function clearwidgets()
	{
		$this->collwidgets = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collwidgets collection (array).
	 *
	 * By default this just sets the collwidgets collection to an empty array (like clearcollwidgets());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initwidgets()
	{
		$this->collwidgets = array();
	}

	/**
	 * Gets an array of widget objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this uiConf has previously been saved, it will retrieve
	 * related widgets from storage. If this uiConf is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array widget[]
	 * @throws     PropelException
	 */
	public function getwidgets($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(uiConfPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
			   $this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				widgetPeer::addSelectColumns($criteria);
				$this->collwidgets = widgetPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				widgetPeer::addSelectColumns($criteria);
				if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
					$this->collwidgets = widgetPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastwidgetCriteria = $criteria;
		return $this->collwidgets;
	}

	/**
	 * Returns the number of related widget objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related widget objects.
	 * @throws     PropelException
	 */
	public function countwidgets(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(uiConfPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				$count = widgetPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
					$count = widgetPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collwidgets);
				}
			} else {
				$count = count($this->collwidgets);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a widget object to this object
	 * through the widget foreign key attribute.
	 *
	 * @param      widget $l widget
	 * @return     void
	 * @throws     PropelException
	 */
	public function addwidget(widget $l)
	{
		if ($this->collwidgets === null) {
			$this->initwidgets();
		}
		if (!in_array($l, $this->collwidgets, true)) { // only add it if the **same** object is not already associated
			array_push($this->collwidgets, $l);
			$l->setuiConf($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this uiConf is new, it will return
	 * an empty collection; or if this uiConf has previously
	 * been saved, it will retrieve related widgets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in uiConf.
	 */
	public function getwidgetsJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(uiConfPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				$this->collwidgets = widgetPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

			if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
				$this->collwidgets = widgetPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastwidgetCriteria = $criteria;

		return $this->collwidgets;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this uiConf is new, it will return
	 * an empty collection; or if this uiConf has previously
	 * been saved, it will retrieve related widgets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in uiConf.
	 */
	public function getwidgetsJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(uiConfPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

				$this->collwidgets = widgetPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(widgetPeer::UI_CONF_ID, $this->id);

			if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
				$this->collwidgets = widgetPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastwidgetCriteria = $criteria;

		return $this->collwidgets;
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
			if ($this->collwidgets) {
				foreach ((array) $this->collwidgets as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collwidgets = null;
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
	
} // BaseuiConf
