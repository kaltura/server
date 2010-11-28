<?php


/**
 * This class defines the structure of the 'flavor_asset' table.
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
class flavorAssetTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.flavorAssetTableMap';

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
		$this->setName('flavor_asset');
		$this->setPhpName('flavorAsset');
		$this->setClassname('flavorAsset');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('ID', 'Id', 'VARCHAR', true, 20, null);
		$this->addPrimaryKey('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', true, 20, null);
		$this->addForeignKey('FLAVOR_PARAMS_ID', 'FlavorParamsId', 'INTEGER', 'flavor_params', 'ID', true, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('VERSION', 'Version', 'VARCHAR', false, 20, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 255, null);
		$this->addColumn('WIDTH', 'Width', 'INTEGER', true, null, 0);
		$this->addColumn('HEIGHT', 'Height', 'INTEGER', true, null, 0);
		$this->addColumn('BITRATE', 'Bitrate', 'INTEGER', true, null, 0);
		$this->addColumn('FRAME_RATE', 'FrameRate', 'FLOAT', true, null, 0);
		$this->addColumn('SIZE', 'Size', 'INTEGER', true, null, 0);
		$this->addColumn('IS_ORIGINAL', 'IsOriginal', 'BOOLEAN', false, null, false);
		$this->addColumn('FILE_EXT', 'FileExt', 'VARCHAR', false, 4, null);
		$this->addColumn('CONTAINER_FORMAT', 'ContainerFormat', 'VARCHAR', false, 127, null);
		$this->addColumn('VIDEO_CODEC_ID', 'VideoCodecId', 'VARCHAR', false, 127, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', true, null, 0);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
    $this->addRelation('flavorParams', 'flavorParams', RelationMap::MANY_TO_ONE, array('flavor_params_id' => 'id', ), null, null);
    $this->addRelation('mediaInfo', 'mediaInfo', RelationMap::ONE_TO_MANY, array('id' => 'flavor_asset_id', ), null, null);
    $this->addRelation('flavorParamsOutput', 'flavorParamsOutput', RelationMap::ONE_TO_MANY, array('id' => 'flavor_asset_id', ), null, null);
	} // buildRelations()

} // flavorAssetTableMap
