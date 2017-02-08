<?php


/**
 * This class defines the structure of the 'business_process_case' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.businessProcessNotification
 * @subpackage model.map
 */
class BusinessProcessCaseTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.businessProcessNotification.BusinessProcessCaseTableMap';

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
		$this->setName('business_process_case');
		$this->setPhpName('BusinessProcessCase');
		$this->setClassname('BusinessProcessCase');
		$this->setPackage('plugins.businessProcessNotification');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CASE_ID', 'CaseId', 'VARCHAR', false, 64, null);
		$this->addColumn('PROCESS_ID', 'ProcessId', 'VARCHAR', false, 255, null);
		$this->addColumn('TEMPLATE_ID', 'TemplateId', 'INTEGER', false, null, null);
		$this->addColumn('SERVER_ID', 'ServerId', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 20, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'INTEGER', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // BusinessProcessCaseTableMap
