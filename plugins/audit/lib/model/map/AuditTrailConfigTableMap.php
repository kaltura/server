<?php


/**
 * This class defines the structure of the 'audit_trail_config' table.
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
class AuditTrailConfigTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.AuditTrailConfigTableMap';

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
		$this->setName('audit_trail_config');
		$this->setPhpName('AuditTrailConfig');
		$this->setClassname('AuditTrailConfig');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'VARCHAR', false, 31, null);
		$this->addColumn('DESCRIPTORS', 'Descriptors', 'VARCHAR', false, 1023, null);
		$this->addColumn('ACTIONS', 'Actions', 'VARCHAR', false, 1023, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // AuditTrailConfigTableMap
