<?php


/**
 * This class defines the structure of the 'notification' table.
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
class notificationTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.notificationTableMap';

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
		$this->setName('notification');
		$this->setPhpName('notification');
		$this->setClassname('notification');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'VARCHAR', false, 64, null);
		$this->addColumn('TYPE', 'Type', 'SMALLINT', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 20, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('NOTIFICATION_DATA', 'NotificationData', 'VARCHAR', false, 4096, null);
		$this->addColumn('NUMBER_OF_ATTEMPTS', 'NumberOfAttempts', 'SMALLINT', false, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('NOTIFICATION_RESULT', 'NotificationResult', 'VARCHAR', false, 256, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'SMALLINT', false, null, null);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('BATCH_INDEX', 'BatchIndex', 'INTEGER', false, null, null);
		$this->addColumn('PROCESSOR_EXPIRATION', 'ProcessorExpiration', 'TIMESTAMP', false, null, null);
		$this->addColumn('EXECUTION_ATTEMPTS', 'ExecutionAttempts', 'TINYINT', false, null, null);
		$this->addColumn('LOCK_VERSION', 'LockVersion', 'INTEGER', false, null, null);
		$this->addColumn('DC', 'Dc', 'VARCHAR', false, 2, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // notificationTableMap
