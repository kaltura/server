<?php

/**
 * Base static class for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseflavorAssetPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_asset';

	/** the related Propel class for this table */
	const OM_CLASS = 'flavorAsset';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.flavorAsset';

	/** the related TableMap class for this table */
	const TM_CLASS = 'flavorAssetTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 23;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'flavor_asset.ID';

	/** the column name for the INT_ID field */
	const INT_ID = 'flavor_asset.INT_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'flavor_asset.PARTNER_ID';

	/** the column name for the TAGS field */
	const TAGS = 'flavor_asset.TAGS';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'flavor_asset.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'flavor_asset.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'flavor_asset.DELETED_AT';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'flavor_asset.ENTRY_ID';

	/** the column name for the FLAVOR_PARAMS_ID field */
	const FLAVOR_PARAMS_ID = 'flavor_asset.FLAVOR_PARAMS_ID';

	/** the column name for the STATUS field */
	const STATUS = 'flavor_asset.STATUS';

	/** the column name for the VERSION field */
	const VERSION = 'flavor_asset.VERSION';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'flavor_asset.DESCRIPTION';

	/** the column name for the WIDTH field */
	const WIDTH = 'flavor_asset.WIDTH';

	/** the column name for the HEIGHT field */
	const HEIGHT = 'flavor_asset.HEIGHT';

	/** the column name for the BITRATE field */
	const BITRATE = 'flavor_asset.BITRATE';

	/** the column name for the FRAME_RATE field */
	const FRAME_RATE = 'flavor_asset.FRAME_RATE';

	/** the column name for the SIZE field */
	const SIZE = 'flavor_asset.SIZE';

	/** the column name for the IS_ORIGINAL field */
	const IS_ORIGINAL = 'flavor_asset.IS_ORIGINAL';

	/** the column name for the FILE_EXT field */
	const FILE_EXT = 'flavor_asset.FILE_EXT';

	/** the column name for the CONTAINER_FORMAT field */
	const CONTAINER_FORMAT = 'flavor_asset.CONTAINER_FORMAT';

	/** the column name for the VIDEO_CODEC_ID field */
	const VIDEO_CODEC_ID = 'flavor_asset.VIDEO_CODEC_ID';

	/** the column name for the TYPE field */
	const TYPE = 'flavor_asset.TYPE';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'flavor_asset.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of flavorAsset objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array flavorAsset[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'IntId', 'PartnerId', 'Tags', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'EntryId', 'FlavorParamsId', 'Status', 'Version', 'Description', 'Width', 'Height', 'Bitrate', 'FrameRate', 'Size', 'IsOriginal', 'FileExt', 'ContainerFormat', 'VideoCodecId', 'Type', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'intId', 'partnerId', 'tags', 'createdAt', 'updatedAt', 'deletedAt', 'entryId', 'flavorParamsId', 'status', 'version', 'description', 'width', 'height', 'bitrate', 'frameRate', 'size', 'isOriginal', 'fileExt', 'containerFormat', 'videoCodecId', 'type', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::INT_ID, self::PARTNER_ID, self::TAGS, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::ENTRY_ID, self::FLAVOR_PARAMS_ID, self::STATUS, self::VERSION, self::DESCRIPTION, self::WIDTH, self::HEIGHT, self::BITRATE, self::FRAME_RATE, self::SIZE, self::IS_ORIGINAL, self::FILE_EXT, self::CONTAINER_FORMAT, self::VIDEO_CODEC_ID, self::TYPE, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'int_id', 'partner_id', 'tags', 'created_at', 'updated_at', 'deleted_at', 'entry_id', 'flavor_params_id', 'status', 'version', 'description', 'width', 'height', 'bitrate', 'frame_rate', 'size', 'is_original', 'file_ext', 'container_format', 'video_codec_id', 'type', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'IntId' => 1, 'PartnerId' => 2, 'Tags' => 3, 'CreatedAt' => 4, 'UpdatedAt' => 5, 'DeletedAt' => 6, 'EntryId' => 7, 'FlavorParamsId' => 8, 'Status' => 9, 'Version' => 10, 'Description' => 11, 'Width' => 12, 'Height' => 13, 'Bitrate' => 14, 'FrameRate' => 15, 'Size' => 16, 'IsOriginal' => 17, 'FileExt' => 18, 'ContainerFormat' => 19, 'VideoCodecId' => 20, 'Type' => 21, 'CustomData' => 22, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'intId' => 1, 'partnerId' => 2, 'tags' => 3, 'createdAt' => 4, 'updatedAt' => 5, 'deletedAt' => 6, 'entryId' => 7, 'flavorParamsId' => 8, 'status' => 9, 'version' => 10, 'description' => 11, 'width' => 12, 'height' => 13, 'bitrate' => 14, 'frameRate' => 15, 'size' => 16, 'isOriginal' => 17, 'fileExt' => 18, 'containerFormat' => 19, 'videoCodecId' => 20, 'type' => 21, 'customData' => 22, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::INT_ID => 1, self::PARTNER_ID => 2, self::TAGS => 3, self::CREATED_AT => 4, self::UPDATED_AT => 5, self::DELETED_AT => 6, self::ENTRY_ID => 7, self::FLAVOR_PARAMS_ID => 8, self::STATUS => 9, self::VERSION => 10, self::DESCRIPTION => 11, self::WIDTH => 12, self::HEIGHT => 13, self::BITRATE => 14, self::FRAME_RATE => 15, self::SIZE => 16, self::IS_ORIGINAL => 17, self::FILE_EXT => 18, self::CONTAINER_FORMAT => 19, self::VIDEO_CODEC_ID => 20, self::TYPE => 21, self::CUSTOM_DATA => 22, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'int_id' => 1, 'partner_id' => 2, 'tags' => 3, 'created_at' => 4, 'updated_at' => 5, 'deleted_at' => 6, 'entry_id' => 7, 'flavor_params_id' => 8, 'status' => 9, 'version' => 10, 'description' => 11, 'width' => 12, 'height' => 13, 'bitrate' => 14, 'frame_rate' => 15, 'size' => 16, 'is_original' => 17, 'file_ext' => 18, 'container_format' => 19, 'video_codec_id' => 20, 'type' => 21, 'custom_data' => 22, ),
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
	 * @param      string $column The column name for current table. (i.e. flavorAssetPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(flavorAssetPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(flavorAssetPeer::ID);
		$criteria->addSelectColumn(flavorAssetPeer::INT_ID);
		$criteria->addSelectColumn(flavorAssetPeer::PARTNER_ID);
		$criteria->addSelectColumn(flavorAssetPeer::TAGS);
		$criteria->addSelectColumn(flavorAssetPeer::CREATED_AT);
		$criteria->addSelectColumn(flavorAssetPeer::UPDATED_AT);
		$criteria->addSelectColumn(flavorAssetPeer::DELETED_AT);
		$criteria->addSelectColumn(flavorAssetPeer::ENTRY_ID);
		$criteria->addSelectColumn(flavorAssetPeer::FLAVOR_PARAMS_ID);
		$criteria->addSelectColumn(flavorAssetPeer::STATUS);
		$criteria->addSelectColumn(flavorAssetPeer::VERSION);
		$criteria->addSelectColumn(flavorAssetPeer::DESCRIPTION);
		$criteria->addSelectColumn(flavorAssetPeer::WIDTH);
		$criteria->addSelectColumn(flavorAssetPeer::HEIGHT);
		$criteria->addSelectColumn(flavorAssetPeer::BITRATE);
		$criteria->addSelectColumn(flavorAssetPeer::FRAME_RATE);
		$criteria->addSelectColumn(flavorAssetPeer::SIZE);
		$criteria->addSelectColumn(flavorAssetPeer::IS_ORIGINAL);
		$criteria->addSelectColumn(flavorAssetPeer::FILE_EXT);
		$criteria->addSelectColumn(flavorAssetPeer::CONTAINER_FORMAT);
		$criteria->addSelectColumn(flavorAssetPeer::VIDEO_CODEC_ID);
		$criteria->addSelectColumn(flavorAssetPeer::TYPE);
		$criteria->addSelectColumn(flavorAssetPeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

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
	 * @return     flavorAsset
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = flavorAssetPeer::doSelect($critcopy, $con);
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
		return flavorAssetPeer::populateObjects(flavorAssetPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(flavorAssetPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = flavorAssetPeer::getCriteriaFilter();
		
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
			flavorAssetPeer::setDefaultCriteriaFilter();
		
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
		flavorAssetPeer::getCriteriaFilter()->applyFilter($criteria);
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
		flavorAssetPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = flavorAssetPeer::alternativeCon ( $con );
		
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
		$con = flavorAssetPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				flavorAssetPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		flavorAssetPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      flavorAsset $value A flavorAsset object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(flavorAsset $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIntId();
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
	 * @param      mixed $value A flavorAsset object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof flavorAsset) {
				$key = (string) $value->getIntId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or flavorAsset object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     flavorAsset Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to flavor_asset
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
		if ($row[$startcol + 1] === null) {
			return null;
		}
		return (string) $row[$startcol + 1];
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
			$key = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = flavorAssetPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = flavorAssetPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				flavorAssetPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related flavorParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinflavorParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorAsset objects pre-filled with their entry objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorAsset objects.
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

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol = (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);
		entryPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorAssetPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = flavorAssetPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorAssetPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (flavorAsset) to $obj2 (entry)
				$obj2->addflavorAsset($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorAsset objects pre-filled with their flavorParams objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorAsset objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinflavorParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol = (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);
		flavorParamsPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorAssetPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = flavorAssetPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorAssetPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = flavorParamsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = flavorParamsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = flavorParamsPeer::getOMClass($row, $startcol);
					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					flavorParamsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (flavorAsset) to $obj2 (flavorParams)
				$obj2->addflavorAsset($obj1);

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
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of flavorAsset objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorAsset objects.
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

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol2 = (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorParamsPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (flavorParamsPeer::NUM_COLUMNS - flavorParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorAssetPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorAssetPeer::getOMClass($row, 0);
        $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorAssetPeer::addInstanceToPool($obj1, $key1);
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
				} // if obj2 loaded

				// Add the $obj1 (flavorAsset) to the collection in $obj2 (entry)
				$obj2->addflavorAsset($obj1);
			} // if joined row not null

			// Add objects for joined flavorParams rows

			$key3 = flavorParamsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = flavorParamsPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = flavorParamsPeer::getOMClass($row, $startcol3);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					flavorParamsPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (flavorAsset) to the collection in $obj3 (flavorParams)
				$obj3->addflavorAsset($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related flavorParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptflavorParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorAssetPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorAssetPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorAssetPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorAsset objects pre-filled with all related objects except entry.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorAsset objects.
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

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol2 = (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (flavorParamsPeer::NUM_COLUMNS - flavorParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorAssetPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorAssetPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorAssetPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorAssetPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined flavorParams rows

				$key2 = flavorParamsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = flavorParamsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = flavorParamsPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					flavorParamsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (flavorAsset) to the collection in $obj2 (flavorParams)
				$obj2->addflavorAsset($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorAsset objects pre-filled with all related objects except flavorParams.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorAsset objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptflavorParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol2 = (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorAssetPeer::ENTRY_ID, entryPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorAssetPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorAssetPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorAssetPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (flavorAsset) to the collection in $obj2 (entry)
				$obj2->addflavorAsset($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseflavorAssetPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseflavorAssetPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new flavorAssetTableMap());
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

			$omClass = $row[$colnum + 21];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a flavorAsset or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorAsset object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorAssetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from flavorAsset object
		}

		if ($criteria->containsKey(flavorAssetPeer::INT_ID) && $criteria->keyContainsValue(flavorAssetPeer::INT_ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.flavorAssetPeer::INT_ID.')');
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
	 * Method perform an UPDATE on the database, given a flavorAsset or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorAsset object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorAssetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(flavorAssetPeer::INT_ID);
			$selectCriteria->add(flavorAssetPeer::INT_ID, $criteria->remove(flavorAssetPeer::INT_ID), $comparison);

		} else { // $values is flavorAsset object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the flavor_asset table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorAssetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(flavorAssetPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			flavorAssetPeer::clearInstancePool();
			flavorAssetPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a flavorAsset or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or flavorAsset object or primary key or array of primary keys
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
			$con = Propel::getConnection(flavorAssetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			flavorAssetPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof flavorAsset) { // it's a model object
			// invalidate the cache for this single object
			flavorAssetPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(flavorAssetPeer::INT_ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				flavorAssetPeer::removeInstanceFromPool($singleval);
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
			flavorAssetPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given flavorAsset object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      flavorAsset $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(flavorAsset $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(flavorAssetPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(flavorAssetPeer::TABLE_NAME);

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

		return BasePeer::doValidate(flavorAssetPeer::DATABASE_NAME, flavorAssetPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     flavorAsset
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = flavorAssetPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(flavorAssetPeer::DATABASE_NAME);
		$criteria->add(flavorAssetPeer::INT_ID, $pk);

		$v = flavorAssetPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(flavorAssetPeer::DATABASE_NAME);
			$criteria->add(flavorAssetPeer::INT_ID, $pks, Criteria::IN);
			$objs = flavorAssetPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseflavorAssetPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseflavorAssetPeer::buildTableMap();

