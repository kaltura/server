<?php

/**
 * Base static class for performing query and update operations on the 'conversion_profile_2' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseconversionProfile2Peer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'conversion_profile_2';

	/** the related Propel class for this table */
	const OM_CLASS = 'conversionProfile2';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.conversionProfile2';

	/** the related TableMap class for this table */
	const TM_CLASS = 'conversionProfile2TableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 20;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'conversion_profile_2.ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'conversion_profile_2.PARTNER_ID';

	/** the column name for the NAME field */
	const NAME = 'conversion_profile_2.NAME';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'conversion_profile_2.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'conversion_profile_2.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'conversion_profile_2.DELETED_AT';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'conversion_profile_2.DESCRIPTION';

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'conversion_profile_2.SYSTEM_NAME';

	/** the column name for the TAGS field */
	const TAGS = 'conversion_profile_2.TAGS';

	/** the column name for the STATUS field */
	const STATUS = 'conversion_profile_2.STATUS';

	/** the column name for the DEFAULT_ENTRY_ID field */
	const DEFAULT_ENTRY_ID = 'conversion_profile_2.DEFAULT_ENTRY_ID';

	/** the column name for the CROP_LEFT field */
	const CROP_LEFT = 'conversion_profile_2.CROP_LEFT';

	/** the column name for the CROP_TOP field */
	const CROP_TOP = 'conversion_profile_2.CROP_TOP';

	/** the column name for the CROP_WIDTH field */
	const CROP_WIDTH = 'conversion_profile_2.CROP_WIDTH';

	/** the column name for the CROP_HEIGHT field */
	const CROP_HEIGHT = 'conversion_profile_2.CROP_HEIGHT';

	/** the column name for the CLIP_START field */
	const CLIP_START = 'conversion_profile_2.CLIP_START';

	/** the column name for the CLIP_DURATION field */
	const CLIP_DURATION = 'conversion_profile_2.CLIP_DURATION';

	/** the column name for the INPUT_TAGS_MAP field */
	const INPUT_TAGS_MAP = 'conversion_profile_2.INPUT_TAGS_MAP';

	/** the column name for the CREATION_MODE field */
	const CREATION_MODE = 'conversion_profile_2.CREATION_MODE';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'conversion_profile_2.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of conversionProfile2 objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array conversionProfile2[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'PartnerId', 'Name', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'Description', 'SystemName', 'Tags', 'Status', 'DefaultEntryId', 'CropLeft', 'CropTop', 'CropWidth', 'CropHeight', 'ClipStart', 'ClipDuration', 'InputTagsMap', 'CreationMode', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'partnerId', 'name', 'createdAt', 'updatedAt', 'deletedAt', 'description', 'systemName', 'tags', 'status', 'defaultEntryId', 'cropLeft', 'cropTop', 'cropWidth', 'cropHeight', 'clipStart', 'clipDuration', 'inputTagsMap', 'creationMode', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PARTNER_ID, self::NAME, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::DESCRIPTION, self::SYSTEM_NAME, self::TAGS, self::STATUS, self::DEFAULT_ENTRY_ID, self::CROP_LEFT, self::CROP_TOP, self::CROP_WIDTH, self::CROP_HEIGHT, self::CLIP_START, self::CLIP_DURATION, self::INPUT_TAGS_MAP, self::CREATION_MODE, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'partner_id', 'name', 'created_at', 'updated_at', 'deleted_at', 'description', 'system_name', 'tags', 'status', 'default_entry_id', 'crop_left', 'crop_top', 'crop_width', 'crop_height', 'clip_start', 'clip_duration', 'input_tags_map', 'creation_mode', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'PartnerId' => 1, 'Name' => 2, 'CreatedAt' => 3, 'UpdatedAt' => 4, 'DeletedAt' => 5, 'Description' => 6, 'SystemName' => 7, 'Tags' => 8, 'Status' => 9, 'DefaultEntryId' => 10, 'CropLeft' => 11, 'CropTop' => 12, 'CropWidth' => 13, 'CropHeight' => 14, 'ClipStart' => 15, 'ClipDuration' => 16, 'InputTagsMap' => 17, 'CreationMode' => 18, 'CustomData' => 19, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'partnerId' => 1, 'name' => 2, 'createdAt' => 3, 'updatedAt' => 4, 'deletedAt' => 5, 'description' => 6, 'systemName' => 7, 'tags' => 8, 'status' => 9, 'defaultEntryId' => 10, 'cropLeft' => 11, 'cropTop' => 12, 'cropWidth' => 13, 'cropHeight' => 14, 'clipStart' => 15, 'clipDuration' => 16, 'inputTagsMap' => 17, 'creationMode' => 18, 'customData' => 19, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PARTNER_ID => 1, self::NAME => 2, self::CREATED_AT => 3, self::UPDATED_AT => 4, self::DELETED_AT => 5, self::DESCRIPTION => 6, self::SYSTEM_NAME => 7, self::TAGS => 8, self::STATUS => 9, self::DEFAULT_ENTRY_ID => 10, self::CROP_LEFT => 11, self::CROP_TOP => 12, self::CROP_WIDTH => 13, self::CROP_HEIGHT => 14, self::CLIP_START => 15, self::CLIP_DURATION => 16, self::INPUT_TAGS_MAP => 17, self::CREATION_MODE => 18, self::CUSTOM_DATA => 19, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'partner_id' => 1, 'name' => 2, 'created_at' => 3, 'updated_at' => 4, 'deleted_at' => 5, 'description' => 6, 'system_name' => 7, 'tags' => 8, 'status' => 9, 'default_entry_id' => 10, 'crop_left' => 11, 'crop_top' => 12, 'crop_width' => 13, 'crop_height' => 14, 'clip_start' => 15, 'clip_duration' => 16, 'input_tags_map' => 17, 'creation_mode' => 18, 'custom_data' => 19, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
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
	 * @param      string $column The column name for current table. (i.e. conversionProfile2Peer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(conversionProfile2Peer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(conversionProfile2Peer::ID);
		$criteria->addSelectColumn(conversionProfile2Peer::PARTNER_ID);
		$criteria->addSelectColumn(conversionProfile2Peer::NAME);
		$criteria->addSelectColumn(conversionProfile2Peer::CREATED_AT);
		$criteria->addSelectColumn(conversionProfile2Peer::UPDATED_AT);
		$criteria->addSelectColumn(conversionProfile2Peer::DELETED_AT);
		$criteria->addSelectColumn(conversionProfile2Peer::DESCRIPTION);
		$criteria->addSelectColumn(conversionProfile2Peer::SYSTEM_NAME);
		$criteria->addSelectColumn(conversionProfile2Peer::TAGS);
		$criteria->addSelectColumn(conversionProfile2Peer::STATUS);
		$criteria->addSelectColumn(conversionProfile2Peer::DEFAULT_ENTRY_ID);
		$criteria->addSelectColumn(conversionProfile2Peer::CROP_LEFT);
		$criteria->addSelectColumn(conversionProfile2Peer::CROP_TOP);
		$criteria->addSelectColumn(conversionProfile2Peer::CROP_WIDTH);
		$criteria->addSelectColumn(conversionProfile2Peer::CROP_HEIGHT);
		$criteria->addSelectColumn(conversionProfile2Peer::CLIP_START);
		$criteria->addSelectColumn(conversionProfile2Peer::CLIP_DURATION);
		$criteria->addSelectColumn(conversionProfile2Peer::INPUT_TAGS_MAP);
		$criteria->addSelectColumn(conversionProfile2Peer::CREATION_MODE);
		$criteria->addSelectColumn(conversionProfile2Peer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(conversionProfile2Peer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			conversionProfile2Peer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = conversionProfile2Peer::doCountStmt($criteria, $con);

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
	 * @return     conversionProfile2
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = conversionProfile2Peer::doSelect($critcopy, $con);
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
		return conversionProfile2Peer::populateObjects(conversionProfile2Peer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(conversionProfile2Peer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = conversionProfile2Peer::getCriteriaFilter();
		
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
			conversionProfile2Peer::setDefaultCriteriaFilter();
		
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
		conversionProfile2Peer::getCriteriaFilter()->applyFilter($criteria);
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
					$partners[] = $partnerId;
					
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
		conversionProfile2Peer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = conversionProfile2Peer::alternativeCon ( $con );
		
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
		$con = conversionProfile2Peer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				conversionProfile2Peer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			conversionProfile2Peer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		conversionProfile2Peer::attachCriteriaFilter($criteria);
		
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
	 * @param      conversionProfile2 $value A conversionProfile2 object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(conversionProfile2 $obj, $key = null)
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
	 * @param      mixed $value A conversionProfile2 object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof conversionProfile2) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or conversionProfile2 object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     conversionProfile2 Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to conversion_profile_2
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
		$cls = conversionProfile2Peer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = conversionProfile2Peer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				conversionProfile2Peer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseconversionProfile2Peer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseconversionProfile2Peer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new conversionProfile2TableMap());
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
		return $withPrefix ? conversionProfile2Peer::CLASS_DEFAULT : conversionProfile2Peer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a conversionProfile2 or Criteria object.
	 *
	 * @param      mixed $values Criteria or conversionProfile2 object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(conversionProfile2Peer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from conversionProfile2 object
		}

		if ($criteria->containsKey(conversionProfile2Peer::ID) && $criteria->keyContainsValue(conversionProfile2Peer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.conversionProfile2Peer::ID.')');
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
	 * Method perform an UPDATE on the database, given a conversionProfile2 or Criteria object.
	 *
	 * @param      mixed $values Criteria or conversionProfile2 object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(conversionProfile2Peer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(conversionProfile2Peer::ID);
			$selectCriteria->add(conversionProfile2Peer::ID, $criteria->remove(conversionProfile2Peer::ID), $comparison);

		} else { // $values is conversionProfile2 object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the conversion_profile_2 table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(conversionProfile2Peer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(conversionProfile2Peer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			conversionProfile2Peer::clearInstancePool();
			conversionProfile2Peer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a conversionProfile2 or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or conversionProfile2 object or primary key or array of primary keys
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
			$con = Propel::getConnection(conversionProfile2Peer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			conversionProfile2Peer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof conversionProfile2) { // it's a model object
			// invalidate the cache for this single object
			conversionProfile2Peer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(conversionProfile2Peer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				conversionProfile2Peer::removeInstanceFromPool($singleval);
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
			conversionProfile2Peer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given conversionProfile2 object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      conversionProfile2 $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(conversionProfile2 $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(conversionProfile2Peer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(conversionProfile2Peer::TABLE_NAME);

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

		return BasePeer::doValidate(conversionProfile2Peer::DATABASE_NAME, conversionProfile2Peer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     conversionProfile2
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = conversionProfile2Peer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(conversionProfile2Peer::DATABASE_NAME);
		$criteria->add(conversionProfile2Peer::ID, $pk);

		$v = conversionProfile2Peer::doSelect($criteria, $con);

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
			$criteria = new Criteria(conversionProfile2Peer::DATABASE_NAME);
			$criteria->add(conversionProfile2Peer::ID, $pks, Criteria::IN);
			$objs = conversionProfile2Peer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseconversionProfile2Peer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseconversionProfile2Peer::buildTableMap();

