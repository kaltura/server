<?php


/**
 * This class defines the structure of the 'media_info' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package Core
 * @subpackage model.map
 */
class mediaInfoTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.mediaInfoTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('media_info');
		$this->setPhpName('mediaInfo');
		$this->setClassname('mediaInfo');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addForeignKey('FLAVOR_ASSET_ID', 'FlavorAssetId', 'VARCHAR', 'flavor_asset', 'ID', false, 20, null);
		$this->addColumn('FILE_SIZE', 'FileSize', 'INTEGER', true, null, null);
		$this->addColumn('CONTAINER_FORMAT', 'ContainerFormat', 'VARCHAR', false, 127, null);
		$this->addColumn('CONTAINER_ID', 'ContainerId', 'VARCHAR', false, 127, null);
		$this->addColumn('CONTAINER_PROFILE', 'ContainerProfile', 'VARCHAR', false, 127, null);
		$this->addColumn('CONTAINER_DURATION', 'ContainerDuration', 'INTEGER', false, null, null);
		$this->addColumn('CONTAINER_BIT_RATE', 'ContainerBitRate', 'INTEGER', false, null, null);
		$this->addColumn('VIDEO_FORMAT', 'VideoFormat', 'VARCHAR', false, 127, null);
		$this->addColumn('VIDEO_CODEC_ID', 'VideoCodecId', 'VARCHAR', false, 127, null);
		$this->addColumn('VIDEO_DURATION', 'VideoDuration', 'INTEGER', false, null, null);
		$this->addColumn('VIDEO_BIT_RATE', 'VideoBitRate', 'INTEGER', false, null, null);
		$this->addColumn('VIDEO_BIT_RATE_MODE', 'VideoBitRateMode', 'TINYINT', false, null, null);
		$this->addColumn('VIDEO_WIDTH', 'VideoWidth', 'INTEGER', true, null, null);
		$this->addColumn('VIDEO_HEIGHT', 'VideoHeight', 'INTEGER', true, null, null);
		$this->addColumn('VIDEO_FRAME_RATE', 'VideoFrameRate', 'FLOAT', false, null, null);
		$this->addColumn('VIDEO_DAR', 'VideoDar', 'FLOAT', false, null, null);
		$this->addColumn('VIDEO_ROTATION', 'VideoRotation', 'INTEGER', false, null, null);
		$this->addColumn('AUDIO_FORMAT', 'AudioFormat', 'VARCHAR', false, 127, null);
		$this->addColumn('AUDIO_CODEC_ID', 'AudioCodecId', 'VARCHAR', false, 127, null);
		$this->addColumn('AUDIO_DURATION', 'AudioDuration', 'INTEGER', false, null, null);
		$this->addColumn('AUDIO_BIT_RATE', 'AudioBitRate', 'INTEGER', false, null, null);
		$this->addColumn('AUDIO_BIT_RATE_MODE', 'AudioBitRateMode', 'TINYINT', false, null, null);
		$this->addColumn('AUDIO_CHANNELS', 'AudioChannels', 'TINYINT', false, null, null);
		$this->addColumn('AUDIO_SAMPLING_RATE', 'AudioSamplingRate', 'INTEGER', false, null, null);
		$this->addColumn('AUDIO_RESOLUTION', 'AudioResolution', 'INTEGER', false, null, null);
		$this->addColumn('WRITING_LIB', 'WritingLib', 'VARCHAR', false, 127, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('RAW_DATA', 'RawData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('MULTI_STREAM_INFO', 'MultiStreamInfo', 'VARCHAR', false, 1023, null);
		$this->addColumn('FLAVOR_ASSET_VERSION', 'FlavorAssetVersion', 'VARCHAR', false, 20, null);
		$this->addColumn('SCAN_TYPE', 'ScanType', 'INTEGER', false, null, null);
		$this->addColumn('MULTI_STREAM', 'MultiStream', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('asset', 'asset', RelationMap::MANY_TO_ONE, array('flavor_asset_id' => 'id', ), null, null);
	} // buildRelations()

} // mediaInfoTableMap
