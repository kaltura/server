<?php


/**
 * This class defines the structure of the 'flickr_token' table.
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
class flickrTokenTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.flickrTokenTableMap';

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
		$this->setName('flickr_token');
		$this->setPhpName('flickrToken');
		$this->setClassname('flickrToken');
		$this->setPackage('Core');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('KALT_TOKEN', 'KaltToken', 'VARCHAR', true, 256, null);
		$this->addColumn('FROB', 'Frob', 'VARCHAR', false, 64, null);
		$this->addColumn('TOKEN', 'Token', 'VARCHAR', false, 64, null);
		$this->addColumn('NSID', 'Nsid', 'VARCHAR', false, 64, null);
		$this->addColumn('RESPONSE', 'Response', 'VARCHAR', false, 512, null);
		$this->addColumn('IS_VALID', 'IsValid', 'BOOLEAN', false, null, false);
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

} // flickrTokenTableMap
