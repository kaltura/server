<?php

/**
 * Base class that represents a row from the 'kce_installation_error' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseKceInstallationError extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        KceInstallationErrorPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the browser field.
	 * @var        string
	 */
	protected $browser;

	/**
	 * The value for the server_ip field.
	 * @var        string
	 */
	protected $server_ip;

	/**
	 * The value for the server_os field.
	 * @var        string
	 */
	protected $server_os;

	/**
	 * The value for the php_version field.
	 * @var        string
	 */
	protected $php_version;

	/**
	 * The value for the ce_admin_email field.
	 * @var        string
	 */
	protected $ce_admin_email;

	/**
	 * The value for the type field.
	 * @var        string
	 */
	protected $type;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the data field.
	 * @var        string
	 */
	protected $data;

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
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
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
	 * Get the [browser] column value.
	 * 
	 * @return     string
	 */
	public function getBrowser()
	{
		return $this->browser;
	}

	/**
	 * Get the [server_ip] column value.
	 * 
	 * @return     string
	 */
	public function getServerIp()
	{
		return $this->server_ip;
	}

	/**
	 * Get the [server_os] column value.
	 * 
	 * @return     string
	 */
	public function getServerOs()
	{
		return $this->server_os;
	}

	/**
	 * Get the [php_version] column value.
	 * 
	 * @return     string
	 */
	public function getPhpVersion()
	{
		return $this->php_version;
	}

	/**
	 * Get the [ce_admin_email] column value.
	 * 
	 * @return     string
	 */
	public function getCeAdminEmail()
	{
		return $this->ce_admin_email;
	}

	/**
	 * Get the [type] column value.
	 * 
	 * @return     string
	 */
	public function getType()
	{
		return $this->type;
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
	 * Get the [data] column value.
	 * 
	 * @return     string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::ID]))
			$this->oldColumnsValues[KceInstallationErrorPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::PARTNER_ID]))
			$this->oldColumnsValues[KceInstallationErrorPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [browser] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setBrowser($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::BROWSER]))
			$this->oldColumnsValues[KceInstallationErrorPeer::BROWSER] = $this->browser;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->browser !== $v) {
			$this->browser = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::BROWSER;
		}

		return $this;
	} // setBrowser()

	/**
	 * Set the value of [server_ip] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setServerIp($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::SERVER_IP]))
			$this->oldColumnsValues[KceInstallationErrorPeer::SERVER_IP] = $this->server_ip;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->server_ip !== $v) {
			$this->server_ip = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::SERVER_IP;
		}

		return $this;
	} // setServerIp()

	/**
	 * Set the value of [server_os] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setServerOs($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::SERVER_OS]))
			$this->oldColumnsValues[KceInstallationErrorPeer::SERVER_OS] = $this->server_os;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->server_os !== $v) {
			$this->server_os = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::SERVER_OS;
		}

		return $this;
	} // setServerOs()

	/**
	 * Set the value of [php_version] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setPhpVersion($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::PHP_VERSION]))
			$this->oldColumnsValues[KceInstallationErrorPeer::PHP_VERSION] = $this->php_version;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->php_version !== $v) {
			$this->php_version = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::PHP_VERSION;
		}

		return $this;
	} // setPhpVersion()

	/**
	 * Set the value of [ce_admin_email] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setCeAdminEmail($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::CE_ADMIN_EMAIL]))
			$this->oldColumnsValues[KceInstallationErrorPeer::CE_ADMIN_EMAIL] = $this->ce_admin_email;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ce_admin_email !== $v) {
			$this->ce_admin_email = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::CE_ADMIN_EMAIL;
		}

		return $this;
	} // setCeAdminEmail()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::TYPE]))
			$this->oldColumnsValues[KceInstallationErrorPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::DESCRIPTION]))
			$this->oldColumnsValues[KceInstallationErrorPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     KceInstallationError The current object (for fluent API support)
	 */
	public function setData($v)
	{
		if(!isset($this->oldColumnsValues[KceInstallationErrorPeer::DATA]))
			$this->oldColumnsValues[KceInstallationErrorPeer::DATA] = $this->data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->data !== $v) {
			$this->data = $v;
			$this->modifiedColumns[] = KceInstallationErrorPeer::DATA;
		}

		return $this;
	} // setData()

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
			$this->partner_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->browser = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->server_ip = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->server_os = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->php_version = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->ce_admin_email = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->type = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->description = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->data = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = KceInstallationErrorPeer::NUM_COLUMNS - KceInstallationErrorPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating KceInstallationError object", $e);
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
			$con = Propel::getConnection(KceInstallationErrorPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		KceInstallationErrorPeer::setUseCriteriaFilter(false);
		$criteria = $this->buildPkeyCriteria();
		KceInstallationErrorPeer::addSelectColumns($criteria);
		$stmt = BasePeer::doSelect($criteria, $con);
		KceInstallationErrorPeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(KceInstallationErrorPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				KceInstallationErrorPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(KceInstallationErrorPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				KceInstallationErrorPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = KceInstallationErrorPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = KceInstallationErrorPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = KceInstallationErrorPeer::doUpdate($this, $con);
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


			if (($retval = KceInstallationErrorPeer::doValidate($this, $columns)) !== true) {
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
		$pos = KceInstallationErrorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getPartnerId();
				break;
			case 2:
				return $this->getBrowser();
				break;
			case 3:
				return $this->getServerIp();
				break;
			case 4:
				return $this->getServerOs();
				break;
			case 5:
				return $this->getPhpVersion();
				break;
			case 6:
				return $this->getCeAdminEmail();
				break;
			case 7:
				return $this->getType();
				break;
			case 8:
				return $this->getDescription();
				break;
			case 9:
				return $this->getData();
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
		$keys = KceInstallationErrorPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getBrowser(),
			$keys[3] => $this->getServerIp(),
			$keys[4] => $this->getServerOs(),
			$keys[5] => $this->getPhpVersion(),
			$keys[6] => $this->getCeAdminEmail(),
			$keys[7] => $this->getType(),
			$keys[8] => $this->getDescription(),
			$keys[9] => $this->getData(),
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
		$pos = KceInstallationErrorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setPartnerId($value);
				break;
			case 2:
				$this->setBrowser($value);
				break;
			case 3:
				$this->setServerIp($value);
				break;
			case 4:
				$this->setServerOs($value);
				break;
			case 5:
				$this->setPhpVersion($value);
				break;
			case 6:
				$this->setCeAdminEmail($value);
				break;
			case 7:
				$this->setType($value);
				break;
			case 8:
				$this->setDescription($value);
				break;
			case 9:
				$this->setData($value);
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
		$keys = KceInstallationErrorPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setBrowser($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setServerIp($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setServerOs($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPhpVersion($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setCeAdminEmail($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setType($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDescription($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setData($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(KceInstallationErrorPeer::DATABASE_NAME);

		if ($this->isColumnModified(KceInstallationErrorPeer::ID)) $criteria->add(KceInstallationErrorPeer::ID, $this->id);
		if ($this->isColumnModified(KceInstallationErrorPeer::PARTNER_ID)) $criteria->add(KceInstallationErrorPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(KceInstallationErrorPeer::BROWSER)) $criteria->add(KceInstallationErrorPeer::BROWSER, $this->browser);
		if ($this->isColumnModified(KceInstallationErrorPeer::SERVER_IP)) $criteria->add(KceInstallationErrorPeer::SERVER_IP, $this->server_ip);
		if ($this->isColumnModified(KceInstallationErrorPeer::SERVER_OS)) $criteria->add(KceInstallationErrorPeer::SERVER_OS, $this->server_os);
		if ($this->isColumnModified(KceInstallationErrorPeer::PHP_VERSION)) $criteria->add(KceInstallationErrorPeer::PHP_VERSION, $this->php_version);
		if ($this->isColumnModified(KceInstallationErrorPeer::CE_ADMIN_EMAIL)) $criteria->add(KceInstallationErrorPeer::CE_ADMIN_EMAIL, $this->ce_admin_email);
		if ($this->isColumnModified(KceInstallationErrorPeer::TYPE)) $criteria->add(KceInstallationErrorPeer::TYPE, $this->type);
		if ($this->isColumnModified(KceInstallationErrorPeer::DESCRIPTION)) $criteria->add(KceInstallationErrorPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(KceInstallationErrorPeer::DATA)) $criteria->add(KceInstallationErrorPeer::DATA, $this->data);

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
		$criteria = new Criteria(KceInstallationErrorPeer::DATABASE_NAME);

		$criteria->add(KceInstallationErrorPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of KceInstallationError (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setBrowser($this->browser);

		$copyObj->setServerIp($this->server_ip);

		$copyObj->setServerOs($this->server_os);

		$copyObj->setPhpVersion($this->php_version);

		$copyObj->setCeAdminEmail($this->ce_admin_email);

		$copyObj->setType($this->type);

		$copyObj->setDescription($this->description);

		$copyObj->setData($this->data);


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
	 * @return     KceInstallationError Clone of current object.
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
	 * @var     KceInstallationError Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      KceInstallationError $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(KceInstallationError $copiedFrom)
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
	 * @return     KceInstallationErrorPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new KceInstallationErrorPeer();
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

} // BaseKceInstallationError
