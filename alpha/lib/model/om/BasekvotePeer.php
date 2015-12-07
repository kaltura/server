<?php

/**
 * Base static class for performing query and update operations on the 'kvote' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasekvotePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'kvote';

	/** the related Propel class for this table */
	const OM_CLASS = 'kvote';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.kvote';

	/** the related TableMap class for this table */
	const TM_CLASS = 'kvoteTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 10;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'kvote.ID';

	/** the column name for the KSHOW_ID field */
	const KSHOW_ID = 'kvote.KSHOW_ID';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'kvote.ENTRY_ID';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'kvote.KUSER_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'kvote.PARTNER_ID';

	/** the column name for the RANK field */
	const RANK = 'kvote.RANK';

	/** the column name for the STATUS field */
	const STATUS = 'kvote.STATUS';

	/** the column name for the KVOTE_TYPE field */
	const KVOTE_TYPE = 'kvote.KVOTE_TYPE';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'kvote.CREATED_AT';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'kvote.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of kvote objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array kvote[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'KshowId', 'EntryId', 'KuserId', 'PartnerId', 'Rank', 'Status', 'KvoteType', 'CreatedAt', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'kshowId', 'entryId', 'kuserId', 'partnerId', 'rank', 'status', 'kvoteType', 'createdAt', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::KSHOW_ID, self::ENTRY_ID, self::KUSER_ID, self::PARTNER_ID, self::RANK, self::STATUS, self::KVOTE_TYPE, self::CREATED_AT, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'kshow_id', 'entry_id', 'kuser_id', 'partner_id', 'rank', 'status', 'kvote_type', 'created_at', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'KshowId' => 1, 'EntryId' => 2, 'KuserId' => 3, 'PartnerId' => 4, 'Rank' => 5, 'Status' => 6, 'KvoteType' => 7, 'CreatedAt' => 8, 'CustomData' => 9, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'kshowId' => 1, 'entryId' => 2, 'kuserId' => 3, 'partnerId' => 4, 'rank' => 5, 'status' => 6, 'kvoteType' => 7, 'createdAt' => 8, 'customData' => 9, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::KSHOW_ID => 1, self::ENTRY_ID => 2, self::KUSER_ID => 3, self::PARTNER_ID => 4, self::RANK => 5, self::STATUS => 6, self::KVOTE_TYPE => 7, self::CREATED_AT => 8, self::CUSTOM_DATA => 9, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'kshow_id' => 1, 'entry_id' => 2, 'kuser_id' => 3, 'partner_id' => 4, 'rank' => 5, 'status' => 6, 'kvote_type' => 7, 'created_at' => 8, 'custom_data' => 9, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
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
	 * @param      string $column The column name for current table. (i.e. kvotePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(kvotePeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(kvotePeer::ID);
		$criteria->addSelectColumn(kvotePeer::KSHOW_ID);
		$criteria->addSelectColumn(kvotePeer::ENTRY_ID);
		$criteria->addSelectColumn(kvotePeer::KUSER_ID);
		$criteria->addSelectColumn(kvotePeer::PARTNER_ID);
		$criteria->addSelectColumn(kvotePeer::RANK);
		$criteria->addSelectColumn(kvotePeer::STATUS);
		$criteria->addSelectColumn(kvotePeer::KVOTE_TYPE);
		$criteria->addSelectColumn(kvotePeer::CREATED_AT);
		$criteria->addSelectColumn(kvotePeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		kvotePeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'kvotePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = kvotePeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     kvote
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = kvotePeer::doSelect($critcopy, $con);
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
			$objFromPool = kvotePeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				kvotePeer::addInstanceToPool($curObject);
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
					kvotePeer::addInstanceToPool($curResult);
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
		$criteriaForSelect = kvotePeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'kvotePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			kvotePeer::filterSelectResults($cachedResult, $criteriaForSelect);
			kvotePeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = kvotePeer::alternativeCon($con, $queryDB);
		
		$queryResult = kvotePeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		kvotePeer::filterSelectResults($queryResult, $criteria);
		
		kvotePeer::addInstancesToPool($queryResult);
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
			$con = Propel::getConnection(kvotePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = kvotePeer::getCriteriaFilter();
		
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
			kvotePeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('kvote');
		if ($partnerCriteria)
		{
			call_user_func_array(array('kvotePeer','addPartnerToCriteria'), $partnerCriteria);
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
		kvotePeer::getCriteriaFilter()->applyFilter($criteria);
	}
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(!$privatePartnerData)
		{
			// the private partner data is not allowed - 
			if($kalturaNetwork)
			{
				// allow only the kaltura netword stuff
				if($partnerId)
				{
					$orderBy = "(" . self::PARTNER_ID . "<>{$partnerId})";  // first take the pattner_id and then the rest
					myCriteria::addComment($criteria , "Only Kaltura Network");
					$criteria->addAscendingOrderByColumn($orderBy);//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criteria->addAnd(self::PARTNER_ID, Partner::PARTNER_THAT_DOWS_NOT_EXIST);
			}
		}
		else
		{
			// private data is allowed
			if(!strlen(strval($partnerGroup)))
			{
				// the default case
				$criteria->addAnd(self::PARTNER_ID, $partnerId);
			}
			elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
				$partners = explode(',', trim($partnerGroup));
				foreach($partners as &$p)
					trim($p); // make sure there are not leading or trailing spaces

				// add the partner_id to the partner_group
				if (!in_array(strval($partnerId), $partners))
					$partners[] = strval($partnerId);
				
				if(count($partners) == 1 && reset($partners) == $partnerId)
				{
					$criteria->addAnd(self::PARTNER_ID, $partnerId);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
					$criteria->addAnd($criterion);
				}
			}
		}
			
		$criteriaFilter->enable();
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
		kvotePeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = kvotePeer::alternativeCon ( $con );
		
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
				kvotePeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			kvotePeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		kvotePeer::attachCriteriaFilter($criteria);

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
		$con = kvotePeer::alternativeCon($con);
		
		$criteria = kvotePeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      kvote $value A kvote object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(kvote $obj, $key = null)
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
				kMemoryManager::registerPeer('kvotePeer');
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
	 * @param      mixed $value A kvote object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof kvote) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or kvote object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     kvote Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to kvote
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
		$cls = kvotePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = kvotePeer::getInstanceFromPool($key))) {
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
	 * Returns the number of rows matching criteria, joining the related kshowRelatedByKshowId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkshowRelatedByKshowId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related entry table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinentry(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kshowRelatedByKuserId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkshowRelatedByKuserId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with their kshow objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkshowRelatedByKshowId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);
		kshowPeer::addSelectColumns($criteria);

		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kshowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (kvote) to $obj2 (kshow)
				$obj2->addkvoteRelatedByKshowId($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with their entry objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinentry(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);
		entryPeer::addSelectColumns($criteria);

		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = kvotePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = entryPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = entryPeer::getOMClass($row, $startcol);
					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					entryPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (kvote) to $obj2 (entry)
				$obj2->addkvote($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with their kshow objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkshowRelatedByKuserId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);
		kshowPeer::addSelectColumns($criteria);

		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kshowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (kvote) to $obj2 (kshow)
				$obj2->addkvoteRelatedByKuserId($obj1);

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
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of kvote objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
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

		kvotePeer::addSelectColumns($criteria);
		$startcol2 = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined kshow rows

			$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = kshowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (kvote) to the collection in $obj2 (kshow)
				$obj2->addkvoteRelatedByKshowId($obj1);
			} // if joined row not null

			// Add objects for joined entry rows

			$key3 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = entryPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = entryPeer::getOMClass($row, $startcol3);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					entryPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (kvote) to the collection in $obj3 (entry)
				$obj3->addkvote($obj1);
			} // if joined row not null

			// Add objects for joined kshow rows

			$key4 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = kshowPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = kshowPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					kshowPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (kvote) to the collection in $obj4 (kshow)
				$obj4->addkvoteRelatedByKuserId($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kshowRelatedByKshowId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptkshowRelatedByKshowId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related entry table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptentry(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kshowRelatedByKuserId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptkshowRelatedByKuserId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(kvotePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kvotePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = kvotePeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with all related objects except kshowRelatedByKshowId.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptkshowRelatedByKshowId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol2 = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined entry rows

				$key2 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = entryPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = entryPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					entryPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (kvote) to the collection in $obj2 (entry)
				$obj2->addkvote($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with all related objects except entry.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptentry(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol2 = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(kvotePeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(kvotePeer::KUSER_ID, kshowPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined kshow rows

				$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = kshowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (kvote) to the collection in $obj2 (kshow)
				$obj2->addkvoteRelatedByKshowId($obj1);

			} // if joined row is not null

				// Add objects for joined kshow rows

				$key3 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = kshowPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = kshowPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					kshowPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (kvote) to the collection in $obj3 (kshow)
				$obj3->addkvoteRelatedByKuserId($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of kvote objects pre-filled with all related objects except kshowRelatedByKuserId.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kvote objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptkshowRelatedByKuserId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		kvotePeer::addSelectColumns($criteria);
		$startcol2 = (kvotePeer::NUM_COLUMNS - kvotePeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(kvotePeer::ENTRY_ID, entryPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kvotePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kvotePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = kvotePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kvotePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined entry rows

				$key2 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = entryPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = entryPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					entryPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (kvote) to the collection in $obj2 (entry)
				$obj2->addkvote($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BasekvotePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasekvotePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new kvoteTableMap());
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
		return $withPrefix ? kvotePeer::CLASS_DEFAULT : kvotePeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a kvote or Criteria object.
	 *
	 * @param      mixed $values Criteria or kvote object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kvotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from kvote object
		}

		if ($criteria->containsKey(kvotePeer::ID) && $criteria->keyContainsValue(kvotePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.kvotePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a kvote or Criteria object.
	 *
	 * @param      mixed $values Criteria or kvote object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kvotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(kvotePeer::ID);
			$selectCriteria->add(kvotePeer::ID, $criteria->remove(kvotePeer::ID), $comparison);

		} else { // $values is kvote object
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
	 * Method to DELETE all rows from the kvote table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kvotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(kvotePeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			kvotePeer::clearInstancePool();
			kvotePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a kvote or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or kvote object or primary key or array of primary keys
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
			$con = Propel::getConnection(kvotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			kvotePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof kvote) { // it's a model object
			// invalidate the cache for this single object
			kvotePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(kvotePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				kvotePeer::removeInstanceFromPool($singleval);
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
			kvotePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given kvote object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      kvote $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(kvote $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(kvotePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(kvotePeer::TABLE_NAME);

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

		return BasePeer::doValidate(kvotePeer::DATABASE_NAME, kvotePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     kvote
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = kvotePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(kvotePeer::DATABASE_NAME);
		$criteria->add(kvotePeer::ID, $pk);

		$v = kvotePeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(kvotePeer::DATABASE_NAME);
			$criteria->add(kvotePeer::ID, $pks, Criteria::IN);
			$objs = kvotePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasekvotePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasekvotePeer::buildTableMap();

