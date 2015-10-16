<?php

/**
 * Base static class for performing query and update operations on the 'flavor_params_output' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseassetParamsOutputPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_params_output';

	/** the related Propel class for this table */
	const OM_CLASS = 'assetParamsOutput';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.assetParamsOutput';

	/** the related TableMap class for this table */
	const TM_CLASS = 'assetParamsOutputTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 38;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'flavor_params_output.ID';

	/** the column name for the FLAVOR_PARAMS_ID field */
	const FLAVOR_PARAMS_ID = 'flavor_params_output.FLAVOR_PARAMS_ID';

	/** the column name for the FLAVOR_PARAMS_VERSION field */
	const FLAVOR_PARAMS_VERSION = 'flavor_params_output.FLAVOR_PARAMS_VERSION';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'flavor_params_output.PARTNER_ID';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'flavor_params_output.ENTRY_ID';

	/** the column name for the FLAVOR_ASSET_ID field */
	const FLAVOR_ASSET_ID = 'flavor_params_output.FLAVOR_ASSET_ID';

	/** the column name for the FLAVOR_ASSET_VERSION field */
	const FLAVOR_ASSET_VERSION = 'flavor_params_output.FLAVOR_ASSET_VERSION';

	/** the column name for the NAME field */
	const NAME = 'flavor_params_output.NAME';

	/** the column name for the TAGS field */
	const TAGS = 'flavor_params_output.TAGS';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'flavor_params_output.DESCRIPTION';

	/** the column name for the READY_BEHAVIOR field */
	const READY_BEHAVIOR = 'flavor_params_output.READY_BEHAVIOR';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'flavor_params_output.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'flavor_params_output.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'flavor_params_output.DELETED_AT';

	/** the column name for the IS_DEFAULT field */
	const IS_DEFAULT = 'flavor_params_output.IS_DEFAULT';

	/** the column name for the FORMAT field */
	const FORMAT = 'flavor_params_output.FORMAT';

	/** the column name for the VIDEO_CODEC field */
	const VIDEO_CODEC = 'flavor_params_output.VIDEO_CODEC';

	/** the column name for the VIDEO_BITRATE field */
	const VIDEO_BITRATE = 'flavor_params_output.VIDEO_BITRATE';

	/** the column name for the AUDIO_CODEC field */
	const AUDIO_CODEC = 'flavor_params_output.AUDIO_CODEC';

	/** the column name for the AUDIO_BITRATE field */
	const AUDIO_BITRATE = 'flavor_params_output.AUDIO_BITRATE';

	/** the column name for the AUDIO_CHANNELS field */
	const AUDIO_CHANNELS = 'flavor_params_output.AUDIO_CHANNELS';

	/** the column name for the AUDIO_SAMPLE_RATE field */
	const AUDIO_SAMPLE_RATE = 'flavor_params_output.AUDIO_SAMPLE_RATE';

	/** the column name for the AUDIO_RESOLUTION field */
	const AUDIO_RESOLUTION = 'flavor_params_output.AUDIO_RESOLUTION';

	/** the column name for the WIDTH field */
	const WIDTH = 'flavor_params_output.WIDTH';

	/** the column name for the HEIGHT field */
	const HEIGHT = 'flavor_params_output.HEIGHT';

	/** the column name for the FRAME_RATE field */
	const FRAME_RATE = 'flavor_params_output.FRAME_RATE';

	/** the column name for the GOP_SIZE field */
	const GOP_SIZE = 'flavor_params_output.GOP_SIZE';

	/** the column name for the TWO_PASS field */
	const TWO_PASS = 'flavor_params_output.TWO_PASS';

	/** the column name for the CONVERSION_ENGINES field */
	const CONVERSION_ENGINES = 'flavor_params_output.CONVERSION_ENGINES';

	/** the column name for the CONVERSION_ENGINES_EXTRA_PARAMS field */
	const CONVERSION_ENGINES_EXTRA_PARAMS = 'flavor_params_output.CONVERSION_ENGINES_EXTRA_PARAMS';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'flavor_params_output.CUSTOM_DATA';

	/** the column name for the COMMAND_LINES field */
	const COMMAND_LINES = 'flavor_params_output.COMMAND_LINES';

	/** the column name for the FILE_EXT field */
	const FILE_EXT = 'flavor_params_output.FILE_EXT';

	/** the column name for the DEINTERLICE field */
	const DEINTERLICE = 'flavor_params_output.DEINTERLICE';

	/** the column name for the ROTATE field */
	const ROTATE = 'flavor_params_output.ROTATE';

	/** the column name for the OPERATORS field */
	const OPERATORS = 'flavor_params_output.OPERATORS';

	/** the column name for the ENGINE_VERSION field */
	const ENGINE_VERSION = 'flavor_params_output.ENGINE_VERSION';

	/** the column name for the TYPE field */
	const TYPE = 'flavor_params_output.TYPE';

	/**
	 * An identiy map to hold any loaded instances of assetParamsOutput objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array assetParamsOutput[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'FlavorParamsId', 'FlavorParamsVersion', 'PartnerId', 'EntryId', 'FlavorAssetId', 'FlavorAssetVersion', 'Name', 'Tags', 'Description', 'ReadyBehavior', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'IsDefault', 'Format', 'VideoCodec', 'VideoBitrate', 'AudioCodec', 'AudioBitrate', 'AudioChannels', 'AudioSampleRate', 'AudioResolution', 'Width', 'Height', 'FrameRate', 'GopSize', 'TwoPass', 'ConversionEngines', 'ConversionEnginesExtraParams', 'CustomData', 'CommandLines', 'FileExt', 'Deinterlice', 'Rotate', 'Operators', 'EngineVersion', 'Type', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'flavorParamsId', 'flavorParamsVersion', 'partnerId', 'entryId', 'flavorAssetId', 'flavorAssetVersion', 'name', 'tags', 'description', 'readyBehavior', 'createdAt', 'updatedAt', 'deletedAt', 'isDefault', 'format', 'videoCodec', 'videoBitrate', 'audioCodec', 'audioBitrate', 'audioChannels', 'audioSampleRate', 'audioResolution', 'width', 'height', 'frameRate', 'gopSize', 'twoPass', 'conversionEngines', 'conversionEnginesExtraParams', 'customData', 'commandLines', 'fileExt', 'deinterlice', 'rotate', 'operators', 'engineVersion', 'type', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::FLAVOR_PARAMS_ID, self::FLAVOR_PARAMS_VERSION, self::PARTNER_ID, self::ENTRY_ID, self::FLAVOR_ASSET_ID, self::FLAVOR_ASSET_VERSION, self::NAME, self::TAGS, self::DESCRIPTION, self::READY_BEHAVIOR, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::IS_DEFAULT, self::FORMAT, self::VIDEO_CODEC, self::VIDEO_BITRATE, self::AUDIO_CODEC, self::AUDIO_BITRATE, self::AUDIO_CHANNELS, self::AUDIO_SAMPLE_RATE, self::AUDIO_RESOLUTION, self::WIDTH, self::HEIGHT, self::FRAME_RATE, self::GOP_SIZE, self::TWO_PASS, self::CONVERSION_ENGINES, self::CONVERSION_ENGINES_EXTRA_PARAMS, self::CUSTOM_DATA, self::COMMAND_LINES, self::FILE_EXT, self::DEINTERLICE, self::ROTATE, self::OPERATORS, self::ENGINE_VERSION, self::TYPE, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'flavor_params_id', 'flavor_params_version', 'partner_id', 'entry_id', 'flavor_asset_id', 'flavor_asset_version', 'name', 'tags', 'description', 'ready_behavior', 'created_at', 'updated_at', 'deleted_at', 'is_default', 'format', 'video_codec', 'video_bitrate', 'audio_codec', 'audio_bitrate', 'audio_channels', 'audio_sample_rate', 'audio_resolution', 'width', 'height', 'frame_rate', 'gop_size', 'two_pass', 'conversion_engines', 'conversion_engines_extra_params', 'custom_data', 'command_lines', 'file_ext', 'deinterlice', 'rotate', 'operators', 'engine_version', 'type', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'FlavorParamsId' => 1, 'FlavorParamsVersion' => 2, 'PartnerId' => 3, 'EntryId' => 4, 'FlavorAssetId' => 5, 'FlavorAssetVersion' => 6, 'Name' => 7, 'Tags' => 8, 'Description' => 9, 'ReadyBehavior' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, 'DeletedAt' => 13, 'IsDefault' => 14, 'Format' => 15, 'VideoCodec' => 16, 'VideoBitrate' => 17, 'AudioCodec' => 18, 'AudioBitrate' => 19, 'AudioChannels' => 20, 'AudioSampleRate' => 21, 'AudioResolution' => 22, 'Width' => 23, 'Height' => 24, 'FrameRate' => 25, 'GopSize' => 26, 'TwoPass' => 27, 'ConversionEngines' => 28, 'ConversionEnginesExtraParams' => 29, 'CustomData' => 30, 'CommandLines' => 31, 'FileExt' => 32, 'Deinterlice' => 33, 'Rotate' => 34, 'Operators' => 35, 'EngineVersion' => 36, 'Type' => 37, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'flavorParamsId' => 1, 'flavorParamsVersion' => 2, 'partnerId' => 3, 'entryId' => 4, 'flavorAssetId' => 5, 'flavorAssetVersion' => 6, 'name' => 7, 'tags' => 8, 'description' => 9, 'readyBehavior' => 10, 'createdAt' => 11, 'updatedAt' => 12, 'deletedAt' => 13, 'isDefault' => 14, 'format' => 15, 'videoCodec' => 16, 'videoBitrate' => 17, 'audioCodec' => 18, 'audioBitrate' => 19, 'audioChannels' => 20, 'audioSampleRate' => 21, 'audioResolution' => 22, 'width' => 23, 'height' => 24, 'frameRate' => 25, 'gopSize' => 26, 'twoPass' => 27, 'conversionEngines' => 28, 'conversionEnginesExtraParams' => 29, 'customData' => 30, 'commandLines' => 31, 'fileExt' => 32, 'deinterlice' => 33, 'rotate' => 34, 'operators' => 35, 'engineVersion' => 36, 'type' => 37, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::FLAVOR_PARAMS_ID => 1, self::FLAVOR_PARAMS_VERSION => 2, self::PARTNER_ID => 3, self::ENTRY_ID => 4, self::FLAVOR_ASSET_ID => 5, self::FLAVOR_ASSET_VERSION => 6, self::NAME => 7, self::TAGS => 8, self::DESCRIPTION => 9, self::READY_BEHAVIOR => 10, self::CREATED_AT => 11, self::UPDATED_AT => 12, self::DELETED_AT => 13, self::IS_DEFAULT => 14, self::FORMAT => 15, self::VIDEO_CODEC => 16, self::VIDEO_BITRATE => 17, self::AUDIO_CODEC => 18, self::AUDIO_BITRATE => 19, self::AUDIO_CHANNELS => 20, self::AUDIO_SAMPLE_RATE => 21, self::AUDIO_RESOLUTION => 22, self::WIDTH => 23, self::HEIGHT => 24, self::FRAME_RATE => 25, self::GOP_SIZE => 26, self::TWO_PASS => 27, self::CONVERSION_ENGINES => 28, self::CONVERSION_ENGINES_EXTRA_PARAMS => 29, self::CUSTOM_DATA => 30, self::COMMAND_LINES => 31, self::FILE_EXT => 32, self::DEINTERLICE => 33, self::ROTATE => 34, self::OPERATORS => 35, self::ENGINE_VERSION => 36, self::TYPE => 37, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'flavor_params_id' => 1, 'flavor_params_version' => 2, 'partner_id' => 3, 'entry_id' => 4, 'flavor_asset_id' => 5, 'flavor_asset_version' => 6, 'name' => 7, 'tags' => 8, 'description' => 9, 'ready_behavior' => 10, 'created_at' => 11, 'updated_at' => 12, 'deleted_at' => 13, 'is_default' => 14, 'format' => 15, 'video_codec' => 16, 'video_bitrate' => 17, 'audio_codec' => 18, 'audio_bitrate' => 19, 'audio_channels' => 20, 'audio_sample_rate' => 21, 'audio_resolution' => 22, 'width' => 23, 'height' => 24, 'frame_rate' => 25, 'gop_size' => 26, 'two_pass' => 27, 'conversion_engines' => 28, 'conversion_engines_extra_params' => 29, 'custom_data' => 30, 'command_lines' => 31, 'file_ext' => 32, 'deinterlice' => 33, 'rotate' => 34, 'operators' => 35, 'engine_version' => 36, 'type' => 37, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
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
	 * @param      string $column The column name for current table. (i.e. assetParamsOutputPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(assetParamsOutputPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(assetParamsOutputPeer::ID);
		$criteria->addSelectColumn(assetParamsOutputPeer::FLAVOR_PARAMS_ID);
		$criteria->addSelectColumn(assetParamsOutputPeer::FLAVOR_PARAMS_VERSION);
		$criteria->addSelectColumn(assetParamsOutputPeer::PARTNER_ID);
		$criteria->addSelectColumn(assetParamsOutputPeer::ENTRY_ID);
		$criteria->addSelectColumn(assetParamsOutputPeer::FLAVOR_ASSET_ID);
		$criteria->addSelectColumn(assetParamsOutputPeer::FLAVOR_ASSET_VERSION);
		$criteria->addSelectColumn(assetParamsOutputPeer::NAME);
		$criteria->addSelectColumn(assetParamsOutputPeer::TAGS);
		$criteria->addSelectColumn(assetParamsOutputPeer::DESCRIPTION);
		$criteria->addSelectColumn(assetParamsOutputPeer::READY_BEHAVIOR);
		$criteria->addSelectColumn(assetParamsOutputPeer::CREATED_AT);
		$criteria->addSelectColumn(assetParamsOutputPeer::UPDATED_AT);
		$criteria->addSelectColumn(assetParamsOutputPeer::DELETED_AT);
		$criteria->addSelectColumn(assetParamsOutputPeer::IS_DEFAULT);
		$criteria->addSelectColumn(assetParamsOutputPeer::FORMAT);
		$criteria->addSelectColumn(assetParamsOutputPeer::VIDEO_CODEC);
		$criteria->addSelectColumn(assetParamsOutputPeer::VIDEO_BITRATE);
		$criteria->addSelectColumn(assetParamsOutputPeer::AUDIO_CODEC);
		$criteria->addSelectColumn(assetParamsOutputPeer::AUDIO_BITRATE);
		$criteria->addSelectColumn(assetParamsOutputPeer::AUDIO_CHANNELS);
		$criteria->addSelectColumn(assetParamsOutputPeer::AUDIO_SAMPLE_RATE);
		$criteria->addSelectColumn(assetParamsOutputPeer::AUDIO_RESOLUTION);
		$criteria->addSelectColumn(assetParamsOutputPeer::WIDTH);
		$criteria->addSelectColumn(assetParamsOutputPeer::HEIGHT);
		$criteria->addSelectColumn(assetParamsOutputPeer::FRAME_RATE);
		$criteria->addSelectColumn(assetParamsOutputPeer::GOP_SIZE);
		$criteria->addSelectColumn(assetParamsOutputPeer::TWO_PASS);
		$criteria->addSelectColumn(assetParamsOutputPeer::CONVERSION_ENGINES);
		$criteria->addSelectColumn(assetParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS);
		$criteria->addSelectColumn(assetParamsOutputPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(assetParamsOutputPeer::COMMAND_LINES);
		$criteria->addSelectColumn(assetParamsOutputPeer::FILE_EXT);
		$criteria->addSelectColumn(assetParamsOutputPeer::DEINTERLICE);
		$criteria->addSelectColumn(assetParamsOutputPeer::ROTATE);
		$criteria->addSelectColumn(assetParamsOutputPeer::OPERATORS);
		$criteria->addSelectColumn(assetParamsOutputPeer::ENGINE_VERSION);
		$criteria->addSelectColumn(assetParamsOutputPeer::TYPE);
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
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		assetParamsOutputPeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'assetParamsOutputPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = assetParamsOutputPeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     assetParamsOutput
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = assetParamsOutputPeer::doSelect($critcopy, $con);
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
	 * @return     array The invalidation keys that should be checked before returning a cached result for this criteria.
	 *		 if an empty array is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys()
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 * @param	   Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
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
			$objFromPool = assetParamsOutputPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				assetParamsOutputPeer::addInstanceToPool($curObject);
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
		if (Propel::isInstancePoolingEnabled())
		{
			if ( count( self::$instances ) + count( $queryResult ) <= kConf::get('max_num_instances_in_pool') )
			{  
				foreach ($queryResult as $curResult)
				{
					assetParamsOutputPeer::addInstanceToPool($curResult);
				}
			}
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
		$criteriaForSelect = assetParamsOutputPeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'assetParamsOutputPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			assetParamsOutputPeer::filterSelectResults($cachedResult, $criteriaForSelect);
			assetParamsOutputPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = assetParamsOutputPeer::alternativeCon($con, $queryDB);
		
		$queryResult = assetParamsOutputPeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		assetParamsOutputPeer::filterSelectResults($queryResult, $criteria);
		
		assetParamsOutputPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		if ($con === null)
		{
			switch ($queryDB)
			{
			case kQueryCache::QUERY_DB_MASTER:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
				break;

			case kQueryCache::QUERY_DB_SLAVE:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
				break;
			}
		}
	
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = assetParamsOutputPeer::getCriteriaFilter();
		
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
			assetParamsOutputPeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('assetParamsOutput');
		if ($partnerCriteria)
		{
			call_user_func_array(array('assetParamsOutputPeer','addPartnerToCriteria'), $partnerCriteria);
		}
		
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
		assetParamsOutputPeer::getCriteriaFilter()->applyFilter($criteria);
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
			if(!strlen(strval($partnerGroup)))
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
				// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
				$partners = explode(',', trim($partnerGroup));
				foreach($partners as &$p)
					trim($p); // make sure there are not leading or trailing spaces

				// add the partner_id to the partner_group
				if (!in_array(strval($partnerId), $partners))
					$partners[] = strval($partnerId);
				
				if(count($partners) == 1 && reset($partners) == $partnerId)
				{
					$criteria->addAnd(self::PARTNER_ID, $partnerId);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
					$criteria->addAnd($criterion);
				}
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
		assetParamsOutputPeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = assetParamsOutputPeer::alternativeCon ( $con );
		
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
				assetParamsOutputPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		assetParamsOutputPeer::attachCriteriaFilter($criteria);

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
		$con = assetParamsOutputPeer::alternativeCon($con);
		
		$criteria = assetParamsOutputPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      assetParamsOutput $value A assetParamsOutput object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(assetParamsOutput $obj, $key = null)
	{
		if ( Propel::isInstancePoolingEnabled() )
		{
			if ( $key === null )
			{
				$key = (string) $obj->getId();
			}
				
			if ( isset( self::$instances[$key] )											// Instance is already mapped?
					|| count( self::$instances ) < kConf::get('max_num_instances_in_pool')	// Not mapped, but max. inst. not yet reached?
				)
			{
				self::$instances[$key] = $obj;
				kMemoryManager::registerPeer('assetParamsOutputPeer');
			}
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
	 * @param      mixed $value A assetParamsOutput object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof assetParamsOutput) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or assetParamsOutput object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     assetParamsOutput Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to flavor_params_output
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
			$key = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = assetParamsOutputPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related assetParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinassetParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
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
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related asset table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinasset(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with their assetParams objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinassetParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		assetParamsPeer::addSelectColumns($criteria);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = assetParamsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = assetParamsPeer::getOMClass($row, $startcol);
					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (assetParamsOutput) to $obj2 (assetParams)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with their entry objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
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

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		entryPeer::addSelectColumns($criteria);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (assetParamsOutput) to $obj2 (entry)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
		return $results;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with their asset objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinasset(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		assetPeer::addSelectColumns($criteria);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = assetPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = assetPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = assetPeer::getOMClass($row, $startcol);
					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					assetPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (assetParamsOutput) to $obj2 (asset)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		
		if($criteria instanceof KalturaCriteria)
			$criteria->applyResultsSort($results);
		
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
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
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

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		assetParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		assetPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (assetPeer::NUM_COLUMNS - assetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
        $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined assetParams rows

			$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = assetParamsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = assetParamsPeer::getOMClass($row, $startcol2);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj2 (assetParams)
				$obj2->addassetParamsOutput($obj1);
			} // if joined row not null

			// Add objects for joined entry rows

			$key3 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = entryPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = entryPeer::getOMClass($row, $startcol3);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					entryPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj3 (entry)
				$obj3->addassetParamsOutput($obj1);
			} // if joined row not null

			// Add objects for joined asset rows

			$key4 = assetPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = assetPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$omClass = assetPeer::getOMClass($row, $startcol4);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					assetPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj4 (asset)
				$obj4->addassetParamsOutput($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related assetParams table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptassetParams(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
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
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related asset table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptasset(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(assetParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = assetParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with all related objects except assetParams.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptassetParams(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		assetPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (assetPeer::NUM_COLUMNS - assetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (assetParamsOutput) to the collection in $obj2 (entry)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row is not null

				// Add objects for joined asset rows

				$key3 = assetPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = assetPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = assetPeer::getOMClass($row, $startcol3);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					assetPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj3 (asset)
				$obj3->addassetParamsOutput($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with all related objects except entry.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
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

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		assetParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		assetPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (assetPeer::NUM_COLUMNS - assetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined assetParams rows

				$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = assetParamsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = assetParamsPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj2 (assetParams)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row is not null

				// Add objects for joined asset rows

				$key3 = assetPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = assetPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = assetPeer::getOMClass($row, $startcol3);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					assetPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj3 (asset)
				$obj3->addassetParamsOutput($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of assetParamsOutput objects pre-filled with all related objects except asset.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of assetParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptasset(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		assetParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (assetParamsOutputPeer::NUM_COLUMNS - assetParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		assetParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (assetParamsPeer::NUM_COLUMNS - assetParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(assetParamsOutputPeer::FLAVOR_PARAMS_ID, assetParamsPeer::ID, $join_behavior);

		$criteria->addJoin(assetParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = assetParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = assetParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = assetParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				assetParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined assetParams rows

				$key2 = assetParamsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = assetParamsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = assetParamsPeer::getOMClass($row, $startcol2);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					assetParamsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj2 (assetParams)
				$obj2->addassetParamsOutput($obj1);

			} // if joined row is not null

				// Add objects for joined entry rows

				$key3 = entryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = entryPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = entryPeer::getOMClass($row, $startcol3);
            $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					entryPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (assetParamsOutput) to the collection in $obj3 (entry)
				$obj3->addassetParamsOutput($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseassetParamsOutputPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseassetParamsOutputPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new assetParamsOutputTableMap());
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

			$omClass = $row[$colnum + 37];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a assetParamsOutput or Criteria object.
	 *
	 * @param      mixed $values Criteria or assetParamsOutput object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from assetParamsOutput object
		}

		if ($criteria->containsKey(assetParamsOutputPeer::ID) && $criteria->keyContainsValue(assetParamsOutputPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.assetParamsOutputPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a assetParamsOutput or Criteria object.
	 *
	 * @param      mixed $values Criteria or assetParamsOutput object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(assetParamsOutputPeer::ID);
			$selectCriteria->add(assetParamsOutputPeer::ID, $criteria->remove(assetParamsOutputPeer::ID), $comparison);

		} else { // $values is assetParamsOutput object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}
	
	/**
	 * Return array of columns that should change only if there is a real change.
	 * @return array
	 */
	public static function getAtomicColumns()
	{
		return array();
	}
	
	/**
	 * Return array of custom-data fields that shouldn't be auto-updated.
	 * @return array
	 */
	public static function getAtomicCustomDataFields()
	{
		return array();
	}

	/**
	 * Method to DELETE all rows from the flavor_params_output table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(assetParamsOutputPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			assetParamsOutputPeer::clearInstancePool();
			assetParamsOutputPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a assetParamsOutput or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or assetParamsOutput object or primary key or array of primary keys
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
			$con = Propel::getConnection(assetParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			assetParamsOutputPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof assetParamsOutput) { // it's a model object
			// invalidate the cache for this single object
			assetParamsOutputPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(assetParamsOutputPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				assetParamsOutputPeer::removeInstanceFromPool($singleval);
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
			assetParamsOutputPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given assetParamsOutput object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      assetParamsOutput $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(assetParamsOutput $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(assetParamsOutputPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(assetParamsOutputPeer::TABLE_NAME);

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

		return BasePeer::doValidate(assetParamsOutputPeer::DATABASE_NAME, assetParamsOutputPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     assetParamsOutput
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = assetParamsOutputPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(assetParamsOutputPeer::DATABASE_NAME);
		$criteria->add(assetParamsOutputPeer::ID, $pk);

		$v = assetParamsOutputPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(assetParamsOutputPeer::DATABASE_NAME);
			$criteria->add(assetParamsOutputPeer::ID, $pks, Criteria::IN);
			$objs = assetParamsOutputPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseassetParamsOutputPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseassetParamsOutputPeer::buildTableMap();

