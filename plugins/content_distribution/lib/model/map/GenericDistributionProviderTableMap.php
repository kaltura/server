<?php


/**
 * This class defines the structure of the 'generic_distribution_provider' table.
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
class GenericDistributionProviderTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.GenericDistributionProviderTableMap';

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
		$this->setName('generic_distribution_provider');
		$this->setPhpName('GenericDistributionProvider');
		$this->setClassname('GenericDistributionProvider');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('IS_DEFAULT', 'IsDefault', 'TINYINT', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 127, null);
		$this->addColumn('OPTIONAL_FLAVOR_PARAMS_IDS', 'OptionalFlavorParamsIds', 'VARCHAR', false, 127, null);
		$this->addColumn('REQUIRED_FLAVOR_PARAMS_IDS', 'RequiredFlavorParamsIds', 'VARCHAR', false, 127, null);
		$this->addColumn('OPTIONAL_THUMB_DIMENSIONS', 'OptionalThumbDimensions', 'VARCHAR', false, 2048, null);
		$this->addColumn('REQUIRED_THUMB_DIMENSIONS', 'RequiredThumbDimensions', 'VARCHAR', false, 2048, null);
		$this->addColumn('EDITABLE_FIELDS', 'EditableFields', 'VARCHAR', false, 255, null);
		$this->addColumn('MANDATORY_FIELDS', 'MandatoryFields', 'VARCHAR', false, 255, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // GenericDistributionProviderTableMap
