<?php


/**
 * This class defines the structure of the 'puser_role' table.
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
class PuserRoleTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.PuserRoleTableMap';

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
		$this->setName('puser_role');
		$this->setPhpName('PuserRole');
		$this->setClassname('PuserRole');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('KSHOW_ID', 'KshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('PARTNER_ID', 'PartnerId', 'INTEGER', 'puser_kuser', 'PARTNER_ID', false, null, null);
		$this->addForeignKey('PUSER_ID', 'PuserId', 'VARCHAR', 'puser_kuser', 'PUSER_ID', false, 64, null);
		$this->addColumn('ROLE', 'Role', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kshow', 'kshow', RelationMap::MANY_TO_ONE, array('kshow_id' => 'id', ), null, null);
    $this->addRelation('PuserKuserRelatedByPartnerId', 'PuserKuser', RelationMap::MANY_TO_ONE, array('partner_id' => 'partner_id', ), null, null);
    $this->addRelation('PuserKuserRelatedByPuserId', 'PuserKuser', RelationMap::MANY_TO_ONE, array('puser_id' => 'puser_id', ), null, null);
	} // buildRelations()

} // PuserRoleTableMap
