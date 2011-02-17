<?php

/**
 * Base static class for performing query and update operations on the 'search_entry' table.
 *
 * 
 *
 * @package plugins.contentDistribution
 * @subpackage model.om
 */
abstract class BaseSearchEntryPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'search_entry';

	/** the related Propel class for this table */
	const OM_CLASS = 'SearchEntry';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.contentDistribution.SearchEntry';

	/** the related TableMap class for this table */
	const TM_CLASS = 'SearchEntryTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 30;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'search_entry.ENTRY_ID';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'search_entry.KUSER_ID';

	/** the column name for the NAME field */
	const NAME = 'search_entry.NAME';

	/** the column name for the TYPE field */
	const TYPE = 'search_entry.TYPE';

	/** the column name for the MEDIA_TYPE field */
	const MEDIA_TYPE = 'search_entry.MEDIA_TYPE';

	/** the column name for the VIEWS field */
	const VIEWS = 'search_entry.VIEWS';

	/** the column name for the RANK field */
	const RANK = 'search_entry.RANK';

	/** the column name for the TAGS field */
	const TAGS = 'search_entry.TAGS';

	/** the column name for the STATUS field */
	const STATUS = 'search_entry.STATUS';

	/** the column name for the SOURCE_LINK field */
	const SOURCE_LINK = 'search_entry.SOURCE_LINK';

	/** the column name for the DURATION field */
	const DURATION = 'search_entry.DURATION';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'search_entry.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'search_entry.UPDATED_AT';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'search_entry.PARTNER_ID';

	/** the column name for the DISPLAY_IN_SEARCH field */
	const DISPLAY_IN_SEARCH = 'search_entry.DISPLAY_IN_SEARCH';

	/** the column name for the GROUP_ID field */
	const GROUP_ID = 'search_entry.GROUP_ID';

	/** the column name for the PLAYS field */
	const PLAYS = 'search_entry.PLAYS';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'search_entry.DESCRIPTION';

	/** the column name for the MEDIA_DATE field */
	const MEDIA_DATE = 'search_entry.MEDIA_DATE';

	/** the column name for the ADMIN_TAGS field */
	const ADMIN_TAGS = 'search_entry.ADMIN_TAGS';

	/** the column name for the MODERATION_STATUS field */
	const MODERATION_STATUS = 'search_entry.MODERATION_STATUS';

	/** the column name for the MODERATION_COUNT field */
	const MODERATION_COUNT = 'search_entry.MODERATION_COUNT';

	/** the column name for the MODIFIED_AT field */
	const MODIFIED_AT = 'search_entry.MODIFIED_AT';

	/** the column name for the ACCESS_CONTROL_ID field */
	const ACCESS_CONTROL_ID = 'search_entry.ACCESS_CONTROL_ID';

	/** the column name for the CATEGORIES field */
	const CATEGORIES = 'search_entry.CATEGORIES';

	/** the column name for the START_DATE field */
	const START_DATE = 'search_entry.START_DATE';

	/** the column name for the END_DATE field */
	const END_DATE = 'search_entry.END_DATE';

	/** the column name for the FLAVOR_PARAMS field */
	const FLAVOR_PARAMS = 'search_entry.FLAVOR_PARAMS';

	/** the column name for the AVAILABLE_FROM field */
	const AVAILABLE_FROM = 'search_entry.AVAILABLE_FROM';

	/** the column name for the PLUGIN_DATA field */
	const PLUGIN_DATA = 'search_entry.PLUGIN_DATA';

	/**
	 * An identiy map to hold any loaded instances of SearchEntry objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array SearchEntry[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('EntryId', 'KuserId', 'Name', 'Type', 'MediaType', 'Views', 'Rank', 'Tags', 'Status', 'SourceLink', 'Duration', 'CreatedAt', 'UpdatedAt', 'PartnerId', 'DisplayInSearch', 'GroupId', 'Plays', 'Description', 'MediaDate', 'AdminTags', 'ModerationStatus', 'ModerationCount', 'ModifiedAt', 'AccessControlId', 'Categories', 'StartDate', 'EndDate', 'FlavorParams', 'AvailableFrom', 'PluginData', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('entryId', 'kuserId', 'name', 'type', 'mediaType', 'views', 'rank', 'tags', 'status', 'sourceLink', 'duration', 'createdAt', 'updatedAt', 'partnerId', 'displayInSearch', 'groupId', 'plays', 'description', 'mediaDate', 'adminTags', 'moderationStatus', 'moderationCount', 'modifiedAt', 'accessControlId', 'categories', 'startDate', 'endDate', 'flavorParams', 'availableFrom', 'pluginData', ),
		BasePeer::TYPE_COLNAME => array (self::ENTRY_ID, self::KUSER_ID, self::NAME, self::TYPE, self::MEDIA_TYPE, self::VIEWS, self::RANK, self::TAGS, self::STATUS, self::SOURCE_LINK, self::DURATION, self::CREATED_AT, self::UPDATED_AT, self::PARTNER_ID, self::DISPLAY_IN_SEARCH, self::GROUP_ID, self::PLAYS, self::DESCRIPTION, self::MEDIA_DATE, self::ADMIN_TAGS, self::MODERATION_STATUS, self::MODERATION_COUNT, self::MODIFIED_AT, self::ACCESS_CONTROL_ID, self::CATEGORIES, self::START_DATE, self::END_DATE, self::FLAVOR_PARAMS, self::AVAILABLE_FROM, self::PLUGIN_DATA, ),
		BasePeer::TYPE_FIELDNAME => array ('entry_id', 'kuser_id', 'name', 'type', 'media_type', 'views', 'rank', 'tags', 'status', 'source_link', 'duration', 'created_at', 'updated_at', 'partner_id', 'display_in_search', 'group_id', 'plays', 'description', 'media_date', 'admin_tags', 'moderation_status', 'moderation_count', 'modified_at', 'access_control_id', 'categories', 'start_date', 'end_date', 'flavor_params', 'available_from', 'plugin_data', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('EntryId' => 0, 'KuserId' => 1, 'Name' => 2, 'Type' => 3, 'MediaType' => 4, 'Views' => 5, 'Rank' => 6, 'Tags' => 7, 'Status' => 8, 'SourceLink' => 9, 'Duration' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, 'PartnerId' => 13, 'DisplayInSearch' => 14, 'GroupId' => 15, 'Plays' => 16, 'Description' => 17, 'MediaDate' => 18, 'AdminTags' => 19, 'ModerationStatus' => 20, 'ModerationCount' => 21, 'ModifiedAt' => 22, 'AccessControlId' => 23, 'Categories' => 24, 'StartDate' => 25, 'EndDate' => 26, 'FlavorParams' => 27, 'AvailableFrom' => 28, 'PluginData' => 29, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('entryId' => 0, 'kuserId' => 1, 'name' => 2, 'type' => 3, 'mediaType' => 4, 'views' => 5, 'rank' => 6, 'tags' => 7, 'status' => 8, 'sourceLink' => 9, 'duration' => 10, 'createdAt' => 11, 'updatedAt' => 12, 'partnerId' => 13, 'displayInSearch' => 14, 'groupId' => 15, 'plays' => 16, 'description' => 17, 'mediaDate' => 18, 'adminTags' => 19, 'moderationStatus' => 20, 'moderationCount' => 21, 'modifiedAt' => 22, 'accessControlId' => 23, 'categories' => 24, 'startDate' => 25, 'endDate' => 26, 'flavorParams' => 27, 'availableFrom' => 28, 'pluginData' => 29, ),
		BasePeer::TYPE_COLNAME => array (self::ENTRY_ID => 0, self::KUSER_ID => 1, self::NAME => 2, self::TYPE => 3, self::MEDIA_TYPE => 4, self::VIEWS => 5, self::RANK => 6, self::TAGS => 7, self::STATUS => 8, self::SOURCE_LINK => 9, self::DURATION => 10, self::CREATED_AT => 11, self::UPDATED_AT => 12, self::PARTNER_ID => 13, self::DISPLAY_IN_SEARCH => 14, self::GROUP_ID => 15, self::PLAYS => 16, self::DESCRIPTION => 17, self::MEDIA_DATE => 18, self::ADMIN_TAGS => 19, self::MODERATION_STATUS => 20, self::MODERATION_COUNT => 21, self::MODIFIED_AT => 22, self::ACCESS_CONTROL_ID => 23, self::CATEGORIES => 24, self::START_DATE => 25, self::END_DATE => 26, self::FLAVOR_PARAMS => 27, self::AVAILABLE_FROM => 28, self::PLUGIN_DATA => 29, ),
		BasePeer::TYPE_FIELDNAME => array ('entry_id' => 0, 'kuser_id' => 1, 'name' => 2, 'type' => 3, 'media_type' => 4, 'views' => 5, 'rank' => 6, 'tags' => 7, 'status' => 8, 'source_link' => 9, 'duration' => 10, 'created_at' => 11, 'updated_at' => 12, 'partner_id' => 13, 'display_in_search' => 14, 'group_id' => 15, 'plays' => 16, 'description' => 17, 'media_date' => 18, 'admin_tags' => 19, 'moderation_status' => 20, 'moderation_count' => 21, 'modified_at' => 22, 'access_control_id' => 23, 'categories' => 24, 'start_date' => 25, 'end_date' => 26, 'flavor_params' => 27, 'available_from' => 28, 'plugin_data' => 29, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
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
	 * @param      string $column The column name for current table. (i.e. SearchEntryPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(SearchEntryPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(SearchEntryPeer::ENTRY_ID);
		$criteria->addSelectColumn(SearchEntryPeer::KUSER_ID);
		$criteria->addSelectColumn(SearchEntryPeer::NAME);
		$criteria->addSelectColumn(SearchEntryPeer::TYPE);
		$criteria->addSelectColumn(SearchEntryPeer::MEDIA_TYPE);
		$criteria->addSelectColumn(SearchEntryPeer::VIEWS);
		$criteria->addSelectColumn(SearchEntryPeer::RANK);
		$criteria->addSelectColumn(SearchEntryPeer::TAGS);
		$criteria->addSelectColumn(SearchEntryPeer::STATUS);
		$criteria->addSelectColumn(SearchEntryPeer::SOURCE_LINK);
		$criteria->addSelectColumn(SearchEntryPeer::DURATION);
		$criteria->addSelectColumn(SearchEntryPeer::CREATED_AT);
		$criteria->addSelectColumn(SearchEntryPeer::UPDATED_AT);
		$criteria->addSelectColumn(SearchEntryPeer::PARTNER_ID);
		$criteria->addSelectColumn(SearchEntryPeer::DISPLAY_IN_SEARCH);
		$criteria->addSelectColumn(SearchEntryPeer::GROUP_ID);
		$criteria->addSelectColumn(SearchEntryPeer::PLAYS);
		$criteria->addSelectColumn(SearchEntryPeer::DESCRIPTION);
		$criteria->addSelectColumn(SearchEntryPeer::MEDIA_DATE);
		$criteria->addSelectColumn(SearchEntryPeer::ADMIN_TAGS);
		$criteria->addSelectColumn(SearchEntryPeer::MODERATION_STATUS);
		$criteria->addSelectColumn(SearchEntryPeer::MODERATION_COUNT);
		$criteria->addSelectColumn(SearchEntryPeer::MODIFIED_AT);
		$criteria->addSelectColumn(SearchEntryPeer::ACCESS_CONTROL_ID);
		$criteria->addSelectColumn(SearchEntryPeer::CATEGORIES);
		$criteria->addSelectColumn(SearchEntryPeer::START_DATE);
		$criteria->addSelectColumn(SearchEntryPeer::END_DATE);
		$criteria->addSelectColumn(SearchEntryPeer::FLAVOR_PARAMS);
		$criteria->addSelectColumn(SearchEntryPeer::AVAILABLE_FROM);
		$criteria->addSelectColumn(SearchEntryPeer::PLUGIN_DATA);
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
		$criteria->setPrimaryTableName(SearchEntryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			SearchEntryPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = SearchEntryPeer::doCountStmt($criteria, $con);

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
	 * @return     SearchEntry
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = SearchEntryPeer::doSelect($critcopy, $con);
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
		return SearchEntryPeer::populateObjects(SearchEntryPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = SearchEntryPeer::getCriteriaFilter();
		
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
			SearchEntryPeer::setDefaultCriteriaFilter();
		
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
		SearchEntryPeer::getCriteriaFilter()->applyFilter($criteria);
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
				
				if($kalturaNetwork)
				{
					$criterionNetwork = $criteria->getNewCriterion(self::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
					$criterion->addOr($criterionNetwork);
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
		SearchEntryPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = SearchEntryPeer::alternativeCon ( $con );
		
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
		$con = SearchEntryPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				SearchEntryPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			SearchEntryPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		SearchEntryPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      SearchEntry $value A SearchEntry object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(SearchEntry $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getEntryId();
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
	 * @param      mixed $value A SearchEntry object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof SearchEntry) {
				$key = (string) $value->getEntryId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or SearchEntry object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     SearchEntry Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to search_entry
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
			$key = SearchEntryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = SearchEntryPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = SearchEntryPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				SearchEntryPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseSearchEntryPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseSearchEntryPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new SearchEntryTableMap());
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

			$omClass = $row[$colnum + 3];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a SearchEntry or Criteria object.
	 *
	 * @param      mixed $values Criteria or SearchEntry object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from SearchEntry object
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
	 * Method perform an UPDATE on the database, given a SearchEntry or Criteria object.
	 *
	 * @param      mixed $values Criteria or SearchEntry object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(SearchEntryPeer::ENTRY_ID);
			$selectCriteria->add(SearchEntryPeer::ENTRY_ID, $criteria->remove(SearchEntryPeer::ENTRY_ID), $comparison);

		} else { // $values is SearchEntry object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the search_entry table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(SearchEntryPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			SearchEntryPeer::clearInstancePool();
			SearchEntryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a SearchEntry or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or SearchEntry object or primary key or array of primary keys
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
			$con = Propel::getConnection(SearchEntryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			SearchEntryPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof SearchEntry) { // it's a model object
			// invalidate the cache for this single object
			SearchEntryPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(SearchEntryPeer::ENTRY_ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				SearchEntryPeer::removeInstanceFromPool($singleval);
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
			SearchEntryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given SearchEntry object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      SearchEntry $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(SearchEntry $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(SearchEntryPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(SearchEntryPeer::TABLE_NAME);

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

		return BasePeer::doValidate(SearchEntryPeer::DATABASE_NAME, SearchEntryPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     SearchEntry
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = SearchEntryPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(SearchEntryPeer::DATABASE_NAME);
		$criteria->add(SearchEntryPeer::ENTRY_ID, $pk);

		$v = SearchEntryPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(SearchEntryPeer::DATABASE_NAME);
			$criteria->add(SearchEntryPeer::ENTRY_ID, $pks, Criteria::IN);
			$objs = SearchEntryPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseSearchEntryPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseSearchEntryPeer::buildTableMap();

