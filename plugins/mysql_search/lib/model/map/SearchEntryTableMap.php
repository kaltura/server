<?php


/**
 * This class defines the structure of the 'search_entry' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.contentDistribution
 * @subpackage model.map
 */
class SearchEntryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.contentDistribution.SearchEntryTableMap';

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
		$this->setName('search_entry');
		$this->setPhpName('SearchEntry');
		$this->setClassname('SearchEntry');
		$this->setPackage('plugins.contentDistribution');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ENTRY_ID', 'EntryId', 'VARCHAR', true, 20, null);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 60, null);
		$this->addColumn('TYPE', 'Type', 'SMALLINT', false, null, null);
		$this->addColumn('MEDIA_TYPE', 'MediaType', 'SMALLINT', false, null, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, 0);
		$this->addColumn('RANK', 'Rank', 'INTEGER', false, null, 0);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('SOURCE_LINK', 'SourceLink', 'VARCHAR', false, 1024, null);
		$this->addColumn('DURATION', 'Duration', 'INTEGER', false, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, null);
		$this->addColumn('GROUP_ID', 'GroupId', 'VARCHAR', false, 64, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, 0);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('MEDIA_DATE', 'MediaDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('ADMIN_TAGS', 'AdminTags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('MODERATION_STATUS', 'ModerationStatus', 'INTEGER', false, null, null);
		$this->addColumn('MODERATION_COUNT', 'ModerationCount', 'INTEGER', false, null, null);
		$this->addColumn('MODIFIED_AT', 'ModifiedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('ACCESS_CONTROL_ID', 'AccessControlId', 'INTEGER', false, null, null);
		$this->addColumn('CATEGORIES', 'Categories', 'VARCHAR', false, 4096, null);
		$this->addColumn('START_DATE', 'StartDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('END_DATE', 'EndDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('FLAVOR_PARAMS', 'FlavorParams', 'VARCHAR', false, 512, null);
		$this->addColumn('AVAILABLE_FROM', 'AvailableFrom', 'TIMESTAMP', false, null, null);
		$this->addColumn('PLUGIN_DATA', 'PluginData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // SearchEntryTableMap
