<?php


/**
 * This class defines the structure of the 'kshow_kuser' table.
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
class KshowKuserTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.KshowKuserTableMap';

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
		$this->setName('kshow_kuser');
		$this->setPhpName('KshowKuser');
		$this->setClassname('KshowKuser');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addForeignKey('KSHOW_ID', 'KshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('SUBSCRIPTION_TYPE', 'SubscriptionType', 'INTEGER', false, null, null);
		$this->addColumn('ALERT_TYPE', 'AlertType', 'INTEGER', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kshow', 'kshow', RelationMap::MANY_TO_ONE, array('kshow_id' => 'id', ), null, null);
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
	} // buildRelations()

} // KshowKuserTableMap
