<?php


/**
 * This class defines the structure of the 'vendor_profile' table.
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
class VendorProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.reach.VendorProfileTableMap';

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
		$this->setName('vendor_profile');
		$this->setPhpName('VendorProfile');
		$this->setClassname('VendorProfile');
		$this->setPackage('plugins.reach');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('TYPE', 'Type', 'TINYINT', true, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('USED_CREDIT', 'UsedCredit', 'INTEGER', true, null, null);
		$this->addColumn('USED_CREDIT_PERIOD', 'UsedCreditPeriod', 'INTEGER', true, null, null);
		$this->addColumn('RULES', 'Rules', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DICTIONARY', 'Dictionary', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // VendorProfileTableMap
