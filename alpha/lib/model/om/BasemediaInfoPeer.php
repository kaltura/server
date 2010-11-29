<?php

/**
 * Base static class for performing query and update operations on the 'media_info' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BasemediaInfoPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'media_info';

	/** the related Propel class for this table */
	const OM_CLASS = 'mediaInfo';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.mediaInfo';

	/** the related TableMap class for this table */
	const TM_CLASS = 'mediaInfoTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 35;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'media_info.ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'media_info.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'media_info.UPDATED_AT';

	/** the column name for the FLAVOR_ASSET_ID field */
	const FLAVOR_ASSET_ID = 'media_info.FLAVOR_ASSET_ID';

	/** the column name for the FILE_SIZE field */
	const FILE_SIZE = 'media_info.FILE_SIZE';

	/** the column name for the CONTAINER_FORMAT field */
	const CONTAINER_FORMAT = 'media_info.CONTAINER_FORMAT';

	/** the column name for the CONTAINER_ID field */
	const CONTAINER_ID = 'media_info.CONTAINER_ID';

	/** the column name for the CONTAINER_PROFILE field */
	const CONTAINER_PROFILE = 'media_info.CONTAINER_PROFILE';

	/** the column name for the CONTAINER_DURATION field */
	const CONTAINER_DURATION = 'media_info.CONTAINER_DURATION';

	/** the column name for the CONTAINER_BIT_RATE field */
	const CONTAINER_BIT_RATE = 'media_info.CONTAINER_BIT_RATE';

	/** the column name for the VIDEO_FORMAT field */
	const VIDEO_FORMAT = 'media_info.VIDEO_FORMAT';

	/** the column name for the VIDEO_CODEC_ID field */
	const VIDEO_CODEC_ID = 'media_info.VIDEO_CODEC_ID';

	/** the column name for the VIDEO_DURATION field */
	const VIDEO_DURATION = 'media_info.VIDEO_DURATION';

	/** the column name for the VIDEO_BIT_RATE field */
	const VIDEO_BIT_RATE = 'media_info.VIDEO_BIT_RATE';

	/** the column name for the VIDEO_BIT_RATE_MODE field */
	const VIDEO_BIT_RATE_MODE = 'media_info.VIDEO_BIT_RATE_MODE';

	/** the column name for the VIDEO_WIDTH field */
	const VIDEO_WIDTH = 'media_info.VIDEO_WIDTH';

	/** the column name for the VIDEO_HEIGHT field */
	const VIDEO_HEIGHT = 'media_info.VIDEO_HEIGHT';

	/** the column name for the VIDEO_FRAME_RATE field */
	const VIDEO_FRAME_RATE = 'media_info.VIDEO_FRAME_RATE';

	/** the column name for the VIDEO_DAR field */
	const VIDEO_DAR = 'media_info.VIDEO_DAR';

	/** the column name for the VIDEO_ROTATION field */
	const VIDEO_ROTATION = 'media_info.VIDEO_ROTATION';

	/** the column name for the AUDIO_FORMAT field */
	const AUDIO_FORMAT = 'media_info.AUDIO_FORMAT';

	/** the column name for the AUDIO_CODEC_ID field */
	const AUDIO_CODEC_ID = 'media_info.AUDIO_CODEC_ID';

	/** the column name for the AUDIO_DURATION field */
	const AUDIO_DURATION = 'media_info.AUDIO_DURATION';

	/** the column name for the AUDIO_BIT_RATE field */
	const AUDIO_BIT_RATE = 'media_info.AUDIO_BIT_RATE';

	/** the column name for the AUDIO_BIT_RATE_MODE field */
	const AUDIO_BIT_RATE_MODE = 'media_info.AUDIO_BIT_RATE_MODE';

	/** the column name for the AUDIO_CHANNELS field */
	const AUDIO_CHANNELS = 'media_info.AUDIO_CHANNELS';

	/** the column name for the AUDIO_SAMPLING_RATE field */
	const AUDIO_SAMPLING_RATE = 'media_info.AUDIO_SAMPLING_RATE';

	/** the column name for the AUDIO_RESOLUTION field */
	const AUDIO_RESOLUTION = 'media_info.AUDIO_RESOLUTION';

	/** the column name for the WRITING_LIB field */
	const WRITING_LIB = 'media_info.WRITING_LIB';

	/** the column name for the CUSTOM_DATA field */
	const CUSTOM_DATA = 'media_info.CUSTOM_DATA';

	/** the column name for the RAW_DATA field */
	const RAW_DATA = 'media_info.RAW_DATA';

	/** the column name for the MULTI_STREAM_INFO field */
	const MULTI_STREAM_INFO = 'media_info.MULTI_STREAM_INFO';

	/** the column name for the FLAVOR_ASSET_VERSION field */
	const FLAVOR_ASSET_VERSION = 'media_info.FLAVOR_ASSET_VERSION';

	/** the column name for the SCAN_TYPE field */
	const SCAN_TYPE = 'media_info.SCAN_TYPE';

	/** the column name for the MULTI_STREAM field */
	const MULTI_STREAM = 'media_info.MULTI_STREAM';

	/**
	 * An identiy map to hold any loaded instances of mediaInfo objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array mediaInfo[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedAt', 'UpdatedAt', 'FlavorAssetId', 'FileSize', 'ContainerFormat', 'ContainerId', 'ContainerProfile', 'ContainerDuration', 'ContainerBitRate', 'VideoFormat', 'VideoCodecId', 'VideoDuration', 'VideoBitRate', 'VideoBitRateMode', 'VideoWidth', 'VideoHeight', 'VideoFrameRate', 'VideoDar', 'VideoRotation', 'AudioFormat', 'AudioCodecId', 'AudioDuration', 'AudioBitRate', 'AudioBitRateMode', 'AudioChannels', 'AudioSamplingRate', 'AudioResolution', 'WritingLib', 'CustomData', 'RawData', 'MultiStreamInfo', 'FlavorAssetVersion', 'ScanType', 'MultiStream', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'createdAt', 'updatedAt', 'flavorAssetId', 'fileSize', 'containerFormat', 'containerId', 'containerProfile', 'containerDuration', 'containerBitRate', 'videoFormat', 'videoCodecId', 'videoDuration', 'videoBitRate', 'videoBitRateMode', 'videoWidth', 'videoHeight', 'videoFrameRate', 'videoDar', 'videoRotation', 'audioFormat', 'audioCodecId', 'audioDuration', 'audioBitRate', 'audioBitRateMode', 'audioChannels', 'audioSamplingRate', 'audioResolution', 'writingLib', 'customData', 'rawData', 'multiStreamInfo', 'flavorAssetVersion', 'scanType', 'multiStream', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CREATED_AT, self::UPDATED_AT, self::FLAVOR_ASSET_ID, self::FILE_SIZE, self::CONTAINER_FORMAT, self::CONTAINER_ID, self::CONTAINER_PROFILE, self::CONTAINER_DURATION, self::CONTAINER_BIT_RATE, self::VIDEO_FORMAT, self::VIDEO_CODEC_ID, self::VIDEO_DURATION, self::VIDEO_BIT_RATE, self::VIDEO_BIT_RATE_MODE, self::VIDEO_WIDTH, self::VIDEO_HEIGHT, self::VIDEO_FRAME_RATE, self::VIDEO_DAR, self::VIDEO_ROTATION, self::AUDIO_FORMAT, self::AUDIO_CODEC_ID, self::AUDIO_DURATION, self::AUDIO_BIT_RATE, self::AUDIO_BIT_RATE_MODE, self::AUDIO_CHANNELS, self::AUDIO_SAMPLING_RATE, self::AUDIO_RESOLUTION, self::WRITING_LIB, self::CUSTOM_DATA, self::RAW_DATA, self::MULTI_STREAM_INFO, self::FLAVOR_ASSET_VERSION, self::SCAN_TYPE, self::MULTI_STREAM, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'created_at', 'updated_at', 'flavor_asset_id', 'file_size', 'container_format', 'container_id', 'container_profile', 'container_duration', 'container_bit_rate', 'video_format', 'video_codec_id', 'video_duration', 'video_bit_rate', 'video_bit_rate_mode', 'video_width', 'video_height', 'video_frame_rate', 'video_dar', 'video_rotation', 'audio_format', 'audio_codec_id', 'audio_duration', 'audio_bit_rate', 'audio_bit_rate_mode', 'audio_channels', 'audio_sampling_rate', 'audio_resolution', 'writing_lib', 'custom_data', 'raw_data', 'multi_stream_info', 'flavor_asset_version', 'scan_type', 'multi_stream', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedAt' => 1, 'UpdatedAt' => 2, 'FlavorAssetId' => 3, 'FileSize' => 4, 'ContainerFormat' => 5, 'ContainerId' => 6, 'ContainerProfile' => 7, 'ContainerDuration' => 8, 'ContainerBitRate' => 9, 'VideoFormat' => 10, 'VideoCodecId' => 11, 'VideoDuration' => 12, 'VideoBitRate' => 13, 'VideoBitRateMode' => 14, 'VideoWidth' => 15, 'VideoHeight' => 16, 'VideoFrameRate' => 17, 'VideoDar' => 18, 'VideoRotation' => 19, 'AudioFormat' => 20, 'AudioCodecId' => 21, 'AudioDuration' => 22, 'AudioBitRate' => 23, 'AudioBitRateMode' => 24, 'AudioChannels' => 25, 'AudioSamplingRate' => 26, 'AudioResolution' => 27, 'WritingLib' => 28, 'CustomData' => 29, 'RawData' => 30, 'MultiStreamInfo' => 31, 'FlavorAssetVersion' => 32, 'ScanType' => 33, 'MultiStream' => 34, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'createdAt' => 1, 'updatedAt' => 2, 'flavorAssetId' => 3, 'fileSize' => 4, 'containerFormat' => 5, 'containerId' => 6, 'containerProfile' => 7, 'containerDuration' => 8, 'containerBitRate' => 9, 'videoFormat' => 10, 'videoCodecId' => 11, 'videoDuration' => 12, 'videoBitRate' => 13, 'videoBitRateMode' => 14, 'videoWidth' => 15, 'videoHeight' => 16, 'videoFrameRate' => 17, 'videoDar' => 18, 'videoRotation' => 19, 'audioFormat' => 20, 'audioCodecId' => 21, 'audioDuration' => 22, 'audioBitRate' => 23, 'audioBitRateMode' => 24, 'audioChannels' => 25, 'audioSamplingRate' => 26, 'audioResolution' => 27, 'writingLib' => 28, 'customData' => 29, 'rawData' => 30, 'multiStreamInfo' => 31, 'flavorAssetVersion' => 32, 'scanType' => 33, 'multiStream' => 34, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CREATED_AT => 1, self::UPDATED_AT => 2, self::FLAVOR_ASSET_ID => 3, self::FILE_SIZE => 4, self::CONTAINER_FORMAT => 5, self::CONTAINER_ID => 6, self::CONTAINER_PROFILE => 7, self::CONTAINER_DURATION => 8, self::CONTAINER_BIT_RATE => 9, self::VIDEO_FORMAT => 10, self::VIDEO_CODEC_ID => 11, self::VIDEO_DURATION => 12, self::VIDEO_BIT_RATE => 13, self::VIDEO_BIT_RATE_MODE => 14, self::VIDEO_WIDTH => 15, self::VIDEO_HEIGHT => 16, self::VIDEO_FRAME_RATE => 17, self::VIDEO_DAR => 18, self::VIDEO_ROTATION => 19, self::AUDIO_FORMAT => 20, self::AUDIO_CODEC_ID => 21, self::AUDIO_DURATION => 22, self::AUDIO_BIT_RATE => 23, self::AUDIO_BIT_RATE_MODE => 24, self::AUDIO_CHANNELS => 25, self::AUDIO_SAMPLING_RATE => 26, self::AUDIO_RESOLUTION => 27, self::WRITING_LIB => 28, self::CUSTOM_DATA => 29, self::RAW_DATA => 30, self::MULTI_STREAM_INFO => 31, self::FLAVOR_ASSET_VERSION => 32, self::SCAN_TYPE => 33, self::MULTI_STREAM => 34, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'created_at' => 1, 'updated_at' => 2, 'flavor_asset_id' => 3, 'file_size' => 4, 'container_format' => 5, 'container_id' => 6, 'container_profile' => 7, 'container_duration' => 8, 'container_bit_rate' => 9, 'video_format' => 10, 'video_codec_id' => 11, 'video_duration' => 12, 'video_bit_rate' => 13, 'video_bit_rate_mode' => 14, 'video_width' => 15, 'video_height' => 16, 'video_frame_rate' => 17, 'video_dar' => 18, 'video_rotation' => 19, 'audio_format' => 20, 'audio_codec_id' => 21, 'audio_duration' => 22, 'audio_bit_rate' => 23, 'audio_bit_rate_mode' => 24, 'audio_channels' => 25, 'audio_sampling_rate' => 26, 'audio_resolution' => 27, 'writing_lib' => 28, 'custom_data' => 29, 'raw_data' => 30, 'multi_stream_info' => 31, 'flavor_asset_version' => 32, 'scan_type' => 33, 'multi_stream' => 34, ),
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
	 * @param      string $column The column name for current table. (i.e. mediaInfoPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(mediaInfoPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(mediaInfoPeer::ID);
		$criteria->addSelectColumn(mediaInfoPeer::CREATED_AT);
		$criteria->addSelectColumn(mediaInfoPeer::UPDATED_AT);
		$criteria->addSelectColumn(mediaInfoPeer::FLAVOR_ASSET_ID);
		$criteria->addSelectColumn(mediaInfoPeer::FILE_SIZE);
		$criteria->addSelectColumn(mediaInfoPeer::CONTAINER_FORMAT);
		$criteria->addSelectColumn(mediaInfoPeer::CONTAINER_ID);
		$criteria->addSelectColumn(mediaInfoPeer::CONTAINER_PROFILE);
		$criteria->addSelectColumn(mediaInfoPeer::CONTAINER_DURATION);
		$criteria->addSelectColumn(mediaInfoPeer::CONTAINER_BIT_RATE);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_FORMAT);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_CODEC_ID);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_DURATION);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_BIT_RATE);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_BIT_RATE_MODE);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_WIDTH);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_HEIGHT);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_FRAME_RATE);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_DAR);
		$criteria->addSelectColumn(mediaInfoPeer::VIDEO_ROTATION);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_FORMAT);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_CODEC_ID);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_DURATION);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_BIT_RATE);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_BIT_RATE_MODE);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_CHANNELS);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_SAMPLING_RATE);
		$criteria->addSelectColumn(mediaInfoPeer::AUDIO_RESOLUTION);
		$criteria->addSelectColumn(mediaInfoPeer::WRITING_LIB);
		$criteria->addSelectColumn(mediaInfoPeer::CUSTOM_DATA);
		$criteria->addSelectColumn(mediaInfoPeer::RAW_DATA);
		$criteria->addSelectColumn(mediaInfoPeer::MULTI_STREAM_INFO);
		$criteria->addSelectColumn(mediaInfoPeer::FLAVOR_ASSET_VERSION);
		$criteria->addSelectColumn(mediaInfoPeer::SCAN_TYPE);
		$criteria->addSelectColumn(mediaInfoPeer::MULTI_STREAM);
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
		$criteria->setPrimaryTableName(mediaInfoPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			mediaInfoPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = mediaInfoPeer::doCountStmt($criteria, $con);

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
	 * @return     mediaInfo
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = mediaInfoPeer::doSelect($critcopy, $con);
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
		return mediaInfoPeer::populateObjects(mediaInfoPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = mediaInfoPeer::getCriteriaFilter();
		
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
			mediaInfoPeer::setDefaultCriteriaFilter();
		
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
		mediaInfoPeer::getCriteriaFilter()->applyFilter($criteria);
	}
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
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
		mediaInfoPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = mediaInfoPeer::alternativeCon ( $con );
		
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
		$con = mediaInfoPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				mediaInfoPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			mediaInfoPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		mediaInfoPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      mediaInfo $value A mediaInfo object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(mediaInfo $obj, $key = null)
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
	 * @param      mixed $value A mediaInfo object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof mediaInfo) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or mediaInfo object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     mediaInfo Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to media_info
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
		$cls = mediaInfoPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = mediaInfoPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = mediaInfoPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				mediaInfoPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(mediaInfoPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			mediaInfoPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(mediaInfoPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = mediaInfoPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of mediaInfo objects pre-filled with their asset objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of mediaInfo objects.
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

		mediaInfoPeer::addSelectColumns($criteria);
		$startcol = (mediaInfoPeer::NUM_COLUMNS - mediaInfoPeer::NUM_LAZY_LOAD_COLUMNS);
		assetPeer::addSelectColumns($criteria);

		$criteria->addJoin(mediaInfoPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = mediaInfoPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = mediaInfoPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = mediaInfoPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = mediaInfoPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				mediaInfoPeer::addInstanceToPool($obj1, $key1);
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
				
				// Add the $obj1 (mediaInfo) to $obj2 (asset)
				$obj2->addmediaInfo($obj1);

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
		$criteria->setPrimaryTableName(mediaInfoPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			mediaInfoPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(mediaInfoPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = mediaInfoPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of mediaInfo objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of mediaInfo objects.
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

		mediaInfoPeer::addSelectColumns($criteria);
		$startcol2 = (mediaInfoPeer::NUM_COLUMNS - mediaInfoPeer::NUM_LAZY_LOAD_COLUMNS);

		assetPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (assetPeer::NUM_COLUMNS - assetPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(mediaInfoPeer::FLAVOR_ASSET_ID, assetPeer::ID, $join_behavior);

		$stmt = mediaInfoPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = mediaInfoPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = mediaInfoPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = mediaInfoPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				mediaInfoPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined asset rows

			$key2 = assetPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = assetPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = assetPeer::getOMClass($row, $startcol2);
          $cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					assetPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (mediaInfo) to the collection in $obj2 (asset)
				$obj2->addmediaInfo($obj1);
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
	  $dbMap = Propel::getDatabaseMap(BasemediaInfoPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BasemediaInfoPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new mediaInfoTableMap());
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
		return $withPrefix ? mediaInfoPeer::CLASS_DEFAULT : mediaInfoPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a mediaInfo or Criteria object.
	 *
	 * @param      mixed $values Criteria or mediaInfo object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from mediaInfo object
		}

		if ($criteria->containsKey(mediaInfoPeer::ID) && $criteria->keyContainsValue(mediaInfoPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.mediaInfoPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a mediaInfo or Criteria object.
	 *
	 * @param      mixed $values Criteria or mediaInfo object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(mediaInfoPeer::ID);
			$selectCriteria->add(mediaInfoPeer::ID, $criteria->remove(mediaInfoPeer::ID), $comparison);

		} else { // $values is mediaInfo object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the media_info table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(mediaInfoPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			mediaInfoPeer::clearInstancePool();
			mediaInfoPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a mediaInfo or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or mediaInfo object or primary key or array of primary keys
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
			$con = Propel::getConnection(mediaInfoPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			mediaInfoPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof mediaInfo) { // it's a model object
			// invalidate the cache for this single object
			mediaInfoPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(mediaInfoPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				mediaInfoPeer::removeInstanceFromPool($singleval);
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
			mediaInfoPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given mediaInfo object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      mediaInfo $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(mediaInfo $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(mediaInfoPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(mediaInfoPeer::TABLE_NAME);

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

		return BasePeer::doValidate(mediaInfoPeer::DATABASE_NAME, mediaInfoPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     mediaInfo
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = mediaInfoPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(mediaInfoPeer::DATABASE_NAME);
		$criteria->add(mediaInfoPeer::ID, $pk);

		$v = mediaInfoPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(mediaInfoPeer::DATABASE_NAME);
			$criteria->add(mediaInfoPeer::ID, $pks, Criteria::IN);
			$objs = mediaInfoPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BasemediaInfoPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasemediaInfoPeer::buildTableMap();

