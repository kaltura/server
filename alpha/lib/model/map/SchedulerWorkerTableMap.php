<?php


/**
 * This class defines the structure of the 'scheduler_worker' table.
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
class SchedulerWorkerTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.SchedulerWorkerTableMap';

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
		$this->setName('scheduler_worker');
		$this->setPhpName('SchedulerWorker');
		$this->setClassname('SchedulerWorker');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_BY', 'UpdatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('SCHEDULER_CONFIGURED_ID', 'SchedulerConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('CONFIGURED_ID', 'ConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('TYPE', 'Type', 'SMALLINT', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 20, '');
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 20, '');
		$this->addColumn('STATUSES', 'Statuses', 'VARCHAR', false, 255, '');
		$this->addColumn('LAST_STATUS', 'LastStatus', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // SchedulerWorkerTableMap
