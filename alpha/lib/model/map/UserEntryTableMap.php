<?php


/**
 * This class defines the structure of the 'user_entry' table.
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
class UserEntryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.UserEntryTableMap';

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
		$this->setName('user_entry');
		$this->setPhpName('UserEntry');
		$this->setClassname('UserEntry');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', true, 20, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
		$this->addColumn('EXTENDED_STATUS', 'ExtendedStatus', 'INTEGER', false, null, null);
		$this->addColumn('PRIVACY_CONTEXT', 'PrivacyContext', 'VARCHAR', false, 255, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
	} // buildRelations()

} // UserEntryTableMap
