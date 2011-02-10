<?php


/**
 * This class defines the structure of the 'audit_trail' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.audit
 * @subpackage model.map
 */
class AuditTrailTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.audit.AuditTrailTableMap';

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
		$this->setName('audit_trail');
		$this->setPhpName('AuditTrail');
		$this->setClassname('AuditTrail');
		$this->setPackage('plugins.audit');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARSED_AT', 'ParsedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'VARCHAR', false, 31, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 31, null);
		$this->addColumn('RELATED_OBJECT_ID', 'RelatedObjectId', 'VARCHAR', false, 31, null);
		$this->addColumn('RELATED_OBJECT_TYPE', 'RelatedObjectType', 'VARCHAR', false, 31, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 31, null);
		$this->addColumn('MASTER_PARTNER_ID', 'MasterPartnerId', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('REQUEST_ID', 'RequestId', 'VARCHAR', false, 31, null);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', false, null, null);
		$this->addColumn('ACTION', 'Action', 'VARCHAR', false, 31, null);
		$this->addColumn('DATA', 'Data', 'LONGVARCHAR', false, null, null);
		$this->addColumn('KS', 'Ks', 'VARCHAR', false, 511, null);
		$this->addColumn('CONTEXT', 'Context', 'TINYINT', false, null, null);
		$this->addColumn('ENTRY_POINT', 'EntryPoint', 'VARCHAR', false, 127, null);
		$this->addColumn('SERVER_NAME', 'ServerName', 'VARCHAR', false, 63, null);
		$this->addColumn('IP_ADDRESS', 'IpAddress', 'VARCHAR', false, 15, null);
		$this->addColumn('USER_AGENT', 'UserAgent', 'VARCHAR', false, 127, null);
		$this->addColumn('CLIENT_TAG', 'ClientTag', 'VARCHAR', false, 127, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 1023, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'VARCHAR', false, 1023, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // AuditTrailTableMap
