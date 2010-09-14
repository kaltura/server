<?php


/**
 * This class defines the structure of the 'kshow' table.
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
class kshowTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.kshowTableMap';

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
		$this->setName('kshow');
		$this->setPhpName('kshow');
		$this->setClassname('kshow');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 20, null);
		$this->addForeignKey('PRODUCER_ID', 'ProducerId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('EPISODE_ID', 'EpisodeId', 'VARCHAR', false, 20, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 60, null);
		$this->addColumn('SUBDOMAIN', 'Subdomain', 'VARCHAR', false, 30, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, 0);
		$this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
		$this->addColumn('MEDIA_TYPE', 'MediaType', 'INTEGER', false, null, null);
		$this->addColumn('FORMAT_TYPE', 'FormatType', 'INTEGER', false, null, null);
		$this->addColumn('LANGUAGE', 'Language', 'INTEGER', false, null, null);
		$this->addColumn('START_DATE', 'StartDate', 'DATE', false, null, null);
		$this->addColumn('END_DATE', 'EndDate', 'DATE', false, null, null);
		$this->addColumn('SKIN', 'Skin', 'LONGVARCHAR', false, null, null);
		$this->addColumn('THUMBNAIL', 'Thumbnail', 'VARCHAR', false, 48, null);
		$this->addColumn('SHOW_ENTRY_ID', 'ShowEntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('INTRO_ID', 'IntroId', 'INTEGER', false, null, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, 0);
		$this->addColumn('VOTES', 'Votes', 'INTEGER', false, null, 0);
		$this->addColumn('COMMENTS', 'Comments', 'INTEGER', false, null, 0);
		$this->addColumn('FAVORITES', 'Favorites', 'INTEGER', false, null, 0);
		$this->addColumn('RANK', 'Rank', 'INTEGER', false, null, 0);
		$this->addColumn('ENTRIES', 'Entries', 'INTEGER', false, null, 0);
		$this->addColumn('CONTRIBUTORS', 'Contributors', 'INTEGER', false, null, 0);
		$this->addColumn('SUBSCRIBERS', 'Subscribers', 'INTEGER', false, null, 0);
		$this->addColumn('NUMBER_OF_UPDATES', 'NumberOfUpdates', 'INTEGER', false, null, 0);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('INDEXED_CUSTOM_DATA_1', 'IndexedCustomData1', 'INTEGER', false, null, null);
		$this->addColumn('INDEXED_CUSTOM_DATA_2', 'IndexedCustomData2', 'INTEGER', false, null, null);
		$this->addColumn('INDEXED_CUSTOM_DATA_3', 'IndexedCustomData3', 'VARCHAR', false, 256, null);
		$this->addColumn('REOCCURENCE', 'Reoccurence', 'INTEGER', false, null, null);
		$this->addColumn('LICENSE_TYPE', 'LicenseType', 'INTEGER', false, null, null);
		$this->addColumn('LENGTH_IN_MSECS', 'LengthInMsecs', 'INTEGER', false, null, 0);
		$this->addColumn('VIEW_PERMISSIONS', 'ViewPermissions', 'INTEGER', false, null, null);
		$this->addColumn('VIEW_PASSWORD', 'ViewPassword', 'VARCHAR', false, 40, null);
		$this->addColumn('CONTRIB_PERMISSIONS', 'ContribPermissions', 'INTEGER', false, null, null);
		$this->addColumn('CONTRIB_PASSWORD', 'ContribPassword', 'VARCHAR', false, 40, null);
		$this->addColumn('EDIT_PERMISSIONS', 'EditPermissions', 'INTEGER', false, null, null);
		$this->addColumn('EDIT_PASSWORD', 'EditPassword', 'VARCHAR', false, 40, null);
		$this->addColumn('SALT', 'Salt', 'VARCHAR', false, 32, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		$this->addColumn('SEARCH_TEXT', 'SearchText', 'VARCHAR', false, 4096, null);
		$this->addColumn('PERMISSIONS', 'Permissions', 'VARCHAR', false, 1024, null);
		$this->addColumn('GROUP_ID', 'GroupId', 'VARCHAR', false, 64, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, 0);
		$this->addColumn('PARTNER_DATA', 'PartnerData', 'VARCHAR', false, 4096, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('producer_id' => 'id', ), null, null);
    $this->addRelation('entry', 'entry', RelationMap::ONE_TO_MANY, array('id' => 'kshow_id', ), null, null);
    $this->addRelation('kvoteRelatedByKshowId', 'kvote', RelationMap::ONE_TO_MANY, array('id' => 'kshow_id', ), null, null);
    $this->addRelation('kvoteRelatedByKuserId', 'kvote', RelationMap::ONE_TO_MANY, array('id' => 'kuser_id', ), null, null);
    $this->addRelation('KshowKuser', 'KshowKuser', RelationMap::ONE_TO_MANY, array('id' => 'kshow_id', ), null, null);
    $this->addRelation('PuserRole', 'PuserRole', RelationMap::ONE_TO_MANY, array('id' => 'kshow_id', ), null, null);
    $this->addRelation('roughcutEntry', 'roughcutEntry', RelationMap::ONE_TO_MANY, array('id' => 'roughcut_kshow_id', ), null, null);
    $this->addRelation('widget', 'widget', RelationMap::ONE_TO_MANY, array('id' => 'kshow_id', ), null, null);
	} // buildRelations()

} // kshowTableMap
