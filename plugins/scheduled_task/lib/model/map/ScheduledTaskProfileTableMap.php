<?php


/**
 * This class defines the structure of the 'scheduled_task_profile' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.scheduledTask
 * @subpackage model.map
 */
class ScheduledTaskProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.scheduledTask.ScheduledTaskProfileTableMap';

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
		$this->setName('scheduled_task_profile');
		$this->setPhpName('ScheduledTaskProfile');
		$this->setClassname('ScheduledTaskProfile');
		$this->setPackage('plugins.scheduledTask');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 127, null);
		$this->addColumn('SYSTEM_NAME', 'SystemName', 'VARCHAR', false, 127, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 255, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', true, null, null);
		$this->addColumn('OBJECT_FILTER_ENGINE_TYPE', 'ObjectFilterEngineType', 'INTEGER', true, null, null);
		$this->addColumn('OBJECT_FILTER', 'ObjectFilter', 'LONGVARCHAR', true, null, null);
		$this->addColumn('OBJECT_FILTER_API_TYPE', 'ObjectFilterApiType', 'VARCHAR', true, 255, null);
		$this->addColumn('OBJECT_TASKS', 'ObjectTasks', 'LONGVARCHAR', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('LAST_EXECUTION_STARTED_AT', 'LastExecutionStartedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('MAX_TOTAL_COUNT_ALLOWED', 'MaxTotalCountAllowed', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ScheduledTaskProfileTableMap
