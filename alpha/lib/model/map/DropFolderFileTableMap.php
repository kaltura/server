<?php


/**
 * This class defines the structure of the 'drop_folder_file' table.
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
class DropFolderFileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.DropFolderFileTableMap';

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
		$this->setName('drop_folder_file');
		$this->setPhpName('DropFolderFile');
		$this->setClassname('DropFolderFile');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('DROP_FOLDER_ID', 'DropFolderId', 'INTEGER', true, null, null);
		$this->addColumn('FILE_NAME', 'FileName', 'VARCHAR', true, 500, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', true, null, null);
		$this->addColumn('FILE_SIZE', 'FileSize', 'INTEGER', true, null, null);
		$this->addColumn('LAST_FILE_SIZE_CHECK_AT', 'LastFileSizeCheckAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'LONGVARCHAR', false, null, null);
		$this->addColumn('PARSED_SLUG', 'ParsedSlug', 'VARCHAR', false, 500, null);
		$this->addColumn('PARSED_FLAVOR_ID', 'ParsedFlavorId', 'INTEGER', false, null, null);
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
	} // buildRelations()

} // DropFolderFileTableMap
