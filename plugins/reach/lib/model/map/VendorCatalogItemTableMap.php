<?php


/**
 * This class defines the structure of the 'vendor_catalog_item' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.reach
 * @subpackage model.map
 */
class VendorCatalogItemTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.reach.VendorCatalogItemTableMap';

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
		$this->setName('vendor_catalog_item');
		$this->setPhpName('VendorCatalogItem');
		$this->setClassname('VendorCatalogItem');
		$this->setPackage('plugins.reach');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 256, null);
		$this->addColumn('SYSTEM_NAME', 'SystemName', 'VARCHAR', false, 256, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('IS_DEFAULT', 'IsDefault', 'TINYINT', false, null, 0);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('VENDOR_PARTNER_ID', 'VendorPartnerId', 'INTEGER', true, null, null);
		$this->addColumn('SERVICE_TYPE', 'ServiceType', 'TINYINT', true, null, null);
		$this->addColumn('SERVICE_FEATURE', 'ServiceFeature', 'TINYINT', true, null, null);
		$this->addColumn('TURN_AROUND_TIME', 'TurnAroundTime', 'INTEGER', true, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // VendorCatalogItemTableMap
