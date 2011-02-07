<?php


/**
 * This class defines the structure of the 'batch_job' table.
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
		$this->setName('batch_job');
		$this->setPhpName('BatchJob');
		$this->setClassname('BatchJob');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('JOB_TYPE', 'JobType', 'SMALLINT', false, null, null);
		$this->addColumn('JOB_SUB_TYPE', 'JobSubType', 'SMALLINT', false, null, null);
		$this->addColumn('DATA', 'Data', 'VARCHAR', false, 4096, null);
		$this->addColumn('FILE_SIZE', 'FileSize', 'INTEGER', false, null, null);
		$this->addColumn('DUPLICATION_KEY', 'DuplicationKey', 'VARCHAR', false, 2047, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('ABORT', 'Abort', 'TINYINT', false, null, null);
		$this->addColumn('CHECK_AGAIN_TIMEOUT', 'CheckAgainTimeout', 'INTEGER', false, null, null);
		$this->addColumn('PROGRESS', 'Progress', 'TINYINT', false, null, null);
		$this->addColumn('MESSAGE', 'Message', 'VARCHAR', false, 1024, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 1024, null);
		$this->addColumn('UPDATES_COUNT', 'UpdatesCount', 'SMALLINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_BY', 'UpdatedBy', 'VARCHAR', false, 20, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PRIORITY', 'Priority', 'TINYINT', false, null, null);
		$this->addColumn('WORK_GROUP_ID', 'WorkGroupId', 'INTEGER', false, null, null);
		$this->addColumn('QUEUE_TIME', 'QueueTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('FINISH_TIME', 'FinishTime', 'TIMESTAMP', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, '');
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('BATCH_INDEX', 'BatchIndex', 'INTEGER', false, null, null);
		$this->addColumn('LAST_SCHEDULER_ID', 'LastSchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('LAST_WORKER_ID', 'LastWorkerId', 'INTEGER', false, null, null);
		$this->addColumn('LAST_WORKER_REMOTE', 'LastWorkerRemote', 'BOOLEAN', false, null, null);
		$this->addColumn('PROCESSOR_EXPIRATION', 'ProcessorExpiration', 'TIMESTAMP', false, null, null);
		$this->addColumn('EXECUTION_ATTEMPTS', 'ExecutionAttempts', 'TINYINT', false, null, null);
		$this->addColumn('LOCK_VERSION', 'LockVersion', 'INTEGER', false, null, null);
		$this->addColumn('TWIN_JOB_ID', 'TwinJobId', 'INTEGER', false, null, null);
		$this->addColumn('BULK_JOB_ID', 'BulkJobId', 'INTEGER', false, null, null);
		$this->addColumn('ROOT_JOB_ID', 'RootJobId', 'INTEGER', false, null, null);
		$this->addColumn('PARENT_JOB_ID', 'ParentJobId', 'INTEGER', false, null, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addColumn('ERR_TYPE', 'ErrType', 'INTEGER', false, null, null);
		$this->addColumn('ERR_NUMBER', 'ErrNumber', 'INTEGER', false, null, null);
		$this->addColumn('ON_STRESS_DIVERT_TO', 'OnStressDivertTo', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // BatchJobTableMap
