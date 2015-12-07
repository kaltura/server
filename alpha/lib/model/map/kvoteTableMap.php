<?php


/**
 * This class defines the structure of the 'kvote' table.
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
class kvoteTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.kvoteTableMap';

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
		$this->setName('kvote');
		$this->setPhpName('kvote');
		$this->setClassname('kvote');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('KSHOW_ID', 'KshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kshow', 'ID', false, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'VARCHAR', false, 100, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('RANK', 'Rank', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('KVOTE_TYPE', 'KvoteType', 'INTEGER', false, null, 1);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kshowRelatedByKshowId', 'kshow', RelationMap::MANY_TO_ONE, array('kshow_id' => 'id', ), null, null);
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
    $this->addRelation('kshowRelatedByKuserId', 'kshow', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
	} // buildRelations()

} // kvoteTableMap
