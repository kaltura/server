<?php

/**
 * Base class that represents a row from the 'partner_load' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasePartnerLoad extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PartnerLoadPeer
	 */
	protected static $peer;

	/**
	 * The value for the job_type field.
	 * @var        int
	 */
	protected $job_type;

	/**
	 * The value for the job_sub_type field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $job_sub_type;

	/**
	 * The value for the partner_id field.
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the dc field.
	 * @var        int
	 */
	protected $dc;

	/**
	 * The value for the partner_load field.
	 * @var        int
	 */
	protected $partner_load;

	/**
	 * The value for the weighted_partner_load field.
	 * @var        int
	 */
	protected $weighted_partner_load;

	/**
	 * The value for the quota field.
	 * @var        int
	 */
	protected $quota;

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
		$this->job_sub_type = 0;
	}

	/**
	 * Initializes internal state of BasePartnerLoad object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [job_type] column value.
	 * 
	 * @return     int
	 */
	public function getJobType()
	{
		return $this->job_type;
	}

	/**
	 * Get the [job_sub_type] column value.
	 * 
	 * @return     int
	 */
	public function getJobSubType()
	{
		return $this->job_sub_type;
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
	 * Get the [partner_load] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerLoad()
	{
		return $this->partner_load;
	}

	/**
	 * Get the [weighted_partner_load] column value.
	 * 
	 * @return     int
	 */
	public function getWeightedPartnerLoad()
	{
		return $this->weighted_partner_load;
	}

	/**
	 * Get the [quota] column value.
	 * 
	 * @return     int
	 */
	public function getQuota()
	{
		return $this->quota;
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
	 * Set the value of [job_type] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setJobType($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::JOB_TYPE]))
			$this->oldColumnsValues[PartnerLoadPeer::JOB_TYPE] = $this->job_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_type !== $v) {
			$this->job_type = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::JOB_TYPE;
		}

		return $this;
	} // setJobType()

	/**
	 * Set the value of [job_sub_type] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setJobSubType($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::JOB_SUB_TYPE]))
			$this->oldColumnsValues[PartnerLoadPeer::JOB_SUB_TYPE] = $this->job_sub_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->job_sub_type !== $v || $this->isNew()) {
			$this->job_sub_type = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::JOB_SUB_TYPE;
		}

		return $this;
	} // setJobSubType()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::PARTNER_ID]))
			$this->oldColumnsValues[PartnerLoadPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [dc] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setDc($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::DC]))
			$this->oldColumnsValues[PartnerLoadPeer::DC] = $this->dc;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->dc !== $v) {
			$this->dc = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::DC;
		}

		return $this;
	} // setDc()

	/**
	 * Set the value of [partner_load] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setPartnerLoad($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::PARTNER_LOAD]))
			$this->oldColumnsValues[PartnerLoadPeer::PARTNER_LOAD] = $this->partner_load;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_load !== $v) {
			$this->partner_load = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::PARTNER_LOAD;
		}

		return $this;
	} // setPartnerLoad()

	/**
	 * Set the value of [weighted_partner_load] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setWeightedPartnerLoad($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::WEIGHTED_PARTNER_LOAD]))
			$this->oldColumnsValues[PartnerLoadPeer::WEIGHTED_PARTNER_LOAD] = $this->weighted_partner_load;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->weighted_partner_load !== $v) {
			$this->weighted_partner_load = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::WEIGHTED_PARTNER_LOAD;
		}

		return $this;
	} // setWeightedPartnerLoad()

	/**
	 * Set the value of [quota] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setQuota($v)
	{
		if(!isset($this->oldColumnsValues[PartnerLoadPeer::QUOTA]))
			$this->oldColumnsValues[PartnerLoadPeer::QUOTA] = $this->quota;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->quota !== $v) {
			$this->quota = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::QUOTA;
		}

		return $this;
	} // setQuota()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     PartnerLoad The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = PartnerLoadPeer::CUSTOM_DATA;
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
			if ($this->job_sub_type !== 0) {
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

			$this->job_type = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->job_sub_type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->partner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->dc = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->partner_load = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->weighted_partner_load = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->quota = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->custom_data = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = PartnerLoadPeer::NUM_COLUMNS - PartnerLoadPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PartnerLoad object", $e);
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
			$con = Propel::getConnection(PartnerLoadPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		PartnerLoadPeer::setUseCriteriaFilter(false);
		$stmt = PartnerLoadPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		PartnerLoadPeer::setUseCriteriaFilter(true);
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
			$con = Propel::getConnection(PartnerLoadPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				PartnerLoadPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PartnerLoadPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				PartnerLoadPeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			$this->objectSaved = false;
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PartnerLoadPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
					$this->objectSaved = true;
				} else {
					$affectedObjects = PartnerLoadPeer::doUpdate($this, $con);
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


			if (($retval = PartnerLoadPeer::doValidate($this, $columns)) !== true) {
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
		$pos = PartnerLoadPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getJobType();
				break;
			case 1:
				return $this->getJobSubType();
				break;
			case 2:
				return $this->getPartnerId();
				break;
			case 3:
				return $this->getDc();
				break;
			case 4:
				return $this->getPartnerLoad();
				break;
			case 5:
				return $this->getWeightedPartnerLoad();
				break;
			case 6:
				return $this->getQuota();
				break;
			case 7:
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
		$keys = PartnerLoadPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getJobType(),
			$keys[1] => $this->getJobSubType(),
			$keys[2] => $this->getPartnerId(),
			$keys[3] => $this->getDc(),
			$keys[4] => $this->getPartnerLoad(),
			$keys[5] => $this->getWeightedPartnerLoad(),
			$keys[6] => $this->getQuota(),
			$keys[7] => $this->getCustomData(),
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
		$pos = PartnerLoadPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setJobType($value);
				break;
			case 1:
				$this->setJobSubType($value);
				break;
			case 2:
				$this->setPartnerId($value);
				break;
			case 3:
				$this->setDc($value);
				break;
			case 4:
				$this->setPartnerLoad($value);
				break;
			case 5:
				$this->setWeightedPartnerLoad($value);
				break;
			case 6:
				$this->setQuota($value);
				break;
			case 7:
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
		$keys = PartnerLoadPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setJobType($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setJobSubType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPartnerId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDc($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPartnerLoad($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setWeightedPartnerLoad($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setQuota($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCustomData($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PartnerLoadPeer::DATABASE_NAME);

		if ($this->isColumnModified(PartnerLoadPeer::JOB_TYPE)) $criteria->add(PartnerLoadPeer::JOB_TYPE, $this->job_type);
		if ($this->isColumnModified(PartnerLoadPeer::JOB_SUB_TYPE)) $criteria->add(PartnerLoadPeer::JOB_SUB_TYPE, $this->job_sub_type);
		if ($this->isColumnModified(PartnerLoadPeer::PARTNER_ID)) $criteria->add(PartnerLoadPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(PartnerLoadPeer::DC)) $criteria->add(PartnerLoadPeer::DC, $this->dc);
		if ($this->isColumnModified(PartnerLoadPeer::PARTNER_LOAD)) $criteria->add(PartnerLoadPeer::PARTNER_LOAD, $this->partner_load);
		if ($this->isColumnModified(PartnerLoadPeer::WEIGHTED_PARTNER_LOAD)) $criteria->add(PartnerLoadPeer::WEIGHTED_PARTNER_LOAD, $this->weighted_partner_load);
		if ($this->isColumnModified(PartnerLoadPeer::QUOTA)) $criteria->add(PartnerLoadPeer::QUOTA, $this->quota);
		if ($this->isColumnModified(PartnerLoadPeer::CUSTOM_DATA)) $criteria->add(PartnerLoadPeer::CUSTOM_DATA, $this->custom_data);

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
		$criteria = new Criteria(PartnerLoadPeer::DATABASE_NAME);

		$criteria->add(PartnerLoadPeer::JOB_TYPE, $this->job_type);
		$criteria->add(PartnerLoadPeer::JOB_SUB_TYPE, $this->job_sub_type);
		$criteria->add(PartnerLoadPeer::PARTNER_ID, $this->partner_id);
		$criteria->add(PartnerLoadPeer::DC, $this->dc);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getJobType();

		$pks[1] = $this->getJobSubType();

		$pks[2] = $this->getPartnerId();

		$pks[3] = $this->getDc();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{

		$this->setJobType($keys[0]);

		$this->setJobSubType($keys[1]);

		$this->setPartnerId($keys[2]);

		$this->setDc($keys[3]);

	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of PartnerLoad (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setJobType($this->job_type);

		$copyObj->setJobSubType($this->job_sub_type);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDc($this->dc);

		$copyObj->setPartnerLoad($this->partner_load);

		$copyObj->setWeightedPartnerLoad($this->weighted_partner_load);

		$copyObj->setQuota($this->quota);

		$copyObj->setCustomData($this->custom_data);


		$copyObj->setNew(true);

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
	 * @return     PartnerLoad Clone of current object.
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
	 * @var     PartnerLoad Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      PartnerLoad $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(PartnerLoad $copiedFrom)
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
	 * @return     PartnerLoadPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PartnerLoadPeer();
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
	
} // BasePartnerLoad
