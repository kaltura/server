<?php


/**
 * This class defines the structure of the 'conversion_params' table.
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
class ConversionParamsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.ConversionParamsTableMap';

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
		$this->setName('conversion_params');
		$this->setPhpName('ConversionParams');
		$this->setClassname('ConversionParams');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ENABLED', 'Enabled', 'TINYINT', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 128, null);
		$this->addColumn('PROFILE_TYPE', 'ProfileType', 'VARCHAR', false, 128, null);
		$this->addColumn('PROFILE_TYPE_INDEX', 'ProfileTypeIndex', 'INTEGER', false, null, null);
		$this->addColumn('WIDTH', 'Width', 'INTEGER', false, null, null);
		$this->addColumn('HEIGHT', 'Height', 'INTEGER', false, null, null);
		$this->addColumn('ASPECT_RATIO', 'AspectRatio', 'VARCHAR', false, 6, null);
		$this->addColumn('GOP_SIZE', 'GopSize', 'INTEGER', false, null, null);
		$this->addColumn('BITRATE', 'Bitrate', 'INTEGER', false, null, null);
		$this->addColumn('QSCALE', 'Qscale', 'INTEGER', false, null, null);
		$this->addColumn('FILE_SUFFIX', 'FileSuffix', 'VARCHAR', false, 64, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'VARCHAR', false, 4096, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ConversionParamsTableMap
