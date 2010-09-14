<?php


/**
 * This class defines the structure of the 'widget_log' table.
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
class WidgetLogTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.WidgetLogTableMap';

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
		$this->setName('widget_log');
		$this->setPhpName('WidgetLog');
		$this->setClassname('WidgetLog');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('KSHOW_ID', 'KshowId', 'VARCHAR', false, 20, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addColumn('KMEDIA_TYPE', 'KmediaType', 'INTEGER', false, null, null);
		$this->addColumn('WIDGET_TYPE', 'WidgetType', 'VARCHAR', false, 32, null);
		$this->addColumn('REFERER', 'Referer', 'VARCHAR', false, 1024, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, 0);
		$this->addColumn('IP1', 'Ip1', 'INTEGER', false, null, null);
		$this->addColumn('IP1_COUNT', 'Ip1Count', 'INTEGER', false, null, 0);
		$this->addColumn('IP2', 'Ip2', 'INTEGER', false, null, null);
		$this->addColumn('IP2_COUNT', 'Ip2Count', 'INTEGER', false, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, 0);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
	} // buildRelations()

} // WidgetLogTableMap
