<?php


/**
 * This class defines the structure of the 'flavor_params_conversion_profile' table.
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
class flavorParamsConversionProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.flavorParamsConversionProfileTableMap';

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
		$this->setName('flavor_params_conversion_profile');
		$this->setPhpName('flavorParamsConversionProfile');
		$this->setClassname('flavorParamsConversionProfile');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('CONVERSION_PROFILE_ID', 'ConversionProfileId', 'INTEGER', 'conversion_profile_2', 'ID', true, null, null);
		$this->addForeignKey('FLAVOR_PARAMS_ID', 'FlavorParamsId', 'INTEGER', 'flavor_params', 'ID', true, null, null);
		$this->addColumn('READY_BEHAVIOR', 'ReadyBehavior', 'TINYINT', true, null, null);
		$this->addColumn('FORCE_NONE_COMPLIED', 'ForceNoneComplied', 'BOOLEAN', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('conversionProfile2', 'conversionProfile2', RelationMap::MANY_TO_ONE, array('conversion_profile_id' => 'id', ), null, null);
    $this->addRelation('flavorParams', 'flavorParams', RelationMap::MANY_TO_ONE, array('flavor_params_id' => 'id', ), null, null);
	} // buildRelations()

} // flavorParamsConversionProfileTableMap
