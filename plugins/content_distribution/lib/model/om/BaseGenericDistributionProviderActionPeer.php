<?php

/**
 * Base static class for performing query and update operations on the 'generic_distribution_provider_action' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseGenericDistributionProviderActionPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'generic_distribution_provider_action';

	/** the related Propel class for this table */
	const OM_CLASS = 'GenericDistributionProviderAction';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.GenericDistributionProviderAction';

	/** the related TableMap class for this table */
	const TM_CLASS = 'GenericDistributionProviderActionTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'generic_distribution_provider_action.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'generic_distribution_provider_action.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'generic_distribution_provider_action.UPDATED_AT';

	/** the column name for the GENERIC_DISTRIBUTION_PROVIDER_ID field */
	const GENERIC_DISTRIBUTION_PROVIDER_ID = 'generic_distribution_provider_action.GENERIC_DISTRIBUTION_PROVIDER_ID';

	/** the column name for the ACTION field */
	const ACTION = 'generic_distribution_provider_action.ACTION';

	/** the column name for the STATUS field */
	const STATUS = 'generic_distribution_provider_action.STATUS';

	/** the column name for the RESULTS_PARSER field */
	const RESULTS_PARSER = 'generic_distribution_provider_action.RESULTS_PARSER';

	/** the column name for the PROTOCOL field */
	const PROTOCOL = 'generic_distribution_provider_action.PROTOCOL';

	/** the column name for the SERVER_ADDRESS field */
	const SERVER_ADDRESS = 'generic_distribution_provider_action.SERVER_ADDRESS';

	/** the column name for the REMOTE_PATH field */
	const REMOTE_PATH = 'generic_distribution_provider_action.REMOTE_PATH';

	/** the column name for the REMOTE_USERNAME field */
	const REMOTE_USERNAME = 'generic_distribution_provider_action.REMOTE_USERNAME';

	/** the column name for the REMOTE_PASSWORD field */
	const REMOTE_PASSWORD = 'generic_distribution_provider_action.REMOTE_PASSWORD';

	/** the column name for the EDITABLE_FIELDS field */
	const EDITABLE_FIELDS = 'generic_distribution_provider_action.EDITABLE_FIELDS';

	/** the column name for the MANDATORY_FIELDS field */
	const MANDATORY_FIELDS = 'generic_distribution_provider_action.MANDATORY_FIELDS';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'generic_distribution_provider_action.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of GenericDistributionProviderAction objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array GenericDistributionProviderAction[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'GenericDistributionProviderId', 'Action', 'Status', 'ResultsParser', 'Protocol', 'ServerAddress', 'RemotePath', 'RemoteUsername', 'RemotePassword', 'EditableFields', 'MandatoryFields', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'genericDistributionProviderId', 'action', 'status', 'resultsParser', 'protocol', 'serverAddress', 'remotePath', 'remoteUsername', 'remotePassword', 'editableFields', 'mandatoryFields', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::GENERIC_DISTRIBUTION_PROVIDER_ID, self::ACTION, self::STATUS, self::RESULTS_PARSER, self::PROTOCOL, self::SERVER_ADDRESS, self::REMOTE_PATH, self::REMOTE_USERNAME, self::REMOTE_PASSWORD, self::EDITABLE_FIELDS, self::MANDATORY_FIELDS, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'generic_distribution_provider_id', 'action', 'status', 'results_parser', 'protocol', 'server_address', 'remote_path', 'remote_username', 'remote_password', 'editable_fields', 'mandatory_fields', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'GenericDistributionProviderId' => 3, 'Action' => 4, 'Status' => 5, 'ResultsParser' => 6, 'Protocol' => 7, 'ServerAddress' => 8, 'RemotePath' => 9, 'RemoteUsername' => 10, 'RemotePassword' => 11, 'EditableFields' => 12, 'MandatoryFields' => 13, 'CustomData' => 14, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'genericDistributionProviderId' => 3, 'action' => 4, 'status' => 5, 'resultsParser' => 6, 'protocol' => 7, 'serverAddress' => 8, 'remotePath' => 9, 'remoteUsername' => 10, 'remotePassword' => 11, 'editableFields' => 12, 'mandatoryFields' => 13, 'customData' => 14, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::GENERIC_DISTRIBUTION_PROVIDER_ID => 3, self::ACTION => 4, self::STATUS => 5, self::RESULTS_PARSER => 6, self::PROTOCOL => 7, self::SERVER_ADDRESS => 8, self::REMOTE_PATH => 9, self::REMOTE_USERNAME => 10, self::REMOTE_PASSWORD => 11, self::EDITABLE_FIELDS => 12, self::MANDATORY_FIELDS => 13, self::CUSTOM_DATA => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'generic_distribution_provider_id' => 3, 'action' => 4, 'status' => 5, 'results_parser' => 6, 'protocol' => 7, 'server_address' => 8, 'remote_path' => 9, 'remote_username' => 10, 'remote_password' => 11, 'editable_fields' => 12, 'mandatory_fields' => 13, 'custom_data' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. GenericDistributionProviderActionPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(GenericDistributionProviderActionPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::ID);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::CREATED_AT);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::UPDATED_AT);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::GENERIC_DISTRIBUTION_PROVIDER_ID);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::ACTION);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::STATUS);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::RESULTS_PARSER);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::PROTOCOL);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::SERVER_ADDRESS);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::REMOTE_PATH);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::REMOTE_USERNAME);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::REMOTE_PASSWORD);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::EDITABLE_FIELDS);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::MANDATORY_FIELDS);
		$criteria->addSelectColumn(GenericDistributionProviderActionPeer::CUSTOM_DATA);
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(GenericDistributionProviderActionPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			GenericDistributionProviderActionPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = GenericDistributionProviderActionPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     GenericDistributionProviderAction
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = GenericDistributionProviderActionPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return GenericDistributionProviderActionPeer::populateObjects(GenericDistributionProviderActionPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(GenericDistributionProviderActionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = GenericDistributionProviderActionPeer::getCriteriaFilter();
		
		if ( $use )  $criteria_filter->enable(); 
		else $criteria_filter->disable();
	}
	
	/**
	 * Returns the default criteria filter
	 *
	 * @return     criteriaFilter The default criteria filter.
	 */
	public static function &getCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			GenericDistributionProviderActionPeer::setDefaultCriteriaFilter();
		
		return self::$s_criteria_filter;
	}
	
	 
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new myCriteria(); 
		self::$s_criteria_filter->setFilter($c);
	}
	
	
	/**
	 * the filterCriteria will filter out all the doSelect methods - ONLY if the filter is turned on.
	 * IMPORTANT - the filter is turend on by default and when switched off - should be turned on again manually .
	 * 
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 */
	protected static function attachCriteriaFilter(Criteria $criteria)
	{
		GenericDistributionProviderActionPeer::getCriteriaFilter()->applyFilter($criteria);
	}
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
	}
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doCount()
	 */
	public static function doCountStmt(Criteria $criteria, PropelPDO $con = null)
	{
		// attach default criteria
		GenericDistributionProviderActionPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = GenericDistributionProviderActionPeer::alternativeCon ( $con );
		
		// BasePeer returns a PDOStatement
		return BasePeer::doCount($criteria, $con);
	}
	
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		$con = GenericDistributionProviderActionPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				GenericDistributionProviderActionPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			GenericDistributionProviderActionPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		GenericDistributionProviderActionPeer::attachCriteriaFilter($criteria);
		
		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      GenericDistributionProviderAction $value A GenericDistributionProviderAction object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(GenericDistributionProviderAction $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A GenericDistributionProviderAction object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof GenericDistributionProviderAction) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or GenericDistributionProviderAction object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     GenericDistributionProviderAction Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to generic_distribution_provider_action
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
	}

	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = GenericDistributionProviderActionPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = GenericDistributionProviderActionPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = GenericDistributionProviderActionPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				GenericDistributionProviderActionPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseGenericDistributionProviderActionPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseGenericDistributionProviderActionPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new GenericDistributionProviderActionTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean  Whether or not to return the path wit hthe class name 
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? GenericDistributionProviderActionPeer::CLASS_DEFAULT : GenericDistributionProviderActionPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a GenericDistributionProviderAction or Criteria object.
	 *
	 * @param      mixed $values Criteria or GenericDistributionProviderAction object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(GenericDistributionProviderActionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from GenericDistributionProviderAction object
		}

		if ($criteria->containsKey(GenericDistributionProviderActionPeer::ID) && $criteria->keyContainsValue(GenericDistributionProviderActionPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.GenericDistributionProviderActionPeer::ID.')');
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a GenericDistributionProviderAction or Criteria object.
	 *
	 * @param      mixed $values Criteria or GenericDistributionProviderAction object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(GenericDistributionProviderActionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(GenericDistributionProviderActionPeer::ID);
			$selectCriteria->add(GenericDistributionProviderActionPeer::ID, $criteria->remove(GenericDistributionProviderActionPeer::ID), $comparison);

		} else { // $values is GenericDistributionProviderAction object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the generic_distribution_provider_action table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(GenericDistributionProviderActionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(GenericDistributionProviderActionPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			GenericDistributionProviderActionPeer::clearInstancePool();
			GenericDistributionProviderActionPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a GenericDistributionProviderAction or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or GenericDistributionProviderAction object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(GenericDistributionProviderActionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			GenericDistributionProviderActionPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof GenericDistributionProviderAction) { // it's a model object
			// invalidate the cache for this single object
			GenericDistributionProviderActionPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(GenericDistributionProviderActionPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				GenericDistributionProviderActionPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			GenericDistributionProviderActionPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given GenericDistributionProviderAction object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      GenericDistributionProviderAction $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(GenericDistributionProviderAction $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(GenericDistributionProviderActionPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(GenericDistributionProviderActionPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(GenericDistributionProviderActionPeer::DATABASE_NAME, GenericDistributionProviderActionPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     GenericDistributionProviderAction
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = GenericDistributionProviderActionPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(GenericDistributionProviderActionPeer::DATABASE_NAME);
		$criteria->add(GenericDistributionProviderActionPeer::ID, $pk);

		$v = GenericDistributionProviderActionPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(GenericDistributionProviderActionPeer::DATABASE_NAME);
			$criteria->add(GenericDistributionProviderActionPeer::ID, $pks, Criteria::IN);
			$objs = GenericDistributionProviderActionPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseGenericDistributionProviderActionPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseGenericDistributionProviderActionPeer::buildTableMap();

