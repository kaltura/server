<?php

/**
 * Base static class for performing query and update operations on the 'partner' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasePartnerPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'partner';

	/** the related Propel class for this table */
	const OM_CLASS = 'Partner';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.Partner';

	/** the related TableMap class for this table */
	const TM_CLASS = 'PartnerTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 41;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'partner.ID';

	/** the column name for the PARTNER_NAME field */
	const PARTNER_NAME = 'partner.PARTNER_NAME';

	/** the column name for the PARTNER_ALIAS field */
	const PARTNER_ALIAS = 'partner.PARTNER_ALIAS';

	/** the column name for the URL1 field */
	const URL1 = 'partner.URL1';

	/** the column name for the URL2 field */
	const URL2 = 'partner.URL2';

	/** the column name for the SECRET field */
	const SECRET = 'partner.SECRET';

	/** the column name for the ADMIN_SECRET field */
	const ADMIN_SECRET = 'partner.ADMIN_SECRET';

	/** the column name for the MAX_NUMBER_OF_HITS_PER_DAY field */
	const MAX_NUMBER_OF_HITS_PER_DAY = 'partner.MAX_NUMBER_OF_HITS_PER_DAY';

	/** the column name for the APPEAR_IN_SEARCH field */
	const APPEAR_IN_SEARCH = 'partner.APPEAR_IN_SEARCH';

	/** the column name for the DEBUG_LEVEL field */
	const DEBUG_LEVEL = 'partner.DEBUG_LEVEL';

	/** the column name for the INVALID_LOGIN_COUNT field */
	const INVALID_LOGIN_COUNT = 'partner.INVALID_LOGIN_COUNT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'partner.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'partner.UPDATED_AT';

	/** the column name for the ANONYMOUS_KUSER_ID field */
	const ANONYMOUS_KUSER_ID = 'partner.ANONYMOUS_KUSER_ID';

	/** the column name for the KS_MAX_EXPIRY_IN_SECONDS field */
	const KS_MAX_EXPIRY_IN_SECONDS = 'partner.KS_MAX_EXPIRY_IN_SECONDS';

	/** the column name for the CREATE_USER_ON_DEMAND field */
	const CREATE_USER_ON_DEMAND = 'partner.CREATE_USER_ON_DEMAND';

	/** the column name for the PREFIX field */
	const PREFIX = 'partner.PREFIX';

	/** the column name for the ADMIN_NAME field */
	const ADMIN_NAME = 'partner.ADMIN_NAME';

	/** the column name for the ADMIN_EMAIL field */
	const ADMIN_EMAIL = 'partner.ADMIN_EMAIL';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'partner.DESCRIPTION';

	/** the column name for the COMMERCIAL_USE field */
	const COMMERCIAL_USE = 'partner.COMMERCIAL_USE';

	/** the column name for the MODERATE_CONTENT field */
	const MODERATE_CONTENT = 'partner.MODERATE_CONTENT';

	/** the column name for the NOTIFY field */
	const NOTIFY = 'partner.NOTIFY';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'partner.CUSTOM_DATA';

	/** the column name for the SERVICE_CONFIG_ID field */
	const SERVICE_CONFIG_ID = 'partner.SERVICE_CONFIG_ID';

	/** the column name for the STATUS field */
	const STATUS = 'partner.STATUS';

	/** the column name for the CONTENT_CATEGORIES field */
	const CONTENT_CATEGORIES = 'partner.CONTENT_CATEGORIES';

	/** the column name for the TYPE field */
	const TYPE = 'partner.TYPE';

	/** the column name for the PHONE field */
	const PHONE = 'partner.PHONE';

	/** the column name for the DESCRIBE_YOURSELF field */
	const DESCRIBE_YOURSELF = 'partner.DESCRIBE_YOURSELF';

	/** the column name for the ADULT_CONTENT field */
	const ADULT_CONTENT = 'partner.ADULT_CONTENT';

	/** the column name for the PARTNER_PACKAGE field */
	const PARTNER_PACKAGE = 'partner.PARTNER_PACKAGE';

	/** the column name for the USAGE_PERCENT field */
	const USAGE_PERCENT = 'partner.USAGE_PERCENT';

	/** the column name for the STORAGE_USAGE field */
	const STORAGE_USAGE = 'partner.STORAGE_USAGE';

	/** the column name for the EIGHTY_PERCENT_WARNING field */
	const EIGHTY_PERCENT_WARNING = 'partner.EIGHTY_PERCENT_WARNING';

	/** the column name for the USAGE_LIMIT_WARNING field */
	const USAGE_LIMIT_WARNING = 'partner.USAGE_LIMIT_WARNING';

	/** the column name for the MONITOR_USAGE field */
	const MONITOR_USAGE = 'partner.MONITOR_USAGE';

	/** the column name for the PRIORITY_GROUP_ID field */
	const PRIORITY_GROUP_ID = 'partner.PRIORITY_GROUP_ID';

	/** the column name for the PARTNER_GROUP_TYPE field */
	const PARTNER_GROUP_TYPE = 'partner.PARTNER_GROUP_TYPE';

	/** the column name for the PARTNER_PARENT_ID field */
	const PARTNER_PARENT_ID = 'partner.PARTNER_PARENT_ID';

	/** the column name for the KMC_VERSION field */
	const KMC_VERSION = 'partner.KMC_VERSION';

	/**
	 * An identiy map to hold any loaded instances of Partner objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array Partner[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'PartnerName', 'PartnerAlias', 'Url1', 'Url2', 'Secret', 'AdminSecret', 'MaxNumberOfHitsPerDay', 'AppearInSearch', 'DebugLevel', 'InvalidLoginCount', 'CreatedAt', 'UpdatedAt', 'AnonymousKuserId', 'KsMaxExpiryInSeconds', 'CreateUserOnDemand', 'Prefix', 'AdminName', 'AdminEmail', 'Description', 'CommercialUse', 'ModerateContent', 'Notify', 'CustomData', 'ServiceConfigId', 'Status', 'ContentCategories', 'Type', 'Phone', 'DescribeYourself', 'AdultContent', 'PartnerPackage', 'UsagePercent', 'StorageUsage', 'EightyPercentWarning', 'UsageLimitWarning', 'MonitorUsage', 'PriorityGroupId', 'PartnerGroupType', 'PartnerParentId', 'KmcVersion', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'partnerName', 'partnerAlias', 'url1', 'url2', 'secret', 'adminSecret', 'maxNumberOfHitsPerDay', 'appearInSearch', 'debugLevel', 'invalidLoginCount', 'createdAt', 'updatedAt', 'anonymousKuserId', 'ksMaxExpiryInSeconds', 'createUserOnDemand', 'prefix', 'adminName', 'adminEmail', 'description', 'commercialUse', 'moderateContent', 'notify', 'customData', 'serviceConfigId', 'status', 'contentCategories', 'type', 'phone', 'describeYourself', 'adultContent', 'partnerPackage', 'usagePercent', 'storageUsage', 'eightyPercentWarning', 'usageLimitWarning', 'monitorUsage', 'priorityGroupId', 'partnerGroupType', 'partnerParentId', 'kmcVersion', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PARTNER_NAME, self::PARTNER_ALIAS, self::URL1, self::URL2, self::SECRET, self::ADMIN_SECRET, self::MAX_NUMBER_OF_HITS_PER_DAY, self::APPEAR_IN_SEARCH, self::DEBUG_LEVEL, self::INVALID_LOGIN_COUNT, self::CREATED_AT, self::UPDATED_AT, self::ANONYMOUS_KUSER_ID, self::KS_MAX_EXPIRY_IN_SECONDS, self::CREATE_USER_ON_DEMAND, self::PREFIX, self::ADMIN_NAME, self::ADMIN_EMAIL, self::DESCRIPTION, self::COMMERCIAL_USE, self::MODERATE_CONTENT, self::NOTIFY, self::CUSTOM_DATA, self::SERVICE_CONFIG_ID, self::STATUS, self::CONTENT_CATEGORIES, self::TYPE, self::PHONE, self::DESCRIBE_YOURSELF, self::ADULT_CONTENT, self::PARTNER_PACKAGE, self::USAGE_PERCENT, self::STORAGE_USAGE, self::EIGHTY_PERCENT_WARNING, self::USAGE_LIMIT_WARNING, self::MONITOR_USAGE, self::PRIORITY_GROUP_ID, self::PARTNER_GROUP_TYPE, self::PARTNER_PARENT_ID, self::KMC_VERSION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'partner_name', 'partner_alias', 'url1', 'url2', 'secret', 'admin_secret', 'max_number_of_hits_per_day', 'appear_in_search', 'debug_level', 'invalid_login_count', 'created_at', 'updated_at', 'anonymous_kuser_id', 'ks_max_expiry_in_seconds', 'create_user_on_demand', 'prefix', 'admin_name', 'admin_email', 'description', 'commercial_use', 'moderate_content', 'notify', 'custom_data', 'service_config_id', 'status', 'content_categories', 'type', 'phone', 'describe_yourself', 'adult_content', 'partner_package', 'usage_percent', 'storage_usage', 'eighty_percent_warning', 'usage_limit_warning', 'monitor_usage', 'priority_group_id', 'partner_group_type', 'partner_parent_id', 'kmc_version', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'PartnerName' => 1, 'PartnerAlias' => 2, 'Url1' => 3, 'Url2' => 4, 'Secret' => 5, 'AdminSecret' => 6, 'MaxNumberOfHitsPerDay' => 7, 'AppearInSearch' => 8, 'DebugLevel' => 9, 'InvalidLoginCount' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, 'AnonymousKuserId' => 13, 'KsMaxExpiryInSeconds' => 14, 'CreateUserOnDemand' => 15, 'Prefix' => 16, 'AdminName' => 17, 'AdminEmail' => 18, 'Description' => 19, 'CommercialUse' => 20, 'ModerateContent' => 21, 'Notify' => 22, 'CustomData' => 23, 'ServiceConfigId' => 24, 'Status' => 25, 'ContentCategories' => 26, 'Type' => 27, 'Phone' => 28, 'DescribeYourself' => 29, 'AdultContent' => 30, 'PartnerPackage' => 31, 'UsagePercent' => 32, 'StorageUsage' => 33, 'EightyPercentWarning' => 34, 'UsageLimitWarning' => 35, 'MonitorUsage' => 36, 'PriorityGroupId' => 37, 'PartnerGroupType' => 38, 'PartnerParentId' => 39, 'KmcVersion' => 40, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'partnerName' => 1, 'partnerAlias' => 2, 'url1' => 3, 'url2' => 4, 'secret' => 5, 'adminSecret' => 6, 'maxNumberOfHitsPerDay' => 7, 'appearInSearch' => 8, 'debugLevel' => 9, 'invalidLoginCount' => 10, 'createdAt' => 11, 'updatedAt' => 12, 'anonymousKuserId' => 13, 'ksMaxExpiryInSeconds' => 14, 'createUserOnDemand' => 15, 'prefix' => 16, 'adminName' => 17, 'adminEmail' => 18, 'description' => 19, 'commercialUse' => 20, 'moderateContent' => 21, 'notify' => 22, 'customData' => 23, 'serviceConfigId' => 24, 'status' => 25, 'contentCategories' => 26, 'type' => 27, 'phone' => 28, 'describeYourself' => 29, 'adultContent' => 30, 'partnerPackage' => 31, 'usagePercent' => 32, 'storageUsage' => 33, 'eightyPercentWarning' => 34, 'usageLimitWarning' => 35, 'monitorUsage' => 36, 'priorityGroupId' => 37, 'partnerGroupType' => 38, 'partnerParentId' => 39, 'kmcVersion' => 40, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PARTNER_NAME => 1, self::PARTNER_ALIAS => 2, self::URL1 => 3, self::URL2 => 4, self::SECRET => 5, self::ADMIN_SECRET => 6, self::MAX_NUMBER_OF_HITS_PER_DAY => 7, self::APPEAR_IN_SEARCH => 8, self::DEBUG_LEVEL => 9, self::INVALID_LOGIN_COUNT => 10, self::CREATED_AT => 11, self::UPDATED_AT => 12, self::ANONYMOUS_KUSER_ID => 13, self::KS_MAX_EXPIRY_IN_SECONDS => 14, self::CREATE_USER_ON_DEMAND => 15, self::PREFIX => 16, self::ADMIN_NAME => 17, self::ADMIN_EMAIL => 18, self::DESCRIPTION => 19, self::COMMERCIAL_USE => 20, self::MODERATE_CONTENT => 21, self::NOTIFY => 22, self::CUSTOM_DATA => 23, self::SERVICE_CONFIG_ID => 24, self::STATUS => 25, self::CONTENT_CATEGORIES => 26, self::TYPE => 27, self::PHONE => 28, self::DESCRIBE_YOURSELF => 29, self::ADULT_CONTENT => 30, self::PARTNER_PACKAGE => 31, self::USAGE_PERCENT => 32, self::STORAGE_USAGE => 33, self::EIGHTY_PERCENT_WARNING => 34, self::USAGE_LIMIT_WARNING => 35, self::MONITOR_USAGE => 36, self::PRIORITY_GROUP_ID => 37, self::PARTNER_GROUP_TYPE => 38, self::PARTNER_PARENT_ID => 39, self::KMC_VERSION => 40, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'partner_name' => 1, 'partner_alias' => 2, 'url1' => 3, 'url2' => 4, 'secret' => 5, 'admin_secret' => 6, 'max_number_of_hits_per_day' => 7, 'appear_in_search' => 8, 'debug_level' => 9, 'invalid_login_count' => 10, 'created_at' => 11, 'updated_at' => 12, 'anonymous_kuser_id' => 13, 'ks_max_expiry_in_seconds' => 14, 'create_user_on_demand' => 15, 'prefix' => 16, 'admin_name' => 17, 'admin_email' => 18, 'description' => 19, 'commercial_use' => 20, 'moderate_content' => 21, 'notify' => 22, 'custom_data' => 23, 'service_config_id' => 24, 'status' => 25, 'content_categories' => 26, 'type' => 27, 'phone' => 28, 'describe_yourself' => 29, 'adult_content' => 30, 'partner_package' => 31, 'usage_percent' => 32, 'storage_usage' => 33, 'eighty_percent_warning' => 34, 'usage_limit_warning' => 35, 'monitor_usage' => 36, 'priority_group_id' => 37, 'partner_group_type' => 38, 'partner_parent_id' => 39, 'kmc_version' => 40, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, )
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
	 * @param      string $column The column name for current table. (i.e. PartnerPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(PartnerPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(PartnerPeer::ID);
		$criteria->addSelectColumn(PartnerPeer::PARTNER_NAME);
		$criteria->addSelectColumn(PartnerPeer::PARTNER_ALIAS);
		$criteria->addSelectColumn(PartnerPeer::URL1);
		$criteria->addSelectColumn(PartnerPeer::URL2);
		$criteria->addSelectColumn(PartnerPeer::SECRET);
		$criteria->addSelectColumn(PartnerPeer::ADMIN_SECRET);
		$criteria->addSelectColumn(PartnerPeer::MAX_NUMBER_OF_HITS_PER_DAY);
		$criteria->addSelectColumn(PartnerPeer::APPEAR_IN_SEARCH);
		$criteria->addSelectColumn(PartnerPeer::DEBUG_LEVEL);
		$criteria->addSelectColumn(PartnerPeer::INVALID_LOGIN_COUNT);
		$criteria->addSelectColumn(PartnerPeer::CREATED_AT);
		$criteria->addSelectColumn(PartnerPeer::UPDATED_AT);
		$criteria->addSelectColumn(PartnerPeer::ANONYMOUS_KUSER_ID);
		$criteria->addSelectColumn(PartnerPeer::KS_MAX_EXPIRY_IN_SECONDS);
		$criteria->addSelectColumn(PartnerPeer::CREATE_USER_ON_DEMAND);
		$criteria->addSelectColumn(PartnerPeer::PREFIX);
		$criteria->addSelectColumn(PartnerPeer::ADMIN_NAME);
		$criteria->addSelectColumn(PartnerPeer::ADMIN_EMAIL);
		$criteria->addSelectColumn(PartnerPeer::DESCRIPTION);
		$criteria->addSelectColumn(PartnerPeer::COMMERCIAL_USE);
		$criteria->addSelectColumn(PartnerPeer::MODERATE_CONTENT);
		$criteria->addSelectColumn(PartnerPeer::NOTIFY);
		$criteria->addSelectColumn(PartnerPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(PartnerPeer::SERVICE_CONFIG_ID);
		$criteria->addSelectColumn(PartnerPeer::STATUS);
		$criteria->addSelectColumn(PartnerPeer::CONTENT_CATEGORIES);
		$criteria->addSelectColumn(PartnerPeer::TYPE);
		$criteria->addSelectColumn(PartnerPeer::PHONE);
		$criteria->addSelectColumn(PartnerPeer::DESCRIBE_YOURSELF);
		$criteria->addSelectColumn(PartnerPeer::ADULT_CONTENT);
		$criteria->addSelectColumn(PartnerPeer::PARTNER_PACKAGE);
		$criteria->addSelectColumn(PartnerPeer::USAGE_PERCENT);
		$criteria->addSelectColumn(PartnerPeer::STORAGE_USAGE);
		$criteria->addSelectColumn(PartnerPeer::EIGHTY_PERCENT_WARNING);
		$criteria->addSelectColumn(PartnerPeer::USAGE_LIMIT_WARNING);
		$criteria->addSelectColumn(PartnerPeer::MONITOR_USAGE);
		$criteria->addSelectColumn(PartnerPeer::PRIORITY_GROUP_ID);
		$criteria->addSelectColumn(PartnerPeer::PARTNER_GROUP_TYPE);
		$criteria->addSelectColumn(PartnerPeer::PARTNER_PARENT_ID);
		$criteria->addSelectColumn(PartnerPeer::KMC_VERSION);
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
		$criteria->setPrimaryTableName(PartnerPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			PartnerPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		PartnerPeer::attachCriteriaFilter($criteria);

		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'PartnerPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// set the connection to slave server
		$con = PartnerPeer::alternativeCon ($con);
		
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
	 * @return     Partner
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = PartnerPeer::doSelect($critcopy, $con);
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
			$objFromPool = PartnerPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				PartnerPeer::addInstanceToPool($curObject);
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
			PartnerPeer::addInstanceToPool($curResult);
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
		$criteria = PartnerPeer::prepareCriteriaForSelect($criteria);
		
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_SELECT,
			'PartnerPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			PartnerPeer::filterSelectResults($cachedResult);
			PartnerPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}

		$con = PartnerPeer::alternativeCon($con);
		
		$queryResult = PartnerPeer::populateObjects(BasePeer::doSelect($criteria, $con));
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
		}
		
		PartnerPeer::filterSelectResults($queryResult);
		PartnerPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = PartnerPeer::getCriteriaFilter();
		
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
			PartnerPeer::setDefaultCriteriaFilter();
		
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
		PartnerPeer::getCriteriaFilter()->applyFilter($criteria);
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
		PartnerPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = PartnerPeer::alternativeCon ( $con );
		
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
				PartnerPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			PartnerPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		PartnerPeer::attachCriteriaFilter($criteria);

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
		$con = PartnerPeer::alternativeCon($con);
		
		$criteria = PartnerPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      Partner $value A Partner object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(Partner $obj, $key = null)
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
	 * @param      mixed $value A Partner object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof Partner) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Partner object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     Partner Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to partner
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
		$cls = PartnerPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = PartnerPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = PartnerPeer::getInstanceFromPool($key))) {
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
	 * Returns the number of rows matching criteria, joining the related kuser table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkuser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(PartnerPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			PartnerPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(PartnerPeer::ANONYMOUS_KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = PartnerPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of Partner objects pre-filled with their kuser objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Partner objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		PartnerPeer::addSelectColumns($criteria);
		$startcol = (PartnerPeer::NUM_COLUMNS - PartnerPeer::NUM_LAZY_LOAD_COLUMNS);
		kuserPeer::addSelectColumns($criteria);

		$criteria->addJoin(PartnerPeer::ANONYMOUS_KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = PartnerPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = PartnerPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = PartnerPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = PartnerPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				PartnerPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kuserPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (Partner) to $obj2 (kuser)
				$obj2->addPartner($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
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
		$criteria->setPrimaryTableName(PartnerPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			PartnerPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(PartnerPeer::ANONYMOUS_KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = PartnerPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of Partner objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Partner objects.
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

		PartnerPeer::addSelectColumns($criteria);
		$startcol2 = (PartnerPeer::NUM_COLUMNS - PartnerPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(PartnerPeer::ANONYMOUS_KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = PartnerPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = PartnerPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = PartnerPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = PartnerPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				PartnerPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined kuser rows

			$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = kuserPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (Partner) to the collection in $obj2 (kuser)
				$obj2->addPartner($obj1);
			} // if joined row not null

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
	  $dbMap = Propel::getDatabaseMap(BasePartnerPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasePartnerPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new PartnerTableMap());
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
		return $withPrefix ? PartnerPeer::CLASS_DEFAULT : PartnerPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a Partner or Criteria object.
	 *
	 * @param      mixed $values Criteria or Partner object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from Partner object
		}

		if ($criteria->containsKey(PartnerPeer::ID) && $criteria->keyContainsValue(PartnerPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.PartnerPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a Partner or Criteria object.
	 *
	 * @param      mixed $values Criteria or Partner object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(PartnerPeer::ID);
			$selectCriteria->add(PartnerPeer::ID, $criteria->remove(PartnerPeer::ID), $comparison);

		} else { // $values is Partner object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the partner table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(PartnerPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			PartnerPeer::clearInstancePool();
			PartnerPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Partner or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Partner object or primary key or array of primary keys
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
			$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			PartnerPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof Partner) { // it's a model object
			// invalidate the cache for this single object
			PartnerPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(PartnerPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				PartnerPeer::removeInstanceFromPool($singleval);
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
			PartnerPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given Partner object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Partner $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Partner $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(PartnerPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(PartnerPeer::TABLE_NAME);

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

		return BasePeer::doValidate(PartnerPeer::DATABASE_NAME, PartnerPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     Partner
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = PartnerPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(PartnerPeer::DATABASE_NAME);
		$criteria->add(PartnerPeer::ID, $pk);

		$v = PartnerPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(PartnerPeer::DATABASE_NAME);
			$criteria->add(PartnerPeer::ID, $pks, Criteria::IN);
			$objs = PartnerPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasePartnerPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasePartnerPeer::buildTableMap();

