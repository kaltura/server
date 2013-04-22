<?php


/**
 * This class defines the structure of the 'batch_job_sep' table.
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
class BatchJobTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.BatchJobTableMap';

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
		$this->setName('batch_job_sep');
		$this->setPhpName('BatchJob');
		$this->setClassname('BatchJob');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('JOB_TYPE', 'JobType', 'INTEGER', false, null, null);
		$this->addColumn('JOB_SUB_TYPE', 'JobSubType', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 20, '');
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'INTEGER', false, null, null);
		$this->addColumn('DATA', 'Data', 'LONGVARCHAR', false, null, null);
		$this->addColumn('HISTORY', 'History', 'LONGVARCHAR', false, null, null);
		$this->addColumn('LOCK_INFO', 'LockInfo', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('EXECUTION_STATUS', 'ExecutionStatus', 'INTEGER', false, null, null);
		$this->addColumn('MESSAGE', 'Message', 'VARCHAR', false, 1024, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 1024, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PRIORITY', 'Priority', 'TINYINT', false, null, null);
		$this->addColumn('QUEUE_TIME', 'QueueTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('FINISH_TIME', 'FinishTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, '');
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('BULK_JOB_ID', 'BulkJobId', 'INTEGER', false, null, null);
		$this->addColumn('ROOT_JOB_ID', 'RootJobId', 'INTEGER', false, null, null);
		$this->addColumn('PARENT_JOB_ID', 'ParentJobId', 'INTEGER', false, null, null);
		$this->addColumn('LAST_SCHEDULER_ID', 'LastSchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('LAST_WORKER_ID', 'LastWorkerId', 'INTEGER', false, null, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addColumn('ERR_TYPE', 'ErrType', 'INTEGER', false, null, null);
		$this->addColumn('ERR_NUMBER', 'ErrNumber', 'INTEGER', false, null, null);
		$this->addForeignKey('BATCH_JOB_LOCK_ID', 'BatchJobLockId', 'INTEGER', 'batch_job_lock', 'ID', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('BatchJobLock', 'BatchJobLock', RelationMap::MANY_TO_ONE, array('batch_job_lock_id' => 'id', ), null, null);
    $this->addRelation('BatchJobLock', 'BatchJobLock', RelationMap::ONE_TO_MANY, array('id' => 'batch_job_id', ), null, null);
    $this->addRelation('BatchJobLockSuspend', 'BatchJobLockSuspend', RelationMap::ONE_TO_MANY, array('id' => 'batch_job_id', ), null, null);
	} // buildRelations()

} // BatchJobTableMap
