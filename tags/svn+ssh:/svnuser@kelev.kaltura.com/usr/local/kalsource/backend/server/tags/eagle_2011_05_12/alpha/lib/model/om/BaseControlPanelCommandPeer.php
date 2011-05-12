<?php

/**
 * Base static class for performing query and update operations on the 'control_panel_command' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseControlPanelCommandPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'control_panel_command';

	/** the related Propel class for this table */
	const OM_CLASS = 'ControlPanelCommand';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.ControlPanelCommand';

	/** the related TableMap class for this table */
	const TM_CLASS = 'ControlPanelCommandTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 18;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'control_panel_command.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'control_panel_command.CREATED_AT';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'control_panel_command.CREATED_BY';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'control_panel_command.UPDATED_AT';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'control_panel_command.UPDATED_BY';

	/** the column name for the CREATED_BY_ID field */
	const CREATED_BY_ID = 'control_panel_command.CREATED_BY_ID';

	/** the column name for the SCHEDULER_ID field */
	const SCHEDULER_ID = 'control_panel_command.SCHEDULER_ID';

	/** the column name for the SCHEDULER_CONFIGURED_ID field */
	const SCHEDULER_CONFIGURED_ID = 'control_panel_command.SCHEDULER_CONFIGURED_ID';

	/** the column name for the WORKER_ID field */
	const WORKER_ID = 'control_panel_command.WORKER_ID';

	/** the column name for the WORKER_CONFIGURED_ID field */
	const WORKER_CONFIGURED_ID = 'control_panel_command.WORKER_CONFIGURED_ID';

	/** the column name for the WORKER_NAME field */
	const WORKER_NAME = 'control_panel_command.WORKER_NAME';

	/** the column name for the BATCH_INDEX field */
	const BATCH_INDEX = 'control_panel_command.BATCH_INDEX';

	/** the column name for the TYPE field */
	const TYPE = 'control_panel_command.TYPE';

	/** the column name for the TARGET_TYPE field */
	const TARGET_TYPE = 'control_panel_command.TARGET_TYPE';

	/** the column name for the STATUS field */
	const STATUS = 'control_panel_command.STATUS';

	/** the column name for the CAUSE field */
	const CAUSE = 'control_panel_command.CAUSE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'control_panel_command.DESCRIPTION';

	/** the column name for the ERROR_DESCRIPTION field */
	const ERROR_DESCRIPTION = 'control_panel_command.ERROR_DESCRIPTION';

	/**
	 * An identiy map to hold any loaded instances of ControlPanelCommand objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array ControlPanelCommand[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'CreatedBy', 'UpdatedAt', 'UpdatedBy', 'CreatedById', 'SchedulerId', 'SchedulerConfiguredId', 'WorkerId', 'WorkerConfiguredId', 'WorkerName', 'BatchIndex', 'Type', 'TargetType', 'Status', 'Cause', 'Description', 'ErrorDescription', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'createdById', 'schedulerId', 'schedulerConfiguredId', 'workerId', 'workerConfiguredId', 'workerName', 'batchIndex', 'type', 'targetType', 'status', 'cause', 'description', 'errorDescription', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::CREATED_BY, self::UPDATED_AT, self::UPDATED_BY, self::CREATED_BY_ID, self::SCHEDULER_ID, self::SCHEDULER_CONFIGURED_ID, self::WORKER_ID, self::WORKER_CONFIGURED_ID, self::WORKER_NAME, self::BATCH_INDEX, self::TYPE, self::TARGET_TYPE, self::STATUS, self::CAUSE, self::DESCRIPTION, self::ERROR_DESCRIPTION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'created_by_id', 'scheduler_id', 'scheduler_configured_id', 'worker_id', 'worker_configured_id', 'worker_name', 'batch_index', 'type', 'target_type', 'status', 'cause', 'description', 'error_description', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'CreatedBy' => 2, 'UpdatedAt' => 3, 'UpdatedBy' => 4, 'CreatedById' => 5, 'SchedulerId' => 6, 'SchedulerConfiguredId' => 7, 'WorkerId' => 8, 'WorkerConfiguredId' => 9, 'WorkerName' => 10, 'BatchIndex' => 11, 'Type' => 12, 'TargetType' => 13, 'Status' => 14, 'Cause' => 15, 'Description' => 16, 'ErrorDescription' => 17, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'createdBy' => 2, 'updatedAt' => 3, 'updatedBy' => 4, 'createdById' => 5, 'schedulerId' => 6, 'schedulerConfiguredId' => 7, 'workerId' => 8, 'workerConfiguredId' => 9, 'workerName' => 10, 'batchIndex' => 11, 'type' => 12, 'targetType' => 13, 'status' => 14, 'cause' => 15, 'description' => 16, 'errorDescription' => 17, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::CREATED_BY => 2, self::UPDATED_AT => 3, self::UPDATED_BY => 4, self::CREATED_BY_ID => 5, self::SCHEDULER_ID => 6, self::SCHEDULER_CONFIGURED_ID => 7, self::WORKER_ID => 8, self::WORKER_CONFIGURED_ID => 9, self::WORKER_NAME => 10, self::BATCH_INDEX => 11, self::TYPE => 12, self::TARGET_TYPE => 13, self::STATUS => 14, self::CAUSE => 15, self::DESCRIPTION => 16, self::ERROR_DESCRIPTION => 17, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'created_by' => 2, 'updated_at' => 3, 'updated_by' => 4, 'created_by_id' => 5, 'scheduler_id' => 6, 'scheduler_configured_id' => 7, 'worker_id' => 8, 'worker_configured_id' => 9, 'worker_name' => 10, 'batch_index' => 11, 'type' => 12, 'target_type' => 13, 'status' => 14, 'cause' => 15, 'description' => 16, 'error_description' => 17, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
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
	 * @param      string $column The column name for current table. (i.e. ControlPanelCommandPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ControlPanelCommandPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(ControlPanelCommandPeer::ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::CREATED_AT);
		$criteria->addSelectColumn(ControlPanelCommandPeer::CREATED_BY);
		$criteria->addSelectColumn(ControlPanelCommandPeer::UPDATED_AT);
		$criteria->addSelectColumn(ControlPanelCommandPeer::UPDATED_BY);
		$criteria->addSelectColumn(ControlPanelCommandPeer::CREATED_BY_ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::SCHEDULER_ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::WORKER_ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::WORKER_CONFIGURED_ID);
		$criteria->addSelectColumn(ControlPanelCommandPeer::WORKER_NAME);
		$criteria->addSelectColumn(ControlPanelCommandPeer::BATCH_INDEX);
		$criteria->addSelectColumn(ControlPanelCommandPeer::TYPE);
		$criteria->addSelectColumn(ControlPanelCommandPeer::TARGET_TYPE);
		$criteria->addSelectColumn(ControlPanelCommandPeer::STATUS);
		$criteria->addSelectColumn(ControlPanelCommandPeer::CAUSE);
		$criteria->addSelectColumn(ControlPanelCommandPeer::DESCRIPTION);
		$criteria->addSelectColumn(ControlPanelCommandPeer::ERROR_DESCRIPTION);
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
		$criteria->setPrimaryTableName(ControlPanelCommandPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ControlPanelCommandPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		ControlPanelCommandPeer::attachCriteriaFilter($criteria);

		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'ControlPanelCommandPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// set the connection to slave server
		$con = ControlPanelCommandPeer::alternativeCon ($con);
		
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);
		
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $count);
		}
		
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     ControlPanelCommand
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ControlPanelCommandPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	
	/**
	 * Override in order to use the query cache.
	 * Cache invalidation keys are used to determine when cached queries are valid.
	 * Before returning a query result from the cache, the time of the cached query
	 * is compared to the time saved in the invalidation key.
	 * A cached query will only be used if it's newer than the matching invalidation key.
	 *  
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      string $queryType The type of the query: select / count.
	 * @return     string The invalidation key that should be checked before returning a cached result for this criteria.
	 *		 if null is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys(Criteria $criteria, $queryType)
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 */
	public static function filterSelectResults(&$selectResults)
	{
	}
	
	/**
	 * Adds the supplied object array to the instance pool, objects already found in the pool
	 * will be replaced with instance from the pool.
	 *  
	 * @param      array $queryResult The array of objects to get / add to pool.
	 */
	public static function updateInstancePool(&$queryResult)
	{
		foreach ($queryResult as $curIndex => $curObject)
		{
			$objFromPool = ControlPanelCommandPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				ControlPanelCommandPeer::addInstanceToPool($curObject);
			}
			else
			{
				$queryResult[$curIndex] = $objFromPool;
			}
		}
	}
	
	/**
	 * Adds the supplied object array to the instance pool.
	 *  
	 * @param      array $queryResult The array of objects to add to pool.
	 */
	public static function addInstancesToPool($queryResult)
	{
		foreach ($queryResult as $curResult)
		{
			ControlPanelCommandPeer::addInstanceToPool($curResult);
		}
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
		$criteria = ControlPanelCommandPeer::prepareCriteriaForSelect($criteria);
		
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_SELECT,
			'ControlPanelCommandPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			ControlPanelCommandPeer::filterSelectResults($cachedResult);
			ControlPanelCommandPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}

		$con = ControlPanelCommandPeer::alternativeCon($con);
		
		$queryResult = ControlPanelCommandPeer::populateObjects(BasePeer::doSelect($criteria, $con));
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
		}
		
		ControlPanelCommandPeer::filterSelectResults($queryResult);
		ControlPanelCommandPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = ControlPanelCommandPeer::getCriteriaFilter();
		
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
			ControlPanelCommandPeer::setDefaultCriteriaFilter();
		
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
		ControlPanelCommandPeer::getCriteriaFilter()->applyFilter($criteria);
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
		ControlPanelCommandPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = ControlPanelCommandPeer::alternativeCon ( $con );
		
		// BasePeer returns a PDOStatement
		return BasePeer::doCount($criteria, $con);
	}
	
	public static function prepareCriteriaForSelect(Criteria $criteria)
	{
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				ControlPanelCommandPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			ControlPanelCommandPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		ControlPanelCommandPeer::attachCriteriaFilter($criteria);

		return $criteria;
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
		$con = ControlPanelCommandPeer::alternativeCon($con);
		
		$criteria = ControlPanelCommandPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      ControlPanelCommand $value A ControlPanelCommand object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(ControlPanelCommand $obj, $key = null)
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
	 * @param      mixed $value A ControlPanelCommand object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof ControlPanelCommand) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or ControlPanelCommand object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     ControlPanelCommand Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		foreach (self::$instances as $instance)
		{
			$instance->clearAllReferences(false);
		}
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to control_panel_command
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
		$cls = ControlPanelCommandPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = ControlPanelCommandPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = ControlPanelCommandPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
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
	  $dbMap = Propel::getDatabaseMap(BaseControlPanelCommandPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseControlPanelCommandPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new ControlPanelCommandTableMap());
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
		return $withPrefix ? ControlPanelCommandPeer::CLASS_DEFAULT : ControlPanelCommandPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a ControlPanelCommand or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControlPanelCommand object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from ControlPanelCommand object
		}

		if ($criteria->containsKey(ControlPanelCommandPeer::ID) && $criteria->keyContainsValue(ControlPanelCommandPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.ControlPanelCommandPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a ControlPanelCommand or Criteria object.
	 *
	 * @param      mixed $values Criteria or ControlPanelCommand object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(ControlPanelCommandPeer::ID);
			$selectCriteria->add(ControlPanelCommandPeer::ID, $criteria->remove(ControlPanelCommandPeer::ID), $comparison);

		} else { // $values is ControlPanelCommand object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the control_panel_command table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(ControlPanelCommandPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			ControlPanelCommandPeer::clearInstancePool();
			ControlPanelCommandPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a ControlPanelCommand or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or ControlPanelCommand object or primary key or array of primary keys
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
			$con = Propel::getConnection(ControlPanelCommandPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			ControlPanelCommandPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof ControlPanelCommand) { // it's a model object
			// invalidate the cache for this single object
			ControlPanelCommandPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ControlPanelCommandPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				ControlPanelCommandPeer::removeInstanceFromPool($singleval);
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
			ControlPanelCommandPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given ControlPanelCommand object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      ControlPanelCommand $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(ControlPanelCommand $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ControlPanelCommandPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ControlPanelCommandPeer::TABLE_NAME);

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

		return BasePeer::doValidate(ControlPanelCommandPeer::DATABASE_NAME, ControlPanelCommandPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     ControlPanelCommand
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = ControlPanelCommandPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(ControlPanelCommandPeer::DATABASE_NAME);
		$criteria->add(ControlPanelCommandPeer::ID, $pk);

		$v = ControlPanelCommandPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(ControlPanelCommandPeer::DATABASE_NAME);
			$criteria->add(ControlPanelCommandPeer::ID, $pks, Criteria::IN);
			$objs = ControlPanelCommandPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseControlPanelCommandPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseControlPanelCommandPeer::buildTableMap();

