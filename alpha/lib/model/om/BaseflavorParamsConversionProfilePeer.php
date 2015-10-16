<?php

/**
 * Base static class for performing query and update operations on the 'flavor_params_conversion_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseflavorParamsConversionProfilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_params_conversion_profile';

	/** the related Propel class for this table */
	const OM_CLASS = 'flavorParamsConversionProfile';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.flavorParamsConversionProfile';

	/** the related TableMap class for this table */
	const TM_CLASS = 'flavorParamsConversionProfileTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'flavor_params_conversion_profile.ID';

	/** the column name for the CONVERSION_PROFILE_ID field */
	const CONVERSION_PROFILE_ID = 'flavor_params_conversion_profile.CONVERSION_PROFILE_ID';

	/** the column name for the FLAVOR_PARAMS_ID field */
	const FLAVOR_PARAMS_ID = 'flavor_params_conversion_profile.FLAVOR_PARAMS_ID';

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'flavor_params_conversion_profile.SYSTEM_NAME';

	/** the column name for the ORIGIN field */
	const ORIGIN = 'flavor_params_conversion_profile.ORIGIN';

	/** the column name for the READY_BEHAVIOR field */
	const READY_BEHAVIOR = 'flavor_params_conversion_profile.READY_BEHAVIOR';

	/** the column name for the FORCE_NONE_COMPLIED field */
	const FORCE_NONE_COMPLIED = 'flavor_params_conversion_profile.FORCE_NONE_COMPLIED';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'flavor_params_conversion_profile.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'flavor_params_conversion_profile.UPDATED_AT';

	/** the column name for the PRIORITY field */
	const PRIORITY = 'flavor_params_conversion_profile.PRIORITY';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'flavor_params_conversion_profile.CUSTOM_DATA';

	/** the column name for the DELETE_POLICY field */
	const DELETE_POLICY = 'flavor_params_conversion_profile.DELETE_POLICY';

	/**
	 * An identiy map to hold any loaded instances of flavorParamsConversionProfile objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array flavorParamsConversionProfile[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ConversionProfileId', 'FlavorParamsId', 'SystemName', 'Origin', 'ReadyBehavior', 'ForceNoneComplied', 'CreatedAt', 'UpdatedAt', 'Priority', 'CustomData', 'DeletePolicy', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'conversionProfileId', 'flavorParamsId', 'systemName', 'origin', 'readyBehavior', 'forceNoneComplied', 'createdAt', 'updatedAt', 'priority', 'customData', 'deletePolicy', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CONVERSION_PROFILE_ID, self::FLAVOR_PARAMS_ID, self::SYSTEM_NAME, self::ORIGIN, self::READY_BEHAVIOR, self::FORCE_NONE_COMPLIED, self::CREATED_AT, self::UPDATED_AT, self::PRIORITY, self::CUSTOM_DATA, self::DELETE_POLICY, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'conversion_profile_id', 'flavor_params_id', 'system_name', 'origin', 'ready_behavior', 'force_none_complied', 'created_at', 'updated_at', 'priority', 'custom_data', 'delete_policy', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ConversionProfileId' => 1, 'FlavorParamsId' => 2, 'SystemName' => 3, 'Origin' => 4, 'ReadyBehavior' => 5, 'ForceNoneComplied' => 6, 'CreatedAt' => 7, 'UpdatedAt' => 8, 'Priority' => 9, 'CustomData' => 10, 'DeletePolicy' => 11, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'conversionProfileId' => 1, 'flavorParamsId' => 2, 'systemName' => 3, 'origin' => 4, 'readyBehavior' => 5, 'forceNoneComplied' => 6, 'createdAt' => 7, 'updatedAt' => 8, 'priority' => 9, 'customData' => 10, 'deletePolicy' => 11, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CONVERSION_PROFILE_ID => 1, self::FLAVOR_PARAMS_ID => 2, self::SYSTEM_NAME => 3, self::ORIGIN => 4, self::READY_BEHAVIOR => 5, self::FORCE_NONE_COMPLIED => 6, self::CREATED_AT => 7, self::UPDATED_AT => 8, self::PRIORITY => 9, self::CUSTOM_DATA => 10, self::DELETE_POLICY => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'conversion_profile_id' => 1, 'flavor_params_id' => 2, 'system_name' => 3, 'origin' => 4, 'ready_behavior' => 5, 'force_none_complied' => 6, 'created_at' => 7, 'updated_at' => 8, 'priority' => 9, 'custom_data' => 10, 'delete_policy' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
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
	 * @param      string $column The column name for current table. (i.e. flavorParamsConversionProfilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(flavorParamsConversionProfilePeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::ID);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::SYSTEM_NAME);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::ORIGIN);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::READY_BEHAVIOR);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::FORCE_NONE_COMPLIED);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::CREATED_AT);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::UPDATED_AT);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::PRIORITY);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::CUSTOM_DATA);
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::DELETE_POLICY);
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
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		flavorParamsConversionProfilePeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'flavorParamsConversionProfilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = flavorParamsConversionProfilePeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     flavorParamsConversionProfile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = flavorParamsConversionProfilePeer::doSelect($critcopy, $con);
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
	 * @return     array The invalidation keys that should be checked before returning a cached result for this criteria.
	 *		 if an empty array is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys()
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 * @param	   Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
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
			$objFromPool = flavorParamsConversionProfilePeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				flavorParamsConversionProfilePeer::addInstanceToPool($curObject);
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
		if (Propel::isInstancePoolingEnabled())
		{
			if ( count( self::$instances ) + count( $queryResult ) <= kConf::get('max_num_instances_in_pool') )
			{  
				foreach ($queryResult as $curResult)
				{
					flavorParamsConversionProfilePeer::addInstanceToPool($curResult);
				}
			}
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
		$criteriaForSelect = flavorParamsConversionProfilePeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'flavorParamsConversionProfilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			flavorParamsConversionProfilePeer::filterSelectResults($cachedResult, $criteriaForSelect);
			flavorParamsConversionProfilePeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = flavorParamsConversionProfilePeer::alternativeCon($con, $queryDB);
		
		$queryResult = flavorParamsConversionProfilePeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		flavorParamsConversionProfilePeer::filterSelectResults($queryResult, $criteria);
		
		flavorParamsConversionProfilePeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		if ($con === null)
		{
			switch ($queryDB)
			{
			case kQueryCache::QUERY_DB_MASTER:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
				break;

			case kQueryCache::QUERY_DB_SLAVE:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
				break;
			}
		}
	
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(flavorParamsConversionProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = flavorParamsConversionProfilePeer::getCriteriaFilter();
		
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
			flavorParamsConversionProfilePeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('flavorParamsConversionProfile');
		if ($partnerCriteria)
		{
			call_user_func_array(array('flavorParamsConversionProfilePeer','addPartnerToCriteria'), $partnerCriteria);
		}
		
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
		flavorParamsConversionProfilePeer::getCriteriaFilter()->applyFilter($criteria);
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
		flavorParamsConversionProfilePeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = flavorParamsConversionProfilePeer::alternativeCon ( $con );
		
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
				flavorParamsConversionProfilePeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		flavorParamsConversionProfilePeer::attachCriteriaFilter($criteria);

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
		$con = flavorParamsConversionProfilePeer::alternativeCon($con);
		
		$criteria = flavorParamsConversionProfilePeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      flavorParamsConversionProfile $value A flavorParamsConversionProfile object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(flavorParamsConversionProfile $obj, $key = null)
	{
		if ( Propel::isInstancePoolingEnabled() )
		{
			if ( $key === null )
			{
				$key = (string) $obj->getId();
			}
				
			if ( isset( self::$instances[$key] )											// Instance is already mapped?
					|| count( self::$instances ) < kConf::get('max_num_instances_in_pool')	// Not mapped, but max. inst. not yet reached?
				)
			{
				self::$instances[$key] = $obj;
				kMemoryManager::registerPeer('flavorParamsConversionProfilePeer');
			}
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
	 * @param      mixed $value A flavorParamsConversionProfile object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof flavorParamsConversionProfile) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or flavorParamsConversionProfile object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     flavorParamsConversionProfile Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to flavor_params_conversion_profile
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
		$cls = flavorParamsConversionProfilePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = flavorParamsConversionProfilePeer::getInstanceFromPool($key))) {
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
	 * Returns the number of rows matching criteria, joining the related conversionProfile2 table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinconversionProfile2(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related assetParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinassetParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorParamsConversionProfile objects pre-filled with their conversionProfile2 objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsConversionProfile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinconversionProfile2(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		$startcol = (flavorParamsConversionProfilePeer::NUM_COLUMNS - flavorParamsConversionProfilePeer::NUM_LAZY_LOAD_COLUMNS);
		conversionProfile2Peer::addSelectColumns($criteria);

		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsConversionProfilePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = flavorParamsConversionProfilePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsConversionProfilePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = conversionProfile2Peer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = conversionProfile2Peer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					conversionProfile2Peer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (flavorParamsConversionProfile) to $obj2 (conversionProfile2)
				$obj2->addflavorParamsConversionProfile($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsConversionProfile objects pre-filled with their assetParams objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsConversionProfile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinassetParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		$startcol = (flavorParamsConversionProfilePeer::NUM_COLUMNS - flavorParamsConversionProfilePeer::NUM_LAZY_LOAD_COLUMNS);
		assetParamsPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsConversionProfilePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = flavorParamsConversionProfilePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsConversionProfilePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = assetParamsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = assetParamsPeer::getOMClass($row, $startcol);
					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (flavorParamsConversionProfile) to $obj2 (assetParams)
				$obj2->addflavorParamsConversionProfile($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of flavorParamsConversionProfile objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsConversionProfile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsConversionProfilePeer::NUM_COLUMNS - flavorParamsConversionProfilePeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		assetParamsPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsConversionProfilePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = flavorParamsConversionProfilePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsConversionProfilePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined conversionProfile2 rows

			$key2 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = conversionProfile2Peer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = conversionProfile2Peer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					conversionProfile2Peer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (flavorParamsConversionProfile) to the collection in $obj2 (conversionProfile2)
				$obj2->addflavorParamsConversionProfile($obj1);
			} // if joined row not null

			// Add objects for joined assetParams rows

			$key3 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = assetParamsPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = assetParamsPeer::getOMClass($row, $startcol3);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					assetParamsPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (flavorParamsConversionProfile) to the collection in $obj3 (assetParams)
				$obj3->addflavorParamsConversionProfile($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related conversionProfile2 table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptconversionProfile2(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related assetParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptassetParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsConversionProfilePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = flavorParamsConversionProfilePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorParamsConversionProfile objects pre-filled with all related objects except conversionProfile2.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsConversionProfile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptconversionProfile2(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsConversionProfilePeer::NUM_COLUMNS - flavorParamsConversionProfilePeer::NUM_LAZY_LOAD_COLUMNS);

		assetParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsConversionProfilePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = flavorParamsConversionProfilePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsConversionProfilePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined assetParams rows

				$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = assetParamsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = assetParamsPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (flavorParamsConversionProfile) to the collection in $obj2 (assetParams)
				$obj2->addflavorParamsConversionProfile($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsConversionProfile objects pre-filled with all related objects except assetParams.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsConversionProfile objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptassetParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsConversionProfilePeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsConversionProfilePeer::NUM_COLUMNS - flavorParamsConversionProfilePeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsConversionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsConversionProfilePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = flavorParamsConversionProfilePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsConversionProfilePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined conversionProfile2 rows

				$key2 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = conversionProfile2Peer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = conversionProfile2Peer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					conversionProfile2Peer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (flavorParamsConversionProfile) to the collection in $obj2 (conversionProfile2)
				$obj2->addflavorParamsConversionProfile($obj1);

			} // if joined row is not null

			$results[] = $obj1;
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
	  $dbMap = Propel::getDatabaseMap(BaseflavorParamsConversionProfilePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseflavorParamsConversionProfilePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new flavorParamsConversionProfileTableMap());
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
		return $withPrefix ? flavorParamsConversionProfilePeer::CLASS_DEFAULT : flavorParamsConversionProfilePeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a flavorParamsConversionProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParamsConversionProfile object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsConversionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from flavorParamsConversionProfile object
		}

		if ($criteria->containsKey(flavorParamsConversionProfilePeer::ID) && $criteria->keyContainsValue(flavorParamsConversionProfilePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.flavorParamsConversionProfilePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a flavorParamsConversionProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParamsConversionProfile object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsConversionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(flavorParamsConversionProfilePeer::ID);
			$selectCriteria->add(flavorParamsConversionProfilePeer::ID, $criteria->remove(flavorParamsConversionProfilePeer::ID), $comparison);

		} else { // $values is flavorParamsConversionProfile object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}
	
	/**
	 * Return array of columns that should change only if there is a real change.
	 * @return array
	 */
	public static function getAtomicColumns()
	{
		return array();
	}
	
	/**
	 * Return array of custom-data fields that shouldn't be auto-updated.
	 * @return array
	 */
	public static function getAtomicCustomDataFields()
	{
		return array();
	}

	/**
	 * Method to DELETE all rows from the flavor_params_conversion_profile table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsConversionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(flavorParamsConversionProfilePeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			flavorParamsConversionProfilePeer::clearInstancePool();
			flavorParamsConversionProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a flavorParamsConversionProfile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or flavorParamsConversionProfile object or primary key or array of primary keys
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
			$con = Propel::getConnection(flavorParamsConversionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			flavorParamsConversionProfilePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof flavorParamsConversionProfile) { // it's a model object
			// invalidate the cache for this single object
			flavorParamsConversionProfilePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(flavorParamsConversionProfilePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				flavorParamsConversionProfilePeer::removeInstanceFromPool($singleval);
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
			flavorParamsConversionProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given flavorParamsConversionProfile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      flavorParamsConversionProfile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(flavorParamsConversionProfile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(flavorParamsConversionProfilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(flavorParamsConversionProfilePeer::TABLE_NAME);

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

		return BasePeer::doValidate(flavorParamsConversionProfilePeer::DATABASE_NAME, flavorParamsConversionProfilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     flavorParamsConversionProfile
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = flavorParamsConversionProfilePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(flavorParamsConversionProfilePeer::DATABASE_NAME);
		$criteria->add(flavorParamsConversionProfilePeer::ID, $pk);

		$v = flavorParamsConversionProfilePeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(flavorParamsConversionProfilePeer::DATABASE_NAME);
			$criteria->add(flavorParamsConversionProfilePeer::ID, $pks, Criteria::IN);
			$objs = flavorParamsConversionProfilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseflavorParamsConversionProfilePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseflavorParamsConversionProfilePeer::buildTableMap();

