<?php

/**
 * Base static class for performing query and update operations on the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseStorageProfilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'storage_profile';

	/** the related Propel class for this table */
	const OM_CLASS = 'StorageProfile';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.StorageProfile';

	/** the related TableMap class for this table */
	const TM_CLASS = 'StorageProfileTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 22;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'storage_profile.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'storage_profile.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'storage_profile.UPDATED_AT';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'storage_profile.PARTNER_ID';

	/** the column name for the NAME field */
	const NAME = 'storage_profile.NAME';

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'storage_profile.SYSTEM_NAME';

	/** the column name for the DESCIPTION field */
	const DESCIPTION = 'storage_profile.DESCIPTION';

	/** the column name for the STATUS field */
	const STATUS = 'storage_profile.STATUS';

	/** the column name for the PROTOCOL field */
	const PROTOCOL = 'storage_profile.PROTOCOL';

	/** the column name for the STORAGE_URL field */
	const STORAGE_URL = 'storage_profile.STORAGE_URL';

	/** the column name for the STORAGE_BASE_DIR field */
	const STORAGE_BASE_DIR = 'storage_profile.STORAGE_BASE_DIR';

	/** the column name for the STORAGE_USERNAME field */
	const STORAGE_USERNAME = 'storage_profile.STORAGE_USERNAME';

	/** the column name for the STORAGE_PASSWORD field */
	const STORAGE_PASSWORD = 'storage_profile.STORAGE_PASSWORD';

	/** the column name for the STORAGE_FTP_PASSIVE_MODE field */
	const STORAGE_FTP_PASSIVE_MODE = 'storage_profile.STORAGE_FTP_PASSIVE_MODE';

	/** the column name for the MIN_FILE_SIZE field */
	const MIN_FILE_SIZE = 'storage_profile.MIN_FILE_SIZE';

	/** the column name for the MAX_FILE_SIZE field */
	const MAX_FILE_SIZE = 'storage_profile.MAX_FILE_SIZE';

	/** the column name for the FLAVOR_PARAMS_IDS field */
	const FLAVOR_PARAMS_IDS = 'storage_profile.FLAVOR_PARAMS_IDS';

	/** the column name for the MAX_CONCURRENT_CONNECTIONS field */
	const MAX_CONCURRENT_CONNECTIONS = 'storage_profile.MAX_CONCURRENT_CONNECTIONS';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'storage_profile.CUSTOM_DATA';

	/** the column name for the PATH_MANAGER_CLASS field */
	const PATH_MANAGER_CLASS = 'storage_profile.PATH_MANAGER_CLASS';

	/** the column name for the DELIVERY_PRIORITY field */
	const DELIVERY_PRIORITY = 'storage_profile.DELIVERY_PRIORITY';

	/** the column name for the DELIVERY_STATUS field */
	const DELIVERY_STATUS = 'storage_profile.DELIVERY_STATUS';

	/**
	 * An identiy map to hold any loaded instances of StorageProfile objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array StorageProfile[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'PartnerId', 'Name', 'SystemName', 'Desciption', 'Status', 'Protocol', 'StorageUrl', 'StorageBaseDir', 'StorageUsername', 'StoragePassword', 'StorageFtpPassiveMode', 'MinFileSize', 'MaxFileSize', 'FlavorParamsIds', 'MaxConcurrentConnections', 'CustomData', 'PathManagerClass', 'DeliveryPriority', 'DeliveryStatus', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'partnerId', 'name', 'systemName', 'desciption', 'status', 'protocol', 'storageUrl', 'storageBaseDir', 'storageUsername', 'storagePassword', 'storageFtpPassiveMode', 'minFileSize', 'maxFileSize', 'flavorParamsIds', 'maxConcurrentConnections', 'customData', 'pathManagerClass', 'deliveryPriority', 'deliveryStatus', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::PARTNER_ID, self::NAME, self::SYSTEM_NAME, self::DESCIPTION, self::STATUS, self::PROTOCOL, self::STORAGE_URL, self::STORAGE_BASE_DIR, self::STORAGE_USERNAME, self::STORAGE_PASSWORD, self::STORAGE_FTP_PASSIVE_MODE, self::MIN_FILE_SIZE, self::MAX_FILE_SIZE, self::FLAVOR_PARAMS_IDS, self::MAX_CONCURRENT_CONNECTIONS, self::CUSTOM_DATA, self::PATH_MANAGER_CLASS, self::DELIVERY_PRIORITY, self::DELIVERY_STATUS, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'partner_id', 'name', 'system_name', 'desciption', 'status', 'protocol', 'storage_url', 'storage_base_dir', 'storage_username', 'storage_password', 'storage_ftp_passive_mode', 'min_file_size', 'max_file_size', 'flavor_params_ids', 'max_concurrent_connections', 'custom_data', 'path_manager_class', 'delivery_priority', 'delivery_status', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'PartnerId' => 3, 'Name' => 4, 'SystemName' => 5, 'Desciption' => 6, 'Status' => 7, 'Protocol' => 8, 'StorageUrl' => 9, 'StorageBaseDir' => 10, 'StorageUsername' => 11, 'StoragePassword' => 12, 'StorageFtpPassiveMode' => 13, 'MinFileSize' => 14, 'MaxFileSize' => 15, 'FlavorParamsIds' => 16, 'MaxConcurrentConnections' => 17, 'CustomData' => 18, 'PathManagerClass' => 19, 'DeliveryPriority' => 20, 'DeliveryStatus' => 21, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'partnerId' => 3, 'name' => 4, 'systemName' => 5, 'desciption' => 6, 'status' => 7, 'protocol' => 8, 'storageUrl' => 9, 'storageBaseDir' => 10, 'storageUsername' => 11, 'storagePassword' => 12, 'storageFtpPassiveMode' => 13, 'minFileSize' => 14, 'maxFileSize' => 15, 'flavorParamsIds' => 16, 'maxConcurrentConnections' => 17, 'customData' => 18, 'pathManagerClass' => 19, 'deliveryPriority' => 20, 'deliveryStatus' => 21, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::PARTNER_ID => 3, self::NAME => 4, self::SYSTEM_NAME => 5, self::DESCIPTION => 6, self::STATUS => 7, self::PROTOCOL => 8, self::STORAGE_URL => 9, self::STORAGE_BASE_DIR => 10, self::STORAGE_USERNAME => 11, self::STORAGE_PASSWORD => 12, self::STORAGE_FTP_PASSIVE_MODE => 13, self::MIN_FILE_SIZE => 14, self::MAX_FILE_SIZE => 15, self::FLAVOR_PARAMS_IDS => 16, self::MAX_CONCURRENT_CONNECTIONS => 17, self::CUSTOM_DATA => 18, self::PATH_MANAGER_CLASS => 19, self::DELIVERY_PRIORITY => 20, self::DELIVERY_STATUS => 21, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'partner_id' => 3, 'name' => 4, 'system_name' => 5, 'desciption' => 6, 'status' => 7, 'protocol' => 8, 'storage_url' => 9, 'storage_base_dir' => 10, 'storage_username' => 11, 'storage_password' => 12, 'storage_ftp_passive_mode' => 13, 'min_file_size' => 14, 'max_file_size' => 15, 'flavor_params_ids' => 16, 'max_concurrent_connections' => 17, 'custom_data' => 18, 'path_manager_class' => 19, 'delivery_priority' => 20, 'delivery_status' => 21, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
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
	 * @param      string $column The column name for current table. (i.e. StorageProfilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(StorageProfilePeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(StorageProfilePeer::ID);
		$criteria->addSelectColumn(StorageProfilePeer::CREATED_AT);
		$criteria->addSelectColumn(StorageProfilePeer::UPDATED_AT);
		$criteria->addSelectColumn(StorageProfilePeer::PARTNER_ID);
		$criteria->addSelectColumn(StorageProfilePeer::NAME);
		$criteria->addSelectColumn(StorageProfilePeer::SYSTEM_NAME);
		$criteria->addSelectColumn(StorageProfilePeer::DESCIPTION);
		$criteria->addSelectColumn(StorageProfilePeer::STATUS);
		$criteria->addSelectColumn(StorageProfilePeer::PROTOCOL);
		$criteria->addSelectColumn(StorageProfilePeer::STORAGE_URL);
		$criteria->addSelectColumn(StorageProfilePeer::STORAGE_BASE_DIR);
		$criteria->addSelectColumn(StorageProfilePeer::STORAGE_USERNAME);
		$criteria->addSelectColumn(StorageProfilePeer::STORAGE_PASSWORD);
		$criteria->addSelectColumn(StorageProfilePeer::STORAGE_FTP_PASSIVE_MODE);
		$criteria->addSelectColumn(StorageProfilePeer::MIN_FILE_SIZE);
		$criteria->addSelectColumn(StorageProfilePeer::MAX_FILE_SIZE);
		$criteria->addSelectColumn(StorageProfilePeer::FLAVOR_PARAMS_IDS);
		$criteria->addSelectColumn(StorageProfilePeer::MAX_CONCURRENT_CONNECTIONS);
		$criteria->addSelectColumn(StorageProfilePeer::CUSTOM_DATA);
		$criteria->addSelectColumn(StorageProfilePeer::PATH_MANAGER_CLASS);
		$criteria->addSelectColumn(StorageProfilePeer::DELIVERY_PRIORITY);
		$criteria->addSelectColumn(StorageProfilePeer::DELIVERY_STATUS);
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
		$criteria->setPrimaryTableName(StorageProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			StorageProfilePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		StorageProfilePeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'StorageProfilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = StorageProfilePeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     StorageProfile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = StorageProfilePeer::doSelect($critcopy, $con);
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
			$objFromPool = StorageProfilePeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				StorageProfilePeer::addInstanceToPool($curObject);
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
					StorageProfilePeer::addInstanceToPool($curResult);
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
		$criteriaForSelect = StorageProfilePeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'StorageProfilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			StorageProfilePeer::filterSelectResults($cachedResult, $criteriaForSelect);
			StorageProfilePeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = StorageProfilePeer::alternativeCon($con, $queryDB);
		
		$queryResult = StorageProfilePeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		StorageProfilePeer::filterSelectResults($queryResult, $criteria);
		
		StorageProfilePeer::addInstancesToPool($queryResult);
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
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = StorageProfilePeer::getCriteriaFilter();
		
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
			StorageProfilePeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('StorageProfile');
		if ($partnerCriteria)
		{
			call_user_func_array(array('StorageProfilePeer','addPartnerToCriteria'), $partnerCriteria);
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
		StorageProfilePeer::getCriteriaFilter()->applyFilter($criteria);
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
		StorageProfilePeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = StorageProfilePeer::alternativeCon ( $con );
		
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
				StorageProfilePeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			StorageProfilePeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		StorageProfilePeer::attachCriteriaFilter($criteria);

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
		$con = StorageProfilePeer::alternativeCon($con);
		
		$criteria = StorageProfilePeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      StorageProfile $value A StorageProfile object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(StorageProfile $obj, $key = null)
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
				kMemoryManager::registerPeer('StorageProfilePeer');
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
	 * @param      mixed $value A StorageProfile object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof StorageProfile) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or StorageProfile object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     StorageProfile Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to storage_profile
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
	
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = StorageProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = StorageProfilePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = StorageProfilePeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
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
	  $dbMap = Propel::getDatabaseMap(BaseStorageProfilePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseStorageProfilePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new StorageProfileTableMap());
	  }
	}

	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		try {

			$omClass = $row[$colnum + 8];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a StorageProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or StorageProfile object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from StorageProfile object
		}

		if ($criteria->containsKey(StorageProfilePeer::ID) && $criteria->keyContainsValue(StorageProfilePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.StorageProfilePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a StorageProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or StorageProfile object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(StorageProfilePeer::ID);
			$selectCriteria->add(StorageProfilePeer::ID, $criteria->remove(StorageProfilePeer::ID), $comparison);

		} else { // $values is StorageProfile object
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
	 * Method to DELETE all rows from the storage_profile table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(StorageProfilePeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			StorageProfilePeer::clearInstancePool();
			StorageProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a StorageProfile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or StorageProfile object or primary key or array of primary keys
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
			$con = Propel::getConnection(StorageProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			StorageProfilePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof StorageProfile) { // it's a model object
			// invalidate the cache for this single object
			StorageProfilePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(StorageProfilePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				StorageProfilePeer::removeInstanceFromPool($singleval);
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
			StorageProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given StorageProfile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      StorageProfile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(StorageProfile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(StorageProfilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(StorageProfilePeer::TABLE_NAME);

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

		return BasePeer::doValidate(StorageProfilePeer::DATABASE_NAME, StorageProfilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     StorageProfile
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = StorageProfilePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::ID, $pk);

		$v = StorageProfilePeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
			$criteria->add(StorageProfilePeer::ID, $pks, Criteria::IN);
			$objs = StorageProfilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseStorageProfilePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseStorageProfilePeer::buildTableMap();

