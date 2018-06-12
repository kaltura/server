<?php

/**
 * Base static class for performing query and update operations on the 'entry_vendor_task' table.
 *
 * 
 *
 * @package plugins.reach
 * @subpackage model.om
 */
abstract class BaseEntryVendorTaskPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'entry_vendor_task';

	/** the related Propel class for this table */
	const OM_CLASS = 'EntryVendorTask';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.reach.EntryVendorTask';

	/** the related TableMap class for this table */
	const TM_CLASS = 'EntryVendorTaskTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 16;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'entry_vendor_task.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'entry_vendor_task.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'entry_vendor_task.UPDATED_AT';

	/** the column name for the QUEUE_TIME field */
	const QUEUE_TIME = 'entry_vendor_task.QUEUE_TIME';

	/** the column name for the FINISH_TIME field */
	const FINISH_TIME = 'entry_vendor_task.FINISH_TIME';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'entry_vendor_task.PARTNER_ID';

	/** the column name for the VENDOR_PARTNER_ID field */
	const VENDOR_PARTNER_ID = 'entry_vendor_task.VENDOR_PARTNER_ID';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'entry_vendor_task.ENTRY_ID';

	/** the column name for the STATUS field */
	const STATUS = 'entry_vendor_task.STATUS';

	/** the column name for the PRICE field */
	const PRICE = 'entry_vendor_task.PRICE';

	/** the column name for the CATALOG_ITEM_ID field */
	const CATALOG_ITEM_ID = 'entry_vendor_task.CATALOG_ITEM_ID';

	/** the column name for the REACH_PROFILE_ID field */
	const REACH_PROFILE_ID = 'entry_vendor_task.REACH_PROFILE_ID';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'entry_vendor_task.KUSER_ID';

	/** the column name for the VERSION field */
	const VERSION = 'entry_vendor_task.VERSION';

	/** the column name for the CONTEXT field */
	const CONTEXT = 'entry_vendor_task.CONTEXT';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'entry_vendor_task.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of EntryVendorTask objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array EntryVendorTask[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'QueueTime', 'FinishTime', 'PartnerId', 'VendorPartnerId', 'EntryId', 'Status', 'Price', 'CatalogItemId', 'ReachProfileId', 'KuserId', 'Version', 'Context', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'queueTime', 'finishTime', 'partnerId', 'vendorPartnerId', 'entryId', 'status', 'price', 'catalogItemId', 'reachProfileId', 'kuserId', 'version', 'context', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::QUEUE_TIME, self::FINISH_TIME, self::PARTNER_ID, self::VENDOR_PARTNER_ID, self::ENTRY_ID, self::STATUS, self::PRICE, self::CATALOG_ITEM_ID, self::REACH_PROFILE_ID, self::KUSER_ID, self::VERSION, self::CONTEXT, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'queue_time', 'finish_time', 'partner_id', 'vendor_partner_id', 'entry_id', 'status', 'price', 'catalog_item_id', 'reach_profile_id', 'kuser_id', 'version', 'context', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'QueueTime' => 3, 'FinishTime' => 4, 'PartnerId' => 5, 'VendorPartnerId' => 6, 'EntryId' => 7, 'Status' => 8, 'Price' => 9, 'CatalogItemId' => 10, 'ReachProfileId' => 11, 'KuserId' => 12, 'Version' => 13, 'Context' => 14, 'CustomData' => 15, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'queueTime' => 3, 'finishTime' => 4, 'partnerId' => 5, 'vendorPartnerId' => 6, 'entryId' => 7, 'status' => 8, 'price' => 9, 'catalogItemId' => 10, 'reachProfileId' => 11, 'kuserId' => 12, 'version' => 13, 'context' => 14, 'customData' => 15, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::QUEUE_TIME => 3, self::FINISH_TIME => 4, self::PARTNER_ID => 5, self::VENDOR_PARTNER_ID => 6, self::ENTRY_ID => 7, self::STATUS => 8, self::PRICE => 9, self::CATALOG_ITEM_ID => 10, self::REACH_PROFILE_ID => 11, self::KUSER_ID => 12, self::VERSION => 13, self::CONTEXT => 14, self::CUSTOM_DATA => 15, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'queue_time' => 3, 'finish_time' => 4, 'partner_id' => 5, 'vendor_partner_id' => 6, 'entry_id' => 7, 'status' => 8, 'price' => 9, 'catalog_item_id' => 10, 'reach_profile_id' => 11, 'kuser_id' => 12, 'version' => 13, 'context' => 14, 'custom_data' => 15, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
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
	 * @param      string $column The column name for current table. (i.e. EntryVendorTaskPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EntryVendorTaskPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(EntryVendorTaskPeer::ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::CREATED_AT);
		$criteria->addSelectColumn(EntryVendorTaskPeer::UPDATED_AT);
		$criteria->addSelectColumn(EntryVendorTaskPeer::QUEUE_TIME);
		$criteria->addSelectColumn(EntryVendorTaskPeer::FINISH_TIME);
		$criteria->addSelectColumn(EntryVendorTaskPeer::PARTNER_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::VENDOR_PARTNER_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::ENTRY_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::STATUS);
		$criteria->addSelectColumn(EntryVendorTaskPeer::PRICE);
		$criteria->addSelectColumn(EntryVendorTaskPeer::CATALOG_ITEM_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::REACH_PROFILE_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::KUSER_ID);
		$criteria->addSelectColumn(EntryVendorTaskPeer::VERSION);
		$criteria->addSelectColumn(EntryVendorTaskPeer::CONTEXT);
		$criteria->addSelectColumn(EntryVendorTaskPeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(EntryVendorTaskPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EntryVendorTaskPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		EntryVendorTaskPeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'EntryVendorTaskPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = EntryVendorTaskPeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     EntryVendorTask
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EntryVendorTaskPeer::doSelect($critcopy, $con);
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
			$objFromPool = EntryVendorTaskPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				EntryVendorTaskPeer::addInstanceToPool($curObject);
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
					EntryVendorTaskPeer::addInstanceToPool($curResult);
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
		$criteriaForSelect = EntryVendorTaskPeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'EntryVendorTaskPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			EntryVendorTaskPeer::filterSelectResults($cachedResult, $criteriaForSelect);
			EntryVendorTaskPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = EntryVendorTaskPeer::alternativeCon($con, $queryDB);
		
		$queryResult = EntryVendorTaskPeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		EntryVendorTaskPeer::filterSelectResults($queryResult, $criteria);
		
		EntryVendorTaskPeer::addInstancesToPool($queryResult);
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
			$con = Propel::getConnection(EntryVendorTaskPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = EntryVendorTaskPeer::getCriteriaFilter();
		
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
			EntryVendorTaskPeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('EntryVendorTask');
		if ($partnerCriteria)
		{
			call_user_func_array(array('EntryVendorTaskPeer','addPartnerToCriteria'), $partnerCriteria);
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
		EntryVendorTaskPeer::getCriteriaFilter()->applyFilter($criteria);
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
			elseif ($partnerGroup === myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
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
		EntryVendorTaskPeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = EntryVendorTaskPeer::alternativeCon ( $con );
		
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
				EntryVendorTaskPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			EntryVendorTaskPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		EntryVendorTaskPeer::attachCriteriaFilter($criteria);

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
		$con = EntryVendorTaskPeer::alternativeCon($con);
		
		$criteria = EntryVendorTaskPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      EntryVendorTask $value A EntryVendorTask object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(EntryVendorTask $obj, $key = null)
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
				kMemoryManager::registerPeer('EntryVendorTaskPeer');
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
	 * @param      mixed $value A EntryVendorTask object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof EntryVendorTask) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or EntryVendorTask object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     EntryVendorTask Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to entry_vendor_task
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
		$cls = EntryVendorTaskPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = EntryVendorTaskPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = EntryVendorTaskPeer::getInstanceFromPool($key))) {
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
	  $dbMap = Propel::getDatabaseMap(BaseEntryVendorTaskPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseEntryVendorTaskPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new EntryVendorTaskTableMap());
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
		return $withPrefix ? EntryVendorTaskPeer::CLASS_DEFAULT : EntryVendorTaskPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a EntryVendorTask or Criteria object.
	 *
	 * @param      mixed $values Criteria or EntryVendorTask object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EntryVendorTaskPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from EntryVendorTask object
		}

		if ($criteria->containsKey(EntryVendorTaskPeer::ID) && $criteria->keyContainsValue(EntryVendorTaskPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.EntryVendorTaskPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a EntryVendorTask or Criteria object.
	 *
	 * @param      mixed $values Criteria or EntryVendorTask object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EntryVendorTaskPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(EntryVendorTaskPeer::ID);
			$selectCriteria->add(EntryVendorTaskPeer::ID, $criteria->remove(EntryVendorTaskPeer::ID), $comparison);

		} else { // $values is EntryVendorTask object
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
	 * Method to DELETE all rows from the entry_vendor_task table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EntryVendorTaskPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(EntryVendorTaskPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			EntryVendorTaskPeer::clearInstancePool();
			EntryVendorTaskPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a EntryVendorTask or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or EntryVendorTask object or primary key or array of primary keys
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
			$con = Propel::getConnection(EntryVendorTaskPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			EntryVendorTaskPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof EntryVendorTask) { // it's a model object
			// invalidate the cache for this single object
			EntryVendorTaskPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EntryVendorTaskPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				EntryVendorTaskPeer::removeInstanceFromPool($singleval);
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
			EntryVendorTaskPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given EntryVendorTask object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      EntryVendorTask $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(EntryVendorTask $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EntryVendorTaskPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EntryVendorTaskPeer::TABLE_NAME);

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

		return BasePeer::doValidate(EntryVendorTaskPeer::DATABASE_NAME, EntryVendorTaskPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     EntryVendorTask
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = EntryVendorTaskPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(EntryVendorTaskPeer::DATABASE_NAME);
		$criteria->add(EntryVendorTaskPeer::ID, $pk);

		$v = EntryVendorTaskPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(EntryVendorTaskPeer::DATABASE_NAME);
			$criteria->add(EntryVendorTaskPeer::ID, $pks, Criteria::IN);
			$objs = EntryVendorTaskPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEntryVendorTaskPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseEntryVendorTaskPeer::buildTableMap();

