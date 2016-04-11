<?php


/**
 * This class defines the structure of the 'schedule_event' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.schedule
 * @subpackage model.map
 */
class ScheduleEventTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.schedule.ScheduleEventTableMap';

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
		$this->setName('schedule_event');
		$this->setPhpName('ScheduleEvent');
		$this->setClassname('ScheduleEvent');
		$this->setPackage('plugins.schedule');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARENT_ID', 'ParentId', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('SUMMARY', 'Summary', 'VARCHAR', false, 256, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('ORIGINAL_START_DATE', 'OriginalStartDate', 'TIMESTAMP', true, null, null);
		$this->addColumn('START_DATE', 'StartDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('END_DATE', 'EndDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('REFERENCE_ID', 'ReferenceId', 'VARCHAR', false, 256, null);
		$this->addColumn('CLASSIFICATION_TYPE', 'ClassificationType', 'INTEGER', false, null, null);
		$this->addColumn('GEO_LAT', 'GeoLat', 'FLOAT', false, null, null);
		$this->addColumn('GEO_LONG', 'GeoLong', 'FLOAT', false, null, null);
		$this->addColumn('LOCATION', 'Location', 'VARCHAR', false, 256, null);
		$this->addColumn('ORGANIZER', 'Organizer', 'VARCHAR', false, 256, null);
		$this->addColumn('OWNER_KUSER_ID', 'OwnerKuserId', 'INTEGER', false, null, null);
		$this->addColumn('PRIORITY', 'Priority', 'INTEGER', false, null, null);
		$this->addColumn('SEQUENCE', 'Sequence', 'INTEGER', false, null, null);
		$this->addColumn('RECURRENCE_TYPE', 'RecurrenceType', 'INTEGER', true, null, null);
		$this->addColumn('DURATION', 'Duration', 'INTEGER', false, null, null);
		$this->addColumn('CONTACT', 'Contact', 'VARCHAR', false, 1024, null);
		$this->addColumn('COMMENT', 'Comment', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // ScheduleEventTableMap
