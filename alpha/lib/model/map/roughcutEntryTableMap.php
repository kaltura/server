<?php


/**
 * This class defines the structure of the 'roughcut_entry' table.
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
class roughcutEntryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.roughcutEntryTableMap';

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
		$this->setName('roughcut_entry');
		$this->setPhpName('roughcutEntry');
		$this->setClassname('roughcutEntry');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('ROUGHCUT_ID', 'RoughcutId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addColumn('ROUGHCUT_VERSION', 'RoughcutVersion', 'INTEGER', false, null, null);
		$this->addForeignKey('ROUGHCUT_KSHOW_ID', 'RoughcutKshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('OP_TYPE', 'OpType', 'SMALLINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entryRelatedByRoughcutId', 'entry', RelationMap::MANY_TO_ONE, array('roughcut_id' => 'id', ), null, null);
    $this->addRelation('kshow', 'kshow', RelationMap::MANY_TO_ONE, array('roughcut_kshow_id' => 'id', ), null, null);
    $this->addRelation('entryRelatedByEntryId', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
	} // buildRelations()

} // roughcutEntryTableMap
