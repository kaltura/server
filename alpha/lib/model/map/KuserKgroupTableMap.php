<?php


/**
 * This class defines the structure of the 'kuser_kgroup' table.
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
class KuserKgroupTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.KuserKgroupTableMap';

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
		$this->setName('kuser_kgroup');
		$this->setPhpName('KuserKgroup');
		$this->setClassname('KuserKgroup');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'BIGINT', true, null, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', true, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'INTEGER', true, null, null);
		$this->addForeignKey('KGROUP_ID', 'KgroupId', 'INTEGER', 'kuser', 'ID', true, null, null);
		$this->addColumn('PGROUP_ID', 'PgroupId', 'INTEGER', true, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
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
    $this->addRelation('kuserRelatedByKgroupId', 'kuser', RelationMap::MANY_TO_ONE, array('kgroup_id' => 'id', ), null, null);
    $this->addRelation('kuserRelatedByKuserId', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
	} // buildRelations()

} // KuserKgroupTableMap
