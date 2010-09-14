<?php


/**
 * This class defines the structure of the 'system_user' table.
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
class SystemUserTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.SystemUserTableMap';

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
		$this->setName('system_user');
		$this->setPhpName('SystemUser');
		$this->setClassname('SystemUser');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 50, null);
		$this->addColumn('FIRST_NAME', 'FirstName', 'VARCHAR', true, 40, null);
		$this->addColumn('LAST_NAME', 'LastName', 'VARCHAR', true, 40, null);
		$this->addColumn('SHA1_PASSWORD', 'Sha1Password', 'VARCHAR', true, 40, null);
		$this->addColumn('SALT', 'Salt', 'VARCHAR', true, 32, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('IS_PRIMARY', 'IsPrimary', 'BOOLEAN', false, null, false);
		$this->addColumn('STATUS_UPDATED_AT', 'StatusUpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('ROLE', 'Role', 'VARCHAR', false, 40, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // SystemUserTableMap
