<?php


/**
 * This class defines the structure of the 'audit_trail_data' table.
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
class AuditTrailDataTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.AuditTrailDataTableMap';

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
		$this->setName('audit_trail_data');
		$this->setPhpName('AuditTrailData');
		$this->setClassname('AuditTrailData');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('AUDIT_TRAIL_ID', 'AuditTrailId', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'VARCHAR', false, 31, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 31, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ACTION', 'Action', 'VARCHAR', false, 31, null);
		$this->addColumn('DESCRIPTOR', 'Descriptor', 'VARCHAR', false, 127, null);
		$this->addColumn('OLD_VALUE', 'OldValue', 'VARCHAR', false, 511, null);
		$this->addColumn('NEW_VALUE', 'NewValue', 'VARCHAR', false, 511, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // AuditTrailDataTableMap
