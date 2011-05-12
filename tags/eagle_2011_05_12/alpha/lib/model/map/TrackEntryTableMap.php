<?php


/**
 * This class defines the structure of the 'track_entry' table.
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
class TrackEntryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.TrackEntryTableMap';

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
		$this->setName('track_entry');
		$this->setPhpName('TrackEntry');
		$this->setClassname('TrackEntry');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('TRACK_EVENT_TYPE_ID', 'TrackEventTypeId', 'SMALLINT', false, null, null);
		$this->addColumn('PS_VERSION', 'PsVersion', 'VARCHAR', false, 10, null);
		$this->addColumn('CONTEXT', 'Context', 'VARCHAR', false, 511, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('HOST_NAME', 'HostName', 'VARCHAR', false, 20, null);
		$this->addColumn('UID', 'Uid', 'VARCHAR', false, 63, null);
		$this->addColumn('TRACK_EVENT_STATUS_ID', 'TrackEventStatusId', 'SMALLINT', false, null, null);
		$this->addColumn('CHANGED_PROPERTIES', 'ChangedProperties', 'VARCHAR', false, 1023, null);
		$this->addColumn('PARAM_1_STR', 'Param1Str', 'VARCHAR', false, 255, null);
		$this->addColumn('PARAM_2_STR', 'Param2Str', 'VARCHAR', false, 511, null);
		$this->addColumn('PARAM_3_STR', 'Param3Str', 'VARCHAR', false, 511, null);
		$this->addColumn('KS', 'Ks', 'VARCHAR', false, 511, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 127, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('USER_IP', 'UserIp', 'VARCHAR', false, 20, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // TrackEntryTableMap
