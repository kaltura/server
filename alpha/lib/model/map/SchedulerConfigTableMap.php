<?php


/**
 * This class defines the structure of the 'scheduler_config' table.
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
class SchedulerConfigTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.SchedulerConfigTableMap';

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
		$this->setName('scheduler_config');
		$this->setPhpName('SchedulerConfig');
		$this->setClassname('SchedulerConfig');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_BY', 'UpdatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('COMMAND_ID', 'CommandId', 'INTEGER', false, null, null);
		$this->addColumn('COMMAND_STATUS', 'CommandStatus', 'TINYINT', false, null, null);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('SCHEDULER_CONFIGURED_ID', 'SchedulerConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('SCHEDULER_NAME', 'SchedulerName', 'VARCHAR', false, 20, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_CONFIGURED_ID', 'WorkerConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_NAME', 'WorkerName', 'VARCHAR', false, 50, null);
		$this->addColumn('VARIABLE', 'Variable', 'VARCHAR', false, 100, null);
		$this->addColumn('VARIABLE_PART', 'VariablePart', 'VARCHAR', false, 100, null);
		$this->addColumn('VALUE', 'Value', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // SchedulerConfigTableMap
