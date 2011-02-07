<?php

/**
 * Base static class for performing query and update operations on the 'entry' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseentryPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'entry';

	/** the related Propel class for this table */
	const OM_CLASS = 'entry';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.entry';

	/** the related TableMap class for this table */
	const TM_CLASS = 'entryTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 54;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'entry.ID';

	/** the column name for the KSHOW_ID field */
	const KSHOW_ID = 'entry.KSHOW_ID';

	/** the column name for the KUSER_ID field */
	const KUSER_ID = 'entry.KUSER_ID';

	/** the column name for the NAME field */
	const NAME = 'entry.NAME';

	/** the column name for the TYPE field */
	const TYPE = 'entry.TYPE';

	/** the column name for the MEDIA_TYPE field */
	const MEDIA_TYPE = 'entry.MEDIA_TYPE';

	/** the column name for the DATA field */
	const DATA = 'entry.DATA';

	/** the column name for the THUMBNAIL field */
	const THUMBNAIL = 'entry.THUMBNAIL';

	/** the column name for the VIEWS field */
	const VIEWS = 'entry.VIEWS';

	/** the column name for the VOTES field */
	const VOTES = 'entry.VOTES';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'entry.COMMENTS';

	/** the column name for the FAVORITES field */
	const FAVORITES = 'entry.FAVORITES';

	/** the column name for the TOTAL_RANK field */
	const TOTAL_RANK = 'entry.TOTAL_RANK';

	/** the column name for the RANK field */
	const RANK = 'entry.RANK';

	/** the column name for the TAGS field */
	const TAGS = 'entry.TAGS';

	/** the column name for the ANONYMOUS field */
	const ANONYMOUS = 'entry.ANONYMOUS';

	/** the column name for the STATUS field */
	const STATUS = 'entry.STATUS';

	/** the column name for the SOURCE field */
	const SOURCE = 'entry.SOURCE';

	/** the column name for the SOURCE_ID field */
	const SOURCE_ID = 'entry.SOURCE_ID';

	/** the column name for the SOURCE_LINK field */
	const SOURCE_LINK = 'entry.SOURCE_LINK';

	/** the column name for the LICENSE_TYPE field */
	const LICENSE_TYPE = 'entry.LICENSE_TYPE';

	/** the column name for the CREDIT field */
	const CREDIT = 'entry.CREDIT';

	/** the column name for the LENGTH_IN_MSECS field */
	const LENGTH_IN_MSECS = 'entry.LENGTH_IN_MSECS';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'entry.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'entry.UPDATED_AT';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'entry.PARTNER_ID';

	/** the column name for the DISPLAY_IN_SEARCH field */
	const DISPLAY_IN_SEARCH = 'entry.DISPLAY_IN_SEARCH';

	/** the column name for the SUBP_ID field */
	const SUBP_ID = 'entry.SUBP_ID';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'entry.CUSTOM_DATA';

	/** the column name for the SEARCH_TEXT field */
	const SEARCH_TEXT = 'entry.SEARCH_TEXT';

	/** the column name for the SCREEN_NAME field */
	const SCREEN_NAME = 'entry.SCREEN_NAME';

	/** the column name for the SITE_URL field */
	const SITE_URL = 'entry.SITE_URL';

	/** the column name for the PERMISSIONS field */
	const PERMISSIONS = 'entry.PERMISSIONS';

	/** the column name for the GROUP_ID field */
	const GROUP_ID = 'entry.GROUP_ID';

	/** the column name for the PLAYS field */
	const PLAYS = 'entry.PLAYS';

	/** the column name for the PARTNER_DATA field */
	const PARTNER_DATA = 'entry.PARTNER_DATA';

	/** the column name for the INT_ID field */
	const INT_ID = 'entry.INT_ID';

	/** the column name for the INDEXED_CUSTOM_DATA_1 field */
	const INDEXED_CUSTOM_DATA_1 = 'entry.INDEXED_CUSTOM_DATA_1';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'entry.DESCRIPTION';

	/** the column name for the MEDIA_DATE field */
	const MEDIA_DATE = 'entry.MEDIA_DATE';

	/** the column name for the ADMIN_TAGS field */
	const ADMIN_TAGS = 'entry.ADMIN_TAGS';

	/** the column name for the MODERATION_STATUS field */
	const MODERATION_STATUS = 'entry.MODERATION_STATUS';

	/** the column name for the MODERATION_COUNT field */
	const MODERATION_COUNT = 'entry.MODERATION_COUNT';

	/** the column name for the MODIFIED_AT field */
	const MODIFIED_AT = 'entry.MODIFIED_AT';

	/** the column name for the PUSER_ID field */
	const PUSER_ID = 'entry.PUSER_ID';

	/** the column name for the ACCESS_CONTROL_ID field */
	const ACCESS_CONTROL_ID = 'entry.ACCESS_CONTROL_ID';

	/** the column name for the CONVERSION_PROFILE_ID field */
	const CONVERSION_PROFILE_ID = 'entry.CONVERSION_PROFILE_ID';

	/** the column name for the CATEGORIES field */
	const CATEGORIES = 'entry.CATEGORIES';

	/** the column name for the CATEGORIES_IDS field */
	const CATEGORIES_IDS = 'entry.CATEGORIES_IDS';

	/** the column name for the START_DATE field */
	const START_DATE = 'entry.START_DATE';

	/** the column name for the END_DATE field */
	const END_DATE = 'entry.END_DATE';

	/** the column name for the SEARCH_TEXT_DISCRETE field */
	const SEARCH_TEXT_DISCRETE = 'entry.SEARCH_TEXT_DISCRETE';

	/** the column name for the FLAVOR_PARAMS_IDS field */
	const FLAVOR_PARAMS_IDS = 'entry.FLAVOR_PARAMS_IDS';

	/** the column name for the AVAILABLE_FROM field */
	const AVAILABLE_FROM = 'entry.AVAILABLE_FROM';

	/**
	 * An identiy map to hold any loaded instances of entry objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array entry[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'KshowId', 'KuserId', 'Name', 'Type', 'MediaType', 'Data', 'Thumbnail', 'Views', 'Votes', 'Comments', 'Favorites', 'TotalRank', 'Rank', 'Tags', 'Anonymous', 'Status', 'Source', 'SourceId', 'SourceLink', 'LicenseType', 'Credit', 'LengthInMsecs', 'CreatedAt', 'UpdatedAt', 'PartnerId', 'DisplayInSearch', 'SubpId', 'CustomData', 'SearchText', 'ScreenName', 'SiteUrl', 'Permissions', 'GroupId', 'Plays', 'PartnerData', 'IntId', 'IndexedCustomData1', 'Description', 'MediaDate', 'AdminTags', 'ModerationStatus', 'ModerationCount', 'ModifiedAt', 'PuserId', 'AccessControlId', 'ConversionProfileId', 'Categories', 'CategoriesIds', 'StartDate', 'EndDate', 'SearchTextDiscrete', 'FlavorParamsIds', 'AvailableFrom', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'kshowId', 'kuserId', 'name', 'type', 'mediaType', 'data', 'thumbnail', 'views', 'votes', 'comments', 'favorites', 'totalRank', 'rank', 'tags', 'anonymous', 'status', 'source', 'sourceId', 'sourceLink', 'licenseType', 'credit', 'lengthInMsecs', 'createdAt', 'updatedAt', 'partnerId', 'displayInSearch', 'subpId', 'customData', 'searchText', 'screenName', 'siteUrl', 'permissions', 'groupId', 'plays', 'partnerData', 'intId', 'indexedCustomData1', 'description', 'mediaDate', 'adminTags', 'moderationStatus', 'moderationCount', 'modifiedAt', 'puserId', 'accessControlId', 'conversionProfileId', 'categories', 'categoriesIds', 'startDate', 'endDate', 'searchTextDiscrete', 'flavorParamsIds', 'availableFrom', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::KSHOW_ID, self::KUSER_ID, self::NAME, self::TYPE, self::MEDIA_TYPE, self::DATA, self::THUMBNAIL, self::VIEWS, self::VOTES, self::COMMENTS, self::FAVORITES, self::TOTAL_RANK, self::RANK, self::TAGS, self::ANONYMOUS, self::STATUS, self::SOURCE, self::SOURCE_ID, self::SOURCE_LINK, self::LICENSE_TYPE, self::CREDIT, self::LENGTH_IN_MSECS, self::CREATED_AT, self::UPDATED_AT, self::PARTNER_ID, self::DISPLAY_IN_SEARCH, self::SUBP_ID, self::CUSTOM_DATA, self::SEARCH_TEXT, self::SCREEN_NAME, self::SITE_URL, self::PERMISSIONS, self::GROUP_ID, self::PLAYS, self::PARTNER_DATA, self::INT_ID, self::INDEXED_CUSTOM_DATA_1, self::DESCRIPTION, self::MEDIA_DATE, self::ADMIN_TAGS, self::MODERATION_STATUS, self::MODERATION_COUNT, self::MODIFIED_AT, self::PUSER_ID, self::ACCESS_CONTROL_ID, self::CONVERSION_PROFILE_ID, self::CATEGORIES, self::CATEGORIES_IDS, self::START_DATE, self::END_DATE, self::SEARCH_TEXT_DISCRETE, self::FLAVOR_PARAMS_IDS, self::AVAILABLE_FROM, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'kshow_id', 'kuser_id', 'name', 'type', 'media_type', 'data', 'thumbnail', 'views', 'votes', 'comments', 'favorites', 'total_rank', 'rank', 'tags', 'anonymous', 'status', 'source', 'source_id', 'source_link', 'license_type', 'credit', 'length_in_msecs', 'created_at', 'updated_at', 'partner_id', 'display_in_search', 'subp_id', 'custom_data', 'search_text', 'screen_name', 'site_url', 'permissions', 'group_id', 'plays', 'partner_data', 'int_id', 'indexed_custom_data_1', 'description', 'media_date', 'admin_tags', 'moderation_status', 'moderation_count', 'modified_at', 'puser_id', 'access_control_id', 'conversion_profile_id', 'categories', 'categories_ids', 'start_date', 'end_date', 'search_text_discrete', 'flavor_params_ids', 'available_from', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'KshowId' => 1, 'KuserId' => 2, 'Name' => 3, 'Type' => 4, 'MediaType' => 5, 'Data' => 6, 'Thumbnail' => 7, 'Views' => 8, 'Votes' => 9, 'Comments' => 10, 'Favorites' => 11, 'TotalRank' => 12, 'Rank' => 13, 'Tags' => 14, 'Anonymous' => 15, 'Status' => 16, 'Source' => 17, 'SourceId' => 18, 'SourceLink' => 19, 'LicenseType' => 20, 'Credit' => 21, 'LengthInMsecs' => 22, 'CreatedAt' => 23, 'UpdatedAt' => 24, 'PartnerId' => 25, 'DisplayInSearch' => 26, 'SubpId' => 27, 'CustomData' => 28, 'SearchText' => 29, 'ScreenName' => 30, 'SiteUrl' => 31, 'Permissions' => 32, 'GroupId' => 33, 'Plays' => 34, 'PartnerData' => 35, 'IntId' => 36, 'IndexedCustomData1' => 37, 'Description' => 38, 'MediaDate' => 39, 'AdminTags' => 40, 'ModerationStatus' => 41, 'ModerationCount' => 42, 'ModifiedAt' => 43, 'PuserId' => 44, 'AccessControlId' => 45, 'ConversionProfileId' => 46, 'Categories' => 47, 'CategoriesIds' => 48, 'StartDate' => 49, 'EndDate' => 50, 'SearchTextDiscrete' => 51, 'FlavorParamsIds' => 52, 'AvailableFrom' => 53, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'kshowId' => 1, 'kuserId' => 2, 'name' => 3, 'type' => 4, 'mediaType' => 5, 'data' => 6, 'thumbnail' => 7, 'views' => 8, 'votes' => 9, 'comments' => 10, 'favorites' => 11, 'totalRank' => 12, 'rank' => 13, 'tags' => 14, 'anonymous' => 15, 'status' => 16, 'source' => 17, 'sourceId' => 18, 'sourceLink' => 19, 'licenseType' => 20, 'credit' => 21, 'lengthInMsecs' => 22, 'createdAt' => 23, 'updatedAt' => 24, 'partnerId' => 25, 'displayInSearch' => 26, 'subpId' => 27, 'customData' => 28, 'searchText' => 29, 'screenName' => 30, 'siteUrl' => 31, 'permissions' => 32, 'groupId' => 33, 'plays' => 34, 'partnerData' => 35, 'intId' => 36, 'indexedCustomData1' => 37, 'description' => 38, 'mediaDate' => 39, 'adminTags' => 40, 'moderationStatus' => 41, 'moderationCount' => 42, 'modifiedAt' => 43, 'puserId' => 44, 'accessControlId' => 45, 'conversionProfileId' => 46, 'categories' => 47, 'categoriesIds' => 48, 'startDate' => 49, 'endDate' => 50, 'searchTextDiscrete' => 51, 'flavorParamsIds' => 52, 'availableFrom' => 53, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::KSHOW_ID => 1, self::KUSER_ID => 2, self::NAME => 3, self::TYPE => 4, self::MEDIA_TYPE => 5, self::DATA => 6, self::THUMBNAIL => 7, self::VIEWS => 8, self::VOTES => 9, self::COMMENTS => 10, self::FAVORITES => 11, self::TOTAL_RANK => 12, self::RANK => 13, self::TAGS => 14, self::ANONYMOUS => 15, self::STATUS => 16, self::SOURCE => 17, self::SOURCE_ID => 18, self::SOURCE_LINK => 19, self::LICENSE_TYPE => 20, self::CREDIT => 21, self::LENGTH_IN_MSECS => 22, self::CREATED_AT => 23, self::UPDATED_AT => 24, self::PARTNER_ID => 25, self::DISPLAY_IN_SEARCH => 26, self::SUBP_ID => 27, self::CUSTOM_DATA => 28, self::SEARCH_TEXT => 29, self::SCREEN_NAME => 30, self::SITE_URL => 31, self::PERMISSIONS => 32, self::GROUP_ID => 33, self::PLAYS => 34, self::PARTNER_DATA => 35, self::INT_ID => 36, self::INDEXED_CUSTOM_DATA_1 => 37, self::DESCRIPTION => 38, self::MEDIA_DATE => 39, self::ADMIN_TAGS => 40, self::MODERATION_STATUS => 41, self::MODERATION_COUNT => 42, self::MODIFIED_AT => 43, self::PUSER_ID => 44, self::ACCESS_CONTROL_ID => 45, self::CONVERSION_PROFILE_ID => 46, self::CATEGORIES => 47, self::CATEGORIES_IDS => 48, self::START_DATE => 49, self::END_DATE => 50, self::SEARCH_TEXT_DISCRETE => 51, self::FLAVOR_PARAMS_IDS => 52, self::AVAILABLE_FROM => 53, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'kshow_id' => 1, 'kuser_id' => 2, 'name' => 3, 'type' => 4, 'media_type' => 5, 'data' => 6, 'thumbnail' => 7, 'views' => 8, 'votes' => 9, 'comments' => 10, 'favorites' => 11, 'total_rank' => 12, 'rank' => 13, 'tags' => 14, 'anonymous' => 15, 'status' => 16, 'source' => 17, 'source_id' => 18, 'source_link' => 19, 'license_type' => 20, 'credit' => 21, 'length_in_msecs' => 22, 'created_at' => 23, 'updated_at' => 24, 'partner_id' => 25, 'display_in_search' => 26, 'subp_id' => 27, 'custom_data' => 28, 'search_text' => 29, 'screen_name' => 30, 'site_url' => 31, 'permissions' => 32, 'group_id' => 33, 'plays' => 34, 'partner_data' => 35, 'int_id' => 36, 'indexed_custom_data_1' => 37, 'description' => 38, 'media_date' => 39, 'admin_tags' => 40, 'moderation_status' => 41, 'moderation_count' => 42, 'modified_at' => 43, 'puser_id' => 44, 'access_control_id' => 45, 'conversion_profile_id' => 46, 'categories' => 47, 'categories_ids' => 48, 'start_date' => 49, 'end_date' => 50, 'search_text_discrete' => 51, 'flavor_params_ids' => 52, 'available_from' => 53, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, )
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
	 * @param      string $column The column name for current table. (i.e. entryPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(entryPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(entryPeer::ID);
		$criteria->addSelectColumn(entryPeer::KSHOW_ID);
		$criteria->addSelectColumn(entryPeer::KUSER_ID);
		$criteria->addSelectColumn(entryPeer::NAME);
		$criteria->addSelectColumn(entryPeer::TYPE);
		$criteria->addSelectColumn(entryPeer::MEDIA_TYPE);
		$criteria->addSelectColumn(entryPeer::DATA);
		$criteria->addSelectColumn(entryPeer::THUMBNAIL);
		$criteria->addSelectColumn(entryPeer::VIEWS);
		$criteria->addSelectColumn(entryPeer::VOTES);
		$criteria->addSelectColumn(entryPeer::COMMENTS);
		$criteria->addSelectColumn(entryPeer::FAVORITES);
		$criteria->addSelectColumn(entryPeer::TOTAL_RANK);
		$criteria->addSelectColumn(entryPeer::RANK);
		$criteria->addSelectColumn(entryPeer::TAGS);
		$criteria->addSelectColumn(entryPeer::ANONYMOUS);
		$criteria->addSelectColumn(entryPeer::STATUS);
		$criteria->addSelectColumn(entryPeer::SOURCE);
		$criteria->addSelectColumn(entryPeer::SOURCE_ID);
		$criteria->addSelectColumn(entryPeer::SOURCE_LINK);
		$criteria->addSelectColumn(entryPeer::LICENSE_TYPE);
		$criteria->addSelectColumn(entryPeer::CREDIT);
		$criteria->addSelectColumn(entryPeer::LENGTH_IN_MSECS);
		$criteria->addSelectColumn(entryPeer::CREATED_AT);
		$criteria->addSelectColumn(entryPeer::UPDATED_AT);
		$criteria->addSelectColumn(entryPeer::PARTNER_ID);
		$criteria->addSelectColumn(entryPeer::DISPLAY_IN_SEARCH);
		$criteria->addSelectColumn(entryPeer::SUBP_ID);
		$criteria->addSelectColumn(entryPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(entryPeer::SEARCH_TEXT);
		$criteria->addSelectColumn(entryPeer::SCREEN_NAME);
		$criteria->addSelectColumn(entryPeer::SITE_URL);
		$criteria->addSelectColumn(entryPeer::PERMISSIONS);
		$criteria->addSelectColumn(entryPeer::GROUP_ID);
		$criteria->addSelectColumn(entryPeer::PLAYS);
		$criteria->addSelectColumn(entryPeer::PARTNER_DATA);
		$criteria->addSelectColumn(entryPeer::INT_ID);
		$criteria->addSelectColumn(entryPeer::INDEXED_CUSTOM_DATA_1);
		$criteria->addSelectColumn(entryPeer::DESCRIPTION);
		$criteria->addSelectColumn(entryPeer::MEDIA_DATE);
		$criteria->addSelectColumn(entryPeer::ADMIN_TAGS);
		$criteria->addSelectColumn(entryPeer::MODERATION_STATUS);
		$criteria->addSelectColumn(entryPeer::MODERATION_COUNT);
		$criteria->addSelectColumn(entryPeer::MODIFIED_AT);
		$criteria->addSelectColumn(entryPeer::PUSER_ID);
		$criteria->addSelectColumn(entryPeer::ACCESS_CONTROL_ID);
		$criteria->addSelectColumn(entryPeer::CONVERSION_PROFILE_ID);
		$criteria->addSelectColumn(entryPeer::CATEGORIES);
		$criteria->addSelectColumn(entryPeer::CATEGORIES_IDS);
		$criteria->addSelectColumn(entryPeer::START_DATE);
		$criteria->addSelectColumn(entryPeer::END_DATE);
		$criteria->addSelectColumn(entryPeer::SEARCH_TEXT_DISCRETE);
		$criteria->addSelectColumn(entryPeer::FLAVOR_PARAMS_IDS);
		$criteria->addSelectColumn(entryPeer::AVAILABLE_FROM);
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
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = entryPeer::doCountStmt($criteria, $con);

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
	 * @return     entry
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = entryPeer::doSelect($critcopy, $con);
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
		return entryPeer::populateObjects(entryPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = entryPeer::getCriteriaFilter();
		
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
			entryPeer::setDefaultCriteriaFilter();
		
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
		entryPeer::getCriteriaFilter()->applyFilter($criteria);
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
		entryPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = entryPeer::alternativeCon ( $con );
		
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
		$con = entryPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				entryPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			entryPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		entryPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      entry $value A entry object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(entry $obj, $key = null)
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
	 * @param      mixed $value A entry object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof entry) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or entry object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     entry Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to entry
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
			$key = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = entryPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				entryPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related kshow table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkshow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kuser table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkuser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related accessControl table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinaccessControl(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related conversionProfile2 table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinconversionProfile2(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of entry objects pre-filled with their kshow objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkshow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);
		kshowPeer::addSelectColumns($criteria);

		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$stmt = entryPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kshowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (entry) to $obj2 (kshow)
				$obj2->addentry($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with their kuser objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);
		kuserPeer::addSelectColumns($criteria);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$stmt = entryPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kuserPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (entry) to $obj2 (kuser)
				$obj2->addentry($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with their accessControl objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinaccessControl(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);
		accessControlPeer::addSelectColumns($criteria);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$stmt = entryPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = accessControlPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = accessControlPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = accessControlPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					accessControlPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (entry) to $obj2 (accessControl)
				$obj2->addentry($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with their conversionProfile2 objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinconversionProfile2(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);
		conversionProfile2Peer::addSelectColumns($criteria);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = conversionProfile2Peer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = conversionProfile2Peer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					conversionProfile2Peer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (entry) to $obj2 (conversionProfile2)
				$obj2->addentry($obj1);

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
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of entry objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
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

		entryPeer::addSelectColumns($criteria);
		$startcol2 = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		accessControlPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (accessControlPeer::NUM_COLUMNS - accessControlPeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = entryPeer::getOMClass($row, 0);
        $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined kshow rows

			$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = kshowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (entry) to the collection in $obj2 (kshow)
				$obj2->addentry($obj1);
			} // if joined row not null

			// Add objects for joined kuser rows

			$key3 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = kuserPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = kuserPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					kuserPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (entry) to the collection in $obj3 (kuser)
				$obj3->addentry($obj1);
			} // if joined row not null

			// Add objects for joined accessControl rows

			$key4 = accessControlPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = accessControlPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = accessControlPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					accessControlPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (entry) to the collection in $obj4 (accessControl)
				$obj4->addentry($obj1);
			} // if joined row not null

			// Add objects for joined conversionProfile2 rows

			$key5 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = conversionProfile2Peer::getInstanceFromPool($key5);
				if (!$obj5) {

					$cls = conversionProfile2Peer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					conversionProfile2Peer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (entry) to the collection in $obj5 (conversionProfile2)
				$obj5->addentry($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kshow table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptkshow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related kuser table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptkuser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related accessControl table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptaccessControl(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related conversionProfile2 table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptconversionProfile2(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(entryPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			entryPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$stmt = entryPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of entry objects pre-filled with all related objects except kshow.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptkshow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol2 = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		accessControlPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (accessControlPeer::NUM_COLUMNS - accessControlPeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined kuser rows

				$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = kuserPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (entry) to the collection in $obj2 (kuser)
				$obj2->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined accessControl rows

				$key3 = accessControlPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = accessControlPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = accessControlPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					accessControlPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (entry) to the collection in $obj3 (accessControl)
				$obj3->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined conversionProfile2 rows

				$key4 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = conversionProfile2Peer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = conversionProfile2Peer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					conversionProfile2Peer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (entry) to the collection in $obj4 (conversionProfile2)
				$obj4->addentry($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with all related objects except kuser.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol2 = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		accessControlPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (accessControlPeer::NUM_COLUMNS - accessControlPeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined kshow rows

				$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = kshowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (entry) to the collection in $obj2 (kshow)
				$obj2->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined accessControl rows

				$key3 = accessControlPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = accessControlPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = accessControlPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					accessControlPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (entry) to the collection in $obj3 (accessControl)
				$obj3->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined conversionProfile2 rows

				$key4 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = conversionProfile2Peer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = conversionProfile2Peer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					conversionProfile2Peer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (entry) to the collection in $obj4 (conversionProfile2)
				$obj4->addentry($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with all related objects except accessControl.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptaccessControl(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol2 = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		conversionProfile2Peer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (conversionProfile2Peer::NUM_COLUMNS - conversionProfile2Peer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::CONVERSION_PROFILE_ID, conversionProfile2Peer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined kshow rows

				$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = kshowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (entry) to the collection in $obj2 (kshow)
				$obj2->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined kuser rows

				$key3 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = kuserPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = kuserPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					kuserPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (entry) to the collection in $obj3 (kuser)
				$obj3->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined conversionProfile2 rows

				$key4 = conversionProfile2Peer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = conversionProfile2Peer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = conversionProfile2Peer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					conversionProfile2Peer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (entry) to the collection in $obj4 (conversionProfile2)
				$obj4->addentry($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of entry objects pre-filled with all related objects except conversionProfile2.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of entry objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptconversionProfile2(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		entryPeer::addSelectColumns($criteria);
		$startcol2 = (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		kshowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		accessControlPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (accessControlPeer::NUM_COLUMNS - accessControlPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, $join_behavior);

		$criteria->addJoin(entryPeer::ACCESS_CONTROL_ID, accessControlPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = entryPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = entryPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = entryPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				entryPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined kshow rows

				$key2 = kshowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = kshowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = kshowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kshowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (entry) to the collection in $obj2 (kshow)
				$obj2->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined kuser rows

				$key3 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = kuserPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = kuserPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					kuserPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (entry) to the collection in $obj3 (kuser)
				$obj3->addentry($obj1);

			} // if joined row is not null

				// Add objects for joined accessControl rows

				$key4 = accessControlPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = accessControlPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = accessControlPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					accessControlPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (entry) to the collection in $obj4 (accessControl)
				$obj4->addentry($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseentryPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseentryPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new entryTableMap());
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
	 * Method perform an INSERT on the database, given a entry or Criteria object.
	 *
	 * @param      mixed $values Criteria or entry object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from entry object
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
	 * Method perform an UPDATE on the database, given a entry or Criteria object.
	 *
	 * @param      mixed $values Criteria or entry object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(entryPeer::ID);
			$selectCriteria->add(entryPeer::ID, $criteria->remove(entryPeer::ID), $comparison);

		} else { // $values is entry object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the entry table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(entryPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			entryPeer::clearInstancePool();
			entryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a entry or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or entry object or primary key or array of primary keys
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
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			entryPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof entry) { // it's a model object
			// invalidate the cache for this single object
			entryPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(entryPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				entryPeer::removeInstanceFromPool($singleval);
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
			entryPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given entry object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      entry $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(entry $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(entryPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(entryPeer::TABLE_NAME);

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

		return BasePeer::doValidate(entryPeer::DATABASE_NAME, entryPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     entry
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = entryPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(entryPeer::DATABASE_NAME);
		$criteria->add(entryPeer::ID, $pk);

		$v = entryPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
			$criteria->add(entryPeer::ID, $pks, Criteria::IN);
			$objs = entryPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseentryPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseentryPeer::buildTableMap();

