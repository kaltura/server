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
class BatchJobSepTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.BatchJobSepTableMap';

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
		$this->setPhpName('BatchJobSep');
		$this->setClassname('BatchJobSep');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('JOB_TYPE', 'JobType', 'SMALLINT', false, null, null);
		$this->addColumn('JOB_SUB_TYPE', 'JobSubType', 'SMALLINT', false, null, null);
		$this->addColumn('DATA', 'Data', 'LONGVARCHAR', false, null, null);
		$this->addColumn('HISTORY', 'History', 'LONGVARCHAR', false, null, null);
		$this->addColumn('LOCK_INFO', 'LockInfo', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('ABORT', 'Abort', 'TINYINT', false, null, null);
		$this->addColumn('PAUSE', 'Pause', 'TINYINT', false, null, null);
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
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addColumn('ERR_TYPE', 'ErrType', 'INTEGER', false, null, null);
		$this->addColumn('ERR_NUMBER', 'ErrNumber', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // BatchJobSepTableMap
