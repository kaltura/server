<?php

/**
 * Base static class for performing query and update operations on the 'category' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasecategoryPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'category';

	/** the related Propel class for this table */
	const OM_CLASS = 'category';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.category';

	/** the related TableMap class for this table */
	const TM_CLASS = 'categoryTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 33;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'category.ID';

	/** the column name for the PARENT_ID field */
	const PARENT_ID = 'category.PARENT_ID';

	/** the column name for the DEPTH field */
	const DEPTH = 'category.DEPTH';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'category.PARTNER_ID';

	/** the column name for the NAME field */
	const NAME = 'category.NAME';

	/** the column name for the FULL_NAME field */
	const FULL_NAME = 'category.FULL_NAME';

	/** the column name for the FULL_IDS field */
	const FULL_IDS = 'category.FULL_IDS';

	/** the column name for the ENTRIES_COUNT field */
	const ENTRIES_COUNT = 'category.ENTRIES_COUNT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'category.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'category.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'category.DELETED_AT';

	/** the column name for the STATUS field */
	const STATUS = 'category.STATUS';

	/** the column name for the DIRECT_ENTRIES_COUNT field */
	const DIRECT_ENTRIES_COUNT = 'category.DIRECT_ENTRIES_COUNT';

	/** the column name for the DIRECT_SUB_CATEGORIES_COUNT field */
	const DIRECT_SUB_CATEGORIES_COUNT = 'category.DIRECT_SUB_CATEGORIES_COUNT';

	/** the column name for the MEMBERS_COUNT field */
	const MEMBERS_COUNT = 'category.MEMBERS_COUNT';

	/** the column name for the PENDING_MEMBERS_COUNT field */
	const PENDING_MEMBERS_COUNT = 'category.PENDING_MEMBERS_COUNT';

	/** the column name for the PENDING_ENTRIES_COUNT field */
	const PENDING_ENTRIES_COUNT = 'category.PENDING_ENTRIES_COUNT';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'category.DESCRIPTION';

	/** the column name for the TAGS field */
	const TAGS = 'category.TAGS';

	/** the column name for the DISPLAY_IN_SEARCH field */
	const DISPLAY_IN_SEARCH = 'category.DISPLAY_IN_SEARCH';

	/** the column name for the PRIVACY field */
	const PRIVACY = 'category.PRIVACY';

	/** the column name for the INHERITANCE_TYPE field */
	const INHERITANCE_TYPE = 'category.INHERITANCE_TYPE';

	/** the column name for the USER_JOIN_POLICY field */
	const USER_JOIN_POLICY = 'category.USER_JOIN_POLICY';

	/** the column name for the DEFAULT_PERMISSION_LEVEL field */
	const DEFAULT_PERMISSION_LEVEL = 'category.DEFAULT_PERMISSION_LEVEL';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'category.KUSER_ID';

	/** the column name for the PUSER_ID field */
	const PUSER_ID = 'category.PUSER_ID';

	/** the column name for the REFERENCE_ID field */
	const REFERENCE_ID = 'category.REFERENCE_ID';

	/** the column name for the CONTRIBUTION_POLICY field */
	const CONTRIBUTION_POLICY = 'category.CONTRIBUTION_POLICY';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'category.CUSTOM_DATA';

	/** the column name for the PRIVACY_CONTEXT field */
	const PRIVACY_CONTEXT = 'category.PRIVACY_CONTEXT';

	/** the column name for the PRIVACY_CONTEXTS field */
	const PRIVACY_CONTEXTS = 'category.PRIVACY_CONTEXTS';

	/** the column name for the INHERITED_PARENT_ID field */
	const INHERITED_PARENT_ID = 'category.INHERITED_PARENT_ID';

	/** the column name for the MODERATION field */
	const MODERATION = 'category.MODERATION';

	/**
	 * An identiy map to hold any loaded instances of category objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array category[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ParentId', 'Depth', 'PartnerId', 'Name', 'FullName', 'FullIds', 'EntriesCount', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'Status', 'DirectEntriesCount', 'DirectSubCategoriesCount', 'MembersCount', 'PendingMembersCount', 'PendingEntriesCount', 'Description', 'Tags', 'DisplayInSearch', 'Privacy', 'InheritanceType', 'UserJoinPolicy', 'DefaultPermissionLevel', 'KuserId', 'PuserId', 'ReferenceId', 'ContributionPolicy', 'CustomData', 'PrivacyContext', 'PrivacyContexts', 'InheritedParentId', 'Moderation', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'parentId', 'depth', 'partnerId', 'name', 'fullName', 'fullIds', 'entriesCount', 'createdAt', 'updatedAt', 'deletedAt', 'status', 'directEntriesCount', 'directSubCategoriesCount', 'membersCount', 'pendingMembersCount', 'pendingEntriesCount', 'description', 'tags', 'displayInSearch', 'privacy', 'inheritanceType', 'userJoinPolicy', 'defaultPermissionLevel', 'kuserId', 'puserId', 'referenceId', 'contributionPolicy', 'customData', 'privacyContext', 'privacyContexts', 'inheritedParentId', 'moderation', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PARENT_ID, self::DEPTH, self::PARTNER_ID, self::NAME, self::FULL_NAME, self::FULL_IDS, self::ENTRIES_COUNT, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::STATUS, self::DIRECT_ENTRIES_COUNT, self::DIRECT_SUB_CATEGORIES_COUNT, self::MEMBERS_COUNT, self::PENDING_MEMBERS_COUNT, self::PENDING_ENTRIES_COUNT, self::DESCRIPTION, self::TAGS, self::DISPLAY_IN_SEARCH, self::PRIVACY, self::INHERITANCE_TYPE, self::USER_JOIN_POLICY, self::DEFAULT_PERMISSION_LEVEL, self::KUSER_ID, self::PUSER_ID, self::REFERENCE_ID, self::CONTRIBUTION_POLICY, self::CUSTOM_DATA, self::PRIVACY_CONTEXT, self::PRIVACY_CONTEXTS, self::INHERITED_PARENT_ID, self::MODERATION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'parent_id', 'depth', 'partner_id', 'name', 'full_name', 'full_ids', 'entries_count', 'created_at', 'updated_at', 'deleted_at', 'status', 'direct_entries_count', 'direct_sub_categories_count', 'members_count', 'pending_members_count', 'pending_entries_count', 'description', 'tags', 'display_in_search', 'privacy', 'inheritance_type', 'user_join_policy', 'default_permission_level', 'kuser_id', 'puser_id', 'reference_id', 'contribution_policy', 'custom_data', 'privacy_context', 'privacy_contexts', 'inherited_parent_id', 'moderation', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ParentId' => 1, 'Depth' => 2, 'PartnerId' => 3, 'Name' => 4, 'FullName' => 5, 'FullIds' => 6, 'EntriesCount' => 7, 'CreatedAt' => 8, 'UpdatedAt' => 9, 'DeletedAt' => 10, 'Status' => 11, 'DirectEntriesCount' => 12, 'DirectSubCategoriesCount' => 13, 'MembersCount' => 14, 'PendingMembersCount' => 15, 'PendingEntriesCount' => 16, 'Description' => 17, 'Tags' => 18, 'DisplayInSearch' => 19, 'Privacy' => 20, 'InheritanceType' => 21, 'UserJoinPolicy' => 22, 'DefaultPermissionLevel' => 23, 'KuserId' => 24, 'PuserId' => 25, 'ReferenceId' => 26, 'ContributionPolicy' => 27, 'CustomData' => 28, 'PrivacyContext' => 29, 'PrivacyContexts' => 30, 'InheritedParentId' => 31, 'Moderation' => 32, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'parentId' => 1, 'depth' => 2, 'partnerId' => 3, 'name' => 4, 'fullName' => 5, 'fullIds' => 6, 'entriesCount' => 7, 'createdAt' => 8, 'updatedAt' => 9, 'deletedAt' => 10, 'status' => 11, 'directEntriesCount' => 12, 'directSubCategoriesCount' => 13, 'membersCount' => 14, 'pendingMembersCount' => 15, 'pendingEntriesCount' => 16, 'description' => 17, 'tags' => 18, 'displayInSearch' => 19, 'privacy' => 20, 'inheritanceType' => 21, 'userJoinPolicy' => 22, 'defaultPermissionLevel' => 23, 'kuserId' => 24, 'puserId' => 25, 'referenceId' => 26, 'contributionPolicy' => 27, 'customData' => 28, 'privacyContext' => 29, 'privacyContexts' => 30, 'inheritedParentId' => 31, 'moderation' => 32, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PARENT_ID => 1, self::DEPTH => 2, self::PARTNER_ID => 3, self::NAME => 4, self::FULL_NAME => 5, self::FULL_IDS => 6, self::ENTRIES_COUNT => 7, self::CREATED_AT => 8, self::UPDATED_AT => 9, self::DELETED_AT => 10, self::STATUS => 11, self::DIRECT_ENTRIES_COUNT => 12, self::DIRECT_SUB_CATEGORIES_COUNT => 13, self::MEMBERS_COUNT => 14, self::PENDING_MEMBERS_COUNT => 15, self::PENDING_ENTRIES_COUNT => 16, self::DESCRIPTION => 17, self::TAGS => 18, self::DISPLAY_IN_SEARCH => 19, self::PRIVACY => 20, self::INHERITANCE_TYPE => 21, self::USER_JOIN_POLICY => 22, self::DEFAULT_PERMISSION_LEVEL => 23, self::KUSER_ID => 24, self::PUSER_ID => 25, self::REFERENCE_ID => 26, self::CONTRIBUTION_POLICY => 27, self::CUSTOM_DATA => 28, self::PRIVACY_CONTEXT => 29, self::PRIVACY_CONTEXTS => 30, self::INHERITED_PARENT_ID => 31, self::MODERATION => 32, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'parent_id' => 1, 'depth' => 2, 'partner_id' => 3, 'name' => 4, 'full_name' => 5, 'full_ids' => 6, 'entries_count' => 7, 'created_at' => 8, 'updated_at' => 9, 'deleted_at' => 10, 'status' => 11, 'direct_entries_count' => 12, 'direct_sub_categories_count' => 13, 'members_count' => 14, 'pending_members_count' => 15, 'pending_entries_count' => 16, 'description' => 17, 'tags' => 18, 'display_in_search' => 19, 'privacy' => 20, 'inheritance_type' => 21, 'user_join_policy' => 22, 'default_permission_level' => 23, 'kuser_id' => 24, 'puser_id' => 25, 'reference_id' => 26, 'contribution_policy' => 27, 'custom_data' => 28, 'privacy_context' => 29, 'privacy_contexts' => 30, 'inherited_parent_id' => 31, 'moderation' => 32, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
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
	 * @param      string $column The column name for current table. (i.e. categoryPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(categoryPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(categoryPeer::ID);
		$criteria->addSelectColumn(categoryPeer::PARENT_ID);
		$criteria->addSelectColumn(categoryPeer::DEPTH);
		$criteria->addSelectColumn(categoryPeer::PARTNER_ID);
		$criteria->addSelectColumn(categoryPeer::NAME);
		$criteria->addSelectColumn(categoryPeer::FULL_NAME);
		$criteria->addSelectColumn(categoryPeer::FULL_IDS);
		$criteria->addSelectColumn(categoryPeer::ENTRIES_COUNT);
		$criteria->addSelectColumn(categoryPeer::CREATED_AT);
		$criteria->addSelectColumn(categoryPeer::UPDATED_AT);
		$criteria->addSelectColumn(categoryPeer::DELETED_AT);
		$criteria->addSelectColumn(categoryPeer::STATUS);
		$criteria->addSelectColumn(categoryPeer::DIRECT_ENTRIES_COUNT);
		$criteria->addSelectColumn(categoryPeer::DIRECT_SUB_CATEGORIES_COUNT);
		$criteria->addSelectColumn(categoryPeer::MEMBERS_COUNT);
		$criteria->addSelectColumn(categoryPeer::PENDING_MEMBERS_COUNT);
		$criteria->addSelectColumn(categoryPeer::PENDING_ENTRIES_COUNT);
		$criteria->addSelectColumn(categoryPeer::DESCRIPTION);
		$criteria->addSelectColumn(categoryPeer::TAGS);
		$criteria->addSelectColumn(categoryPeer::DISPLAY_IN_SEARCH);
		$criteria->addSelectColumn(categoryPeer::PRIVACY);
		$criteria->addSelectColumn(categoryPeer::INHERITANCE_TYPE);
		$criteria->addSelectColumn(categoryPeer::USER_JOIN_POLICY);
		$criteria->addSelectColumn(categoryPeer::DEFAULT_PERMISSION_LEVEL);
		$criteria->addSelectColumn(categoryPeer::KUSER_ID);
		$criteria->addSelectColumn(categoryPeer::PUSER_ID);
		$criteria->addSelectColumn(categoryPeer::REFERENCE_ID);
		$criteria->addSelectColumn(categoryPeer::CONTRIBUTION_POLICY);
		$criteria->addSelectColumn(categoryPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(categoryPeer::PRIVACY_CONTEXT);
		$criteria->addSelectColumn(categoryPeer::PRIVACY_CONTEXTS);
		$criteria->addSelectColumn(categoryPeer::INHERITED_PARENT_ID);
		$criteria->addSelectColumn(categoryPeer::MODERATION);
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
		$criteria->setPrimaryTableName(categoryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			categoryPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		categoryPeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'categoryPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = categoryPeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     category
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = categoryPeer::doSelect($critcopy, $con);
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
			$objFromPool = categoryPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				categoryPeer::addInstanceToPool($curObject);
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
					categoryPeer::addInstanceToPool($curResult);
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
		$criteriaForSelect = categoryPeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'categoryPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			categoryPeer::filterSelectResults($cachedResult, $criteriaForSelect);
			categoryPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = categoryPeer::alternativeCon($con, $queryDB);
		
		$queryResult = categoryPeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		categoryPeer::filterSelectResults($queryResult, $criteria);
		
		categoryPeer::addInstancesToPool($queryResult);
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
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = categoryPeer::getCriteriaFilter();
		
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
			categoryPeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('category');
		if ($partnerCriteria)
		{
			call_user_func_array(array('categoryPeer','addPartnerToCriteria'), $partnerCriteria);
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
		categoryPeer::getCriteriaFilter()->applyFilter($criteria);
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
				$criteria->addAnd(self::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
				
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
					if($kalturaNetwork)
					{
						$criterionNetwork = $criteria->getNewCriterion(self::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
						$criterion->addOr($criterionNetwork);
					}
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
		categoryPeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = categoryPeer::alternativeCon ( $con );
		
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
				categoryPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			categoryPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		categoryPeer::attachCriteriaFilter($criteria);

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
		$con = categoryPeer::alternativeCon($con);
		
		$criteria = categoryPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      category $value A category object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(category $obj, $key = null)
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
				kMemoryManager::registerPeer('categoryPeer');
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
	 * @param      mixed $value A category object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof category) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or category object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     category Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to category
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
		$cls = categoryPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = categoryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = categoryPeer::getInstanceFromPool($key))) {
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
	  $dbMap = Propel::getDatabaseMap(BasecategoryPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasecategoryPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new categoryTableMap());
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
		return $withPrefix ? categoryPeer::CLASS_DEFAULT : categoryPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a category or Criteria object.
	 *
	 * @param      mixed $values Criteria or category object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from category object
		}

		if ($criteria->containsKey(categoryPeer::ID) && $criteria->keyContainsValue(categoryPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.categoryPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a category or Criteria object.
	 *
	 * @param      mixed $values Criteria or category object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(categoryPeer::ID);
			$selectCriteria->add(categoryPeer::ID, $criteria->remove(categoryPeer::ID), $comparison);

		} else { // $values is category object
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
	 * Method to DELETE all rows from the category table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(categoryPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			categoryPeer::clearInstancePool();
			categoryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a category or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or category object or primary key or array of primary keys
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
			$con = Propel::getConnection(categoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			categoryPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof category) { // it's a model object
			// invalidate the cache for this single object
			categoryPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(categoryPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				categoryPeer::removeInstanceFromPool($singleval);
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
			categoryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given category object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      category $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(category $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(categoryPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(categoryPeer::TABLE_NAME);

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

		return BasePeer::doValidate(categoryPeer::DATABASE_NAME, categoryPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     category
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = categoryPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(categoryPeer::DATABASE_NAME);
		$criteria->add(categoryPeer::ID, $pk);

		$v = categoryPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(categoryPeer::DATABASE_NAME);
			$criteria->add(categoryPeer::ID, $pks, Criteria::IN);
			$objs = categoryPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasecategoryPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasecategoryPeer::buildTableMap();

