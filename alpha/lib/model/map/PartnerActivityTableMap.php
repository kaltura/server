<?php


/**
 * This class defines the structure of the 'partner_activity' table.
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
class PartnerActivityTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.PartnerActivityTableMap';

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
		$this->setName('partner_activity');
		$this->setPhpName('PartnerActivity');
		$this->setClassname('PartnerActivity');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ACTIVITY_DATE', 'ActivityDate', 'DATE', false, null, null);
		$this->addColumn('ACTIVITY', 'Activity', 'INTEGER', false, null, null);
		$this->addColumn('SUB_ACTIVITY', 'SubActivity', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT', 'Amount', 'BIGINT', false, null, null);
		$this->addColumn('AMOUNT1', 'Amount1', 'BIGINT', false, null, null);
		$this->addColumn('AMOUNT2', 'Amount2', 'BIGINT', false, null, null);
		$this->addColumn('AMOUNT3', 'Amount3', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT4', 'Amount4', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT5', 'Amount5', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT6', 'Amount6', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT7', 'Amount7', 'INTEGER', false, null, null);
		$this->addColumn('AMOUNT9', 'Amount9', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // PartnerActivityTableMap
