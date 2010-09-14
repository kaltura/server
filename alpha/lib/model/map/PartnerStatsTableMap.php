<?php


/**
 * This class defines the structure of the 'partner_stats' table.
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
class PartnerStatsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.PartnerStatsTableMap';

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
		$this->setName('partner_stats');
		$this->setPhpName('PartnerStats');
		$this->setClassname('PartnerStats');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, null);
		$this->addColumn('VIDEOS', 'Videos', 'INTEGER', false, null, null);
		$this->addColumn('AUDIOS', 'Audios', 'INTEGER', false, null, null);
		$this->addColumn('IMAGES', 'Images', 'INTEGER', false, null, null);
		$this->addColumn('ENTRIES', 'Entries', 'INTEGER', false, null, null);
		$this->addColumn('USERS_1', 'Users1', 'INTEGER', false, null, null);
		$this->addColumn('USERS_2', 'Users2', 'INTEGER', false, null, null);
		$this->addColumn('RC_1', 'Rc1', 'INTEGER', false, null, null);
		$this->addColumn('RC_2', 'Rc2', 'INTEGER', false, null, null);
		$this->addColumn('KSHOWS_1', 'Kshows1', 'INTEGER', false, null, null);
		$this->addColumn('KSHOWS_2', 'Kshows2', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('WIDGETS', 'Widgets', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // PartnerStatsTableMap
