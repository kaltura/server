<?php


/**
 * This class defines the structure of the 'drop_folder' table.
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
class DropFolderTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.dropFolder.DropFolderTableMap';

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
		$this->setName('drop_folder');
		$this->setPhpName('DropFolder');
		$this->setClassname('DropFolder');
		$this->setPackage('plugins.dropFolder');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 100, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', true, null, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', true, null, null);
		$this->addColumn('PATH', 'Path', 'LONGVARCHAR', true, null, null);
		$this->addColumn('CONVERSION_PROFILE_ID', 'ConversionProfileId', 'INTEGER', false, null, null);
		$this->addColumn('FILE_DELETE_POLICY', 'FileDeletePolicy', 'INTEGER', false, null, null);
		$this->addColumn('UNMATCHED_FILE_POLICY', 'UnmatchedFilePolicy', 'INTEGER', false, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', true, null, null);
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

} // DropFolderTableMap
