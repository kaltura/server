<?php

/**
 * Base class that represents a row from the 'sphinx_log' table.
 *
 * 
 *
 * @package plugins.search
 * @subpackage model.om
 */
abstract class BaseSphinxLog extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        SphinxLogPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the executed_server_id field.
	 * @var        int
	 */
	protected $executed_server_id;

	/**
	 * The value for the object_type field.
	 * @var        string
	 */
	protected $object_type;

	/**
	 * The value for the object_id field.
	 * @var        string
	 */
	protected $object_id;

	/**
	 * The value for the entry_id field.
	 * @var        string
	 */
	protected $entry_id;

	/**
	 * The value for the partner_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the dc field.
	 * @var        int
	 */
	protected $dc;

	/**
	 * The value for the sql field.
	 * @var        string
	 */
	protected $sql;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * @var        array SphinxLogServer[] Collection to store aggregation of SphinxLogServer objects.
	 */
	protected $collSphinxLogServers;

	/**
	 * @var        Criteria The criteria used to select the current contents of collSphinxLogServers.
	 */
	private $lastSphinxLogServerCriteria = null;

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
		$this->partner_id = 0;
	}

	/**
	 * Initializes internal state of BaseSphinxLog object.
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
	 * Get the [executed_server_id] column value.
	 * 
	 * @return     int
	 */
	public function getExecutedServerId()
	{
		return $this->executed_server_id;
	}

	/**
	 * Get the [object_type] column value.
	 * 
	 * @return     string
	 */
	public function getObjectType()
	{
		return $this->object_type;
	}

	/**
	 * Get the [object_id] column value.
	 * 
	 * @return     string
	 */
	public function getObjectId()
	{
		return $this->object_id;
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
	 * Get the [dc] column value.
	 * 
	 * @return     int
	 */
	public function getDc()
	{
		return $this->dc;
	}

	/**
	 * Get the [sql] column value.
	 * 
	 * @return     string
	 */
	public function getSql()
	{
		return $this->sql;
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
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::ID]))
			$this->oldColumnsValues[SphinxLogPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = SphinxLogPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [executed_server_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setExecutedServerId($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::EXECUTED_SERVER_ID]))
			$this->oldColumnsValues[SphinxLogPeer::EXECUTED_SERVER_ID] = $this->executed_server_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->executed_server_id !== $v) {
			$this->executed_server_id = $v;
			$this->modifiedColumns[] = SphinxLogPeer::EXECUTED_SERVER_ID;
		}

		return $this;
	} // setExecutedServerId()

	/**
	 * Set the value of [object_type] column.
	 * 
	 * @param      string $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setObjectType($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::OBJECT_TYPE]))
			$this->oldColumnsValues[SphinxLogPeer::OBJECT_TYPE] = $this->object_type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_type !== $v) {
			$this->object_type = $v;
			$this->modifiedColumns[] = SphinxLogPeer::OBJECT_TYPE;
		}

		return $this;
	} // setObjectType()

	/**
	 * Set the value of [object_id] column.
	 * 
	 * @param      string $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setObjectId($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::OBJECT_ID]))
			$this->oldColumnsValues[SphinxLogPeer::OBJECT_ID] = $this->object_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->object_id !== $v) {
			$this->object_id = $v;
			$this->modifiedColumns[] = SphinxLogPeer::OBJECT_ID;
		}

		return $this;
	} // setObjectId()

	/**
	 * Set the value of [entry_id] column.
	 * 
	 * @param      string $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setEntryId($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::ENTRY_ID]))
			$this->oldColumnsValues[SphinxLogPeer::ENTRY_ID] = $this->entry_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->entry_id !== $v) {
			$this->entry_id = $v;
			$this->modifiedColumns[] = SphinxLogPeer::ENTRY_ID;
		}

		return $this;
	} // setEntryId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::PARTNER_ID]))
			$this->oldColumnsValues[SphinxLogPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = SphinxLogPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::DC]))
			$this->oldColumnsValues[SphinxLogPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = SphinxLogPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [sql] column.
	 * 
	 * @param      string $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setSql($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::SQL]))
			$this->oldColumnsValues[SphinxLogPeer::SQL] = $this->sql;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->sql !== $v) {
			$this->sql = $v;
			$this->modifiedColumns[] = SphinxLogPeer::SQL;
		}

		return $this;
	} // setSql()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     SphinxLog The current object (for fluent API support)
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
				$this->modifiedColumns[] = SphinxLogPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     SphinxLog The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[SphinxLogPeer::TYPE]))
			$this->oldColumnsValues[SphinxLogPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = SphinxLogPeer::TYPE;
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
			$this->executed_server_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->object_type = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->object_id = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->entry_id = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->partner_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->dc = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->sql = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->type = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = SphinxLogPeer::NUM_COLUMNS - SphinxLogPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating SphinxLog object", $e);
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
			$con = Propel::getConnection(SphinxLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		SphinxLogPeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		SphinxLogPeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		SphinxLogPeer::setUseCriteriaFilter(true);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collSphinxLogServers = null;
			$this->lastSphinxLogServerCriteria = null;

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
			$con = Propel::getConnection(SphinxLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				SphinxLogPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(SphinxLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				SphinxLogPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = SphinxLogPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SphinxLogPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = SphinxLogPeer::doUpdate($this, $con);
					if($affectedObjects)
						$this->objectSaved = true;
						
					$affectedRows += $affectedObjects;
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collSphinxLogServers !== null) {
				foreach ($this->collSphinxLogServers as $referrerFK) {
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
	 * @return boolean
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
		kEventsManager::raiseEvent(new kObjectSavedEvent($this));
		$this->oldColumnsValues = array(); 
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
		return parent::preInsert($con);
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		kQueryCache::invalidateQueryCache($this);
		
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
	
		kQueryCache::invalidateQueryCache($this);
		
		parent::postUpdate($con);
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


			if (($retval = SphinxLogPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collSphinxLogServers !== null) {
					foreach ($this->collSphinxLogServers as $referrerFK) {
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
		$pos = SphinxLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getExecutedServerId();
				break;
			case 2:
				return $this->getObjectType();
				break;
			case 3:
				return $this->getObjectId();
				break;
			case 4:
				return $this->getEntryId();
				break;
			case 5:
				return $this->getPartnerId();
				break;
			case 6:
				return $this->getDc();
				break;
			case 7:
				return $this->getSql();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			case 9:
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
		$keys = SphinxLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getExecutedServerId(),
			$keys[2] => $this->getObjectType(),
			$keys[3] => $this->getObjectId(),
			$keys[4] => $this->getEntryId(),
			$keys[5] => $this->getPartnerId(),
			$keys[6] => $this->getDc(),
			$keys[7] => $this->getSql(),
			$keys[8] => $this->getCreatedAt(),
			$keys[9] => $this->getType(),
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
		$pos = SphinxLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setExecutedServerId($value);
				break;
			case 2:
				$this->setObjectType($value);
				break;
			case 3:
				$this->setObjectId($value);
				break;
			case 4:
				$this->setEntryId($value);
				break;
			case 5:
				$this->setPartnerId($value);
				break;
			case 6:
				$this->setDc($value);
				break;
			case 7:
				$this->setSql($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
			case 9:
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
		$keys = SphinxLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setExecutedServerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setObjectType($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjectId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEntryId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPartnerId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDc($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSql($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setType($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(SphinxLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(SphinxLogPeer::ID)) $criteria->add(SphinxLogPeer::ID, $this->id);
		if ($this->isColumnModified(SphinxLogPeer::EXECUTED_SERVER_ID)) $criteria->add(SphinxLogPeer::EXECUTED_SERVER_ID, $this->executed_server_id);
		if ($this->isColumnModified(SphinxLogPeer::OBJECT_TYPE)) $criteria->add(SphinxLogPeer::OBJECT_TYPE, $this->object_type);
		if ($this->isColumnModified(SphinxLogPeer::OBJECT_ID)) $criteria->add(SphinxLogPeer::OBJECT_ID, $this->object_id);
		if ($this->isColumnModified(SphinxLogPeer::ENTRY_ID)) $criteria->add(SphinxLogPeer::ENTRY_ID, $this->entry_id);
		if ($this->isColumnModified(SphinxLogPeer::PARTNER_ID)) $criteria->add(SphinxLogPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(SphinxLogPeer::DC)) $criteria->add(SphinxLogPeer::DC, $this->dc);
		if ($this->isColumnModified(SphinxLogPeer::SQL)) $criteria->add(SphinxLogPeer::SQL, $this->sql);
		if ($this->isColumnModified(SphinxLogPeer::CREATED_AT)) $criteria->add(SphinxLogPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(SphinxLogPeer::TYPE)) $criteria->add(SphinxLogPeer::TYPE, $this->type);

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
		$criteria = new Criteria(SphinxLogPeer::DATABASE_NAME);

		$criteria->add(SphinxLogPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of SphinxLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setExecutedServerId($this->executed_server_id);

		$copyObj->setObjectType($this->object_type);

		$copyObj->setObjectId($this->object_id);

		$copyObj->setEntryId($this->entry_id);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDc($this->dc);

		$copyObj->setSql($this->sql);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setType($this->type);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getSphinxLogServers() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addSphinxLogServer($relObj->copy($deepCopy));
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
	 * @return     SphinxLog Clone of current object.
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
	 * @var     SphinxLog Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      SphinxLog $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(SphinxLog $copiedFrom)
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
	 * @return     SphinxLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new SphinxLogPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collSphinxLogServers collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addSphinxLogServers()
	 */
	public function clearSphinxLogServers()
	{
		$this->collSphinxLogServers = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collSphinxLogServers collection (array).
	 *
	 * By default this just sets the collSphinxLogServers collection to an empty array (like clearcollSphinxLogServers());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initSphinxLogServers()
	{
		$this->collSphinxLogServers = array();
	}

	/**
	 * Gets an array of SphinxLogServer objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this SphinxLog has previously been saved, it will retrieve
	 * related SphinxLogServers from storage. If this SphinxLog is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array SphinxLogServer[]
	 * @throws     PropelException
	 */
	public function getSphinxLogServers($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(SphinxLogPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collSphinxLogServers === null) {
			if ($this->isNew()) {
			   $this->collSphinxLogServers = array();
			} else {

				$criteria->add(SphinxLogServerPeer::LAST_LOG_ID, $this->id);

				SphinxLogServerPeer::addSelectColumns($criteria);
				$this->collSphinxLogServers = SphinxLogServerPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(SphinxLogServerPeer::LAST_LOG_ID, $this->id);

				SphinxLogServerPeer::addSelectColumns($criteria);
				if (!isset($this->lastSphinxLogServerCriteria) || !$this->lastSphinxLogServerCriteria->equals($criteria)) {
					$this->collSphinxLogServers = SphinxLogServerPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastSphinxLogServerCriteria = $criteria;
		return $this->collSphinxLogServers;
	}

	/**
	 * Returns the number of related SphinxLogServer objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related SphinxLogServer objects.
	 * @throws     PropelException
	 */
	public function countSphinxLogServers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(SphinxLogPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collSphinxLogServers === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(SphinxLogServerPeer::LAST_LOG_ID, $this->id);

				$count = SphinxLogServerPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(SphinxLogServerPeer::LAST_LOG_ID, $this->id);

				if (!isset($this->lastSphinxLogServerCriteria) || !$this->lastSphinxLogServerCriteria->equals($criteria)) {
					$count = SphinxLogServerPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collSphinxLogServers);
				}
			} else {
				$count = count($this->collSphinxLogServers);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a SphinxLogServer object to this object
	 * through the SphinxLogServer foreign key attribute.
	 *
	 * @param      SphinxLogServer $l SphinxLogServer
	 * @return     void
	 * @throws     PropelException
	 */
	public function addSphinxLogServer(SphinxLogServer $l)
	{
		if ($this->collSphinxLogServers === null) {
			$this->initSphinxLogServers();
		}
		if (!in_array($l, $this->collSphinxLogServers, true)) { // only add it if the **same** object is not already associated
			array_push($this->collSphinxLogServers, $l);
			$l->setSphinxLog($this);
		}
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
			if ($this->collSphinxLogServers) {
				foreach ((array) $this->collSphinxLogServers as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collSphinxLogServers = null;
	}

} // BaseSphinxLog
