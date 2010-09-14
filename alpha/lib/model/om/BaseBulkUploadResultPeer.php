<?php

/**
 * Base static class for performing query and update operations on the 'bulk_upload_result' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseBulkUploadResultPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'bulk_upload_result';

	/** the related Propel class for this table */
	const OM_CLASS = 'BulkUploadResult';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.BulkUploadResult';

	/** the related TableMap class for this table */
	const TM_CLASS = 'BulkUploadResultTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 24;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'bulk_upload_result.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'bulk_upload_result.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'bulk_upload_result.UPDATED_AT';

	/** the column name for the BULK_UPLOAD_JOB_ID field */
	const BULK_UPLOAD_JOB_ID = 'bulk_upload_result.BULK_UPLOAD_JOB_ID';

	/** the column name for the LINE_INDEX field */
	const LINE_INDEX = 'bulk_upload_result.LINE_INDEX';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'bulk_upload_result.PARTNER_ID';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'bulk_upload_result.ENTRY_ID';

	/** the column name for the ENTRY_STATUS field */
	const ENTRY_STATUS = 'bulk_upload_result.ENTRY_STATUS';

	/** the column name for the ROW_DATA field */
	const ROW_DATA = 'bulk_upload_result.ROW_DATA';

	/** the column name for the TITLE field */
	const TITLE = 'bulk_upload_result.TITLE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'bulk_upload_result.DESCRIPTION';

	/** the column name for the TAGS field */
	const TAGS = 'bulk_upload_result.TAGS';

	/** the column name for the URL field */
	const URL = 'bulk_upload_result.URL';

	/** the column name for the CONTENT_TYPE field */
	const CONTENT_TYPE = 'bulk_upload_result.CONTENT_TYPE';

	/** the column name for the CONVERSION_PROFILE_ID field */
	const CONVERSION_PROFILE_ID = 'bulk_upload_result.CONVERSION_PROFILE_ID';

	/** the column name for the ACCESS_CONTROL_PROFILE_ID field */
	const ACCESS_CONTROL_PROFILE_ID = 'bulk_upload_result.ACCESS_CONTROL_PROFILE_ID';

	/** the column name for the CATEGORY field */
	const CATEGORY = 'bulk_upload_result.CATEGORY';

	/** the column name for the SCHEDULE_START_DATE field */
	const SCHEDULE_START_DATE = 'bulk_upload_result.SCHEDULE_START_DATE';

	/** the column name for the SCHEDULE_END_DATE field */
	const SCHEDULE_END_DATE = 'bulk_upload_result.SCHEDULE_END_DATE';

	/** the column name for the THUMBNAIL_URL field */
	const THUMBNAIL_URL = 'bulk_upload_result.THUMBNAIL_URL';

	/** the column name for the THUMBNAIL_SAVED field */
	const THUMBNAIL_SAVED = 'bulk_upload_result.THUMBNAIL_SAVED';

	/** the column name for the PARTNER_DATA field */
	const PARTNER_DATA = 'bulk_upload_result.PARTNER_DATA';

	/** the column name for the ERROR_DESCRIPTION field */
	const ERROR_DESCRIPTION = 'bulk_upload_result.ERROR_DESCRIPTION';

	/** the column name for the PLUGINS_DATA field */
	const PLUGINS_DATA = 'bulk_upload_result.PLUGINS_DATA';

	/**
	 * An identiy map to hold any loaded instances of BulkUploadResult objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array BulkUploadResult[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'BulkUploadJobId', 'LineIndex', 'PartnerId', 'EntryId', 'EntryStatus', 'RowData', 'Title', 'Description', 'Tags', 'Url', 'ContentType', 'ConversionProfileId', 'AccessControlProfileId', 'Category', 'ScheduleStartDate', 'ScheduleEndDate', 'ThumbnailUrl', 'ThumbnailSaved', 'PartnerData', 'ErrorDescription', 'PluginsData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'bulkUploadJobId', 'lineIndex', 'partnerId', 'entryId', 'entryStatus', 'rowData', 'title', 'description', 'tags', 'url', 'contentType', 'conversionProfileId', 'accessControlProfileId', 'category', 'scheduleStartDate', 'scheduleEndDate', 'thumbnailUrl', 'thumbnailSaved', 'partnerData', 'errorDescription', 'pluginsData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::BULK_UPLOAD_JOB_ID, self::LINE_INDEX, self::PARTNER_ID, self::ENTRY_ID, self::ENTRY_STATUS, self::ROW_DATA, self::TITLE, self::DESCRIPTION, self::TAGS, self::URL, self::CONTENT_TYPE, self::CONVERSION_PROFILE_ID, self::ACCESS_CONTROL_PROFILE_ID, self::CATEGORY, self::SCHEDULE_START_DATE, self::SCHEDULE_END_DATE, self::THUMBNAIL_URL, self::THUMBNAIL_SAVED, self::PARTNER_DATA, self::ERROR_DESCRIPTION, self::PLUGINS_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'bulk_upload_job_id', 'line_index', 'partner_id', 'entry_id', 'entry_status', 'row_data', 'title', 'description', 'tags', 'url', 'content_type', 'conversion_profile_id', 'access_control_profile_id', 'category', 'schedule_start_date', 'schedule_end_date', 'thumbnail_url', 'thumbnail_saved', 'partner_data', 'error_description', 'plugins_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'BulkUploadJobId' => 3, 'LineIndex' => 4, 'PartnerId' => 5, 'EntryId' => 6, 'EntryStatus' => 7, 'RowData' => 8, 'Title' => 9, 'Description' => 10, 'Tags' => 11, 'Url' => 12, 'ContentType' => 13, 'ConversionProfileId' => 14, 'AccessControlProfileId' => 15, 'Category' => 16, 'ScheduleStartDate' => 17, 'ScheduleEndDate' => 18, 'ThumbnailUrl' => 19, 'ThumbnailSaved' => 20, 'PartnerData' => 21, 'ErrorDescription' => 22, 'PluginsData' => 23, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'bulkUploadJobId' => 3, 'lineIndex' => 4, 'partnerId' => 5, 'entryId' => 6, 'entryStatus' => 7, 'rowData' => 8, 'title' => 9, 'description' => 10, 'tags' => 11, 'url' => 12, 'contentType' => 13, 'conversionProfileId' => 14, 'accessControlProfileId' => 15, 'category' => 16, 'scheduleStartDate' => 17, 'scheduleEndDate' => 18, 'thumbnailUrl' => 19, 'thumbnailSaved' => 20, 'partnerData' => 21, 'errorDescription' => 22, 'pluginsData' => 23, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::BULK_UPLOAD_JOB_ID => 3, self::LINE_INDEX => 4, self::PARTNER_ID => 5, self::ENTRY_ID => 6, self::ENTRY_STATUS => 7, self::ROW_DATA => 8, self::TITLE => 9, self::DESCRIPTION => 10, self::TAGS => 11, self::URL => 12, self::CONTENT_TYPE => 13, self::CONVERSION_PROFILE_ID => 14, self::ACCESS_CONTROL_PROFILE_ID => 15, self::CATEGORY => 16, self::SCHEDULE_START_DATE => 17, self::SCHEDULE_END_DATE => 18, self::THUMBNAIL_URL => 19, self::THUMBNAIL_SAVED => 20, self::PARTNER_DATA => 21, self::ERROR_DESCRIPTION => 22, self::PLUGINS_DATA => 23, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'bulk_upload_job_id' => 3, 'line_index' => 4, 'partner_id' => 5, 'entry_id' => 6, 'entry_status' => 7, 'row_data' => 8, 'title' => 9, 'description' => 10, 'tags' => 11, 'url' => 12, 'content_type' => 13, 'conversion_profile_id' => 14, 'access_control_profile_id' => 15, 'category' => 16, 'schedule_start_date' => 17, 'schedule_end_date' => 18, 'thumbnail_url' => 19, 'thumbnail_saved' => 20, 'partner_data' => 21, 'error_description' => 22, 'plugins_data' => 23, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
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
	 * @param      string $column The column name for current table. (i.e. BulkUploadResultPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(BulkUploadResultPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(BulkUploadResultPeer::ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::CREATED_AT);
		$criteria->addSelectColumn(BulkUploadResultPeer::UPDATED_AT);
		$criteria->addSelectColumn(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::LINE_INDEX);
		$criteria->addSelectColumn(BulkUploadResultPeer::PARTNER_ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::ENTRY_ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::ENTRY_STATUS);
		$criteria->addSelectColumn(BulkUploadResultPeer::ROW_DATA);
		$criteria->addSelectColumn(BulkUploadResultPeer::TITLE);
		$criteria->addSelectColumn(BulkUploadResultPeer::DESCRIPTION);
		$criteria->addSelectColumn(BulkUploadResultPeer::TAGS);
		$criteria->addSelectColumn(BulkUploadResultPeer::URL);
		$criteria->addSelectColumn(BulkUploadResultPeer::CONTENT_TYPE);
		$criteria->addSelectColumn(BulkUploadResultPeer::CONVERSION_PROFILE_ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::ACCESS_CONTROL_PROFILE_ID);
		$criteria->addSelectColumn(BulkUploadResultPeer::CATEGORY);
		$criteria->addSelectColumn(BulkUploadResultPeer::SCHEDULE_START_DATE);
		$criteria->addSelectColumn(BulkUploadResultPeer::SCHEDULE_END_DATE);
		$criteria->addSelectColumn(BulkUploadResultPeer::THUMBNAIL_URL);
		$criteria->addSelectColumn(BulkUploadResultPeer::THUMBNAIL_SAVED);
		$criteria->addSelectColumn(BulkUploadResultPeer::PARTNER_DATA);
		$criteria->addSelectColumn(BulkUploadResultPeer::ERROR_DESCRIPTION);
		$criteria->addSelectColumn(BulkUploadResultPeer::PLUGINS_DATA);
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
		$criteria->setPrimaryTableName(BulkUploadResultPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			BulkUploadResultPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = BulkUploadResultPeer::doCountStmt($criteria, $con);

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
	 * @return     BulkUploadResult
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = BulkUploadResultPeer::doSelect($critcopy, $con);
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
		return BulkUploadResultPeer::populateObjects(BulkUploadResultPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = BulkUploadResultPeer::getCriteriaFilter();
		
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
			BulkUploadResultPeer::setDefaultCriteriaFilter();
		
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
		BulkUploadResultPeer::getCriteriaFilter()->applyFilter($criteria);
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
		BulkUploadResultPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = BulkUploadResultPeer::alternativeCon ( $con );
		
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
		$con = BulkUploadResultPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				BulkUploadResultPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			BulkUploadResultPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		BulkUploadResultPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      BulkUploadResult $value A BulkUploadResult object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(BulkUploadResult $obj, $key = null)
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
	 * @param      mixed $value A BulkUploadResult object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof BulkUploadResult) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or BulkUploadResult object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     BulkUploadResult Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to bulk_upload_result
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
		$cls = BulkUploadResultPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = BulkUploadResultPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = BulkUploadResultPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				BulkUploadResultPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseBulkUploadResultPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseBulkUploadResultPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new BulkUploadResultTableMap());
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
		return $withPrefix ? BulkUploadResultPeer::CLASS_DEFAULT : BulkUploadResultPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a BulkUploadResult or Criteria object.
	 *
	 * @param      mixed $values Criteria or BulkUploadResult object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from BulkUploadResult object
		}

		if ($criteria->containsKey(BulkUploadResultPeer::ID) && $criteria->keyContainsValue(BulkUploadResultPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.BulkUploadResultPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a BulkUploadResult or Criteria object.
	 *
	 * @param      mixed $values Criteria or BulkUploadResult object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(BulkUploadResultPeer::ID);
			$selectCriteria->add(BulkUploadResultPeer::ID, $criteria->remove(BulkUploadResultPeer::ID), $comparison);

		} else { // $values is BulkUploadResult object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the bulk_upload_result table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(BulkUploadResultPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			BulkUploadResultPeer::clearInstancePool();
			BulkUploadResultPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a BulkUploadResult or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or BulkUploadResult object or primary key or array of primary keys
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
			$con = Propel::getConnection(BulkUploadResultPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			BulkUploadResultPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof BulkUploadResult) { // it's a model object
			// invalidate the cache for this single object
			BulkUploadResultPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(BulkUploadResultPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				BulkUploadResultPeer::removeInstanceFromPool($singleval);
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
			BulkUploadResultPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given BulkUploadResult object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      BulkUploadResult $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(BulkUploadResult $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(BulkUploadResultPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(BulkUploadResultPeer::TABLE_NAME);

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

		return BasePeer::doValidate(BulkUploadResultPeer::DATABASE_NAME, BulkUploadResultPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     BulkUploadResult
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = BulkUploadResultPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(BulkUploadResultPeer::DATABASE_NAME);
		$criteria->add(BulkUploadResultPeer::ID, $pk);

		$v = BulkUploadResultPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(BulkUploadResultPeer::DATABASE_NAME);
			$criteria->add(BulkUploadResultPeer::ID, $pks, Criteria::IN);
			$objs = BulkUploadResultPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseBulkUploadResultPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseBulkUploadResultPeer::buildTableMap();

