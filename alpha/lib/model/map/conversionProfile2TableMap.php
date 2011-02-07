<?php


/**
 * This class defines the structure of the 'conversion_profile_2' table.
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
class conversionProfile2TableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.conversionProfile2TableMap';

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
		$this->setName('conversion_profile_2');
		$this->setPhpName('conversionProfile2');
		$this->setClassname('conversionProfile2');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, '');
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', true, 1024, '');
		$this->addColumn('CROP_LEFT', 'CropLeft', 'INTEGER', true, null, -1);
		$this->addColumn('CROP_TOP', 'CropTop', 'INTEGER', true, null, -1);
		$this->addColumn('CROP_WIDTH', 'CropWidth', 'INTEGER', true, null, -1);
		$this->addColumn('CROP_HEIGHT', 'CropHeight', 'INTEGER', true, null, -1);
		$this->addColumn('CLIP_START', 'ClipStart', 'INTEGER', true, null, -1);
		$this->addColumn('CLIP_DURATION', 'ClipDuration', 'INTEGER', true, null, -1);
		$this->addColumn('INPUT_TAGS_MAP', 'InputTagsMap', 'VARCHAR', false, 1023, null);
		$this->addColumn('CREATION_MODE', 'CreationMode', 'SMALLINT', false, null, 1);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entry', 'entry', RelationMap::ONE_TO_MANY, array('id' => 'conversion_profile_id', ), null, null);
    $this->addRelation('flavorParamsConversionProfile', 'flavorParamsConversionProfile', RelationMap::ONE_TO_MANY, array('id' => 'conversion_profile_id', ), null, null);
	} // buildRelations()

} // conversionProfile2TableMap
