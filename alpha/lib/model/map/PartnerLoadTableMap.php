<?php


/**
 * This class defines the structure of the 'partner_load' table.
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
class PartnerLoadTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.PartnerLoadTableMap';

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
		$this->setName('partner_load');
		$this->setPhpName('PartnerLoad');
		$this->setClassname('PartnerLoad');
		$this->setPackage('Core');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('JOB_TYPE', 'JobType', 'INTEGER', true, null, null);
		$this->addPrimaryKey('JOB_SUB_TYPE', 'JobSubType', 'INTEGER', true, null, 0);
		$this->addPrimaryKey('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addPrimaryKey('DC', 'Dc', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_LOAD', 'PartnerLoad', 'INTEGER', false, null, null);
		$this->addColumn('WEIGHTED_PARTNER_LOAD', 'WeightedPartnerLoad', 'INTEGER', false, null, null);
		$this->addColumn('QUOTA', 'Quota', 'INTEGER', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // PartnerLoadTableMap
