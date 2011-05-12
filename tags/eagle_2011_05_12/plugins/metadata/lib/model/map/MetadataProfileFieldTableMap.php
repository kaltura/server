<?php


/**
 * This class defines the structure of the 'metadata_profile_field' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.metadata
 * @subpackage model.map
 */
class MetadataProfileFieldTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.metadata.MetadataProfileFieldTableMap';

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
		$this->setName('metadata_profile_field');
		$this->setPhpName('MetadataProfileField');
		$this->setClassname('MetadataProfileField');
		$this->setPackage('plugins.metadata');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('METADATA_PROFILE_ID', 'MetadataProfileId', 'INTEGER', false, null, null);
		$this->addColumn('METADATA_PROFILE_VERSION', 'MetadataProfileVersion', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('LABEL', 'Label', 'VARCHAR', false, 127, null);
		$this->addColumn('KEY', 'Key', 'VARCHAR', false, 127, null);
		$this->addColumn('TYPE', 'Type', 'VARCHAR', false, 127, null);
		$this->addColumn('XPATH', 'Xpath', 'VARCHAR', false, 255, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // MetadataProfileFieldTableMap
