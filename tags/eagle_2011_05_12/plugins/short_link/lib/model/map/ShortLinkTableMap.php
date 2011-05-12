<?php


/**
 * This class defines the structure of the 'short_link' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.shortLink
 * @subpackage model.map
 */
class ShortLinkTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.shortLink.ShortLinkTableMap';

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
		$this->setName('short_link');
		$this->setPhpName('ShortLink');
		$this->setClassname('ShortLink');
		$this->setPackage('plugins.shortLink');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 5, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('EXPIRES_AT', 'ExpiresAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 63, null);
		$this->addColumn('SYSTEM_NAME', 'SystemName', 'VARCHAR', false, 63, null);
		$this->addColumn('FULL_URL', 'FullUrl', 'VARCHAR', false, 255, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ShortLinkTableMap
