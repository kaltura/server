<?php


/**
 * This class defines the structure of the 'live_channel_segment' table.
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
class LiveChannelSegmentTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.LiveChannelSegmentTableMap';

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
		$this->setName('live_channel_segment');
		$this->setPhpName('LiveChannelSegment');
		$this->setClassname('LiveChannelSegment');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'BIGINT', true, null, null);
		$this->addForeignKey('PARTNER_ID', 'PartnerId', 'INTEGER', 'partner', 'ID', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 255, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addForeignKey('CHANNEL_ID', 'ChannelId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addColumn('TRIGGER_TYPE', 'TriggerType', 'INTEGER', false, null, null);
		$this->addForeignKey('TRIGGER_SEGMENT_ID', 'TriggerSegmentId', 'BIGINT', 'live_channel_segment', 'ID', false, null, null);
		$this->addColumn('START_TIME', 'StartTime', 'FLOAT', false, null, null);
		$this->addColumn('DURATION', 'Duration', 'FLOAT', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('LiveChannelSegmentRelatedByTriggerSegmentId', 'LiveChannelSegment', RelationMap::MANY_TO_ONE, array('trigger_segment_id' => 'id', ), null, null);
    $this->addRelation('Partner', 'Partner', RelationMap::MANY_TO_ONE, array('partner_id' => 'id', ), null, null);
    $this->addRelation('entryRelatedByChannelId', 'entry', RelationMap::MANY_TO_ONE, array('channel_id' => 'id', ), null, null);
    $this->addRelation('entryRelatedByEntryId', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
    $this->addRelation('LiveChannelSegmentRelatedByTriggerSegmentId', 'LiveChannelSegment', RelationMap::ONE_TO_MANY, array('id' => 'trigger_segment_id', ), null, null);
	} // buildRelations()

} // LiveChannelSegmentTableMap
