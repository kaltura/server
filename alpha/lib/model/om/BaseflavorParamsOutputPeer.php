<?php

/**
 * Base static class for performing query and update operations on the 'flavor_params_output' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseflavorParamsOutputPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_params_output';

	/** the related Propel class for this table */
	const OM_CLASS = 'flavorParamsOutput';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.flavorParamsOutput';

	/** the related TableMap class for this table */
	const TM_CLASS = 'flavorParamsOutputTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 37;

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

	/**
	 * An identiy map to hold any loaded instances of flavorParamsOutput objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array flavorParamsOutput[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'FlavorParamsId', 'FlavorParamsVersion', 'PartnerId', 'EntryId', 'FlavorAssetId', 'FlavorAssetVersion', 'Name', 'Tags', 'Description', 'ReadyBehavior', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'IsDefault', 'Format', 'VideoCodec', 'VideoBitrate', 'AudioCodec', 'AudioBitrate', 'AudioChannels', 'AudioSampleRate', 'AudioResolution', 'Width', 'Height', 'FrameRate', 'GopSize', 'TwoPass', 'ConversionEngines', 'ConversionEnginesExtraParams', 'CustomData', 'CommandLines', 'FileExt', 'Deinterlice', 'Rotate', 'Operators', 'EngineVersion', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'flavorParamsId', 'flavorParamsVersion', 'partnerId', 'entryId', 'flavorAssetId', 'flavorAssetVersion', 'name', 'tags', 'description', 'readyBehavior', 'createdAt', 'updatedAt', 'deletedAt', 'isDefault', 'format', 'videoCodec', 'videoBitrate', 'audioCodec', 'audioBitrate', 'audioChannels', 'audioSampleRate', 'audioResolution', 'width', 'height', 'frameRate', 'gopSize', 'twoPass', 'conversionEngines', 'conversionEnginesExtraParams', 'customData', 'commandLines', 'fileExt', 'deinterlice', 'rotate', 'operators', 'engineVersion', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::FLAVOR_PARAMS_ID, self::FLAVOR_PARAMS_VERSION, self::PARTNER_ID, self::ENTRY_ID, self::FLAVOR_ASSET_ID, self::FLAVOR_ASSET_VERSION, self::NAME, self::TAGS, self::DESCRIPTION, self::READY_BEHAVIOR, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::IS_DEFAULT, self::FORMAT, self::VIDEO_CODEC, self::VIDEO_BITRATE, self::AUDIO_CODEC, self::AUDIO_BITRATE, self::AUDIO_CHANNELS, self::AUDIO_SAMPLE_RATE, self::AUDIO_RESOLUTION, self::WIDTH, self::HEIGHT, self::FRAME_RATE, self::GOP_SIZE, self::TWO_PASS, self::CONVERSION_ENGINES, self::CONVERSION_ENGINES_EXTRA_PARAMS, self::CUSTOM_DATA, self::COMMAND_LINES, self::FILE_EXT, self::DEINTERLICE, self::ROTATE, self::OPERATORS, self::ENGINE_VERSION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'flavor_params_id', 'flavor_params_version', 'partner_id', 'entry_id', 'flavor_asset_id', 'flavor_asset_version', 'name', 'tags', 'description', 'ready_behavior', 'created_at', 'updated_at', 'deleted_at', 'is_default', 'format', 'video_codec', 'video_bitrate', 'audio_codec', 'audio_bitrate', 'audio_channels', 'audio_sample_rate', 'audio_resolution', 'width', 'height', 'frame_rate', 'gop_size', 'two_pass', 'conversion_engines', 'conversion_engines_extra_params', 'custom_data', 'command_lines', 'file_ext', 'deinterlice', 'rotate', 'operators', 'engine_version', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'FlavorParamsId' => 1, 'FlavorParamsVersion' => 2, 'PartnerId' => 3, 'EntryId' => 4, 'FlavorAssetId' => 5, 'FlavorAssetVersion' => 6, 'Name' => 7, 'Tags' => 8, 'Description' => 9, 'ReadyBehavior' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, 'DeletedAt' => 13, 'IsDefault' => 14, 'Format' => 15, 'VideoCodec' => 16, 'VideoBitrate' => 17, 'AudioCodec' => 18, 'AudioBitrate' => 19, 'AudioChannels' => 20, 'AudioSampleRate' => 21, 'AudioResolution' => 22, 'Width' => 23, 'Height' => 24, 'FrameRate' => 25, 'GopSize' => 26, 'TwoPass' => 27, 'ConversionEngines' => 28, 'ConversionEnginesExtraParams' => 29, 'CustomData' => 30, 'CommandLines' => 31, 'FileExt' => 32, 'Deinterlice' => 33, 'Rotate' => 34, 'Operators' => 35, 'EngineVersion' => 36, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'flavorParamsId' => 1, 'flavorParamsVersion' => 2, 'partnerId' => 3, 'entryId' => 4, 'flavorAssetId' => 5, 'flavorAssetVersion' => 6, 'name' => 7, 'tags' => 8, 'description' => 9, 'readyBehavior' => 10, 'createdAt' => 11, 'updatedAt' => 12, 'deletedAt' => 13, 'isDefault' => 14, 'format' => 15, 'videoCodec' => 16, 'videoBitrate' => 17, 'audioCodec' => 18, 'audioBitrate' => 19, 'audioChannels' => 20, 'audioSampleRate' => 21, 'audioResolution' => 22, 'width' => 23, 'height' => 24, 'frameRate' => 25, 'gopSize' => 26, 'twoPass' => 27, 'conversionEngines' => 28, 'conversionEnginesExtraParams' => 29, 'customData' => 30, 'commandLines' => 31, 'fileExt' => 32, 'deinterlice' => 33, 'rotate' => 34, 'operators' => 35, 'engineVersion' => 36, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::FLAVOR_PARAMS_ID => 1, self::FLAVOR_PARAMS_VERSION => 2, self::PARTNER_ID => 3, self::ENTRY_ID => 4, self::FLAVOR_ASSET_ID => 5, self::FLAVOR_ASSET_VERSION => 6, self::NAME => 7, self::TAGS => 8, self::DESCRIPTION => 9, self::READY_BEHAVIOR => 10, self::CREATED_AT => 11, self::UPDATED_AT => 12, self::DELETED_AT => 13, self::IS_DEFAULT => 14, self::FORMAT => 15, self::VIDEO_CODEC => 16, self::VIDEO_BITRATE => 17, self::AUDIO_CODEC => 18, self::AUDIO_BITRATE => 19, self::AUDIO_CHANNELS => 20, self::AUDIO_SAMPLE_RATE => 21, self::AUDIO_RESOLUTION => 22, self::WIDTH => 23, self::HEIGHT => 24, self::FRAME_RATE => 25, self::GOP_SIZE => 26, self::TWO_PASS => 27, self::CONVERSION_ENGINES => 28, self::CONVERSION_ENGINES_EXTRA_PARAMS => 29, self::CUSTOM_DATA => 30, self::COMMAND_LINES => 31, self::FILE_EXT => 32, self::DEINTERLICE => 33, self::ROTATE => 34, self::OPERATORS => 35, self::ENGINE_VERSION => 36, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'flavor_params_id' => 1, 'flavor_params_version' => 2, 'partner_id' => 3, 'entry_id' => 4, 'flavor_asset_id' => 5, 'flavor_asset_version' => 6, 'name' => 7, 'tags' => 8, 'description' => 9, 'ready_behavior' => 10, 'created_at' => 11, 'updated_at' => 12, 'deleted_at' => 13, 'is_default' => 14, 'format' => 15, 'video_codec' => 16, 'video_bitrate' => 17, 'audio_codec' => 18, 'audio_bitrate' => 19, 'audio_channels' => 20, 'audio_sample_rate' => 21, 'audio_resolution' => 22, 'width' => 23, 'height' => 24, 'frame_rate' => 25, 'gop_size' => 26, 'two_pass' => 27, 'conversion_engines' => 28, 'conversion_engines_extra_params' => 29, 'custom_data' => 30, 'command_lines' => 31, 'file_ext' => 32, 'deinterlice' => 33, 'rotate' => 34, 'operators' => 35, 'engine_version' => 36, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, )
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
	 * @param      string $column The column name for current table. (i.e. flavorParamsOutputPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(flavorParamsOutputPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(flavorParamsOutputPeer::ID);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FLAVOR_PARAMS_ID);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FLAVOR_PARAMS_VERSION);
		$criteria->addSelectColumn(flavorParamsOutputPeer::PARTNER_ID);
		$criteria->addSelectColumn(flavorParamsOutputPeer::ENTRY_ID);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FLAVOR_ASSET_ID);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FLAVOR_ASSET_VERSION);
		$criteria->addSelectColumn(flavorParamsOutputPeer::NAME);
		$criteria->addSelectColumn(flavorParamsOutputPeer::TAGS);
		$criteria->addSelectColumn(flavorParamsOutputPeer::DESCRIPTION);
		$criteria->addSelectColumn(flavorParamsOutputPeer::READY_BEHAVIOR);
		$criteria->addSelectColumn(flavorParamsOutputPeer::CREATED_AT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::UPDATED_AT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::DELETED_AT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::IS_DEFAULT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FORMAT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::VIDEO_CODEC);
		$criteria->addSelectColumn(flavorParamsOutputPeer::VIDEO_BITRATE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::AUDIO_CODEC);
		$criteria->addSelectColumn(flavorParamsOutputPeer::AUDIO_BITRATE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::AUDIO_CHANNELS);
		$criteria->addSelectColumn(flavorParamsOutputPeer::AUDIO_SAMPLE_RATE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::AUDIO_RESOLUTION);
		$criteria->addSelectColumn(flavorParamsOutputPeer::WIDTH);
		$criteria->addSelectColumn(flavorParamsOutputPeer::HEIGHT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FRAME_RATE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::GOP_SIZE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::TWO_PASS);
		$criteria->addSelectColumn(flavorParamsOutputPeer::CONVERSION_ENGINES);
		$criteria->addSelectColumn(flavorParamsOutputPeer::CONVERSION_ENGINES_EXTRA_PARAMS);
		$criteria->addSelectColumn(flavorParamsOutputPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(flavorParamsOutputPeer::COMMAND_LINES);
		$criteria->addSelectColumn(flavorParamsOutputPeer::FILE_EXT);
		$criteria->addSelectColumn(flavorParamsOutputPeer::DEINTERLICE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::ROTATE);
		$criteria->addSelectColumn(flavorParamsOutputPeer::OPERATORS);
		$criteria->addSelectColumn(flavorParamsOutputPeer::ENGINE_VERSION);
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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

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
	 * @return     flavorParamsOutput
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = flavorParamsOutputPeer::doSelect($critcopy, $con);
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
		return flavorParamsOutputPeer::populateObjects(flavorParamsOutputPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(flavorParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = flavorParamsOutputPeer::getCriteriaFilter();
		
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
			flavorParamsOutputPeer::setDefaultCriteriaFilter();
		
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
		flavorParamsOutputPeer::getCriteriaFilter()->applyFilter($criteria);
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
		flavorParamsOutputPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = flavorParamsOutputPeer::alternativeCon ( $con );
		
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
		$con = flavorParamsOutputPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				flavorParamsOutputPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		flavorParamsOutputPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      flavorParamsOutput $value A flavorParamsOutput object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(flavorParamsOutput $obj, $key = null)
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
	 * @param      mixed $value A flavorParamsOutput object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof flavorParamsOutput) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or flavorParamsOutput object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     flavorParamsOutput Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
			$key = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = flavorParamsOutputPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				flavorParamsOutputPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related flavorAsset table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinflavorAsset(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with their flavorParams objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
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

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		flavorParamsPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (flavorParamsOutput) to $obj2 (flavorParams)
				$obj2->addflavorParamsOutput($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with their entry objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
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

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		entryPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (flavorParamsOutput) to $obj2 (entry)
				$obj2->addflavorParamsOutput($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with their flavorAsset objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinflavorAsset(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);
		flavorAssetPeer::addSelectColumns($criteria);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = flavorAssetPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = flavorAssetPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					flavorAssetPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (flavorParamsOutput) to $obj2 (flavorAsset)
				$obj2->addflavorParamsOutput($obj1);

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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
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

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (flavorParamsPeer::NUM_COLUMNS - flavorParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
        $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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
				} // if obj2 loaded

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj2 (flavorParams)
				$obj2->addflavorParamsOutput($obj1);
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

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj3 (entry)
				$obj3->addflavorParamsOutput($obj1);
			} // if joined row not null

			// Add objects for joined flavorAsset rows

			$key4 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = flavorAssetPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = flavorAssetPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					flavorAssetPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj4 (flavorAsset)
				$obj4->addflavorParamsOutput($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

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
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related flavorAsset table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptflavorAsset(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(flavorParamsOutputPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsOutputPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$stmt = flavorParamsOutputPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with all related objects except flavorParams.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
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

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj2 (entry)
				$obj2->addflavorParamsOutput($obj1);

			} // if joined row is not null

				// Add objects for joined flavorAsset rows

				$key3 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = flavorAssetPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = flavorAssetPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					flavorAssetPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj3 (flavorAsset)
				$obj3->addflavorParamsOutput($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with all related objects except entry.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
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

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (flavorParamsPeer::NUM_COLUMNS - flavorParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorAssetPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (flavorAssetPeer::NUM_COLUMNS - flavorAssetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_ASSET_ID, flavorAssetPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj2 (flavorParams)
				$obj2->addflavorParamsOutput($obj1);

			} // if joined row is not null

				// Add objects for joined flavorAsset rows

				$key3 = flavorAssetPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = flavorAssetPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = flavorAssetPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					flavorAssetPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj3 (flavorAsset)
				$obj3->addflavorParamsOutput($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of flavorParamsOutput objects pre-filled with all related objects except flavorAsset.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of flavorParamsOutput objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptflavorAsset(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		flavorParamsOutputPeer::addSelectColumns($criteria);
		$startcol2 = (flavorParamsOutputPeer::NUM_COLUMNS - flavorParamsOutputPeer::NUM_LAZY_LOAD_COLUMNS);

		flavorParamsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (flavorParamsPeer::NUM_COLUMNS - flavorParamsPeer::NUM_LAZY_LOAD_COLUMNS);

		entryPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(flavorParamsOutputPeer::FLAVOR_PARAMS_ID, flavorParamsPeer::ID, $join_behavior);

		$criteria->addJoin(flavorParamsOutputPeer::ENTRY_ID, entryPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = flavorParamsOutputPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = flavorParamsOutputPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = flavorParamsOutputPeer::getOMClass($row, 0);
				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				flavorParamsOutputPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj2 (flavorParams)
				$obj2->addflavorParamsOutput($obj1);

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

				// Add the $obj1 (flavorParamsOutput) to the collection in $obj3 (entry)
				$obj3->addflavorParamsOutput($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseflavorParamsOutputPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseflavorParamsOutputPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new flavorParamsOutputTableMap());
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

			$omClass = $row[$colnum + 15];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a flavorParamsOutput or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParamsOutput object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from flavorParamsOutput object
		}

		if ($criteria->containsKey(flavorParamsOutputPeer::ID) && $criteria->keyContainsValue(flavorParamsOutputPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.flavorParamsOutputPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a flavorParamsOutput or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParamsOutput object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(flavorParamsOutputPeer::ID);
			$selectCriteria->add(flavorParamsOutputPeer::ID, $criteria->remove(flavorParamsOutputPeer::ID), $comparison);

		} else { // $values is flavorParamsOutput object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the flavor_params_output table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(flavorParamsOutputPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			flavorParamsOutputPeer::clearInstancePool();
			flavorParamsOutputPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a flavorParamsOutput or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or flavorParamsOutput object or primary key or array of primary keys
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
			$con = Propel::getConnection(flavorParamsOutputPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			flavorParamsOutputPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof flavorParamsOutput) { // it's a model object
			// invalidate the cache for this single object
			flavorParamsOutputPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(flavorParamsOutputPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				flavorParamsOutputPeer::removeInstanceFromPool($singleval);
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
			flavorParamsOutputPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given flavorParamsOutput object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      flavorParamsOutput $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(flavorParamsOutput $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(flavorParamsOutputPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(flavorParamsOutputPeer::TABLE_NAME);

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

		return BasePeer::doValidate(flavorParamsOutputPeer::DATABASE_NAME, flavorParamsOutputPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     flavorParamsOutput
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = flavorParamsOutputPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(flavorParamsOutputPeer::DATABASE_NAME);
		$criteria->add(flavorParamsOutputPeer::ID, $pk);

		$v = flavorParamsOutputPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(flavorParamsOutputPeer::DATABASE_NAME);
			$criteria->add(flavorParamsOutputPeer::ID, $pks, Criteria::IN);
			$objs = flavorParamsOutputPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseflavorParamsOutputPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseflavorParamsOutputPeer::buildTableMap();

