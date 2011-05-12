<?php

/**
 * Base static class for performing query and update operations on the 'kshow' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BasekshowPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'kshow';

	/** the related Propel class for this table */
	const OM_CLASS = 'kshow';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.kshow';

	/** the related TableMap class for this table */
	const TM_CLASS = 'kshowTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 52;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'kshow.ID';

	/** the column name for the PRODUCER_ID field */
	const PRODUCER_ID = 'kshow.PRODUCER_ID';

	/** the column name for the EPISODE_ID field */
	const EPISODE_ID = 'kshow.EPISODE_ID';

	/** the column name for the NAME field */
	const NAME = 'kshow.NAME';

	/** the column name for the SUBDOMAIN field */
	const SUBDOMAIN = 'kshow.SUBDOMAIN';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'kshow.DESCRIPTION';

	/** the column name for the STATUS field */
	const STATUS = 'kshow.STATUS';

	/** the column name for the TYPE field */
	const TYPE = 'kshow.TYPE';

	/** the column name for the MEDIA_TYPE field */
	const MEDIA_TYPE = 'kshow.MEDIA_TYPE';

	/** the column name for the FORMAT_TYPE field */
	const FORMAT_TYPE = 'kshow.FORMAT_TYPE';

	/** the column name for the LANGUAGE field */
	const LANGUAGE = 'kshow.LANGUAGE';

	/** the column name for the START_DATE field */
	const START_DATE = 'kshow.START_DATE';

	/** the column name for the END_DATE field */
	const END_DATE = 'kshow.END_DATE';

	/** the column name for the SKIN field */
	const SKIN = 'kshow.SKIN';

	/** the column name for the THUMBNAIL field */
	const THUMBNAIL = 'kshow.THUMBNAIL';

	/** the column name for the SHOW_ENTRY_ID field */
	const SHOW_ENTRY_ID = 'kshow.SHOW_ENTRY_ID';

	/** the column name for the INTRO_ID field */
	const INTRO_ID = 'kshow.INTRO_ID';

	/** the column name for the VIEWS field */
	const VIEWS = 'kshow.VIEWS';

	/** the column name for the VOTES field */
	const VOTES = 'kshow.VOTES';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'kshow.COMMENTS';

	/** the column name for the FAVORITES field */
	const FAVORITES = 'kshow.FAVORITES';

	/** the column name for the RANK field */
	const RANK = 'kshow.RANK';

	/** the column name for the ENTRIES field */
	const ENTRIES = 'kshow.ENTRIES';

	/** the column name for the CONTRIBUTORS field */
	const CONTRIBUTORS = 'kshow.CONTRIBUTORS';

	/** the column name for the SUBSCRIBERS field */
	const SUBSCRIBERS = 'kshow.SUBSCRIBERS';

	/** the column name for the NUMBER_OF_UPDATES field */
	const NUMBER_OF_UPDATES = 'kshow.NUMBER_OF_UPDATES';

	/** the column name for the TAGS field */
	const TAGS = 'kshow.TAGS';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'kshow.CUSTOM_DATA';

	/** the column name for the INDEXED_CUSTOM_DATA_1 field */
	const INDEXED_CUSTOM_DATA_1 = 'kshow.INDEXED_CUSTOM_DATA_1';

	/** the column name for the INDEXED_CUSTOM_DATA_2 field */
	const INDEXED_CUSTOM_DATA_2 = 'kshow.INDEXED_CUSTOM_DATA_2';

	/** the column name for the INDEXED_CUSTOM_DATA_3 field */
	const INDEXED_CUSTOM_DATA_3 = 'kshow.INDEXED_CUSTOM_DATA_3';

	/** the column name for the REOCCURENCE field */
	const REOCCURENCE = 'kshow.REOCCURENCE';

	/** the column name for the LICENSE_TYPE field */
	const LICENSE_TYPE = 'kshow.LICENSE_TYPE';

	/** the column name for the LENGTH_IN_MSECS field */
	const LENGTH_IN_MSECS = 'kshow.LENGTH_IN_MSECS';

	/** the column name for the VIEW_PERMISSIONS field */
	const VIEW_PERMISSIONS = 'kshow.VIEW_PERMISSIONS';

	/** the column name for the VIEW_PASSWORD field */
	const VIEW_PASSWORD = 'kshow.VIEW_PASSWORD';

	/** the column name for the CONTRIB_PERMISSIONS field */
	const CONTRIB_PERMISSIONS = 'kshow.CONTRIB_PERMISSIONS';

	/** the column name for the CONTRIB_PASSWORD field */
	const CONTRIB_PASSWORD = 'kshow.CONTRIB_PASSWORD';

	/** the column name for the EDIT_PERMISSIONS field */
	const EDIT_PERMISSIONS = 'kshow.EDIT_PERMISSIONS';

	/** the column name for the EDIT_PASSWORD field */
	const EDIT_PASSWORD = 'kshow.EDIT_PASSWORD';

	/** the column name for the SALT field */
	const SALT = 'kshow.SALT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'kshow.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'kshow.UPDATED_AT';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'kshow.PARTNER_ID';

	/** the column name for the DISPLAY_IN_SEARCH field */
	const DISPLAY_IN_SEARCH = 'kshow.DISPLAY_IN_SEARCH';

	/** the column name for the SUBP_ID field */
	const SUBP_ID = 'kshow.SUBP_ID';

	/** the column name for the SEARCH_TEXT field */
	const SEARCH_TEXT = 'kshow.SEARCH_TEXT';

	/** the column name for the PERMISSIONS field */
	const PERMISSIONS = 'kshow.PERMISSIONS';

	/** the column name for the GROUP_ID field */
	const GROUP_ID = 'kshow.GROUP_ID';

	/** the column name for the PLAYS field */
	const PLAYS = 'kshow.PLAYS';

	/** the column name for the PARTNER_DATA field */
	const PARTNER_DATA = 'kshow.PARTNER_DATA';

	/** the column name for the INT_ID field */
	const INT_ID = 'kshow.INT_ID';

	/**
	 * An identiy map to hold any loaded instances of kshow objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array kshow[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'ProducerId', 'EpisodeId', 'Name', 'Subdomain', 'Description', 'Status', 'Type', 'MediaType', 'FormatType', 'Language', 'StartDate', 'EndDate', 'Skin', 'Thumbnail', 'ShowEntryId', 'IntroId', 'Views', 'Votes', 'Comments', 'Favorites', 'Rank', 'Entries', 'Contributors', 'Subscribers', 'NumberOfUpdates', 'Tags', 'CustomData', 'IndexedCustomData1', 'IndexedCustomData2', 'IndexedCustomData3', 'Reoccurence', 'LicenseType', 'LengthInMsecs', 'ViewPermissions', 'ViewPassword', 'ContribPermissions', 'ContribPassword', 'EditPermissions', 'EditPassword', 'Salt', 'CreatedAt', 'UpdatedAt', 'PartnerId', 'DisplayInSearch', 'SubpId', 'SearchText', 'Permissions', 'GroupId', 'Plays', 'PartnerData', 'IntId', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'producerId', 'episodeId', 'name', 'subdomain', 'description', 'status', 'type', 'mediaType', 'formatType', 'language', 'startDate', 'endDate', 'skin', 'thumbnail', 'showEntryId', 'introId', 'views', 'votes', 'comments', 'favorites', 'rank', 'entries', 'contributors', 'subscribers', 'numberOfUpdates', 'tags', 'customData', 'indexedCustomData1', 'indexedCustomData2', 'indexedCustomData3', 'reoccurence', 'licenseType', 'lengthInMsecs', 'viewPermissions', 'viewPassword', 'contribPermissions', 'contribPassword', 'editPermissions', 'editPassword', 'salt', 'createdAt', 'updatedAt', 'partnerId', 'displayInSearch', 'subpId', 'searchText', 'permissions', 'groupId', 'plays', 'partnerData', 'intId', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PRODUCER_ID, self::EPISODE_ID, self::NAME, self::SUBDOMAIN, self::DESCRIPTION, self::STATUS, self::TYPE, self::MEDIA_TYPE, self::FORMAT_TYPE, self::LANGUAGE, self::START_DATE, self::END_DATE, self::SKIN, self::THUMBNAIL, self::SHOW_ENTRY_ID, self::INTRO_ID, self::VIEWS, self::VOTES, self::COMMENTS, self::FAVORITES, self::RANK, self::ENTRIES, self::CONTRIBUTORS, self::SUBSCRIBERS, self::NUMBER_OF_UPDATES, self::TAGS, self::CUSTOM_DATA, self::INDEXED_CUSTOM_DATA_1, self::INDEXED_CUSTOM_DATA_2, self::INDEXED_CUSTOM_DATA_3, self::REOCCURENCE, self::LICENSE_TYPE, self::LENGTH_IN_MSECS, self::VIEW_PERMISSIONS, self::VIEW_PASSWORD, self::CONTRIB_PERMISSIONS, self::CONTRIB_PASSWORD, self::EDIT_PERMISSIONS, self::EDIT_PASSWORD, self::SALT, self::CREATED_AT, self::UPDATED_AT, self::PARTNER_ID, self::DISPLAY_IN_SEARCH, self::SUBP_ID, self::SEARCH_TEXT, self::PERMISSIONS, self::GROUP_ID, self::PLAYS, self::PARTNER_DATA, self::INT_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'producer_id', 'episode_id', 'name', 'subdomain', 'description', 'status', 'type', 'media_type', 'format_type', 'language', 'start_date', 'end_date', 'skin', 'thumbnail', 'show_entry_id', 'intro_id', 'views', 'votes', 'comments', 'favorites', 'rank', 'entries', 'contributors', 'subscribers', 'number_of_updates', 'tags', 'custom_data', 'indexed_custom_data_1', 'indexed_custom_data_2', 'indexed_custom_data_3', 'reoccurence', 'license_type', 'length_in_msecs', 'view_permissions', 'view_password', 'contrib_permissions', 'contrib_password', 'edit_permissions', 'edit_password', 'salt', 'created_at', 'updated_at', 'partner_id', 'display_in_search', 'subp_id', 'search_text', 'permissions', 'group_id', 'plays', 'partner_data', 'int_id', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'ProducerId' => 1, 'EpisodeId' => 2, 'Name' => 3, 'Subdomain' => 4, 'Description' => 5, 'Status' => 6, 'Type' => 7, 'MediaType' => 8, 'FormatType' => 9, 'Language' => 10, 'StartDate' => 11, 'EndDate' => 12, 'Skin' => 13, 'Thumbnail' => 14, 'ShowEntryId' => 15, 'IntroId' => 16, 'Views' => 17, 'Votes' => 18, 'Comments' => 19, 'Favorites' => 20, 'Rank' => 21, 'Entries' => 22, 'Contributors' => 23, 'Subscribers' => 24, 'NumberOfUpdates' => 25, 'Tags' => 26, 'CustomData' => 27, 'IndexedCustomData1' => 28, 'IndexedCustomData2' => 29, 'IndexedCustomData3' => 30, 'Reoccurence' => 31, 'LicenseType' => 32, 'LengthInMsecs' => 33, 'ViewPermissions' => 34, 'ViewPassword' => 35, 'ContribPermissions' => 36, 'ContribPassword' => 37, 'EditPermissions' => 38, 'EditPassword' => 39, 'Salt' => 40, 'CreatedAt' => 41, 'UpdatedAt' => 42, 'PartnerId' => 43, 'DisplayInSearch' => 44, 'SubpId' => 45, 'SearchText' => 46, 'Permissions' => 47, 'GroupId' => 48, 'Plays' => 49, 'PartnerData' => 50, 'IntId' => 51, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'producerId' => 1, 'episodeId' => 2, 'name' => 3, 'subdomain' => 4, 'description' => 5, 'status' => 6, 'type' => 7, 'mediaType' => 8, 'formatType' => 9, 'language' => 10, 'startDate' => 11, 'endDate' => 12, 'skin' => 13, 'thumbnail' => 14, 'showEntryId' => 15, 'introId' => 16, 'views' => 17, 'votes' => 18, 'comments' => 19, 'favorites' => 20, 'rank' => 21, 'entries' => 22, 'contributors' => 23, 'subscribers' => 24, 'numberOfUpdates' => 25, 'tags' => 26, 'customData' => 27, 'indexedCustomData1' => 28, 'indexedCustomData2' => 29, 'indexedCustomData3' => 30, 'reoccurence' => 31, 'licenseType' => 32, 'lengthInMsecs' => 33, 'viewPermissions' => 34, 'viewPassword' => 35, 'contribPermissions' => 36, 'contribPassword' => 37, 'editPermissions' => 38, 'editPassword' => 39, 'salt' => 40, 'createdAt' => 41, 'updatedAt' => 42, 'partnerId' => 43, 'displayInSearch' => 44, 'subpId' => 45, 'searchText' => 46, 'permissions' => 47, 'groupId' => 48, 'plays' => 49, 'partnerData' => 50, 'intId' => 51, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PRODUCER_ID => 1, self::EPISODE_ID => 2, self::NAME => 3, self::SUBDOMAIN => 4, self::DESCRIPTION => 5, self::STATUS => 6, self::TYPE => 7, self::MEDIA_TYPE => 8, self::FORMAT_TYPE => 9, self::LANGUAGE => 10, self::START_DATE => 11, self::END_DATE => 12, self::SKIN => 13, self::THUMBNAIL => 14, self::SHOW_ENTRY_ID => 15, self::INTRO_ID => 16, self::VIEWS => 17, self::VOTES => 18, self::COMMENTS => 19, self::FAVORITES => 20, self::RANK => 21, self::ENTRIES => 22, self::CONTRIBUTORS => 23, self::SUBSCRIBERS => 24, self::NUMBER_OF_UPDATES => 25, self::TAGS => 26, self::CUSTOM_DATA => 27, self::INDEXED_CUSTOM_DATA_1 => 28, self::INDEXED_CUSTOM_DATA_2 => 29, self::INDEXED_CUSTOM_DATA_3 => 30, self::REOCCURENCE => 31, self::LICENSE_TYPE => 32, self::LENGTH_IN_MSECS => 33, self::VIEW_PERMISSIONS => 34, self::VIEW_PASSWORD => 35, self::CONTRIB_PERMISSIONS => 36, self::CONTRIB_PASSWORD => 37, self::EDIT_PERMISSIONS => 38, self::EDIT_PASSWORD => 39, self::SALT => 40, self::CREATED_AT => 41, self::UPDATED_AT => 42, self::PARTNER_ID => 43, self::DISPLAY_IN_SEARCH => 44, self::SUBP_ID => 45, self::SEARCH_TEXT => 46, self::PERMISSIONS => 47, self::GROUP_ID => 48, self::PLAYS => 49, self::PARTNER_DATA => 50, self::INT_ID => 51, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'producer_id' => 1, 'episode_id' => 2, 'name' => 3, 'subdomain' => 4, 'description' => 5, 'status' => 6, 'type' => 7, 'media_type' => 8, 'format_type' => 9, 'language' => 10, 'start_date' => 11, 'end_date' => 12, 'skin' => 13, 'thumbnail' => 14, 'show_entry_id' => 15, 'intro_id' => 16, 'views' => 17, 'votes' => 18, 'comments' => 19, 'favorites' => 20, 'rank' => 21, 'entries' => 22, 'contributors' => 23, 'subscribers' => 24, 'number_of_updates' => 25, 'tags' => 26, 'custom_data' => 27, 'indexed_custom_data_1' => 28, 'indexed_custom_data_2' => 29, 'indexed_custom_data_3' => 30, 'reoccurence' => 31, 'license_type' => 32, 'length_in_msecs' => 33, 'view_permissions' => 34, 'view_password' => 35, 'contrib_permissions' => 36, 'contrib_password' => 37, 'edit_permissions' => 38, 'edit_password' => 39, 'salt' => 40, 'created_at' => 41, 'updated_at' => 42, 'partner_id' => 43, 'display_in_search' => 44, 'subp_id' => 45, 'search_text' => 46, 'permissions' => 47, 'group_id' => 48, 'plays' => 49, 'partner_data' => 50, 'int_id' => 51, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, )
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
	 * @param      string $column The column name for current table. (i.e. kshowPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(kshowPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(kshowPeer::ID);
		$criteria->addSelectColumn(kshowPeer::PRODUCER_ID);
		$criteria->addSelectColumn(kshowPeer::EPISODE_ID);
		$criteria->addSelectColumn(kshowPeer::NAME);
		$criteria->addSelectColumn(kshowPeer::SUBDOMAIN);
		$criteria->addSelectColumn(kshowPeer::DESCRIPTION);
		$criteria->addSelectColumn(kshowPeer::STATUS);
		$criteria->addSelectColumn(kshowPeer::TYPE);
		$criteria->addSelectColumn(kshowPeer::MEDIA_TYPE);
		$criteria->addSelectColumn(kshowPeer::FORMAT_TYPE);
		$criteria->addSelectColumn(kshowPeer::LANGUAGE);
		$criteria->addSelectColumn(kshowPeer::START_DATE);
		$criteria->addSelectColumn(kshowPeer::END_DATE);
		$criteria->addSelectColumn(kshowPeer::SKIN);
		$criteria->addSelectColumn(kshowPeer::THUMBNAIL);
		$criteria->addSelectColumn(kshowPeer::SHOW_ENTRY_ID);
		$criteria->addSelectColumn(kshowPeer::INTRO_ID);
		$criteria->addSelectColumn(kshowPeer::VIEWS);
		$criteria->addSelectColumn(kshowPeer::VOTES);
		$criteria->addSelectColumn(kshowPeer::COMMENTS);
		$criteria->addSelectColumn(kshowPeer::FAVORITES);
		$criteria->addSelectColumn(kshowPeer::RANK);
		$criteria->addSelectColumn(kshowPeer::ENTRIES);
		$criteria->addSelectColumn(kshowPeer::CONTRIBUTORS);
		$criteria->addSelectColumn(kshowPeer::SUBSCRIBERS);
		$criteria->addSelectColumn(kshowPeer::NUMBER_OF_UPDATES);
		$criteria->addSelectColumn(kshowPeer::TAGS);
		$criteria->addSelectColumn(kshowPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(kshowPeer::INDEXED_CUSTOM_DATA_1);
		$criteria->addSelectColumn(kshowPeer::INDEXED_CUSTOM_DATA_2);
		$criteria->addSelectColumn(kshowPeer::INDEXED_CUSTOM_DATA_3);
		$criteria->addSelectColumn(kshowPeer::REOCCURENCE);
		$criteria->addSelectColumn(kshowPeer::LICENSE_TYPE);
		$criteria->addSelectColumn(kshowPeer::LENGTH_IN_MSECS);
		$criteria->addSelectColumn(kshowPeer::VIEW_PERMISSIONS);
		$criteria->addSelectColumn(kshowPeer::VIEW_PASSWORD);
		$criteria->addSelectColumn(kshowPeer::CONTRIB_PERMISSIONS);
		$criteria->addSelectColumn(kshowPeer::CONTRIB_PASSWORD);
		$criteria->addSelectColumn(kshowPeer::EDIT_PERMISSIONS);
		$criteria->addSelectColumn(kshowPeer::EDIT_PASSWORD);
		$criteria->addSelectColumn(kshowPeer::SALT);
		$criteria->addSelectColumn(kshowPeer::CREATED_AT);
		$criteria->addSelectColumn(kshowPeer::UPDATED_AT);
		$criteria->addSelectColumn(kshowPeer::PARTNER_ID);
		$criteria->addSelectColumn(kshowPeer::DISPLAY_IN_SEARCH);
		$criteria->addSelectColumn(kshowPeer::SUBP_ID);
		$criteria->addSelectColumn(kshowPeer::SEARCH_TEXT);
		$criteria->addSelectColumn(kshowPeer::PERMISSIONS);
		$criteria->addSelectColumn(kshowPeer::GROUP_ID);
		$criteria->addSelectColumn(kshowPeer::PLAYS);
		$criteria->addSelectColumn(kshowPeer::PARTNER_DATA);
		$criteria->addSelectColumn(kshowPeer::INT_ID);
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
		$criteria->setPrimaryTableName(kshowPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kshowPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		kshowPeer::attachCriteriaFilter($criteria);

		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'kshowPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// set the connection to slave server
		$con = kshowPeer::alternativeCon ($con);
		
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
	 * @return     kshow
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = kshowPeer::doSelect($critcopy, $con);
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
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      string $queryType The type of the query: select / count.
	 * @return     string The invalidation key that should be checked before returning a cached result for this criteria.
	 *		 if null is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys(Criteria $criteria, $queryType)
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 */
	public static function filterSelectResults(&$selectResults)
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
			$objFromPool = kshowPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				kshowPeer::addInstanceToPool($curObject);
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
			kshowPeer::addInstanceToPool($curResult);
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
		$criteria = kshowPeer::prepareCriteriaForSelect($criteria);
		
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_SELECT,
			'kshowPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			kshowPeer::filterSelectResults($cachedResult);
			kshowPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}

		$con = kshowPeer::alternativeCon($con);
		
		$queryResult = kshowPeer::populateObjects(BasePeer::doSelect($criteria, $con));
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
		}
		
		kshowPeer::filterSelectResults($queryResult);
		kshowPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(kshowPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = kshowPeer::getCriteriaFilter();
		
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
			kshowPeer::setDefaultCriteriaFilter();
		
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
		kshowPeer::getCriteriaFilter()->applyFilter($criteria);
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
		kshowPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = kshowPeer::alternativeCon ( $con );
		
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
				kshowPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			kshowPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		kshowPeer::attachCriteriaFilter($criteria);

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
		$con = kshowPeer::alternativeCon($con);
		
		$criteria = kshowPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      kshow $value A kshow object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(kshow $obj, $key = null)
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
	 * @param      mixed $value A kshow object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof kshow) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or kshow object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     kshow Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to kshow
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
		$cls = kshowPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = kshowPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = kshowPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(kshowPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kshowPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, $join_behavior);

		$stmt = kshowPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of kshow objects pre-filled with their kuser objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kshow objects.
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

		kshowPeer::addSelectColumns($criteria);
		$startcol = (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);
		kuserPeer::addSelectColumns($criteria);

		$criteria->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, $join_behavior);

		$stmt = kshowPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kshowPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kshowPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = kshowPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kshowPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (kshow) to $obj2 (kuser)
				$obj2->addkshow($obj1);

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
		$criteria->setPrimaryTableName(kshowPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			kshowPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, $join_behavior);

		$stmt = kshowPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of kshow objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of kshow objects.
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

		kshowPeer::addSelectColumns($criteria);
		$startcol2 = (kshowPeer::NUM_COLUMNS - kshowPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, $join_behavior);

		$stmt = kshowPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = kshowPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = kshowPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = kshowPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				kshowPeer::addInstanceToPool($obj1, $key1);
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
				} // if obj2 loaded

				// Add the $obj1 (kshow) to the collection in $obj2 (kuser)
				$obj2->addkshow($obj1);
			} // if joined row not null

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
	  $dbMap = Propel::getDatabaseMap(BasekshowPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasekshowPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new kshowTableMap());
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
		return $withPrefix ? kshowPeer::CLASS_DEFAULT : kshowPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a kshow or Criteria object.
	 *
	 * @param      mixed $values Criteria or kshow object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kshowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from kshow object
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
	 * Method perform an UPDATE on the database, given a kshow or Criteria object.
	 *
	 * @param      mixed $values Criteria or kshow object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kshowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(kshowPeer::ID);
			$selectCriteria->add(kshowPeer::ID, $criteria->remove(kshowPeer::ID), $comparison);

		} else { // $values is kshow object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the kshow table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(kshowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(kshowPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			kshowPeer::clearInstancePool();
			kshowPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a kshow or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or kshow object or primary key or array of primary keys
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
			$con = Propel::getConnection(kshowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			kshowPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof kshow) { // it's a model object
			// invalidate the cache for this single object
			kshowPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(kshowPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				kshowPeer::removeInstanceFromPool($singleval);
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
			kshowPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given kshow object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      kshow $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(kshow $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(kshowPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(kshowPeer::TABLE_NAME);

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

		return BasePeer::doValidate(kshowPeer::DATABASE_NAME, kshowPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     kshow
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = kshowPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(kshowPeer::DATABASE_NAME);
		$criteria->add(kshowPeer::ID, $pk);

		$v = kshowPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(kshowPeer::DATABASE_NAME);
			$criteria->add(kshowPeer::ID, $pks, Criteria::IN);
			$objs = kshowPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasekshowPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasekshowPeer::buildTableMap();

