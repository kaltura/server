<?php


/**
 * This class defines the structure of the 'entry_vendor_task' table.
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
class EntryVendorTaskTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.reach.EntryVendorTaskTableMap';

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
		$this->setName('entry_vendor_task');
		$this->setPhpName('EntryVendorTask');
		$this->setClassname('EntryVendorTask');
		$this->setPackage('plugins.reach');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'BIGINT', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('QUEUE_TIME', 'QueueTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('FINISH_TIME', 'FinishTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('VENDOR_PARTNER_ID', 'VendorPartnerId', 'INTEGER', true, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', true, 31, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('PRICE', 'Price', 'INTEGER', true, null, null);
		$this->addColumn('CATALOG_ITEM_ID', 'CatalogItemId', 'INTEGER', true, null, null);
		$this->addColumn('REACH_PROFILE_ID', 'ReachProfileId', 'INTEGER', true, null, null);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', true, null, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, null);
		$this->addColumn('CONTEXT', 'Context', 'VARCHAR', false, 256, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // EntryVendorTaskTableMap
