<?php

/**
 * Base class that represents a row from the 'partner_activity' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BasePartnerActivity extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PartnerActivityPeer
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
	 * The value for the activity_date field.
	 * @var        string
	 */
	protected $activity_date;

	/**
	 * The value for the activity field.
	 * @var        int
	 */
	protected $activity;

	/**
	 * The value for the sub_activity field.
	 * @var        int
	 */
	protected $sub_activity;

	/**
	 * The value for the amount field.
	 * @var        string
	 */
	protected $amount;

	/**
	 * The value for the amount1 field.
	 * @var        string
	 */
	protected $amount1;

	/**
	 * The value for the amount2 field.
	 * @var        string
	 */
	protected $amount2;

	/**
	 * The value for the amount3 field.
	 * @var        int
	 */
	protected $amount3;

	/**
	 * The value for the amount4 field.
	 * @var        int
	 */
	protected $amount4;

	/**
	 * The value for the amount5 field.
	 * @var        int
	 */
	protected $amount5;

	/**
	 * The value for the amount6 field.
	 * @var        int
	 */
	protected $amount6;

	/**
	 * The value for the amount7 field.
	 * @var        int
	 */
	protected $amount7;

	/**
	 * The value for the amount9 field.
	 * @var        int
	 */
	protected $amount9;

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
	 * Get the [optionally formatted] temporal [activity_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getActivityDate($format = '%x')
	{
		if ($this->activity_date === null) {
			return null;
		}


		if ($this->activity_date === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->activity_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->activity_date, true), $x);
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
	 * Get the [activity] column value.
	 * 
	 * @return     int
	 */
	public function getActivity()
	{
		return $this->activity;
	}

	/**
	 * Get the [sub_activity] column value.
	 * 
	 * @return     int
	 */
	public function getSubActivity()
	{
		return $this->sub_activity;
	}

	/**
	 * Get the [amount] column value.
	 * 
	 * @return     string
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * Get the [amount1] column value.
	 * 
	 * @return     string
	 */
	public function getAmount1()
	{
		return $this->amount1;
	}

	/**
	 * Get the [amount2] column value.
	 * 
	 * @return     string
	 */
	public function getAmount2()
	{
		return $this->amount2;
	}

	/**
	 * Get the [amount3] column value.
	 * 
	 * @return     int
	 */
	public function getAmount3()
	{
		return $this->amount3;
	}

	/**
	 * Get the [amount4] column value.
	 * 
	 * @return     int
	 */
	public function getAmount4()
	{
		return $this->amount4;
	}

	/**
	 * Get the [amount5] column value.
	 * 
	 * @return     int
	 */
	public function getAmount5()
	{
		return $this->amount5;
	}

	/**
	 * Get the [amount6] column value.
	 * 
	 * @return     int
	 */
	public function getAmount6()
	{
		return $this->amount6;
	}

	/**
	 * Get the [amount7] column value.
	 * 
	 * @return     int
	 */
	public function getAmount7()
	{
		return $this->amount7;
	}

	/**
	 * Get the [amount9] column value.
	 * 
	 * @return     int
	 */
	public function getAmount9()
	{
		return $this->amount9;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Sets the value of [activity_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setActivityDate($v)
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

		if ( $this->activity_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->activity_date !== null && $tmpDt = new DateTime($this->activity_date)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->activity_date = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = PartnerActivityPeer::ACTIVITY_DATE;
			}
		} // if either are not null

		return $this;
	} // setActivityDate()

	/**
	 * Set the value of [activity] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setActivity($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->activity !== $v) {
			$this->activity = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::ACTIVITY;
		}

		return $this;
	} // setActivity()

	/**
	 * Set the value of [sub_activity] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setSubActivity($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->sub_activity !== $v) {
			$this->sub_activity = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::SUB_ACTIVITY;
		}

		return $this;
	} // setSubActivity()

	/**
	 * Set the value of [amount] column.
	 * 
	 * @param      string $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->amount !== $v) {
			$this->amount = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT;
		}

		return $this;
	} // setAmount()

	/**
	 * Set the value of [amount1] column.
	 * 
	 * @param      string $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->amount1 !== $v) {
			$this->amount1 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT1;
		}

		return $this;
	} // setAmount1()

	/**
	 * Set the value of [amount2] column.
	 * 
	 * @param      string $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->amount2 !== $v) {
			$this->amount2 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT2;
		}

		return $this;
	} // setAmount2()

	/**
	 * Set the value of [amount3] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount3($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount3 !== $v) {
			$this->amount3 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT3;
		}

		return $this;
	} // setAmount3()

	/**
	 * Set the value of [amount4] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount4($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount4 !== $v) {
			$this->amount4 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT4;
		}

		return $this;
	} // setAmount4()

	/**
	 * Set the value of [amount5] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount5($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount5 !== $v) {
			$this->amount5 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT5;
		}

		return $this;
	} // setAmount5()

	/**
	 * Set the value of [amount6] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount6($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount6 !== $v) {
			$this->amount6 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT6;
		}

		return $this;
	} // setAmount6()

	/**
	 * Set the value of [amount7] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount7($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount7 !== $v) {
			$this->amount7 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT7;
		}

		return $this;
	} // setAmount7()

	/**
	 * Set the value of [amount9] column.
	 * 
	 * @param      int $v new value
	 * @return     PartnerActivity The current object (for fluent API support)
	 */
	public function setAmount9($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->amount9 !== $v) {
			$this->amount9 = $v;
			$this->modifiedColumns[] = PartnerActivityPeer::AMOUNT9;
		}

		return $this;
	} // setAmount9()

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
			$this->activity_date = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->activity = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->sub_activity = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->amount = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->amount1 = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->amount2 = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->amount3 = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->amount4 = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->amount5 = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->amount6 = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->amount7 = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->amount9 = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 14; // 14 = PartnerActivityPeer::NUM_COLUMNS - PartnerActivityPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PartnerActivity object", $e);
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
			$con = Propel::getConnection(PartnerActivityPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = PartnerActivityPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$con = Propel::getConnection(PartnerActivityPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				PartnerActivityPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PartnerActivityPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				PartnerActivityPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = PartnerActivityPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PartnerActivityPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += PartnerActivityPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

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
		PartnerActivityPeer::setUseCriteriaFilter(false);
		$this->reload();
		PartnerActivityPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = PartnerActivityPeer::doValidate($this, $columns)) !== true) {
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
		$pos = PartnerActivityPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getActivityDate();
				break;
			case 3:
				return $this->getActivity();
				break;
			case 4:
				return $this->getSubActivity();
				break;
			case 5:
				return $this->getAmount();
				break;
			case 6:
				return $this->getAmount1();
				break;
			case 7:
				return $this->getAmount2();
				break;
			case 8:
				return $this->getAmount3();
				break;
			case 9:
				return $this->getAmount4();
				break;
			case 10:
				return $this->getAmount5();
				break;
			case 11:
				return $this->getAmount6();
				break;
			case 12:
				return $this->getAmount7();
				break;
			case 13:
				return $this->getAmount9();
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
		$keys = PartnerActivityPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPartnerId(),
			$keys[2] => $this->getActivityDate(),
			$keys[3] => $this->getActivity(),
			$keys[4] => $this->getSubActivity(),
			$keys[5] => $this->getAmount(),
			$keys[6] => $this->getAmount1(),
			$keys[7] => $this->getAmount2(),
			$keys[8] => $this->getAmount3(),
			$keys[9] => $this->getAmount4(),
			$keys[10] => $this->getAmount5(),
			$keys[11] => $this->getAmount6(),
			$keys[12] => $this->getAmount7(),
			$keys[13] => $this->getAmount9(),
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
		$pos = PartnerActivityPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setActivityDate($value);
				break;
			case 3:
				$this->setActivity($value);
				break;
			case 4:
				$this->setSubActivity($value);
				break;
			case 5:
				$this->setAmount($value);
				break;
			case 6:
				$this->setAmount1($value);
				break;
			case 7:
				$this->setAmount2($value);
				break;
			case 8:
				$this->setAmount3($value);
				break;
			case 9:
				$this->setAmount4($value);
				break;
			case 10:
				$this->setAmount5($value);
				break;
			case 11:
				$this->setAmount6($value);
				break;
			case 12:
				$this->setAmount7($value);
				break;
			case 13:
				$this->setAmount9($value);
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
		$keys = PartnerActivityPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPartnerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setActivityDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setActivity($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSubActivity($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setAmount($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAmount1($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setAmount2($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAmount3($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setAmount4($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setAmount5($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setAmount6($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setAmount7($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setAmount9($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PartnerActivityPeer::DATABASE_NAME);

		if ($this->isColumnModified(PartnerActivityPeer::ID)) $criteria->add(PartnerActivityPeer::ID, $this->id);
		if ($this->isColumnModified(PartnerActivityPeer::PARTNER_ID)) $criteria->add(PartnerActivityPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(PartnerActivityPeer::ACTIVITY_DATE)) $criteria->add(PartnerActivityPeer::ACTIVITY_DATE, $this->activity_date);
		if ($this->isColumnModified(PartnerActivityPeer::ACTIVITY)) $criteria->add(PartnerActivityPeer::ACTIVITY, $this->activity);
		if ($this->isColumnModified(PartnerActivityPeer::SUB_ACTIVITY)) $criteria->add(PartnerActivityPeer::SUB_ACTIVITY, $this->sub_activity);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT)) $criteria->add(PartnerActivityPeer::AMOUNT, $this->amount);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT1)) $criteria->add(PartnerActivityPeer::AMOUNT1, $this->amount1);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT2)) $criteria->add(PartnerActivityPeer::AMOUNT2, $this->amount2);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT3)) $criteria->add(PartnerActivityPeer::AMOUNT3, $this->amount3);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT4)) $criteria->add(PartnerActivityPeer::AMOUNT4, $this->amount4);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT5)) $criteria->add(PartnerActivityPeer::AMOUNT5, $this->amount5);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT6)) $criteria->add(PartnerActivityPeer::AMOUNT6, $this->amount6);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT7)) $criteria->add(PartnerActivityPeer::AMOUNT7, $this->amount7);
		if ($this->isColumnModified(PartnerActivityPeer::AMOUNT9)) $criteria->add(PartnerActivityPeer::AMOUNT9, $this->amount9);

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
		$criteria = new Criteria(PartnerActivityPeer::DATABASE_NAME);

		$criteria->add(PartnerActivityPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of PartnerActivity (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setActivityDate($this->activity_date);

		$copyObj->setActivity($this->activity);

		$copyObj->setSubActivity($this->sub_activity);

		$copyObj->setAmount($this->amount);

		$copyObj->setAmount1($this->amount1);

		$copyObj->setAmount2($this->amount2);

		$copyObj->setAmount3($this->amount3);

		$copyObj->setAmount4($this->amount4);

		$copyObj->setAmount5($this->amount5);

		$copyObj->setAmount6($this->amount6);

		$copyObj->setAmount7($this->amount7);

		$copyObj->setAmount9($this->amount9);


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
	 * @return     PartnerActivity Clone of current object.
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
	 * @var     PartnerActivity Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      PartnerActivity $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(PartnerActivity $copiedFrom)
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
	 * @return     PartnerActivityPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PartnerActivityPeer();
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

} // BasePartnerActivity
