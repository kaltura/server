<?php


/**
 * This class defines the structure of the 'flavor_params' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class flavorParamsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.flavorParamsTableMap';

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
		$this->setName('flavor_params');
		$this->setPhpName('flavorParams');
		$this->setClassname('flavorParams');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, '');
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', true, 1024, '');
		$this->addColumn('READY_BEHAVIOR', 'ReadyBehavior', 'TINYINT', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('IS_DEFAULT', 'IsDefault', 'TINYINT', true, null, 0);
		$this->addColumn('FORMAT', 'Format', 'VARCHAR', true, 20, null);
		$this->addColumn('VIDEO_CODEC', 'VideoCodec', 'VARCHAR', true, 20, null);
		$this->addColumn('VIDEO_BITRATE', 'VideoBitrate', 'INTEGER', true, null, 0);
		$this->addColumn('AUDIO_CODEC', 'AudioCodec', 'VARCHAR', true, 20, null);
		$this->addColumn('AUDIO_BITRATE', 'AudioBitrate', 'INTEGER', true, null, 0);
		$this->addColumn('AUDIO_CHANNELS', 'AudioChannels', 'TINYINT', true, null, 0);
		$this->addColumn('AUDIO_SAMPLE_RATE', 'AudioSampleRate', 'INTEGER', false, null, 0);
		$this->addColumn('AUDIO_RESOLUTION', 'AudioResolution', 'INTEGER', false, null, 0);
		$this->addColumn('WIDTH', 'Width', 'INTEGER', true, null, 0);
		$this->addColumn('HEIGHT', 'Height', 'INTEGER', true, null, 0);
		$this->addColumn('FRAME_RATE', 'FrameRate', 'FLOAT', true, null, 0);
		$this->addColumn('GOP_SIZE', 'GopSize', 'INTEGER', true, null, 0);
		$this->addColumn('TWO_PASS', 'TwoPass', 'BOOLEAN', true, null, false);
		$this->addColumn('CONVERSION_ENGINES', 'ConversionEngines', 'VARCHAR', false, 1024, null);
		$this->addColumn('CONVERSION_ENGINES_EXTRA_PARAMS', 'ConversionEnginesExtraParams', 'VARCHAR', false, 1024, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('VIEW_ORDER', 'ViewOrder', 'INTEGER', false, null, null);
		$this->addColumn('CREATION_MODE', 'CreationMode', 'SMALLINT', false, null, 1);
		$this->addColumn('DEINTERLICE', 'Deinterlice', 'INTEGER', false, null, null);
		$this->addColumn('ROTATE', 'Rotate', 'INTEGER', false, null, null);
		$this->addColumn('OPERATORS', 'Operators', 'LONGVARCHAR', false, null, null);
		$this->addColumn('ENGINE_VERSION', 'EngineVersion', 'SMALLINT', false, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', true, null, 1);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('flavorParamsOutput', 'flavorParamsOutput', RelationMap::ONE_TO_MANY, array('id' => 'flavor_params_id', ), null, null);
    $this->addRelation('flavorAsset', 'flavorAsset', RelationMap::ONE_TO_MANY, array('id' => 'flavor_params_id', ), null, null);
    $this->addRelation('flavorParamsConversionProfile', 'flavorParamsConversionProfile', RelationMap::ONE_TO_MANY, array('id' => 'flavor_params_id', ), null, null);
	} // buildRelations()

} // flavorParamsTableMap
