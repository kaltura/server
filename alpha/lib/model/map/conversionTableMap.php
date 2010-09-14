<?php


/**
 * This class defines the structure of the 'conversion' table.
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
class conversionTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.conversionTableMap';

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
		$this->setName('conversion');
		$this->setPhpName('conversion');
		$this->setClassname('conversion');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addColumn('IN_FILE_NAME', 'InFileName', 'VARCHAR', false, 128, null);
		$this->addColumn('IN_FILE_EXT', 'InFileExt', 'VARCHAR', false, 16, null);
		$this->addColumn('IN_FILE_SIZE', 'InFileSize', 'INTEGER', false, null, null);
		$this->addColumn('SOURCE', 'Source', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('CONVERSION_PARAMS', 'ConversionParams', 'VARCHAR', false, 512, null);
		$this->addColumn('OUT_FILE_NAME', 'OutFileName', 'VARCHAR', false, 128, null);
		$this->addColumn('OUT_FILE_SIZE', 'OutFileSize', 'INTEGER', false, null, null);
		$this->addColumn('OUT_FILE_NAME_2', 'OutFileName2', 'VARCHAR', false, 128, null);
		$this->addColumn('OUT_FILE_SIZE_2', 'OutFileSize2', 'INTEGER', false, null, null);
		$this->addColumn('CONVERSION_TIME', 'ConversionTime', 'INTEGER', false, null, null);
		$this->addColumn('TOTAL_PROCESS_TIME', 'TotalProcessTime', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
	} // buildRelations()

} // conversionTableMap
