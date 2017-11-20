<?php


/**
 * This class defines the structure of the 'app_token' table.
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
class AppTokenTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.AppTokenTableMap';

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
		$this->setName('app_token');
		$this->setPhpName('AppToken');
		$this->setClassname('AppToken');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 20, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('EXPIRY', 'Expiry', 'INTEGER', false, null, null);
		$this->addColumn('SESSION_TYPE', 'SessionType', 'INTEGER', false, null, null);
		$this->addColumn('SESSION_USER_ID', 'SessionUserId', 'VARCHAR', false, 100, null);
		$this->addColumn('SESSION_DURATION', 'SessionDuration', 'INTEGER', false, null, null);
		$this->addColumn('SESSION_PRIVILEGES', 'SessionPrivileges', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TOKEN', 'Token', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // AppTokenTableMap
