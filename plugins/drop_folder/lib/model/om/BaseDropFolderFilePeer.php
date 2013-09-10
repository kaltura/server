<?php

/**
 * Base static class for performing query and update operations on the 'drop_folder_file' table.
 *
 * 
 *
 * @package plugins.dropFolder
 * @subpackage model.om
 */
abstract class BaseDropFolderFilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'drop_folder_file';

	/** the related Propel class for this table */
	const OM_CLASS = 'DropFolderFile';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.dropFolder.DropFolderFile';

	/** the related TableMap class for this table */
	const TM_CLASS = 'DropFolderFileTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 22;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'drop_folder_file.ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'drop_folder_file.PARTNER_ID';

	/** the column name for the DROP_FOLDER_ID field */
	const DROP_FOLDER_ID = 'drop_folder_file.DROP_FOLDER_ID';

	/** the column name for the FILE_NAME field */
	const FILE_NAME = 'drop_folder_file.FILE_NAME';

	/** the column name for the TYPE field */
	const TYPE = 'drop_folder_file.TYPE';

	/** the column name for the STATUS field */
	const STATUS = 'drop_folder_file.STATUS';

	/** the column name for the FILE_SIZE field */
	const FILE_SIZE = 'drop_folder_file.FILE_SIZE';

	/** the column name for the FILE_SIZE_LAST_SET_AT field */
	const FILE_SIZE_LAST_SET_AT = 'drop_folder_file.FILE_SIZE_LAST_SET_AT';

	/** the column name for the ERROR_CODE field */
	const ERROR_CODE = 'drop_folder_file.ERROR_CODE';

	/** the column name for the ERROR_DESCRIPTION field */
	const ERROR_DESCRIPTION = 'drop_folder_file.ERROR_DESCRIPTION';

	/** the column name for the PARSED_SLUG field */
	const PARSED_SLUG = 'drop_folder_file.PARSED_SLUG';

	/** the column name for the PARSED_FLAVOR field */
	const PARSED_FLAVOR = 'drop_folder_file.PARSED_FLAVOR';

	/** the column name for the LEAD_DROP_FOLDER_FILE_ID field */
	const LEAD_DROP_FOLDER_FILE_ID = 'drop_folder_file.LEAD_DROP_FOLDER_FILE_ID';

	/** the column name for the DELETED_DROP_FOLDER_FILE_ID field */
	const DELETED_DROP_FOLDER_FILE_ID = 'drop_folder_file.DELETED_DROP_FOLDER_FILE_ID';

	/** the column name for the MD5_FILE_NAME field */
	const MD5_FILE_NAME = 'drop_folder_file.MD5_FILE_NAME';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'drop_folder_file.ENTRY_ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'drop_folder_file.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'drop_folder_file.UPDATED_AT';

	/** the column name for the UPLOAD_START_DETECTED_AT field */
	const UPLOAD_START_DETECTED_AT = 'drop_folder_file.UPLOAD_START_DETECTED_AT';

	/** the column name for the UPLOAD_END_DETECTED_AT field */
	const UPLOAD_END_DETECTED_AT = 'drop_folder_file.UPLOAD_END_DETECTED_AT';

	/** the column name for the IMPORT_STARTED_AT field */
	const IMPORT_STARTED_AT = 'drop_folder_file.IMPORT_STARTED_AT';

	/** the column name for the IMPORT_ENDED_AT field */
	const IMPORT_ENDED_AT = 'drop_folder_file.IMPORT_ENDED_AT';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'drop_folder_file.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of DropFolderFile objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array DropFolderFile[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'PartnerId', 'DropFolderId', 'FileName', 'Type', 'Status', 'FileSize', 'FileSizeLastSetAt', 'ErrorCode', 'ErrorDescription', 'ParsedSlug', 'ParsedFlavor', 'LeadDropFolderFileId', 'DeletedDropFolderFileId', 'Md5FileName', 'EntryId', 'CreatedAt', 'UpdatedAt', 'UploadStartDetectedAt', 'UploadEndDetectedAt', 'ImportStartedAt', 'ImportEndedAt', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'partnerId', 'dropFolderId', 'fileName', 'type', 'status', 'fileSize', 'fileSizeLastSetAt', 'errorCode', 'errorDescription', 'parsedSlug', 'parsedFlavor', 'leadDropFolderFileId', 'deletedDropFolderFileId', 'md5FileName', 'entryId', 'createdAt', 'updatedAt', 'uploadStartDetectedAt', 'uploadEndDetectedAt', 'importStartedAt', 'importEndedAt', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PARTNER_ID, self::DROP_FOLDER_ID, self::FILE_NAME, self::TYPE, self::STATUS, self::FILE_SIZE, self::FILE_SIZE_LAST_SET_AT, self::ERROR_CODE, self::ERROR_DESCRIPTION, self::PARSED_SLUG, self::PARSED_FLAVOR, self::LEAD_DROP_FOLDER_FILE_ID, self::DELETED_DROP_FOLDER_FILE_ID, self::MD5_FILE_NAME, self::ENTRY_ID, self::CREATED_AT, self::UPDATED_AT, self::UPLOAD_START_DETECTED_AT, self::UPLOAD_END_DETECTED_AT, self::IMPORT_STARTED_AT, self::IMPORT_ENDED_AT, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'partner_id', 'drop_folder_id', 'file_name', 'type', 'status', 'file_size', 'file_size_last_set_at', 'error_code', 'error_description', 'parsed_slug', 'parsed_flavor', 'lead_drop_folder_file_id', 'deleted_drop_folder_file_id', 'md5_file_name', 'entry_id', 'created_at', 'updated_at', 'upload_start_detected_at', 'upload_end_detected_at', 'import_started_at', 'import_ended_at', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'PartnerId' => 1, 'DropFolderId' => 2, 'FileName' => 3, 'Type' => 4, 'Status' => 5, 'FileSize' => 6, 'FileSizeLastSetAt' => 7, 'ErrorCode' => 8, 'ErrorDescription' => 9, 'ParsedSlug' => 10, 'ParsedFlavor' => 11, 'LeadDropFolderFileId' => 12, 'DeletedDropFolderFileId' => 13, 'Md5FileName' => 14, 'EntryId' => 15, 'CreatedAt' => 16, 'UpdatedAt' => 17, 'UploadStartDetectedAt' => 18, 'UploadEndDetectedAt' => 19, 'ImportStartedAt' => 20, 'ImportEndedAt' => 21, 'CustomData' => 22, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'partnerId' => 1, 'dropFolderId' => 2, 'fileName' => 3, 'type' => 4, 'status' => 5, 'fileSize' => 6, 'fileSizeLastSetAt' => 7, 'errorCode' => 8, 'errorDescription' => 9, 'parsedSlug' => 10, 'parsedFlavor' => 11, 'leadDropFolderFileId' => 12, 'deletedDropFolderFileId' => 13, 'md5FileName' => 14, 'entryId' => 15, 'createdAt' => 16, 'updatedAt' => 17, 'uploadStartDetectedAt' => 18, 'uploadEndDetectedAt' => 19, 'importStartedAt' => 20, 'importEndedAt' => 21, 'customData' => 22, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PARTNER_ID => 1, self::DROP_FOLDER_ID => 2, self::FILE_NAME => 3, self::TYPE => 4, self::STATUS => 5, self::FILE_SIZE => 6, self::FILE_SIZE_LAST_SET_AT => 7, self::ERROR_CODE => 8, self::ERROR_DESCRIPTION => 9, self::PARSED_SLUG => 10, self::PARSED_FLAVOR => 11, self::LEAD_DROP_FOLDER_FILE_ID => 12, self::DELETED_DROP_FOLDER_FILE_ID => 13, self::MD5_FILE_NAME => 14, self::ENTRY_ID => 15, self::CREATED_AT => 16, self::UPDATED_AT => 17, self::UPLOAD_START_DETECTED_AT => 18, self::UPLOAD_END_DETECTED_AT => 19, self::IMPORT_STARTED_AT => 20, self::IMPORT_ENDED_AT => 21, self::CUSTOM_DATA => 22, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'partner_id' => 1, 'drop_folder_id' => 2, 'file_name' => 3, 'type' => 4, 'status' => 5, 'file_size' => 6, 'file_size_last_set_at' => 7, 'error_code' => 8, 'error_description' => 9, 'parsed_slug' => 10, 'parsed_flavor' => 11, 'lead_drop_folder_file_id' => 12, 'deleted_drop_folder_file_id' => 13, 'md5_file_name' => 14, 'entry_id' => 15, 'created_at' => 16, 'updated_at' => 17, 'upload_start_detected_at' => 18, 'upload_end_detected_at' => 19, 'import_started_at' => 20, 'import_ended_at' => 21, 'custom_data' => 22, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
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
	 * @param      string $column The column name for current table. (i.e. DropFolderFilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(DropFolderFilePeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(DropFolderFilePeer::ID);
		$criteria->addSelectColumn(DropFolderFilePeer::PARTNER_ID);
		$criteria->addSelectColumn(DropFolderFilePeer::DROP_FOLDER_ID);
		$criteria->addSelectColumn(DropFolderFilePeer::FILE_NAME);
		$criteria->addSelectColumn(DropFolderFilePeer::TYPE);
		$criteria->addSelectColumn(DropFolderFilePeer::STATUS);
		$criteria->addSelectColumn(DropFolderFilePeer::FILE_SIZE);
		$criteria->addSelectColumn(DropFolderFilePeer::FILE_SIZE_LAST_SET_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::ERROR_CODE);
		$criteria->addSelectColumn(DropFolderFilePeer::ERROR_DESCRIPTION);
		$criteria->addSelectColumn(DropFolderFilePeer::PARSED_SLUG);
		$criteria->addSelectColumn(DropFolderFilePeer::PARSED_FLAVOR);
		$criteria->addSelectColumn(DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID);
		$criteria->addSelectColumn(DropFolderFilePeer::DELETED_DROP_FOLDER_FILE_ID);
		$criteria->addSelectColumn(DropFolderFilePeer::MD5_FILE_NAME);
		$criteria->addSelectColumn(DropFolderFilePeer::ENTRY_ID);
		$criteria->addSelectColumn(DropFolderFilePeer::CREATED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::UPDATED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::UPLOAD_START_DETECTED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::UPLOAD_END_DETECTED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::IMPORT_STARTED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::IMPORT_ENDED_AT);
		$criteria->addSelectColumn(DropFolderFilePeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(DropFolderFilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			DropFolderFilePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		DropFolderFilePeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'DropFolderFilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = DropFolderFilePeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     DropFolderFile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = DropFolderFilePeer::doSelect($critcopy, $con);
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
			$objFromPool = DropFolderFilePeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				DropFolderFilePeer::addInstanceToPool($curObject);
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
			DropFolderFilePeer::addInstanceToPool($curResult);
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
		$criteriaForSelect = DropFolderFilePeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'DropFolderFilePeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			DropFolderFilePeer::filterSelectResults($cachedResult, $criteriaForSelect);
			DropFolderFilePeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = DropFolderFilePeer::alternativeCon($con, $queryDB);
		
		$queryResult = DropFolderFilePeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		DropFolderFilePeer::filterSelectResults($queryResult, $criteria);
		
		DropFolderFilePeer::addInstancesToPool($queryResult);
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
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = DropFolderFilePeer::getCriteriaFilter();
		
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
			DropFolderFilePeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('DropFolderFile');
		if ($partnerCriteria)
		{
			call_user_func_array(array('DropFolderFilePeer','addPartnerToCriteria'), $partnerCriteria);
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
		DropFolderFilePeer::getCriteriaFilter()->applyFilter($criteria);
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
		DropFolderFilePeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = DropFolderFilePeer::alternativeCon ( $con );
		
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
				DropFolderFilePeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			DropFolderFilePeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		DropFolderFilePeer::attachCriteriaFilter($criteria);

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
		$con = DropFolderFilePeer::alternativeCon($con);
		
		$criteria = DropFolderFilePeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      DropFolderFile $value A DropFolderFile object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(DropFolderFile $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
			} // if key === null
			self::$instances[$key] = $obj;
			kMemoryManager::registerPeer('DropFolderFilePeer');
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
	 * @param      mixed $value A DropFolderFile object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof DropFolderFile) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or DropFolderFile object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     DropFolderFile Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to drop_folder_file
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
			$key = DropFolderFilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = DropFolderFilePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = DropFolderFilePeer::getOMClass($row, 0);
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
	  $dbMap = Propel::getDatabaseMap(BaseDropFolderFilePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseDropFolderFilePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new DropFolderFileTableMap());
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

			$omClass = $row[$colnum + 2];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a DropFolderFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DropFolderFile object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from DropFolderFile object
		}

		if ($criteria->containsKey(DropFolderFilePeer::ID) && $criteria->keyContainsValue(DropFolderFilePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.DropFolderFilePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a DropFolderFile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DropFolderFile object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(DropFolderFilePeer::ID);
			$selectCriteria->add(DropFolderFilePeer::ID, $criteria->remove(DropFolderFilePeer::ID), $comparison);

		} else { // $values is DropFolderFile object
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
	 * Method to DELETE all rows from the drop_folder_file table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(DropFolderFilePeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			DropFolderFilePeer::clearInstancePool();
			DropFolderFilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a DropFolderFile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or DropFolderFile object or primary key or array of primary keys
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
			$con = Propel::getConnection(DropFolderFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			DropFolderFilePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof DropFolderFile) { // it's a model object
			// invalidate the cache for this single object
			DropFolderFilePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(DropFolderFilePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				DropFolderFilePeer::removeInstanceFromPool($singleval);
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
			DropFolderFilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given DropFolderFile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      DropFolderFile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(DropFolderFile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(DropFolderFilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(DropFolderFilePeer::TABLE_NAME);

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

		return BasePeer::doValidate(DropFolderFilePeer::DATABASE_NAME, DropFolderFilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     DropFolderFile
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = DropFolderFilePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(DropFolderFilePeer::DATABASE_NAME);
		$criteria->add(DropFolderFilePeer::ID, $pk);

		$v = DropFolderFilePeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(DropFolderFilePeer::DATABASE_NAME);
			$criteria->add(DropFolderFilePeer::ID, $pks, Criteria::IN);
			$objs = DropFolderFilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseDropFolderFilePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseDropFolderFilePeer::buildTableMap();

