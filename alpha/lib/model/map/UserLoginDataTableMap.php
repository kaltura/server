<?php


/**
 * This class defines the structure of the 'user_login_data' table.
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
class UserLoginDataTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.UserLoginDataTableMap';

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
		$this->setName('user_login_data');
		$this->setPhpName('UserLoginData');
		$this->setClassname('UserLoginData');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('LOGIN_EMAIL', 'LoginEmail', 'VARCHAR', true, 100, null);
		$this->addColumn('FIRST_NAME', 'FirstName', 'VARCHAR', false, 40, null);
		$this->addColumn('LAST_NAME', 'LastName', 'VARCHAR', false, 40, null);
		$this->addColumn('SHA1_PASSWORD', 'Sha1Password', 'VARCHAR', true, 40, null);
		$this->addColumn('SALT', 'Salt', 'VARCHAR', true, 32, null);
		$this->addColumn('CONFIG_PARTNER_ID', 'ConfigPartnerId', 'INTEGER', true, null, null);
		$this->addColumn('LOGIN_BLOCKED_UNTIL', 'LoginBlockedUntil', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // UserLoginDataTableMap
