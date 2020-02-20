<?php


/**
 * This class defines the structure of the 'reach_profile' table.
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
class ReachProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.reach.ReachProfileTableMap';

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
		$this->setName('reach_profile');
		$this->setPhpName('ReachProfile');
		$this->setClassname('ReachProfile');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 256, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('TYPE', 'Type', 'TINYINT', true, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('USED_CREDIT', 'UsedCredit', 'DOUBLE', false, null, 0);
		$this->addColumn('ADD_ON', 'AddOn', 'INTEGER', false, null, 0);
		$this->addColumn('SYNCED_CREDIT', 'SyncedCredit', 'INTEGER', false, null, 0);
		$this->addColumn('LAST_SYNC_TIME', 'LastSyncTime', 'VARCHAR', false, 100, null);
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

} // ReachProfileTableMap
