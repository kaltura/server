<?php


/**
 * This class defines the structure of the 'drm_key' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.drm
 * @subpackage model.map
 */
class DrmKeyTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.drm.DrmKeyTableMap';

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
		$this->setName('drm_key');
		$this->setPhpName('DrmKey');
		$this->setClassname('DrmKey');
		$this->setPackage('plugins.drm');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('PROVIDER', 'Provider', 'INTEGER', true, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', true, 20, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'TINYINT', true, null, null);
		$this->addColumn('KEY', 'Key', 'VARCHAR', true, 128, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // DrmKeyTableMap
