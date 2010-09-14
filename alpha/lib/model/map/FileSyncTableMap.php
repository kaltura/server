<?php


/**
 * This class defines the structure of the 'file_sync' table.
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
class FileSyncTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.FileSyncTableMap';

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
		$this->setName('file_sync');
		$this->setPhpName('FileSync');
		$this->setClassname('FileSync');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_TYPE', 'ObjectType', 'TINYINT', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'VARCHAR', false, 20, null);
		$this->addColumn('VERSION', 'Version', 'VARCHAR', false, 20, null);
		$this->addColumn('OBJECT_SUB_TYPE', 'ObjectSubType', 'TINYINT', false, null, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addColumn('ORIGINAL', 'Original', 'TINYINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('READY_AT', 'ReadyAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('SYNC_TIME', 'SyncTime', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('FILE_TYPE', 'FileType', 'TINYINT', false, null, null);
		$this->addColumn('LINKED_ID', 'LinkedId', 'INTEGER', false, null, null);
		$this->addColumn('LINK_COUNT', 'LinkCount', 'INTEGER', false, null, null);
		$this->addColumn('FILE_ROOT', 'FileRoot', 'VARCHAR', false, 64, null);
		$this->addColumn('FILE_PATH', 'FilePath', 'VARCHAR', false, 128, null);
		$this->addColumn('FILE_SIZE', 'FileSize', 'BIGINT', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // FileSyncTableMap
