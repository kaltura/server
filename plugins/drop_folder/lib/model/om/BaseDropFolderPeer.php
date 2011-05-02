<?php

/**
 * Base static class for performing query and update operations on the 'drop_folder' table.
 *
 * 
 *
 * @package plugins.dropFolder
 * @subpackage model.om
 */
abstract class BaseDropFolderPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'drop_folder';

	/** the related Propel class for this table */
	const OM_CLASS = 'DropFolder';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.dropFolder.DropFolder';

	/** the related TableMap class for this table */
	const TM_CLASS = 'DropFolderTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 17;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'drop_folder.ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'drop_folder.PARTNER_ID';

	/** the column name for the NAME field */
	const NAME = 'drop_folder.NAME';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'drop_folder.DESCRIPTION';

	/** the column name for the TYPE field */
	const TYPE = 'drop_folder.TYPE';

	/** the column name for the STATUS field */
	const STATUS = 'drop_folder.STATUS';

	/** the column name for the DC field */
	const DC = 'drop_folder.DC';

	/** the column name for the PATH field */
	const PATH = 'drop_folder.PATH';

	/** the column name for the CONVERSION_PROFILE_ID field */
	const CONVERSION_PROFILE_ID = 'drop_folder.CONVERSION_PROFILE_ID';

	/** the column name for the FILE_DELETE_POLICY field */
	const FILE_DELETE_POLICY = 'drop_folder.FILE_DELETE_POLICY';

	/** the column name for the FILE_HANDLER_TYPE field */
	const FILE_HANDLER_TYPE = 'drop_folder.FILE_HANDLER_TYPE';

	/** the column name for the FILE_NAME_PATTERNS field */
	const FILE_NAME_PATTERNS = 'drop_folder.FILE_NAME_PATTERNS';

	/** the column name for the FILE_HANDLER_CONFIG field */
	const FILE_HANDLER_CONFIG = 'drop_folder.FILE_HANDLER_CONFIG';

	/** the column name for the TAGS field */
	const TAGS = 'drop_folder.TAGS';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'drop_folder.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'drop_folder.UPDATED_AT';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'drop_folder.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of DropFolder objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array DropFolder[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'PartnerId', 'Name', 'Description', 'Type', 'Status', 'Dc', 'Path', 'ConversionProfileId', 'FileDeletePolicy', 'FileHandlerType', 'FileNamePatterns', 'FileHandlerConfig', 'Tags', 'CreatedAt', 'UpdatedAt', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'partnerId', 'name', 'description', 'type', 'status', 'dc', 'path', 'conversionProfileId', 'fileDeletePolicy', 'fileHandlerType', 'fileNamePatterns', 'fileHandlerConfig', 'tags', 'createdAt', 'updatedAt', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PARTNER_ID, self::NAME, self::DESCRIPTION, self::TYPE, self::STATUS, self::DC, self::PATH, self::CONVERSION_PROFILE_ID, self::FILE_DELETE_POLICY, self::FILE_HANDLER_TYPE, self::FILE_NAME_PATTERNS, self::FILE_HANDLER_CONFIG, self::TAGS, self::CREATED_AT, self::UPDATED_AT, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'partner_id', 'name', 'description', 'type', 'status', 'dc', 'path', 'conversion_profile_id', 'file_delete_policy', 'file_handler_type', 'file_name_patterns', 'file_handler_config', 'tags', 'created_at', 'updated_at', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'PartnerId' => 1, 'Name' => 2, 'Description' => 3, 'Type' => 4, 'Status' => 5, 'Dc' => 6, 'Path' => 7, 'ConversionProfileId' => 8, 'FileDeletePolicy' => 9, 'FileHandlerType' => 10, 'FileNamePatterns' => 11, 'FileHandlerConfig' => 12, 'Tags' => 13, 'CreatedAt' => 14, 'UpdatedAt' => 15, 'CustomData' => 16, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'partnerId' => 1, 'name' => 2, 'description' => 3, 'type' => 4, 'status' => 5, 'dc' => 6, 'path' => 7, 'conversionProfileId' => 8, 'fileDeletePolicy' => 9, 'fileHandlerType' => 10, 'fileNamePatterns' => 11, 'fileHandlerConfig' => 12, 'tags' => 13, 'createdAt' => 14, 'updatedAt' => 15, 'customData' => 16, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PARTNER_ID => 1, self::NAME => 2, self::DESCRIPTION => 3, self::TYPE => 4, self::STATUS => 5, self::DC => 6, self::PATH => 7, self::CONVERSION_PROFILE_ID => 8, self::FILE_DELETE_POLICY => 9, self::FILE_HANDLER_TYPE => 10, self::FILE_NAME_PATTERNS => 11, self::FILE_HANDLER_CONFIG => 12, self::TAGS => 13, self::CREATED_AT => 14, self::UPDATED_AT => 15, self::CUSTOM_DATA => 16, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'partner_id' => 1, 'name' => 2, 'description' => 3, 'type' => 4, 'status' => 5, 'dc' => 6, 'path' => 7, 'conversion_profile_id' => 8, 'file_delete_policy' => 9, 'file_handler_type' => 10, 'file_name_patterns' => 11, 'file_handler_config' => 12, 'tags' => 13, 'created_at' => 14, 'updated_at' => 15, 'custom_data' => 16, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
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
	 * @param      string $column The column name for current table. (i.e. DropFolderPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(DropFolderPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(DropFolderPeer::ID);
		$criteria->addSelectColumn(DropFolderPeer::PARTNER_ID);
		$criteria->addSelectColumn(DropFolderPeer::NAME);
		$criteria->addSelectColumn(DropFolderPeer::DESCRIPTION);
		$criteria->addSelectColumn(DropFolderPeer::TYPE);
		$criteria->addSelectColumn(DropFolderPeer::STATUS);
		$criteria->addSelectColumn(DropFolderPeer::DC);
		$criteria->addSelectColumn(DropFolderPeer::PATH);
		$criteria->addSelectColumn(DropFolderPeer::CONVERSION_PROFILE_ID);
		$criteria->addSelectColumn(DropFolderPeer::FILE_DELETE_POLICY);
		$criteria->addSelectColumn(DropFolderPeer::FILE_HANDLER_TYPE);
		$criteria->addSelectColumn(DropFolderPeer::FILE_NAME_PATTERNS);
		$criteria->addSelectColumn(DropFolderPeer::FILE_HANDLER_CONFIG);
		$criteria->addSelectColumn(DropFolderPeer::TAGS);
		$criteria->addSelectColumn(DropFolderPeer::CREATED_AT);
		$criteria->addSelectColumn(DropFolderPeer::UPDATED_AT);
		$criteria->addSelectColumn(DropFolderPeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(DropFolderPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			DropFolderPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = DropFolderPeer::doCountStmt($criteria, $con);

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
	 * @return     DropFolder
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = DropFolderPeer::doSelect($critcopy, $con);
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
		return DropFolderPeer::populateObjects(DropFolderPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(DropFolderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = DropFolderPeer::getCriteriaFilter();
		
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
			DropFolderPeer::setDefaultCriteriaFilter();
		
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
		DropFolderPeer::getCriteriaFilter()->applyFilter($criteria);
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
			if(empty($partnerGroup) && empty($kalturaNetwork))
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
				$criterion = null;
				if($partnerGroup)
				{
					// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
					$partners = explode(',', trim($partnerGroup));
					foreach($partners as &$p)
						trim($p); // make sure there are not leading or trailing spaces
	
					// add the partner_id to the partner_group
					if (!in_array($partnerId, $partners))
					{
						// NOTE: we need to add the partner as a string since we want all
						// the PATNER_ID IN () values to be of the same type.
						// otherwise mysql will fail choosing the right index and will
						// do a full table scan
						$partners[] = "".$partnerId;
					}
					
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
				}	
				
				$criteria->addAnd($criterion);
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
		DropFolderPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = DropFolderPeer::alternativeCon ( $con );
		
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
		$con = DropFolderPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				DropFolderPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			DropFolderPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		DropFolderPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      DropFolder $value A DropFolder object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(DropFolder $obj, $key = null)
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
	 * @param      mixed $value A DropFolder object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof DropFolder) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or DropFolder object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     DropFolder Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to drop_folder
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
		$cls = DropFolderPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = DropFolderPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = DropFolderPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				DropFolderPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseDropFolderPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseDropFolderPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new DropFolderTableMap());
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
		return $withPrefix ? DropFolderPeer::CLASS_DEFAULT : DropFolderPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a DropFolder or Criteria object.
	 *
	 * @param      mixed $values Criteria or DropFolder object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from DropFolder object
		}

		if ($criteria->containsKey(DropFolderPeer::ID) && $criteria->keyContainsValue(DropFolderPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.DropFolderPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a DropFolder or Criteria object.
	 *
	 * @param      mixed $values Criteria or DropFolder object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(DropFolderPeer::ID);
			$selectCriteria->add(DropFolderPeer::ID, $criteria->remove(DropFolderPeer::ID), $comparison);

		} else { // $values is DropFolder object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the drop_folder table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DropFolderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(DropFolderPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			DropFolderPeer::clearInstancePool();
			DropFolderPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a DropFolder or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or DropFolder object or primary key or array of primary keys
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
			$con = Propel::getConnection(DropFolderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			DropFolderPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof DropFolder) { // it's a model object
			// invalidate the cache for this single object
			DropFolderPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(DropFolderPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				DropFolderPeer::removeInstanceFromPool($singleval);
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
			DropFolderPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given DropFolder object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      DropFolder $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(DropFolder $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(DropFolderPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(DropFolderPeer::TABLE_NAME);

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

		return BasePeer::doValidate(DropFolderPeer::DATABASE_NAME, DropFolderPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     DropFolder
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = DropFolderPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(DropFolderPeer::DATABASE_NAME);
		$criteria->add(DropFolderPeer::ID, $pk);

		$v = DropFolderPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(DropFolderPeer::DATABASE_NAME);
			$criteria->add(DropFolderPeer::ID, $pks, Criteria::IN);
			$objs = DropFolderPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseDropFolderPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseDropFolderPeer::buildTableMap();

