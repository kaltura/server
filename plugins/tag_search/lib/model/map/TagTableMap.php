<?php


/**
 * This class defines the structure of the 'tag' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.tagSearch
 * @subpackage model.map
 */
class TagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.tagSearch.TagTableMap';

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
		$this->setName('tag');
		$this->setPhpName('Tag');
		$this->setClassname('Tag');
		$this->setPackage('plugins.tagSearch');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('TAG', 'Tag', 'VARCHAR', true, 32, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'INTEGER', true, null, null);
		$this->addColumn('INSTANCE_COUNT', 'InstanceCount', 'INTEGER', true, null, 1);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PRIVACY_CONTEXT', 'PrivacyContext', 'VARCHAR', true, 255, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // TagTableMap
