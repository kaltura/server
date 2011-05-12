<?php


/**
 * This class defines the structure of the 'conversion_profile' table.
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
class ConversionProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.ConversionProfileTableMap';

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
		$this->setName('conversion_profile');
		$this->setPhpName('ConversionProfile');
		$this->setClassname('ConversionProfile');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('ENABLED', 'Enabled', 'TINYINT', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 128, null);
		$this->addColumn('PROFILE_TYPE', 'ProfileType', 'VARCHAR', false, 128, null);
		$this->addColumn('COMMERCIAL_TRANSCODER', 'CommercialTranscoder', 'TINYINT', false, null, null);
		$this->addColumn('WIDTH', 'Width', 'INTEGER', false, null, null);
		$this->addColumn('HEIGHT', 'Height', 'INTEGER', false, null, null);
		$this->addColumn('ASPECT_RATIO', 'AspectRatio', 'VARCHAR', false, 6, null);
		$this->addColumn('BYPASS_FLV', 'BypassFlv', 'TINYINT', false, null, null);
		$this->addColumn('USE_WITH_BULK', 'UseWithBulk', 'TINYINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PROFILE_TYPE_SUFFIX', 'ProfileTypeSuffix', 'VARCHAR', false, 32, null);
		$this->addColumn('CONVERSION_PROFILE_2_ID', 'ConversionProfile2Id', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ConversionProfileTableMap
