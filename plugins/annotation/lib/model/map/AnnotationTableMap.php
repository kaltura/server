<?php


/**
 * This class defines the structure of the 'annotation' table.
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
class AnnotationTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.AnnotationTableMap';

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
		$this->setName('annotation');
		$this->setPhpName('Annotation');
		$this->setClassname('Annotation');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 255, null);
		$this->addColumn('PARENT_ID', 'ParentId', 'VARCHAR', false, 255, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', true, 31, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('TEXT', 'Text', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TAGS', 'Tags', 'VARCHAR', false, 255, null);
		$this->addColumn('START_TIME', 'StartTime', 'INTEGER', false, null, null);
		$this->addColumn('END_TIME', 'EndTime', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // AnnotationTableMap
