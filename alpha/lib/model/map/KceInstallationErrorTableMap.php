<?php


/**
 * This class defines the structure of the 'kce_installation_error' table.
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
class KceInstallationErrorTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.KceInstallationErrorTableMap';

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
		$this->setName('kce_installation_error');
		$this->setPhpName('KceInstallationError');
		$this->setClassname('KceInstallationError');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('BROWSER', 'Browser', 'VARCHAR', false, 100, null);
		$this->addColumn('SERVER_IP', 'ServerIp', 'VARCHAR', false, 20, null);
		$this->addColumn('SERVER_OS', 'ServerOs', 'VARCHAR', false, 100, null);
		$this->addColumn('PHP_VERSION', 'PhpVersion', 'VARCHAR', false, 20, null);
		$this->addColumn('CE_ADMIN_EMAIL', 'CeAdminEmail', 'VARCHAR', false, 50, null);
		$this->addColumn('TYPE', 'Type', 'VARCHAR', false, 50, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 100, null);
		$this->addColumn('DATA', 'Data', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // KceInstallationErrorTableMap
