<?php

/**
 * Base static class for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseassetParamsPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'flavor_params';

	/** the related Propel class for this table */
	const OM_CLASS = 'assetParams';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.assetParams';

	/** the related TableMap class for this table */
	const TM_CLASS = 'assetParamsTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 35;

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

	/** the column name for the SYSTEM_NAME field */
	const SYSTEM_NAME = 'flavor_params.SYSTEM_NAME';

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

	/** the column name for the TYPE field */
	const TYPE = 'flavor_params.TYPE';

	/**
	 * An identiy map to hold any loaded instances of assetParams objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array assetParams[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Version', 'PartnerId', 'Name', 'SystemName', 'Tags', 'Description', 'ReadyBehavior', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'IsDefault', 'Format', 'VideoCodec', 'VideoBitrate', 'AudioCodec', 'AudioBitrate', 'AudioChannels', 'AudioSampleRate', 'AudioResolution', 'Width', 'Height', 'FrameRate', 'GopSize', 'TwoPass', 'ConversionEngines', 'ConversionEnginesExtraParams', 'CustomData', 'ViewOrder', 'CreationMode', 'Deinterlice', 'Rotate', 'Operators', 'EngineVersion', 'Type', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'version', 'partnerId', 'name', 'systemName', 'tags', 'description', 'readyBehavior', 'createdAt', 'updatedAt', 'deletedAt', 'isDefault', 'format', 'videoCodec', 'videoBitrate', 'audioCodec', 'audioBitrate', 'audioChannels', 'audioSampleRate', 'audioResolution', 'width', 'height', 'frameRate', 'gopSize', 'twoPass', 'conversionEngines', 'conversionEnginesExtraParams', 'customData', 'viewOrder', 'creationMode', 'deinterlice', 'rotate', 'operators', 'engineVersion', 'type', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::VERSION, self::PARTNER_ID, self::NAME, self::SYSTEM_NAME, self::TAGS, self::DESCRIPTION, self::READY_BEHAVIOR, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::IS_DEFAULT, self::FORMAT, self::VIDEO_CODEC, self::VIDEO_BITRATE, self::AUDIO_CODEC, self::AUDIO_BITRATE, self::AUDIO_CHANNELS, self::AUDIO_SAMPLE_RATE, self::AUDIO_RESOLUTION, self::WIDTH, self::HEIGHT, self::FRAME_RATE, self::GOP_SIZE, self::TWO_PASS, self::CONVERSION_ENGINES, self::CONVERSION_ENGINES_EXTRA_PARAMS, self::CUSTOM_DATA, self::VIEW_ORDER, self::CREATION_MODE, self::DEINTERLICE, self::ROTATE, self::OPERATORS, self::ENGINE_VERSION, self::TYPE, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'version', 'partner_id', 'name', 'system_name', 'tags', 'description', 'ready_behavior', 'created_at', 'updated_at', 'deleted_at', 'is_default', 'format', 'video_codec', 'video_bitrate', 'audio_codec', 'audio_bitrate', 'audio_channels', 'audio_sample_rate', 'audio_resolution', 'width', 'height', 'frame_rate', 'gop_size', 'two_pass', 'conversion_engines', 'conversion_engines_extra_params', 'custom_data', 'view_order', 'creation_mode', 'deinterlice', 'rotate', 'operators', 'engine_version', 'type', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Version' => 1, 'PartnerId' => 2, 'Name' => 3, 'SystemName' => 4, 'Tags' => 5, 'Description' => 6, 'ReadyBehavior' => 7, 'CreatedAt' => 8, 'UpdatedAt' => 9, 'DeletedAt' => 10, 'IsDefault' => 11, 'Format' => 12, 'VideoCodec' => 13, 'VideoBitrate' => 14, 'AudioCodec' => 15, 'AudioBitrate' => 16, 'AudioChannels' => 17, 'AudioSampleRate' => 18, 'AudioResolution' => 19, 'Width' => 20, 'Height' => 21, 'FrameRate' => 22, 'GopSize' => 23, 'TwoPass' => 24, 'ConversionEngines' => 25, 'ConversionEnginesExtraParams' => 26, 'CustomData' => 27, 'ViewOrder' => 28, 'CreationMode' => 29, 'Deinterlice' => 30, 'Rotate' => 31, 'Operators' => 32, 'EngineVersion' => 33, 'Type' => 34, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'version' => 1, 'partnerId' => 2, 'name' => 3, 'systemName' => 4, 'tags' => 5, 'description' => 6, 'readyBehavior' => 7, 'createdAt' => 8, 'updatedAt' => 9, 'deletedAt' => 10, 'isDefault' => 11, 'format' => 12, 'videoCodec' => 13, 'videoBitrate' => 14, 'audioCodec' => 15, 'audioBitrate' => 16, 'audioChannels' => 17, 'audioSampleRate' => 18, 'audioResolution' => 19, 'width' => 20, 'height' => 21, 'frameRate' => 22, 'gopSize' => 23, 'twoPass' => 24, 'conversionEngines' => 25, 'conversionEnginesExtraParams' => 26, 'customData' => 27, 'viewOrder' => 28, 'creationMode' => 29, 'deinterlice' => 30, 'rotate' => 31, 'operators' => 32, 'engineVersion' => 33, 'type' => 34, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::VERSION => 1, self::PARTNER_ID => 2, self::NAME => 3, self::SYSTEM_NAME => 4, self::TAGS => 5, self::DESCRIPTION => 6, self::READY_BEHAVIOR => 7, self::CREATED_AT => 8, self::UPDATED_AT => 9, self::DELETED_AT => 10, self::IS_DEFAULT => 11, self::FORMAT => 12, self::VIDEO_CODEC => 13, self::VIDEO_BITRATE => 14, self::AUDIO_CODEC => 15, self::AUDIO_BITRATE => 16, self::AUDIO_CHANNELS => 17, self::AUDIO_SAMPLE_RATE => 18, self::AUDIO_RESOLUTION => 19, self::WIDTH => 20, self::HEIGHT => 21, self::FRAME_RATE => 22, self::GOP_SIZE => 23, self::TWO_PASS => 24, self::CONVERSION_ENGINES => 25, self::CONVERSION_ENGINES_EXTRA_PARAMS => 26, self::CUSTOM_DATA => 27, self::VIEW_ORDER => 28, self::CREATION_MODE => 29, self::DEINTERLICE => 30, self::ROTATE => 31, self::OPERATORS => 32, self::ENGINE_VERSION => 33, self::TYPE => 34, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'version' => 1, 'partner_id' => 2, 'name' => 3, 'system_name' => 4, 'tags' => 5, 'description' => 6, 'ready_behavior' => 7, 'created_at' => 8, 'updated_at' => 9, 'deleted_at' => 10, 'is_default' => 11, 'format' => 12, 'video_codec' => 13, 'video_bitrate' => 14, 'audio_codec' => 15, 'audio_bitrate' => 16, 'audio_channels' => 17, 'audio_sample_rate' => 18, 'audio_resolution' => 19, 'width' => 20, 'height' => 21, 'frame_rate' => 22, 'gop_size' => 23, 'two_pass' => 24, 'conversion_engines' => 25, 'conversion_engines_extra_params' => 26, 'custom_data' => 27, 'view_order' => 28, 'creation_mode' => 29, 'deinterlice' => 30, 'rotate' => 31, 'operators' => 32, 'engine_version' => 33, 'type' => 34, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, )
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
	 * @param      string $column The column name for current table. (i.e. assetParamsPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(assetParamsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(assetParamsPeer::ID);
		$criteria->addSelectColumn(assetParamsPeer::VERSION);
		$criteria->addSelectColumn(assetParamsPeer::PARTNER_ID);
		$criteria->addSelectColumn(assetParamsPeer::NAME);
		$criteria->addSelectColumn(assetParamsPeer::SYSTEM_NAME);
		$criteria->addSelectColumn(assetParamsPeer::TAGS);
		$criteria->addSelectColumn(assetParamsPeer::DESCRIPTION);
		$criteria->addSelectColumn(assetParamsPeer::READY_BEHAVIOR);
		$criteria->addSelectColumn(assetParamsPeer::CREATED_AT);
		$criteria->addSelectColumn(assetParamsPeer::UPDATED_AT);
		$criteria->addSelectColumn(assetParamsPeer::DELETED_AT);
		$criteria->addSelectColumn(assetParamsPeer::IS_DEFAULT);
		$criteria->addSelectColumn(assetParamsPeer::FORMAT);
		$criteria->addSelectColumn(assetParamsPeer::VIDEO_CODEC);
		$criteria->addSelectColumn(assetParamsPeer::VIDEO_BITRATE);
		$criteria->addSelectColumn(assetParamsPeer::AUDIO_CODEC);
		$criteria->addSelectColumn(assetParamsPeer::AUDIO_BITRATE);
		$criteria->addSelectColumn(assetParamsPeer::AUDIO_CHANNELS);
		$criteria->addSelectColumn(assetParamsPeer::AUDIO_SAMPLE_RATE);
		$criteria->addSelectColumn(assetParamsPeer::AUDIO_RESOLUTION);
		$criteria->addSelectColumn(assetParamsPeer::WIDTH);
		$criteria->addSelectColumn(assetParamsPeer::HEIGHT);
		$criteria->addSelectColumn(assetParamsPeer::FRAME_RATE);
		$criteria->addSelectColumn(assetParamsPeer::GOP_SIZE);
		$criteria->addSelectColumn(assetParamsPeer::TWO_PASS);
		$criteria->addSelectColumn(assetParamsPeer::CONVERSION_ENGINES);
		$criteria->addSelectColumn(assetParamsPeer::CONVERSION_ENGINES_EXTRA_PARAMS);
		$criteria->addSelectColumn(assetParamsPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(assetParamsPeer::VIEW_ORDER);
		$criteria->addSelectColumn(assetParamsPeer::CREATION_MODE);
		$criteria->addSelectColumn(assetParamsPeer::DEINTERLICE);
		$criteria->addSelectColumn(assetParamsPeer::ROTATE);
		$criteria->addSelectColumn(assetParamsPeer::OPERATORS);
		$criteria->addSelectColumn(assetParamsPeer::ENGINE_VERSION);
		$criteria->addSelectColumn(assetParamsPeer::TYPE);
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
		$criteria->setPrimaryTableName(assetParamsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			assetParamsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = assetParamsPeer::doCountStmt($criteria, $con);

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
	 * @return     assetParams
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = assetParamsPeer::doSelect($critcopy, $con);
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
		return assetParamsPeer::populateObjects(assetParamsPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = assetParamsPeer::getCriteriaFilter();
		
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
			assetParamsPeer::setDefaultCriteriaFilter();
		
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
		assetParamsPeer::getCriteriaFilter()->applyFilter($criteria);
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
		assetParamsPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = assetParamsPeer::alternativeCon ( $con );
		
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
		$con = assetParamsPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				assetParamsPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			assetParamsPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		assetParamsPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      assetParams $value A assetParams object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(assetParams $obj, $key = null)
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
	 * @param      mixed $value A assetParams object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof assetParams) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or assetParams object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     assetParams Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
			$key = assetParamsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = assetParamsPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				// class must be set each time from the record row
				$cls = assetParamsPeer::getOMClass($row, 0);
				$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				assetParamsPeer::addInstanceToPool($obj, $key);
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
	  $dbMap = Propel::getDatabaseMap(BaseassetParamsPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseassetParamsPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new assetParamsTableMap());
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

			$omClass = $row[$colnum + 34];
			$omClass = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

		} catch (Exception $e) {
			throw new PropelException('Unable to get OM class.', $e);
		}
		return $omClass;
	}

	/**
	 * Method perform an INSERT on the database, given a assetParams or Criteria object.
	 *
	 * @param      mixed $values Criteria or assetParams object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from assetParams object
		}

		if ($criteria->containsKey(assetParamsPeer::ID) && $criteria->keyContainsValue(assetParamsPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.assetParamsPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a assetParams or Criteria object.
	 *
	 * @param      mixed $values Criteria or assetParams object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(assetParamsPeer::ID);
			$selectCriteria->add(assetParamsPeer::ID, $criteria->remove(assetParamsPeer::ID), $comparison);

		} else { // $values is assetParams object
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
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(assetParamsPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			assetParamsPeer::clearInstancePool();
			assetParamsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a assetParams or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or assetParams object or primary key or array of primary keys
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
			$con = Propel::getConnection(assetParamsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			assetParamsPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof assetParams) { // it's a model object
			// invalidate the cache for this single object
			assetParamsPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(assetParamsPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				assetParamsPeer::removeInstanceFromPool($singleval);
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
			assetParamsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given assetParams object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      assetParams $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(assetParams $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(assetParamsPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(assetParamsPeer::TABLE_NAME);

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

		return BasePeer::doValidate(assetParamsPeer::DATABASE_NAME, assetParamsPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     assetParams
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = assetParamsPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
		$criteria->add(assetParamsPeer::ID, $pk);

		$v = assetParamsPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(assetParamsPeer::DATABASE_NAME);
			$criteria->add(assetParamsPeer::ID, $pks, Criteria::IN);
			$objs = assetParamsPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseassetParamsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseassetParamsPeer::buildTableMap();

