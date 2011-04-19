<?php

/**
 * Base static class for performing query and update operations on the 'distribution_profile' table.
 *
 * 
 *
 * @package plugins.contentDistribution
 * @subpackage model.om
 */
abstract class BaseDistributionProfilePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'distribution_profile';

	/** the related Propel class for this table */
	const OM_CLASS = 'DistributionProfile';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.contentDistribution.DistributionProfile';

	/** the related TableMap class for this table */
	const TM_CLASS = 'DistributionProfileTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 20;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'distribution_profile.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'distribution_profile.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'distribution_profile.UPDATED_AT';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'distribution_profile.PARTNER_ID';

	/** the column name for the PROVIDER_TYPE field */
	const PROVIDER_TYPE = 'distribution_profile.PROVIDER_TYPE';

	/** the column name for the NAME field */
	const NAME = 'distribution_profile.NAME';

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'distribution_profile.SYSTEM_NAME';

	/** the column name for the STATUS field */
	const STATUS = 'distribution_profile.STATUS';

	/** the column name for the SUBMIT_ENABLED field */
	const SUBMIT_ENABLED = 'distribution_profile.SUBMIT_ENABLED';

	/** the column name for the UPDATE_ENABLED field */
	const UPDATE_ENABLED = 'distribution_profile.UPDATE_ENABLED';

	/** the column name for the DELETE_ENABLED field */
	const DELETE_ENABLED = 'distribution_profile.DELETE_ENABLED';

	/** the column name for the REPORT_ENABLED field */
	const REPORT_ENABLED = 'distribution_profile.REPORT_ENABLED';

	/** the column name for the AUTO_CREATE_FLAVORS field */
	const AUTO_CREATE_FLAVORS = 'distribution_profile.AUTO_CREATE_FLAVORS';

	/** the column name for the AUTO_CREATE_THUMB field */
	const AUTO_CREATE_THUMB = 'distribution_profile.AUTO_CREATE_THUMB';

	/** the column name for the OPTIONAL_FLAVOR_PARAMS_IDS field */
	const OPTIONAL_FLAVOR_PARAMS_IDS = 'distribution_profile.OPTIONAL_FLAVOR_PARAMS_IDS';

	/** the column name for the REQUIRED_FLAVOR_PARAMS_IDS field */
	const REQUIRED_FLAVOR_PARAMS_IDS = 'distribution_profile.REQUIRED_FLAVOR_PARAMS_IDS';

	/** the column name for the OPTIONAL_THUMB_DIMENSIONS field */
	const OPTIONAL_THUMB_DIMENSIONS = 'distribution_profile.OPTIONAL_THUMB_DIMENSIONS';

	/** the column name for the REQUIRED_THUMB_DIMENSIONS field */
	const REQUIRED_THUMB_DIMENSIONS = 'distribution_profile.REQUIRED_THUMB_DIMENSIONS';

	/** the column name for the REPORT_INTERVAL field */
	const REPORT_INTERVAL = 'distribution_profile.REPORT_INTERVAL';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'distribution_profile.CUSTOM_DATA';

	/**
	 * An identiy map to hold any loaded instances of DistributionProfile objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array DistributionProfile[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'PartnerId', 'ProviderType', 'Name', 'SystemName', 'Status', 'SubmitEnabled', 'UpdateEnabled', 'DeleteEnabled', 'ReportEnabled', 'AutoCreateFlavors', 'AutoCreateThumb', 'OptionalFlavorParamsIds', 'RequiredFlavorParamsIds', 'OptionalThumbDimensions', 'RequiredThumbDimensions', 'ReportInterval', 'CustomData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'partnerId', 'providerType', 'name', 'systemName', 'status', 'submitEnabled', 'updateEnabled', 'deleteEnabled', 'reportEnabled', 'autoCreateFlavors', 'autoCreateThumb', 'optionalFlavorParamsIds', 'requiredFlavorParamsIds', 'optionalThumbDimensions', 'requiredThumbDimensions', 'reportInterval', 'customData', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::PARTNER_ID, self::PROVIDER_TYPE, self::NAME, self::SYSTEM_NAME, self::STATUS, self::SUBMIT_ENABLED, self::UPDATE_ENABLED, self::DELETE_ENABLED, self::REPORT_ENABLED, self::AUTO_CREATE_FLAVORS, self::AUTO_CREATE_THUMB, self::OPTIONAL_FLAVOR_PARAMS_IDS, self::REQUIRED_FLAVOR_PARAMS_IDS, self::OPTIONAL_THUMB_DIMENSIONS, self::REQUIRED_THUMB_DIMENSIONS, self::REPORT_INTERVAL, self::CUSTOM_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'partner_id', 'provider_type', 'name', 'system_name', 'status', 'submit_enabled', 'update_enabled', 'delete_enabled', 'report_enabled', 'auto_create_flavors', 'auto_create_thumb', 'optional_flavor_params_ids', 'required_flavor_params_ids', 'optional_thumb_dimensions', 'required_thumb_dimensions', 'report_interval', 'custom_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'PartnerId' => 3, 'ProviderType' => 4, 'Name' => 5, 'SystemName' => 6, 'Status' => 7, 'SubmitEnabled' => 8, 'UpdateEnabled' => 9, 'DeleteEnabled' => 10, 'ReportEnabled' => 11, 'AutoCreateFlavors' => 12, 'AutoCreateThumb' => 13, 'OptionalFlavorParamsIds' => 14, 'RequiredFlavorParamsIds' => 15, 'OptionalThumbDimensions' => 16, 'RequiredThumbDimensions' => 17, 'ReportInterval' => 18, 'CustomData' => 19, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'partnerId' => 3, 'providerType' => 4, 'name' => 5, 'systemName' => 6, 'status' => 7, 'submitEnabled' => 8, 'updateEnabled' => 9, 'deleteEnabled' => 10, 'reportEnabled' => 11, 'autoCreateFlavors' => 12, 'autoCreateThumb' => 13, 'optionalFlavorParamsIds' => 14, 'requiredFlavorParamsIds' => 15, 'optionalThumbDimensions' => 16, 'requiredThumbDimensions' => 17, 'reportInterval' => 18, 'customData' => 19, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::PARTNER_ID => 3, self::PROVIDER_TYPE => 4, self::NAME => 5, self::SYSTEM_NAME => 6, self::STATUS => 7, self::SUBMIT_ENABLED => 8, self::UPDATE_ENABLED => 9, self::DELETE_ENABLED => 10, self::REPORT_ENABLED => 11, self::AUTO_CREATE_FLAVORS => 12, self::AUTO_CREATE_THUMB => 13, self::OPTIONAL_FLAVOR_PARAMS_IDS => 14, self::REQUIRED_FLAVOR_PARAMS_IDS => 15, self::OPTIONAL_THUMB_DIMENSIONS => 16, self::REQUIRED_THUMB_DIMENSIONS => 17, self::REPORT_INTERVAL => 18, self::CUSTOM_DATA => 19, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'partner_id' => 3, 'provider_type' => 4, 'name' => 5, 'system_name' => 6, 'status' => 7, 'submit_enabled' => 8, 'update_enabled' => 9, 'delete_enabled' => 10, 'report_enabled' => 11, 'auto_create_flavors' => 12, 'auto_create_thumb' => 13, 'optional_flavor_params_ids' => 14, 'required_flavor_params_ids' => 15, 'optional_thumb_dimensions' => 16, 'required_thumb_dimensions' => 17, 'report_interval' => 18, 'custom_data' => 19, ),
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
	 * @param      string $column The column name for current table. (i.e. DistributionProfilePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(DistributionProfilePeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(DistributionProfilePeer::ID);
		$criteria->addSelectColumn(DistributionProfilePeer::CREATED_AT);
		$criteria->addSelectColumn(DistributionProfilePeer::UPDATED_AT);
		$criteria->addSelectColumn(DistributionProfilePeer::PARTNER_ID);
		$criteria->addSelectColumn(DistributionProfilePeer::PROVIDER_TYPE);
		$criteria->addSelectColumn(DistributionProfilePeer::NAME);
		$criteria->addSelectColumn(DistributionProfilePeer::SYSTEM_NAME);
		$criteria->addSelectColumn(DistributionProfilePeer::STATUS);
		$criteria->addSelectColumn(DistributionProfilePeer::SUBMIT_ENABLED);
		$criteria->addSelectColumn(DistributionProfilePeer::UPDATE_ENABLED);
		$criteria->addSelectColumn(DistributionProfilePeer::DELETE_ENABLED);
		$criteria->addSelectColumn(DistributionProfilePeer::REPORT_ENABLED);
		$criteria->addSelectColumn(DistributionProfilePeer::AUTO_CREATE_FLAVORS);
		$criteria->addSelectColumn(DistributionProfilePeer::AUTO_CREATE_THUMB);
		$criteria->addSelectColumn(DistributionProfilePeer::OPTIONAL_FLAVOR_PARAMS_IDS);
		$criteria->addSelectColumn(DistributionProfilePeer::REQUIRED_FLAVOR_PARAMS_IDS);
		$criteria->addSelectColumn(DistributionProfilePeer::OPTIONAL_THUMB_DIMENSIONS);
		$criteria->addSelectColumn(DistributionProfilePeer::REQUIRED_THUMB_DIMENSIONS);
		$criteria->addSelectColumn(DistributionProfilePeer::REPORT_INTERVAL);
		$criteria->addSelectColumn(DistributionProfilePeer::CUSTOM_DATA);
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
		$criteria->setPrimaryTableName(DistributionProfilePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			DistributionProfilePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = DistributionProfilePeer::doCountStmt($criteria, $con);

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
	 * @return     DistributionProfile
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = DistributionProfilePeer::doSelect($critcopy, $con);
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
		return DistributionProfilePeer::populateObjects(DistributionProfilePeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(DistributionProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = DistributionProfilePeer::getCriteriaFilter();
		
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
			DistributionProfilePeer::setDefaultCriteriaFilter();
		
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
		DistributionProfilePeer::getCriteriaFilter()->applyFilter($criteria);
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
		DistributionProfilePeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = DistributionProfilePeer::alternativeCon ( $con );
		
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
		$con = DistributionProfilePeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				DistributionProfilePeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			DistributionProfilePeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		DistributionProfilePeer::attachCriteriaFilter($criteria);
		
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
	 * @param      DistributionProfile $value A DistributionProfile object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(DistributionProfile $obj, $key = null)
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
	 * @param      mixed $value A DistributionProfile object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof DistributionProfile) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or DistributionProfile object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     DistributionProfile Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to distribution_profile
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
			$key = DistributionProfilePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = DistributionProfilePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = DistributionProfilePeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				DistributionProfilePeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseDistributionProfilePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseDistributionProfilePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new DistributionProfileTableMap());
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

			$omClass = $row[$colnum + 4];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a DistributionProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DistributionProfile object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DistributionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from DistributionProfile object
		}

		if ($criteria->containsKey(DistributionProfilePeer::ID) && $criteria->keyContainsValue(DistributionProfilePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.DistributionProfilePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a DistributionProfile or Criteria object.
	 *
	 * @param      mixed $values Criteria or DistributionProfile object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DistributionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(DistributionProfilePeer::ID);
			$selectCriteria->add(DistributionProfilePeer::ID, $criteria->remove(DistributionProfilePeer::ID), $comparison);

		} else { // $values is DistributionProfile object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the distribution_profile table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(DistributionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(DistributionProfilePeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			DistributionProfilePeer::clearInstancePool();
			DistributionProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a DistributionProfile or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or DistributionProfile object or primary key or array of primary keys
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
			$con = Propel::getConnection(DistributionProfilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			DistributionProfilePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof DistributionProfile) { // it's a model object
			// invalidate the cache for this single object
			DistributionProfilePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(DistributionProfilePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				DistributionProfilePeer::removeInstanceFromPool($singleval);
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
			DistributionProfilePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given DistributionProfile object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      DistributionProfile $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(DistributionProfile $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(DistributionProfilePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(DistributionProfilePeer::TABLE_NAME);

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

		return BasePeer::doValidate(DistributionProfilePeer::DATABASE_NAME, DistributionProfilePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     DistributionProfile
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = DistributionProfilePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(DistributionProfilePeer::DATABASE_NAME);
		$criteria->add(DistributionProfilePeer::ID, $pk);

		$v = DistributionProfilePeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(DistributionProfilePeer::DATABASE_NAME);
			$criteria->add(DistributionProfilePeer::ID, $pks, Criteria::IN);
			$objs = DistributionProfilePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseDistributionProfilePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseDistributionProfilePeer::buildTableMap();

