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
 * @package plugins.dropFolder
 * @subpackage model.map
 */
class DropFolderFileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.dropFolder.DropFolderFileTableMap';

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
		$this->setPackage('plugins.dropFolder');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('DROP_FOLDER_ID', 'DropFolderId', 'INTEGER', true, null, null);
		$this->addColumn('FILE_NAME', 'FileName', 'VARCHAR', true, 500, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', true, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', true, null, null);
		$this->addColumn('FILE_SIZE', 'FileSize', 'INTEGER', true, null, null);
		$this->addColumn('FILE_SIZE_LAST_SET_AT', 'FileSizeLastSetAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('ERROR_CODE', 'ErrorCode', 'INTEGER', false, null, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'LONGVARCHAR', false, null, null);
		$this->addColumn('PARSED_SLUG', 'ParsedSlug', 'VARCHAR', false, 500, null);
		$this->addColumn('PARSED_FLAVOR', 'ParsedFlavor', 'VARCHAR', false, 500, null);
		$this->addColumn('LEAD_DROP_FOLDER_FILE_ID', 'LeadDropFolderFileId', 'INTEGER', false, null, null);
		$this->addColumn('DELETED_DROP_FOLDER_FILE_ID', 'DeletedDropFolderFileId', 'INTEGER', false, null, null);
		$this->addColumn('MD5_FILE_NAME', 'Md5FileName', 'VARCHAR', true, 32, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPLOAD_START_DETECTED_AT', 'UploadStartDetectedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPLOAD_END_DETECTED_AT', 'UploadEndDetectedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('IMPORT_STARTED_AT', 'ImportStartedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('IMPORT_ENDED_AT', 'ImportEndedAt', 'TIMESTAMP', false, null, null);
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
