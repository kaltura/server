<?php


/**
 * This class defines the structure of the 'batch_job_lock_suspend' table.
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
class BatchJobLockSuspendTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.BatchJobLockSuspendTableMap';

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
		$this->setName('batch_job_lock_suspend');
		$this->setPhpName('BatchJobLockSuspend');
		$this->setClassname('BatchJobLockSuspend');
		$this->setPackage('Core');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('JOB_TYPE', 'JobType', 'INTEGER', false, null, null);
		$this->addColumn('JOB_SUB_TYPE', 'JobSubType', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 20, '');
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'INTEGER', false, null, null);
		$this->addColumn('ESTIMATED_EFFORT', 'EstimatedEffort', 'BIGINT', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('START_AT', 'StartAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PRIORITY', 'Priority', 'TINYINT', false, null, null);
		$this->addColumn('URGENCY', 'Urgency', 'TINYINT', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, '');
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('BATCH_INDEX', 'BatchIndex', 'INTEGER', false, null, null);
		$this->addColumn('EXPIRATION', 'Expiration', 'TIMESTAMP', false, null, null);
		$this->addColumn('EXECUTION_ATTEMPTS', 'ExecutionAttempts', 'TINYINT', false, null, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addForeignKey('BATCH_JOB_ID', 'BatchJobId', 'INTEGER', 'batch_job_sep', 'ID', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('BATCH_VERSION', 'BatchVersion', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('BatchJob', 'BatchJob', RelationMap::MANY_TO_ONE, array('batch_job_id' => 'id', ), null, null);
	} // buildRelations()

} // BatchJobLockSuspendTableMap
