<?php


/**
 * This class defines the structure of the 'entry' table.
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
class entryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.entryTableMap';

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
		$this->setName('entry');
		$this->setPhpName('entry');
		$this->setClassname('entry');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 20, null);
		$this->addForeignKey('KSHOW_ID', 'KshowId', 'VARCHAR', 'kshow', 'ID', false, 20, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 60, null);
		$this->addColumn('TYPE', 'Type', 'SMALLINT', false, null, null);
		$this->addColumn('MEDIA_TYPE', 'MediaType', 'SMALLINT', false, null, null);
		$this->addColumn('DATA', 'Data', 'VARCHAR', false, 48, null);
		$this->addColumn('THUMBNAIL', 'Thumbnail', 'VARCHAR', false, 48, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, 0);
		$this->addColumn('VOTES', 'Votes', 'INTEGER', false, null, 0);
		$this->addColumn('COMMENTS', 'Comments', 'INTEGER', false, null, 0);
		$this->addColumn('FAVORITES', 'Favorites', 'INTEGER', false, null, 0);
		$this->addColumn('TOTAL_RANK', 'TotalRank', 'INTEGER', false, null, 0);
		$this->addColumn('RANK', 'Rank', 'INTEGER', false, null, 0);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('ANONYMOUS', 'Anonymous', 'TINYINT', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('SOURCE', 'Source', 'SMALLINT', false, null, null);
		$this->addColumn('SOURCE_ID', 'SourceId', 'VARCHAR', false, 48, null);
		$this->addColumn('SOURCE_LINK', 'SourceLink', 'VARCHAR', false, 1024, null);
		$this->addColumn('LICENSE_TYPE', 'LicenseType', 'SMALLINT', false, null, null);
		$this->addColumn('CREDIT', 'Credit', 'VARCHAR', false, 1024, null);
		$this->addColumn('LENGTH_IN_MSECS', 'LengthInMsecs', 'INTEGER', false, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, 0);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('SEARCH_TEXT', 'SearchText', 'VARCHAR', false, 4096, null);
		$this->addColumn('SCREEN_NAME', 'ScreenName', 'VARCHAR', false, 20, null);
		$this->addColumn('SITE_URL', 'SiteUrl', 'VARCHAR', false, 256, null);
		$this->addColumn('PERMISSIONS', 'Permissions', 'INTEGER', false, null, 1);
		$this->addColumn('GROUP_ID', 'GroupId', 'VARCHAR', false, 64, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, 0);
		$this->addColumn('PARTNER_DATA', 'PartnerData', 'VARCHAR', false, 4096, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('INDEXED_CUSTOM_DATA_1', 'IndexedCustomData1', 'INTEGER', false, null, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('MEDIA_DATE', 'MediaDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('ADMIN_TAGS', 'AdminTags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('MODERATION_STATUS', 'ModerationStatus', 'INTEGER', false, null, null);
		$this->addColumn('MODERATION_COUNT', 'ModerationCount', 'INTEGER', false, null, null);
		$this->addColumn('MODIFIED_AT', 'ModifiedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'VARCHAR', false, 64, null);
		$this->addForeignKey('ACCESS_CONTROL_ID', 'AccessControlId', 'INTEGER', 'access_control', 'ID', false, null, null);
		$this->addForeignKey('CONVERSION_PROFILE_ID', 'ConversionProfileId', 'INTEGER', 'conversion_profile_2', 'ID', false, null, null);
		$this->addColumn('CATEGORIES', 'Categories', 'VARCHAR', false, 4096, null);
		$this->addColumn('CATEGORIES_IDS', 'CategoriesIds', 'VARCHAR', false, 1024, null);
		$this->addColumn('START_DATE', 'StartDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('END_DATE', 'EndDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('SEARCH_TEXT_DISCRETE', 'SearchTextDiscrete', 'VARCHAR', false, 4096, null);
		$this->addColumn('FLAVOR_PARAMS_IDS', 'FlavorParamsIds', 'VARCHAR', false, 512, null);
		$this->addColumn('AVAILABLE_FROM', 'AvailableFrom', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kshow', 'kshow', RelationMap::MANY_TO_ONE, array('kshow_id' => 'id', ), null, null);
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
    $this->addRelation('accessControl', 'accessControl', RelationMap::MANY_TO_ONE, array('access_control_id' => 'id', ), null, null);
    $this->addRelation('conversionProfile2', 'conversionProfile2', RelationMap::MANY_TO_ONE, array('conversion_profile_id' => 'id', ), null, null);
    $this->addRelation('kvote', 'kvote', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('conversion', 'conversion', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('WidgetLog', 'WidgetLog', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('moderationFlag', 'moderationFlag', RelationMap::ONE_TO_MANY, array('id' => 'flagged_entry_id', ), null, null);
    $this->addRelation('roughcutEntryRelatedByRoughcutId', 'roughcutEntry', RelationMap::ONE_TO_MANY, array('id' => 'roughcut_id', ), null, null);
    $this->addRelation('roughcutEntryRelatedByEntryId', 'roughcutEntry', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('widget', 'widget', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('flavorParamsOutput', 'flavorParamsOutput', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('flavorAsset', 'flavorAsset', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
    $this->addRelation('SphinxLog', 'SphinxLog', RelationMap::ONE_TO_MANY, array('id' => 'entry_id', ), null, null);
	} // buildRelations()

} // entryTableMap
