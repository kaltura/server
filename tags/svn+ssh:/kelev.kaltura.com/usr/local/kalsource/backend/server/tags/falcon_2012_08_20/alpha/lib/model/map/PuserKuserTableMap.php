<?php


/**
 * This class defines the structure of the 'puser_kuser' table.
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
class PuserKuserTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.PuserKuserTableMap';

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
		$this->setName('puser_kuser');
		$this->setPhpName('PuserKuser');
		$this->setClassname('PuserKuser');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'VARCHAR', false, 64, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('PUSER_NAME', 'PuserName', 'VARCHAR', false, 64, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'VARCHAR', false, 1024, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CONTEXT', 'Context', 'VARCHAR', false, 1024, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
    $this->addRelation('PuserRoleRelatedByPartnerId', 'PuserRole', RelationMap::ONE_TO_MANY, array('partner_id' => 'partner_id', ), null, null);
    $this->addRelation('PuserRoleRelatedByPuserId', 'PuserRole', RelationMap::ONE_TO_MANY, array('puser_id' => 'puser_id', ), null, null);
	} // buildRelations()

} // PuserKuserTableMap
