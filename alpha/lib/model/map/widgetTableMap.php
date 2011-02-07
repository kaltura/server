<?php


/**
 * This class defines the structure of the 'widget' table.
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
class widgetTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.widgetTableMap';

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
		$this->setName('widget');
		$this->setPhpName('widget');
		$this->setClassname('widget');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 32, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('SOURCE_WIDGET_ID', 'SourceWidgetId', 'VARCHAR', false, 32, null);
		$this->addColumn('ROOT_WIDGET_ID', 'RootWidgetId', 'VARCHAR', false, 32, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, null);
		$this->addForeignKey('KSHOW_ID', 'KshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('ENTRY_ID', 'EntryId', 'VARCHAR', 'entry', 'ID', false, 20, null);
		$this->addForeignKey('UI_CONF_ID', 'UiConfId', 'INTEGER', 'ui_conf', 'ID', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'VARCHAR', false, 1024, null);
		$this->addColumn('SECURITY_TYPE', 'SecurityType', 'SMALLINT', false, null, null);
		$this->addColumn('SECURITY_POLICY', 'SecurityPolicy', 'SMALLINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_DATA', 'PartnerData', 'VARCHAR', false, 4096, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kshow', 'kshow', RelationMap::MANY_TO_ONE, array('kshow_id' => 'id', ), null, null);
    $this->addRelation('entry', 'entry', RelationMap::MANY_TO_ONE, array('entry_id' => 'id', ), null, null);
    $this->addRelation('uiConf', 'uiConf', RelationMap::MANY_TO_ONE, array('ui_conf_id' => 'id', ), null, null);
	} // buildRelations()

} // widgetTableMap
