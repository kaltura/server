<?php

/**
 * Base static class for performing query and update operations on the 'syndication_feed' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasesyndicationFeedPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'syndication_feed';

	/** the related Propel class for this table */
	const OM_CLASS = 'syndicationFeed';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.syndicationFeed';

	/** the related TableMap class for this table */
	const TM_CLASS = 'syndicationFeedTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 25;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'syndication_feed.ID';

	/** the column name for the INT_ID field */
	const INT_ID = 'syndication_feed.INT_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'syndication_feed.PARTNER_ID';

	/** the column name for the PLAYLIST_ID field */
	const PLAYLIST_ID = 'syndication_feed.PLAYLIST_ID';

	/** the column name for the NAME field */
	const NAME = 'syndication_feed.NAME';

	/** the column name for the STATUS field */
	const STATUS = 'syndication_feed.STATUS';

	/** the column name for the TYPE field */
	const TYPE = 'syndication_feed.TYPE';

	/** the column name for the LANDING_PAGE field */
	const LANDING_PAGE = 'syndication_feed.LANDING_PAGE';

	/** the column name for the FLAVOR_PARAM_ID field */
	const FLAVOR_PARAM_ID = 'syndication_feed.FLAVOR_PARAM_ID';

	/** the column name for the PLAYER_UICONF_ID field */
	const PLAYER_UICONF_ID = 'syndication_feed.PLAYER_UICONF_ID';

	/** the column name for the ALLOW_EMBED field */
	const ALLOW_EMBED = 'syndication_feed.ALLOW_EMBED';

	/** the column name for the ADULT_CONTENT field */
	const ADULT_CONTENT = 'syndication_feed.ADULT_CONTENT';

	/** the column name for the TRANSCODE_EXISTING_CONTENT field */
	const TRANSCODE_EXISTING_CONTENT = 'syndication_feed.TRANSCODE_EXISTING_CONTENT';

	/** the column name for the ADD_TO_DEFAULT_CONVERSION_PROFILE field */
	const ADD_TO_DEFAULT_CONVERSION_PROFILE = 'syndication_feed.ADD_TO_DEFAULT_CONVERSION_PROFILE';

	/** the column name for the CATEGORIES field */
	const CATEGORIES = 'syndication_feed.CATEGORIES';

	/** the column name for the FEED_DESCRIPTION field */
	const FEED_DESCRIPTION = 'syndication_feed.FEED_DESCRIPTION';

	/** the column name for the LANGUAGE field */
	const LANGUAGE = 'syndication_feed.LANGUAGE';

	/** the column name for the FEED_LANDING_PAGE field */
	const FEED_LANDING_PAGE = 'syndication_feed.FEED_LANDING_PAGE';

	/** the column name for the OWNER_NAME field */
	const OWNER_NAME = 'syndication_feed.OWNER_NAME';

	/** the column name for the OWNER_EMAIL field */
	const OWNER_EMAIL = 'syndication_feed.OWNER_EMAIL';

	/** the column name for the FEED_IMAGE_URL field */
	const FEED_IMAGE_URL = 'syndication_feed.FEED_IMAGE_URL';

	/** the column name for the FEED_AUTHOR field */
	const FEED_AUTHOR = 'syndication_feed.FEED_AUTHOR';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'syndication_feed.CREATED_AT';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'syndication_feed.CUSTOM_DATA';

	/** the column name for the DISPLAY_IN_SEARCH field */
	const DISPLAY_IN_SEARCH = 'syndication_feed.DISPLAY_IN_SEARCH';

	/**
	 * An identiy map to hold any loaded instances of syndicationFeed objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array syndicationFeed[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'IntId', 'PartnerId', 'PlaylistId', 'Name', 'Status', 'Type', 'LandingPage', 'FlavorParamId', 'PlayerUiconfId', 'AllowEmbed', 'AdultContent', 'TranscodeExistingContent', 'AddToDefaultConversionProfile', 'Categories', 'FeedDescription', 'Language', 'FeedLandingPage', 'OwnerName', 'OwnerEmail', 'FeedImageUrl', 'FeedAuthor', 'CreatedAt', 'CustomData', 'DisplayInSearch', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'intId', 'partnerId', 'playlistId', 'name', 'status', 'type', 'landingPage', 'flavorParamId', 'playerUiconfId', 'allowEmbed', 'adultContent', 'transcodeExistingContent', 'addToDefaultConversionProfile', 'categories', 'feedDescription', 'language', 'feedLandingPage', 'ownerName', 'ownerEmail', 'feedImageUrl', 'feedAuthor', 'createdAt', 'customData', 'displayInSearch', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::INT_ID, self::PARTNER_ID, self::PLAYLIST_ID, self::NAME, self::STATUS, self::TYPE, self::LANDING_PAGE, self::FLAVOR_PARAM_ID, self::PLAYER_UICONF_ID, self::ALLOW_EMBED, self::ADULT_CONTENT, self::TRANSCODE_EXISTING_CONTENT, self::ADD_TO_DEFAULT_CONVERSION_PROFILE, self::CATEGORIES, self::FEED_DESCRIPTION, self::LANGUAGE, self::FEED_LANDING_PAGE, self::OWNER_NAME, self::OWNER_EMAIL, self::FEED_IMAGE_URL, self::FEED_AUTHOR, self::CREATED_AT, self::CUSTOM_DATA, self::DISPLAY_IN_SEARCH, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'int_id', 'partner_id', 'playlist_id', 'name', 'status', 'type', 'landing_page', 'flavor_param_id', 'player_uiconf_id', 'allow_embed', 'adult_content', 'transcode_existing_content', 'add_to_default_conversion_profile', 'categories', 'feed_description', 'language', 'feed_landing_page', 'owner_name', 'owner_email', 'feed_image_url', 'feed_author', 'created_at', 'custom_data', 'display_in_search', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'IntId' => 1, 'PartnerId' => 2, 'PlaylistId' => 3, 'Name' => 4, 'Status' => 5, 'Type' => 6, 'LandingPage' => 7, 'FlavorParamId' => 8, 'PlayerUiconfId' => 9, 'AllowEmbed' => 10, 'AdultContent' => 11, 'TranscodeExistingContent' => 12, 'AddToDefaultConversionProfile' => 13, 'Categories' => 14, 'FeedDescription' => 15, 'Language' => 16, 'FeedLandingPage' => 17, 'OwnerName' => 18, 'OwnerEmail' => 19, 'FeedImageUrl' => 20, 'FeedAuthor' => 21, 'CreatedAt' => 22, 'CustomData' => 23, 'DisplayInSearch' => 24, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'intId' => 1, 'partnerId' => 2, 'playlistId' => 3, 'name' => 4, 'status' => 5, 'type' => 6, 'landingPage' => 7, 'flavorParamId' => 8, 'playerUiconfId' => 9, 'allowEmbed' => 10, 'adultContent' => 11, 'transcodeExistingContent' => 12, 'addToDefaultConversionProfile' => 13, 'categories' => 14, 'feedDescription' => 15, 'language' => 16, 'feedLandingPage' => 17, 'ownerName' => 18, 'ownerEmail' => 19, 'feedImageUrl' => 20, 'feedAuthor' => 21, 'createdAt' => 22, 'customData' => 23, 'displayInSearch' => 24, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::INT_ID => 1, self::PARTNER_ID => 2, self::PLAYLIST_ID => 3, self::NAME => 4, self::STATUS => 5, self::TYPE => 6, self::LANDING_PAGE => 7, self::FLAVOR_PARAM_ID => 8, self::PLAYER_UICONF_ID => 9, self::ALLOW_EMBED => 10, self::ADULT_CONTENT => 11, self::TRANSCODE_EXISTING_CONTENT => 12, self::ADD_TO_DEFAULT_CONVERSION_PROFILE => 13, self::CATEGORIES => 14, self::FEED_DESCRIPTION => 15, self::LANGUAGE => 16, self::FEED_LANDING_PAGE => 17, self::OWNER_NAME => 18, self::OWNER_EMAIL => 19, self::FEED_IMAGE_URL => 20, self::FEED_AUTHOR => 21, self::CREATED_AT => 22, self::CUSTOM_DATA => 23, self::DISPLAY_IN_SEARCH => 24, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'int_id' => 1, 'partner_id' => 2, 'playlist_id' => 3, 'name' => 4, 'status' => 5, 'type' => 6, 'landing_page' => 7, 'flavor_param_id' => 8, 'player_uiconf_id' => 9, 'allow_embed' => 10, 'adult_content' => 11, 'transcode_existing_content' => 12, 'add_to_default_conversion_profile' => 13, 'categories' => 14, 'feed_description' => 15, 'language' => 16, 'feed_landing_page' => 17, 'owner_name' => 18, 'owner_email' => 19, 'feed_image_url' => 20, 'feed_author' => 21, 'created_at' => 22, 'custom_data' => 23, 'display_in_search' => 24, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
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
	 * @param      string $column The column name for current table. (i.e. syndicationFeedPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(syndicationFeedPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(syndicationFeedPeer::ID);
		$criteria->addSelectColumn(syndicationFeedPeer::INT_ID);
		$criteria->addSelectColumn(syndicationFeedPeer::PARTNER_ID);
		$criteria->addSelectColumn(syndicationFeedPeer::PLAYLIST_ID);
		$criteria->addSelectColumn(syndicationFeedPeer::NAME);
		$criteria->addSelectColumn(syndicationFeedPeer::STATUS);
		$criteria->addSelectColumn(syndicationFeedPeer::TYPE);
		$criteria->addSelectColumn(syndicationFeedPeer::LANDING_PAGE);
		$criteria->addSelectColumn(syndicationFeedPeer::FLAVOR_PARAM_ID);
		$criteria->addSelectColumn(syndicationFeedPeer::PLAYER_UICONF_ID);
		$criteria->addSelectColumn(syndicationFeedPeer::ALLOW_EMBED);
		$criteria->addSelectColumn(syndicationFeedPeer::ADULT_CONTENT);
		$criteria->addSelectColumn(syndicationFeedPeer::TRANSCODE_EXISTING_CONTENT);
		$criteria->addSelectColumn(syndicationFeedPeer::ADD_TO_DEFAULT_CONVERSION_PROFILE);
		$criteria->addSelectColumn(syndicationFeedPeer::CATEGORIES);
		$criteria->addSelectColumn(syndicationFeedPeer::FEED_DESCRIPTION);
		$criteria->addSelectColumn(syndicationFeedPeer::LANGUAGE);
		$criteria->addSelectColumn(syndicationFeedPeer::FEED_LANDING_PAGE);
		$criteria->addSelectColumn(syndicationFeedPeer::OWNER_NAME);
		$criteria->addSelectColumn(syndicationFeedPeer::OWNER_EMAIL);
		$criteria->addSelectColumn(syndicationFeedPeer::FEED_IMAGE_URL);
		$criteria->addSelectColumn(syndicationFeedPeer::FEED_AUTHOR);
		$criteria->addSelectColumn(syndicationFeedPeer::CREATED_AT);
		$criteria->addSelectColumn(syndicationFeedPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(syndicationFeedPeer::DISPLAY_IN_SEARCH);
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
		$criteria->setPrimaryTableName(syndicationFeedPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			syndicationFeedPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = syndicationFeedPeer::doCountStmt($criteria, $con);

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
	 * @return     syndicationFeed
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = syndicationFeedPeer::doSelect($critcopy, $con);
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
		return syndicationFeedPeer::populateObjects(syndicationFeedPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = syndicationFeedPeer::getCriteriaFilter();
		
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
			syndicationFeedPeer::setDefaultCriteriaFilter();
		
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
		syndicationFeedPeer::getCriteriaFilter()->applyFilter($criteria);
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
		syndicationFeedPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = syndicationFeedPeer::alternativeCon ( $con );
		
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
		$con = syndicationFeedPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				syndicationFeedPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			syndicationFeedPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		syndicationFeedPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      syndicationFeed $value A syndicationFeed object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(syndicationFeed $obj, $key = null)
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
	 * @param      mixed $value A syndicationFeed object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof syndicationFeed) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or syndicationFeed object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     syndicationFeed Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to syndication_feed
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
			$key = syndicationFeedPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = syndicationFeedPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = syndicationFeedPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				syndicationFeedPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BasesyndicationFeedPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasesyndicationFeedPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new syndicationFeedTableMap());
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

			$omClass = $row[$colnum + 6];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a syndicationFeed or Criteria object.
	 *
	 * @param      mixed $values Criteria or syndicationFeed object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from syndicationFeed object
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
	 * Method perform an UPDATE on the database, given a syndicationFeed or Criteria object.
	 *
	 * @param      mixed $values Criteria or syndicationFeed object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(syndicationFeedPeer::ID);
			$selectCriteria->add(syndicationFeedPeer::ID, $criteria->remove(syndicationFeedPeer::ID), $comparison);

		} else { // $values is syndicationFeed object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the syndication_feed table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(syndicationFeedPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			syndicationFeedPeer::clearInstancePool();
			syndicationFeedPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a syndicationFeed or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or syndicationFeed object or primary key or array of primary keys
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
			$con = Propel::getConnection(syndicationFeedPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			syndicationFeedPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof syndicationFeed) { // it's a model object
			// invalidate the cache for this single object
			syndicationFeedPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(syndicationFeedPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				syndicationFeedPeer::removeInstanceFromPool($singleval);
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
			syndicationFeedPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given syndicationFeed object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      syndicationFeed $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(syndicationFeed $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(syndicationFeedPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(syndicationFeedPeer::TABLE_NAME);

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

		return BasePeer::doValidate(syndicationFeedPeer::DATABASE_NAME, syndicationFeedPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     syndicationFeed
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = syndicationFeedPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(syndicationFeedPeer::DATABASE_NAME);
		$criteria->add(syndicationFeedPeer::ID, $pk);

		$v = syndicationFeedPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(syndicationFeedPeer::DATABASE_NAME);
			$criteria->add(syndicationFeedPeer::ID, $pks, Criteria::IN);
			$objs = syndicationFeedPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasesyndicationFeedPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasesyndicationFeedPeer::buildTableMap();

