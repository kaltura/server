<?php

/**
 * Base class that represents a row from the 'favorite' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class Basefavorite extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        favoritePeer
	 */
	protected static $peer;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the subject_type field.
	 * @var        int
	 */
	protected $subject_type;

	/**
	 * The value for the subject_id field.
	 * @var        int
	 */
	protected $subject_id;

	/**
	 * The value for the privacy field.
	 * @var        int
	 */
	protected $privacy;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

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
	 * Get the [kuser_id] column value.
	 * 
	 * @return     int
	 */
	public function getKuserId()
	{
		return $this->kuser_id;
	}

	/**
	 * Get the [subject_type] column value.
	 * 
	 * @return     int
	 */
	public function getSubjectType()
	{
		return $this->subject_type;
	}

	/**
	 * Get the [subject_id] column value.
	 * 
	 * @return     int
	 */
	public function getSubjectId()
	{
		return $this->subject_id;
	}

	/**
	 * Get the [privacy] column value.
	 * 
	 * @return     int
	 */
	public function getPrivacy()
	{
		return $this->privacy;
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
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     favorite The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[favoritePeer::KUSER_ID]))
			$this->oldColumnsValues[favoritePeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = favoritePeer::KUSER_ID;
		}

		if ($this->akuser !== null && $this->akuser->getId() !== $v) {
			$this->akuser = null;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [subject_type] column.
	 * 
	 * @param      int $v new value
	 * @return     favorite The current object (for fluent API support)
	 */
	public function setSubjectType($v)
	{
		if(!isset($this->oldColumnsValues[favoritePeer::SUBJECT_TYPE]))
			$this->oldColumnsValues[favoritePeer::SUBJECT_TYPE] = $this->subject_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subject_type !== $v) {
			$this->subject_type = $v;
			$this->modifiedColumns[] = favoritePeer::SUBJECT_TYPE;
		}

		return $this;
	} // setSubjectType()

	/**
	 * Set the value of [subject_id] column.
	 * 
	 * @param      int $v new value
	 * @return     favorite The current object (for fluent API support)
	 */
	public function setSubjectId($v)
	{
		if(!isset($this->oldColumnsValues[favoritePeer::SUBJECT_ID]))
			$this->oldColumnsValues[favoritePeer::SUBJECT_ID] = $this->subject_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subject_id !== $v) {
			$this->subject_id = $v;
			$this->modifiedColumns[] = favoritePeer::SUBJECT_ID;
		}

		return $this;
	} // setSubjectId()

	/**
	 * Set the value of [privacy] column.
	 * 
	 * @param      int $v new value
	 * @return     favorite The current object (for fluent API support)
	 */
	public function setPrivacy($v)
	{
		if(!isset($this->oldColumnsValues[favoritePeer::PRIVACY]))
			$this->oldColumnsValues[favoritePeer::PRIVACY] = $this->privacy;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->privacy !== $v) {
			$this->privacy = $v;
			$this->modifiedColumns[] = favoritePeer::PRIVACY;
		}

		return $this;
	} // setPrivacy()

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     favorite The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[favoritePeer::ID]))
			$this->oldColumnsValues[favoritePeer::ID] = $this->id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = favoritePeer::ID;
		}

		return $this;
	} // setId()

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

			$this->kuser_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->subject_type = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->subject_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->privacy = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = favoritePeer::NUM_COLUMNS - favoritePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating favorite object", $e);
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

		if ($this->akuser !== null && $this->kuser_id !== $this->akuser->getId()) {
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
			$con = Propel::getConnection(favoritePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = favoritePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(favoritePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				favoritePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(favoritePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				favoritePeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = favoritePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = favoritePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += favoritePeer::doUpdate($this, $con);
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
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		favoritePeer::setUseCriteriaFilter(false);
		$this->reload();
		favoritePeer::setUseCriteriaFilter(true);
		
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


			if (($retval = favoritePeer::doValidate($this, $columns)) !== true) {
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
		$pos = favoritePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getKuserId();
				break;
			case 1:
				return $this->getSubjectType();
				break;
			case 2:
				return $this->getSubjectId();
				break;
			case 3:
				return $this->getPrivacy();
				break;
			case 4:
				return $this->getId();
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
		$keys = favoritePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getKuserId(),
			$keys[1] => $this->getSubjectType(),
			$keys[2] => $this->getSubjectId(),
			$keys[3] => $this->getPrivacy(),
			$keys[4] => $this->getId(),
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
		$pos = favoritePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setKuserId($value);
				break;
			case 1:
				$this->setSubjectType($value);
				break;
			case 2:
				$this->setSubjectId($value);
				break;
			case 3:
				$this->setPrivacy($value);
				break;
			case 4:
				$this->setId($value);
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
		$keys = favoritePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setKuserId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSubjectType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setSubjectId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPrivacy($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setId($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(favoritePeer::DATABASE_NAME);

		if ($this->isColumnModified(favoritePeer::KUSER_ID)) $criteria->add(favoritePeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(favoritePeer::SUBJECT_TYPE)) $criteria->add(favoritePeer::SUBJECT_TYPE, $this->subject_type);
		if ($this->isColumnModified(favoritePeer::SUBJECT_ID)) $criteria->add(favoritePeer::SUBJECT_ID, $this->subject_id);
		if ($this->isColumnModified(favoritePeer::PRIVACY)) $criteria->add(favoritePeer::PRIVACY, $this->privacy);
		if ($this->isColumnModified(favoritePeer::ID)) $criteria->add(favoritePeer::ID, $this->id);

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
		$criteria = new Criteria(favoritePeer::DATABASE_NAME);

		$criteria->add(favoritePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of favorite (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setSubjectType($this->subject_type);

		$copyObj->setSubjectId($this->subject_id);

		$copyObj->setPrivacy($this->privacy);


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
	 * @return     favorite Clone of current object.
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
	 * @var     favorite Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      favorite $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(favorite $copiedFrom)
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
	 * @return     favoritePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new favoritePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a kuser object.
	 *
	 * @param      kuser $v
	 * @return     favorite The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setkuser(kuser $v = null)
	{
		if ($v === null) {
			$this->setKuserId(NULL);
		} else {
			$this->setKuserId($v->getId());
		}

		$this->akuser = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the kuser object, it will not be re-added.
		if ($v !== null) {
			$v->addfavorite($this);
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
		if ($this->akuser === null && ($this->kuser_id !== null)) {
			$this->akuser = kuserPeer::retrieveByPk($this->kuser_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->akuser->addfavorites($this);
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

} // Basefavorite
