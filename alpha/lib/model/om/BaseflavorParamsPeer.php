<?php

/**
 * Base static class for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseflavorParamsPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_params';

	/** the related Propel class for this table */
	const OM_CLASS = 'flavorParams';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.flavorParams';

	/** the related TableMap class for this table */
	const TM_CLASS = 'flavorParamsTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 33;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'flavor_params.ID';

	/** the column name for the VERSION field */
	const VERSION = 'flavor_params.VERSION';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'flavor_params.PARTNER_ID';

	/** the column name for the NAME field */
	const NAME = 'flavor_params.NAME';

	/** the column name for the TAGS field */
	const TAGS = 'flavor_params.TAGS';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'flavor_params.DESCRIPTION';

	/** the column name for the READY_BEHAVIOR field */
	const READY_BEHAVIOR = 'flavor_params.READY_BEHAVIOR';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'flavor_params.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'flavor_params.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'flavor_params.DELETED_AT';

	/** the column name for the IS_DEFAULT field */
	const IS_DEFAULT = 'flavor_params.IS_DEFAULT';

	/** the column name for the FORMAT field */
	const FORMAT = 'flavor_params.FORMAT';

	/** the column name for the VIDEO_CODEC field */
	const VIDEO_CODEC = 'flavor_params.VIDEO_CODEC';

	/** the column name for the VIDEO_BITRATE field */
	const VIDEO_BITRATE = 'flavor_params.VIDEO_BITRATE';

	/** the column name for the AUDIO_CODEC field */
	const AUDIO_CODEC = 'flavor_params.AUDIO_CODEC';

	/** the column name for the AUDIO_BITRATE field */
	const AUDIO_BITRATE = 'flavor_params.AUDIO_BITRATE';

	/** the column name for the AUDIO_CHANNELS field */
	const AUDIO_CHANNELS = 'flavor_params.AUDIO_CHANNELS';

	/** the column name for the AUDIO_SAMPLE_RATE field */
	const AUDIO_SAMPLE_RATE = 'flavor_params.AUDIO_SAMPLE_RATE';

	/** the column name for the AUDIO_RESOLUTION field */
	const AUDIO_RESOLUTION = 'flavor_params.AUDIO_RESOLUTION';

	/** the column name for the WIDTH field */
	const WIDTH = 'flavor_params.WIDTH';

	/** the column name for the HEIGHT field */
	const HEIGHT = 'flavor_params.HEIGHT';

	/** the column name for the FRAME_RATE field */
	const FRAME_RATE = 'flavor_params.FRAME_RATE';

	/** the column name for the GOP_SIZE field */
	const GOP_SIZE = 'flavor_params.GOP_SIZE';

	/** the column name for the TWO_PASS field */
	const TWO_PASS = 'flavor_params.TWO_PASS';

	/** the column name for the CONVERSION_ENGINES field */
	const CONVERSION_ENGINES = 'flavor_params.CONVERSION_ENGINES';

	/** the column name for the CONVERSION_ENGINES_EXTRA_PARAMS field */
	const CONVERSION_ENGINES_EXTRA_PARAMS = 'flavor_params.CONVERSION_ENGINES_EXTRA_PARAMS';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'flavor_params.CUSTOM_DATA';

	/** the column name for the VIEW_ORDER field */
	const VIEW_ORDER = 'flavor_params.VIEW_ORDER';

	/** the column name for the CREATION_MODE field */
	const CREATION_MODE = 'flavor_params.CREATION_MODE';

	/** the column name for the DEINTERLICE field */
	const DEINTERLICE = 'flavor_params.DEINTERLICE';

	/** the column name for the ROTATE field */
	const ROTATE = 'flavor_params.ROTATE';

	/** the column name for the OPERATORS field */
	const OPERATORS = 'flavor_params.OPERATORS';

	/** the column name for the ENGINE_VERSION field */
	const ENGINE_VERSION = 'flavor_params.ENGINE_VERSION';

	/**
	 * An identiy map to hold any loaded instances of flavorParams objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array flavorParams[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Version', 'PartnerId', 'Name', 'Tags', 'Description', 'ReadyBehavior', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'IsDefault', 'Format', 'VideoCodec', 'VideoBitrate', 'AudioCodec', 'AudioBitrate', 'AudioChannels', 'AudioSampleRate', 'AudioResolution', 'Width', 'Height', 'FrameRate', 'GopSize', 'TwoPass', 'ConversionEngines', 'ConversionEnginesExtraParams', 'CustomData', 'ViewOrder', 'CreationMode', 'Deinterlice', 'Rotate', 'Operators', 'EngineVersion', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'version', 'partnerId', 'name', 'tags', 'description', 'readyBehavior', 'createdAt', 'updatedAt', 'deletedAt', 'isDefault', 'format', 'videoCodec', 'videoBitrate', 'audioCodec', 'audioBitrate', 'audioChannels', 'audioSampleRate', 'audioResolution', 'width', 'height', 'frameRate', 'gopSize', 'twoPass', 'conversionEngines', 'conversionEnginesExtraParams', 'customData', 'viewOrder', 'creationMode', 'deinterlice', 'rotate', 'operators', 'engineVersion', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::VERSION, self::PARTNER_ID, self::NAME, self::TAGS, self::DESCRIPTION, self::READY_BEHAVIOR, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::IS_DEFAULT, self::FORMAT, self::VIDEO_CODEC, self::VIDEO_BITRATE, self::AUDIO_CODEC, self::AUDIO_BITRATE, self::AUDIO_CHANNELS, self::AUDIO_SAMPLE_RATE, self::AUDIO_RESOLUTION, self::WIDTH, self::HEIGHT, self::FRAME_RATE, self::GOP_SIZE, self::TWO_PASS, self::CONVERSION_ENGINES, self::CONVERSION_ENGINES_EXTRA_PARAMS, self::CUSTOM_DATA, self::VIEW_ORDER, self::CREATION_MODE, self::DEINTERLICE, self::ROTATE, self::OPERATORS, self::ENGINE_VERSION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'version', 'partner_id', 'name', 'tags', 'description', 'ready_behavior', 'created_at', 'updated_at', 'deleted_at', 'is_default', 'format', 'video_codec', 'video_bitrate', 'audio_codec', 'audio_bitrate', 'audio_channels', 'audio_sample_rate', 'audio_resolution', 'width', 'height', 'frame_rate', 'gop_size', 'two_pass', 'conversion_engines', 'conversion_engines_extra_params', 'custom_data', 'view_order', 'creation_mode', 'deinterlice', 'rotate', 'operators', 'engine_version', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Version' => 1, 'PartnerId' => 2, 'Name' => 3, 'Tags' => 4, 'Description' => 5, 'ReadyBehavior' => 6, 'CreatedAt' => 7, 'UpdatedAt' => 8, 'DeletedAt' => 9, 'IsDefault' => 10, 'Format' => 11, 'VideoCodec' => 12, 'VideoBitrate' => 13, 'AudioCodec' => 14, 'AudioBitrate' => 15, 'AudioChannels' => 16, 'AudioSampleRate' => 17, 'AudioResolution' => 18, 'Width' => 19, 'Height' => 20, 'FrameRate' => 21, 'GopSize' => 22, 'TwoPass' => 23, 'ConversionEngines' => 24, 'ConversionEnginesExtraParams' => 25, 'CustomData' => 26, 'ViewOrder' => 27, 'CreationMode' => 28, 'Deinterlice' => 29, 'Rotate' => 30, 'Operators' => 31, 'EngineVersion' => 32, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'version' => 1, 'partnerId' => 2, 'name' => 3, 'tags' => 4, 'description' => 5, 'readyBehavior' => 6, 'createdAt' => 7, 'updatedAt' => 8, 'deletedAt' => 9, 'isDefault' => 10, 'format' => 11, 'videoCodec' => 12, 'videoBitrate' => 13, 'audioCodec' => 14, 'audioBitrate' => 15, 'audioChannels' => 16, 'audioSampleRate' => 17, 'audioResolution' => 18, 'width' => 19, 'height' => 20, 'frameRate' => 21, 'gopSize' => 22, 'twoPass' => 23, 'conversionEngines' => 24, 'conversionEnginesExtraParams' => 25, 'customData' => 26, 'viewOrder' => 27, 'creationMode' => 28, 'deinterlice' => 29, 'rotate' => 30, 'operators' => 31, 'engineVersion' => 32, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::VERSION => 1, self::PARTNER_ID => 2, self::NAME => 3, self::TAGS => 4, self::DESCRIPTION => 5, self::READY_BEHAVIOR => 6, self::CREATED_AT => 7, self::UPDATED_AT => 8, self::DELETED_AT => 9, self::IS_DEFAULT => 10, self::FORMAT => 11, self::VIDEO_CODEC => 12, self::VIDEO_BITRATE => 13, self::AUDIO_CODEC => 14, self::AUDIO_BITRATE => 15, self::AUDIO_CHANNELS => 16, self::AUDIO_SAMPLE_RATE => 17, self::AUDIO_RESOLUTION => 18, self::WIDTH => 19, self::HEIGHT => 20, self::FRAME_RATE => 21, self::GOP_SIZE => 22, self::TWO_PASS => 23, self::CONVERSION_ENGINES => 24, self::CONVERSION_ENGINES_EXTRA_PARAMS => 25, self::CUSTOM_DATA => 26, self::VIEW_ORDER => 27, self::CREATION_MODE => 28, self::DEINTERLICE => 29, self::ROTATE => 30, self::OPERATORS => 31, self::ENGINE_VERSION => 32, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'version' => 1, 'partner_id' => 2, 'name' => 3, 'tags' => 4, 'description' => 5, 'ready_behavior' => 6, 'created_at' => 7, 'updated_at' => 8, 'deleted_at' => 9, 'is_default' => 10, 'format' => 11, 'video_codec' => 12, 'video_bitrate' => 13, 'audio_codec' => 14, 'audio_bitrate' => 15, 'audio_channels' => 16, 'audio_sample_rate' => 17, 'audio_resolution' => 18, 'width' => 19, 'height' => 20, 'frame_rate' => 21, 'gop_size' => 22, 'two_pass' => 23, 'conversion_engines' => 24, 'conversion_engines_extra_params' => 25, 'custom_data' => 26, 'view_order' => 27, 'creation_mode' => 28, 'deinterlice' => 29, 'rotate' => 30, 'operators' => 31, 'engine_version' => 32, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
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
	 * @param      string $column The column name for current table. (i.e. flavorParamsPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(flavorParamsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(flavorParamsPeer::ID);
		$criteria->addSelectColumn(flavorParamsPeer::VERSION);
		$criteria->addSelectColumn(flavorParamsPeer::PARTNER_ID);
		$criteria->addSelectColumn(flavorParamsPeer::NAME);
		$criteria->addSelectColumn(flavorParamsPeer::TAGS);
		$criteria->addSelectColumn(flavorParamsPeer::DESCRIPTION);
		$criteria->addSelectColumn(flavorParamsPeer::READY_BEHAVIOR);
		$criteria->addSelectColumn(flavorParamsPeer::CREATED_AT);
		$criteria->addSelectColumn(flavorParamsPeer::UPDATED_AT);
		$criteria->addSelectColumn(flavorParamsPeer::DELETED_AT);
		$criteria->addSelectColumn(flavorParamsPeer::IS_DEFAULT);
		$criteria->addSelectColumn(flavorParamsPeer::FORMAT);
		$criteria->addSelectColumn(flavorParamsPeer::VIDEO_CODEC);
		$criteria->addSelectColumn(flavorParamsPeer::VIDEO_BITRATE);
		$criteria->addSelectColumn(flavorParamsPeer::AUDIO_CODEC);
		$criteria->addSelectColumn(flavorParamsPeer::AUDIO_BITRATE);
		$criteria->addSelectColumn(flavorParamsPeer::AUDIO_CHANNELS);
		$criteria->addSelectColumn(flavorParamsPeer::AUDIO_SAMPLE_RATE);
		$criteria->addSelectColumn(flavorParamsPeer::AUDIO_RESOLUTION);
		$criteria->addSelectColumn(flavorParamsPeer::WIDTH);
		$criteria->addSelectColumn(flavorParamsPeer::HEIGHT);
		$criteria->addSelectColumn(flavorParamsPeer::FRAME_RATE);
		$criteria->addSelectColumn(flavorParamsPeer::GOP_SIZE);
		$criteria->addSelectColumn(flavorParamsPeer::TWO_PASS);
		$criteria->addSelectColumn(flavorParamsPeer::CONVERSION_ENGINES);
		$criteria->addSelectColumn(flavorParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS);
		$criteria->addSelectColumn(flavorParamsPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(flavorParamsPeer::VIEW_ORDER);
		$criteria->addSelectColumn(flavorParamsPeer::CREATION_MODE);
		$criteria->addSelectColumn(flavorParamsPeer::DEINTERLICE);
		$criteria->addSelectColumn(flavorParamsPeer::ROTATE);
		$criteria->addSelectColumn(flavorParamsPeer::OPERATORS);
		$criteria->addSelectColumn(flavorParamsPeer::ENGINE_VERSION);
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
		$criteria->setPrimaryTableName(flavorParamsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			flavorParamsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = flavorParamsPeer::doCountStmt($criteria, $con);

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
	 * @return     flavorParams
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = flavorParamsPeer::doSelect($critcopy, $con);
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
		return flavorParamsPeer::populateObjects(flavorParamsPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(flavorParamsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = flavorParamsPeer::getCriteriaFilter();
		
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
			flavorParamsPeer::setDefaultCriteriaFilter();
		
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
		flavorParamsPeer::getCriteriaFilter()->applyFilter($criteria);
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
		flavorParamsPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = flavorParamsPeer::alternativeCon ( $con );
		
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
		$con = flavorParamsPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				flavorParamsPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			flavorParamsPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		flavorParamsPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      flavorParams $value A flavorParams object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(flavorParams $obj, $key = null)
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
	 * @param      mixed $value A flavorParams object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof flavorParams) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or flavorParams object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     flavorParams Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to flavor_params
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
			$key = flavorParamsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = flavorParamsPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = flavorParamsPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				flavorParamsPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseflavorParamsPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseflavorParamsPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new flavorParamsTableMap());
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

			$omClass = $row[$colnum + 11];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a flavorParams or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParams object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from flavorParams object
		}

		if ($criteria->containsKey(flavorParamsPeer::ID) && $criteria->keyContainsValue(flavorParamsPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.flavorParamsPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a flavorParams or Criteria object.
	 *
	 * @param      mixed $values Criteria or flavorParams object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(flavorParamsPeer::ID);
			$selectCriteria->add(flavorParamsPeer::ID, $criteria->remove(flavorParamsPeer::ID), $comparison);

		} else { // $values is flavorParams object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the flavor_params table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(flavorParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(flavorParamsPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			flavorParamsPeer::clearInstancePool();
			flavorParamsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a flavorParams or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or flavorParams object or primary key or array of primary keys
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
			$con = Propel::getConnection(flavorParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			flavorParamsPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof flavorParams) { // it's a model object
			// invalidate the cache for this single object
			flavorParamsPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(flavorParamsPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				flavorParamsPeer::removeInstanceFromPool($singleval);
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
			flavorParamsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given flavorParams object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      flavorParams $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(flavorParams $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(flavorParamsPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(flavorParamsPeer::TABLE_NAME);

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

		return BasePeer::doValidate(flavorParamsPeer::DATABASE_NAME, flavorParamsPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     flavorParams
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = flavorParamsPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(flavorParamsPeer::DATABASE_NAME);
		$criteria->add(flavorParamsPeer::ID, $pk);

		$v = flavorParamsPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(flavorParamsPeer::DATABASE_NAME);
			$criteria->add(flavorParamsPeer::ID, $pks, Criteria::IN);
			$objs = flavorParamsPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseflavorParamsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseflavorParamsPeer::buildTableMap();

