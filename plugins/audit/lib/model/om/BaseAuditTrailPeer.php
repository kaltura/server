<?php

/**
 * Base static class for performing query and update operations on the 'audit_trail' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseAuditTrailPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'audit_trail';

	/** the related Propel class for this table */
	const OM_CLASS = 'AuditTrail';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.AuditTrail';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AuditTrailTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 23;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'audit_trail.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'audit_trail.CREATED_AT';

	/** the column name for the PARSED_AT field */
	const PARSED_AT = 'audit_trail.PARSED_AT';

	/** the column name for the STATUS field */
	const STATUS = 'audit_trail.STATUS';

	/** the column name for the OBJECT_TYPE field */
	const OBJECT_TYPE = 'audit_trail.OBJECT_TYPE';

	/** the column name for the OBJECT_ID field */
	const OBJECT_ID = 'audit_trail.OBJECT_ID';

	/** the column name for the RELATED_OBJECT_ID field */
	const RELATED_OBJECT_ID = 'audit_trail.RELATED_OBJECT_ID';

	/** the column name for the RELATED_OBJECT_TYPE field */
	const RELATED_OBJECT_TYPE = 'audit_trail.RELATED_OBJECT_TYPE';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'audit_trail.ENTRY_ID';

	/** the column name for the MASTER_PARTNER_ID field */
	const MASTER_PARTNER_ID = 'audit_trail.MASTER_PARTNER_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'audit_trail.PARTNER_ID';

	/** the column name for the REQUEST_ID field */
	const REQUEST_ID = 'audit_trail.REQUEST_ID';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'audit_trail.KUSER_ID';

	/** the column name for the ACTION field */
	const ACTION = 'audit_trail.ACTION';

	/** the column name for the DATA field */
	const DATA = 'audit_trail.DATA';

	/** the column name for the KS field */
	const KS = 'audit_trail.KS';

	/** the column name for the CONTEXT field */
	const CONTEXT = 'audit_trail.CONTEXT';

	/** the column name for the ENTRY_POINT field */
	const ENTRY_POINT = 'audit_trail.ENTRY_POINT';

	/** the column name for the SERVER_NAME field */
	const SERVER_NAME = 'audit_trail.SERVER_NAME';

	/** the column name for the IP_ADDRESS field */
	const IP_ADDRESS = 'audit_trail.IP_ADDRESS';

	/** the column name for the USER_AGENT field */
	const USER_AGENT = 'audit_trail.USER_AGENT';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'audit_trail.DESCRIPTION';

	/** the column name for the ERROR_DESCRIPTION field */
	const ERROR_DESCRIPTION = 'audit_trail.ERROR_DESCRIPTION';

	/**
	 * An identiy map to hold any loaded instances of AuditTrail objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AuditTrail[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'ParsedAt', 'Status', 'ObjectType', 'ObjectId', 'RelatedObjectId', 'RelatedObjectType', 'EntryId', 'MasterPartnerId', 'PartnerId', 'RequestId', 'KuserId', 'Action', 'Data', 'Ks', 'Context', 'EntryPoint', 'ServerName', 'IpAddress', 'UserAgent', 'Description', 'ErrorDescription', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'parsedAt', 'status', 'objectType', 'objectId', 'relatedObjectId', 'relatedObjectType', 'entryId', 'masterPartnerId', 'partnerId', 'requestId', 'kuserId', 'action', 'data', 'ks', 'context', 'entryPoint', 'serverName', 'ipAddress', 'userAgent', 'description', 'errorDescription', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::PARSED_AT, self::STATUS, self::OBJECT_TYPE, self::OBJECT_ID, self::RELATED_OBJECT_ID, self::RELATED_OBJECT_TYPE, self::ENTRY_ID, self::MASTER_PARTNER_ID, self::PARTNER_ID, self::REQUEST_ID, self::KUSER_ID, self::ACTION, self::DATA, self::KS, self::CONTEXT, self::ENTRY_POINT, self::SERVER_NAME, self::IP_ADDRESS, self::USER_AGENT, self::DESCRIPTION, self::ERROR_DESCRIPTION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'parsed_at', 'status', 'object_type', 'object_id', 'related_object_id', 'related_object_type', 'entry_id', 'master_partner_id', 'partner_id', 'request_id', 'kuser_id', 'action', 'data', 'ks', 'context', 'entry_point', 'server_name', 'ip_address', 'user_agent', 'description', 'error_description', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'ParsedAt' => 2, 'Status' => 3, 'ObjectType' => 4, 'ObjectId' => 5, 'RelatedObjectId' => 6, 'RelatedObjectType' => 7, 'EntryId' => 8, 'MasterPartnerId' => 9, 'PartnerId' => 10, 'RequestId' => 11, 'KuserId' => 12, 'Action' => 13, 'Data' => 14, 'Ks' => 15, 'Context' => 16, 'EntryPoint' => 17, 'ServerName' => 18, 'IpAddress' => 19, 'UserAgent' => 20, 'Description' => 21, 'ErrorDescription' => 22, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'parsedAt' => 2, 'status' => 3, 'objectType' => 4, 'objectId' => 5, 'relatedObjectId' => 6, 'relatedObjectType' => 7, 'entryId' => 8, 'masterPartnerId' => 9, 'partnerId' => 10, 'requestId' => 11, 'kuserId' => 12, 'action' => 13, 'data' => 14, 'ks' => 15, 'context' => 16, 'entryPoint' => 17, 'serverName' => 18, 'ipAddress' => 19, 'userAgent' => 20, 'description' => 21, 'errorDescription' => 22, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::PARSED_AT => 2, self::STATUS => 3, self::OBJECT_TYPE => 4, self::OBJECT_ID => 5, self::RELATED_OBJECT_ID => 6, self::RELATED_OBJECT_TYPE => 7, self::ENTRY_ID => 8, self::MASTER_PARTNER_ID => 9, self::PARTNER_ID => 10, self::REQUEST_ID => 11, self::KUSER_ID => 12, self::ACTION => 13, self::DATA => 14, self::KS => 15, self::CONTEXT => 16, self::ENTRY_POINT => 17, self::SERVER_NAME => 18, self::IP_ADDRESS => 19, self::USER_AGENT => 20, self::DESCRIPTION => 21, self::ERROR_DESCRIPTION => 22, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'parsed_at' => 2, 'status' => 3, 'object_type' => 4, 'object_id' => 5, 'related_object_id' => 6, 'related_object_type' => 7, 'entry_id' => 8, 'master_partner_id' => 9, 'partner_id' => 10, 'request_id' => 11, 'kuser_id' => 12, 'action' => 13, 'data' => 14, 'ks' => 15, 'context' => 16, 'entry_point' => 17, 'server_name' => 18, 'ip_address' => 19, 'user_agent' => 20, 'description' => 21, 'error_description' => 22, ),
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
	 * @param      string $column The column name for current table. (i.e. AuditTrailPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AuditTrailPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(AuditTrailPeer::ID);
		$criteria->addSelectColumn(AuditTrailPeer::CREATED_AT);
		$criteria->addSelectColumn(AuditTrailPeer::PARSED_AT);
		$criteria->addSelectColumn(AuditTrailPeer::STATUS);
		$criteria->addSelectColumn(AuditTrailPeer::OBJECT_TYPE);
		$criteria->addSelectColumn(AuditTrailPeer::OBJECT_ID);
		$criteria->addSelectColumn(AuditTrailPeer::RELATED_OBJECT_ID);
		$criteria->addSelectColumn(AuditTrailPeer::RELATED_OBJECT_TYPE);
		$criteria->addSelectColumn(AuditTrailPeer::ENTRY_ID);
		$criteria->addSelectColumn(AuditTrailPeer::MASTER_PARTNER_ID);
		$criteria->addSelectColumn(AuditTrailPeer::PARTNER_ID);
		$criteria->addSelectColumn(AuditTrailPeer::REQUEST_ID);
		$criteria->addSelectColumn(AuditTrailPeer::KUSER_ID);
		$criteria->addSelectColumn(AuditTrailPeer::ACTION);
		$criteria->addSelectColumn(AuditTrailPeer::DATA);
		$criteria->addSelectColumn(AuditTrailPeer::KS);
		$criteria->addSelectColumn(AuditTrailPeer::CONTEXT);
		$criteria->addSelectColumn(AuditTrailPeer::ENTRY_POINT);
		$criteria->addSelectColumn(AuditTrailPeer::SERVER_NAME);
		$criteria->addSelectColumn(AuditTrailPeer::IP_ADDRESS);
		$criteria->addSelectColumn(AuditTrailPeer::USER_AGENT);
		$criteria->addSelectColumn(AuditTrailPeer::DESCRIPTION);
		$criteria->addSelectColumn(AuditTrailPeer::ERROR_DESCRIPTION);
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
		$criteria->setPrimaryTableName(AuditTrailPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AuditTrailPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = AuditTrailPeer::doCountStmt($criteria, $con);

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
	 * @return     AuditTrail
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AuditTrailPeer::doSelect($critcopy, $con);
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
		return AuditTrailPeer::populateObjects(AuditTrailPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = AuditTrailPeer::getCriteriaFilter();
		
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
			AuditTrailPeer::setDefaultCriteriaFilter();
		
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
		AuditTrailPeer::getCriteriaFilter()->applyFilter($criteria);
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
		AuditTrailPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = AuditTrailPeer::alternativeCon ( $con );
		
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
		$con = AuditTrailPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				AuditTrailPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			AuditTrailPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		AuditTrailPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      AuditTrail $value A AuditTrail object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(AuditTrail $obj, $key = null)
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
	 * @param      mixed $value A AuditTrail object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AuditTrail) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AuditTrail object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AuditTrail Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to audit_trail
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
		$cls = AuditTrailPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AuditTrailPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AuditTrailPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AuditTrailPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseAuditTrailPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAuditTrailPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AuditTrailTableMap());
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
		return $withPrefix ? AuditTrailPeer::CLASS_DEFAULT : AuditTrailPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a AuditTrail or Criteria object.
	 *
	 * @param      mixed $values Criteria or AuditTrail object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AuditTrail object
		}

		if ($criteria->containsKey(AuditTrailPeer::ID) && $criteria->keyContainsValue(AuditTrailPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.AuditTrailPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a AuditTrail or Criteria object.
	 *
	 * @param      mixed $values Criteria or AuditTrail object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AuditTrailPeer::ID);
			$selectCriteria->add(AuditTrailPeer::ID, $criteria->remove(AuditTrailPeer::ID), $comparison);

		} else { // $values is AuditTrail object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the audit_trail table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(AuditTrailPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AuditTrailPeer::clearInstancePool();
			AuditTrailPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a AuditTrail or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AuditTrail object or primary key or array of primary keys
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
			$con = Propel::getConnection(AuditTrailPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			AuditTrailPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AuditTrail) { // it's a model object
			// invalidate the cache for this single object
			AuditTrailPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AuditTrailPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				AuditTrailPeer::removeInstanceFromPool($singleval);
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
			AuditTrailPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given AuditTrail object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AuditTrail $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(AuditTrail $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AuditTrailPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AuditTrailPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AuditTrailPeer::DATABASE_NAME, AuditTrailPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AuditTrail
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AuditTrailPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(AuditTrailPeer::DATABASE_NAME);
		$criteria->add(AuditTrailPeer::ID, $pk);

		$v = AuditTrailPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(AuditTrailPeer::DATABASE_NAME);
			$criteria->add(AuditTrailPeer::ID, $pks, Criteria::IN);
			$objs = AuditTrailPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAuditTrailPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAuditTrailPeer::buildTableMap();

