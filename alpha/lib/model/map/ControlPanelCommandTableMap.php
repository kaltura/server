<?php


/**
 * This class defines the structure of the 'control_panel_command' table.
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
class ControlPanelCommandTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.ControlPanelCommandTableMap';

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
		$this->setName('control_panel_command');
		$this->setPhpName('ControlPanelCommand');
		$this->setClassname('ControlPanelCommand');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_BY', 'UpdatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('CREATED_BY_ID', 'CreatedById', 'INTEGER', false, null, null);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('SCHEDULER_CONFIGURED_ID', 'SchedulerConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_CONFIGURED_ID', 'WorkerConfiguredId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_NAME', 'WorkerName', 'VARCHAR', false, 50, null);
		$this->addColumn('BATCH_INDEX', 'BatchIndex', 'INTEGER', false, null, null);
		$this->addColumn('TYPE', 'Type', 'SMALLINT', false, null, null);
		$this->addColumn('TARGET_TYPE', 'TargetType', 'SMALLINT', false, null, null);
		$this->addColumn('STATUS', 'Status', 'SMALLINT', false, null, null);
		$this->addColumn('CAUSE', 'Cause', 'VARCHAR', false, 255, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 255, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ControlPanelCommandTableMap
